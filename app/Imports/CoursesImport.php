<?php

namespace App\Imports;

use App\Models\Course;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class CoursesImport extends OldSiteImport implements ToModel
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(array $row): \Illuminate\Database\Eloquent\Model
    {
        return new Course([
            'user_id' => $row['userid'],
            'title' => $row['fonction'],
            'school' => $row['organisation'],
            'description' => $row['description'],
            'start_date' => Carbon::createFromTimestamp($row['begin'])->toDateString(),
            'end_date' => $row['fin'] === 0
                ? null
                : Carbon::createFromTimestamp($row['fin'])->toDateString(),
            'created_at' => Carbon::createFromTimestamp($row['tdate'])->toDateString(),
            'updated_at' => Carbon::createFromTimestamp($row['tdate'])->toDateString(),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'fonction' => ['required', 'string', 'max:255'],
            'organisation' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'begin' => ['required', 'integer'],
            'fin' => ['required', 'integer'],
            'tdate' => ['required', 'integer'],
        ];
    }
}
