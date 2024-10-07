<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Illuminate\Http\Request;
use App\Services\ProjectReportService;

class ProjectReportController extends Controller
{

    /**
     * @param \App\Services\ProjectReportService $projectReportProvider
     */
    public function __construct(private ProjectReportService $projectReportProvider, protected PdfService $pdfGenerator)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Handler for invoking the project report route action implicitly.
     * @return mixed
     */
    public function __invoke(): mixed
    {
        // Redirects to previous or 404 If no project found
        if (!empty($resolve = $this->projectReportProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $project = $this->projectReportProvider->project();


        return response()->make(($filename = $this->pdfGenerator->view("projects.report", [
            "project" => $project
        ])->download('report_' . $project->title)), 200, [
            "Content-Type" => "application/pdf",
            "content-Diposition" => "attachment; filename=\"{$filename}\""
        ]);
    }
}
