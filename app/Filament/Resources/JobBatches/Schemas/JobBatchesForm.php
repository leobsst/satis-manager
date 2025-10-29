<?php

namespace App\Filament\Resources\JobBatches\Schemas;

use Filament\Schemas\Schema;
use ValentinMorice\FilamentJsonColumn\JsonColumn;

class JobBatchesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                JsonColumn::make('options')
                    ->label(trans_choice('options', 2))
                    ->hiddenLabel()
                    ->viewerOnly()
                    ->columnSpanFull()
                    ->formatStateUsing(fn (string $state) => filled($state) ? json_encode(unserialize($state), JSON_PRETTY_PRINT) : json_encode([]))
                    ->viewerHeight(400),
            ]);
    }
}
