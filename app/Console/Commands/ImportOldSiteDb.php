<?php

namespace App\Console\Commands;

use App\Imports\UsersImport;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class ImportOldSiteDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old-site:import-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from the old site from the `database/old-site.sql` file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Model::unguard();

        $this->output->title('Importing Users');

        $this->line('Importing Users - Basic info');
        (new UsersImport)->withOutput($this->output)
            ->import('agepacprzeforum/agepacprzeforum_table_user.csv');

        $this->info('Users imported!');

        Model::reguard();

        $this->output->success('Import successful');
    }
}
