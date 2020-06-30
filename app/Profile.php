<?php

namespace App;

trait Profile
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillableProfile = [
        'bio',
        'flight_hours',
    ];

    public function initializeProfile()
    {
        $this->fillable = array_merge($this->fillable, $this->fillableProfile);
    }

    /**
     * Get the model's work experience.
     */
    public function experience()
    {
        return $this->hasMany(Occupation::class)
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
        return $this->hasMany(Course::class)
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
