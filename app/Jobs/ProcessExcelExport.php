<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DynamicExport;

class ProcessExcelExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $table;
    protected $fields;
    protected $userId;

    public function __construct($table, $fields, $userId)
    {
        $this->table  = $table;
        $this->fields = $fields;
        $this->userId = $userId;
    }

    public function handle()
    {
        $export   = new DynamicExport($this->table, $this->fields);
        $fileName = "{$this->table}_export_".time().".xlsx";
        Excel::store($export, $fileName, 'public');
        // Optionally: notify the user via email/notification here
    }
}
