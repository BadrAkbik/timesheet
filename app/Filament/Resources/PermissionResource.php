<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function getNavigationLabel(): string
    {
        return __('attributes.permissions');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.permissions');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('roles')
                    ->label(__('attributes.roles'))
                    ->relationship('roles', 'id')
                    ->multiple()
                    ->live()
                    ->notIn(Role::firstWhere('name', 'owner')->id)
                    ->preload()
                    ->exists('roles', 'id')
                    ->options(Role::whereNot('name', 'owner')->pluck('name', 'id'))
                    ->searchable(),
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
                    ->label(__('attributes.permission'))
                    ->searchable(),
                TextColumn::make('name_ar')
                    ->label(__('attributes.permission_ar'))
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('attributes.role'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListPermissions::route('/'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
