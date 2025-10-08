<?php

namespace App\Http\Controllers;

use App\Models\EventMember;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventMemberController extends Controller
{
    public function index() {
        try {
            $eventmembers = EventMember::with(['user', 'event'])->get();

            return response()->json([
                'status' => 200,
                'data' => $eventmembers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request) {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string'
        ]);

        try {
            // Check if member already exists for this event
            $existingMember = EventMember::where('event_id', $request->event_id)
                                        ->where('user_id', $request->user_id)
                                        ->first();

            if($existingMember) {
                return response()->json([
                    'status' => 409,
                    'message' => "User is already a member of this event"
                ], 409);
            }

            $eventmember = EventMember::create([
                'event_id' => $request->event_id,
                'user_id' => $request->user_id,
                'role' => $request->role,
            ]);

            // Load relationships for response
            $eventmember->load(['user', 'event']);

            return response()->json([
                'status' => 201,
                'data' => $eventmember
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request) {
        $request->validate([
            "id" => "required|exists:event_members,id",
            "user_id" => 'sometimes|exists:users,id',
            "role" => 'sometimes|string',
        ]);

        try {
            $eventmember = EventMember::find($request->id);

            if(!$eventmember) {
                return response()->json([
                    'status' => 404,
                    'message' => "No member found"
                ], 404);
            }

            // Check for duplicate if user_id is being updated
            if($request->has('user_id') && $request->user_id != $eventmember->user_id) {
                $existingMember = EventMember::where('event_id', $eventmember->event_id)
                                            ->where('user_id', $request->user_id)
                                            ->first();

                if($existingMember) {
                    return response()->json([
                        'status' => 409,
                        'message' => "User is already a member of this event"
                    ], 409);
                }
            }

            $eventmember->update($request->only([
                'user_id',
                'role',
            ]));

            // Reload relationships
            $eventmember->load(['user', 'event']);

            return response()->json([
                'status' => 200,
                "data" => $eventmember,
                "message" => "Event member successfully updated"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request) {
        $request->validate([
            "id" => "required|exists:event_members,id",
        ]);

        try {
            $eventmember = EventMember::find($request->id);

            if(!$eventmember) {
                return response()->json([
                    'status' => 404,
                    'message' => "No record found"
                ], 404);
            }

            $eventmember->delete();

            return response()->json([
                'status' => 200,
                "message" => "Event member successfully removed"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}