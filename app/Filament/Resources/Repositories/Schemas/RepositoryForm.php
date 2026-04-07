<?php

declare(strict_types=1);

namespace App\Filament\Resources\Repositories\Schemas;

use App\Enums\CodespaceProviderEnum;
use App\Filament\Resources\RepositoryCredentials\Schemas\RepositoryCredentialForm;
use App\Models\RepositoryCredential;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RepositoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider')
                    ->label(__('provider'))
                    ->required()
                    ->selectablePlaceholder(false)
                    ->default(CodespaceProviderEnum::GITHUB->value)
                    ->prefixIcon(fn ($state) => CodespaceProviderEnum::tryFrom($state)->icon())
                    ->reactive()
                    ->partiallyRenderComponentsAfterStateUpdated([
                        'provider',
                        'vendor',
                        'repository_credential_id',
                    ])
                    ->afterStateUpdated(fn (callable $set) => $set('repository_credential_id', null))
                    ->options(CodespaceProviderEnum::asSelectableArray()),

                FusedGroup::make([
                    TextInput::make('vendor')
                        ->hiddenLabel()
                        ->required()
                        ->regex('/^[a-zA-Z0-9\-]+$/')
                        ->prefix(fn (Get $get): string => CodespaceProviderEnum::tryFrom($get('provider'))->prefix() ?? ':')
                        ->validationAttribute(__('user'))
                        ->extraInputAttributes(['class' => 'py-2.5']),
                    TextInput::make('repository_name')
                        ->hiddenLabel()
                        ->validationAttribute(__('name'))
                        ->regex('/^[a-zA-Z0-9\-]+$/')
                        ->prefix('/')
                        ->suffix('.git')
                        ->required()
                        ->extraInputAttributes(['class' => 'py-2.5']),
                ])
                    ->columns(2)
                    ->label(__('user') . ' / ' . __('name'))
                    ->columnSpanFull(),

                Select::make('repository_credential_id')
                    ->label(__('credentials.title_singular'))
                    ->relationship('credential', 'name')
                    ->nullable()
                    ->placeholder(__('credentials.none'))
                    ->columnSpanFull()
                    ->options(function (Get $get): array {
                        $provider = $get('provider');
                        if (! $provider) {
                            return [];
                        }

                        return RepositoryCredential::where('provider', $provider)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->createOptionForm(RepositoryCredentialForm::getComponents())
                    ->createOptionUsing(function (array $data): int {
                        return RepositoryCredential::create($data)->id;
                    })
                    ->createOptionModalHeading(__('credentials.create.title'))
                    ->editOptionForm(RepositoryCredentialForm::getComponents())
                    ->editOptionModalHeading(__('credentials.edit.title')),

                TagsInput::make('excluded_branches')
                    ->label(__('repositories.excluded_branches.label'))
                    ->placeholder(__('repositories.excluded_branches.placeholder'))
                    ->helperText(__('repositories.excluded_branches.helper'))
                    ->columnSpanFull()
                    ->suggestions([
                        'dependabot/*',
                        'renovate/*',
                        'renovate/**',
                        'feature/*',
                        'release/*',
                    ]),
            ])
            ->columns(3);
    }
}
