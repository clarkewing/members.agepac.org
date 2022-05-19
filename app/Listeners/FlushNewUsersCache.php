<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;

class FlushNewUsersCache
{
    public function handle()
    {
        Cache::forget('new-users');
    }
}
