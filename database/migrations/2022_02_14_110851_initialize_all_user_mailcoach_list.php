<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class InitializeAllUserMailcoachList extends Migration
{
    protected string $emailListName = 'All Users';

    public function up()
    {
        if (! class_exists(\Spatie\Mailcoach\MailcoachServiceProvider::class)) {
            return;
        }

        EmailList::create([
            'name' => $this->emailListName,
            'default_from_email' => 'bonjour@agepac.org',
            'default_from_name' => 'AGEPAC',
            'default_reply_to_email' => 'bonjour@agepac.org',
            'default_reply_to_name' => 'AGEPAC',
            'allow_form_subscriptions' => false,
            'requires_confirmation' => false,
            'send_welcome_mail' => false,
        ]);

        $this->importSubscribers();
    }

    public function down()
    {
        if (! class_exists(\Spatie\Mailcoach\MailcoachServiceProvider::class)) {
            return;
        }

        EmailList::where('name', $this->emailListName)->delete();
    }

    protected function importSubscribers()
    {
        User::query()->chunk(100, function ($users) {
            foreach ($users as $user) {
                Subscriber::createWithEmail($user->email, $user->only([
                    'first_name',
                    'last_name',
                    'class_course',
                    'class_year',
                    'gender',
                    'birthdate',
                    'phone',
                ]))->subscribeTo(EmailList::where('name', $this->emailListName)->firstOrFail());
            }
        });
    }
}
