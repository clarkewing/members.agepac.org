<?php

namespace App\Imports;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
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

        return new Thread([
            'id' => $row['threadid'],
            'user_id' => $ownerId,
            'channel_id' => $row['forumid'],
            'visits' => $row['views'],
            'title' => $row['title'],
            'locked' => ! (bool) $row['open'],
            'pinned' => (bool) $row['sticky'],
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
            'postusername' => ['nullable', 'string'],
            'postuserid' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'views' => ['required', 'integer'],
            'open' => ['required', 'integer'], // Use integer as some values are nor 0 nor 1 but can evaluate as truthy
            'sticky' => ['required', 'boolean'],
            'dateline' => ['required', 'date_format:U'],
            'lastpost' => ['required', 'date_format:U'],
        ];
    }
}
