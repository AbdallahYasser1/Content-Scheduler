<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filters
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Registered requested filters to operate upon.
     *
     * @var array
     */
    protected $requestedFilters = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     *
     * @param Builder $builder
     * @param array $params
     * @return Builder
     */
    public function apply(Builder $builder, array $params = []): Builder
    {
        $this->builder = $builder;

        $this->requestedFilters = $params;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $method = Str::camel($filter))) {
                $this->$method($value);
            }
        }
        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return array
     */
    public function getFilters(): array
    {
        if (empty($this->requestedFilters)) {
            $this->requestedFilters = $this->request->only($this->filters);
        }
        return $this->requestedFilters;
    }
}
