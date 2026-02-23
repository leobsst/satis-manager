<?php

declare(strict_types=1);

namespace App\Filament\Resources\FailedJobs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FailedJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('queue')
                    ->label(__('jobs.queue'))
                    ->sortable()
                    ->badge(),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payload')
                    ->label(__('jobs.payload'))
                    ->limit(50)
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('connection')
                    ->label(__('connection'))
                    ->sortable(),
                TextColumn::make('exception')
                    ->label(__('exception'))
                    ->limit(50)
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('failed_at')
                    ->label(__('failed_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make('view_exception')
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
