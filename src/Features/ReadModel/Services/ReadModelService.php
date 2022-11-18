<?php

namespace Kellton\Tools\Features\ReadModel\Services;

use Illuminate\Support\Collection;
use Kellton\Tools\Builders\Builder;
use Kellton\Tools\Enums\OrderDirection;
use Kellton\Tools\Features\Action\Data\ActionResult;
use Kellton\Tools\Features\Action\Data\Result;
use Kellton\Tools\Features\Action\Services\ModelService;
use Kellton\Tools\Features\ReadModel\Models\ReadModel;
use Kellton\Tools\Models\Model;
use Kellton\Tools\Undefined;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class ReadModelService handles the read model logic.
 */
abstract class ReadModelService extends ModelService
{
    /**
     * @var class-string<ReadModel>
     */
    protected const READ_MODEL = null;

    /**
     * @var int chunk size for queries
     */
    protected const CHUNK_SIZE = 100;

    /**
     * Returns read models records based on filters, pagination and order.
     *
     * @param Collection|Undefined $filters
     * @param string|Undefined $orderBy
     * @param OrderDirection|Undefined $orderDirection
     * @param int|Undefined $page
     * @param int|Undefined $perPage
     *
     * @return ActionResult
     */
    public function getList(
        Collection|Undefined $filters,
        string|Undefined $orderBy,
        OrderDirection|Undefined $orderDirection,
        int|Undefined $page,
        int|Undefined $perPage,
    ): ActionResult {
        return $this->action(function () use ($filters, $orderBy, $orderDirection, $page, $perPage) {
            $objects = $this->readModelQuery()
                ->filters($filters)
                ->order($orderBy, $orderDirection)
                ->pagination($page, $perPage);

            return new Result($objects);
        });
    }

    /**
     * Create or update read model for single object.
     *
     * @param Model $object
     *
     * @return ActionResult
     */
    public function createOrUpdate(Model $object): ActionResult
    {
        return $this->action(function () use ($object) {
            $data = $this->generateData($object);

            if (!$data->has('id')) {
                throw new RuntimeException('Missing id in data');
            }

            $object = $this->readModelQuery()->updateOrCreate(
                ['id' => $data->get('id')],
                $data->toArray()
            );

            return new Result($object);
        });
    }

    /**
     * Returns the records count.
     *
     * @return ActionResult
     */
    public function count(): ActionResult
    {
        return $this->action(function () {
            return $this->query()->count();
        });
    }

    /**
     * Rebuild the read model.
     *
     * @param ProgressBar|null $progressBar
     *
     * @return ActionResult
     */
    public function rebuild(?ProgressBar $progressBar = null): ActionResult
    {
        return $this->action(function () use ($progressBar) {
            $this->query()->chunk(self::CHUNK_SIZE, function (Collection $objects) use ($progressBar) {
                foreach ($objects as $object) {
                    $this->createOrUpdate($object)->mandatory();
                    /** @noinspection DisconnectedForeachInstructionInspection */
                    $progressBar?->advance();
                }
            });

            $progressBar?->finish();
            $progressBar?->clear();

            return new Result();
        });
    }

    /**
     * Returns collection of models attributes.
     *
     * @param Model $object
     *
     * @return Collection
     */
    abstract protected function generateData(Model $object): Collection;

    /**
     * Returns query builder.
     *
     * @return Builder
     */
    protected function readModelQuery(): Builder
    {
        /** @var class-string<ReadModel> $model */
        $model = static::READ_MODEL;

        return $model::query();
    }
}
