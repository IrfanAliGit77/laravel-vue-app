<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DynamicExport;
use App\Imports\DynamicImport;
use App\Jobs\ProcessExcelExport;
use App\Jobs\ProcessExcelImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Memulai job export untuk tabel dan field yang dipilih.
     *
     * Data yang diterima:
     *   - table: "projects", "tasks", atau "comments"
     *   - fields: string (dipisahkan dengan koma, misalnya "id,name")
     */
    public function export(Request $request)
    {
        $table = $request->input('table');
        $fields = explode(',', $request->input('fields'));

        ProcessExcelExport::dispatch($table, $fields, Auth::user()->id);

        return redirect()->back()->with('success', 'Export process started; you will be notified when finished.');
    }

    /**
     * Memulai job import untuk tabel tertentu melalui unggahan file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'table' => 'required|in:projects,tasks,comments',
            'file'  => 'required|file|mimes:xlsx,xls'
        ]);

        $table = $request->input('table');
        $file = $request->file('file');

        ProcessExcelImport::dispatch($table, $file->getRealPath(), Auth::user()->id);

        return redirect()->back()->with('success', 'Import process started; you will be notified upon completion.');
    }
}
