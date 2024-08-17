<?php

namespace Basketin\Component\Cart\Filament\Resources;

use Basketin\Component\Cart\Filament\Resources\CartResource\Pages\ListCarts;
use Basketin\Component\Cart\Filament\Resources\CartResource\Pages\ViewCart;
use Basketin\Component\Cart\Filament\Resources\CartResource\RelationManagers\FieldsRelationManager;
use Basketin\Component\Cart\Filament\Resources\CartResource\RelationManagers\QuotesRelationManager;
use Basketin\Component\Cart\Models\Cart;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Basketin';

    protected static ?string $slug = 'basketin/carts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ulid')
                    ->label('ULID')
                    ->searchable(),

                TextColumn::make('currency')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'open' => 'gray',
                        'checkout' => 'success',
                        'abandoned' => 'danger',
                        're-open' => 'warning',
                    })
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'checkout' => 'Checkout',
                        'abandoned' => 'Abandoned',
                        're-open' => 'Re-Open',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuotesRelationManager::class,
            FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarts::route('/'),
            'view' => ViewCart::route('/{record}'),
        ];
    }
}
