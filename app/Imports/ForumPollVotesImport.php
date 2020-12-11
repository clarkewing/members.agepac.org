<?php

namespace App\Imports;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumPollVotesImport extends LegacyDBImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $createdAt = Carbon::createFromTimestamp($row['votedate']);

        return new PollVote([
            'option_id' => Poll::find($row['pollid'])
                ->options[$row['voteoption'] - 1]
                ->id,
            'user_id' => $row['userid'],
            'created_at' => $createdAt->toDateTimeString(),
            'updated_at' => $createdAt->toDateTimeString(),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'pollid' => ['required', 'integer', Rule::exists('polls', 'id')],
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'votedate' => ['required', 'date_format:U'],
            'voteoption' => ['required', 'integer'],
        ];
    }
}
