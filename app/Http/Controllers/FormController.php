<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{

    public function index($id) {
        try {
            $event = Event::with(['forms.commexApprover', 'forms.deanApprover', 'forms.asdApprover', 'forms.adApprover'])->findOrFail($id);

            return response()->json([
                'status' => 200,
                'data' => $event->forms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
    public function getProgramForm ($model_id) {
        try {
            // Get event IDs with the given model_id
            $eventIds = Event::where('model_id', $model_id)->pluck('id');

            // Fetch forms that are connected to those event IDs and load related events
            $forms = Form::whereHas('events', function ($query) use ($eventIds) {
                    $query->whereIn('events.id', $eventIds);
                })
                ->with(['events' => function ($query) use ($eventIds) {
                    $query->whereIn('events.id', $eventIds);
                }])
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $forms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        $event = Event::with('user')->find($validated['event_id']);

        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded.',
            ], 400);
        }

        $path = $request->file('file')->store('public/pdf');
        $url = Storage::url($path); // public URL

        $form = Form::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'file' => asset($url),
        ]);

        // Attach the form to the event via pivot table
        $form->events()->attach($validated['event_id']);


        // COMMEX START
        if ($event && $event->user && $event->user->role_id === 1) {
            $userId = auth()->id(); // Get current logged-in user ID

            $form->update([
                'is_commex' => true,
                'commex_approved_by' => $userId,
                'commex_approve_date' => now(),
                'is_dean' => true,
                'dean_approved_by' => $userId,
                'dean_approve_date' => now(),
            ]);
        }
        // COMMEX END


        return response()->json([
            'message' => 'Uploaded successfully',
            'form' => $form
        ], 201);
    }
    public function approve(Request $request) {

        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "dean_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes',
            "ad_remarks" => 'sometimes',
        ]);

        try {
            $form = Form::find($request->id);

            if (!$form) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id(); // current logged-in user

            $roleUpdateMap = [
                1  => ['is_commex' => true, 'commex_remarks' => $request->input('commex_remarks'), 'commex_approved_by' => $userId, 'commex_approve_date' => now()],
                9  => ['is_dean' => true, 'dean_remarks' => $request->input('dean_remarks'), 'dean_approved_by' => $userId, 'dean_approve_date' => now()],
                10 => ['is_asd' => true, 'asd_remarks' => $request->input('asd_remarks'), 'asd_approved_by' => $userId, 'asd_approve_date' => now()],
                11 => ['is_ad' => true, 'ad_remarks' => $request->input('ad_remarks'), 'ad_approved_by' => $userId, 'ad_approve_date' => now()],
            ];

            if (isset($roleUpdateMap[$request->role_id])) {
                $form->update($roleUpdateMap[$request->role_id]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Approved Successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
    public function reject(Request $request) {
        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "dean_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes',
            "ad_remarks" => 'sometimes',
        ]);

        try {
            $form = Form::find($request->id);

            if (!$form) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $roleUpdateMap = [
                1  => ['is_commex' => false, 'commex_remarks' => $request->input('commex_remarks')],
                9  => ['is_dean' => false, 'dean_remarks' => $request->input('dean_remarks')],
                10 => ['is_asd' => false, 'asd_remarks' => $request->input('asd_remarks')],
                11 => ['is_ad' => false, 'ad_remarks' => $request->input('ad_remarks')],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;

            if ($updateData) {
                $form->update($updateData);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Form Rejected',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function attachToEvent(Request $request) {

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'form_id' => 'required|exists:forms,id',
        ]);

        try {
            $form = Form::findOrFail($request->form_id);
            $event = Event::findOrFail($request->event_id);

            // Check if already attached
            if ($form->events()->where('events.id', $event->id)->exists()) {
                return response()->json([
                    'status' => 409,
                    'message' => 'Form is already attached to the event.',
                ], 409);
            }

            // Attach form to event
            $form->events()->attach($event->id);

            return response()->json([
                'status' => 200,
                'message' => 'Form successfully attached to event.',
                'form' => $form,
                'event' => $event
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
