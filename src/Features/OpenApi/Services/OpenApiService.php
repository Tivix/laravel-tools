<?php

namespace Kellton\Tools\Features\OpenApi\Services;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Action\Services\ActionService;
use Kellton\Tools\Helpers\Json;
use OpenApi\Generator;
use RuntimeException;
use Storage;

/**
 * Class OpenApiService handles the OpenAPI specification.
 */
class OpenApiService extends ActionService
{
    /**
     * @var string The filename of the OpenAPI specification.
     */
    private const FILENAME = 'openapi.json';

    /**
     * Returns the OpenAPI specification.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->action(function () {
            $content = Storage::disk('local')->get(self::FILENAME);

            return Json::decode($content);
        });
    }

    /**
     * Generates the OpenAPI specification.
     *
     * @return void
     */
    public function generate(): void
    {
        $this->action(function () {
            $openapi = (new Generator())->generate([app_path('Http')]);
            if ($openapi === null) {
                throw new RuntimeException('OpenAPI specification could not be generated.');
            }

            Storage::disk('local')->put(self::FILENAME, $openapi->toJson());
        });
    }
}
