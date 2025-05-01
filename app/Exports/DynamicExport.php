<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class DynamicExport implements FromCollection
{
    protected $table;
    protected $fields;

    public function __construct($table, $fields)
    {
        $this->table  = $table;
        $this->fields = $fields;
    }

    public function collection()
    {
        return DB::table($this->table)->select($this->fields)->get();
    }
}
