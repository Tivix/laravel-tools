<?php

namespace Kellton\Tools\Features\OpenApi\Services;

use Kellton\Tools\Features\Action\Data\ActionResult;
use Kellton\Tools\Features\Action\Data\Result;
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
     * @return ActionResult
     */
    public function get(): ActionResult
    {
        return $this->action(function () {
            $content = Storage::disk('local')->get(self::FILENAME);

            return new Result(Json::decode($content));
        });
    }

    /**
     * Generates the OpenAPI specification.
     *
     * @return ActionResult
     */
    public function generate(): ActionResult
    {
        return $this->action(function () {
            $openapi = (new Generator())->generate([app_path('Http')]);
            if ($openapi === null) {
                throw new RuntimeException('OpenAPI specification could not be generated.');
            }

            Storage::disk('local')->put(self::FILENAME, $openapi->toJson());

            return new Result();
        });
    }
}
