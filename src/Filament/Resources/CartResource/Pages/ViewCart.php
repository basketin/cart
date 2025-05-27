<?php

namespace Obelaw\Basketin\Cart\Filament\Resources\CartResource\Pages;

use Obelaw\Basketin\Cart\Filament\Resources\CartResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Cart data')
                    ->icon('heroicon-m-shopping-bag')
                    ->schema([
                        TextEntry::make('ulid'),

                        TextEntry::make('currency')
                            ->badge(),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'open' => 'gray',
                                'checkout' => 'success',
                                'abandoned' => 'danger',
                                're-open' => 'warning',
                            })
                    ])

            ])->columns(1);
    }
}
