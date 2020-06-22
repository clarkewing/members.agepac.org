<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aircraft')->insert([
            ['name' => 'SEP (land)'],
            ['name' => 'SEP (sea)'],
            ['name' => 'SET'],
            ['name' => 'MEP (land)'],
            ['name' => 'MEP (sea)'],
            ['name' => 'TMG'],
            ['name' => 'Airbus A300'],
            ['name' => 'Airbus A310/300-600'],
            ['name' => 'Airbus A300-600ST'],
            ['name' => 'Airbus A32F'],
            ['name' => 'Airbus A330/340'],
            ['name' => 'Airbus A330/350'],
            ['name' => 'Airbus A380'],
            ['name' => 'Airbus A400M'],
            ['name' => 'ATR42/72'],
            ['name' => 'AVRO RJ Bae146'],
            ['name' => 'Bae ATP Jetstream 61'],
            ['name' => 'Boeing 707/720'],
            ['name' => 'Boeing 717'],
            ['name' => 'Boeing 727'],
            ['name' => 'Boeing 737 100-200'],
            ['name' => 'Boeing 737 300-500'],
            ['name' => 'Boeing 737 600-900'],
            ['name' => 'Boeing 747 100-300'],
            ['name' => 'Boeing 747-400'],
            ['name' => 'Boeing 757/767'],
            ['name' => 'Boeing 777/787'],
            ['name' => 'Bombardier CRJ 100-200'],
            ['name' => 'Bombardier CRJ 700-900'],
            ['name' => 'Bombardier CRJ 1000'],
            ['name' => 'Bombardier DHC8-100-300'],
            ['name' => 'Bombardier DHC8 Q400'],
            ['name' => 'Cessna SET'],
            ['name' => 'Cessna 501/551'],
            ['name' => 'Cessna 510'],
            ['name' => 'Cessna 525'],
            ['name' => 'Cessna 500/550/560'],
            ['name' => 'Cessna 560XL/XLS'],
            ['name' => 'Cessna 650'],
            ['name' => 'Cessna 680'],
            ['name' => 'Cessna 750'],
            ['name' => 'Concorde'],
            ['name' => 'Dassault Falcon 10/100'],
            ['name' => 'Dassault Falcon 20/200'],
            ['name' => 'Dassault Falcon 2000'],
            ['name' => 'Dassault Falcon 2000/2000EX'],
            ['name' => 'Dassault Falcon 2000EX EASy'],
            ['name' => 'Dassault Falcon 50/900'],
            ['name' => 'Dassault Falcon 7X'],
            ['name' => 'Dassault Falcon 900EX EASy'],
            ['name' => 'Embraer 110'],
            ['name' => 'Embraer 120'],
            ['name' => 'Embraer 135/145'],
            ['name' => 'Embraer ERJ 170/175/190/195'],
            ['name' => 'Gulfstream I'],
            ['name' => 'Gulfstream II/III'],
            ['name' => 'Gulfstream IV'],
            ['name' => 'Gulfstream V'],
            ['name' => 'Gulfstream VI'],
            ['name' => 'Gulfstream VII'],
            ['name' => 'Hawker Beechcraft BE-90/99/100/200'],
            ['name' => 'Hawker Beechcraft BE300/1900'],
            ['name' => 'Learjet 20/30'],
            ['name' => 'Learjet 45/75'],
            ['name' => 'Learjet 55'],
            ['name' => 'Learjet 60'],
            ['name' => 'McDonnell Douglas DC3'],
            ['name' => 'McDonnell Douglas DC10'],
            ['name' => 'McDonnell Douglas MD11'],
        ]);
    }
}
