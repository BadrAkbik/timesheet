<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function getNavigationLabel(): string
    {
        return __('attributes.sites');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.site');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.sites');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        $hasSuperAdminRole = $user->roles->contains(function ($role) {
            return $role->name === 'super_admin';
        });

        $canUpdate = $record->usersPermissions()
            ->where('name', 'update_site')
            ->wherePivot('user_id', $user->id)
            ->exists();
        return $hasSuperAdminRole || $canUpdate;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('attributes.site_name'))
                            ->required()
                            ->maxLength(255),
                    ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('attributes.name'))
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
            ->query(function (Site $site) {
                $hasSuperAdminRole = auth()->user()->roles->contains(function ($role) {
                    return $role->name === 'super_admin';
                });
                if ($hasSuperAdminRole) {
                    return $site;
                } else {
                    return $site->whereHas('usersPermissions', function ($query) {
                        $query->where('name', 'view_site')->where('permissions_users_sites.user_id', auth()->user()->id);
                    });
                }
            })
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }
}
