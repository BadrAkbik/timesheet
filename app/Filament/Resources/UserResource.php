<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Site;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
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
use Spatie\Permission\Models\Permission;

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

    // public static function canDelete($record): bool
    // {
    //     $user = auth()->user();

    //     $hasSuperAdminRole = $record->roles->contains(function ($role) {
    //         return $role->name === 'super_admin';
    //     });
    //     return !$hasSuperAdminRole && $user->can('delete_user');
    // }

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
                        ->revealable()
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Select::make('roles')
                        ->label(__('filament-shield::filament-shield.resource.label.roles'))
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Hidden::make('email_verified_at')->default(now()),
                    Repeater::make('sitesPermissions')
                        ->label(__('attributes.sites_permissions'))
                        ->schema([
                            Select::make('site_id')
                                ->label(__('attributes.site_name'))
                                ->options(Site::pluck('name', 'id'))
                                ->required(),
                            Select::make('permission_id')
                                ->label(__('attributes.permission'))
                                ->exists('permissions', 'id')
                                ->options(
                                    function () {
                                        return Permission::where('name', 'view_site')->orWhere('name', 'update_site')->pluck('name', 'id')->toArray();
                                    }
                                )
                                ->required(),
                        ])
                        ->columns(2)
                        ->columnSpan(2)
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
            ->query(function (User $user) {
                return $user->where('id', "!=", 1);
            })
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
