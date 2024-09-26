<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string
    {
        return __('attributes.users');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.users');
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();

        $hasSuperAdminRole = $record->roles->contains(function ($role) {
            return $role->name === 'super_admin';
        });
        return !$hasSuperAdminRole && $user->can('delete_user');
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $hasSuperAdminRole = $record->roles->contains(function ($role) {
            return $role->name === 'super_admin';
        });

        return !$hasSuperAdminRole && $user->can('update_user');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('attributes.name'))
                        ->minLength(2)->maxLength(15)->string()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label(__('attributes.email'))
                        ->email()
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label(__('attributes.password'))
                        ->password()
                        ->hiddenOn('edit')
                        ->required()
                        ->maxLength(255),
                    Select::make('roles')
                        ->label(__('filament-shield::filament-shield.resource.label.roles'))
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Hidden::make('email_verified_at')->default(now())
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('attributes.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('attributes.email'))
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('filament-shield::filament-shield.resource.label.roles'))
                    ->badge()
                    ->color(function ($record) {
                        return $record->roles == 'owner' ? 'danger' : 'warning';
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
