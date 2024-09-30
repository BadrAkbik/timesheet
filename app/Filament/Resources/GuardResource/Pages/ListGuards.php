<?php

namespace App\Filament\Resources\GuardResource\Pages;

use App\Exports\GuardsExport;
use App\Filament\Resources\GuardResource;
use App\Imports\GuardsImport;
use App\Models\Guard;
use App\Models\Site;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListGuards extends ListRecords
{
    protected static string $resource = GuardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('download_example')
                ->label(__('attributes.download_excel_example'))
                ->color('warning')
                ->action(function () {
                    if (!Storage::disk('public')->exists('guards_example.xlsx')) {
                        abort(404);
                    }
                    return Storage::disk('public')->download('guards_example.xlsx');
                }),
            Action::make('import')
                ->label(__('attributes.import'))
                ->color('info')
                ->form([
                    FileUpload::make('guards')
                        ->label(__('dashboard.guards'))->disk('public')->directory('excels/guards')
                ])
                ->action(function (array $data) {
                    if (isset($data['guards'])) {
                        try {
                            Excel::import(new GuardsImport, Storage::disk('public')->path($data['guards']));
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            foreach ($failures as $failure) {
                                Notification::make()
                                    ->title('يوجد مشكلة في ملف الاكسل')
                                    ->body('يوجد مشكلة في السطر' . $failure->row() . ': ' . $failure->errors()[0])
                                    ->duration(10000)
                                    ->danger()
                                    ->send();
                            }
                        }
                    }
                }),
            Action::make('guards_export')
                ->label(__('attributes.export_guards'))
                ->color('danger')
                ->form([
                    Select::make('site')
                        ->label(__('attributes.site_name'))
                        ->options(Site::whereHas('guards')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),
                    ToggleButtons::make('format')
                        ->label(__('attributes.format'))
                        ->required()
                        ->colors([
                            'pdf' => 'info',
                            'excel' => 'success',
                        ])
                        ->options([
                            'pdf' => 'Pdf',
                            'excel' => 'Excel',
                        ])
                        ->inline()
                ])
                ->action(function (array $data) {
                    $site_name = Site::find($data['site'])?->name;

                    $guards = Guard::with('site')->where('site_id', $data['site'])->get();
                    $reportHtml = view('guards-pdf', ['guards' => $guards])->render();
                    $arabic = new Arabic();
                    $p = $arabic->arIdentify($reportHtml);

                    for ($i = count($p) - 1; $i >= 0; $i -= 2) {
                        $utf8ar = $arabic->utf8Glyphs(substr($reportHtml, $p[$i - 1], $p[$i] - $p[$i - 1]));
                        $reportHtml = substr_replace($reportHtml, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
                    }
                    $pdf = Pdf::loadHTML($reportHtml);
                    if ($data['format'] === 'pdf') {
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, "{$site_name} - الحراس.pdf");
                        
                    } elseif ($data['format'] === 'excel') {
                        return Excel::download(new GuardsExport($guards), "{$site_name} - الحراس.xlsx");
                    }
                }),
        ];
    }
}
