<?php

namespace App\Console\Commands\Packages;

use App\Enums\CodespaceProviderEnum;
use App\Jobs\BuildPackages;
use App\Models\Repository;
use Illuminate\Console\Command;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:build
                            {repository? : The repository name to build e.g. "vendor/package"}
                            {--provider=github : The repository provider (github, gitlab, bitbucket)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the package repository using Satis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $repository = null;
        $this->option('provider');

        $this->checkIfProviderSupported();

        if (filled($this->argument('repository'))) {
            $repository = $this->checkIfRepositoryExists($this->argument('repository'));
        } else {
            $repository = null;
        }

        BuildPackages::dispatch($repository)->withoutDelay();

        $this->info('Package build job dispatched successfully.');
    }

    private function checkIfProviderSupported(): void
    {
        $provider = $this->option('provider');
        $supportedProviders = array_map(fn ($case) => $case->value, CodespaceProviderEnum::cases());

        if (! in_array($provider, $supportedProviders, true)) {
            $this->error("Provider '{$provider}' is not supported. Supported providers are: " . implode(', ', $supportedProviders) . '.');
            exit(1);
        }
    }

    private function checkIfRepositoryExists(string $repository): ?Repository
    {
        $record = Repository::where('url', $repository)
            ->where('provider', $this->option('provider'));

        if (! $record->exists()) {
            $this->error("Repository '{$repository}' with provider '{$this->option('provider')}' does not exist in the database.");
            exit(1);
        }

        return $record->first();
    }
}
