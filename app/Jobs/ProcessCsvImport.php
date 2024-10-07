<?php

namespace App\Jobs;

use App\Contracts\CsvImportableService;
use App\File\FileWrapper;
use App\Models\User;
use App\Notifications\JobCompletedNotification;
use App\Services\CsvImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCsvImport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected FileWrapper $file,
        protected CsvImportableService $importProvider,
        protected int $userId
    ) {
        // Empty Space Isn't Empty :)
    }

    /**
     * Execute the job.
     */
    public function handle(CsvImportService $importer): void
    {
        $message = $importer->import($this->file, $this->importProvider);
        $user = User::find($this->userId);
        $user->notify(new JobCompletedNotification($message, $this->userId));
    }
}
