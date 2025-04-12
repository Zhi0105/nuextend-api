<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index() {
        try {
            $programs = Program::all();

            return response()->json([
                'status' => 200,
                'data' => $programs
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
            "department_id" => 'required',
            "name" => "required|string"
        ]);

        try {
            $department = Program::create([
                "department_id" => $request->department_id,
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
            $program = Program::find($request->id);

            if(!$program) {
                return response()->json([
                    'status' => 404,
                    'message' => "No program found"
                ], 404);
            }

            Program::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Program successfully updated"
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
            $program = Program::find($request->id);

            if(!$program) {
                return response()->json([
                    'status' => 404,
                    'message' => "No program found"
                ], 404);
            }

            Program::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Program successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
