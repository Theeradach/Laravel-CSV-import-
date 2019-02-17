<?php

namespace App\Imports;

use Hash;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{

    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!empty($row)) {
            User::firstOrCreate([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => $row['pass'],
            ]);
        } else {
            dd($row);
        }
    }

    public function rules() : array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
        ];
    }

    public function batchSize() : int
    {
        return 1000;
    }

    public function chunkSize() : int
    {
        return 1000;
    }
}
