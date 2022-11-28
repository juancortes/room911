<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => \Hash::make($row[2]),
            'departamento_produccion_id' => $row[3],
        ]);
    }
}
