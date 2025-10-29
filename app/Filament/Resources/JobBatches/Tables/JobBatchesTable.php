<?php

namespace App\Filament\Resources\JobBatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_jobs')
                    ->label(__('jobs.total_jobs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pending_jobs')
                    ->label(__('jobs.pending_jobs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('failed_jobs')
                    ->label(__('jobs.failed_jobs'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('failed_job_ids')
                    ->label(('ID ' . __('jobs.failed_jobs')))
                    ->searchable()
                    ->badge(),
                TextColumn::make('cancelled_at')
                    ->label(__('cancelled_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('finished_at')
                    ->label(__('finished_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make('view_options')
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
