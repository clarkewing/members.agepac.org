<?php

namespace App\Traits;

use App\Events\Dispatchers\NullEventDispatcher;
use Illuminate\Support\Facades\Event;

trait SuppressesEvents
{
    public function suppressingEvents($events, callable $callback)
    {
        try {
            $dispatcher = Event::getFacadeRoot();

            Event::swap(
                new NullEventDispatcher($dispatcher, $events)
            );

            return $callback();
        } finally {
            Event::swap($dispatcher);
        }
    }

    public function suppressingModelEvents(string $model, $events, callable $callback)
    {
        try {
            $dispatcher = $model::getEventDispatcher();

            $model::setEventDispatcher(
                new NullEventDispatcher($dispatcher, $events)
            );

            return $callback();
        } finally {
            $model::setEventDispatcher($dispatcher);
        }
    }
}
