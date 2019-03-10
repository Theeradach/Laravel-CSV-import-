<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\HeadingRowImport;
use App\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Jobs\ImportJob;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Validator;
use League\Csv\Exception;
use App\Jobs\ExportJob;
use League\Csv\Writer;
use SplTempFileObject;
use App\Imports\ExcelImport;


class UsersController extends Controller
{

    public function importExportView()
    {
        $users = User::paginate(10)->onEachSide(5);
        return view('importLibrary', compact('users'));
    }

    public function exportLibrary()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function importLibrary()
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

    //===========================================
    //     Import without Library / using Queue
    //===========================================

    public function importNoLibrary(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $request->file('file')->storeAs(
                'public/import',
                $filename
            );

            // Validate data in csv 
            $csv_errors = $this->validator($filename);

            //dd($csv_errors);
            if (!empty($csv_errors)) {
                return redirect()->back()
                    ->withErrors($csv_errors, 'import');
            }

            logger($filename);
            //ImportJob::dispatch($filename);
            dispatch(new ImportJob($filename));

            return redirect()->back()->with(['success' => 'Upload success']);
        }
        return redirect()->back()->with(['error' => 'Failed to upload file']);
    }

    public function viewNoLibrary()
    {
        $user = User::paginate(10);
        return view('importNoLibrary', compact('user'));
    }

    public function validator($fileName)
    {
        // Line endings fix
        ini_set('auto_detect_line_endings', true);
        $errs = array();
        $line = 0;
        $filePath = storage_path('app/public/import/' . $fileName);

        if (($handle = fopen($filePath, "r")) !== false) {
            // Get rid of the first row of the file as the header
            $header = fgetcsv($handle, 0, ',');
            //dd($header);
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $line++;
                $user = array();
                list(
                    $user['id'],
                    $user['name'],
                    $user['email'],
                ) = $data;

                //dd($data);

                $csv_errors = Validator::make(
                    $user,
                    (new User)->rules()
                )->errors();

                //dd($csv_errors);
                if ($csv_errors->any()) {
                    $errs[] = array(
                        'line' => $line,
                        'csv_errors' => $csv_errors
                    );
                }
            }
            dd($errs);
            return $errs;
            fclose($handle);
        }
    }

    // very fast 
    public function export()
    {
        //dispatch(new ExportJob());

        $users = User::all();

        // create csv file in mempory
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        // insert header 
        $header = \Schema::getColumnListing('users');
        //dd($header);
        $csv->insertOne($header);

        // insert rows 
        foreach ($users as $user) {
            $csv->insertOne($user->toArray());
        }

        // output
        $csv->output('exportUser.csv');
    }


    // import excel with specific cell 
    public function importExcelView()
    {
        return view('importExcel');
    }

    public function importExcel()
    {
        if (request()->hasFile('file')) {
            try {
                Excel::import(new ExcelImport, request()->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();

                return back()->with('failures', $failures);
            }
            return back()->with('success', 'upload successfully!');
        }
    }
}
