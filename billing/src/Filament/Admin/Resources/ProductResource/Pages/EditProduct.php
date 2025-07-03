<?php

namespace Boy132\Billing\Filament\Admin\Resources\ProductResource\Pages;

use Boy132\Billing\Filament\Admin\Resources\ProductResource;
use Boy132\Billing\Filament\Admin\Resources\ProductResource\RelationManagers\PriceRelationManager;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            $this->getSaveFormAction()->formId('form'),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            PriceRelationManager::class,
        ];
    }
}
