<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('md')
                ->using(fn (array $data): Model => self::getModel()::create(array_merge($data, ['password' => bcrypt(Str::random(16))])))
                ->after(fn ($record) => $record->sendFinalizationEmail()),
        ];
    }
}
