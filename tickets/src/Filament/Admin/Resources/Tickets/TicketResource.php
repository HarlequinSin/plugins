<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets;

use App\Filament\Admin\Resources\Servers\Pages\EditServer;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Tickets\Enums\TicketCategory;
use Boy132\Tickets\Enums\TicketPriority;
use Boy132\Tickets\Filament\Admin\Resources\Tickets\Pages\ManageTickets;
use Boy132\Tickets\Filament\Components\Actions\AnswerAction;
use Boy132\Tickets\Filament\Components\Actions\AssignToMeAction;
use Boy132\Tickets\Models\Ticket;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Markdown;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-ticket';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Ticket::where('is_answered', false)->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->description(fn (Ticket $ticket) => Markdown::inline($ticket->description ?? ''))
                    ->sortable()
                    ->searchable()
                    ->grow(),
                TextColumn::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('assignedUser.username')
                    ->label(trans('tickets::tickets.assigned_to'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.noone'))
                    ->url(fn (Ticket $ticket) => $ticket->assignedUser && auth()->user()->can('update user', $ticket->assignedUser) ? EditUser::getUrl(['record' => $ticket->assignedUser]) : null)
                    ->toggleable(),
                TextColumn::make('server.name')
                    ->label(trans('tickets::tickets.server'))
                    ->icon('tabler-brand-docker')
                    ->url(fn (Ticket $ticket) => auth()->user()->can('update server', $ticket->server) ? EditServer::getUrl(['record' => $ticket->server]) : null)
                    ->toggleable(),
                TextColumn::make('author.username')
                    ->label(trans('tickets::tickets.created_by'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.unknown'))
                    ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author]) : null)
                    ->toggleable(),
                DateTimeColumn::make('created_at')
                    ->label(trans('tickets::tickets.created_at'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    AnswerAction::make(),
                    AssignToMeAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->groups([
                Group::make('category')
                    ->label(trans('tickets::tickets.category')),
                Group::make('priority')
                    ->label(trans('tickets::tickets.priority')),
                Group::make('server.name')
                    ->label(trans('tickets::tickets.server')),
                Group::make('author.username')
                    ->label(trans('tickets::tickets.created_by')),
            ])
            ->emptyStateIcon('tabler-ticket')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('tickets::tickets.no_tickets'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                TextInput::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->required()
                    ->columnSpanFull(),
                Select::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->required()
                    ->options(TicketCategory::class),
                Select::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->required()
                    ->options(TicketPriority::class)
                    ->default(TicketPriority::Normal),
                Select::make('server_id')
                    ->label(trans('tickets::tickets.server'))
                    ->required()
                    ->relationship('server', 'name'),
                MarkdownEditor::make('description')
                    ->label(trans('tickets::tickets.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->columnSpanFull(),
                TextEntry::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->badge(),
                TextEntry::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->badge(),
                TextEntry::make('description')
                    ->label(trans('tickets::tickets.description'))
                    ->columnSpanFull()
                    ->markdown()
                    ->placeholder(trans('tickets::tickets.no_description')),
                TextEntry::make('answer')
                    ->visible(fn (Ticket $ticket) => $ticket->is_answered)
                    ->label(trans('tickets::tickets.answer_noun'))
                    ->columnSpanFull()
                    ->markdown(),
                TextEntry::make('server.name')
                    ->label(trans('tickets::tickets.server'))
                    ->icon('tabler-brand-docker')
                    ->url(fn (Ticket $ticket) => auth()->user()->can('update server', $ticket->server) ? EditServer::getUrl(['record' => $ticket->server]) : null),
                TextEntry::make('server.user.username')
                    ->label(trans('tickets::tickets.owner'))
                    ->icon('tabler-user')
                    ->url(fn (Ticket $ticket) => auth()->user()->can('update user', $ticket->server->user) ? EditUser::getUrl(['record' => $ticket->server->user]) : null),
                TextEntry::make('author.username')
                    ->label(trans('tickets::tickets.created_by'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.unknown'))
                    ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author]) : null),
                TextEntry::make('created_at')
                    ->label(trans('tickets::tickets.created_at'))
                    ->dateTime(timezone: auth()->user()->timezone ?? config('app.timezone', 'UTC')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTickets::route('/'),
        ];
    }
}
