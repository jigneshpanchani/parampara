<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use File;
use ZipArchive;

class BackupDatabase extends Command
{
    // Define the retention period in days as a constant
    const BACKUP_RETENTION_DAYS = 30; //Backups older than 30 days

    protected $signature = 'db:backup';
    protected $description = 'Backup the database, compress it, and delete backups older than 7 days';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Define the backup path and filename
        $backupPath = storage_path('app/db-backups/');
        $filename = 'backup_' . Carbon::now()->format('Ymd_His') . '.sql';
        $backupFile = $backupPath . $filename;

        $zipFilename = 'backup_' . Carbon::now()->format('Ymd_His') . '.zip';
        $zipFile = $backupPath . $zipFilename;

        // Ensure the backup directory exists
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        // Database connection details from config
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        // Run the mysqldump command to create a backup
        //$mysqldumpPath = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe'; // Full path to mysqldump.exe
        $mysqldumpPath = env('MYSQL_DUMP_PATH', 'D:\xammp8.1\mysql\bin\mysqldump.exe');
        $command = "\"{$mysqldumpPath}\" --user={$username} --password={$password} --host={$host} {$database} > \"{$backupFile}\"";
        $result = null;

        try {
            exec($command, $output, $result);

            if ($result === 0) {
                $this->info('Backup created successfully: ' . $filename);

                // Compress the backup file
                if ($this->compressBackup($backupFile, $zipFile)) {
                    $this->info('Backup compressed successfully: ' . $zipFilename);

                    // Delete the original .sql file after compression
                    File::delete($backupFile);
                    $this->info('Original SQL file deleted after compression: ' . $filename);
                } else {
                    $this->error('Failed to compress the backup file.');
                }
            } else {
                $this->error('Failed to create backup.');
            }

        } catch (\Exception $e) {
            $this->error('Error during backup: ' . $e->getMessage());
        }

        // Delete backups older than the retention period
        $this->deleteOldBackups($backupPath);
    }

    // Compress the SQL file to ZIP
    private function compressBackup($filePath, $zipFilePath)
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filePath, basename($filePath)); // Add SQL file to the zip
            $zip->close();
            return true;
        }

        return false;
    }

    private function deleteOldBackups($backupPath)
    {
        $files = File::files($backupPath);

        foreach ($files as $file) {
            // Compare file's last modified time with the retention period
            if (Carbon::parse(File::lastModified($file))->lt(Carbon::now()->subDays(self::BACKUP_RETENTION_DAYS))) {
                File::delete($file);
                $this->info('Deleted old backup: ' . $file->getFilename());
            }
        }
    }
}
