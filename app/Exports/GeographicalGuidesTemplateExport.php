<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeographicalGuidesTemplateExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        // Return empty collection for template
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'service_name',
            'description',
            'phone_1',
            'phone_2',
            'address',
            'latitude',
            'longitude',
            'website',
            'commercial_register',
            'is_active',
            'status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}


