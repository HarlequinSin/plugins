<?php

namespace Boy132\Billing\Policies;

use App\Policies\DefaultPolicies;

class CustomerPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'customer';
}
