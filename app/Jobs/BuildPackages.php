<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Repository;
use App\Services\PackageAuthenticationService;
use App\Services\SatisConfigService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BuildPackages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 400;

    public function __construct(private ?Repository $repository = null) {}

    public function handle(): void
    {
        SatisConfigService::generateConfig();

        $processParams = [
            PHP_BINARY,
            base_path('vendor/bin/satis'),
            'build',
            base_path('satis.json'),
        ];

        if ($this->repository) {
            $processParams[] = "--repository-url={$this->repository->getFullUrl()}";
        }

        $process = new Process($processParams, base_path());
        $process->setTimeout(300);

        $auth = $this->repository !== null
            ? PackageAuthenticationService::getAuthForRepository($this->repository)
            : PackageAuthenticationService::getAllAuthentications();

        if (! empty($auth)) {
            $process->setEnv($auth);
        }

        $process->run();

        File::put(
            storage_path('logs/satis-last-build.log'),
            $process->getOutput() . $process->getErrorOutput()
        );

        Log::channel('satis')->info('Satis build process output: ', [
            'success' => $process->isSuccessful(),
            'repository_id' => $this->repository?->id,
            'output' => $process->getOutput() ?: $process->getErrorOutput(),
        ]);

        if ($process->isSuccessful()) {
            SatisConfigService::postProcessPackages();
            SatisConfigService::postProcessExcludedBranches();
        }
    }
}
