<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Stream rows as a CSV download.
     *
     * @param  array<int, string>  $headings
     * @param  iterable<int, array>  $rows
     */
    public function csv(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders non-ASCII characters correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Stream rows as an Excel-compatible SpreadsheetML download.
     *
     * @param  array<int, string>  $headings
     * @param  iterable<int, array>  $rows
     */
    public function excel(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
            echo '<Worksheet ss:Name="Export"><Table>';

            $writeRow = function (array $cells, bool $bold = false) {
                echo '<Row>';
                foreach ($cells as $cell) {
                    $value = htmlspecialchars((string) $cell, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    echo '<Cell><Data ss:Type="String">'.$value.'</Data></Cell>';
                }
                echo '</Row>';
            };

            $writeRow($headings, true);
            foreach ($rows as $row) {
                $writeRow($row);
            }

            echo '</Table></Worksheet></Workbook>';
        }, $filename.'.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    /**
     * Render rows into a tabular PDF download.
     *
     * @param  array<int, string>  $headings
     * @param  iterable<int, array>  $rows
     */
    public function pdf(string $filename, string $title, array $headings, iterable $rows): Response
    {
        $pdf = Pdf::loadView('exports.table', [
            'title' => $title,
            'headings' => $headings,
            'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename.'.pdf');
    }

    /**
     * Dispatch to the requested format: csv | excel | pdf.
     */
    public function download(string $format, string $filename, string $title, array $headings, iterable $rows): Response|StreamedResponse
    {
        return match ($format) {
            'excel' => $this->excel($filename, $headings, $rows),
            'pdf' => $this->pdf($filename, $title, $headings, $rows),
            default => $this->csv($filename, $headings, $rows),
        };
    }
}
