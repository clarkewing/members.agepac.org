<?php

namespace App\Services\Mailcoach\Facades;

use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;
use Illuminate\Support\Facades\Facade;

class Mailcoach extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MailcoachApi::class;
    }
}
