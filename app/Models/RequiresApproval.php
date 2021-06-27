<?php

namespace App\Models;

trait RequiresApproval
{
    /**
     * Determine if the model has been approved by an authorized entity.
     */
    public function isApproved(): bool
    {
        return ! is_null($this->approved_at);
    }

    /**
     * Mark the given model as approved.
     */
    public function markAsApproved(): bool
    {
        return $this->forceFill([
            'approved_at' => $this->freshTimestamp(),
        ])->save();
    }
}
