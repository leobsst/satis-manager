<?php

namespace App\Filament\Resources\Repositories\Tables;

use App\Models\Repository;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use App\Jobs\BuildPackages;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\DeleteAction;
use App\Enums\CodespaceProviderEnum;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Laravel\Passport\ClientRepository;

class RepositoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('Url')
                    ->copyable()
                    ->searchable(),
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
                    ->url(fn (Repository $record): string => $record->getFullUrl(true), true)
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('added_at'))
                    ->date('d/m/Y'),
            ])
            ->headerActions([
                Action::make('refresh_repositories')
                    ->label(__('repositories.refresh.title'))
                    ->action(
                        fn () => BuildPackages::dispatchIf(! empty($table->getRecords()->isNotEmpty()))
                    )
                    ->successNotificationTitle(__('repositories.refresh.success_notification'))
                    ->icon('icon-package')
                    ->color('gray')
                    ->disabled(fn () => $table->getRecords()->isEmpty()),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::TwoExtraLarge),
                Action::make('rebuild_repository')
                    ->label(__('repositories.rebuild.action'))
                    ->action(fn (Repository $record) => BuildPackages::dispatch(repository: $record))
                    ->successNotificationTitle(__('repositories.rebuild.success_notification'))
                    ->icon('icon-package')
                    ->color('gray'),
                Action::make('generate_client_credentials')
                    ->label('Generate Client Credentials')
                    ->modalHeading(__('authentication'))
                    ->mountUsing(callback: function (Schema $schema, Repository $record) {
                        $client = app(ClientRepository::class)
                            ->createClientCredentialsGrantClient($record->url);

                        $schema->fill(state: [
                            'secret' => $client->plainSecret,
                        ]);
                    })
                    ->schema([
                        TextInput::make('secret')
                            ->label(__('repositories.api_credentials.secret'))
                            ->password()
                            ->revealable()
                            ->copyable()
                            ->disabled(),
                    ])
                    ->successNotificationTitle(__('repositories.api_credentials.success_notification'))
                    ->icon(Heroicon::Key)
                    ->color('primary')
                    ->modalSubmitActionLabel(null)
                    ->modalWidth(Width::Large)
                    ->modalCancelActionLabel(__('close')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
