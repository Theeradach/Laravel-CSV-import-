<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use League\Csv\Reader;
use App\User;
use File;

class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        logger('In construct file name :' . $filename);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger('get here!!');

        $csv = Reader::createFromPath(storage_path('app/public/import/' . $this->filename), 'r');
        logger($csv);

        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {

            User::updateOrCreate([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => $row['password'],
                'remember_token' => $row['remember_token'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ]);
        }

        File::delete(storage_path('app/public/import/' . $this->filename));
    }
}
