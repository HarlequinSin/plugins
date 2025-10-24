<?php

namespace Boy132\Billing\Filament\Admin\Resources\Orders\Pages;

use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Filament\Admin\Resources\Orders\OrderResource;
use Boy132\Billing\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getDefaultActiveTab(): string
    {
        return 'active';
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', OrderStatus::Pending))
                ->badge(fn () => Order::where('status', OrderStatus::Pending)->count()),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', OrderStatus::Active))
                ->badge(fn () => Order::where('status', OrderStatus::Active)->count()),

            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', OrderStatus::Closed))
                ->badge(fn () => Order::where('status', OrderStatus::Closed)->count()),

            'all' => Tab::make(trans('tickets::tickets.all'))
                ->badge(fn () => Order::count()),
        ];
    }
}
