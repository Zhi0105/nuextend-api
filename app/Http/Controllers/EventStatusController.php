<?php

namespace App\Http\Controllers;

use App\Models\EventStatus;
use Illuminate\Http\Request;

class EventStatusController extends Controller
{
    public function index() {
        try {
            $event_status = EventStatus::all();

            return response()->json([
                'status' => 200,
                'data' => $event_status
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
