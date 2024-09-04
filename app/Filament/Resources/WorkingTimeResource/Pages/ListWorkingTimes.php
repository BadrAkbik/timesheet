<?php

namespace App\Filament\Resources\WorkingTimeResource\Pages;

use App\Filament\Resources\WorkingTimeResource;
use App\Imports\WorkingTimesImport;
use App\Models\Guard;
use App\Models\Site;
use App\Models\WorkingTime;
use ArPHP\I18N\Arabic;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class ListWorkingTimes extends ListRecords
{
    protected static string $resource = WorkingTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('import')
                ->label(__('attributes.import'))
                ->color('info')
                ->form([
                    FileUpload::make('workingTimes')
                        ->label(__('dashboard.working_times'))->disk('public')->directory('excels/working_times')
                        ->required(),
                    Select::make('site')
                        ->label(__('attributes.site_name'))
                        ->options(Site::all()->pluck('name', 'id'))
                        ->required()
                ])
                ->action(function (array $data) {
                    if (isset($data['workingTimes'])) {
                        try {
                            Excel::import(new WorkingTimesImport($data['site']), Storage::disk('public')->path($data['workingTimes']));
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
            Action::make('report_export')
                ->label(__('attributes.export_reports'))
                ->color('danger')
                ->form([
                    Select::make('site')
                        ->label(__('attributes.site_name'))
                        ->options(Site::whereHas('guards', function ($query) {
                            $query->whereHas('workingTimes');
                        })->pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $set('guard', null);
                        })
                        ->required(),
                    Select::make('guard')
                        ->label(__('dashboard.the_guard'))
                        ->options(
                            function (Get $get) {
                                return Guard::where('site_id', $get('site'))->whereHas('workingTimes')->get()->mapWithKeys(function ($guard) {
                                    return [$guard->id => $guard->name . ' - ' . $guard->guard_number];
                                });
                            }
                        )
                        ->placeholder(__('attributes.all_guards'))
                        ->helperText(__('attributes.guards_select_helper'))
                        ->searchable(static fn(Select $component) => !$component->isDisabled())
                        ->live()
                        ->preload()
                        ->disabled(fn(Get $get) => $get('site') ? false : true)
                ])
                ->action(function (array $data) {
                    $site = Site::find($data['site']);

                    if (isset($data['guard'])) {
                        $guard_name = Guard::find($data['guard'])->name;
                        $guards = Guard::with('workingTimes')->where('id', $data['guard'])->get();
                    } else {
                        $guards = Guard::with('workingTimes')->whereHas('workingTimes')->whereBelongsTo($site)->get();
                        $guard_name = '';
                    }
                    $reportHtml = view('pdf', ['guards' => $guards])->render();
                    $arabic = new Arabic();
                    $p = $arabic->arIdentify($reportHtml);

                    for ($i = count($p) - 1; $i >= 0; $i -= 2) {
                        $utf8ar = $arabic->utf8Glyphs(substr($reportHtml, $p[$i - 1], $p[$i] - $p[$i - 1]));
                        $reportHtml = substr_replace($reportHtml, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
                    }
                    $pdf = PDF::loadHTML($reportHtml);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, $site->name . '- ' . $guard_name . '.pdf');
                }),
        ];
    }
}
