<?php

declare(strict_types=1);

namespace App\Filament\Resources\RepositoryCredentials\Schemas;

use App\Enums\CodespaceProviderEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RepositoryCredentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            TextInput::make('name')
                ->label(__('name'))
                ->required()
                ->maxLength(255),

            Select::make('provider')
                ->label(__('provider'))
                ->required()
                ->selectablePlaceholder(false)
                ->default(CodespaceProviderEnum::GITHUB->value)
                ->prefixIcon(fn ($state) => CodespaceProviderEnum::tryFrom((string) $state)?->icon() ?? 'icon-git')
                ->options(CodespaceProviderEnum::asSelectableArray())
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('username', null)),

            TextInput::make('domain')
                ->label(__('credentials.domain'))
                ->required(fn (Get $get): bool => $get('provider') === CodespaceProviderEnum::CUSTOM->value)
                ->visible(fn (Get $get): bool => $get('provider') === CodespaceProviderEnum::CUSTOM->value)
                ->placeholder('git.example.com')
                ->maxLength(255),

            TextInput::make('username')
                ->label(fn (Get $get): string => $get('provider') === CodespaceProviderEnum::BITBUCKET->value
                    ? __('credentials.consumer_key')
                    : __('credentials.username'))
                ->visible(fn (Get $get): bool => in_array(
                    $get('provider'),
                    [CodespaceProviderEnum::BITBUCKET->value, CodespaceProviderEnum::CUSTOM->value],
                    true
                ))
                ->required(fn (Get $get): bool => $get('provider') === CodespaceProviderEnum::BITBUCKET->value)
                ->password()
                ->revealable()
                ->maxLength(255),

            TextInput::make('token')
                ->label(fn (Get $get): string => $get('provider') === CodespaceProviderEnum::BITBUCKET->value
                    ? __('credentials.consumer_secret')
                    : __('credentials.token'))
                ->required()
                ->password()
                ->revealable()
                ->maxLength(1000),
        ];
    }
}
