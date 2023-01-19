<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Requests\StoreSalaryRequest;
use App\Http\Requests\UpdateSalaryRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use DataTables;
use URL;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
class SalaryController extends Controller
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
            $data = Salary::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('user_id', function($row){
                       return $row->user->name;
                    })
                    ->addColumn('created_at', function($row){
                        return date('d M Y', strtotime($row->created_at));
                    })
                    // ->addColumn('action', function($row){

                    //        $btn = '<a href="' . URL::route('user.edit', $row->id) . '" class="edit btn btn-primary btn-xs">Edit</a>';
                    //        $btn .= ' <a href="javascript:void(0)" onclick="deleteUser(' . $row->id . ')" class="edit btn btn-danger btn-xs">Delete</a>';
                    //        return $btn;
                    // })
                    ->filter(function ($instance) use ($request) {

                        if ($request->has('user') && !empty($request->get('user'))) {
                            $instance->where('user_id', $request->user);
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
                    ->rawColumns(['action','user_id'])
                    ->make(true);
        }

        return view('pages.salaries.index')->with([
            'users' => $users
        ]);;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('role','!=', 'Admin')->where('status',1)->get();
        return view('pages.salaries.create')->with([
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSalaryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalaryRequest $request)
    {
        $dateRange = explode(' - ', $request->custom_date_range_input);
        $request['from_date']   = date("Y-m-d", strtotime($dateRange[0]));
        $request['to_date']     = date("Y-m-d", strtotime($dateRange[1]));

        $alreadyExsist = Salary::where('user_id',$request->user_id)
                        ->where(\DB::raw('DATE_FORMAT(from_date,"%Y-%m")'), date("Y-m", strtotime($dateRange[0])))
                        ->first();
        if($alreadyExsist){
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "You have already calculate salary in this month.",
            ]);
        }

        $salary = Salary::create($request->all());
        return response()->json([
            'status_code' => Response::HTTP_OK,
            'success'     => TRUE,
            'error'       => FALSE,
            'data'        => [],
            'message'     => 'Created Successfully'
        ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function show(Salary $salary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function edit(Salary $salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSalaryRequest  $request
     * @param  \App\Models\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSalaryRequest $request, Salary $salary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salary $salary)
    {
        //
    }
    public function getWorkingDays(Request $request)
    {
        // dd($request->all());

        if ($request->has('date_range')) {

            $dateRange = explode(' - ', $request->date_range);
            $from = date("Y-m-d", strtotime($dateRange[0]));
            $to = date("Y-m-d", strtotime($dateRange[1]));

            $user = User::where('id', $request->user_id)->first();
            $attendance = Attendance::whereBetween('due_date',[$from, $to])
            ->where('user_id',$request->user_id)
            ->selectRaw("sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) ) ) as 'total_working_seconds',
                        SEC_TO_TIME(sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) )) ) as 'total_working_hours',
                        sum(is_late) as total_late")
            ->first();

            $totalAbsents = Attendance::whereBetween('due_date',[$from, $to])
            ->where('user_id',$request->user_id)
            ->where('status','Absent')
            ->count();


            $working_days = $attendance->total_working_seconds / (8 * 60 * 60); // according to 8 working hours

            $salary = ($user->basic_salary / 30) * $working_days;
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'basic_salary' => $user->basic_salary,
                'working_hours' => $attendance->total_working_hours,
                'working_days' => number_format((float)$working_days, 2, '.', ''),
                'total_late' => $attendance->total_late,
                'total_absent' => $totalAbsents,
                'salary' => number_format((float)$salary, 2, '.', '')
            ]);
            // $instance->whereBetween('created_at', [$from, $to]);
        }
    }
    public function getSalary(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id'               => 'required',
            'travel_allowance'      => 'required|numeric|gt:0',
            'medical_allowance'     => 'required|numeric|gt:0',
            'bonus'                 => 'required|numeric|gt:0',
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


        if ($request->has('custom_date_range')) {

            // calculate attendance working days

            $dateRange      = explode(' - ', $request->custom_date_range);
            $from           = date("Y-m-d", strtotime($dateRange[0]));
            $to             = date("Y-m-d", strtotime($dateRange[1]));

            $user           = User::where('id', $request->user_id)->first();
            $attendance     = Attendance::whereBetween('due_date',[$from, $to])
                                ->where('user_id',$request->user_id)
                                ->selectRaw("sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) ) ) as 'total_working_seconds',
                                            SEC_TO_TIME(sum(TIME_TO_SEC(TIMEDIFF(check_out,check_in) )) ) as 'total_working_hours',
                                            sum(is_late) as total_late")
                                ->first();

            $working_days   = $attendance->total_working_seconds / (8 * 60 * 60); // according to 8 working hours

            // end calculate attendance working days

            //  calculate selected date range saturdays or sundays

            $startDate      = Carbon::parse($from);
            $endDate        = Carbon::parse($to);
            $saturdays      = [];
            $sundays        = [];
            if($working_days > 0){
                while ($startDate->lte($endDate)) {

                    if ($startDate->isSaturday()) {

                        $saturdays[] = $startDate->toDateString();
                    }
                    if($startDate->isSunday()){

                        $sundays[] = $startDate->toDateString();
                    }
                    $startDate->addDay();
                }

                $totalSaturdays = count($saturdays);
                $totalSundays   = count($sundays);


                $salary = ($user->basic_salary / 30) * ($working_days + $totalSaturdays + $totalSundays);


                // late salary deduction calculation
                // $totalLate = $attendance->total_late;


                $totalLate = 3;
                // $count = 0;
                $perDaySalary = number_format((float)$user->basic_salary / 30, 2, '.', '');
                $lateDeductionSalary = 0;
                if($totalLate >= 3){
                    $lateDeductionSalary = round($perDaySalary / 2) * round($totalLate/3);
                }

                $salary = $salary - $lateDeductionSalary;

                $salaryWithAllowance = $salary + ( $request->travel_allowance ?? 0 ) + ( $medical_allowance ?? 0 ) + ( $bonus ?? 0 );


                return response()->json([
                    'status'                => 1,
                    'message'               => 'Success',
                    'salaryWithAllowance'   => number_format((float)$salaryWithAllowance, 2, '.', '')
                ]);
            }else{
                return response()->json([
                    'status'                => 1,
                    'message'               => 'Success',
                    'salaryWithAllowance'   => 0
                ]);
            }

        }
    }

}
