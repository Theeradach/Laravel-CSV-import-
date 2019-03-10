<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\User;

class ExcelImport implements ToModel, WithStartRow, WithCalculatedFormulas, WithLimit, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {

        dd($row);

        return new User([
            'name'     => $row[1],
            'name2'     => $row[2],
            'name3'     => $row[3],
        ]);
    }

    // start row 
    public function startRow(): int
    {
        return 21;
    }

    // end row
    public function limit(): int
    {
        return 68;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
