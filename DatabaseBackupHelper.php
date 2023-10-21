<?php


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (!function_exists('backupDatabase')) {
    function backupDatabase()
    {
        // Path to the backups folder inside storage/app
        $backupFolder = 'database-backups';

        // Create the backups directory if it doesn't exist
        Storage::makeDirectory($backupFolder);

        // Get the list of files in the backups directory
        $files = Storage::files($backupFolder);

        // Remove backup files older than 30 days
        foreach ($files as $file) {
            $fileCreationTime = Storage::lastModified($file);
            $fileAgeInDays = Carbon::now()->diffInDays(Carbon::createFromTimestamp($fileCreationTime));

            if ($fileAgeInDays > 30) {
                Storage::delete($file);
            }
        }

        // Generate a unique backup filename
        $backupFileName = "backup-" . Carbon::now()->format('Y-m-d-His') . '.sql';

        // Full path to the backup file inside storage/app/backups folder
        $dumpPath = storage_path("app/{$backupFolder}/{$backupFileName}");

        // Create a dump of the database
        exec("mysqldump -u root -p  -h 127.0.0.1 si > {$dumpPath}");

        // Check if the backup file was created successfully
        if (file_exists($dumpPath)) {
            return $backupFileName; // Return the filename if backup is successful
        } else {
            return null; // Backup creation failed
        }
    }
}

if (!function_exists('listBackupFiles')) {
    function listBackupFiles()
    {
        // Path to the backups folder inside storage/app
        $backupFolder = 'backups';

        // Get the list of files in the backups directory
        $files = Storage::files($backupFolder);

        // Return the list of backup files as an array
        return $files;
    }
}