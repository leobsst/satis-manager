<?php

declare(strict_types=1);

namespace App\Filament\Resources\RepositoryCredentials\Pages;

use App\Filament\Resources\RepositoryCredentials\RepositoryCredentialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListRepositoryCredentials extends ListRecords
{
    protected static string $resource = RepositoryCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::ExtraLarge)
                ->modalHeading(__('credentials.create.title')),
        ];
    }
}
