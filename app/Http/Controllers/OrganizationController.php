<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index() {
        try {
            $organizations = Organization::all();

            return response()->json([
                'status' => 200,
                'data' => $organizations
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
            $organization = Organization::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 201,
                'data' => $organization
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
            $organization = Organization::find($request->id);

            if(!$organization) {
                return response()->json([
                    'status' => 404,
                    'message' => "No organization found"
                ], 404);
            }

            Organization::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Organization successfully updated"
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
            $organization = Organization::find($request->id);

            if(!$organization) {
                return response()->json([
                    'status' => 404,
                    'message' => "No organization found"
                ], 404);
            }

            Organization::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Organization successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
