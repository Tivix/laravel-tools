<?php

namespace Kellton\Tools\Features\Action\Services;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Kellton\Tools\Models\Model;
use Kellton\Tools\Models\ModelInterface;
use Kellton\Tools\Rules\Exists;

/**
 * Class ModelService handles the model.
 *
 * @property Model $object
 *
 * @method self load(ModelInterface $object)
 */
abstract class ModelService extends ActionService
{
    /**
     * @var class-string<Model>
     */
    protected const MODEL = null;

    /**
     * Returns validation for existence of id.
     *
     * @param int $id
     *
     * @return Rule
     */
    public function getValidationForIdExists(int $id): Rule
    {
        return new Exists($this->query()->whereId($id));
    }

    /**
     * Get loaded object.
     *
     * @return Model|null
     */
    protected function getObject(): Model|null
    {
        return parent::getObject();
    }

    /**
     * Returns query builder.
     *
     * @return Builder
     */
    protected function query(): Builder
    {
        /** @var class-string<Model> $model */
        $model = static::MODEL;

        return $model::query();
    }
}
