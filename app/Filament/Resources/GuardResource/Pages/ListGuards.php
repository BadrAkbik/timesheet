<?php

namespace App\Filament\Resources\GuardResource\Pages;

use App\Filament\Resources\GuardResource;
use App\Imports\GuardsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
                })
        ];
    }
}
