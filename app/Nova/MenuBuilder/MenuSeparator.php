<?php

namespace App\Nova\MenuBuilder;

use OptimistDigital\MenuBuilder\Classes\MenuLinkable;

class MenuSeparator extends MenuLinkable
{
    /**
     * {@inheritdoc}
     */
    public static function getType(): string
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public static function getIdentifier(): string
    {
        return 'separator';
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'Separator';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDisplayValue($value = null, array $parameters = null)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public static function getValue($value = null, array $parameters = null)
    {
    }

    public static function getRules(): array
    {
        $rules = parent::getRules();

        unset($rules['value']);

        return $rules;
    }
}
