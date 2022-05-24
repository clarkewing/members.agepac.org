<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\MenuItemTypes\BaseMenuItemType;

class MenuSeparator extends BaseMenuItemType
{
    /**
     * @inheritdoc
     */
    public static function getType(): string
    {
        return 'separator';
    }

    /**
     * @inheritdoc
     */
    public static function getIdentifier(): string
    {
        return 'separator';
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'Separator';
    }

    /**
     * @inheritdoc
     */
    public static function getDisplayValue($value, array $data = null, $locale)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public static function getValue($value, array $data = null, $locale) {}

    public static function getRules(): array
    {
        $rules = parent::getRules();

        unset($rules['value']);

        return $rules;
    }
}
