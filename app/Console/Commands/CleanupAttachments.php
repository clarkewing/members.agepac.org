<?php

namespace App\Console\Commands;

use App\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CleanupAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachment:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete attachments that haven\'t been reconciled with a post and are more than 2 hours old';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Attachment::where('post_id', null)
            ->where('created_at', '<', now()->subHours(2))
            ->delete();
    }
}
