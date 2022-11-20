<?php

namespace Kellton\Tools\Features\ReadModel\Services;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Kellton\Tools\Features\Action\Data\ActionResult;
use Kellton\Tools\Features\Action\Data\Result;
use Kellton\Tools\Features\Action\Services\ActionService;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ReadModelsService handles the read models logic.
 */
final class ReadModelsService extends ActionService
{
    /**
     * Get the cached model services for all defined cached models inside the given path.
     *
     * @return ActionResult
     */
    public function getReadModels(): ActionResult
    {
        return $this->action(function () {
            /** @var Application $app */
            $app = Container::getInstance();
            $namespace = $app->getNamespace();

            $services = collect(File::allFiles(app_path()))
                ->map(function (SplFileInfo $file) use ($namespace) {
                    $path = $file->getRelativePathName();

                    $class = sprintf(
                        '\%s%s',
                        $namespace,
                        str_replace('/', '\\', substr($path, 0, strrpos($path, '.')))
                    );

                    if (!class_exists($class)) {
                        return null;
                    }

                    $reflectionClass = new ReflectionClass($class);
                    if ($reflectionClass->isAbstract()
                        || !$reflectionClass->isSubclassOf(ReadModelService::class)
                    ) {
                        return null;
                    }

                    return $reflectionClass->newInstance();
                })
                ->filter()
                ->values();

            return new Result($services);
        });
    }

}
