<?php

namespace App\Exports;

use App\Models\Cuti;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CutiExport implements FromQuery, WithHeadings
{
    protected $query;
    protected $request;

    // Konstruktor menerima query dan request
    public function __construct($query, $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    // Gunakan FromQuery untuk mengambil data berdasarkan query yang dikirimkan
    public function query()
    {
        return $this->query; // Mengembalikan query yang sudah difilter
    }

    // Menentukan headings atau kolom yang ingin ditampilkan
    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Alasan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
        ];
    }
}