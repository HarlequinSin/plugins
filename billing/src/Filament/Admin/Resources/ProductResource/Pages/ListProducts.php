<?php

namespace Boy132\Billing\Filament\Admin\Resources\ProductResource\Pages;

use Boy132\Billing\Filament\Admin\Resources\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Product'),
        ];
    }
}
