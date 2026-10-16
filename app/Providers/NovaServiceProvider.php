<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use App\Policies\ChannelNovaPolicy;
use App\Policies\MentorshipTagsPolicy;
use App\Policies\MenuPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\ThreadNovaPolicy;
use App\Policies\UserNovaPolicy;
use Giuga\LaravelNovaSidebar\NovaSidebar;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Mydnic\NovaKustomer\NovaKustomer;
use Vyuldashev\NovaPermission\NovaPermissionTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(function (ServingNova $event) {
            app()->setLocale('en');

            // Override Channel policy in Nova.
            Gate::policy(Channel::class, ChannelNovaPolicy::class);

            // Override Thread policy in Nova.
            Gate::policy(Thread::class, ThreadNovaPolicy::class);

            // Override User policy in Nova.
            Gate::policy(User::class, UserNovaPolicy::class);

            // Set mentorship tags policy in Nova.
            Gate::policy(\Spatie\Tags\Tag::class, MentorshipTagsPolicy::class);

            // Set menus policy in Nova.
            Gate::policy(\OptimistDigital\MenuBuilder\Models\Menu::class, MenuPolicy::class);
        });

        // Boot the nova-menu-builder package so we can properly render the resource.
        (new \OptimistDigital\MenuBuilder\MenuBuilder)->boot();
    }

    /**
     * Register the application's Nova resources.
     *
     * @return void
     */
    protected function resources()
    {
        Nova::resourcesIn(app_path('Nova'));

        // Users are managed in the new app's admin panel.
        Nova::replaceResources(array_values(array_diff(Nova::$resources, [\App\Nova\User::class])));
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        // Admins log in on the new app, and come back to Nova once they have.
        Nova::routes()
            ->withAuthenticationRoutes(['web', 'handoff.login'])
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        // viewNova gate defined in AuthServiceProvider
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            NovaPermissionTool::make()
                ->permissionPolicy(PermissionPolicy::class)
                ->rolePolicy(RolePolicy::class),

            (new NovaKustomer)->canSee(function ($request) {
                return $request->user()->hasPermissionTo('feedback.manage');
            }),

            (new NovaSidebar)->hydrate([
                'Communication' => [
                    ['Mailcoach', 'https://agepac.mailcoach.app/'],
                ],
            ]),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
