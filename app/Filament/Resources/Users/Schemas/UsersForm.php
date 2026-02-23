<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(components: [
                TextInput::make(name: 'name')
                    ->label(label: 'Nom')
                    ->required(),
                TextInput::make(name: 'email')
                    ->label(label: 'Adresse e-mail')
                    ->required(),
                Select::make(name: 'roles')
                    ->label(label: 'Rôles')
                    ->default(state: 'user')
                    ->required()
                    ->multiple()
                    ->placeholder(placeholder: 'Sélectionnez un rôle')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->preload(),
            ])->columns(columns: 1);
    }
}
