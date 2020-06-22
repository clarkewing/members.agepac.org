<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use ReflectionClass;

class Occupation extends Model
{
    const EMPLOYED_FULL_TIME = 1;
    const EMPLOYED_PART_TIME = 2;
    const SELF_EMPLOYED = 3;
    const UNPAID = 4;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['aircraft', 'location'];

    /**
     * Get an array containing the defined statuses.
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function definedStatuses()
    {
        return Arr::except(
            (new ReflectionClass(get_class()))->getConstants(),
            ['CREATED_AT', 'UPDATED_AT']
        );
    }

    /**
     * Get status string.
     *
     * @return string
     */
    public function status()
    {
        switch ($this->status) {
            case self::EMPLOYED_FULL_TIME:
                return 'Salarié à temps plein';

            case self::EMPLOYED_PART_TIME :
                return 'Salarié à temps partiel';

            case self::SELF_EMPLOYED :
                return 'Auto-entrepreneur';

            case self::UNPAID :
                return 'Bénévole';
        }
    }

    /**
     * Get the aircraft associated with the position.
     */
    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * Get the occupation's location.
     */
    public function location()
    {
        return $this->morphOne(Location::class, 'locatable');
    }

    /**
     * Determine if this is a flying occupation.
     *
     * @return bool
     */
    public function getIsPilotAttribute(): bool
    {
        return ! is_null($this->aircraft_id);
    }

    /**
     * Return occupation title.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if ($this->is_pilot) {
            return $this->position . ' sur ' . $this->aircraft->name;
        }

        return $this->position;
    }
}
