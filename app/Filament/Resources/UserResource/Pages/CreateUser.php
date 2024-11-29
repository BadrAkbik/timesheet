<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->sitePermissions = $data['sitesPermissions'] ?? null;
        unset($data['sitesPermissions']);
        return $data;
    }

    public function afterCreate(): void
    {

        $this->record->sitesPermissions()->detach();
        if ($this->sitePermissions) {
            foreach ($this->sitePermissions as $sitePermission) {
                $this->record->sitesPermissions()->attach(
                    $sitePermission['site_id'],
                    ['permission_id' => $sitePermission['permission_id']]
                );
            }
        }        
    }
}
