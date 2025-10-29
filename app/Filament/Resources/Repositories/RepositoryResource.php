<?php

namespace App\Filament\Resources\Repositories;

use App\Filament\Resources\Repositories\Pages\ListRepositories;
use App\Filament\Resources\Repositories\Schemas\RepositoryForm;
use App\Filament\Resources\Repositories\Tables\RepositoriesTable;
use App\Models\Repository;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static string | BackedEnum | null $navigationIcon = 'icon-git';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'url';

    public static function form(Schema $schema): Schema
    {
        return RepositoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepositoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositories::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans_choice('repositories.title', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('repositories.title', 2);
    }

    public static function getNavigationGroup(): string
    {
        return __('configuration');
    }
}
