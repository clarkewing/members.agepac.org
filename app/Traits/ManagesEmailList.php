<?php

namespace App\Traits;

use App\Models\User;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

trait ManagesEmailList
{
    public function addToEmailList(User $user): void
    {
        Subscriber::createWithEmail($user->email, $user->only([
            'first_name',
            'last_name',
            'class_course',
            'class_year',
            'gender',
            'birthdate',
            'phone',
        ]))
            ->subscribeTo($this->emailList());
    }

    public function removeFromEmailList(User $user): void
    {
        Subscriber::findForEmail($user->email, $this->emailList())
            ?->delete();
    }

    public function updateEmailListEmail(string $oldEmail, string $newEmail): void
    {
        Subscriber::whereEmail($oldEmail)->get()->each->update(['email' => $newEmail]);
    }

    public function emailList(): EmailList
    {
        return EmailList::where('name', $this->emailListName)->firstOrFail();
    }
}
