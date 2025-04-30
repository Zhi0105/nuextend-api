<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index() {
        try {
            $events = Event::with('skills')->with(['unsdgs', 'eventstatus', 'user', 'eventtype', 'model', 'organization', 'skills', 'unsdgs', 'participants'])->get();

            return response()->json([
                'status' => 200,
                'data' => $events
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
            "organization_id" => 'sometimes',
            'model_id' => 'required',
            "event_type_id" => 'required',
            "event_status_id" => 'sometimes',
            "name" => "required|string",
            "address" => "required|string",
            "term" => "required|string",
            "start_date" => "required|string",
            "end_date" => "required|string",
            "description" => "required|string",
            'skills' => 'array',
            'skills.*' => 'integer|exists:skills,id',
            'unsdgs' => 'array',
            'unsdgs.*' => 'integer|exists:unsdgs,id'
        ]);

        try {
            $event = Event::create([
                'user_id' => $request->user_id,
                'organization_id' => $request->organization_id,
                'model_id' => $request->model_id,
                'event_type_id' => $request->event_type_id,
                'event_status_id' => $request->event_status_id,
                'name' => $request->name,
                'address' => $request->address,
                'term' => $request->term,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
            ]);

            $event->skills()->sync($request->skills);
            $event->unsdgs()->sync($request->unsdgs);

            return response()->json([
                'status' => 201,
                'data' => $event
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ],  500);
        }

    }
    public function update(Request $request) {
        $request->validate([
            "id" => "required",
            "user_id" => 'sometimes',
            "organization_id" => 'sometimes',
            "model_id" => 'sometimes',
            "event_type_id" => 'sometimes',
            "event_status_id" => 'sometimes',
            "name" => "sometimes",
            "address" => "sometimes",
            "term" => "sometimes",
            "start_date" => "sometimes",
            "end_date" => "sometimes",
            "description" => "sometimes",
        ]);

        try {
            $event = Event::find($request->id);

            if(!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            $event->update($request->only([
                'user_id',
                'organization_id',
                'model_id',
                'event_type_id',
                'event_status_id',
                'name',
                'address',
                'term',
                'start_date',
                'end_date',
                'description'
            ]));

            $event->skills()->sync($request->skills);
            $event->unsdgs()->sync($request->unsdgs);


            return response()->json([
                'status' => 200,
                "message" => "Event successfully updated"
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
            $event = Event::find($request->id);

            if(!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            Event::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Event successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function accept(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $event = Event::find($request->id);

            if(!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            Event::where('id', $request->id)->update([
                'event_status_id' => 2
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Event accepted"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function reject(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $event = Event::find($request->id);

            if(!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            Event::where('id', $request->id)->update([
                'event_status_id' => 3
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Event rejected"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
