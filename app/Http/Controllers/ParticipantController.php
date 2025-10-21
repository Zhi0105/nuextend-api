<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        ]);

        try {
            // 1. Create the Participant
            $participant = Participant::updateOrCreate([
                'user_id' => $request->user_id,
                'event_id' => $request->event_id,
            ]);

            // 2. Get upcoming events this user is participating in
            $upcomingEvents = Participant::with('event')->where('user_id', $request->user_id)->get();

            // 3. Return both participant info and upcoming events
            return response()->json([
                'status' => 201,
                'participant' => $participant,
                'upcoming_events' => $upcomingEvents
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }

    }
    public function update(Request $request) {
        $request->validate([
            "id" => "required",
            'user_id' => 'sometimes',
            'event_id' => 'sometimes',
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
    public function getParticipantEvents($id) {
        try {

            $upcomingEvents = Participant::with('event')->where('user_id', $id)->get();

            if(!$upcomingEvents) {
                return response()->json([
                    'status' => 200,
                    'upcoming_events' => $upcomingEvents
                ], 200);
            }

            return response()->json([
                'status' => 200,
                'upcoming_events' => $upcomingEvents
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
    // public function attendance(Request $request) {

    //     $request->validate([
    //         "participant_id" => 'required',
    //     ]);

    //     try {
    //         $attendanceLog = Attendance::updateOrCreate([
    //             'participant_id' => $request->participant_id,
    //             'attendance_date' => now()->addDay(), // add 1 day
    //         ], [
    //             'is_attended' => true
    //         ]);

    //         return response()->json([
    //             'status' => 201,
    //             'data' => $attendanceLog
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => $e->getCode() ?: 500,
    //             'message' => $e->getMessage(),
    //         ], $e->getCode() ?: 500);
    //     }
    // }

   public function attendance(Request $request)
{
    $validated = $request->validate([
        'participant_id' => 'required|integer|exists:participants,id',
    ]);

    try {
        DB::beginTransaction();

        $participantId = $validated['participant_id'];
        $today = now()->toDateString();

        // Check if there's an attendance record for today
        $attendance = Attendance::where('participant_id', $participantId)
            ->whereDate('created_at', $today)
            ->first();

        if (!$attendance) {
            // Create a new attendance record (time-in)
            $attendance = Attendance::create([
                'participant_id' => $participantId,
                'time_in' => now(),
                'time_out' => null,
                'is_attended' => false,
            ]);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Time in recorded successfully.',
                'data' => $attendance,
            ], 201);
        }

        if (is_null($attendance->time_out)) {
            // Update existing record with time-out
            $attendance->update([
                'time_out' => now(),
                'is_attended' => true,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Time out recorded successfully.',
                'data' => $attendance,
            ], 200);
        }

        // If both time_in and time_out already exist
        DB::rollBack();
        return response()->json([
            'status' => 400,
            'message' => 'Attendance already completed for today.',
        ], 400);

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('Attendance error: '.$e->getMessage(), [
            'participant_id' => $request->participant_id,
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Something went wrong while recording attendance.',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

}
