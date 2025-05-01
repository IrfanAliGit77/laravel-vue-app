<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DynamicImport implements ToModel, WithHeadingRow
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function model(array $row)
    {
        // This example inserts the row into the table.
        // In a real scenario, map the row data to proper model fields.
        DB::table($this->table)->insert($row);
    }
}
