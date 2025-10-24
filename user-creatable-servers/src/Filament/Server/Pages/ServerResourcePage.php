<?php

namespace Boy132\UserCreatableServers\Filament\Server\Pages;

use App\Filament\Server\Pages\ServerFormPage;
use App\Models\Server;
use App\Repositories\Daemon\DaemonServerRepository;
use Boy132\UserCreatableServers\Filament\App\Widgets\UserResourceLimitsOverview;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Http\Client\ConnectionException;

class ServerResourcePage extends ServerFormPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-cube-plus';

    protected static ?string $navigationLabel = 'Resource Limits';

    protected static ?string $title = 'Resource Limits';

    public static function canAccess(): bool
    {
        if (!config('user-creatable-servers.can_users_update_servers')) {
            return false;
        }

        /** @var Server $server */
        $server = Filament::getTenant();

        if (!UserResourceLimits::where('user_id', $server->owner_id)->exists()) {
            return false;
        }

        return parent::canAccess();
    }

    protected static ?int $navigationSort = 20;

    public function form(Schema $schema): Schema
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        /** @var UserResourceLimits $userResourceLimits */
        $userResourceLimits = UserResourceLimits::where('user_id', $server->owner_id)->firstOrFail();

        $maxCpu = $server->cpu + $userResourceLimits->getCpuLeft();
        $maxMemory = $server->memory + $userResourceLimits->getMemoryLeft();
        $maxDisk = $server->disk + $userResourceLimits->getDiskLeft();

        $suffix = config('panel.use_binary_prefix') ? 'MiB' : 'MB';

        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                TextInput::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->cpu > 0 ? ($maxCpu - $state . '% ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->cpu > 0 && $maxCpu - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->cpu > 0 ? $maxCpu : null)
                    ->suffix('%'),
                TextInput::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->memory > 0 ? ($maxMemory - $state . $suffix . ' ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->memory > 0 && $maxMemory - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->memory > 0 ? $maxMemory : null)
                    ->suffix($suffix),
                TextInput::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->disk > 0 ? ($maxDisk - $state . $suffix . ' ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->disk > 0 && $maxDisk - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->disk > 0 ? $maxDisk : null)
                    ->suffix($suffix),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResourceLimitsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->formId('form')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var Server $server */
        $server = Filament::getTenant();

        $server->update([
            'cpu' => $data['cpu'],
            'memory' => $data['memory'],
            'disk' => $data['disk'],
        ]);

        try {
            /** @var DaemonServerRepository $repository */
            $repository = app(DaemonServerRepository::class); // @phpstan-ignore-line

            $repository->setServer($server)->sync();

            Notification::make()
                ->title('Server Resource Limits updated')
                ->body('To fully use the new resource limits a server restart might be required.')
                ->success()
                ->persistent()
                ->send();
        } catch (ConnectionException) {
            Notification::make()
                ->title('Server Resource Limits updated')
                ->body('Please manually restart your server to apply the new resource limits.')
                ->warning()
                ->persistent()
                ->send();
        }

        $redirectUrl = self::getUrl();
        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
    }
}
