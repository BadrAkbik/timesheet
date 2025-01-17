<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuardResource\Pages;
use App\Models\Guard;
use App\Models\Site;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class GuardResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Guard::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.guards');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.guard');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.guards');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('attributes.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('site_id')
                            ->label(__('attributes.site_name'))
                            ->relationship('site', 'name')
                            ->exists('sites', 'id')
                            ->live()
                            ->preload()
                            ->nullable(),
                        TextInput::make('guard_number')
                            ->label(__('attributes.guard_number'))
                            ->disabled(fn(Get $get) => $get('site_id') ? false : true)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('site_id', $get('site_id'));
                            })
                            ->validationMessages([
                                'unique' => 'قيمة هذا الحقل مستخدمة من قبل ضمن نفس الموقع',
                            ])
                            ->required()
                            ->maxLength(255),
                        TextInput::make('id_number')
                            ->label(__('attributes.id_number'))
                            ->required()
                            ->maxLength(255),
                        Select::make('job_title_id')
                            ->label(__('attributes.the_job_title'))
                            ->relationship('jobTitle', 'name')
                            ->exists('job_titles', 'id')
                            ->preload()
                            ->nullable(),
                        TextInput::make('phone')
                            ->label(__('attributes.phone_number'))
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        DatePicker::make('start_date')
                            ->label(__('attributes.start_date')),
                        FileUpload::make('image')
                            ->label(__('attributes.image'))
                            ->disk('public')
                            ->directory('images/guards_images')
                            ->image()
                            ->nullable(),
                        TextInput::make('iban')
                            ->label(__('attributes.iban'))
                            ->maxLength(255)
                            ->string(),
                        TextInput::make('bank')
                            ->label(__('attributes.bank'))
                            ->maxLength(255)
                            ->string(),
                        TextInput::make('salary')
                            ->label(__('attributes.salary'))
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->maxValue(99999999),
                        Toggle::make('active')
                            ->default(true)
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 200, 300, 400, 500])
            ->columns([
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->searchable(),
                TextColumn::make('guard_number')
                    ->label(__('attributes.guard_number'))
                    ->sortable()
                    ->numeric()
                    ->searchable(),
                TextColumn::make('id_number')
                    ->label(__('attributes.id_number'))
                    ->searchable(),
                TextColumn::make('site.name')
                    ->label(__('attributes.site_name'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jobTitle.name')
                    ->label(__('attributes.the_job_title'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('attributes.phone_number'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->date()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label(__('attributes.image'))
                    ->circular()
                    ->disk('public'),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('iban')
                    ->label(__('attributes.iban')),
                TextColumn::make('bank')
                    ->label(__('attributes.bank')),
                TextColumn::make('salary')
                    ->numeric()
                    ->separator(',')
                    ->label(__('attributes.salary')),
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
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('site_id')
                    ->label(__('attributes.site'))
                    ->relationship('site', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListGuards::route('/'),
            'create' => Pages\CreateGuard::route('/create'),
            'edit' => Pages\EditGuard::route('/{record}/edit'),
        ];
    }
}
