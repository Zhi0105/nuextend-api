<?php

namespace App\Http\Controllers;

use App\Models\Form3OutreachActivityPlansBudget;
use App\Models\Form3OutreachBudgetSourcing;
use App\Models\Form3OutreachDetailedBudget;
use App\Models\Form3OutreachProposal;
use App\Models\FormRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form3Controller extends Controller
{
    public function index() {
        try {
            $outreach_proposals = Form3OutreachProposal::with(
                'activityPlansBudgets',
                'detailedBudgets',
                'budgetSourcings'
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
            'event_id'  => 'sometimes',
            "description" => 'sometimes',
            'targetGroup' => 'sometimes',
            "startDate" => 'sometimes',
            "endDate" => 'sometimes',

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

            $outreach_proposal = Form3OutreachProposal::create([
                'event_id'      => $request->event_id,
                'description' => $request->description,
                'targetGroup' => $request->targetGroup,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);

            foreach ($request->activity_plan_budget as $activity_budget) {
                Form3OutreachActivityPlansBudget::create([
                    'form3_outreach_proposals_id' => $outreach_proposal->id,
                    'objectives' => $activity_budget['objectives'],
                    'activities' => $activity_budget['activities'],
                    'outputs' => $activity_budget['outputs'],
                    'personnel' => $activity_budget['personnel'],
                    'budget' => $activity_budget['budget'],
                ]);
            }
            foreach ($request->detailed_budget as $detail_budget) {
                Form3OutreachDetailedBudget::create([
                    'form3_outreach_proposals_id' => $outreach_proposal->id,
                    'item' => $detail_budget['item'],
                    'details' => $detail_budget['details'],
                    'quantity' => $detail_budget['quantity'],
                    'amount' => $detail_budget['amount'],
                    'total' => $detail_budget['total'],
                ]);
            }
            foreach ($request->budget_sourcing as $budget_source) {
                Form3OutreachBudgetSourcing::create([
                    'form3_outreach_proposals_id' => $outreach_proposal->id,
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
            "description" => 'sometimes',
            'targetGroup' => 'sometimes',
            "startDate" => 'sometimes',
            "endDate" => 'sometimes',

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
            'budget_sourcing.*.university' => 'sometimes',
            'budget_sourcing.*.outreachGroup' => 'sometimes',
            'budget_sourcing.*.service' => 'sometimes',
            'budget_sourcing.*.other' => 'sometimes',
            'budget_sourcing.*.total' => 'sometimes',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $outreach = Form3OutreachProposal::findOrFail($id);

                // Update main proposal
                $outreach->update([
                    'description'   => $request->description,
                    'targetGroup'   => $request->targetGroup,
                    'startDate'     => $request->startDate,
                    'endDate'       => $request->endDate,
                ]);

                // ========== ACTIVITY PLAN BUDGET ==========
                $keepActivityIds = [];
                foreach ($request->input('activity_plan_budget', []) as $row) {
                    $data = [
                        'form3_outreach_proposals_id' => $outreach->id,
                        'objectives'            => $row['objectives'] ?? null,
                        'activities'            => $row['activities'] ?? null,
                        'outputs'               => $row['outputs'] ?? null,
                        'personnel'             => $row['personnel'] ?? null,
                        'budget'                => $row['budget'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form3OutreachActivityPlansBudget::updateOrCreate(
                            ['id' => $row['id'], 'form3_outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = Form3OutreachActivityPlansBudget::create($data);
                    }
                    $keepActivityIds[] = $model->id;
                }
                if (!empty($keepActivityIds)) {
                    Form3OutreachActivityPlansBudget::where('form3_outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepActivityIds)
                        ->delete();
                } else {
                    Form3OutreachActivityPlansBudget::where('form3_outreach_proposals_id', $outreach->id)->delete();
                }

                // ========== DETAILED BUDGET ==========
                $keepDetailIds = [];
                foreach ($request->input('detailed_budget', []) as $row) {
                    $data = [
                        'form3_outreach_proposals_id' => $outreach->id,
                        'item'                  => $row['item'] ?? null,
                        'details'               => $row['details'] ?? null,
                        'quantity'              => $row['quantity'] ?? null,
                        'amount'                => $row['amount'] ?? null,
                        'total'                 => $row['total'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form3OutreachDetailedBudget::updateOrCreate(
                            ['id' => $row['id'], 'form3_outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = Form3OutreachDetailedBudget::create($data);
                    }
                    $keepDetailIds[] = $model->id;
                }

                if (!empty($keepDetailIds)) {
                    Form3OutreachDetailedBudget::where('form3_outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepDetailIds)
                        ->delete();
                } else {
                    Form3OutreachDetailedBudget::where('form3_outreach_proposals_id', $outreach->id)->delete();
                }

                // ========== BUDGET SOURCING ==========
                $keepSourceIds = [];
                foreach ($request->input('budget_sourcing', []) as $row) {
                    // FIX: use array [] (not {})
                    $data = [
                        'form3_outreach_proposals_id' => $outreach->id,
                        'university'            => $row['university'] ?? null,
                        'outreachGroup'         => $row['outreachGroup'] ?? null,
                        'service'               => $row['service'] ?? null,
                        'other'                 => $row['other'] ?? null,
                        'total'                 => $row['total'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form3OutreachBudgetSourcing::updateOrCreate(
                            ['id' => $row['id'], 'form3_outreach_proposals_id' => $outreach->id],
                            $data
                        );
                    } else {
                        $model = Form3OutreachBudgetSourcing::create($data);
                    }
                    $keepSourceIds[] = $model->id;
                }

                if (!empty($keepSourceIds)) {
                    Form3OutreachBudgetSourcing::where('form3_outreach_proposals_id', $outreach->id)
                        ->whereNotIn('id', $keepSourceIds)
                        ->delete();
                } else {
                    Form3OutreachBudgetSourcing::where('form3_outreach_proposals_id', $outreach->id)->delete();
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'outreach proposal updated successfully',
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function approve(Request $request) {
        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
        ]);

        try {
            $proposal = Form3OutreachProposal::find($request->id);

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
                    'commex_approved_by' => $userId, 
                    'commex_approve_date' => now()
                ],
                9  => [
                    'is_dean' => true, 
                    'dean_approved_by' => $userId, 
                    'dean_approve_date' => now()
                ],
                10 => [
                    'is_asd' => true, 
                    'asd_approved_by' => $userId, 
                    'asd_approve_date' => now()
                ],
                11 => [
                    'is_ad' => true, 
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
            $proposal = Form3OutreachProposal::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form3'; // your table name

            // Determine which flag to reset
            $roleUpdateMap = [
                1  => ['is_commex' => false],
                9  => ['is_dean' => false],
                10 => ['is_asd' => false],
                11 => ['is_ad' => false],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;

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
                'message' => 'Form 3 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
