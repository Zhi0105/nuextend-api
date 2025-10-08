<?php

namespace App\Http\Controllers;

use App\Models\Form12;
use App\Models\Form12Attendee;
use App\Models\Form12NewItem;
use App\Models\FormRemark;
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

        $form12->update(array_merge($validated, [
            'is_updated' => true,
            'is_revised' => false,
        ]));

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
            "commex_remarks" => 'sometimes|string|nullable',
            "dean_remarks" => 'sometimes|string|nullable',
            "asd_remarks" => 'sometimes|string|nullable',
            "ad_remarks" => 'sometimes|string|nullable',
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

            // Prepare update data for each role
            $roleUpdateMap = [
                1  => [
                    'is_commex' => true, 
                    'commex_remarks' => $request->input('commex_remarks'), 
                    'commex_approved_by' => $userId, 
                    'commex_approve_date' => now()
                ],
                9  => [
                    'is_dean' => true, 
                    'dean_remarks' => $request->input('dean_remarks'), 
                    'dean_approved_by' => $userId, 
                    'dean_approve_date' => now()
                ],
                10 => [
                    'is_asd' => true, 
                    'asd_remarks' => $request->input('asd_remarks'), 
                    'asd_approved_by' => $userId, 
                    'asd_approve_date' => now()
                ],
                11 => [
                    'is_ad' => true, 
                    'ad_remarks' => $request->input('ad_remarks'), 
                    'ad_approved_by' => $userId, 
                    'ad_approve_date' => now()
                ],
            ];

            if (isset($roleUpdateMap[$request->role_id])) {
                // Remove null values to avoid overwriting existing remarks
                $updateData = array_filter(
                    $roleUpdateMap[$request->role_id],
                    fn($value) => $value !== null
                );

                $updateData['is_updated'] = false;

                $proposal->update($updateData);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Approval successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function reject(Request $request){
        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "remark" => 'required|string', // unified remark input
        ]);

        try {
            $proposal = Form12::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form12'; // your table name

            // Determine which flag to reset
            $roleUpdateMap = [
                1  => ['is_commex' => false],
                9  => ['is_dean' => false],
                10 => ['is_asd' => false],
                11 => ['is_ad' => false],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;
            $updateData['is_revised'] = true;

            if ($updateData) {
                $proposal->update($updateData);
            }

            // 🔹 Save the remark in the new table
            FormRemark::create([
                'form_type' => $formType,
                'form_id' => $request->id,
                'event_id' => $proposal->event_id,
                'user_id' => $userId,
                'remark' => $request->remark,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Form 12 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
