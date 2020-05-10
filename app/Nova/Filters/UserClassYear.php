<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Nova;

class UserClassYear extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name = 'Class Year';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('class_year', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $resource = Nova::resourceForKey($request->route('resource'));

        $years = call_user_func([$resource, 'newModel'])->pluck('class_year')->toArray();

        return Arr::keysFromValues($years);
    }
}
