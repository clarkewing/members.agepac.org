<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\User;
use App\Traits\ParsesLegacyBBCode;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumPostsImport extends LegacyDBImport implements ToModel
{
    use ParsesLegacyBBCode;

    /**
     * ForumPostsImport constructor.
     */
    public function __construct()
    {
        $this->instantiateBBCode();
    }

    /**
     * @param  array  $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $authorId = (
            User::where('id', $row['userid'])
                ->orWhere('username', $row['username'])
                ->orWhereRaw(Builder::concat('`first_name`', '" "', '`last_name`'), 'LIKE', $row['username'])
        )->value('id') ?? 1;

        $body = $row['pagetext'];

        if (! is_null($row['title'])) {
            $body = "[b][u]{$row['title']}[/u][/b]\n\n$body";
        }

        if ($authorId !== $row['userid']) {
            $body = "[i]{$row['username']}[/i] :\n\n$body";
        }

        $body = $this->parseBBCode($body);

        return new Post([
            'id' => $row['postid'],
            'thread_id' => $row['threadid'],
            'user_id' => $authorId,
            'body' => $body,
            'created_at' => Carbon::createFromTimestamp($row['dateline']),
            'updated_at' => Carbon::createFromTimestamp($row['dateline']),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'postid' => ['required', 'integer'],
            'threadid' => ['required', 'integer', Rule::exists('threads', 'id')],
            'username' => ['nullable', 'string'],
            'userid' => ['required', 'integer'],
            'title' => ['nullable', 'string'],
            'dateline' => ['required', 'date_format:U'],
            'pagetext' => ['required', 'string'],
        ];
    }
}
