<?php

namespace Boy132\UserCreatableServers\Filament\Components\Actions;

use App\Filament\Server\Pages\Console;
use App\Models\Egg;
use App\Services\Servers\RandomWordService;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class CreateServerAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'create_server';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn () => UserResourceLimits::where('user_id', auth()->user()->id)->exists());

        $this->createAnother(false);

        $this->model(UserResourceLimits::class);

        $this->form([
            Group::make([
                TextInput::make('name')
                    ->label(trans('usercreatableservers::strings.name'))
                    ->required()
                    ->default(fn () => (new RandomWordService())->word()),
                Select::make('egg_id')
                    ->label(trans('usercreatableservers::strings.egg'))
                    ->required()
                    ->options(fn () => Egg::all()->mapWithKeys(fn (Egg $egg) => [$egg->id => $egg->name])),
                TextInput::make('memory')
                    ->label(trans('usercreatableservers::strings.memory'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                    ->hint(trans('usercreatableservers::strings.hint_unlimited')),
                TextInput::make('disk')
                    ->label(trans('usercreatableservers::strings.disk'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                    ->hint(trans('usercreatableservers::strings.hint_unlimited')),
                TextInput::make('cpu')
                    ->label(trans('usercreatableservers::strings.cpu'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('%')
                    ->hint(trans('usercreatableservers::strings.hint_unlimited')),
            ])->columns(2),
        ]);

        $this->action(function (array $data) {
            /** @var UserResourceLimits $userResourceLimits */
            $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->first();

            try {
                $server = $userResourceLimits->createServer($data['name'], Egg::findOrFail($data['egg_id']), $data['memory'], $data['disk'], $data['cpu']);
            } catch (Exception $exception) {
                Notification::make()
                    ->title('Could not create server')
                    ->body($exception->getMessage())
                    ->danger()
                    ->send();

                return;
            }

            if ($server) {
                Notification::make()
                    ->title('Server created')
                    ->success()
                    ->send();

                redirect(Console::getUrl(panel: 'server', tenant: $server));
            } else {
                Notification::make()
                    ->title('Could not create server')
                    ->body('Not enough resources')
                    ->danger()
                    ->send();
            }
        });
    }
}
