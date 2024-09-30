<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuardsExport implements FromView, WithEvents, WithColumnWidths, WithStyles
{

    public function __construct(protected $guards)
    {
    }

    public function view(): View
    {
        return view('guards-excel', [
            'guards' => $this->guards
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 9,
            'C' => 15,
            'D' => 12,
            'E' => 15,
            'F' => 12,
            'G' => 20,
            'H' => 10,
            'I' => 10,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
