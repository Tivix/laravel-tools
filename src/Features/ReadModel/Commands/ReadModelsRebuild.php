<?php

namespace Kellton\Tools\Features\ReadModel\Commands;

use Exception;
use Kellton\Tools\Commands\Command;
use Kellton\Tools\Features\Action\Exceptions\MandatoryResult;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;
use Kellton\Tools\Features\ReadModel\Services\ReadModelService;
use Kellton\Tools\Features\ReadModel\Services\ReadModelsService;

/**
 * Class ReadModelsRebuild handles the rebuilding of the read models.
 *
 * @property-read ReadModelsService $service
 */
#[Dependency(ReadModelsService::class, 'service')]
class ReadModelsRebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read-models:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command rebuild all read models';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Read model rebuild started.');
        $this->newLine();

        try {
            $services = $this->service->getReadModels()->getResult();
            $progressBar = $this->output->createProgressBar($services->count());

            $services->each(function (ReadModelService $service) use ($progressBar) {
                $this->rebuildByService($service);

                $progressBar->advance();
            });

            $progressBar->finish();
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->newLine();
        $this->info('Read model rebuild finished.');
    }

    /**
     * Rebuild the read model by the service.
     *
     * @param ReadModelService $readModelService
     *
     * @throws MandatoryResult
     */
    protected function rebuildByService(ReadModelService $readModelService): void
    {
        $this->info($readModelService::class);
        $this->newLine();

        $progressBar = $this->createProgressBar($readModelService->count()->getResult());
        $readModelService->rebuild($progressBar)->mandatory();
    }
}
