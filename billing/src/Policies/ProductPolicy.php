<?php

namespace Boy132\Billing\Policies;

use App\Policies\DefaultPolicies;

class ProductPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'product';
}
