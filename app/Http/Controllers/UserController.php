<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use DataTables;
use URL;
use Carbon\Carbon;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*')->where('role','!=','Admin');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '<a href="' . URL::route('user.edit', $row->id) . '" class="edit btn btn-primary btn-xs">Edit</a>';
                           $btn .= ' <a href="javascript:void(0)" onclick="deleteUser(' . $row->id . ')" class="edit btn btn-danger btn-xs">Delete</a>';
                           return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('pages.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $request['password']        = Hash::make('12345678');
        $request['dob']             = Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d');
        $request['member_since']    = Carbon::createFromFormat('d/m/Y', $request->member_since)->format('Y-m-d');
        $request['role']            = 'Employee';
        
        $user = User::create($request->all());
        if($user)
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
        $user = User::find($id);

        return View('pages.users.edit')
            ->with('user', $user)
            ->with('id',$id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user )
    {
        $request['dob']             = Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d');
        $request['member_since']    = Carbon::createFromFormat('d/m/Y', $request->member_since)->format('Y-m-d');
        
        $data = $user->update($request->all());
       
        if($data)
        {
            return response()->json([
                'status_code' => Response::HTTP_OK,
                'success'     => TRUE,
                'error'       => FALSE,
                'data'        => [],
                'message'     => 'Update Successfully']);
        }
        else{
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "doesn't Update! Try again ",
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id)->delete();
        if($user)
        {
            return response()->json([
                'status_code' => Response::HTTP_OK,
                'success'     => TRUE,
                'error'       => FALSE,
                'data'        => [],
                'message'     => 'Deleted Successfully']);
        }
        else{
            return response()->json([
                'status_code'   => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'       => FALSE,
                'error'         => TRUE,
                'data'          => [],
                'message'       => "doesn't Deleted! Try again ",
            ]);
        }
    }
}
