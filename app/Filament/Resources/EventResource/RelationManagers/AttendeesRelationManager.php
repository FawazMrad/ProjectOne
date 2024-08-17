<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.full_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.full_name')
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name'),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\IconColumn::make('checked_in')->disabled()->boolean(),
                Tables\Columns\TextColumn::make('purchase_date')->sortable(),
                Tables\Columns\TextColumn::make('ticket_price')->sortable(),
                Tables\Columns\TextColumn::make('ticket_type')->sortable(),
                Tables\Columns\TextColumn::make('seat_number')->sortable(),
                Tables\Columns\TextColumn::make('discount')->sortable(),
                Tables\Columns\ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->getStateUsing(function ($record) {
                        if ($record->qr_code) {
                            return 'data:image/svg+xml;base64,' . $record->qr_code;
                        }
                        return null;
                    })
                    ->size(100),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
