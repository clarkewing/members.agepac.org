<?php

namespace App;

use Illuminate\Support\Collection;

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
        $this->attributes['body'] = preg_replace('/@([\w-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    /**
     * Returns an array of users mentioned in body.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mentionedUsers(): Collection
    {
        preg_match_all('/@([\w-]+)/', $this->body, $matches);

        return User::whereIn('name', $matches[1])->get();
    }
}
