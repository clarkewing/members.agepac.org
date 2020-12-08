<?php

namespace App\Traits;

use App\Models\Location;

trait HasLocation
{
    /**
     * The "boot" method of the trait.
     *
     * @return void
     */
    protected static function bootHasLocation()
    {
        static::deleting(function ($model) {
            $model->location->delete();
        });
    }

    /**
     * Get the model's location relationship.
     */
    public function location()
    {
        return $this->morphOne(Location::class, 'locatable');
    }

    /**
     * Set the model's location.
     *
     * @param  array|null  $value
     * @return \App\Models\Location|null
     */
    public function setLocation(?array $value)
    {
        if (is_null($value)) {
            $this->location()->delete();

            return null;
        }

        return $this->location()->updateOrCreate([], $value);
    }
}
