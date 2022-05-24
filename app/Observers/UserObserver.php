<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\ManagesEmailList;

class UserObserver
{
    use ManagesEmailList;

    protected string $emailListName = 'All Users';

    public function created(User $user)
    {
        $this->addToEmailList($user);
    }

    public function updated(User $user)
    {
        if ($user->wasChanged('email')) {
            $this->updateEmailListEmail($user->getOriginal('email'), $user->email);
        }
    }

    public function deleted(User $user)
    {
        $this->removeFromEmailList($user);
    }
}
