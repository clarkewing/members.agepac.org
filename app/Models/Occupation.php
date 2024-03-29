<?php

namespace App\Models;

use App\Exceptions\UnknownOccupationStatusException;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Occupation extends Model
{
    use HasFactory, HasLocation;

    public const EMPLOYED_FULL_TIME = 1;

    public const EMPLOYED_PART_TIME = 2;

    public const SELF_EMPLOYED = 3;

    public const UNPAID = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'position',
        'aircraft_id',
        'company_id',
        'status_code',
        'description',
        'start_date',
        'end_date',
        'is_primary',
    ];

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
        'user_id' => 'integer',
        'status_code' => 'integer',
        'aircraft_id' => 'integer',
        'company_id' => 'integer',
        'is_primary' => 'boolean',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['aircraft', 'company', 'location'];

    /**
     * The array of strings corresponding to different statuses.
     *
     * @return array|string[]
     */
    public static function statusStrings(): array
    {
        return [
            self::EMPLOYED_FULL_TIME => 'Salarié à temps plein',
            self::EMPLOYED_PART_TIME => 'Salarié à temps partiel',
            self::SELF_EMPLOYED => 'Auto-entrepreneur',
            self::UNPAID => 'Bénévole',
        ];
    }

    /**
     * Get the aircraft associated with the position.
     */
    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * Get the company associated with the position.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
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
}
