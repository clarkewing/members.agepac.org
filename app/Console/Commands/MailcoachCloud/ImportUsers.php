<?php

namespace App\Console\Commands\MailcoachCloud;

use App\Actions\SubscribeUserToMembersNewsletterAction;
use App\Actions\SubscribeUserToNewsletterAction;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Cashier\Subscription as StripeSubscription;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailcoach-cloud:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all users into Mailcoach Cloud';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->withProgressBar(User::all(), function ($user) {
            $this->subscribeToNewsletters($user);
        });
        $this->newLine();

        $this->info('Import complete!');
    }

    protected function subscribeToNewsletters(User $user): void
    {
        app(SubscribeUserToNewsletterAction::class)->execute($user);

        if ($this->hasActiveSubscription($user)) {
            app(SubscribeUserToMembersNewsletterAction::class)->execute($user);
        }
    }

    protected function hasActiveSubscription(User $user): bool
    {
        return StripeSubscription
            ::where('user_id', $user->id)
            ->where('name', 'membership')
            ->active()
            ->exists();
    }
}
