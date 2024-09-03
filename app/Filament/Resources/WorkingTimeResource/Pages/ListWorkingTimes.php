<?php

namespace App\Filament\Resources\WorkingTimeResource\Pages;

use App\Filament\Resources\WorkingTimeResource;
use App\Imports\WorkingTimesImport;
use App\Models\Site;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
                })
        ];
    }
}
