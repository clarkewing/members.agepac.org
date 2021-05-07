<?php

namespace App\Nova;

use App\Nova\Filters\UserClassCourse;
use App\Nova\Filters\UserClassYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inspheric\Fields\Indicator;
use KABBOUCHI\NovaImpersonate\Impersonate;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
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
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "$this->class_course $this->class_year";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'first_name', 'last_name', 'email',
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
            $this->avatarField()
                ->maxWidth(150),

            Stack::make('Name', [
                Line::make('Name')->asHeading(),
                Line::make('Class')->asSmall(),
            ])->onlyOnIndex(),

            Text::make('First name')
                ->sortable()
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            Text::make('Last name')
                ->sortable()
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            Select::make('Gender')
                ->options(config('council.genders'))->displayUsingLabels()
                ->rules('required', Rule::in(array_keys(config('council.genders'))))
                ->hideFromIndex(),

            Date::make('Birthdate')
                ->firstDayOfWeek(1)->format('DD/MM/YYYY')->pickerFormat('Y-m-d')
                ->rules('required', 'date_format:Y-m-d', 'before:13 years ago')
                ->hideFromIndex(),

            Text::make('Class')
                ->onlyOnDetail(),

            Select::make('Class Course')
                ->options(array_combine(config('council.courses'), config('council.courses')))
                ->rules('required', Rule::in(config('council.courses')))
                ->onlyOnForms(),

            Number::make('Class Year')
                ->step(1)
                ->rules('required', 'digits:4')
                ->onlyOnForms(),

            new Panel('Contact Information', [
                Text::make('Username')
                    ->rules('required', 'regex:/^[a-z-]+\.[a-z-]+$/', 'max:255'),

                Text::make('Email')
                    ->rules('required', 'email', 'max:255')
                    ->updateRules('unique:users,email,{{resourceId}}'),

                Text::make('Phone')
                    ->resolveUsing(function ($phone) {
                        return $phone->formatInternational();
                    })
                    ->hideFromIndex()
                    ->rules('required', Rule::opinionatedPhone()),
            ]),

            $this->membershipIndicatorField()
                ->onlyOnIndex(),

            Subscription::make()
                ->canSee(function ($request) {
                    return $request->user()->hasPermissionTo('subscriptions.manage');
                }),

            new Panel('Roles & Permissions', [
                RoleBooleanGroup::make('Roles')->hideFromIndex(),
                PermissionBooleanGroup::make('Permissions')->hideFromIndex(),
            ]),

            Impersonate::make($this),
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

    /**
     * @return \Inspheric\Fields\Indicator
     */
    protected function membershipIndicatorField(): Indicator
    {
        return Indicator::make('Membership', function () {
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
            ]);
    }

    /**
     * @return \Laravel\Nova\Fields\Avatar
     */
    protected function avatarField(): Avatar
    {
        return Avatar::make('Avatar', 'avatar_path')
            ->disableDownload() // Tough to make it work.
            ->deletable(! is_null($this->model()->getRawOriginal('avatar_path')))
            ->preview(function ($value) {
                return $value;
            })
            ->thumbnail(function ($value) {
                return $value;
            })
            ->store(function (Request $request, $user) {
                Storage::disk('public')->delete($user->getRawOriginal('avatar_path'));

                return [
                    'avatar_path' => $request->file('avatar_path')
                        ->store('avatars', 'public'),
                ];
            })
            ->delete(function (Request $request, $user, $disk, $path) {
                Storage::disk('public')->delete($user->getRawOriginal('avatar_path'));
            });
    }
}
