<?php

namespace Boy132\Billing\Filament\Admin\Resources\ProductResource\Pages;

use Boy132\Billing\Filament\Admin\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
        ];
    }
}
