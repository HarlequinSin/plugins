<?php

namespace Boy132\Tickets;

use App\Models\Role;
use Filament\Contracts\Plugin;
use Filament\Panel;

class TicketsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'tickets';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\Tickets\\Filament\\$id\\Resources");

        Role::registerCustomDefaultPermissions('ticket');
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
