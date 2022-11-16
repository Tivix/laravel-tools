<?php

namespace Kellton\Tools\Features\Initializers\Commands;

use DB;
use Illuminate\Support\Collection;
use Kellton\Tools\Commands\Command;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;
use Kellton\Tools\Features\Initializers\Initializer;
use Kellton\Tools\Features\Initializers\Services\InitializeService;
use Throwable;

/**
 * Class MigrateInitialize handles the initialization of data.
 *
 * @property-read InitializeService $service
 */
#[Dependency(InitializeService::class, 'service')]
class MigrateInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize data.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Initialization started.');

        try {
            /** @var Collection<Initializer> $initializers */
            $initializers = $this->service->get()->getResult();
            if ($initializers->isEmpty()) {
                $this->warn('No initializers found.');

                return 0;
            }
            $initializersCount = $initializers->count();

            $globalProgressBar = $this->createProgressBar($initializersCount, 'Running initializers:');

            DB::transaction(function () use ($initializers, $globalProgressBar) {
                $initializers->map(function (Initializer $initializer) use ($globalProgressBar) {
                    $actions = $initializer->getActions();
                    $actionsCount = $actions->count();

                    $actionProgressBar = $this->createProgressBar($actionsCount, 'Running actions:');

                    $actions->map(function (callable $action) use ($actionProgressBar) {
                        $action();
                        $actionProgressBar->advance();
                    });

                    $actionProgressBar?->setMessage('Finished executing initializers:');
                    $actionProgressBar?->finish();
                    $actionProgressBar?->clear();
                    $globalProgressBar->advance();
                });
            });

            $globalProgressBar?->setMessage('Finished executing initializers:');
            $globalProgressBar?->finish();

            $this->newLine();
            $this->info('Initialization finished.');

            return 0;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }
    }
}
