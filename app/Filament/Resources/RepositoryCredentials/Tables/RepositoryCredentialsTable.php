<?php

declare(strict_types=1);

namespace App\Filament\Resources\RepositoryCredentials\Tables;

use App\Enums\CodespaceProviderEnum;
use App\Models\RepositoryCredential;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RepositoryCredentialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('domain')
                    ->label(__('credentials.domain'))
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('repositories_count')
                    ->label(__('credentials.repositories_count'))
                    ->counts('repositories')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label(__('updated_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->toggleable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth(Width::ExtraLarge),
                    DeleteAction::make()
                        ->before(function (RepositoryCredential $record, DeleteAction $action): void {
                            if ($record->repositories()->exists()) {
                                $action->failureNotificationTitle(__('credentials.delete_blocked'));
                                $action->cancel();
                            }
                        }),
                ])->button()->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
