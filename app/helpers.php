<?php
 
 use App\Models\User;
 use App\Models\Attendance; 
 use App\Models\Config; 

 use Carbon\Carbon;

function active_class($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function is_active_route($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function show_class($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}


// get full status salary
function getFullStatusAttendance($user_id, $fromDate, $toDate){

  $attendance = Attendance::whereBetween('due_date',[$fromDate, $toDate])
            ->where('user_id',$user_id)
            ->where('status','Full')
            ->selectRaw("sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) ) ) as 'total_working_seconds',
                        SEC_TO_TIME(sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) )) ) as 'total_working_hours',
                        sum(is_late) as total_late, count(id) as totalDays")
            ->first();

    return $attendance;
}

function getReducedStatusAttendance($user, $fromDate, $toDate){

  $reducedAttendance = Attendance::whereBetween('due_date',[$fromDate, $toDate])
            ->where('user_id',$user->id)
            ->where('status','Reduced')
            ->get();
  $data = [];
 
  $salary = 0;
  $total_reduced_working_hours = 0;
  $reduced_working_days = 0;
  $reduced_late = 0;
  foreach($reducedAttendance as $attendance){

    $start_time = Carbon::createFromFormat('H:i:s', $attendance->check_in);
    $end_time = Carbon::createFromFormat('H:i:s', $attendance->check_out);


    
    $working_minutes = $end_time->diffInMinutes($start_time);
    $working_seconds = $end_time->diffInSeconds($start_time);
    $working_hours = $working_minutes / 60;

    $config = Config::first();
    $office_start_time = Carbon::createFromFormat('H:i:s', $config->start_time);
    $office_end_time = Carbon::createFromFormat('H:i:s', $config->end_time);
    $office_working_minutes = $office_end_time->diffInMinutes($office_start_time);
    $office_working_hours = $office_working_minutes / 60;
   

    $total_reduced_working_hours += $working_hours;
    $reduced_working_days += $working_seconds / (9 * 60 * 60);
    
    $perDaySaary = number_format((float)$user->basic_salary / getDaysFromDateRange($fromDate,$toDate), 2, '.', '');


    if($working_minutes >= $office_working_minutes){
        $salary += $perDaySaary; 
    }else{
        $hourlySalaryRate = $perDaySaary / $office_working_hours;
        $salary = $salary + ($working_hours * $hourlySalaryRate);
    }
 

    $reduced_late += $attendance->is_late;

  }

  $data['reduced_salary'] = number_format((float)$salary ?? 0.00, 2, '.', '');
  $data['total_reduced_working_hours'] = number_format((float)$total_reduced_working_hours ?? 0.00, 2, '.', '');
  $data['reduced_working_days'] = number_format((float)$reduced_working_days?? 0.00, 2, '.', '');
  $data['reduced_late'] = $reduced_late;

  
  return $data;
}

function getDaysFromDateRange($from,$to){
   // get days from date range picker
   $start_date = Carbon::parse($from);
   $end_date = Carbon::parse($to);
   $selectionDays = $start_date->diffInDays($end_date) + 1;
   return $selectionDays;
}