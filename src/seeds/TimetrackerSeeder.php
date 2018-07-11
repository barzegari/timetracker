<?php
use Barzegari\Timetracker;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Barzegari\Timetracker\Model\User;
use Barzegari\Timetracker\Model\Project;


class TimetrackerSeeder extends Seeder
{
    /**
     * Run the sample data for all table seeds.
     * run: php artisan db:seed --class=Barzegari\Timetracker\Seeder\TimetrackerSeeder
     * @return void
     */
    public function run()
    {

        DB::table('projects')->delete();

        $projects = array(
            [
                'code'		 	=> 'PRJ-1',
                'name'          => 'Digital Signage System',
                'description' 	=> 'Develope Digital Signage System for None Company',
            ],
            [
                'code'		 	=> 'PRJ-2',
                'name'          => 'Time Tracker system',
                'description' 	=> 'Develope Time Tracker System for None Company',
            ],
        );


        foreach ($projects as $project)
        {
            Project::create($project);
        }

        DB::table('users')->delete();

        $users = array(
            [
                'name'		 	=> 'hossein',
                'employee_no'   =>  1001,
                'email' 		=> 'barzegari.ir@gmail.com',
                'password' 		=>  Hash::make('secret'),
                'project_id'    => DB::table('projects')->select('id')->first(),
            ],
            [
                'name'		 	=> 'philipp',
                'employee_no'   =>  1002,
                'email' 		=> 'philipp.derksen@mediaopt.de',
                'password' 		=> Hash::make('secret'),
                'project_id'    => DB::table('projects')->select('id')->first(),
            ],
        );


        foreach ($users as $user)
        {
            User::create($user);
        }

    }
}