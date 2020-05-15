<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Attachment extends Model
{
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'path',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($attachment): void
        {
            // Set the attachment's id.
            $attachment->id = Str::uuid();
        });

        static::deleting(function ($attachment): void
        {
            Storage::disk('public')->delete($attachment->path);
        });
    }

    /**
     * Get the post that owns the attachment.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
