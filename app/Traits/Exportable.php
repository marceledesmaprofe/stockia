<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

trait Exportable
{
    /**
     * Export records to CSV format
     *
     * @param mixed $query
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function exportToCsv($query, string $filename)
    {
        $headers = $this->getCsvHeaders();
        $records = $query->get();

        $csvContent = $this->buildCsvContent($headers, $records);

        $response = response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Export records to PDF format
     *
     * @param mixed $query
     * @param string $filename
     * @param string $title
     * @return \Illuminate\Http\Response
     */
    public function exportToPdf($query, string $filename, string $title = '')
    {
        $headers = $this->getCsvHeaders(); // Reusing CSV headers for PDF columns
        $records = $query->get();

        $pdf = Pdf::loadView('exports.pdf-template', [
            'headers' => $headers,
            'records' => $records,
            'title' => $title ?: ucfirst(class_basename($this)) . ' Report'
        ]);

        return $pdf->download($filename);
    }

    /**
     * Get headers for CSV export
     *
     * @return array
     */
    abstract public function getCsvHeaders(): array;

    /**
     * Build CSV content from headers and records
     *
     * @param array $headers
     * @param \Illuminate\Support\Collection $records
     * @return string
     */
    protected function buildCsvContent(array $headers, $records): string
    {
        $csv = implode(',', $headers) . "\n";

        foreach ($records as $record) {
            $row = [];
            foreach ($headers as $header) {
                $value = $this->formatValueForExport($record, $header);

                // Escape commas and wrap in quotes if needed
                if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
                }

                $row[] = $value;
            }

            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Format a value for export
     *
     * @param object $record
     * @param string $header
     * @return string
     */
    protected function formatValueForExport($record, string $header): string
    {
        $value = $record->{$header} ?? '';

        // Handle special fields
        if ($header === 'category_name' && isset($record->category)) {
            $value = $record->category->name ?? 'Uncategorized';
        } elseif ($header === 'product_count' && isset($record->products)) {
            $value = $record->products->count();
        } elseif ($header === 'status') {
            $value = $value ? 'Active' : 'Inactive';
        }

        // Format the value for export
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        return (string) $value;
    }
}