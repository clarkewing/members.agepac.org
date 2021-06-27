<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Filters\Filter;

class UserMembershipState extends Filter
{
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
        if ($value === 'pending-approval') {
            return $query->whereNull('approved_at');
        }

        if ($value === 'inactive') {
            return $query->whereDoesntHave('subscriptions', function ($innerQuery) {
                $innerQuery->whereName('default');
            });
        }

        return $query->whereHas('subscriptions', function ($innerQuery) use ($value) {
            $innerQuery->whereName('default')->{Str::camel($value)}();
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Active' => 'active',
            'On trial' => 'on-trial',
            'On grace period' => 'on-grace-period',
            'Ended' => 'ended',
            'Inactive' => 'inactive',
            'Pending approval' => 'pending-approval',
        ];
    }
}
