<?php

declare(strict_types=1);

namespace App\Filament\Resources\RepositoryCredentials;

use App\Filament\Resources\RepositoryCredentials\Pages\ListRepositoryCredentials;
use App\Filament\Resources\RepositoryCredentials\Schemas\RepositoryCredentialForm;
use App\Filament\Resources\RepositoryCredentials\Tables\RepositoryCredentialsTable;
use App\Models\RepositoryCredential;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RepositoryCredentialResource extends Resource
{
    protected static ?string $model = RepositoryCredential::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return RepositoryCredentialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepositoryCredentialsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositoryCredentials::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans_choice('credentials.title', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('credentials.title', 2);
    }

    public static function getNavigationGroup(): string
    {
        return __('configuration');
    }
}
