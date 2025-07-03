<?php

namespace Boy132\Billing\Policies;

use App\Policies\DefaultPolicies;

class OrderPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'order';
}
