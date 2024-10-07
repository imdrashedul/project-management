<?php

namespace App\File;

use App\Traits\Helpers\FileUploadSize;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FileUploadService
{
    use FileUploadSize;

    protected $file;
    protected string $disk = 'local';
    protected string $path = 'temp'; // Default storage location
    protected string $fileKey = 'file'; // Default file input key

    // Private properties for default values
    private array $defaultMimes = ['csv', 'txt']; // Default MIME types
    private int $defaultLimit; // Default maximum upload size (in KB)

    // Properties to hold current values
    private array $mimes = [];
    private int $limit;

    public function __construct()
    {
        $this->mimes = $this->defaultMimes; // Initialize with default MIME types
        $this->defaultLimit = $this->getServerMaxFileUploadSize(); // Get server limit
        $this->limit = $this->defaultLimit; // Initialize with server limit
    }

    private function getServerMaxFileUploadSize(): int
    {

        return $this->convertToKB(self::getMaxUploadSize() . "M");
    }

    private function convertToKB(string $value): int
    {
        // Convert PHP ini size value to KB
        $unit = strtoupper(substr($value, -1));
        $size = (int) $value;

        return match ($unit) {
            'G' => $size * 1024 * 1024,
            'M' => $size * 1024, // Convert to KB
            'K' => $size, // Already in KB
            default => $size, // Treat as KB if no unit is provided
        };
    }

    public function setFileKey(string $fileKey = 'file'): self
    {
        $this->fileKey = $fileKey;
        return $this;
    }

    public function setMimes(array|string $mimes): self
    {
        // Convert array or comma-separated string to array
        if (is_string($mimes)) {
            $mimes = explode(',', $mimes);
        }

        // Replace default MIME types with provided values
        $this->mimes = array_map('trim', (array) $mimes);
        return $this;
    }

    public function setLimit(string $size): self
    {
        // Convert the provided size to KB
        $this->limit = $this->convertToKB($size);

        // Check if the provided size exceeds server limits
        $serverLimit = $this->getServerMaxFileUploadSize();
        $this->limit = $this->limit > $serverLimit ? $serverLimit : $this->limit; // Set to server limit if larger
        return $this;
    }

    protected function handleUpload(): void
    {
        $request = request();

        // Build validation rules
        $rules = [
            $this->fileKey => 'required|mimes:' . implode(',', $this->mimes) . '|max:' . $this->limit,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $this->file = $request->file($this->fileKey);
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    public function file(): FileWrapper
    {
        // Automatically handle upload when file is called
        $this->handleUpload();

        $storedFile = $this->file->store($this->path, $this->disk);

        // Return a FileWrapper object for further operations
        return new FileWrapper($storedFile, $this->disk);
    }
}
