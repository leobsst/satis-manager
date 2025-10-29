<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->label(label: 'Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make(name: 'email')
                    ->label(label: 'Adresse e-mail')
                    ->searchable(),
                TextColumn::make(name: 'roles.name')
                    ->label(label: 'Rôles')
                    ->badge()
                    ->toggleable(),
                TextColumn::make(name: 'created_at')
                    ->label(label: 'Créé le')
                    ->sortable()
                    ->formatStateUsing(callback: function ($record): mixed {
                        return $record->created_at->format('d/m/Y');
                    })
                    ->toggleable(),
            ])
            ->recordActions(actions: [
                ActionGroup::make(actions: [
                    EditAction::make()
                        ->modalHeading(heading: 'Modification de l\'utilisateur')
                        ->modalWidth(width: 'md'),
                    DeleteAction::make()
                        ->hidden(condition: fn ($record): mixed => $record->hasRole('admin')),
                ])->button()->color(color: 'gray'),
            ])
            ->toolbarActions(actions: [
                BulkActionGroup::make(actions: [
                    DeleteBulkAction::make(),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(callback: fn ($record): bool => ! $record->hasRole('admin'));
    }
}
