<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Config;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

use DB;
use Carbon\Carbon;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($num = 10, $last = NULL)
    {
        $data = User::select('id','prefix as user_id','name','email','cnic','phone','dob','designation','member_since','role')->where('role','!=','Admin')->paginate(10);
        if(!empty($data)){

            $status_code = Response::HTTP_OK;
            $success     = TRUE;
            $error       = FALSE;
            $data        = $data;
            // $rows        = $total;
            $message     = 'User Data Loaded Successfully';

        }else{

            $status_code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $success     = TRUE;
            $error       = FALSE;
            $data        = [];
            $rows        = '';
            $message     = 'User Data does not exists';            
        }
        return response()->json([
            'status'    => $status_code,
            'success'   => $success,
            'error'     => $error,
            'data'      => $data,
            // 'rows'      => $rows,
            'message'   => $message,
            ]);
    }
    public function attendanceMark(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id'         => 'required',

        ]);
        if ($validator->fails()) {
            $response = [
                "status"     => Response::HTTP_UNPROCESSABLE_ENTITY,
                "success"    => false,
                'error'      => true,
                "message"    => "validation error",
                "data"       => $validator->errors()->messages(),
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);

        }
        else{
            $user = User::where('role', '!=', 'Admin')->where('id',(int)$request->user_id)->first();
            if($user){

               $due_date     =  Carbon::today()->toDateString();
               $current_time =  Carbon::now()->format('H:i');

               $config = Config::first();
            
               if(!$config){
                    return response()->json([
                        'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'success'   => FALSE,
                        'error'     => TRUE,
                        'message'   => 'Please Contact Administration. First add office in or out time.'
                    ]);
               }

                $checkExsist = Attendance::where('user_id',$user->id)->where('due_date',$due_date)->first();
            
                if($checkExsist){

                    if($checkExsist->check_in != null && $checkExsist->check_out != null)
                    {
                        // when user already check in or check out
                        return response()->json([
                            'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                            'success'   => FALSE,
                            'error'     => TRUE,
                            'message'   => 'Already Marked Attendace'
                        ]);
                    }

                    // if exsit then reocrd update
                    $to = Carbon::createFromFormat('H:s', date('H:i', strtotime( $checkExsist->check_in )));
                    $from = Carbon::createFromFormat('H:s', $current_time);
                    $diff_in_hours = $to->diffInHours($from);
                    // dd($config->start_time);

                    $officeTo = Carbon::createFromFormat('H:s', date('H:i', strtotime($config->start_time )));
                    $officeFrom = Carbon::createFromFormat('H:s', date('H:i', strtotime( $config->end_time )));
                    $office_diff_in_hours = $officeTo->diffInHours($officeFrom);


                    $office_end_time = date('H:i', strtotime( $config->end_time ));
                    if($current_time <= $office_end_time || $current_time >= $office_end_time)
                    {   
                        $status = 'Full';
                    }
                    if($diff_in_hours < $office_diff_in_hours)
                    {
                        $status = 'Reduced';
                    }
                    $data = [
                        'user_id'   => $user->id,
                        'due_date'  => $due_date,
                        'check_out' => $current_time,
                        'status'    => $status
                    ];
                    $record = $checkExsist->update($data);
                    if($record){

                        $status_code = Response::HTTP_OK;
                        $success     = TRUE;
                        $error       = FALSE;
                        $message     = 'You have signed out successfully';
                    }

                }else{
                    // if not exsist then new row created
                    
                    $office_start_time = date('H:i', strtotime( $config->start_time ));
                    $status = '';
                    if($current_time <= $office_start_time || $current_time >= $office_start_time)
                    {   
                        $status = 'Start';
                    }
                    $data = [
                        'user_id'   => $user->id,
                        'due_date'  => $due_date,
                        'check_in'  => $current_time,
                        'status'    => $status
                    ];


                    $officeTo = Carbon::createFromFormat('H:s', date('H:i', strtotime($config->start_time )));
                    $currentFrom = Carbon::createFromFormat('H:s', date('H:i', strtotime($current_time )));
                    $time_diff_in_minute = $officeTo->diffInMinutes($currentFrom);

                    if($time_diff_in_minute > $config->leverage_time){
                        $data['is_late'] = 1;
                    }
                    $record = Attendance::create($data);
                    if($record){

                        $status_code = Response::HTTP_OK;
                        $success     = TRUE;
                        $error       = FALSE;
                        $message     = 'You have signed in successfully';
                    }
                }
                return response()->json([
                    'status'    => $status_code,
                    'success'   => $success,
                    'error'     => $error,
                    // 'rows'      => $rows,
                    'message'   => $message,
                ]);
            }else{
                return response()->json([
                    'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'success'   => FALSE,
                    'error'     => TRUE,
                    // 'rows'      => $rows,
                    'message'   => 'Something went wrong, User not found.',
                ]);
            }
        
           
        }
       
    }
    public function attendanceHistory($id = null){
    
        $data = User::with('attendances')
        ->where('role','!=','Admin')
        ->when($id, function ($query) use ($id){
            $query->where('id',$id);
        })
        ->select('id','prefix as user_id','name','email','cnic','phone','dob','designation','member_since','role',
        \DB::raw('(CASE 
                        WHEN users.status = "0" THEN "Inactive" 
                        ELSE "Active" 
                        END) AS status'))
        ->get();
        
        if(!empty($data) && count($data) > 0){
    
            $status_code = Response::HTTP_OK;
            $success     = TRUE;
            $error       = FALSE;
            $data        = $data;
            // $rows        = $total;
            $message     = 'Data Loaded Successfully';
            if($id)
            {
           
                if($data[0]->status == 'Inactive'){
                    $status_code = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $success     = TRUE;
                    $error       = FALSE;
                    $data        = [];
                    $rows        = '';
                    $message     = 'User is Inactive';    
                }
            }

        }
        else{

            $status_code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $success     = TRUE;
            $error       = FALSE;
            $data        = [];
            $rows        = '';
            $message     = 'Data does not exists';            
        }
        return response()->json([
            'status'    => $status_code,
            'success'   => $success,
            'error'     => $error,
            'data'      => $data,
            // 'rows'      => $rows,
            'message'   => $message,
            ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
