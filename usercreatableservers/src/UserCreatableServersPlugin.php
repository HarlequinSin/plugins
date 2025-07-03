<?php

namespace Boy132\UserCreatableServers;

use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class UserCreatableServersPlugin implements Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'usercreatableservers';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\UserCreatableServers\\Filament\\$id\\Resources");
        $panel->discoverWidgets(plugin_path($this->getId(), "src/Filament/$id/Widgets"), "Boy132\\UserCreatableServers\\Filament\\$id\\Widgets");
    }

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('database_limit')
                ->label('Default database limit')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(fn () => config('usercreatableservers.database_limit')),
            TextInput::make('allocation_limit')
                ->label('Default allocation limit')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(fn () => config('usercreatableservers.allocation_limit')),
            TextInput::make('backup_limit')
                ->label('Default backup limit')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(fn () => config('usercreatableservers.backup_limit')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'UCS_DEFAULT_DATABASE_LIMIT' => $data['database_limit'],
            'UCS_DEFAULT_ALLOCATION_LIMIT' => $data['allocation_limit'],
            'UCS_DEFAULT_BACKUP_LIMIT' => $data['backup_limit'],
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

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
