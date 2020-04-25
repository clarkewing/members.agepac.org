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

    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }

    protected function recordActivity($event)
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

    protected function getActivityType($event)
    {
        $type = strtolower((new ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
