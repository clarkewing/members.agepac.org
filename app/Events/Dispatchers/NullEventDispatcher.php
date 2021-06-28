<?php

namespace App\Events\Dispatchers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;

class NullEventDispatcher implements DispatcherContract
{
    use ForwardsCalls;

    protected $dispatcher;

    protected $events;

    public function __construct(DispatcherContract $dispatcher, $events)
    {
        $this->dispatcher = $dispatcher;
        $this->events = Arr::wrap($events);
    }

    /**
     * @inheritDoc
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        if (! $this->suppresses($event)) {
            return $this->dispatcher->dispatch($event, $payload, $halt);
        }
    }

    protected function suppresses($event): bool
    {
        return in_array($this->eventName($event), $this->events);
    }

    protected function eventName($event)
    {
        if (is_string($event)) {
            return $event;
        }

        if (is_object($event)) {
            return get_class($event);
        }
    }

    /**
     * @inheritDoc
     */
    public function listen($events, $listener = null)
    {
        $this->dispatcher->listen($events, $listener);
    }

    // To meet DispatcherContract, also proxy methods 'hasListeners',
    // 'subscribe', 'until', 'push', 'flush', 'forget', 'forgetPushed'.

    /**
     * @inheritDoc
     */
    public function hasListeners($eventName)
    {
        return $this->forwardCallTo($this->dispatcher, 'hasListeners', $eventName);
    }

    /**
     * @inheritDoc
     */
    public function subscribe($subscriber)
    {
        return $this->forwardCallTo($this->dispatcher, 'subscribe', $subscriber);
    }

    /**
     * @inheritDoc
     */
    public function until($event, $payload = [])
    {
        return $this->forwardCallTo($this->dispatcher, 'until', [$event, $payload]);
    }

    /**
     * @inheritDoc
     */
    public function push($event, $payload = [])
    {
        return $this->forwardCallTo($this->dispatcher, 'push', [$event, $payload]);
    }

    /**
     * @inheritDoc
     */
    public function flush($event)
    {
        return $this->forwardCallTo($this->dispatcher, 'flush', $event);
    }

    /**
     * @inheritDoc
     */
    public function forget($event)
    {
        return $this->forwardCallTo($this->dispatcher, 'forget', $event);
    }

    /**
     * @inheritDoc
     */
    public function forgetPushed()
    {
        return $this->forwardCallTo($this->dispatcher, 'forgetPushed', []);
    }
}
