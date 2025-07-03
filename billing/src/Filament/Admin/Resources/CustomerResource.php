<?php

namespace Boy132\Billing\Filament\Admin\Resources;

use App\Models\User;
use Boy132\Billing\Filament\Admin\Resources\CustomerResource\Pages;
use Boy132\Billing\Models\Customer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'tabler-user-dollar';

    protected static ?string $navigationGroup = 'Billing';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->prefixIcon('tabler-user')
                    ->label('User')
                    ->required()
                    ->relationship('user', 'username')
                    ->searchable(['username', 'email'])
                    ->getOptionLabelFromRecordUsing(fn (User $user) => $user->email . ' | ' . $user->username)
                    ->preload(),
                TextInput::make('balance')
                    ->required()
                    ->suffix(config('billing.currency'))
                    ->numeric()
                    ->minValue(0),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('first_name')
                    ->sortable(),
                TextColumn::make('last_name')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('E-Mail')
                    ->sortable(),
                TextColumn::make('balance')
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter(auth()->user()->language, NumberFormatter::CURRENCY);

                        return $formatter->formatCurrency($state, config('billing.currency'));
                    }),
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->badge(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No Customers')
            ->emptyStateDescription('');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
