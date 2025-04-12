<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index() {
        try {
            $departments = Department::all();

            return response()->json([
                'status' => 200,
                'data' => $departments
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function create(Request $request) {
        $request->validate([
            "name" => "required|string",
        ]);

        try {
            $department = Department::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 201,
                'data' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }

    }
    public function update(Request $request) {
        $request->validate([
            "id" => "required",
            "name" => "required|string"
        ]);

        try {
            $department = Department::find($request->id);

            if(!$department) {
                return response()->json([
                    'status' => 404,
                    'message' => "No Department found"
                ], 404);
            }

            Department::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Department successfully updated"
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
            $department = Department::find($request->id);

            if(!$department) {
                return response()->json([
                    'status' => 404,
                    'message' => "No department found"
                ], 404);
            }

            Department::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Department successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }

}
