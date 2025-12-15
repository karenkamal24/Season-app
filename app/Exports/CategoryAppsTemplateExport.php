<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryAppsTemplateExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        // Return empty collection for template
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'name_ar',
            'name_en',
            'icon',
            'url',
            'is_active',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

