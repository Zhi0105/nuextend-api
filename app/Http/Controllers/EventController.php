<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Event;
use App\Models\Targetgroup;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index() {
        try {
            $events = Event::with('skills')->with([
                'forms',
                'activity.progress_report',
                'unsdgs',
                'eventstatus',
                'user',
                'user.department',
                'user.program',
                'eventtype',
                'model',
                'organization',
                'skills',
                'unsdgs',
                'participants.user',
                'participants.attendance',
                'participants.event'
            ])->get();

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
            "target_group" => 'sometimes',
            "term" => "required|string",
            "budget_proposal" => "sometimes",
            'skills' => 'array',
            'skills.*' => 'integer|exists:skills,id',
            'unsdgs' => 'array',
            'unsdgs.*' => 'integer|exists:unsdgs,id',
            'activities' => 'array',
            'activities.*.name' => 'required|string',
            'activities.*.description' => 'required|string',
            'activities.*.address' => 'required|string',
            'activities.*.start_date' => 'required|string',
            'activities.*.end_date' => 'required|string',

        ]);

        try {

            $event = Event::create([
                'user_id' => $request->user_id,
                'organization_id' => $request->organization_id,
                'model_id' => $request->model_id,
                'event_type_id' => $request->event_type_id,
                'event_status_id' => 1,
                'target_group' => $request->target_group,
                'term' => $request->term,
                'budget_proposal' => $request->budget_proposal
            ]);

            foreach ($request->activities as $activity) {
                Activity::create([
                    'event_id' => $event->id,
                    'name' => $activity['name'],
                    'address' => $activity['address'],
                    'start_date' => $activity['start_date'],
                    'end_date' => $activity['end_date'],
                    'description' => $activity['description'],
                ]);
            }

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
            "activity_id" => "sometimes",
            "organization_id" => 'sometimes',
            "model_id" => 'sometimes',
            "event_type_id" => 'sometimes',
            "event_status_id" => 'sometimes',
            "target_group" => 'sometimes',
            "name" => "sometimes",
            "address" => "sometimes",
            "term" => "sometimes",
            "start_date" => "sometimes",
            "end_date" => "sometimes",
            "description" => "sometimes",
            "budget_proposal" => "sometimes",
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
                'program_model_name',
                'organization_id',
                'model_id',
                'event_type_id',
                'event_status_id',
                'target_group',
                'term',
                'budget_proposal'
            ]));

            Activity::where('id', $request->activity_id)->update($request->only([
                'name',
                'address',
                'start_date',
                'end_date',
                'description'
            ]));

            $event->skills()->sync($request->skills);
            $event->unsdgs()->sync($request->unsdgs);

            Event::where('id', $request->id)->update([
                'event_status_id' => 1
            ]);

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
    public function getEvent($userID) {
        $user = User::with('organizations')->find($userID);

        // if (!$user || $user->organizations->isEmpty()) {
        //     return response()->json([
        //         "status" => 404,
        //         "message" => "No events found for this user"
        //     ], 404);
        // }

        // Get organization IDs related to the user
        $organizationIds = $user->organizations->pluck('id');

        // Get events where organization_id is in those IDs and eager load everything
        $events = Event::whereIn('organization_id', $organizationIds)
            ->with([
                'skills',
                'activity.progress_report',
                'unsdgs',
                'eventstatus',
                'user',
                'eventtype',
                'model',
                'organization',
                'participants.user',
                'participants.attendance',
                'participants.event',
            ])
            ->get();

        return response()->json([
            "status" => 200,
            "data" => $events
        ], 200);

        // $user = User::with('organizations.events.eventstatus')->find($userID);
        // $events = $user->organizations->flatMap->events;

        // if(!$events) {
        //     return response()->json([
        //         "status" => 404,
        //         "message" => "no event found"
        //     ], 404);
        // }

        // return response()->json([
        //     "status" => 200,
        //     "data" => $events
        // ], 200);
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
                'event_status_id' => 2,
                'remarks' => null,
                'approve_date' => now()
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
            "remarks" => "required"
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
                'event_status_id' => 3,
                'remarks' => $request->remarks
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
    public function posted(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $event = Event::find($request->id);

            if (!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            $forms = $event->forms;

            if ($forms->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => "No form associated with this event"
                ], 404);
            }

            foreach ($forms as $form) {
                if (!$form->is_commex) {
                    $form->is_commex = 1;
                    $form->commex_approved_by = 1;
                    $form->commex_approve_date = now();
                }

                if (!$form->is_dean) {
                    $form->is_dean = 1;
                    $form->dean_approved_by = 1;
                    $form->dean_approve_date = now();
                }

                if (!$form->is_asd) {
                    $form->is_asd = 1;
                    $form->asd_approved_by = 1;
                    $form->asd_approve_date = now();
                }

                if (!$form->is_ad) {
                    $form->is_ad = 1;
                    $form->ad_approved_by = 1;
                    $form->ad_approve_date = now();
                }

                $form->save();
            }

            $event->update([
                'is_posted' => true
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Event and associated forms posted"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function terminate(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $event = Event::find($request->id);

            if (!$event) {
                return response()->json([
                    'status' => 404,
                    'message' => "No event found"
                ], 404);
            }

            $event->update([
                'event_status_id' => 2
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Event successfully terminated."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // public function getForms($id) {
    //     try {
    //         $event = Event::findOrFail($id);
    //         $forms = $event->forms; // Collection of Form models

    //         return response()->json([
    //             'status' => 200,
    //             'data' => $forms
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' =>  $e->getCode(),
    //             'message' => $e->getMessage(),
    //         ],  $e->getCode());
    //     }
    // }
}
