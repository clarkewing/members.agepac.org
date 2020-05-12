<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use ReflectionClass;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (Auth::guest()) {
            return;
        }

        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    /**
     * Returns which activities should be recorded.
     *
     * @return array
     */
    protected static function getActivitiesToRecord(): array
    {
        return ['created'];
    }

    /**
     * Records model activity for given event.
     *
     * @param  \Symfony\Contracts\EventDispatcher\Event  $event
     * @return void
     */
    protected function recordActivity($event): void
    {
        $this->activity()->create([
            'user_id' => Auth::id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    /**
     * Get all of the subject's activity.
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Return the name of the activity.
     *
     * @param  \Symfony\Contracts\EventDispatcher\Event  $event
     * @return string
     */
    protected function getActivityType($event): string
    {
        $type = strtolower((new ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
