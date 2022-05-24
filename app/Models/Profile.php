<?php

namespace App\Models;

use App\Traits\HasLocation;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Tags\HasTags;

class Profile extends User
{
    use HasFactory, HasLocation, HasTags, RecordsActivity, Searchable;

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
    protected $with = ['education', 'experience', 'location', 'mentorship_tags'];

    /**
     * Returns which activities should be recorded.
     *
     * @return array
     */
    protected static function getActivitiesToRecord(): array
    {
        return ['updated'];
    }

    /**
     * Create a new Profile model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
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
     * @return \App\Models\Occupation
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
     * @return \App\Models\Course
     */
    public function addEducation(array $course)
    {
        $course = $this->education()->create($course);

        return $course;
    }

    /**
     * Set the custom tag model.
     */
    public static function getTagClassName(): string
    {
        return MentorshipTag::class;
    }

    /**
     * Get the model's mentorship tags.
     */
    public function tags()
    {
        return $this->morphToMany(
            self::getTagClassName(),
            'taggable',
            relatedPivotKey: 'tag_id',
        )
            ->ordered();
    }

    /**
     * Get the model's mentorship tags.
     */
    public function mentorship_tags()
    {
        return $this->tags();
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return config('scout.prefix') . 'profiles';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->transform([
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'class_course' => $this->class_course,
            'class_year' => $this->class_year,
            'profile_photo_url' => $this->profile_photo_url,
            'mentorship_tags' => $this->mentorship_tags->pluck('name')->all(),
        ]);
    }
}
