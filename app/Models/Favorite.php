<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use RecordsActivity;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the favorited model.
     */
    public function favoritable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the favorite.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
