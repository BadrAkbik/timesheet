<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;


    public static function getNavigationLabel(): string
    {
        return __('attributes.roles');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.roles');
    }

    public static function canEdit($record): bool
    {
        return $record->name !== 'owner' && auth()->user()->hasPermission('role.update');
    }

    public static function canDelete($record): bool
    {
        return $record->name !== 'owner' && auth()->user()->hasPermission('role.delete');
    }


    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('attributes.role'))
                    ->required()
                    ->unique()
                    ->hiddenOn('edit')
                    ->maxLength(255),
                Select::make('permissions')
                    ->label(__('attributes.permissions'))
                    ->relationship('permissions', 'id')
                    ->multiple()
                    ->live()
                    ->preload()
                    ->exists('permissions', 'id')
                    ->options(
                        Permission::all()->mapWithKeys(function ($permission) {
                            return [$permission->id => $permission->name . ' - ' . $permission->name_ar];
                        })
                    )
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
                    ->label(__('attributes.role'))
                    ->searchable(),
                TextColumn::make('permissions.name')
                    ->label(__('attributes.permissions'))
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(10)
                    ->expandableLimitedList()
                    ->wrap()
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
