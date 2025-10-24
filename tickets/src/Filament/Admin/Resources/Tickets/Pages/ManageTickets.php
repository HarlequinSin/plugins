<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Admin\Resources\Tickets\TicketResource;
use Boy132\Tickets\Models\Ticket;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
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
            'my' => Tab::make(trans('tickets::tickets.assigned_to_me'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_answered', false)->where('assigned_user_id', auth()->user()->id))
                ->badge(fn () => Ticket::where('is_answered', false)->where('assigned_user_id', auth()->user()->id)->count()),

            'unanswered' => Tab::make(trans('tickets::tickets.unanswered'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_answered', false))
                ->badge(fn () => Ticket::where('is_answered', false)->count()),

            'answered' => Tab::make(trans('tickets::tickets.answered'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_answered', true))
                ->badge(fn () => Ticket::where('is_answered', true)->count()),

            'all' => Tab::make(trans('tickets::tickets.all'))
                ->badge(fn () => Ticket::count()),
        ];
    }
}
