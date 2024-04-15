<?php

namespace App\JsonApi\V1\Activities;

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class ActivityCollectionQuery extends ResourceQuery
{

    /**
     * Get the validation rules that apply to the request query parameters.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fields' => [
                'nullable',
                'array',
                JsonApiRule::fieldSets(),
            ],
            'filter' => [
                'nullable',
                'array',
                JsonApiRule::filter(),
            ],
            'include' => [
                'nullable',
                'string',
                JsonApiRule::includePaths(),
            ],
            'page' => [
                'nullable',
                'array',
                JsonApiRule::page(),
            ],
            'sort' => [
                'nullable',
                'string',
                JsonApiRule::sort(),
            ],
            'withCount' => [
                'nullable',
                'string',
                JsonApiRule::countable(),
            ],

            'filter.from' => ['string', 'airport'],
            'filter.type' => ['string', 'activitytype'],
            'filter.dateFrom' => [ 'date', 'date_format:Y-m-d' ],
            'filter.dateTo' => [ 'date', 'date_format:Y-m-d' ],
            'filter.week' => ['string', 'weekscope'],

        ];
    }
}
