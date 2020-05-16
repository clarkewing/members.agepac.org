<?php

namespace App\Listeners;

use App\Attachment;
use Illuminate\Support\Collection;
use Symfony\Contracts\EventDispatcher\Event;

class ReconcileAttachments
{
    /**
     * Handle the event.
     *
     * @param  \Symfony\Contracts\EventDispatcher\Event  $event
     * @return void
     */
    public function handle(Event $event): void
    {
        $attachmentsData = $this->parseAttachments($event->post->getAttributes()['body']);

        // Associate existing attachments with those referenced in body.
        Attachment::whereIn('id', $attachmentsData->pluck('id'))
            ->update(['post_id' => $event->post->id]);

        // Delete post's attachments that are not referenced in body.
        Attachment::where('post_id', $event->post->id)
            ->whereNotIn('id', $attachmentsData->pluck('id'))
            ->get()->each->delete();
    }

    /**
     * Retrieved attachment data from passed body.
     *
     * @param  string  $body
     * @return \Illuminate\Support\Collection
     */
    protected function parseAttachments(string $body): Collection
    {
        preg_match_all(
            '/data-trix-attachment="([^"]*)"/',
            $body,
            $matches
        );

        return collect(
            array_map('json_decode', array_map('html_entity_decode', $matches[1]))
        );
    }
}
