<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class UserAviationInfoImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure, WithProgressBar
{
    use Importable, SkipsFailures;

    /**
     * @param  \Maatwebsite\Excel\Row  $row
     * @return void
     */
    public function onRow(Row $row)
    {
        $user = User::find($row['userid']);

        $user->flight_hours = $row['hdv'];

        $user->bio = implode("\n", array_filter([$row['avion'], $row['competence']])) ?: null;

        $user->save();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'hdv' => ['nullable', 'integer', 'min:0', 'max:16777215'],
            'avion' => ['nullable', 'string', 'max:255'],
            'competence' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
