<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Election;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class VotersTemplateExport extends DefaultValueBinder implements FromArray, WithHeadings, WithTitle, WithCustomValueBinder
{
    public function __construct(
        private readonly Election $election,
    ) {}

    public function title(): string
    {
        return 'Voters';
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Phone',
        ];
    }

    public function array(): array
    {
        return [
            [
                'John Doe',
                'john.doe@example.com',
                '2348012345678',
            ],
        ];
    }

    //  Force phone column as string at cell level
    public function bindValue(Cell $cell, $value)
    {
        // Phone column is 3rd column (C)
        if ($cell->getColumn() === 'C') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}