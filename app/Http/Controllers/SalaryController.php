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
class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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


            $working_days = $attendance->total_working_seconds / (8 * 60 * 60); // according to 8 working hours

            $salary = ($user->basic_salary / 30) * $working_days;
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'basic_salary' => $user->basic_salary,
                'working_hours' => $attendance->total_working_hours,
                'working_days' => number_format((float)$working_days, 2, '.', ''),
                'total_late' => $attendance->total_late,
                'salary' => number_format((float)$salary, 2, '.', '')
            ]);
            // $instance->whereBetween('created_at', [$from, $to]);
        }
    }
}
