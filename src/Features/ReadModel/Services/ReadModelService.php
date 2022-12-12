<?php

namespace Kellton\Tools\Features\ReadModel\Services;

use Illuminate\Support\Collection;
use Kellton\Tools\Builders\Builder;
use Kellton\Tools\Enums\OrderDirection;
use Kellton\Tools\Features\Action\Data\ActionResult;
use Kellton\Tools\Features\Action\Data\Result;
use Kellton\Tools\Features\Action\Services\ModelService;
use Kellton\Tools\Features\ReadModel\Models\ReadModel;
use Kellton\Tools\Models\ModelInterface;
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
     * Returns single read model record based on given id.
     *
     * @param int $id
     *
     * @return Result
     */
    public function getOne(int $id): Result
    {
        return $this->action(function () use ($id) {
            $object = $this->readModelQuery()->find($id);

            if ($object) {
                $this->load($object);
            }

            return new Result($object);
        });
    }

    /**
     * Create or update read model for single object.
     *
     * @param ModelInterface $object
     *
     * @return ActionResult
     */
    public function createOrUpdate(ModelInterface $object): ActionResult
    {
        return $this->action(function () use ($object) {
            $data = $this->generateData($object);

            if (!$data->has('id')) {
                throw new RuntimeException('Missing id in data');
            }

            /** @var ModelInterface $object */
            $object = $this->readModelQuery()->updateOrCreate(
                ['id' => $data->get('id')],
                $data->toArray()
            );

            $this->load($object);

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
            $count = $this->query()->count();

            return new Result($count);
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
     * Delete the read model.
     *
     * @return Result
     */
    public function delete(): ActionResult
    {
        return $this->actionOnObject(
            action: function () {
                $this->object->delete();

                return new Result();
            },
        );
    }

    /**
     * Returns collection of models attributes.
     *
     * @param ModelInterface $object
     *
     * @return Collection
     */
    abstract protected function generateData(ModelInterface $object): Collection;

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
