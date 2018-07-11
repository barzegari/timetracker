<?php

/*
|--------------------------------------------------------------------------
| Package Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an Package.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app = app();

Route::Group(['prefix'=>'api/timetracker'], function(){


    Route::get('/', function () {
        return 'Time Tracker';
    });

    Route::post('/login',                        'Barzegari\Timetracker\\Controllers\\TimetrackerController@login');
    Route::post('/logout',                       'Barzegari\Timetracker\\Controllers\\TimetrackerController@logout');
    Route::post('/bulk',                         'Barzegari\Timetracker\\Controllers\\TimetrackerController@bulk');
    Route::get ('/projects/{projectCode}/hours', 'Barzegari\Timetracker\\Controllers\\TimetrackerController@getSpendingHoursOnProject');
    Route::post('/peaktime',                     'Barzegari\Timetracker\\Controllers\\TimetrackerController@peaktime');

});
