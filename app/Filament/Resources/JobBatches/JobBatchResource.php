<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobBatches;

use App\Filament\Resources\JobBatches\Pages\ListJobBatches;
use App\Filament\Resources\JobBatches\Schemas\JobBatchesForm;
use App\Filament\Resources\JobBatches\Tables\JobBatchesTable;
use App\Models\JobBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JobBatchResource extends Resource
{
    protected static ?string $model = JobBatch::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Jobs';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 91;

    public static function form(Schema $schema): Schema
    {
        return JobBatchesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobBatchesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobBatches::route('/'),
        ];
    }
}
