<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'deploy')]
class DeployCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache framework bootstrap, configuration, and metadata to increase performance';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->components->info('Clearing cached bootstrap files.');

        collect([
            'cache' => fn () => $this->callSilent('cache:clear') == 0,
            'compiled' => fn () => $this->callSilent('clear-compiled') == 0,
            'config' => fn () => $this->callSilent('config:clear') == 0,
            'events' => fn () => $this->callSilent('event:clear') == 0,
            'routes' => fn () => $this->callSilent('route:clear') == 0,
            'views' => fn () => $this->callSilent('view:clear') == 0,
            'blade-icons' => fn () => $this->callSilent('icons:clear') == 0,
            'filament' => fn () => $this->callSilent('filament:optimize-clear') == 0,
        ])->each(fn ($task, $description) => $this->components->task($description, $task));
        $this->newLine();

        $this->components->info('Building assets.');
        $vite = popen('npm run build', 'r');
        while ($line = fgets($vite)) {
            $this->output->write('  ' . $line);
        }
        pclose($vite);
        $this->newLine(2);

        $this->components->info('Caching framework bootstrap, configuration, and metadata.');

        collect([
            'config' => fn () => $this->callSilent('config:cache') == 0,
            'events' => fn () => $this->callSilent('event:cache') == 0,
            'routes' => fn () => $this->callSilent('route:cache') == 0,
            'blade-icons' => fn () => $this->callSilent('icons:cache') == 0,
            'filament' => fn () => $this->callSilent('filament:optimize') == 0,
        ])->each(fn ($task, $description) => $this->components->task($description, $task));
        $this->newLine();
    }
}
