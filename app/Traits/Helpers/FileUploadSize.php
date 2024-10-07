<?php

namespace App\Traits\Helpers;

trait FileUploadSize
{
    public static function getMaxUploadSize(): float
    {
        // Get the values from php.ini
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');

        // Convert sizes to bytes
        $uploadMaxFilesizeBytes = self::convertToBytes($uploadMaxFilesize);
        $postMaxSizeBytes = self::convertToBytes($postMaxSize);

        // Determine the minimum of the two values
        $maxSize = min($uploadMaxFilesizeBytes, $postMaxSizeBytes);

        // Convert to MB
        return round($maxSize / 1024 / 1024, 2);
    }

    private static function convertToBytes(string $size): int
    {
        $unit = strtoupper(substr($size, -1));
        $bytes = (int) $size;

        return match ($unit) {
            'K' => $bytes * 1024,
            'M' => $bytes * 1024 * 1024,
            'G' => $bytes * 1024 * 1024 * 1024,
            default => $bytes,
        };
    }
}
