<?php

namespace App\Console\Commands;

use App\Imports\CompaniesImport;
use App\Imports\CompanyCommentsImport;
use App\Imports\CoursesImport;
use App\Imports\OccupationsImport;
use App\Imports\ProfileInfoImport;
use App\Imports\SubscriptionsImport;
use App\Imports\UserFieldsImport;
use App\Imports\UsersImport;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class ImportLegacyDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:import-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from the legacy site DB CSV exports.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Model::unguard();

        $this->section('Users', $this->importUsers());

        $this->section('Companies', $this->importCompanies());

        $this->section('Profiles', $this->importProfiles());

        Model::reguard();

        $this->output->success('Import successful');
    }

    /**
     * Import Users.
     *
     * @return \Closure
     */
    protected function importUsers(): \Closure
    {
        return function () {
            $this->line('Importing Users - Basic info');
            (new UsersImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_user.csv');

            $this->line('Importing Users - Additional fields');
            (new UserFieldsImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_userfield.csv');

            //        $this->line('Importing Users - Subscriptions');
            //        (new SubscriptionsImport)->withOutput($this->output)
            //            ->import('agepacprzeforum/agepacprzeforum_table_u_cotisation.csv');
        };
    }

    /**
     * Import Companies.
     *
     * @return \Closure
     */
    protected function importCompanies(): \Closure
    {
        return function () {
            $this->line('Importing Companies - Basic info');
            (new CompaniesImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_c_airline.csv');

            $this->line('Importing Companies - Comments');
            (new CompanyCommentsImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_c_comment.csv');
        };
    }

    /**
     * Import Profiles.
     *
     * @return \Closure
     */
    protected function importProfiles(): \Closure
    {
        return function () {
            $this->line('Importing Profiles - Bio and flight hours');
            (new ProfileInfoImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_u_parcours.csv');

            $this->line('Importing Profiles - Courses');
            (new CoursesImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_u_formation.csv');

            $this->line('Importing Profiles - Occupations');
            (new OccupationsImport)->withOutput($this->output)
                ->import('agepacprzeforum/agepacprzeforum_table_u_emploi.csv');
        };
    }

    /**
     * Define an import section.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    protected function section(string $name, callable $callback): void
    {
        $this->output->title("Importing $name");

        call_user_func($callback);

        $this->info("$name imported!");
    }
}
