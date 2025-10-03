<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(){
        return Announcement::with('event')->get();
    }

    public function store(Request $request){
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        return Announcement::create($validated);
    }

    public function show(Announcement $announcement){
        return $announcement->load('event');
    }

    public function update(Request $request, Announcement $announcement){
        $validated = $request->validate([
            'event_id' => 'sometimes|exists:events,id',
            'title' => 'sometimes|string',
            'body' => 'sometimes|string',
        ]);

        $announcement->update($validated);
        return $announcement;
    }

    public function destroy(Announcement $announcement){
        $announcement->delete();
        return response()->noContent();
    }

    public function getByEvent($eventId){
        return Announcement::where('event_id', $eventId)->get();
    }
}