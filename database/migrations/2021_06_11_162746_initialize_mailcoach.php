<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Laravel\Cashier\Subscription;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class InitializeMailcoach extends Migration
{
    protected string $emailListName = 'Members';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! class_exists(\Spatie\Mailcoach\MailcoachServiceProvider::class)) {
            return;
        }

        Schema::table('mailcoach_subscribers', function (Blueprint $table) {
            $table->string('class_course', 30)->nullable();
            $table->year('class_year')->nullable();
            $table->string('gender', 1)->default('U');
            $table->date('birthdate')->nullable();
            $table->string('phone', 20)->nullable();
        });

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! class_exists(\Spatie\Mailcoach\MailcoachServiceProvider::class)) {
            return;
        }

        Schema::table('mailcoach_subscribers', function (Blueprint $table) {
            $table->dropColumn(['class_course', 'class_year', 'gender', 'birthdate', 'phone']);
        });

        EmailList::where('name', $this->emailListName)->delete();
    }

    protected function importSubscribers()
    {
        Subscription::query()->active()->with('user')->chunk(100, function ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                Subscriber::createWithEmail($subscription->user->email, $subscription->user->only([
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
