<?php

namespace App\Models;

trait MustBeVerified
{
    /**
     * Determine if the user has been verified by an authorized entity.
     */
    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Mark the given user as verified.
     */
    public function markAsVerified(): bool
    {
        return $this->forceFill([
            'verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
