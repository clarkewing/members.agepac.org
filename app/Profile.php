<?php

namespace App;

class Profile extends User
{
    use HasLocation;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bio',
        'flight_hours',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['location'];

    /**
     * Create a new Profile model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        // Hide Stripe fields.
        $this->makeHidden([
            'stripe_id',
            'card_brand',
            'card_last_four',
            'trial_ends_at',
        ]);
    }

    /**
     * Get the model's work experience.
     */
    public function experience()
    {
        return $this->hasMany(Occupation::class, 'user_id')
            ->orderBy('is_primary', 'desc')
            ->orderBy('start_date', 'desc');
    }

    /**
     * Get the model's current occupation.
     *
     * @return \App\Occupation|null
     */
    public function currentOccupation()
    {
        return $this->experience->where('is_primary', true)->first()
               ?? $this->experience->whereNull('end_date')->first();
    }

    /**
     * Get the model's current occupation.
     *
     * @return bool
     */
    public function hasOccupation(): bool
    {
        return ! is_null($this->currentOccupation());
    }

    /**
     * Add an occupation to the model's experience.
     *
     * @param  array $occupation
     * @return \App\Occupation
     */
    public function addExperience(array $occupation)
    {
        $occupation = $this->experience()->create($occupation);

        return $occupation;
    }

    /**
     * Get the model's education.
     */
    public function education()
    {
        return $this->hasMany(Course::class, 'user_id')
            ->orderBy('start_date', 'desc');
    }

    /**
     * Add an course to the model's education.
     *
     * @param  array $course
     * @return \App\Course
     */
    public function addEducation(array $course)
    {
        $course = $this->education()->create($course);

        return $course;
    }
}
