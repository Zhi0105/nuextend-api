<?php

namespace App\Http\Controllers;

use App\Models\OutreachActivityPlansBudget;
use App\Models\OutreachBudgetSourcing;
use App\Models\OutreachDetailedBudget;
use App\Models\OutreachProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutreachProposalController extends Controller
{
    //
    public function index() {
        try {
            $outreach_proposals = OutreachProposal::with(
                'OutreachActivityPlansBudgets',
                'OutreachDetailedBudgets',
                'OutreachBudgetSourcings'
            )->get();

            return response()->json([
                'status' => 200,
                'data' => $outreach_proposals
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function create(Request $request) {
        $request->validate([
            "title" => 'sometimes',
            "description" => 'sometimes',
            'targetGroup' => 'sometimes',
            "startDate" => 'sometimes',
            "endDate" => 'sometimes',
            "projectLeader" => 'sometimes',
            "mobile" => "sometimes",
            "email" => "sometimes",

            'activity_plan_budget' => 'array',
            'activity_plan_budget.*.objectives' => 'sometimes',
            'activity_plan_budget.*.activities' => 'sometimes',
            'activity_plan_budget.*.outputs' => 'sometimes',
            'activity_plan_budget.*.personnel' => 'sometimes',
            'activity_plan_budget.*.budget' => 'sometimes',

            'detailed_budget' => 'array',
            'detailed_budget.*.item' => 'sometimes',
            'detailed_budget.*.details' => 'sometimes',
            'detailed_budget.*.quantity' => 'sometimes',
            'detailed_budget.*.amount' => 'sometimes',
            'detailed_budget.*.total' => 'sometimes',

            'budget_sourcing' => 'array',
            'budget_sourcing.*.university' => 'sometimes',
            'budget_sourcing.*.outreachGroup' => 'sometimes',
            'budget_sourcing.*.service' => 'sometimes',
            'budget_sourcing.*.other' => 'sometimes',
            'budget_sourcing.*.total' => 'sometimes',
        ]);

        try {

            $outreach_proposal = OutreachProposal::create([
                'title' => $request->title,
                'description' => $request->description,
                'targetGroup' => $request->targetGroup,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
                'projectLeader' => $request->projectLeader,
                'mobile' => $request->mobile,
                'email' => $request->email
            ]);

            foreach ($request->activity_plan_budget as $activity_budget) {
                OutreachActivityPlansBudget::create([
                    'outreach_proposals_id' => $outreach_proposal->id,
                    'objectives' => $activity_budget['objectives'],
                    'activities' => $activity_budget['activities'],
                    'outputs' => $activity_budget['outputs'],
                    'personnel' => $activity_budget['personnel'],
                    'budget' => $activity_budget['budget'],
                ]);
            }
            foreach ($request->detailed_budget as $detail_budget) {
                OutreachDetailedBudget::create([
                    'outreach_proposals_id' => $outreach_proposal->id,
                    'item' => $detail_budget['item'],
                    'details' => $detail_budget['details'],
                    'quantity' => $detail_budget['quantity'],
                    'amount' => $detail_budget['amount'],
                    'total' => $detail_budget['total'],
                ]);
            }
            foreach ($request->budget_sourcing as $budget_source) {
                OutreachBudgetSourcing::create([
                    'outreach_proposals_id' => $outreach_proposal->id,
                    'university' => $budget_source['university'],
                    'outreachGroup' => $budget_source['outreachGroup'],
                    'service' => $budget_source['service'],
                    'other' => $budget_source['other'],
                    'total' => $budget_source['total'],
                ]);
            }

            return response()->json([
                'status' => 201,
                'message' => 'new outreach proposal created'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ],  500);
        }

    }
    public function update(Request $request, $id) {
        $request->validate([
            "title" => 'sometimes',
            "description" => 'sometimes',
            'targetGroup' => 'sometimes',
            "startDate" => 'sometimes',
            "endDate" => 'sometimes',
            "projectLeader" => 'sometimes',
            "mobile" => "sometimes",
            "email" => "sometimes|email",

            // activity_plan_budget
            'activity_plan_budget' => 'array',
            'activity_plan_budget.*.id' => 'sometimes|integer|exists:outreach_activity_plans_budgets,id',
            'activity_plan_budget.*.objectives' => 'sometimes',
            'activity_plan_budget.*.activities' => 'sometimes',
            'activity_plan_budget.*.outputs' => 'sometimes',
            'activity_plan_budget.*.personnel' => 'sometimes',
            'activity_plan_budget.*.budget' => 'sometimes|numeric',

            // detailed_budget
            'detailed_budget' => 'array',
            'detailed_budget.*.id' => 'sometimes|integer|exists:outreach_detailed_budgets,id',
            'detailed_budget.*.item' => 'sometimes',
            'detailed_budget.*.details' => 'sometimes',
            'detailed_budget.*.quantity' => 'sometimes|numeric',
            'detailed_budget.*.amount' => 'sometimes|numeric',
            'detailed_budget.*.total' => 'sometimes|numeric',

            // budget_sourcing
            'budget_sourcing' => 'array',
            'budget_sourcing.*.id' => 'sometimes|integer|exists:outreach_budget_sourcings,id',
            'budget_sourcing.*.university' => 'sometimes|numeric',
            'budget_sourcing.*.outreachGroup' => 'sometimes|numeric',
            'budget_sourcing.*.service' => 'sometimes|numeric',
            'budget_sourcing.*.other' => 'sometimes|numeric',
            'budget_sourcing.*.total' => 'sometimes|numeric',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $outreach = OutreachProposal::findOrFail($id);

                // Update main proposal
                $outreach->update([
                    'title'         => $request->title,
                    'description'   => $request->description,
                    'targetGroup'   => $request->targetGroup,
                    'startDate'     => $request->startDate,
                    'endDate'       => $request->endDate,
                    'projectLeader' => $request->projectLeader,
                    'mobile'        => $request->mobile,
                    'email'         => $request->email,
                ]);

                // ========== ACTIVITY PLAN BUDGET ==========
                $keepActivityIds = [];
                foreach ($request->input('activity_plan_budget', []) as $row) {
                    $data = [
                        'outreach_proposals_id' => $outreach->id,
                        'objectives'            => $row['objectives'] ?? null,
                        'activities'            => $row['activities'] ?? null,
                        'outputs'               => $row['outputs'] ?? null,
                        'personnel'             => $row['personnel'] ?? null,
                        'budget'                => $row['budget'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = OutreachActivityPlansBudget::updateOrCreate(
                            ['id' => $row['id'], 'outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = OutreachActivityPlansBudget::create($data);
                    }
                    $keepActivityIds[] = $model->id;
                }

                if (!empty($keepActivityIds)) {
                    OutreachActivityPlansBudget::where('outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepActivityIds)
                        ->delete();
                } else {
                    OutreachActivityPlansBudget::where('outreach_proposals_id', $outreach->id)->delete();
                }

                // ========== DETAILED BUDGET ==========
                $keepDetailIds = [];
                foreach ($request->input('detailed_budget', []) as $row) {
                    $data = [
                        'outreach_proposals_id' => $outreach->id,
                        'item'                  => $row['item'] ?? null,
                        'details'               => $row['details'] ?? null,
                        'quantity'              => $row['quantity'] ?? null,
                        'amount'                => $row['amount'] ?? null,
                        'total'                 => $row['total'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = OutreachDetailedBudget::updateOrCreate(
                            ['id' => $row['id'], 'outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = OutreachDetailedBudget::create($data);
                    }
                    $keepDetailIds[] = $model->id;
                }

                if (!empty($keepDetailIds)) {
                    OutreachDetailedBudget::where('outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepDetailIds)
                        ->delete();
                } else {
                    OutreachDetailedBudget::where('outreach_proposals_id', $outreach->id)->delete();
                }

                // ========== BUDGET SOURCING ==========
                $keepSourceIds = [];
                foreach ($request->input('budget_sourcing', []) as $row) {
                    // FIX: use array [] (not {})
                    $data = [
                        'outreach_proposals_id' => $outreach->id,
                        'university'            => $row['university'] ?? null,
                        'outreachGroup'         => $row['outreachGroup'] ?? null,
                        'service'               => $row['service'] ?? null,
                        'other'                 => $row['other'] ?? null,
                        'total'                 => $row['total'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = OutreachBudgetSourcing::updateOrCreate(
                            ['id' => $row['id'], 'outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = OutreachBudgetSourcing::create($data);
                    }
                    $keepSourceIds[] = $model->id;
                }

                if (!empty($keepSourceIds)) {
                    OutreachBudgetSourcing::where('outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepSourceIds)
                        ->delete();
                } else {
                    OutreachBudgetSourcing::where('outreach_proposals_id', $outreach->id)->delete();
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'outreach proposal updated (synced) successfully',
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
