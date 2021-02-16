<?php

namespace App\Traits;

trait CanImpersonate
{
    /**
     * Determines whether the model can impersonate.
     */
    public function canImpersonate(): bool
    {
        return $this->hasPermissionTo('impersonate');
    }
}
