<?php

namespace App\Nova\MenuBuilder;

use App\Page;
use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuPages extends MenuLinkable
{
    /**
     * {@inheritdoc}
     */
    public static function getIdentifier(): string
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'Page';
    }

    /**
     * {@inheritdoc}
     */
    public static function getOptions($locale): array
    {
        return Page::pluck('title', 'id')->all();
    }

    /**
     * {@inheritdoc}
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
