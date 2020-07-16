<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\Http\Resources\MenuResource as BaseMenuResource;

class MenuResource extends BaseMenuResource
{
    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Content Management';
}
