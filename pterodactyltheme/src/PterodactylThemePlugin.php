<?php

namespace Boy132\PterodactylTheme;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;

class PterodactylThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'pterodactyltheme';
    }

    public const PTERO_GRAY = [
        50 => '245, 247, 250',
        100 => '229, 232, 235',
        200 => '202, 209, 216',
        300 => '154, 165, 177',
        400 => '123, 135, 147',
        500 => '96, 109, 123',
        600 => '75, 87, 99',
        700 => '63, 77, 90',
        800 => '51, 64, 77',
        900 => '31, 41, 51',
        950 => '15, 20, 25',
    ];

    public function register(Panel $panel): void
    {
        $panel
            ->font('IBM Plex Sans')
            ->colors([
                'gray' => self::PTERO_GRAY,
                'primary' => Color::Blue,
            ]);
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
