<?php

namespace App\Imports;

use App\Models\Favorite;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ForumPostFavoritesImport extends LegacyDBImport implements ToModel, WithUpserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $createdAt = Carbon::createFromTimestamp($row['date']);

        return new Favorite([
            'user_id' => $row['userid'],
            'favoritable_id' => $row['postid'],
            'favoritable_type' => 'post',
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
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'date' => ['required', 'date_format:U'],
            'postid' => ['required', 'integer', Rule::exists('posts', 'id')],
        ];
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return ['user_id', 'favoritable_id', 'favoritable_type'];
    }
}
