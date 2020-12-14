<?php

namespace App\Imports;

use App\Models\Company;
use App\Traits\ParsesLegacyBBCode;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class CompaniesImport extends LegacyDBImport implements OnEachRow
{
    use ParsesLegacyBBCode;

    /**
     * ForumPostsImport constructor.
     */
    public function __construct()
    {
        $this->instantiateBBCode();
    }

    /**
     * @param  \Maatwebsite\Excel\Row  $row
     * @return void
     */
    public function onRow(Row $row)
    {
        $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $row['d_maj']);

        $company = Company::firstOrNew([
            'name' => $row['titre'],
        ]);

        $company->fill([
            'type_code' => $row['type'] === 1 ? Company::AIRLINE : Company::OTHER_BUSINESS,
            'description' => $row['p_description'],
            'remarks' => strip_tags($this->parseBBCode($row['texte'])),
            'created_at' => ($company->exists ? min($company->created_at, $updatedAt) : $updatedAt)->toDateTimeString(),
            'updated_at' => $updatedAt->toDateString(),
        ])->save();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'titre' => ['required', 'string', 'max:255'],
            'd_maj' => ['required', 'date_format:Y-m-d H:i:s'],
            'p_description' => ['nullable', 'string', 'max:65535'],
            'texte' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
