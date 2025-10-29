<?php

namespace App\Filament\Resources\Repositories\Tables;

use App\Enums\CodespaceProviderEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RepositoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('Url'),
                TextColumn::make('provider')
                    ->label(__('provider'))
                    ->icon(fn (CodespaceProviderEnum $state): string => match ($state) {
                        CodespaceProviderEnum::GITHUB => 'icon-github',
                        CodespaceProviderEnum::GITLAB => 'icon-gitlab',
                        CodespaceProviderEnum::BITBUCKET => 'icon-bitbucket',
                        CodespaceProviderEnum::CUSTOM => 'icon-git',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state->value))
                    ->size(TextSize::Large)
                    ->color(fn (CodespaceProviderEnum $state): string => match ($state) {
                        CodespaceProviderEnum::GITHUB => 'white',
                        CodespaceProviderEnum::GITLAB => 'warning',
                        CodespaceProviderEnum::BITBUCKET => 'info',
                        CodespaceProviderEnum::CUSTOM => 'gray',
                    })
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('added_at'))
                    ->date('d/m/Y'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::TwoExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
