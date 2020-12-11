<?php

namespace App\Imports;

use App\Models\Poll;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumThreadsImport extends LegacyDBImport implements ToModel
{
    /**
     * @param  array  $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $ownerId = (
            User::where('id', $row['postuserid'])
                ->orWhere('username', $row['postusername'])
                ->orWhereRaw(Builder::concat('`first_name`', '" "', '`last_name`'), 'LIKE', $row['postusername'])
        )->value('id') ?? 1;

        $this->createPoll($row);

        return new Thread([
            'id' => $row['threadid'],
            'user_id' => $ownerId,
            'channel_id' => $row['forumid'],
            'visits' => $row['views'],
            'title' => $row['title'],
            'locked' => ! $row['open'],
            'pinned' => $row['sticky'],
            'created_at' => Carbon::createFromTimestamp($row['dateline']),
            'updated_at' => Carbon::createFromTimestamp($row['lastpost']),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'threadid' => ['required', 'integer'],
            'forumid' => ['required', 'integer'],
            'pollid' => ['required', 'integer'],
            'postusername' => ['nullable', 'string'],
            'postuserid' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'views' => ['required', 'integer'],
            'open' => ['required', 'boolean'],
            'sticky' => ['required', 'boolean'],
            'dateline' => ['required', 'date_format:U'],
            'lastpost' => ['required', 'date_format:U'],
        ];
    }

    /**
     * Create the poll associated with the thread if it exists.
     *
     * @param  array  $row
     * @return void
     */
    protected function createPoll(array $row): void
    {
        if ($row['pollid'] === 0) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Poll::create([
            'id' => $row['pollid'],
            'thread_id' => $row['threadid'],

            // Following must be set temporarily while awaiting ForumPollsImport.
            'title' => '',
            'votes_editable' => false,
            'votes_privacy' => 0,
            'results_before_voting' => false,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
