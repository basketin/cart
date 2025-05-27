<?php

namespace Obelaw\Basketin\Cart\Filament\Resources\CartResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotesRelationManager extends RelationManager
{
    protected static ?string $icon = 'heroicon-o-clipboard';

    protected static string $relationship = 'quotes';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.sku')
                    ->label('SKU')
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('item.original_price')
                    ->label('Original Price')
                    ->searchable(),

                TextColumn::make('quantity'),
            ])
            ->filters([]);
    }
}
