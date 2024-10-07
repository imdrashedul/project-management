<?php

namespace App\Services;
use App\Contracts\CsvImportableService;
use App\Contracts\QueueableService;
use App\File\FileWrapper;
use App\Jobs\ProcessCsvImport;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Illuminate\Support\Str;

class CsvImportService
{
    /**
     * Chunk size for processing
     * @var int
     */
    protected $batchSize = 1000;

    public function setBatchSize($batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    public function queue(FileWrapper $file, CsvImportableService $importProvider): void
    {
        ProcessCsvImport::dispatch(
            $file,
            $importProvider,
            auth()->id()
        );
    }

    public function import(FileWrapper $file, CsvImportableService $importProvider)
    {
        $csv = Reader::createFromPath($file->getPath(), "r");
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $collection = collect($records);

        $import = function (string $model, Collection $records, callable $callback = null, array $mapper = []) {
            $chunks = $records->chunk($this->batchSize);
            $inserted = 0;

            $chunks->each(function ($chunk) use ($mapper, $model, $inserted, $callback) {
                DB::transaction(function () use ($chunk, $mapper, $model, $inserted, $callback) {
                    if (!empty($mapper)) {
                        $formatted = [];
                        foreach ($chunk as $row) {
                            foreach ($mapper as $entry => $fillable) {
                                $formatted[][$fillable] = $row[$entry] ?? null;
                            }
                        }
                        $model::insert($formatted);
                    }
                    try {
                        if ($model::insert($chunk->values()->toArray())) {
                            $inserted++;
                            if (is_callable($callback))
                                $callback($chunk);
                        }
                    } catch (Exception $e) {
                    }
                });
            });

            return $chunks->count() == $inserted;
        };

        $response = $importProvider->resolve($import, $collection);
        $file->destroy();

        return $response;
    }
}
