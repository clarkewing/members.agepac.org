<?php

namespace App\Imports;

use App\Models\Profile;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class ProfileInfoImport extends LegacyDBImport implements OnEachRow
{
    /**
     * @param  \Maatwebsite\Excel\Row  $row
     * @return void
     */
    public function onRow(Row $row)
    {
        Profile::where('id', $row['userid'])->update([
            'flight_hours' => $row['hdv'],
            'bio' => implode("\n", array_filter([$row['avion'], $row['competence']])) ?: null,
        ]);
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
