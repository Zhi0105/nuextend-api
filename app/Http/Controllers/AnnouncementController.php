<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements.
     */
    public function index()
    {
        // ✅ Include event relation to prevent null errors
        $announcements = Announcement::with('event')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $announcements,
        ], 200);
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id', // ✅ Make event_id optional
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $announcement = Announcement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => $announcement,
        ], 201);
    }

    /**
     * Display a single announcement.
     */
    public function show(Announcement $announcement)
    {
        return response()->json([
            'success' => true,
            'data' => $announcement->load('event'),
        ]);
    }

    /**
     * Update an existing announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'event_id' => 'sometimes|exists:events,id',
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ]);

        $announcement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => $announcement,
        ]);
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }

    /**
     * Get announcements by event.
     */
    public function getByEvent($eventId)
    {
        $announcements = Announcement::where('event_id', $eventId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements,
        ]);
    }
}
