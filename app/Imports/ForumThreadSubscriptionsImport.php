<?php

namespace App\Imports;

use App\Models\ThreadSubscription;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumThreadSubscriptionsImport extends LegacyDBImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new ThreadSubscription([
            'user_id' => $row['userid'],
            'thread_id' => $row['threadid'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'threadid' => ['required', 'integer', Rule::exists('threads', 'id')],
            'emailupdate' => [
                'required',
                'integer',
                Rule::in([
                    1, // Notification instantanée par email
                    2, // Mises à jour quotidiennes par email
                    3, // Mises à jour hebdomadaires par email
                ]),
            ],
        ];
    }
}
