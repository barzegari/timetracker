<?php

namespace Barzegari\Timetracker\Controllers;

use App\Http\Controllers\Controller;
use Barzegari\Timetracker\Model\Timelog;
use Illuminate\Http\Request;
use Barzegari\Timetracker\Model\User;
use Barzegari\Timetracker\Model\Project;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Array_;

class TimetrackerController extends Controller
{

    /**
     * @return  time
     *
     */
    public function index()
    {
        ///TODO show api usage on response
        return time();
    }

    /**
     * @param string    employee_no
     * @param datetime  startdate
     *
     * @return result
     */
    public function login(Request $request)
    {
        ///TODO: should check if there is one record without ending, do not insert.

        $this->validate($request, [
            'time' => 'required|date',
            'employee_no' => 'required|string',
        ]);

        $strTime   = $request->input('time');

        $from=$this->convertToDatetime($strTime);

        if(!$from)
            return $this->message('ERR','Invalid Time Format!') ;

        $employeeNo = $request->input('employee_no');
        $user=User::getUserByEmployeeNo($employeeNo);
        if(!$user)
            return $this->message('ERR','Invalid Data!') ;

        if(Timelog::where('user_id',$user->id)->wherenull('to_date')->exists())
            return $this->message('ERR','There is an other starting record whitout ending!') ;

        $timelog             = new Timelog;
        $timelog->project_id = $user->project_id;
        $timelog->user_id    = $user->id;
        $timelog->from_date  = $from;
        $timelog->save();

        return $this->message('OK','Insert Successful') ;
    }

    /**
     * @param string    employee_no
     * @param datetime  end date
     *
     * @return result
     */
    public function logout(Request $request)
    {
        ///TODO: should check if there is not login record  do not update.
        $this->validate($request, [
            'time' => 'required|date',
            'employee_no' => 'required|string',
        ]);

        $strTime   = $request->input('time');

        $to=$this->convertToDatetime($strTime);
        if(!$to)
            return $this->message('ERR','Invalid Time Format!') ;

        $employeeNo = $request->input('employee_no');
        $user=User::getUserByEmployeeNo($employeeNo);

        if(!$user)
            return $this->message('ERR','Invalid Data!') ;

        $timelog = Timelog::where('user_id',$user->id)
                          ->where('project_id',$user->project_id)
                          ->wherenull('to_date');

        if(!$timelog->count())
            return $this->message('ERR','Does not Exist Starting Time') ;

        ///TODO check if to date in greate than from date
        $timelog->update(['to_date'=>$to]);

        return $this->message('OK','Update Successful') ;

    }

    /**
     *  @param   string     project code
     *  @return  string     spend hours
     */
    public function getSpendingHoursOnProject($projectCode)
    {
        $project = Project::where('code',$projectCode)->first();

        if(!$project)
            return $this->message('ERR','Project does not Exist!') ;

        $result = Timelog::where('project_id',$project->id)
                        ->wherenotnull('to_date')
                        ->selectRaw(' time(sum(TIMEDIFF( to_date, from_date ))) as total')
                        ->first();

        $total=($result->total)?$result->total:0;

        return $this->message('OK',$total) ;
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function bulk(Request $request)
    {
        $this->validate($request, [
            'timesheet' => 'required|file',
            'employee_no' => 'required|string',
        ]);

        $employeeNo = $request->input('employee_no');
        $user=User::getUserByEmployeeNo($employeeNo);
        if(!$user)
            return $this->message('ERR','Invalid Data!') ;

        ///TODO Vlidate File Type AND Content

        if(!$request->hasFile('timesheet'))
            $this->message('ERR','File Invalid');

        $path = $request->file('timesheet')->getRealPath();
        $file = fopen($path,"r");

        $inserted=0;
        $error=Array();

        while ( ($data = fgetcsv($file, 1000, ",")) !==FALSE )
        {

            $from_date =$this->convertToDatetime($data[0]);
            $to_date   =$this->convertToDatetime($data[1]);

            if($from_date&&$to_date){
                Timelog::create(['user_id'   =>$user->id,
                                'project_id '=>$user->project_id,
                                'from_date'  => $from_date ,
                                'to_date'    => $to_date ,
                                'description'=>'Import from csv file']);
                $inserted++;

            }else{
                $error[]=['fromDate'=>$data[0],'toDate'=>$data[1]];
            }
        }

        fclose($file);

        if($inserted)
            return $this->message('OK',['insertedCount'=>$inserted,'faildRecord'=>$error]) ;
        else
            return $this->message('ERR',['No record inserted!','faildRecord'=>$error]) ;

    }

    /**
     * @param  Request $request
     * @return Json    peack time period in a day
     */
    public function peakTime(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|date',
            'project_code' => 'required|string',
        ]);

        $projectCode   = $request->input('project_code');
        $project = Project::where('code',$projectCode)->first();
        if(!$project)
            return $this->message('ERR','Project does not Exist!') ;

        $date   = $request->input('date');
        try{

            $startDay= Carbon::createFromFormat('Y-m-d', $date)
                ->startOfDay()
                ->format('Y-m-d H:i');

            $endDay = Carbon::createFromFormat('Y-m-d', $date)
                ->endOfDay()
                ->format('Y-m-d H:i');

        }catch(\Exception $e) {

            ///TODO log Error
            return $this->message('ERR','Invalid Time Format!') ;
        }

        $peakTime = DB::select("SELECT temp.time time, COUNT(1) count from (
                                                SELECT date_format(`from_date`, '%Y-%m-%d %H:%i') time FROM `timelogs`
                                                UNION
                                                SELECT date_format(`to_date`, '%Y-%m-%d %H:%i') time FROM `timelogs`
                                                ) temp
                                                inner join `timelogs` on temp.time between timelogs.`from_date` and `timelogs`.`to_date` 
                                                where project_id= ? 
													  and	date_format (from_date , '%Y-%m-%d %H:%i') < ?
													  and   date_format (to_date, '%Y-%m-%d %H:%i')    > ?
                                                group by temp.time
                                                order by COUNT(1) desc ", [$project->id,$endDay ,$startDay]);

        if(!$peakTime)
            return $this->message('ERR','There is not any log for entired project and date!') ;

        extract((array)$peakTime[0]);

        $result = DB::select(" SELECT max(from_date) startPeak, min(to_date) endPeak, COUNT(1) concurrent 
                          from timelogs where ? BETWEEN from_date and to_date",[$time]);

        extract((array)$result[0]);

        if(!$result)
            return $this->message('ERR','There is not related log!') ;

        return $this->message('OK',(array)$result[0]);
    }

    /**
     *  @param    string     datetime
     *  @return   Datetime
     */
    private function convertToDatetime($stringTime)
    {
        try{
            return Carbon::createFromFormat('Y-m-d H:i:s', $stringTime)->toDateTimeString();
        }catch(\Exception $e){
            ///TODO: Log Error
            return false ;
        }
    }

    /**
     *  @param string message
     *  @param string key
     *  @return Json
     */
    private function message($key,$message)
    {
        return response()->json(['status'=>$key,'message'=>$message], ($key=='OK')?200:400);
    }


}