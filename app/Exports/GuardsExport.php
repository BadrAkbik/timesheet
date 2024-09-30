<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GuardsExport implements FromView, WithEvents, WithColumnWidths
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
