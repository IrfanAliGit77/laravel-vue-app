<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DynamicImport;

class ProcessExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $table;
    protected $filePath;
    protected $userId;

    public function __construct($table, $filePath, $userId)
    {
        $this->table    = $table;
        $this->filePath = $filePath;
        $this->userId   = $userId;
    }

    public function handle()
    {
        Excel::import(new DynamicImport($this->table), $this->filePath, 'local');
        // Optionally: notify the user upon successful import
    }
}
