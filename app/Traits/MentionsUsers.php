<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Collection;
use Stevebauman\Purify\Facades\Purify;

trait MentionsUsers
{
    /**
     * Sets the body of the subject.
     *
     * @param  string $body
     * @return void
     */
    public function setBodyAttribute($body): void
    {
        // TODO: Replace hardcoded href with dynamic route.
        $this->attributes['body'] = preg_replace($this->mentionPattern(), '<a href="/profiles/$1">$0</a>', $body);
    }

    /**
     * Get the sanitized body.
     *
     * @param  string  $value
     * @return string
     */
    public function getBodyAttribute($value)
    {
        return Purify::clean($value);
    }

    /**
     * Returns an array of users mentioned in body.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mentionedUsers(): Collection
    {
        preg_match_all($this->mentionPattern(), $this->body, $matches);

        return User::whereIn('username', $matches[1])->get();
    }

    /**
     * Returns the used for matching usernames.
     *
     * @return string
     */
    protected function mentionPattern(): string
    {
        return '/@([\w.-]+[^[:punct:]\s])/';
    }
}
