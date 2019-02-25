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
            $errs = $csv_errors[1];
            $err_line = $csv_errors[0];

            //dd($csv_errors);
            if ($errs->any()) {
                return redirect()->back()
                    ->withErrors($errs, 'import')
                    ->with('error_line', $err_line);
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
                $errs = array();
                array_push($errs, $line);
                array_push($errs, $csv_errors);
                //dd($errs[1]);

                return $errs;
            }
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
}
