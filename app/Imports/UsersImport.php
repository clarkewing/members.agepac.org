<?php

namespace App\Imports;

use App\Models\User;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;

class UsersImport extends LegacyDBImport implements ToModel, WithUpserts
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(array $row): \Illuminate\Database\Eloquent\Model
    {
        $user = new User([
            'id' => $row['userid'],
            'first_name' => Str::before($row['username'], '.'),
            'last_name' => Str::after($row['username'], '.'),
            'username' => $row['username'],
            'gender' => 'U', // U for Unknown
            'email' => strtolower($row['email']),
            'created_at' => $row['joindate'] === 0
                ? null
                : Carbon::createFromTimestamp($row['joindate'])->toDateTimeString(),
        ]);

        if (! empty($row['birthday'])) {
            try {
                $user->birthdate = Carbon::createFromFormat('m-d-Y', $row['birthday'])->toDateString();
            } catch (InvalidFormatException $e) {
                try {
                    $user->birthdate = Carbon::createFromFormat('m-j-Y', $row['birthday'])->toDateString();
                } catch (InvalidFormatException $e) {
                    // Leave birthday null.
                }
            }
        }

        return $user;
    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'username';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'userid' => ['required', 'integer'],
            'username' => ['required', 'string'],
            'joindate' => ['integer'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'birthday' => ['nullable', 'regex:/^\d{2}-\d{1,2}-\d{4}$/'],
        ];
    }
}
