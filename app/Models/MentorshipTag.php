<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag as BaseTag;

class MentorshipTag extends BaseTag
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('mentorship_tag', function (Builder $builder) {
            $builder->withType('mentorship');
        });

        static::creating(function ($mentorshipTag) {
            $mentorshipTag->type = 'mentorship';
        });
    }

    /**
     * Get all of the profiles that are assigned this tag.
     */
    public function profiles() {
        return $this->morphedByMany(
            Profile::class,
            'taggable',
            'taggables',
        );
    }
}
