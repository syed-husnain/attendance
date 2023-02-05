<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Config;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
class AttendanceCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attedance:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
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
