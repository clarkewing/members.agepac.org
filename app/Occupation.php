<?php

namespace App;

use App\Exceptions\UnknownOccupationStatusException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_pilot',
        'status',
        'title',
    ];

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
     * Get occupation title.
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

    /**
     * Get the occupation's status.
     *
     * @return string
     * @throws \App\Exceptions\UnknownOccupationStatusException
     */
    public function getStatusAttribute(): string
    {
        if (isset($this->statusStrings()[$this->status_code])) {
            return $this->statusStrings()[$this->status_code];
        }

        Log::alert($this->getRawOriginal());
        throw new UnknownOccupationStatusException('', $this);
    }

    /**
     * Set the occupation's status.
     *
     * @param  mixed  $value
     * @return void
     * @throws \App\Exceptions\UnknownOccupationStatusException
     */
    public function setStatusAttribute($value): void
    {
        if (is_string($value)) {
            $statusInt = array_search($value, $this->statusStrings());

            if ($statusInt !== false) {
                $this->attributes['status_code'] = $statusInt;

                return;
            }
        }

        if (is_int($value) && isset($this->statusStrings()[$value])) {
            $this->attributes['status_code'] = $value;

            return;
        }

        throw new UnknownOccupationStatusException('', $this, $value);
    }

    /**
     * The array of strings corresponding to different statuses.
     *
     * @return array|string[]
     */
    public function statusStrings(): array
    {
        return [
            self::EMPLOYED_FULL_TIME => 'Salarié à temps plein',
            self::EMPLOYED_PART_TIME => 'Salarié à temps partiel',
            self::SELF_EMPLOYED => 'Auto-entrepreneur',
            self::UNPAID => 'Bénévole',
        ];
    }
}
