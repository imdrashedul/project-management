<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\File\FileUploadService;
use App\Models\Project;
use App\Services\CsvImportService;
use App\Services\ProjectTaskImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CsvImportController extends Controller
{
    public function upload()
    {
        return view("import");
    }

    public function process(FileUploadService $fileProvider, ProjectTaskImportService $importer)
    {
        $importer->import($fileProvider->file());

        return response()->json([
            "message" => "The import process has started. You'll receive a notification once it's complete. In the meantime, feel free to explore other activities."
        ]);
    }
}
