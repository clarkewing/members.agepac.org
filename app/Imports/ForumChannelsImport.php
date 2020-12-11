<?php

namespace App\Imports;

use App\Models\Channel;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumChannelsImport extends LegacyDBImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Channel([
            'id' => $row['forumid'],
            'parent_id' => $row['parentid'] === -1 ? null : $row['parentid'],
            'name' => $row['title'],
            'description' => $row['description'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'forumid' => ['required', 'integer'],
            'parentid' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
