<?php

namespace App\JsonApi\V1\Activities;

use App\Models\Activity;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\Scope;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Str;

class ActivitySchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Activity::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('type'), //TODO: add enum validation
            DateTime::make('starts')->sortable(),
            DateTime::make('ends')->sortable(),
            Str::make('from'),
            Str::make('to'),
            Str::make('activity_remark'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {

        return [
            WhereIdIn::make($this),
            //type
            //date start and end (default end is 1 week from start)
            //locations
            Where::make('from'), //give all flights that start on the given location.
            Scope::make('type'),
            Scope::make('dateFrom'),
            Scope::make('dateTo'),
            Scope::make('week'), //for the next week (current date can be set to 14 Jan 2022)

        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }

}
