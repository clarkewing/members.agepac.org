<?php

namespace App\Imports;

use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CompaniesImport extends LegacyDBImport implements ToModel, WithUpserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $company = new Company([
            'id' => $row['cid'],
            'name' => $row['titre'],
            'type_code' => [
                'Compagnie Aérienne' => Company::AIRLINE,
                'Ecole / Formation' => Company::SCHOOL,
                'Aéroclub' => Company::FLYING_CLUB,
                'Association' => Company::ASSOCIATION,
                'Entreprise' => Company::OTHER_BUSINESS,
                'Autre' => Company::OTHER_BUSINESS,
            ][$row['type']],
            'description' => strip_tags($row['description']),
            'conditions' => strip_tags($row['condition'] . '<br><br>' . $row['perspective']),
            'created_at' => Carbon::createFromTimestamp($row['d_date'])->toDateTimeString(),
            'updated_at' => Carbon::createFromTimestamp($row['u_date'])->toDateTimeString(),
        ]);

        $company->generateSlug();

        return $company;
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'id';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'cid' => ['required', 'integer'],
            'd_date' => ['required', 'date_format:U'],
            'u_date' => ['required', 'date_format:U'],
            'titre' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'string',
                Rule::in(['Compagnie Aérienne', 'Ecole / Formation', 'Aéroclub', 'Association', 'Entreprise', 'Autre']),
            ],
            'perspective' => ['nullable', 'string', 'max:65535'],
            'description' => ['nullable', 'string', 'max:65535'],
            'condition' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
