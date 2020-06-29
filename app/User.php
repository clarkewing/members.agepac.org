<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use Propaganistas\LaravelPhone\PhoneNumber;
use Torann\GeoIP\Facades\GeoIP;
use URLify;

class User extends Authenticatable implements MustVerifyEmail
{
    use Billable, HasLocation, HasReputation, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'class_course',
        'class_year',
        'gender',
        'birthdate',
        'phone',
        'avatar_path',
        'bio',
        'flight_hours',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password', 'remember_token', 'email_verified_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'isAdmin'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'username';
    }

    /**
     * Get the activity for the user.
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the threads for the user.
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    /**
     * Get the latest post posted by the user.
     */
    public function lastPost()
    {
        return $this->hasOne(Post::class)->latest();
    }

    /**
     * Get the user's work experience.
     */
    public function experience()
    {
        return $this->hasMany(Occupation::class)
            ->orderBy('is_primary', 'desc')
            ->orderBy('start_date', 'desc');
    }

    /**
     * Get the user's current occupation.
     *
     * @return \App\Occupation|null
     */
    public function currentOccupation()
    {
        return $this->experience->where('is_primary', true)->first()
               ?? $this->experience->whereNull('end_date')->first();
    }

    /**
     * Get the user's current occupation.
     *
     * @return bool
     */
    public function hasOccupation(): bool
    {
        return ! is_null($this->currentOccupation());
    }

    /**
     * Add an occupation to the user's experience.
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
     * Get the user's education.
     */
    public function education()
    {
        return $this->hasMany(Course::class)
            ->orderBy('start_date', 'desc');
    }

    /** Determine if user is administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array(
            strtolower($this->email),
            array_map('strtolower', config('council.administrators'))
        );
    }

    /**
     * Determine whether the user is an admin.
     *
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the cache key for visited threads.
     *
     * @param  \App\Thread $thread
     * @return string
     */
    public function read(Thread $thread)
    {
        Cache::forever(
            $this->visitedThreadCacheKey($thread),
            now()
        );
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's class.
     *
     * @return string
     */
    public function getClassAttribute(): string
    {
        return $this->class_course . ' ' . $this->class_year;
    }

    /**
     * Set the user's phone number.
     *
     * @param  string  $value
     * @return void
     */
    public function setPhoneAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['phone'] = null;
        } else {
            $this->attributes['phone'] = PhoneNumber::make($value)
                ->ofCountry('AUTO')
                ->ofCountry('FR')
                ->ofCountry(GeoIP::getLocation(request()->ip())->iso_code);
        }
    }

    /**
     * Get the user's phone number.
     *
     * @param  string  $value
     * @return \Propaganistas\LaravelPhone\PhoneNumber|null
     */
    public function getPhoneAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return PhoneNumber::make($value);
    }

    /**
     * Get the path of the User's avatar.
     *
     * @param  string $avatar
     * @return string
     */
    public function getAvatarPathAttribute($avatar): string
    {
        return $avatar
            ? Storage::url($avatar)
            : asset('images/avatars/default.jpg');
    }

    /**
     * Get the cache key for visited threads.
     *
     * @param  \App\Thread $thread
     * @return string
     */
    public function visitedThreadCacheKey(Thread $thread)
    {
        return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }

    /**
     * Make a username string from a first and last name.
     *
     * @param  string  $firstName
     * @param  string  $lastName
     * @return string
     */
    public static function makeUsername(string $firstName, string $lastName): string
    {
        return strtolower(URLify::filter($firstName) . '.' . URLify::filter($lastName));
    }
}
