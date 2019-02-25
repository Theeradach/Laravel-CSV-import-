<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use League\Csv\Writer;
use SplTempFileObject;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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
        $csv->output(storage_path('app/public/export/exportUser.csv'));
    }
}
