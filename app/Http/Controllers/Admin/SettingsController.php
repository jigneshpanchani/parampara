<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = CompanyProfile::first() ?? new CompanyProfile();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Download database backup
     */
    public function downloadDbBackup()
    {
        // Path to the DB backup directory
        $backupPath = 'D:\\etc\\Batsal\\Parampara\\DB Backup';

        // Check if directory exists
        if (!File::exists($backupPath)) {
            return redirect()->back()->with('error', 'Backup directory not found: ' . $backupPath);
        }

        // Get all files in the backup directory
        $files = File::files($backupPath);

        if (empty($files)) {
            return redirect()->back()->with('error', 'No backup files found in the directory.');
        }

        // Get the most recent file
        $latestFile = null;
        $latestTime = 0;

        foreach ($files as $file) {
            $fileTime = File::lastModified($file);
            if ($fileTime > $latestTime) {
                $latestTime = $fileTime;
                $latestFile = $file;
            }
        }

        if (!$latestFile) {
            return redirect()->back()->with('error', 'Could not determine the latest backup file.');
        }

        // Download the file
        return response()->download($latestFile);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'favicon_16' => 'nullable|image|mimes:png,ico|max:1024',
            'favicon_32' => 'nullable|image|mimes:png,ico|max:1024',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website_url' => 'nullable|url|max:255',
            'gst_number' => 'nullable|string|max:20',
        ]);

        $settings = CompanyProfile::first() ?? new CompanyProfile();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                $path = $file->store('uploads/img', 'public');
                $validated['logo'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload logo: ' . $e->getMessage());
            }
        }

        // Handle favicon 16x16 upload
        if ($request->hasFile('favicon_16')) {
            try {
                $file = $request->file('favicon_16');
                $path = $file->store('uploads/img', 'public');
                $validated['favicon_16'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload favicon 16x16: ' . $e->getMessage());
            }
        }

        // Handle favicon 32x32 upload
        if ($request->hasFile('favicon_32')) {
            try {
                $file = $request->file('favicon_32');
                $path = $file->store('uploads/img', 'public');
                $validated['favicon_32'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload favicon 32x32: ' . $e->getMessage());
            }
        }

        $settings->fill($validated)->save();

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully!');
    }
}
