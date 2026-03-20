<?php

namespace App\Exports;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    /**
     * Export data to Excel
     */
    public static function toExcel($data, $filename, $title, $headings = null)
    {
        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return Excel::download(
                new class($data, $title, $headings) implements
                    \Maatwebsite\Excel\Concerns\FromArray,
                    \Maatwebsite\Excel\Concerns\WithHeadings,
                    \Maatwebsite\Excel\Concerns\WithTitle,
                    \Maatwebsite\Excel\Concerns\WithStyles,
                    \Maatwebsite\Excel\Concerns\ShouldAutoSize
                {
                    private $data;
                    private $title;
                    private $headings;

                    public function __construct($data, $title, $headings)
                    {
                        // Convert collection to array if needed
                        $this->data = $data;
                        $this->title = $title;

                        // Handle headings
                        if ($headings) {
                            $this->headings = $headings;
                        } else {
                            // Get first item to extract headings
                            $firstItem = null;
                            if (is_array($data) && !empty($data)) {
                                $firstItem = $data[0];
                            } elseif (is_object($data) && method_exists($data, 'first') && $data->first()) {
                                $firstItem = $data->first();
                            }

                            if ($firstItem) {
                                if (is_object($firstItem) && method_exists($firstItem, 'toArray')) {
                                    $this->headings = array_keys($firstItem->toArray());
                                } elseif (is_array($firstItem)) {
                                    $this->headings = array_keys($firstItem);
                                } else {
                                    $this->headings = [];
                                }
                            } else {
                                $this->headings = [];
                            }
                        }
                    }

                    public function array(): array
                    {
                        // Convert data to array format
                        if (is_object($this->data) && method_exists($this->data, 'toArray')) {
                            return $this->data->toArray();
                        }

                        if (is_array($this->data)) {
                            // Ensure all items are arrays
                            return array_map(function($item) {
                                if (is_object($item) && method_exists($item, 'toArray')) {
                                    return $item->toArray();
                                }
                                return (array) $item;
                            }, $this->data);
                        }

                        return [];
                    }

                    public function headings(): array
                    {
                        return $this->headings;
                    }

                    public function title(): string
                    {
                        return $this->title;
                    }

                    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                    {
                        return [
                            1 => ['font' => ['bold' => true, 'size' => 12]],
                        ];
                    }
                },
                $filename . '.xlsx'
            );
        }

        // Fallback to CSV
        return self::toCsv($data, $filename);
    }

    /**
     * Export data to PDF
     */
    public static function toPdf($data, $filename, $view, $title)
    {
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            // Convert data to array format for PDF view
            $pdfData = self::convertToArray($data);

            $pdf = Pdf::loadView($view, compact('pdfData', 'title'));
            return $pdf->download($filename . '.pdf');
        }

        // Fallback to CSV
        return self::toCsv($data, $filename);
    }

    /**
     * Export data to CSV (fallback)
     */
    public static function toCsv($data, $filename)
    {
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Convert data to array
            $arrayData = self::convertToArray($data);

            // Add headers
            if (!empty($arrayData)) {
                fputcsv($file, array_keys($arrayData[0]));

                // Add rows
                foreach ($arrayData as $row) {
                    fputcsv($file, array_values($row));
                }
            }

            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper method to convert data to array
     */
    private static function convertToArray($data)
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        if (is_array($data)) {
            return array_map(function($item) {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    return $item->toArray();
                }
                return (array) $item;
            }, $data);
        }

        return [];
    }
}
