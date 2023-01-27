<?php

namespace Kellton\Tools\Features\ReadModel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kellton\Tools\Builders\Builder;
use Kellton\Tools\Enums\OrderDirection;
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
     * @return LengthAwarePaginator
     */
    public function getList(
        Collection|Undefined $filters,
        string|Undefined $orderBy,
        OrderDirection|Undefined $orderDirection,
        int|Undefined $page,
        int|Undefined $perPage,
    ): LengthAwarePaginator {
        return $this->action(function () use ($filters, $orderBy, $orderDirection, $page, $perPage) {
            return $this->readModelQuery()
                ->filters($filters)
                ->order($orderBy, $orderDirection)
                ->pagination($page, $perPage);
        });
    }

    /**
     * Returns single read model record based on given id.
     *
     * @param int $id
     *
     * @return ReadModel
     */
    public function getOne(int $id): ReadModel
    {
        return $this->action(function () use ($id) {
            $object = $this->readModelQuery()->find($id);

            if ($object) {
                $this->load($object);
            }

            return $object;
        });
    }

    /**
     * Create or update read model for single object.
     *
     * @param ModelInterface $object
     *
     * @return ReadModel
     */
    public function createOrUpdate(ModelInterface $object): ReadModel
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

            return $object;
        });
    }

    /**
     * Returns the records count.
     *
     * @return int
     */
    public function count(): int
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
     * @return void
     */
    public function rebuild(?ProgressBar $progressBar = null): void
    {
        $this->action(function () use ($progressBar) {
            $this->query()->chunk(self::CHUNK_SIZE, function (Collection $objects) use ($progressBar) {
                foreach ($objects as $object) {
                    $this->createOrUpdate($object);
                    /** @noinspection DisconnectedForeachInstructionInspection */
                    $progressBar?->advance();
                }
            });

            $progressBar?->finish();
            $progressBar?->clear();
        });
    }

    /**
     * Delete the read model.
     *
     * @return ReadModel
     */
    public function delete(): ReadModel
    {
        return $this->actionOnObject(function () {
            $this->object->delete();

            return $this->object;
        });
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
