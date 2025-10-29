<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Schemas\UsersForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $label = 'Utilisateurs';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return UsersForm::configure(schema: $schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure(table: $table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }

    public static function getNavigationGroup(): string
    {
        return __('configuration');
    }
}
