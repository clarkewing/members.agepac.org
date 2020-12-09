<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Occupation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class OccupationsImport extends LegacyDBImport implements ToModel
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(array $row): \Illuminate\Database\Eloquent\Model
    {
        return new Occupation([
            'user_id' => $row['userid'],
            'position' => $row['fonction'],
            'company_id' => Company::firstOrCreate(
                [
                    'name' => $row['organisation'],
                ], [
                    'type_code' => Company::OTHER_BUSINESS,
                    'created_at' => Carbon::createFromTimestamp($row['tdate'])->toDateString(),
                    'updated_at' => Carbon::createFromTimestamp($row['tdate'])->toDateString(),
                ]
            )->id,
            'status_code' => $row['statut'],
            'description' => $row['description'],
            'start_date' => Carbon::createFromTimestamp($row['begin'])->toDateString(),
            'end_date' => $row['fin'] === 0
                ? null
                : Carbon::createFromTimestamp($row['fin'])->toDateString(),
            'is_primary' => $row['principal'],
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
            'tdate' => ['required', 'integer'],
            'userid' => ['required', 'integer', Rule::exists('users', 'id')],
            'statut' => ['required', Rule::in(array_keys(Occupation::statusStrings()))],
            'organisation' => ['required', 'string', 'max:255'],
            'fonction' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'begin' => ['required', 'integer'],
            'fin' => ['required', 'integer'],
            'principal' => ['required', 'boolean'],
        ];
    }
}
