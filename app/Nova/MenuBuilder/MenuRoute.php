<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\MenuItemTypes\MenuItemStaticURLType;

class MenuRoute extends MenuItemStaticURLType
{
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
    public static function getDisplayValue($value, array $data = null, $locale)
    {
        return 'Route: ' . $value;
    }

    /**
     * @inheritdoc
     */
    public static function getValue($value, array $data = null, $locale)
    {
        return [
            'route' => $value,
            'params' => $data,
        ];
    }
}
