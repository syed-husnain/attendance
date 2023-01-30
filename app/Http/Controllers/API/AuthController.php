<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());
        $user = User::create([
            'name' => $request->name,
            'email'  => $request->email,
            'role' => 'Employee',
            'password' => Hash::make($request->password),
        ]);
       $token = $user->createToken('Token')->accessToken;
        $response = [
            "code" => Response::HTTP_OK,
            "success" => true,
            "error" => false,
            "message" => "User register successfully.",
            "data" => $token,

        ];
        return response()->json($response, Response::HTTP_OK);

    }
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email'         => 'required|string|email|max:255',
            'password'      => 'required|string|min:8',

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
                $data = [
                    'email'         => $request->email,
                    'password'      => $request->password
                ];
                if(auth()->attempt($data)){

                   $token =  auth()->user()->createToken('Token')->accessToken;
                   $response = [
                        'status'    => Response::HTTP_OK,
                        'success'   => true,
                        'error'     => false,
                        'message'   => "User login successfully.",
                        'token'     => $token,
                        'data'      => []
                    ];

                    return response()->json($response, Response::HTTP_OK);
                }
                else{
                    return response()->json([
                        'status'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'success'   => false,
                        'error'     => true,
                        'message'   => "Invalid user email or password",
                        'token'     => '',
                        'data'      => []
                    ]);
                }
        }

    }
    public function logout(Request $request) {

        $token      = $request->user()->token();
        $token->revoke();
        $response   = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
