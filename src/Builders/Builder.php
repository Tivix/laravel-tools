<?php

namespace Kellton\Tools\Builders;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Kellton\Tools\Data\FilterData;
use Kellton\Tools\Data\QueryData;
use Kellton\Tools\Enums\FilterOperation;
use Kellton\Tools\Enums\SortDirection;
use Kellton\Tools\Feature\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Feature\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Undefined;
use ReflectionException;

/**
 * Class Builder handles base class for eloquent builders.
 *
 * @method self whereId(int $value)
 */
abstract class Builder extends EloquentBuilder
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
     * @param QueryData|Undefined $data
     *
     * @return $this
     */
    public function filter(QueryData|Undefined $data): static
    {
        if ($data instanceof Undefined) {
            return $this;
        }

        $data->filters->each(function (FilterData $value) {
            $operation = match ($value->operation) {
                FilterOperation::EQUAL => '=',
            };

            $this->where($value->name, $operation, $value->value);
        });

        if ($data->sortBy) {
            $direction = $data->sortDirection instanceof Undefined ? SortDirection::ASC : $data->sortDirection;

            $this->orderBy($data->sortBy, $direction->value);
        }

        return $this;
    }

    /**
     * Paginate the given query.
     *
     * @param QueryData|Undefined $data
     *
     * @return LengthAwarePaginator
     */
    public function paginateByData(QueryData|Undefined $data): LengthAwarePaginator
    {
        $defaultPerPage = config('tools.pagination.per_page');

        if ($data instanceof Undefined) {
            $page = 1;
            $perPage = $defaultPerPage;
        } else {
            $perPage = !($data->perPage instanceof Undefined) ? $data->perPage : $defaultPerPage;
            $page = !($data->page instanceof Undefined) ? $data->page : 1;
        }

        return $this->paginate(perPage: $perPage, page: $page);
    }
}
