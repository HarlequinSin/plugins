<?php

namespace Boy132\Tickets\Filament\Server\Resources\TicketResource\Pages;

use Boy132\Tickets\Filament\Server\Resources\TicketResource;
use Boy132\Tickets\Models\Ticket;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageTickets extends ManageRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->using(function (array $data) {
                    $data['server_id'] ??= Filament::getTenant()->getKey();
                    $data['author_id'] ??= auth()->user()->id;

                    return Ticket::create($data);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'unanswered' => Tab::make(trans('tickets::tickets.unanswered'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_answered', false))
                ->badge(fn () => Ticket::where('server_id', Filament::getTenant()->getKey())->where('is_answered', false)->count()),

            'answered' => Tab::make(trans('tickets::tickets.answered'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_answered', true))
                ->badge(fn () => Ticket::where('server_id', Filament::getTenant()->getKey())->where('is_answered', true)->count()),
        ];
    }
}
