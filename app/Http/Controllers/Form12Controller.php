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
}
