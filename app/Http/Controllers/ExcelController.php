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
    public function __construct(){
        $this->middleware('auth');
    }

    // Start an export job for a given table and selected fields (comma-separated)
    public function export(Request $request)
    {
        $table = $request->table;  // "projects", "tasks", or "comments"
        $fields = explode(',', $request->fields);
        ProcessExcelExport::dispatch($table, $fields, Auth::user()->id);
        return response()->json(['message' => 'Export process started; you will be notified when finished.']);
    }

    // Start an import job for a given table via an Excel file upload
    public function import(Request $request)
    {
        $request->validate([
           'table' => 'required|in:projects,tasks,comments',
           'file'  => 'required|file|mimes:xlsx,xls'
        ]);

        $table = $request->table;
        $file = $request->file('file');
        ProcessExcelImport::dispatch($table, $file->getRealPath(), Auth::user()->id);
        return response()->json(['message' => 'Import process started; you will be notified upon completion.']);
    }
}
