<?php

namespace Boy132\Subdomains;

use App\Contracts\Plugins\HasPluginSettings;
use App\Filament\Admin\Resources\Servers\ServerResource;
use App\Traits\EnvironmentWriterTrait;
use Boy132\Subdomains\Filament\Admin\Resources\Servers\RelationManagers\SubdomainRelationManager;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class SubdomainsPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'subdomains';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        ServerResource::registerCustomRelations(SubdomainRelationManager::class);

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\Subdomains\\Filament\\$id\\Resources");
        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Boy132\\Subdomains\\Filament\\$id\\Pages");
    }

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('token')
                ->label(trans('subdomains::strings.api_token'))
                ->required()
                ->hintIcon('tabler-question-mark')
                ->hintIconTooltip(trans('subdomains::strings.api_token_help'))
                ->default(fn () => config('subdomains.token')),
            TextInput::make('defaultSubdomainLimit')
                ->label(trans('subdomains::strings.default_subdomain_limit'))
                ->integer()
                ->minValue(0)
                ->default(fn () => config('subdomains.defaultSubdomainLimit')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'CLOUDFLARE_TOKEN' => $data['token'],
            'DEFAULT_SUBDOMAIN_LIMIT' => $data['defaultSubdomainLimit']
        ]);

        Notification::make()
            ->title(trans('subdomains::strings.notifications.settings_saved'))
            ->success()
            ->send();
    }
}
