<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuRoute extends MenuLinkable
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'static-url';
    }

    /**
     * @inheritDoc
     */
    public static function getIdentifier(): string
    {
        return 'route';
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Route';
    }

    /**
     * @inheritDoc
     */
    public static function getDisplayValue($value = null, array $parameters = null)
    {
        return 'Route: ' . $value;
    }

    /**
     * @inheritDoc
     */
    public static function getValue($value = null, array $parameters = null)
    {
        return [
            'route' => $value,
            'params' => $parameters,
        ];
    }
}
