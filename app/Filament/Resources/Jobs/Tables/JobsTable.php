<?php

namespace App\Filament\Resources\Jobs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('queue')
                    ->label(__('jobs.queue'))
                    ->sortable()
                    ->badge(),
                TextColumn::make('payload')
                    ->label(__('jobs.payload'))
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('attempts')
                    ->label(__('jobs.attempts'))
                    ->sortable(),
                TextColumn::make('reserved_at')
                    ->label(__('jobs.reserved_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('available_at')
                    ->label(__('jobs.available_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make('view_payload')
                    ->modalHeading('')
                    ->modalWidth(Width::ThreeExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
