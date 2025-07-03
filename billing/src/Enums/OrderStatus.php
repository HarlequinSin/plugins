<?php

namespace Boy132\Billing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Active = 'active';
    case Closed = 'closed';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Closed => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'tabler-circle-dotted',
            self::Active => 'tabler-circle-check',
            self::Closed => 'tabler-circle-x',
        };
    }

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
