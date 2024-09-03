<?php

namespace App\Filament\Pages\Auth;

use App\Models\Role;
use Filament\Forms\Components\Hidden;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        Hidden::make('role_id')->default(Role::firstOrCreate(['name' => 'user'])->id),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}
