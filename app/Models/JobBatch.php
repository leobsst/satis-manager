<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JobBatch extends Model
{
    use HasUuids;

    protected $casts = [
        'failed_job_ids' => 'array',
    ];
}
