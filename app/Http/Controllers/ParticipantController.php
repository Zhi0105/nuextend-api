<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index() {
        try {
            $participants = Participant::all();

            return response()->json([
                'status' => 200,
                'data' => $participants
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
            "user_id" => 'required',
            "event_id" => 'required',
            "is_attended" => "required|boolean",
        ]);

        try {
            $participant = Participant::create([
                'user_id' => $request->user_id,
                'event_id' => $request->event_id,
                'is_attended' => $request->is_attended
            ]);

            return response()->json([
                'status' => 201,
                'data' => $participant
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
            'user_id' => 'sometimes',
            'event_id' => 'sometimes',
            "is_attended" => "sometimes"
        ]);

        try {
            $participant = Participant::find($request->id);

            if(!$participant) {
                return response()->json([
                    'status' => 404,
                    'message' => "No participant found"
                ], 404);
            }

            $participant->update($request->only([
                'user_id',
                'event_id',
                'is_attended',
            ]));

            return response()->json([
                'status' => 200,
                "message" => "Participant successfully updated"
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
            $participant = Participant::find($request->id);

            if(!$participant) {
                return response()->json([
                    'status' => 404,
                    'message' => "No participant found"
                ], 404);
            }

            Participant::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Participant successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
