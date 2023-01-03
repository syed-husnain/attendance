<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\User;
use App\Models\Config;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;


use URL;

use DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('role','!=', 'Admin')->where('status',1)->get();
        if ($request->ajax()) {
            $data = Attendance::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function($row){     
                        return $row->user->name;
                    })
                    ->addColumn('due_date', function($row){     
                        return date('d M Y', strtotime($row->due_date));
                    })
                    ->addColumn('check_in', function($row){     
                        return date('h:i a', strtotime($row->check_in));
                    })
                    ->addColumn('check_out', function($row){     
                        return date('h:i a', strtotime($row->check_out));
                    })
                    ->addColumn('status', function($row){     
                       
                        $status = '<a href="javascript:void(0)" class="badge bg-success">'.$row->status.'</a>';
                        return $status;
                    })
                    ->addColumn('action', function($row){
                        $btn = ' <a href="' . URL::route('attendance.edit', $row->id) . '" class="edit btn btn-primary btn-xs">Edit</a>';
                        
                        $btn .= ' <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          '.$row->status.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                        if($row->status == 'Start'){
                          $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                          <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                          <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                          <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>
                          <a class="dropdown-item" href="#" onclick="changeStatus(\'Holiday\', '.$row->id.')">Holiday</a>';
                        }
                        else if($row->status == 'Full'){
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Holiday\', '.$row->id.')">Holiday</a>';
                        }
                        else if($row->status == 'Reduced'){
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Holiday\', '.$row->id.')">Holiday</a>';
                        }
                        else if($row->status == 'Absent'){
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Holiday\', '.$row->id.')">Holiday</a>';
                        }
                        else if($row->status == 'Leave'){
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Holiday\', '.$row->id.')">Holiday</a>';
                        }
                        else if($row->status == 'Holiday'){
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>';
                        }
                        else{
                            $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Start\', '.$row->id.')">Start</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Full\', '.$row->id.')">Full</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Reduced\', '.$row->id.')">Reduced</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Leave\', '.$row->id.')">Leave</a>
                            <a class="dropdown-item" href="#" onclick="changeStatus(\'Absent\', '.$row->id.')">Absent</a>

                            ';
                        }



                        $btn .='</div>
                      </div>';

                        return $btn;
                    })
                    ->filter(function ($instance) use ($request) {

                        if ($request->has('user') && !empty($request->get('user'))) {
                            $instance->where('user_id', $request->user);
                        }
    
                        if ($request->has('merchant_type') && !empty($request->get('merchant_type'))) {
                            $instance->whereHas('merchant', function ($q) use ($request) {
                                $q->where('user_type', $request->merchant_type);
                            });
                        }
                        
                        if ($request->get('date_type')) {
                            if ($request->get('date_type') == '1') {
                                $instance->whereDate('created_at', Carbon::today());
                            }
                            if ($request->get('date_type') == '2') {
                                $instance->whereDate('created_at', Carbon::yesterday());
                            }
                            if ($request->get('date_type') == '3') {
                                $date = Carbon::now()->subDays(7);
                                $instance->where('created_at', '>=', $date);
                            }
                            if ($request->get('date_type') == '4') {
                                $date = Carbon::now()->subDays(30);
                                $instance->where('created_at', '>=', $date);
                            }
                            if ($request->date_type == 5) {
                                if ($request->get('custom_date')) {
                                    $date = date("Y-m-d", strtotime($request->get('custom_date')));
                                    $instance->whereDate('due_date', $date);
                                }
                            }
                            if ($request->date_type == 6) {
                                if ($request->get('date_range')) {
                                    $dateRange = explode(' - ', $request->date_range);
                                    $from = date("Y-m-d", strtotime($dateRange[0]));
                                    $to = date("Y-m-d", strtotime($dateRange[1]));
                                    $instance->whereBetween('due_date', [$from, $to]);
                                }
                            }
                        }
    
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($query) use ($request) {
                                $search = $request->get('search');
    
                                $query->orWhereHas('user', function ($q) use ($search) {
                                    $q->where('name', 'LIKE', "%$search%");
                                });
                                $query->orWhere('id', 'LIKE', "%$search%")
                                    ->orWhere('status', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['action','status','name','due_date','check_in','check_out'])
                    ->make(true);
        }
        return view('pages.attendances.index')->with([
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('role','!=', 'Admin')->where('status',1)->get();
        return view('pages.attendances.create')->with([
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAttendanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttendanceRequest $request)
    {
        $request['due_date'] = Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
        $alreadyExsist = Attendance::where('user_id',$request->user_id)->where('due_date', $request['due_date'])->first();
        if($alreadyExsist){
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "User have already marked attendance on same day! Try again ",
            ]);
        }
        else{
            $attendance = Attendance::create($request->all());
            if($attendance)
            {
                return response()->json([
                    'status_code' => Response::HTTP_OK,
                    'success'     => TRUE,
                    'error'       => FALSE,
                    'data'        => [],
                    'message'     => 'Created Successfully']);
            }
            else{
                return response()->json([
                    'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'success'       => FALSE,
                    'error'         => TRUE,
                    'data'          => [],
                    'message'       => "doesn't Created! Try again ",
                ]);
            }
        }
        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {   
        $users = User::where('role','!=', 'Admin')->where('status',1)->get();
        return View('pages.attendances.edit')
            ->with('attendance', $attendance)
            ->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAttendanceRequest  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $request['due_date'] = Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
        $alreadyExsist = Attendance::where('user_id',$request->user_id)->where('due_date', $request['due_date'])->where('id','!=', $attendance->id)->first();
        if($alreadyExsist){
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "User have already marked attendance on same day! Try again ",
            ]);
        }
        else{
            $attendance->update($request->all());
            if($attendance)
            {
                return response()->json([
                    'status_code' => Response::HTTP_OK,
                    'success'     => TRUE,
                    'error'       => FALSE,
                    'data'        => [],
                    'message'     => 'Updated Successfully']);
            }
            else{
                return response()->json([
                    'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'success'       => FALSE,
                    'error'         => TRUE,
                    'data'          => [],
                    'message'       => "doesn't Updated! Try again ",
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
    public function status(Request $request)
    {
        $attendance = Attendance::find($request->id);
        if($attendance)
        {
            
            $attendance->update(['status' => $request->status]);

            return response()->json([
                'status_code' => Response::HTTP_OK,
                'success'     => TRUE,
                'error'       => FALSE,
                'data'        => [],
                'message'     => 'Status Change Successfully']);
        }
        else{
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "doesn't Change Status! Try again ",
            ]);
        }
    }
}
