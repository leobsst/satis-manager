<?php

namespace App\Filament\Resources\Repositories\Tables;

use App\Enums\CodespaceProviderEnum;
use App\Filament\Resources\OauthClients\Schemas\OauthClientForm;
use App\Jobs\BuildPackages;
use App\Models\Repository;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Laravel\Passport\Client;
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
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth(Width::TwoExtraLarge),
                    Action::make('rebuild_repository')
                        ->label(__('repositories.rebuild.action'))
                        ->action(fn (Repository $record) => BuildPackages::dispatch(repository: $record))
                        ->successNotificationTitle(__('repositories.rebuild.success_notification'))
                        ->icon('icon-package'),
                    Action::make('generate_client_credentials')
                        ->label(__('repositories.api_credentials.title'))
                        ->modalHeading(fn (Repository $record) => __('authentication') . ' - ' . $record->url)
                        ->mountUsing(callback: function (Schema $schema, Repository $record) {
                            $client = app(ClientRepository::class)
                                ->createClientCredentialsGrantClient(sprintf(
                                    '%s #%d',
                                    $record->url,
                                    Client::query()
                                        ->whereLike('name', $record->url . ' #%')
                                        ->count() + 1,
                                ));

                            $schema->fill(state: [
                                'id' => $client->id,
                                'secret' => $client->plainSecret,
                                'grant_types' => $client->grant_types, /** @phpstan-ignore-line */
                            ]);
                        })
                        ->schema(OauthClientForm::getComponents())
                        ->icon(Heroicon::Key)
                        ->modalSubmitAction(false)
                        ->modalWidth(Width::Large)
                        ->modalCancelActionLabel(__('close')),
                    DeleteAction::make(),
                ])->button()->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
