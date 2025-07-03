<?php

namespace Boy132\Billing\Filament\Admin\Resources\CustomerResource\RelationManagers;

use App\Filament\Admin\Resources\ServerResource\Pages\EditServer;
use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Filament\Admin\Resources\ProductResource\Pages\EditProduct;
use Boy132\Billing\Models\Customer;
use Boy132\Billing\Models\Order;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

/**
 * @method Customer getOwnerRecord()
 */
class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->sortable()
                    ->badge(),
                TextColumn::make('server.name')
                    ->label('Server')
                    ->icon('tabler-brand-docker')
                    ->sortable()
                    ->url(fn (Order $order): ?string => $order->server ? EditServer::getUrl(['record' => $order->server]) : null),
                TextColumn::make('productPrice.product.name')
                    ->label('Product')
                    ->icon('tabler-package')
                    ->sortable()
                    ->url(fn (Order $order): string => EditProduct::getUrl(['record' => $order->productPrice->product])),
                TextColumn::make('productPrice.name')
                    ->label('Price')
                    ->sortable(),
                TextColumn::make('productPrice.cost')
                    ->label('Cost')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter(auth()->user()->language, NumberFormatter::CURRENCY);

                        return $formatter->formatCurrency($state, config('billing.currency'));
                    }),
            ])
            ->actions([
                Action::make('activate')
                    ->visible(fn (Order $order) => $order->status !== OrderStatus::Active)
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Order $order) => $order->activate()),
                Action::make('close')
                    ->visible(fn (Order $order) => $order->status === OrderStatus::Active)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Order $order) => $order->close()),
            ])
            ->emptyStateHeading('No Orders')
            ->emptyStateDescription('');
    }
}
