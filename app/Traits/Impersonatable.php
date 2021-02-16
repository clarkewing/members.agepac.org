<?php

namespace App\Traits;

trait Impersonatable
{
    /**
     * Determines whether the model can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return true;
    }
}
