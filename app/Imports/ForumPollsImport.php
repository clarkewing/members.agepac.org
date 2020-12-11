<?php

namespace App\Imports;

use App\Models\Poll;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ForumPollsImport extends LegacyDBImport implements OnEachRow
{
    /**
     * @param  \Maatwebsite\Excel\Row  $row
     */
    public function onRow(Row $row): void
    {
        $poll = Poll::find($row['pollid']);
        $createdAt = Carbon::createFromTimestamp($row['dateline']);

        $poll->fill([
            'title' => $row['question'],
            'votes_editable' => false,
            'max_votes' => $row['multiple'] ? null : 1,
            'votes_privacy' => (int) ! $row['public'],
            'results_before_voting' => $row['public'],
            'locked_at' => null,
            'created_at' => $createdAt->toDateTimeString(),
            'updated_at' => $createdAt->toDateTimeString(),
        ]);

        if ($row['timeout'] !== 0) {
            $poll->locked_at = $createdAt->copy()->addDays($row['timeout'])->toDateTimeString();
        } elseif (! $row['active']) {
            $poll->locked_at = $poll->updated_at
                             = Carbon::createFromTimestamp($row['lastvote'])->toDateTimeString();
        }

        $poll->save();

        $poll->addOptions(
            array_map(function ($optionLabel) use ($createdAt) {
                return [
                    'label' => $optionLabel,
                    'created_at' => $createdAt->toDateTimeString(),
                    'updated_at' => $createdAt->toDateTimeString(),
                ];
            }, explode(' |||', $row['options']))
        );
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'pollid' => ['required', 'integer', Rule::exists('polls', 'id')],
            'question' => ['required', 'string', 'max:255'],
            'dateline' => ['required', 'date_format:U'],
            'options' => ['required', 'string'],
            'active' => ['required', 'boolean'],
            'timeout' => ['required', 'integer'],
            'multiple' => ['required', 'boolean'],
            'public' => ['required', 'boolean'],
            'lastvote' => ['required', 'date_format:U'],
        ];
    }
}
