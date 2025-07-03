<?php

namespace Boy132\Tickets\Filament\Components\Actions;

use Boy132\Tickets\Models\Ticket;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class AnswerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'answer';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize(fn (Ticket $ticket) => auth()->user()->can('update ticket', $ticket));

        $this->hidden(fn (Ticket $ticket) => $ticket->is_answered || $ticket->assigned_user_id !== auth()->user()->id);

        $this->label(trans('tickets::tickets.answer_verb'));

        $this->icon('tabler-edit');

        $this->color('primary');

        $this->form([
            MarkdownEditor::make('answer')
                ->required()
                ->hiddenLabel(),
        ]);

        $this->action(function (Ticket $ticket, array $data) {
            $ticket->answer($data['answer']);

            Notification::make()
                ->title(trans('tickets::tickets.notifications.answered'))
                ->success()
                ->send();
        });
    }
}
