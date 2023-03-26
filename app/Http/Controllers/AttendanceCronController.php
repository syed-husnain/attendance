<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
class AttendanceCronController extends Controller
{
    public function index(){
        $users = User::where('role', '!=', 'Admin')->where('status',1)->get();

        $due_date     =  Carbon::today()->toDateString();
        $current_time =  Carbon::now()->format('H:i');


        foreach( $users as $user ){

            $attendance =  Attendance::where('user_id',$user->id)->where('due_date',$due_date)->first();
            if($attendance){

                if($attendance->check_out == null && $attendance->status != 'Absent'){
                    $attendance->update(['status' => 'Reduced']);
                }

            }
            else{

                $today = Carbon::today();

                if (!($today->isSaturday()) && !($today->isSunday()) ) {
                    $attendance = Attendance::create([
                        'user_id'   =>    $user->id,
                        'due_date'  =>    Carbon::today()->toDateString(),
                        'check_in'  =>    null,
                        'check_out' =>    null,
                        'status'    =>    'Absent'
                    ]);
                }


            }

        }

    }
}
