<?php

namespace App\Nova\Actions;

use App\Models\Channel;
use App\Models\Thread;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;

class UpdateThreadChannel extends Action
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Update Channel';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        Thread::whereIn('id', $models->pluck('id'))
            ->update(['channel_id' => $fields->channel_id]);

        return Action::message('Channel successfully updated for selected threads.');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        $channels = Channel::withoutGlobalScopes()->get();

        return [
            Select::make('Channel', 'channel_id')
                ->options($channels->mapWithKeys(function ($channel) {
                    return [
                        $channel['id'] => [
                            'label' => $channel->name,
                            'group' => $channel->archived
                                ? 'Archived'
                                : optional($channel->parent)->name ?? $channel->name,
                        ]
                    ];
                }))
                ->rules(['required', Rule::in($channels->pluck('id'))]),
        ];
    }
}
