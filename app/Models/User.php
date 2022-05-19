<?php

namespace App\Models;

use App\Events\UserDeleted;
use App\Traits\CanImpersonate;
use App\Traits\HasReputation;
use App\Traits\Impersonatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Permission\Traits\HasRoles;
use Torann\GeoIP\Facades\GeoIP;
use URLify;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasReputation;
    use HasRoles;
    use RequiresApproval;
    use HasProfilePhoto;
    use Notifiable;
    use CanImpersonate;
    use Impersonatable;
    use Billable {
        createAsStripeCustomer as protected traitCreateAsStripeCustomer;
    }
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'approved_at',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birthdate' => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
        'flight_hours' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'profile_photo_url',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => UserDeleted::class,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($user) {
            $user->activity()->create([
                'type' => 'created_user',
                'user_id' => $user->id,
                'subject_id' => $user->id,
                'subject_type' => array_flip(Relation::$morphMap)[static::class],
            ]);
        });

        static::deleting(function ($user) {
            $user->activity()->delete();
        });
    }

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
     * Get the associated profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'id');
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
     * Get the cache key for visited threads.
     *
     * @param  \App\Models\Thread  $thread
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
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setFirstNameAttribute(string $value): void
    {
        $this->attributes['first_name'] = $this->ensureNameCapitalized($value);
    }

    /**
     * Set the user's last name.
     *
     * @param  string  $value
     * @return void
     */
    public function setLastNameAttribute(string $value): void
    {
        $this->attributes['last_name'] = $this->ensureNameCapitalized($value);
    }

    /**
     * Ensure the given name value is properly capitalized.
     * If not all lower or upper case, then apply nameCase method.
     *
     * @param  string  $value
     * @return string
     */
    public function ensureNameCapitalized(string $value): string
    {
        $isAllLowerCase = Str::lower($value) === $value;
        $isAllUpperCase = Str::upper($value) === $value;

        if ($isAllLowerCase || $isAllUpperCase) {
            return Str::nameCase($value);
        }

        return $value;
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
     * @param  \App\Models\Thread  $thread
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

    /**
     * Create a Stripe customer for the given model.
     *
     * @param  array  $options
     * @return \Stripe\Customer
     *
     * @throws \Laravel\Cashier\Exceptions\CustomerAlreadyCreated
     */
    public function createAsStripeCustomer(array $options = []): \Stripe\Customer
    {
        return $this->traitCreateAsStripeCustomer(array_merge(
            [
                'name' => $this->name,
                'description' => $this->class,
                'phone' => optional($this->phone)->formatInternational(),
            ],
            $options
        ));
    }
}
