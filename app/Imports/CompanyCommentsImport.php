<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class CompanyCommentsImport extends LegacyDBImport implements OnEachRow
{
    /**
     * @param  \Maatwebsite\Excel\Row  $row
     */
    public function onRow(Row $row)
    {
        $company = Company::find($row['cid']);
        $author = User::find($row['uid']);
        $time = Carbon::createFromTimestamp($row['tdate']);

        $company->remarks = implode("\n\n", array_filter([
            $company->remarks,
            "{$author->name} ({$time->toDateTimeString()}) :\n{$row['texte']}"
        ]));

        $company->save();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'cid' => ['required', 'integer', Rule::exists('companies', 'id')],
            'uid' => ['required', 'integer', Rule::exists('users', 'id')],
            'tdate' => ['required', 'date_format:U'],
            'texte' => ['required', 'string', 'max:65535'],
        ];
    }
}
