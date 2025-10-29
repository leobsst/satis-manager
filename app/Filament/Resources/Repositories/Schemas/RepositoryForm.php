<?php

namespace App\Filament\Resources\Repositories\Schemas;

use App\Enums\CodespaceProviderEnum;
use Filament\Forms\Components\Select;
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
                    ])
                    ->options(CodespaceProviderEnum::asSelectableArray()),
                FusedGroup::make([
                    TextInput::make('vendor')
                        ->label(__('vendor'))
                        ->required()
                        ->prefix(fn (Get $get): ?string => CodespaceProviderEnum::tryFrom($get('provider'))->prefix())
                        ->extraInputAttributes(['class' => 'py-2.5']),
                    TextInput::make('repository_name')
                        ->label(__('repository_name'))
                        ->prefix('/')
                        ->suffix('.git')
                        ->required()
                        ->extraInputAttributes(['class' => 'py-2.5']),
                ])
                    ->columns(2)
                    ->label(__('user') . ' / ' . __('name'))
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
