<?php

namespace Boy132\Register\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->name('username')
            ->statePath('username');
    }
}
