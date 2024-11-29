<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkingTimeResource\Pages;
use App\Models\Guard;
use App\Models\WorkingTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class WorkingTimeResource extends Resource
{
    protected static ?string $model = WorkingTime::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationLabel(): string
    {
        return __('dashboard.working_times');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.working_time');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.working_times');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('date')
                            ->label(__('attributes.date'))
                            ->required(),
                        TextInput::make('time')
                            ->label(__('attributes.time'))
                            ->mask('99:99:99')
                            ->required(),
                        TextInput::make('period')
                            ->label(__('attributes.period'))
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    if (!in_array($value, ['م', 'ص'])) {
                                        $fail(__('attributes.period_validation'));
                                    }
                                },
                            ])
                            ->required(),
                        Select::make('site_id')
                            ->label(__('attributes.site_name'))
                            ->relationship('site', 'name')
                            ->exists('sites', 'id')
                            ->preload()
                            ->searchable(),
                        TextInput::make('guard_number')
                            ->label(__('dashboard.the_guard'))
                            ->required(),
                    ])->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 200, 300, 400, 500])
            ->columns([
                TextColumn::make('date')
                    ->label(__('attributes.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('time')
                    ->label(__('attributes.time'))
                    ->sortable()
                    ->time(),
                TextColumn::make('period')
                    ->label(__('attributes.period'))
                    ->sortable(),
                TextColumn::make("secguard.name")
                    ->label(__('dashboard.the_guard'))
                    ->searchable(isIndividual: true),
                TextColumn::make('guard_number')
                    ->label(__('attributes.guard_number')),
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
            ->query(function(WorkingTime $workingTime){
                $hasSuperAdminRole = auth()->user()->roles->contains(function ($role) {
                    return $role->name === 'super_admin';
                });

                if ($hasSuperAdminRole) {
                    return $workingTime;
                } else {
                    return $workingTime->whereHas('site.usersPermissions', function ($workingTime) {
                        $workingTime->where('name', 'view_site')->where('permissions_users_sites.user_id', auth()->user()->id);
                    });
                }
            })
            ->filters([
                Filter::make('guard')
                    ->Form([
                        Select::make('site_id')
                            ->label(__('attributes.site_name'))
                            ->relationship('site', 'name')
                            ->exists('sites', 'id')
                            ->live()
                            ->preload()
                            ->nullable(),
                        Select::make('guard')
                            ->label(__('dashboard.the_guard'))
                            ->reactive()
                            ->options(
                                function (Get $get) {
                                    return Guard::where('site_id', $get('site_id'))->whereHas('workingTimes')->get()->mapWithKeys(function ($guard) {
                                        return [$guard->guard_number => $guard->name . ' - ' . $guard->guard_number];
                                    });
                                }
                            )
                            ->disabled(fn(Get $get) => $get('site_id') ? false : true)
                            ->searchable(static fn(Select $component) => !$component->isDisabled())
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['site_id'],
                                fn(Builder $query) => $query->where('site_id', $data['site_id'])
                            )
                            ->when(
                                $data['guard'],
                                fn(Builder $query) => $query->where('guard_number', $data['guard'])->where('site_id', $data['site_id'])
                            );
                    }),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('attributes.from')),
                        Forms\Components\DatePicker::make('to')->label(__('attributes.to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)->filtersFormColumns(2)
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
            'index' => Pages\ListWorkingTimes::route('/'),
            'create' => Pages\CreateWorkingTime::route('/create'),
            'edit' => Pages\EditWorkingTime::route('/{record}/edit'),
        ];
    }
}
