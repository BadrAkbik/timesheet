<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkingTimeResource\Pages;
use App\Models\WorkingTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use HusamTariq\FilamentTimePicker\Forms\Components\TimePickerField;
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
                        Select::make('guard_number')
                            ->label(__('dashboard.the_guard'))
                            ->relationship('secguard', 'name')
                            ->required(),
                    ])->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->filters([
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
                    })
            ])
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
