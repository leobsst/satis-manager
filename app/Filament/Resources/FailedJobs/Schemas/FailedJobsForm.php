<?php

declare(strict_types=1);

namespace App\Filament\Resources\FailedJobs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use ValentinMorice\FilamentJsonColumn\JsonColumn;

class FailedJobsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                JsonColumn::make('payload')
                    ->label(__('jobs.payload'))
                    ->hiddenLabel()
                    ->viewerOnly()
                    ->columnSpanFull()
                    ->viewerHeight(400),
                Textarea::make('exception')
                    ->label(__('exception'))
                    ->disabled()
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }
}
