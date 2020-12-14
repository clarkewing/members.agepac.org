<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithValidation;

class LegacyDBImport implements WithHeadingRow, WithValidation, SkipsOnFailure, WithProgressBar
{
    use Importable, SkipsFailures;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
