<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuSeparator extends MenuLinkable
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'text';
    }

    /**
     * @inheritDoc
     */
    public static function getIdentifier(): string
    {
        return 'separator';
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'Separator';
    }

    /**
     * @inheritDoc
     */
    public static function getDisplayValue($value = null, array $parameters = null)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public static function getValue($value = null, array $parameters = null)
    {
        return null;
    }

    public static function getRules(): array
    {
        $rules = parent::getRules();

        unset($rules['value']);

        return $rules;
    }
}
