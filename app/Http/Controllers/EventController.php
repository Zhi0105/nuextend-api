<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index() {
        try {
            $events = Event::with('skills')->with([
                'forms',
                'form_remarks.user',
                'activity.progress_report',
                'activity.form14',
                'activity.form14.budgetSummaries',
                'activity.form14.commexApprover',
                'activity.form14.asdApprover',
                'unsdgs',
                'eventstatus',
                'user',
                'user.department',
                'user.role',
                'user.program',
                'eventtype',
                'eventmember',
                'model',
                'organization',
                'skills',
                'unsdgs',
                'participants.user',
                'participants.attendance',
                'participants.event',
                'form1',
                'form1.teamMembers',
                'form1.cooperatingAgencies',
                'form1.componentProjects',
                'form1.projects',
                'form1.projects.teamMembers',
                'form1.projects.budgetSummaries',
                'form1.commexApprover',
                'form1.deanApprover',
                'form1.asdApprover',
                'form1.adApprover',
                'form2',
                'form2.eventType',
                'form2.objectives',
                'form2.impactOutcomes',
                'form2.risks',
                'form2.staffings',
                'form2.workPlans',
                'form2.detailedBudgets',
                'form2.commexApprover',
                'form2.deanApprover',
                'form2.asdApprover',
                'form2.adApprover',
                'form3',
                'form3.activityPlansBudgets',
                'form3.detailedBudgets',
                'form3.budgetSourcings',
                'form3.commexApprover',
                'form3.deanApprover',
                'form3.asdApprover',
                'form3.adApprover',
                'form4',
                'form4.commexApprover',
                'form4.deanApprover',
                'form4.asdApprover',
                'form4.adApprover',
                'form5',
                'form5.commexApprover',
                'form5.deanApprover',
                'form5.asdApprover',
                'form5.adApprover',
                'form6',
                'form6.commexApprover',
                'form6.deanApprover',
                'form6.asdApprover',
                'form6.adApprover',
                'form7',
                'form7.commexApprover',
                'form7.deanApprover',
                'form7.asdApprover',
                'form7.adApprover',
                'form8',
                'form8.references',
                'form8.commexApprover',
                'form8.deanApprover',
                'form8.asdApprover',
                'form8.adApprover',
                'form9',
                'form9.logicModels',
                'form9.commexApprover',
                'form9.deanApprover',
                'form9.asdApprover',
                'form9.adApprover',
                'form10',
                'form10.oaopb',
                'form10.commexApprover',
                'form10.deanApprover',
                'form10.asdApprover',
                'form10.adApprover',
                'form11',
                'form11.travelDetails',
                'form11.commexApprover',
                'form11.deanApprover',
                'form11.asdApprover',
                'form11.adApprover',
                'form12',
                'form12.attendees',
                'form12.newItems',
                'form12.attendees.program',
                'form12.attendees.department',
                'form12.commexApprover',
                'form12.deanApprover',
                'form12.asdApprover',
                'form12.adApprover',

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
            "name" => 'sometimes',
            "description" => 'sometimes',
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
            'members' => 'array',
            'members.*.user_id' => 'required',
            'members.*.role' => 'required|string',

        ]);

        try {

            $event = Event::create([
                'user_id' => $request->user_id,
                'organization_id' => $request->organization_id,
                'model_id' => $request->model_id,
                'event_type_id' => $request->event_type_id,
                'event_status_id' => 1,
                'name' => $request->name,
                'description' => $request->description,
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

            foreach ($request->members as $member) {
                EventMember::create([
                    'event_id' => $event->id,
                    'user_id' => $member['user_id'],
                    'role' => $member['role']
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
        $validated = $request->validate([
            "id" => "required|integer|exists:events,id",

            // Event fields
            "user_id" => "sometimes|integer|exists:users,id",
            "organization_id" => "sometimes|nullable|integer|exists:organizations,id",
            "model_id" => "sometimes|integer|exists:models,id",
            "event_type_id" => "sometimes|integer|exists:event_types,id",
            "event_status_id" => "sometimes|integer|exists:event_statuses,id",
            "name" => "sometimes|nullable|string",
            "description"=> "sometimes|nullable|string",
            "target_group" => "sometimes|nullable|string",
            "term" => "sometimes|string",
            "budget_proposal" => "sometimes|nullable",

            // Relations
            "skills" => "sometimes|array",
            "skills.*" => "integer|exists:skills,id",
            "unsdgs" => "sometimes|array",
            "unsdgs.*" => "integer|exists:unsdgs,id",

            // Nested activities
            "activities" => "sometimes|array",
            "activities.*.id" => "sometimes|integer|exists:activities,id",
            "activities.*.name" => "required_with:activities|string",
            "activities.*.description" => "required_with:activities|string",
            "activities.*.address" => "required_with:activities|string",
            "activities.*.start_date" => "required_with:activities|string",
            "activities.*.end_date" => "required_with:activities|string",

            // Members array
            "members" => "sometimes|array",
            "members.*.id" => "sometimes|integer|exists:event_members,id",
            "members.*.user_id" => "required_with:members|integer|exists:users,id",
            "members.*.role" => "required_with:members|string",
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $event = Event::findOrFail($validated['id']);

                // ✅ Update event main info
                $event->update(collect($validated)->only([
                    'user_id',
                    'organization_id',
                    'model_id',
                    'event_type_id',
                    'event_status_id',
                    'name',
                    'description',
                    'target_group',
                    'term',
                    'budget_proposal',
                ])->toArray());

                // ✅ Upsert activities
                $keptActivityIds = [];
                if (isset($validated['activities'])) {
                    foreach ($validated['activities'] as $activityData) {
                        $payload = [
                            'event_id' => $event->id,
                            'name' => $activityData['name'],
                            'address' => $activityData['address'],
                            'start_date' => $activityData['start_date'],
                            'end_date' => $activityData['end_date'],
                            'description' => $activityData['description'],
                        ];

                        if (!empty($activityData['id'])) {
                            $activity = Activity::where('id', $activityData['id'])
                                ->where('event_id', $event->id)
                                ->firstOrFail();
                            $activity->update($payload);
                            $keptActivityIds[] = $activity->id;
                        } else {
                            $new = Activity::create($payload);
                            $keptActivityIds[] = $new->id;
                        }
                    }
                    $event->activities()->whereNotIn('id', $keptActivityIds)->delete();
                }

                // ✅ Upsert members (new part)
                if (isset($validated['members'])) {
                    $keptMemberIds = [];
                    foreach ($validated['members'] as $memberData) {
                        $memberPayload = [
                            'event_id' => $event->id,
                            'user_id' => $memberData['user_id'],
                            'role' => $memberData['role'],
                        ];

                        if (!empty($memberData['id'])) {
                            $member = EventMember::where('id', $memberData['id'])
                                ->where('event_id', $event->id)
                                ->firstOrFail();
                            $member->update($memberPayload);
                            $keptMemberIds[] = $member->id;
                        } else {
                            $newMember = EventMember::create($memberPayload);
                            $keptMemberIds[] = $newMember->id;
                        }
                    }
                    // Delete members not in payload
                    $event->eventmember()->whereNotIn('id', $keptMemberIds)->delete();
                }

                // ✅ Sync relations
                if (array_key_exists('skills', $validated)) {
                    $event->skills()->sync($validated['skills'] ?? []);
                }
                if (array_key_exists('unsdgs', $validated)) {
                    $event->unsdgs()->sync($validated['unsdgs'] ?? []);
                }

                // Load relations for response
                $event->load(['activities', 'skills', 'unsdgs', 'eventmember']);

                return response()->json([
                    'status' => 200,
                    'message' => 'Event successfully updated',
                    'data' => $event
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
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
                'form_remarks.user',
                'activity.progress_report',
                'activity.form14',
                'activity.form14.budgetSummaries',
                'activity.form14.commexApprover',
                'activity.form14.asdApprover',
                'unsdgs',
                'eventstatus',
                'user',
                'user.department',
                'user.program',
                'user.role',
                'eventtype',
                'eventmember',
                'model',
                'organization',
                'participants.user',
                'participants.attendance',
                'participants.event',
                'form1',
                'form1.teamMembers',
                'form1.cooperatingAgencies',
                'form1.componentProjects',
                'form1.projects',
                'form1.projects.teamMembers',
                'form1.projects.budgetSummaries',
                'form1.commexApprover',
                'form1.deanApprover',
                'form1.asdApprover',
                'form1.adApprover',
                'form2',
                'form2.eventType',
                'form2.objectives',
                'form2.impactOutcomes',
                'form2.risks',
                'form2.staffings',
                'form2.workPlans',
                'form2.detailedBudgets',
                'form2.commexApprover',
                'form2.deanApprover',
                'form2.asdApprover',
                'form2.adApprover',
                'form3',
                'form3.activityPlansBudgets',
                'form3.detailedBudgets',
                'form3.budgetSourcings',
                'form3.commexApprover',
                'form3.deanApprover',
                'form3.asdApprover',
                'form3.adApprover',
                'form4',
                'form4.commexApprover',
                'form4.deanApprover',
                'form4.asdApprover',
                'form4.adApprover',
                'form5',
                'form5.commexApprover',
                'form5.deanApprover',
                'form5.asdApprover',
                'form5.adApprover',
                'form6',
                'form6.commexApprover',
                'form6.deanApprover',
                'form6.asdApprover',
                'form6.adApprover',
                'form7',
                'form7.commexApprover',
                'form7.deanApprover',
                'form7.asdApprover',
                'form7.adApprover',
                'form8',
                'form8.references',
                'form8.commexApprover',
                'form8.deanApprover',
                'form8.asdApprover',
                'form8.adApprover',
                'form9',
                'form9.logicModels',
                'form9.commexApprover',
                'form9.deanApprover',
                'form9.asdApprover',
                'form9.adApprover',
                'form10',
                'form10.oaopb',
                'form10.commexApprover',
                'form10.deanApprover',
                'form10.asdApprover',
                'form10.adApprover',
                'form11',
                'form11.travelDetails',
                'form11.commexApprover',
                'form11.deanApprover',
                'form11.asdApprover',
                'form11.adApprover',
                'form12',
                'form12.attendees',
                'form12.newItems',
                'form12.attendees.program',
                'form12.attendees.department',
                'form12.commexApprover',
                'form12.deanApprover',
                'form12.asdApprover',
                'form12.adApprover',
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

            // collect all form relations (form1 ... form12)
            $formRelations = [
                'form1', 'form2', 'form3', 'form4',
                'form5', 'form6', 'form7', 'form8',
                'form9', 'form10', 'form11', 'form12'
            ];

            $hasForms = false;

            foreach ($formRelations as $relation) {
                $forms = $event->$relation; // ex: $event->form1

                if ($forms->isNotEmpty()) {
                    $hasForms = true;

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
                }
            }

            if (!$hasForms) {
                return response()->json([
                    'status' => 404,
                    'message' => "No forms (form1 - form12) associated with this event"
                ], 404);
            }

            $event->update([
                'is_posted' => true
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Event and all associated forms posted"
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
