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
        //$headings = (new HeadingRowImport)->toArray(request()->file('file'));
        //dd($headings);
        try {
            Excel::import(new UsersImport, request()->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return back()->with('failures', $failures);

            //dd($failures);
            // foreach ($failures as $failure) {
            //     $failure->row(); // row that went wrong
            //     $failure->attribute(); // either heading key (if using heading row concern) or column index
            //     $failure->errors(); // Actual error messages from Laravel validator
            // }
        }
        return back()->with('success', 'upload successfully!');
    }

}
