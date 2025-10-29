<?php

namespace App\Filament\Resources\Jobs\Schemas;

use Filament\Schemas\Schema;
use ValentinMorice\FilamentJsonColumn\JsonColumn;

class JobsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                JsonColumn::make('payload')
                    ->hiddenLabel()
                    ->viewerOnly()
                    ->columnSpanFull()
                    ->viewerHeight(400),
            ]);
    }
}
