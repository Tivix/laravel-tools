<?php

namespace Kellton\Tools\Features\OpenApi\Commands;

use Kellton\Tools\Commands\Command;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;
use Kellton\Tools\Features\OpenApi\Services\OpenApiService;
use RuntimeException;

/**
 * Class GenerateOpenApi handles the generation of the OpenAPI specification.
 *
 * @property-read OpenApiService $service
 */
#[Dependency(OpenApiService::class, 'service')]
class OpenApiGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open-api:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAPI specification';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->service->generate();

            $this->info('OpenAPI specification generated.');
        } catch (RuntimeException) {
            $this->error('OpenAPI specification could not be generated.');
        }
    }
}
