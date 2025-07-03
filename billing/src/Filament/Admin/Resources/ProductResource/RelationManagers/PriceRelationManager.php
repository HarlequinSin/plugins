<?php

namespace Boy132\Billing\Filament\Admin\Resources\ProductResource\RelationManagers;

use Boy132\Billing\Enums\PriceInterval;
use Boy132\Billing\Models\Product;
use Boy132\Billing\Models\ProductPrice;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

/**
 * @method Product getOwnerRecord()
 */
class PriceRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('cost')
                    ->suffix(config('billing.currency'))
                    ->numeric()
                    ->minValue(0),
                Select::make('interval_type')
                    ->options(PriceInterval::class),
                TextInput::make('interval_value')
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('cost')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter(auth()->user()->language, NumberFormatter::CURRENCY);

                        return $formatter->formatCurrency($state, config('billing.currency'));
                    }),
                TextColumn::make('interval')
                    ->state(fn (ProductPrice $price) => $price->interval_value . ' ' . $price->interval_type->name),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Price')
                    ->createAnother(false),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No Prices')
            ->emptyStateDescription('');
    }
}
