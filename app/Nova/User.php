<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use LimeDeck\NovaCashierOverview\Subscription;
use Vyuldashev\NovaPermission\PermissionBooleanGroup;
use Vyuldashev\NovaPermission\RoleBooleanGroup;

class User extends Resource
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
    public static $model = \App\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
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

            Gravatar::make()->maxWidth(50),

            Text::make('First name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Last name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Username')
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Indicator::make('Membership', function () {
                if ($this->subscribed('default')) {
                    if ($this->subscription('default')->ended()) {
                        return 'ended';
                    }

                    if ($this->subscription('default')->onGracePeriod()) {
                        return 'on-grace-period';
                    }

                    if ($this->subscription('default')->onTrial()) {
                        return 'on-trial';
                    }

                    return 'active';
                }

                return 'inactive';
            })
                ->labels([
                    'active' => 'Active',
                    'on-trial' => 'On trial',
                    'on-grace-period' => 'On grace period',
                    'ended' => 'Ended',
                    'inactive' => 'Inactive',
                ])
                ->colors([
                    'active' => 'green',
                    'on-trial' => 'green',
                    'on-grace-period' => 'orange',
                    'ended' => 'red',
                    'inactive' => 'grey',
                ])
                ->onlyOnIndex(),

            Subscription::make(),

            new Panel('Roles & Permissions', [
                RoleBooleanGroup::make('Roles')->hideFromIndex(),
                PermissionBooleanGroup::make('Permissions')->hideFromIndex(),
            ]),
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
