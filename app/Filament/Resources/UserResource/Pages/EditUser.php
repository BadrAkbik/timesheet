<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;


class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->form->getRecord();

        if ($record) {
            $data['sitesPermissions'] = $record->sitesPermissions->map(function ($permission) {
                return [
                    'permission_id' => $permission->id,
                    'site_id' => $permission->pivot->site_id
                ];
            })->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->sitePermissions = $data['sitesPermissions'] ?? null;
        unset($data['sitesPermissions']);
        return $data;
    }

    public function afterSave(): void
    {
        $this->record->sitesPermissions()->detach();
        if ($this->sitePermissions) {
            foreach ($this->sitePermissions as $sitePermission) {
                $this->record->sitesPermissions()->attach(
                    $sitePermission['permission_id'],
                    ['site_id' => $sitePermission['site_id']]
                );
            }
        }
    }
}
