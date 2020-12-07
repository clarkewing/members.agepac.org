<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'color',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($pollOption) {
            if (is_null($pollOption->color)) {
                $pollOption->color = Faker::create()->hexColor;
            }
        });
    }

    /**
     * Get the poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    /**
     * Get the votes of the poll.
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'option_id');
    }

    /**
     * Get the users that voted for this option.
     */
    public function voters()
    {
        return $this->belongsToMany(User::class, 'poll_votes', 'option_id');
    }
}
