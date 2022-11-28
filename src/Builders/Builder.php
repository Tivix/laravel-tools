<?php

namespace Kellton\Tools\Builders;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Kellton\Tools\Data\FilterData;
use Kellton\Tools\Enums\FilterOperator;
use Kellton\Tools\Enums\OrderDirection;
use Kellton\Tools\Models\Model;
use Kellton\Tools\Undefined;

/**
 * Class Builder handles base class for eloquent builders.
 *
 * @method self whereId(int $value)
 * @method Model|EloquentCollection |array|static|null find($id, $columns = ['*'])
 * @method Model|object|static|null first($columns = ['*'])
 * @method Model|self create(array $attributes = [])
 */
class Builder extends EloquentBuilder
{
    /**
     * Scope a query with whereIn by a primary key.
     *
     * @param Collection $values
     *
     * @return static
     */
    public function whereKeyIn(Collection $values): static
    {
        if ($values->isEmpty()) {
            return $this->whereRaw('0=1');
        }

        return $this->whereIn($this->model->getQualifiedKeyName(), $values);
    }

    /**
     * Scope a query to only include models in the given values for column.
     *
     * @param string $columnName
     * @param Collection $values
     *
     * @return static
     */
    public function whereInColumn(string $columnName, Collection $values): static
    {
        if ($values->isEmpty()) {
            return $this->whereRaw('0=1');
        }

        return $this->whereIn($this->qualifyColumn($columnName), $values);
    }

    /**
     * Scope a query to only include models not in the given values for column.
     *
     * @param string $columnName
     * @param Collection $values
     *
     * @return static
     */
    public function whereNotInColumn(string $columnName, Collection $values): static
    {
        if ($values->isEmpty()) {
            return $this->whereRaw('1=1');
        }

        return $this->whereNotIn($this->qualifyColumn($columnName), $values);
    }

    /**
     * Scope a query to check if a date is in range of 2 date columns.
     *
     * @param string $startAtColumnName
     * @param string $endAtColumnName
     * @param Carbon|null $date
     *
     * @return static
     */
    public function whereDateInRange(string $startAtColumnName, string $endAtColumnName, ?Carbon $date = null): static
    {
        $self = $this;

        if (!$date) {
            $date = now();
        }

        return $this
            ->where(static function (self $query) use ($self, $date, $startAtColumnName) {
                $query
                    ->whereNull($self->qualifyColumn($startAtColumnName))
                    ->orWhere(
                        $self->qualifyColumn($startAtColumnName),
                        '<=',
                        $date->format(config('tools.date.datetime_format'))
                    );
            })
            ->where(static function (self $query) use ($self, $date, $endAtColumnName) {
                $query
                    ->whereNull($self->qualifyColumn($endAtColumnName))
                    ->orWhere(
                        $self->qualifyColumn($endAtColumnName),
                        '>=',
                        $date->format(config('tools.date.datetime_format'))
                    );
            });
    }

    /**
     * Scope a query to add filters based on the given query data.
     *
     * @param Collection|Undefined $filters
     *
     * @return $this
     */
    public function filters(Collection|Undefined $filters): static
    {
        if ($filters instanceof Undefined) {
            return $this;
        }

        $filters->each(function (FilterData $filter) {
            $operation = match ($filter->operation) {
                FilterOperator::EQUAL => '=',
                FilterOperator::LIKE => 'LIKE',
            };

            $value = $filter->value;
            if ($filter->operation === FilterOperator::LIKE) {
                $value = "%{$value}%";
            }

            $this->where($filter->name, $operation, $value);
        });

        return $this;
    }

    /**
     * Scope a query to add sorting based on the given query data.
     *
     * @param string|Undefined $orderBy
     * @param OrderDirection|Undefined $orderDirection
     *
     * @return $this
     */
    public function order(string|Undefined $orderBy, OrderDirection|Undefined $orderDirection): static
    {
        if (!($orderBy instanceof Undefined)) {
            $direction = $orderDirection instanceof Undefined ? OrderDirection::ASC : $orderDirection;

            $this->orderBy($orderBy, $direction->value);
        }

        return $this;
    }

    /**
     * Paginate the given query.
     *
     * @param int|Undefined $page
     * @param int|Undefined $perPage
     *
     * @return LengthAwarePaginator
     */
    public function pagination(int|Undefined $page, int|Undefined $perPage): LengthAwarePaginator
    {
        if ($page instanceof Undefined) {
            $page = 1;
        }

        if ($perPage instanceof Undefined) {
            $perPage = (int) config('tools.pagination.per_page');
        }

        return $this->paginate(perPage: $perPage, page: $page);
    }
}
