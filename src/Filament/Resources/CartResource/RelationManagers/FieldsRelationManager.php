<?php

namespace Obelaw\Basketin\Cart\Filament\Resources\CartResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static ?string $icon = 'heroicon-o-adjustments-horizontal';
    protected static string $relationship = 'fields';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('field_key')->required(),
                TextInput::make('field_value')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('field_key')
                    ->label('Key')
                    ->searchable(),

                TextColumn::make('field_value')
                    ->label('Value')
                    ->searchable(),

            ]);
    }
}
