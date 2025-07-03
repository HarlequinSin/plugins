<?php

namespace Boy132\Billing\Filament\Admin\Resources\CustomerResource\Pages;

use Boy132\Billing\Filament\Admin\Resources\CustomerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Customer'),
        ];
    }
}
