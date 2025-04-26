<?php

namespace App\Imports;

use App\Models\Cuti;
use Maatwebsite\Excel\Concerns\ToModel;

class CutiImport implements ToModel
{
    public function model(array $row)
    {
        return new Cuti([
            'user_id' => $row[0],
            'alasan' => $row[1],
            'tanggal_mulai' => $row[2],
            'tanggal_selesai' => $row[3],
            'status' => $row[4],
        ]);
    }
}
