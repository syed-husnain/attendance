<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Http\Requests\StoreHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;

use App\Models\User;
use App\Models\Config;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;


use URL;

use DataTables;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Holiday::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('due_date', function($row){     
                        return date('d M Y', strtotime($row->due_date));
                    })
                    ->addColumn('status', function($row){     
                       
                        $status = '<a href="javascript:void(0)" class="badge bg-danger">'.$row->status.'</a>';
                        return $status;
                    })
                    ->addColumn('action', function($row){
                        $btn = ' <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          '.$row->status.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                        if($row->status == 'Holiday'){
                          $btn .= '<a class="dropdown-item" href="#" onclick="changeStatus(\'Remove\', '.$row->id.')">Remove</a>';
                        }
                        $btn .='</div>
                      </div>';

                        return $btn;
                    })
                    ->filter(function ($instance) use ($request) {
    
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($query) use ($request) {
                                $search = $request->get('search');
    
                                $query->orWhere('due_date', 'LIKE', "%$search%")
                                        ->orWhere('id', 'LIKE', "%$search%")
                                        ->orWhere('status', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['action','status','due_date'])
                    ->make(true);
        }
        return view('pages.holidays.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHolidayRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHolidayRequest $request)
    {
        $request['due_date'] = Carbon::createFromFormat('d/m/Y', $request->due_date)->format('Y-m-d');
        $alreadyExsist = Holiday::where('due_date', $request['due_date'])->first();
        if($alreadyExsist){
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "Already marked as Holiday!",
            ]);
        }
        $holiday = Holiday::create($request->all());
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
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHolidayRequest  $request
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHolidayRequest $request, Holiday $holiday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        //
    }
    public function status(Request $request)
    {
        $holiday = Holiday::find($request->id);
        if($holiday)
        {
            $holiday->delete();
            return response()->json([
                'status_code' => Response::HTTP_OK,
                'success'     => TRUE,
                'error'       => FALSE,
                'data'        => [],
                'message'     => 'Removed Successfully']);
        }
    }
}
