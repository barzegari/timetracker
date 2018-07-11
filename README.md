# Simple Time Tracker

Simple Time Tracker package with Lumen micro-framework

## Install


1. Install laravel lumen:
```
$ composer create-project --prefer-dist laravel/lumen project_dir
```


2. Copy this package to :

```
project_dir/packages/barzegari/timetracker
```


3. Add to  project_dir/composer.json

```
"repositories": [
    {
        "type": "path",
        "url": "packages/barzegari/timetracker",
        "options": {
            "symlink": true
        }
    }
],
 "require": {
    ...
    "barzegari/timetracker": "dev-master"
},
```


4. Run command in project_dir
```
 $ composer update
```


5. Add Service Provider in project_dir/bootstrap/app.php 
```
$app->register(Barzegari\Timetracker\TimetrackerServiceProvider::class);
```


6. Uncomment this lines in project_dir/bootstrap/app.php

```
$app->withFacades();

$app->withEloquent();
```


7. Comment routes in :
```
project_dir/app/Http/routes.php
```


8. Create database OR use your own database by set config in .ENV file:
```
CREATE DATABASE timetracker;
create user timetracker;
grant all on timetracker.* to 'timetracker'@'localhost' identified by 'db password';
grant all on timetracker.* to 'timetracker'@'%' identified by 'db password';
FLUSH PRIVILEGES;
```


10. Migrate tables
```
$ php artisan migrate
```


11. Seed database (Optional)
```bash
$ php artisan db:seed --class=Barzegari\Timetracker\Seeder\TimetrackerSeeder
```


## Default Url Routes

1. login  (set start time)
```
POST http://yourdomain.com/api/timetracker/login
{
    "employee_no": "1001",  
    "time": "2018-07-12 14:45:00", 
}
``` 


2. logout (set end time)
```
POST http://yourdomain.com/api/timetracker/logout
{
    "employee_no": "1001",  
    "time": "2018-07-12 14:45:00", 
}
``` 


3. bulk   (upload bulk timesheet in csv format with ',' seperator)
```
POST http://yourdomain.com/api/timetracker/bulk
{
    "employee_no": "1001",  
    "timesheet": "file", # .csv file  
}
``` 


4. Project Hours (to get all spending hours on specific project)
```
GET http://yourdomain.com/api/timetracker/projects/PRJ-1/hours
{
}
``` 


5. Peak Time  (to get peack time of specific project in selected day)
```
POST http://yourdomain.com/api/timetracker/peaktime
{
    "project_code": "1001",  
    "date": "2018-07-12", 
}
``` 
For all requests
```
Response:
400 (Invalid Data)
200 (Successful)
```


## Demo Url
