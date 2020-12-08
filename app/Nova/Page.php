<?php

namespace App\Nova;

use Davidpiesse\NovaToggle\Toggle;
use GeneaLabs\NovaGutenberg\Gutenberg;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;

class Page extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content Management';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Page::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
        'path',
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

            Stack::make('Page', [
                Line::make('Title')->asHeading(),
                Line::make('Path', function () {
                    return route('pages.show', $this);
                })->asSmall(),
            ]),

            Text::make('Title')
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make('Path')
                ->rules('required', 'max:255', 'regex:/^[a-z0-9]+([a-z0-9-\/][a-z0-9]+)*$/')
                ->creationRules('unique:pages,path')
                ->updateRules('unique:pages,path,{{resourceId}}')
                ->onlyOnForms(),

            Toggle::make('Restricted to users', 'restricted')
                ->falseColor('#E53E3E')
                ->trueLabel('ON')
                ->falseLabel('OFF')
                ->showLabels()
                ->help('If off, then the page will be viewable by anyone (used for pages like Privacy Policy).')
                ->default(true)
                ->rules('boolean')
                ->hideFromIndex(),

            DateTime::make('Published at')
                ->help('The date and time at which this page will become widely viewable.')
                ->default(function () {
                    return now()->toDateTimeString();
                })
                ->rules('nullable', 'date_format:Y-m-d H:i:s')
                ->hideFromIndex(),

            Gutenberg::make('Body')
                ->rules('required', 'max:65535')
                ->hideFromIndex(),
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
        return [];
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
