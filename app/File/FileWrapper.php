<?php

namespace App\File;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileWrapper
{
    public function __construct(protected string $filePath, protected string $disk)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Get the full path of the file.
     * @return string
     */
    public function getPath(): string
    {
        return Storage::disk($this->disk)->path($this->filePath);
    }

    /**
     * Get the full url of the file.
     * @return string
     */
    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->filePath);
    }

    /**
     * Move the file to a new location.
     *
     * @param string $to Destination path (including filename).
     * @return bool
     * @throws \Exception
     */
    public function move(string $to): bool
    {
        // Ensure the file exists before attempting to delete
        if (!Storage::exists($this->filePath)) {
            throw new \Exception("File does not exist at path: {$this->filePath}");
        }

        return Storage::disk($this->disk)->move($this->filePath, $to);
    }

    /**
     * Delete the file.
     * @return bool
     */
    public function destroy(): bool
    {
        // Ensure the file exists before attempting to delete
        if (!Storage::exists($this->filePath)) {
            throw new \Exception("File does not exist at path: {$this->filePath}");
        }

        return Storage::delete($this->filePath);
    }

    /**
     * Get the file's size in bytes.
     * @return int
     */
    public function getSize(): int
    {
        return Storage::size($this->filePath);
    }

    /**
     * Get the file's mime type.
     * @return string
     */
    public function getMimeType(): string
    {
        return Storage::mimeType($this->filePath);
    }

    /**
     * Get the file's last modified time.
     * @return int
     */
    public function getLastModified(): int
    {
        return Storage::lastModified($this->filePath);
    }

    /**
     * Determine the storage disk from the file path.
     * @param string $path
     * @return string
     */
    protected function getStorageDisk(string $path): string
    {
        $storageDisks = config('filesystems.disks');
        foreach ($storageDisks as $disk => $config) {
            if (isset($config['root']) && Str::startsWith($path, $config['root'])) {
                return $disk;
            }
        }

        $prefixes = [
            'public/' => 'public',   // Public storage
            's3/' => 's3',           // Amazon S3 storage
            'ftp/' => 'ftp',         // FTP storage
            'gcs/' => 'gcs',         // Google Cloud Storage
            'azure/' => 'azure',     // Azure Blob Storage
            'dropbox/' => 'dropbox', // Dropbox storage
            'backblaze/' => 'backblaze', // Backblaze B2 storage
            'local/' => 'local',     // Local storage
            'uploads/' => 'local',   // Custom uploads directory
            'temp/' => 'local',      // Temporary files storage
        ];

        foreach ($prefixes as $prefix => $disk) {
            if (Str::startsWith($path, $prefix)) {
                return $disk;
            }
        }

        return 'local'; // Default to local storage
    }

    /**
     * Check if a given path is under the allowed storage directory.
     * @return bool
     */
    protected function isPathUnderStorage(string $path, $disk): bool
    {
        // Get the root path of the disk
        $rootPath = $disk->path('');

        // Normalize paths to avoid issues with slashes
        $path = Str::finish(Str::replaceArray('/', [DIRECTORY_SEPARATOR], $path), DIRECTORY_SEPARATOR);
        $rootPath = Str::finish(Str::replaceArray('/', [DIRECTORY_SEPARATOR], $rootPath), DIRECTORY_SEPARATOR);

        return Str::startsWith($path, $rootPath);
    }
}
