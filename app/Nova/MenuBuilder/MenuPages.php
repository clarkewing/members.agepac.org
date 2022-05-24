<?php

namespace App\Nova\MenuBuilder;

use App\Models\Page;
use OptimistDigital\MenuBuilder\MenuItemTypes\MenuItemSelectType;

class MenuPages extends MenuItemSelectType
{
    /**
     * @inheritdoc
     */
    public static function getIdentifier(): string
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'Page';
    }

    /**
     * @inheritdoc
     */
    public static function getOptions($locale): array
    {
        return Page::pluck('title', 'id')->all();
    }

    /**
     * @inheritdoc
     */
    public static function getDisplayValue($value, array $data = null, $locale)
    {
        return 'Page: ' . static::getValue($value)->title;
    }

    /**
     * @inheritdoc
     */
    public static function getValue($value, array $data = null, $locale)
    {
        return Page::find($value);
    }
}
