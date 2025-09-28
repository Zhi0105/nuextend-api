<?php

namespace App\Http\Controllers;

use App\Models\Form12;
use App\Models\Form12Attendee;
use App\Models\Form12NewItem;
use Illuminate\Http\Request;

class Form12Controller extends Controller
{
    // INDEX - fetch with relationships
    public function index()
    {
        $form12 = Form12::with(['attendees.department', 'attendees.program', 'newItems'])->get();
        return response()->json($form12);
    }

    // CREATE - parent + nested attendees and new items
    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'meeting_date' => 'nullable|date',
            'call_to_order' => 'nullable|string',
            'aomftlm' => 'nullable|string',
            'other_matters' => 'nullable|string',
            'adjournment' => 'nullable|date',
            'documentation' => 'nullable|string',

            // children
            'attendees' => 'array',
            'attendees.*.full_name' => 'nullable|string',
            'attendees.*.designation' => 'nullable|string',
            'attendees.*.department_id' => 'nullable|exists:departments,id',
            'attendees.*.programs_id' => 'nullable|exists:programs,id',

            'new_items' => 'array',
            'new_items.*.topic' => 'nullable|string',
            'new_items.*.discussion' => 'nullable|string',
            'new_items.*.resolution' => 'nullable|string',
        ]);

        $form12 = Form12::create($validated);

        // Save attendees
        if ($request->has('attendees')) {
            foreach ($request->attendees as $attendee) {
                $form12->attendees()->create($attendee);
            }
        }

        // Save new items
        if ($request->has('new_items')) {
            foreach ($request->new_items as $item) {
                $form12->newItems()->create($item);
            }
        }

        return response()->json($form12->load(['attendees.department', 'attendees.program', 'newItems']), 201);
    }

    // UPDATE - parent + replace attendees and new items
    public function update(Request $request, $id)
    {
        $form12 = Form12::findOrFail($id);

        $validated = $request->validate([
            'meeting_date' => 'nullable|date',
            'call_to_order' => 'nullable|string',
            'aomftlm' => 'nullable|string',
            'other_matters' => 'nullable|string',
            'adjournment' => 'nullable|date',
            'documentation' => 'nullable|string',

            'attendees' => 'array',
            'attendees.*.full_name' => 'nullable|string',
            'attendees.*.designation' => 'nullable|string',
            'attendees.*.department_id' => 'nullable|exists:departments,id',
            'attendees.*.programs_id' => 'nullable|exists:programs,id',

            'new_items' => 'array',
            'new_items.*.topic' => 'nullable|string',
            'new_items.*.discussion' => 'nullable|string',
            'new_items.*.resolution' => 'nullable|string',
        ]);

        $form12->update($validated);

        // Refresh attendees
        if ($request->has('attendees')) {
            $form12->attendees()->delete();
            foreach ($request->attendees as $attendee) {
                $form12->attendees()->create($attendee);
            }
        }

        // Refresh new items
        if ($request->has('new_items')) {
            $form12->newItems()->delete();
            foreach ($request->new_items as $item) {
                $form12->newItems()->create($item);
            }
        }

        return response()->json($form12->load(['attendees.department', 'attendees.program', 'newItems']));
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
            $proposal = Form12::find($request->id);

            if (!$proposal) {
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
                $proposal->update($roleUpdateMap[$request->role_id]);
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
            $proposal = Form12::find($request->id);

            if (!$proposal) {
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
                $proposal->update($updateData);
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
}
