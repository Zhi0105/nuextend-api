<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller {

    public function getTerms() {
        $terms = Event::select('term')->distinct()->pluck('term');

        if ($terms->isEmpty()) {
            return response()->json([
                'message' => 'No terms found'
            ], 404);
        }

        // transform each term into an object with "name" key
        $formatted = $terms->map(fn($term) => ['name' => $term]);

        return response()->json([
            'terms' => $formatted
        ]);
    }
    public function index(Request $request) {
        $request->validate([
            'term' => 'string|required'
        ]);

        $events = Event::where('term', $request->term)->get();

        if ($events->isEmpty()) {
            return response()->json([
                'message' => 'No event found for this term'
            ], 404);
        }

        return response()->json([
            'data' => $events->load([
                'organization',
                'model',
                'eventtype',
                'eventstatus',
                'skills',
                'eventmember',
                'unsdgs',
                'participants',
                'forms',
                'activity',
                'activity.form14',
                'activity.form14.budgetSummaries',
                'activity.form14.commexApprover',
                'activity.form14.asdApprover',
                'progress_report',
                'form1',
                'form2',
                'form3',
                'form4',
                'form5',
                'form6',
                'form7',
                'form8',
                'form9',
                'form10',
                'form11',
                'form12',
            ])
        ], 200);
    }
}
