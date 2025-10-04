<?php

namespace App\Http\Controllers;

use App\Models\Form14;
use App\Models\Form14BudgetSummary;
use App\Models\Event;
use Illuminate\Http\Request;

class Form14Controller extends Controller
{
    // GET all form14 records with budget summaries
    public function index(){
        $form14 = Form14::with('budgetSummaries')->get();
        return response()->json($form14);
    }

    // GET single form14 record with budget summaries
    public function show($id){
        $form14 = Form14::with('budgetSummaries')->findOrFail($id);
        return response()->json($form14);
    }

    // CREATE form14 with budget summaries
    public function store(Request $request){
        $validated = $request->validate([
            'activities_id' => 'required|exists:activities,id',
            'event_status_id' => 'required|exists:event_status,id',
            'objectives' => 'nullable|string',
            'target_group' => 'nullable|string',
            'description' => 'nullable|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'feedback' => 'nullable|string',
            'acknowledgements' => 'nullable|string',
            'budget_summaries' => 'nullable|array',
            'budget_summaries.*.item' => 'nullable|string',
            'budget_summaries.*.cost' => 'required|numeric',
            'budget_summaries.*.personnel' => 'nullable|string',
            'budget_summaries.*.quantity' => 'nullable|integer',
            'budget_summaries.*.description' => 'nullable|string',
        ]);

        $form14 = Form14::create(collect($validated)->except('budget_summaries')->toArray());

        if (!empty($validated['budget_summaries'])) {
            foreach ($validated['budget_summaries'] as $budget) {
                $budget['form14_id'] = $form14->form14_id;
                Form14BudgetSummary::create($budget);
            }
        }

        return response()->json($form14->load('budgetSummaries'), 201);
    }

    // UPDATE form14 and its budget summaries
    public function update(Request $request, $id){
        $form14 = Form14::findOrFail($id);

        $validated = $request->validate([
            'objectives' => 'nullable|string',
            'target_group' => 'nullable|string',
            'description' => 'nullable|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'feedback' => 'nullable|string',
            'acknowledgements' => 'nullable|string',
            'budget_summaries' => 'nullable|array',
            'budget_summaries.*.id' => 'nullable|exists:budget_summaries,id',
            'budget_summaries.*.item' => 'nullable|string',
            'budget_summaries.*.cost' => 'required|numeric',
            'budget_summaries.*.personnel' => 'nullable|string',
            'budget_summaries.*.quantity' => 'nullable|integer',
            'budget_summaries.*.description' => 'nullable|string',
        ]);

        $validated['event_status_id'] = 8;

        // Only update form14 fields, not budget_summaries
        $form14->update(collect($validated)->except('budget_summaries')->toArray());

        if (isset($validated['budget_summaries'])) {
            $existingIds = $form14->budgetSummaries()->pluck('id')->toArray();
            $incomingIds = collect($validated['budget_summaries'])->pluck('id')->filter()->toArray();

            // Delete removed budget summaries
            $toDelete = array_diff($existingIds, $incomingIds);
            if (!empty($toDelete)) {
                Form14BudgetSummary::whereIn('id', $toDelete)->delete();
            }

            // Update or create
            foreach ($validated['budget_summaries'] as $budget) {
                if (isset($budget['id'])) {
                    $summary = Form14BudgetSummary::find($budget['id']);
                    $summary->update($budget);
                } else {
                    $budget['form14_id'] = $form14->form14_id;
                    Form14BudgetSummary::create($budget);
                }
            }
        }

        return response()->json($form14->load('budgetSummaries'));
    }

    // DELETE form14 (cascades to budget summaries)
    public function destroy($id){
        $form14 = Form14::findOrFail($id);
        $form14->delete();

        return response()->json(['message' => 'Form14 deleted successfully']);
    }
    
    // GET all reports for a specific activity, including budget summaries
    public function getReportsByActivity($activities_id){
        $reports = Form14::with('budgetSummaries')
            ->where('activities_id', $activities_id)
            ->get();

        return response()->json($reports);
    }
    
    public function updateStatus(Request $request, $id){
        try {
            $validated = $request->validate([
                'event_status_id' => 'required|exists:event_status,id',
                'remarks' => 'nullable|string',
            ]);

            $form14 = Form14::findOrFail($id);
            $user = $request->user();
            $roleId = $user->role_id;

            switch ($validated['event_status_id']) {
                // SUBMIT action
                case 4:
                    $form14->event_status_id = 4;
                    break;

                // PULL-BACK action (creator only)
                case 5:
                    $form14->event_status_id = 5;
                    break;

                case 9:
                    $form14->event_status_id = 9;
                    break;

                // REVISE action (role 1 = Commex, role 10 = ASD)
                case 6:
                    if ($roleId == 1) {
                        $form14->commex_remarks = $validated['remarks'] ?? null;
                    } elseif ($roleId == 10) {
                        $form14->asd_remarks = $validated['remarks'] ?? null;
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'You are not allowed to send this report for revision.'
                        ], 403);
                    }
                    $form14->event_status_id = 6;
                    break;

                // APPROVE action
                case 7:
                    if ($roleId == 1) {
                        $form14->is_commex = true;
                        $form14->commex_approved_by = $user->id;
                        $form14->commex_approve_date = now();
                    }
                    if ($roleId == 10) {
                        $form14->is_asd = true;
                        $form14->asd_approved_by = $user->id;
                        $form14->asd_approve_date = now();
                    }
                    // Only mark fully approved if both roles have approved
                    if ($form14->is_commex && $form14->is_asd) {
                        $form14->event_status_id = 7;
                    }
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action.'
                    ], 400);
            }

            $form14->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'data' => $form14,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}

