<?php

namespace Kellton\Tools\Features\Initializers\Services;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kellton\Tools\Features\Action\Services\ActionService;
use Kellton\Tools\Features\Initializers\Initializer;
use ReflectionClass;

/**
 * Class InitializeService handles the initialization of the data.
 */
class InitializeService extends ActionService
{
    /**
     * Returns the initializers.
     *
     * @return Collection<Initializer>
     */
    public function get(): Collection
    {
        return $this->action(function () {
            /** @var Application $app */
            $app = Container::getInstance();
            $namespace = $app->getNamespace();

            return collect(File::allFiles(app_path()))
                ->map(function ($file) use ($namespace) {
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
                        || !$reflectionClass->isSubclassOf(Initializer::class)
                    ) {
                        return null;
                    }

                    return collect([
                        'order' => $reflectionClass->getConstant('ORDER'),
                        'initializer' => $reflectionClass->newInstance(),
                    ]);
                })
                ->filter()
                ->sortBy('order')
                ->map(fn ($item) => $item->get('initializer'))
                ->values();
        });
    }
}
