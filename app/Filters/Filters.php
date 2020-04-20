<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

abstract class Filters
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $builder;

    /**
     * The filters that are applied.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    public function getFilters()
    {
        return array_filter($this->request->only($this->filters));
    }
}
