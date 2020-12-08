<?php

namespace App\Nova;

use App\Nova\Filters\UserClassCourse;
use App\Nova\Filters\UserClassYear;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class UserInvitation extends Resource
{
    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return __('nova-permission-tool::navigation.sidebar-label');
    }

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\UserInvitation::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'first_name', 'last_name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('First name')
                ->rules('required'),

            Text::make('Last name')
                ->rules('required'),

            Select::make('Course', 'class_course')
                ->options(Arr::keysFromValues($courses = config('council.courses')))
                ->rules('required', Rule::in($courses)),

            Text::make('Year', 'class_year')->withMeta([
                'extraAttributes' => [
                    'minlength' => 4,
                    'maxlength' => 4,
                    'pattern' => '(?:19|20|21)[0-9]{2}',
                    // Year between 1900 and 2199
                    // If this code is still up past 2199, please tell my great-great-grandchildren!
                ],
            ])->default(now()->year)
                ->rules('required', 'digits:4'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new UserClassCourse,
            new UserClassYear,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
