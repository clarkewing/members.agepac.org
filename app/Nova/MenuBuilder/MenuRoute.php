<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuRoute extends MenuLinkable
{
    /**
     * @inheritdoc
     */
    public static function getType(): string
    {
        return 'static-url';
    }

    /**
     * @inheritdoc
     */
    public static function getIdentifier(): string
    {
        return 'route';
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'Route';
    }

    /**
     * @inheritdoc
     */
    public static function getDisplayValue($value = null, array $parameters = null)
    {
        return 'Route: ' . $value;
    }

    /**
     * @inheritdoc
     */
    public static function getValue($value = null, array $parameters = null)
    {
        return [
            'route' => $value,
            'params' => $parameters,
        ];
    }
}
