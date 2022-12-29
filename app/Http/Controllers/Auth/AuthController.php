<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function loginShow(){
        return view('pages.auth.login');
    }

    public function login(Request $request){
    //    dd($request->all());
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('admin.dashboard')
                        ->withSuccess('You have Successfully loggedin');
        }
        return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
    }
    public function logout(){
        Auth::logout();
        return view('pages.auth.login');
    }

    public function changePassword(){
        return view('pages.auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $userid = Auth::id();
        $user = User::find($userid);
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:32',
            'confirm_password'  => 'required|same:new_password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();
        if ((Hash::check(request('current_password'), $user->password)) == true) {
            if ($data['new_password'] == $data['confirm_password']) {
                User::where('id', $userid)->update(['password' => Hash::make($data['new_password'])]);
                return response()->json([
                    'status'    => Response::HTTP_OK,
                    'success'   => True,
                    'error'     => FALSE,
                    'message'   => 'Password Has Been Updated Successfully!'
                ]);

            } else {
                return response()->json([
                    'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'success'   => FALSE,
                    'error'     => TRUE,
                    'message'   => 'New Password & Confirm Password NOT MATCH'
                ]);
            }

        } else {
            return response()->json([
                'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success'   => FALSE,
                'error'     => TRUE,
                'message'   => 'Your Current Password is INCORRECT'
            ]);
        }

    }
}
