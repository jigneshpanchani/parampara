<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use File;
use ZipArchive;

class BackupDatabase extends Command
{
    const BACKUP_RETENTION_DAYS = 30;

    protected $signature = 'db:backup';
    protected $description = 'Backup the full database, compress to ZIP, save to D:->etc->Batsal->Parampara->DB Backup';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $backupPath = 'D:\\etc\\Batsal\\Parampara\\DB Backup\\';

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        $baseName   = $database . '__' . Carbon::now()->format('Y-m-d_H-i-s');
        $sqlFile    = $backupPath . $baseName . '.sql';
        $zipFile    = $backupPath . $baseName . '.zip';

        // Ensure backup directory exists
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $mysqldumpPath = env('MYSQL_DUMP_PATH', 'D:\xammp8.1\mysql\bin\mysqldump.exe');
        $passwordArg   = $password !== '' ? "--password={$password}" : '--password=';

        // Full dump — all tables, routines, triggers, events
        $command = "\"{$mysqldumpPath}\""
            . " --host={$host}"
            . " --port={$port}"
            . " --user={$username}"
            . " {$passwordArg}"
            . " --single-transaction"
            . " --routines"
            . " --triggers"
            . " --events"
            . " --complete-insert"
            . " --add-drop-table"
            . " {$database}"
            . " > \"{$sqlFile}\"";

        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error("mysqldump failed for database: {$database}");
            return;
        }

        $this->info("SQL dump created: {$sqlFile}");

        // Compress to ZIP
        if ($this->compressToZip($sqlFile, $zipFile)) {
            File::delete($sqlFile); // remove raw .sql to save space
            $this->info("Compressed to ZIP: {$zipFile}");
        } else {
            $this->error("ZIP compression failed. Raw SQL kept: {$sqlFile}");
        }

        $this->deleteOldBackups($backupPath);
    }

    private function compressToZip(string $sqlFile, string $zipFile): bool
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $zip->addFile($sqlFile, basename($sqlFile));
        $zip->close();

        return File::exists($zipFile);
    }

    private function deleteOldBackups(string $backupPath)
    {
        foreach (File::files($backupPath) as $file) {
            if (Carbon::parse(File::lastModified($file))->lt(Carbon::now()->subDays(self::BACKUP_RETENTION_DAYS))) {
                File::delete($file);
                $this->info('Deleted old backup: ' . $file->getFilename());
            }
        }
    }
}
