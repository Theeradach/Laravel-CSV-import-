<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\HeadingRowImport;
use App\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function importExportView()
    {
        $users = User::all();
        return view('import', compact('users'));
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function import()
    {
        if (request()->hasFile('file')) {
            try {
                Excel::import(new UsersImport, request()->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();

                return back()->with('failures', $failures);

            }
            return back()->with('success', 'upload successfully!');
        }

    }

}
