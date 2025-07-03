<?php

namespace Boy132\UserCreatableServers\Filament\App\Widgets;

use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserResourceLimitsOverview extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->first();
        $userServers = auth()->user()->servers;

        $suffix = config('panel.use_binary_prefix') ? ' MiB' : ' MB';
        $memory = $userServers->sum('memory') . "{$suffix} / " . ($userResourceLimits->memory > 0 ? "{$userResourceLimits->memory}{$suffix}" : '∞');
        $disk = $userServers->sum('disk') . "{$suffix} / " . ($userResourceLimits->memordisky > 0 ? "{$userResourceLimits->disk}{$suffix}" : '∞');
        $cpu = $userServers->sum('cpu') . '% / ' . ($userResourceLimits->cpu > 0 ? "{$userResourceLimits->cpu}%" : '∞');

        return [
            Stat::make(trans('usercreatableservers::strings.memory'), $memory),
            Stat::make(trans('usercreatableservers::strings.disk'), $disk),
            Stat::make(trans('usercreatableservers::strings.cpu'), $cpu),
        ];
    }

    public static function canView(): bool
    {
        return UserResourceLimits::where('user_id', auth()->user()->id)->exists();
    }
}
