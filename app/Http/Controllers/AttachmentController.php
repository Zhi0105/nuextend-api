<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttachmentController extends Controller
{
    public function index($event_id) {
    try {
        $attachments = Attachment::where('event_id', $event_id)->get();

        return response()->json([
            'status' => 200,
            'data' => $attachments
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
        ], 500); // ✅ fixed
    }
}


  public function store(Request $request) {
    $validated = $request->validate([
        'event_id' => 'required|exists:events,id',
        'name' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB, PDF only
        'remarks' => 'nullable|string|max:500', // 👈 add this
    ]);

    try {
        // Check if event exists
        $event = Event::find($validated['event_id']);
        if (!$event) {
            return response()->json([
                'message' => 'Event not found.',
            ], 404);
        }

        // Handle file upload
        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded.',
            ], 400);
        }

        // Store file and get path
        $path = $request->file('file')->store('public/attachments');
        $url = Storage::url($path); // public URL

        // Create attachment record
        $attachment = Attachment::create([
            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'file' => asset($url), // Store full URL like in FormController
            'remarks' => $validated['remarks'] ?? null, // 👈 safely handle remarks
        ]);

        return response()->json([
            'message' => 'Attachment uploaded successfully',
            'attachment' => $attachment
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'file' => 'sometimes|file|mimes:pdf|max:10240',
        ]);

        try {
            $attachment = Attachment::findOrFail($id);

            // Handle file update if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file (same logic as FormController)
                $oldFilePath = str_replace(asset('storage'), 'public', $attachment->file);
                if (Storage::exists($oldFilePath)) {
                    Storage::delete($oldFilePath);
                }

                // Store new file
                $path = $request->file('file')->store('public/attachments');
                $url = Storage::url($path);
                $validated['file'] = asset($url);
            }

            // Parse dates if provided
            if (isset($validated['seen_date'])) {
                $validated['seen_date'] = Carbon::parse($validated['seen_date']);
            }

            $attachment->update($validated);

            return response()->json([
                'message' => 'Attachment updated successfully',
                'attachment' => $attachment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateRemarks(Request $request, $id) {
        $validated = $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $attachment = Attachment::findOrFail($id);

            // Update only the remarks
            $attachment->update([
                'remarks' => $validated['remarks'],
            ]);

            return response()->json([
                'message' => 'Remarks updated successfully',
                'attachment' => $attachment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id) {
        try {
            $attachment = Attachment::findOrFail($id);

            // Delete file from storage (same as FormController)
            $filePath = str_replace(asset('storage'), 'public', $attachment->file);
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // Delete record from database
            $attachment->delete();

            return response()->json([
                'message' => 'Attachment removed successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id) {
        try {
            $attachment = Attachment::findOrFail($id);

            return response()->json([
                'status' => 200,
                'data' => $attachment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}