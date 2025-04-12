<?php

namespace App\Http\Controllers;

use App\Models\EventMember;
use Illuminate\Http\Request;

class EventMemberController extends Controller
{
    public function index() {
        try {
            $eventmembers = EventMember::all();

            return response()->json([
                'status' => 200,
                'data' => $eventmembers
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
            'event_id' => 'required',
            'role_id' => 'required',
            "firstname" => "required",
            "middlename" => "required",
            "lastname" => "required",
        ]);

        try {
            $eventmember = EventMember::create([
                'event_id' => $request->event_id,
                'role_id' => $request->role_id,
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                "lastname" => $request->lastname,
            ]);


            return response()->json([
                'status' => 201,
                'data' => $eventmember
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
            "event_id" => 'sometimes',
            "role_id" => 'sometimes',
            "firstname" => "sometimes",
            "middlename" => "sometimes",
            "lastname" => "sometimes",
        ]);

        try {
            $eventmember = EventMember::find($request->id);

            if(!$eventmember) {
                return response()->json([
                    'status' => 404,
                    'message' => "No member found"
                ], 404);
            }

            $eventmember->update($request->only([
                'event_id',
                'role_id',
                'firstname',
                'middlename',
                'lastname',
            ]));

            return response()->json([
                'status' => 200,
                "message" => "Event members successfully updated"
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
            $eventmember = EventMember::find($request->id);

            if(!$eventmember) {
                return response()->json([
                    'status' => 404,
                    'message' => "No record found"
                ], 404);
            }

            EventMember::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Event members successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
