<?php

namespace App\Jobs;

use App\Models\Repository;
use App\Services\PackageAuthenticationService;
use App\Services\SatisConfigService;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BuildPackages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 400;

    /**
     * Create a new job instance.
     */
    public function __construct(private ?Repository $repository = null) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate satis.json from database before building
        SatisConfigService::generateConfig();

        $processParams = [
            PHP_BINARY,
            base_path('vendor/bin/satis'),
            'build',
            base_path('satis.json'),
        ];

        // If building for a specific repository, add the repository URL filter
        if ($this->repository) {
            $processParams[] = "--repository-url={$this->repository->getFullUrl()}";
        }

        $process = new Process($processParams, base_path());
        $process->setTimeout(300);

        // Configure authentication for private repos (temporary, only for this process)
        $auth = PackageAuthenticationService::getAvailableAuthentications();
        if (! empty($auth)) {
            $process->setEnv($auth);
        }

        $process->run();

        File::put(
            storage_path('logs/satis-last-build.log'),
            $process->getOutput() . $process->getErrorOutput()
        );

        Log::channel('satis')
            ->info('Satis build process output: ', [
                'success' => $process->isSuccessful(),
                'repository_id' => $this->repository?->id,
                'output' => $process->getOutput() ?: $process->getErrorOutput(),
            ]);
    }
}
