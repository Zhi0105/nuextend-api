<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index() {
        try {
            $skills = Skill::all();

            return response()->json([
                'status' => 200,
                'data' => $skills
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
            $skill = Skill::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 201,
                'data' => $skill
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
            $skill = Skill::find($request->id);

            if(!$skill) {
                return response()->json([
                    'status' => 404,
                    'message' => "No skill found"
                ], 404);
            }

            Skill::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Skill successfully updated"
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
            $skill = Skill::find($request->id);

            if(!$skill) {
                return response()->json([
                    'status' => 404,
                    'message' => "No skill found"
                ], 404);
            }

            Skill::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Skill successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
