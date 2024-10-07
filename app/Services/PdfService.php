<?php

namespace App\Services;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Output\Destination;
use Illuminate\Support\Str;


class PdfService
{
    protected Mpdf $mpdf;

    public function __construct()
    {
        $this->mpdf = new Mpdf([
            "mode" => "utf-8",
            "format" => "A4",
            "default_font_size" => 12,
            "default_font" => "sans-serif",
            "orientation" => 'L',
            'fontDir' => array_merge((new ConfigVariables())->getDefaults()['fontDir'], [
                resource_path('fonts/')
            ]),
        ]);
    }

    public function html($html, $attributes = []): self
    {
        $this->mpdf->writeHTML($html);

        return $this;
    }

    public function view($view, $attributes = []): self
    {
        try {
            $html = view($view, $attributes)->render();
            $this->html($html, $attributes);
        } catch (\Exception $e) {
        }
        return $this;
    }

    public function download($filename)
    {
        $filename = $this->sanitizeFilename($filename);
        $this->mpdf->Output("{$filename}.pdf", Destination::DOWNLOAD);
        return $filename;
    }

    public function save($filename)
    {
        $filename = $this->sanitizeFilename($filename);
        $path = storage_path("pdfs/{$filename}.pdf");
        $this->mpdf->Output($path, Destination::FILE);
        return $filename;
    }

    public function preview($filename)
    {
        $filename = $this->sanitizeFilename($filename);
        $this->mpdf->Output("{$filename}.pdf", Destination::INLINE);
        return $filename;
    }

    private function sanitizeFilename($filename)
    {
        $filename = preg_replace('/[^\w\-. ]+/', '', $filename);
        return substr(Str::slug($filename, '_'), 0, 255);
    }
}
