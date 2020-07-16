<?php

namespace App\Nova\MenuBuilder;

use App\Page;
use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuPages extends MenuLinkable
{
    /**
     * @inheritDoc
     */
    public static function getIdentifier(): string
    {
        return 'page';
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Page';
    }

    /**
     * @inheritDoc
     */
    public static function getOptions($locale): array
    {
        return Page::pluck('title', 'id')->all();
    }

    /**
     * @inheritDoc
     */
    public static function getDisplayValue($value = null, array $parameters = null)
    {
        return 'Page: ' . static::getValue($value, $parameters)->title;
    }

    public static function getValue($value = null, array $parameters = null)
    {
        return Page::find($value);
    }
}
