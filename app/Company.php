<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Company extends Model
{
    use HasFactory;

    use HasSlug, Searchable;

    const AIRLINE = 1;
    const AIRWORK = 2;
    const SCHOOL = 3;
    const FLYING_CLUB = 4;
    const GOV_AGENCY = 5;
    const ASSOCIATION = 6;
    const OTHER_BUSINESS = 9;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type_code',
        'website',
        'description',
        'operations',
        'conditions',
        'remarks',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['type'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->description)) {
                $user->setDescriptionFromWikipedia();
            }
        });
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the company's employees.
     */
    public function employees()
    {
        return $this->belongsToMany(
            Profile::class,
            'occupations',
            'company_id',
            'user_id'
        );
    }

    /**
     * Get the company's current employees.
     */
    public function current_employees()
    {
        return $this->employees()->wherePivotNull('end_date');
    }

    /**
     * Get the company's former employees.
     */
    public function former_employees()
    {
        return $this->employees()->wherePivotNotNull('end_date');
    }

    /**
     * Get the company's type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        if (isset($this->typeStrings()[$this->type_code])) {
            return $this->typeStrings()[$this->type_code];
        }
    }

    /**
     * The array of strings corresponding to different types.
     *
     * @return array|string[]
     */
    public static function typeStrings(): array
    {
        return [
            self::AIRLINE => 'Compagnie aérienne',
            self::AIRWORK => 'Travail aérien',
            self::SCHOOL => 'École',
            self::FLYING_CLUB => 'Aéroclub',
            self::GOV_AGENCY => 'Agence gouvernementale',
            self::ASSOCIATION => 'Association',
            self::OTHER_BUSINESS => 'Autre entreprise',
        ];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'type' => $this->type,
            'website' => $this->website,
        ];
    }

    public function setDescriptionFromWikipedia()
    {
        $response = Http::get('https://fr.wikipedia.org/w/api.php', [
            'format' => 'json',
            'action' => 'query',
            'prop' => 'extracts',
            'exintro' => true,
            'explaintext' => true,
            'redirects' => true,
            'titles' => $this->name,
        ])['query']['pages'];

        if (! Arr::has($response, -1)
            && Arr::has(Arr::first($response), 'extract')) {
            // Company intro found on Wikipedia. Fill description.
            $this->description = Arr::first($response)['extract'];
        }

        return $this;
    }
}
