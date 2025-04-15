<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request) {
        try {
            $request->validate([
                "email" => "required",
                "password" => "required",
            ]);

            $user = User::where('email', $request->email)->first();

            if($user && Hash::check($request->password, $user->password)) {

                $token = $user->createToken('sanctum-token')->plainTextToken;
                return response()->json([
                    'status' => 200,
                    'data' => $user,
                    "token" => $token,
                    'message' => 'Login Successful'
                ]);
            }

            return response()->json([
                'status' => 404,
                'message' => 'Invalid email or password'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // or log it
        }

    }
    public function register(Request $request) {

        try {
            $request->validate([
                'department_id' => 'sometimes',
                'program_id' => 'sometimes',
                'role_id' => 'sometimes',
                'school_id' => 'sometimes',
                "firstname" => "required",
                "middlename" => "required",
                "lastname" => "required",
                "email" => "required",
                "password" => "required",
                "contact" => "required",
                'skills' => 'array|sometimes',
                'skills.*' => 'integer|exists:skills,id'
            ]);


            $user = User::where('email', $request->email)->first();

            if($user) {
                return response()->json([
                    "message" => 'Email has already taken'
                ]);
            } else {
                $data = User::create([
                    'department_id' => $request->department_id,
                    'program_id' => $request->program_id,
                    'role_id' => $request->role_id,
                    'school_id' => $request->school_id,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    "lastname" => $request->lastname,
                    "email" => $request->email,
                    'password' => bcrypt($request->password),
                    "contact" => $request->contact
                ]);

                $data->skill()->sync($request->skills);

                $token = $data->createToken('sanctum-token')->plainTextToken;
                return response()->json([
                    "status" => 201,
                    "data" => $data,
                    "token" => $token
                ], 201);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // or log it
        }

    }
    public function update(Request $request, $id) {
        $request->validate([
            'department_id' => 'sometimes',
            'program_id' => 'sometimes',
            'role_id' => 'sometimes',
            'school_id' => 'sometimes',
            "firstname" => "sometimes",
            "middlename" => "sometimes",
            "lastname" => "sometimes",
            "email" => "sometimes",
            "contact" => "sometimes",
        ]);

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No user found',
                ], 404);
            }

            $user->update($request->only([
                'department_id',
                'program_id',
                'role_id',
                'school_id',
                'firstname',
                'middlename',
                'lastname',
                'email',
                'contact',
            ]));

            return response()->json([
                'status' => 200,
                'message' => 'User successfully updated',
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function delete(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $user = User::find($request->id);

            if(!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => "No User found"
                ], 404);
            }

            User::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "User successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function index() {
        try {
            $users = User::with('skill')->with('organizations')->get();

            return response()->json([
                'status' => 200,
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function getUser($id) {
        try {
            $user = User::where('id', $id)->with('skill')->with('organizations')->get();

            if(!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No user found'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $user
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }

}

