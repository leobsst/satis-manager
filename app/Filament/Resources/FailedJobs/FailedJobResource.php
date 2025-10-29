<?php

namespace App\Filament\Resources\FailedJobs;

use App\Filament\Resources\FailedJobs\Pages\ListFailedJobs;
use App\Filament\Resources\FailedJobs\Schemas\FailedJobsForm;
use App\Filament\Resources\FailedJobs\Tables\FailedJobsTable;
use App\Models\FailedJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Jobs';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?int $navigationSort = 92;

    public static function form(Schema $schema): Schema
    {
        return FailedJobsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FailedJobsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
        ];
    }
}
