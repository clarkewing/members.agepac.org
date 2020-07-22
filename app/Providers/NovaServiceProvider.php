<?php

namespace App\Providers;

use App\Policies\MentorshipTagsPolicy;
use App\Policies\MenuPolicy;
use App\Policies\UserNovaPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

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

            // Override User policy in Nova.
            Gate::policy(User::class, UserNovaPolicy::class);

            if ($event->request->segment(2) === 'mentorship-tags') {
                Gate::policy(\Spatie\Tags\Tag::class, MentorshipTagsPolicy::class);
            }

            if ($event->request->segment(2) === 'nova-menu') {
                Gate::policy(\OptimistDigital\MenuBuilder\Models\Menu::class, MenuPolicy::class);
            }
        });

        // Boot the nova-menu-builder package so we can properly render the resource.
        (new \OptimistDigital\MenuBuilder\MenuBuilder)->boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
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
        Gate::define('viewNova', function (User $user) {
            return $user->isAdmin();
        });
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
            \Vyuldashev\NovaPermission\NovaPermissionTool::make(),
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
