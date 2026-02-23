<?php

declare(strict_types=1);

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;
}
