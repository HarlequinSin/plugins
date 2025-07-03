<?php

namespace Boy132\Tickets\Policies;

use App\Policies\DefaultPolicies;

class TicketPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'ticket';
}
