<?php

namespace App\Http\Controllers;

use App\Models\Form2ProjectDetailedBudget;
use App\Models\Form2ProjectImpactOutcome;
use App\Models\Form2ProjectObjective;
use App\Models\Form2ProjectProposal;
use App\Models\Form2ProjectRisk;
use App\Models\Form2ProjectStaffing;
use App\Models\Form2ProjectWorkPlan;
use App\Models\FormRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form2Controller extends Controller
{
    public function index() {
        try {
            $project_proposals = Form2ProjectProposal::with(
                'eventType',
                'objectives',
                'impactOutcomes',
                'risks',
                'staffings',
                'workPlans',
                'detailedBudgets'
            )->get();

            return response()->json([
                'status' => 200,
                'data' => $project_proposals
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
            "event_type_id" => 'required',
            "proponents" => 'sometimes',
            "collaborators" => 'sometimes',
            "participants" => 'sometimes',
            "partners" => 'sometimes',
            "implementationDate" => "sometimes",
            "area" => "sometimes",
            "budgetRequirement" => "sometimes",
            "budgetRequested" => "sometimes",
            "background" => "sometimes",
            "otherInfo" => "sometimes",


            'project_objectives' => 'array',
            'project_objectives.*.objectives' => 'sometimes',
            'project_objectives.*.strategies' => 'sometimes',

            'project_impact_outcomes' => 'array',
            'project_impact_outcomes.*.impact' => 'sometimes',
            'project_impact_outcomes.*.outcome' => 'sometimes',
            'project_impact_outcomes.*.linkage' => 'sometimes',

            'project_risks' => 'array',
            'project_risks.*.risk_identification' => 'sometimes',
            'project_risks.*.risk_mitigation' => 'sometimes',

            'project_staffings' => 'array',
            'project_staffings.*.staff' => 'sometimes',
            'project_staffings.*.responsibilities' => 'sometimes',
            'project_staffings.*.contact' => 'sometimes',

            'project_work_plans' => 'array',
            'project_work_plans.*.phaseDate' => 'sometimes',
            'project_work_plans.*.activities' => 'sometimes',
            'project_work_plans.*.targets' => 'sometimes',
            'project_work_plans.*.indicators' => 'sometimes',
            'project_work_plans.*.personnel' => 'sometimes',
            'project_work_plans.*.resources' => 'sometimes',
            'project_work_plans.*.cost' => 'sometimes',

            'project_detailed_budgets' => 'array',
            'project_detailed_budgets.*.item' => 'sometimes',
            'project_detailed_budgets.*.description' => 'sometimes',
            'project_detailed_budgets.*.quantity' => 'sometimes',
            'project_detailed_budgets.*.amount' => 'sometimes',
            'project_detailed_budgets.*.source' => 'sometimes',

        ]);

        try {
            $project_proposal = Form2ProjectProposal::create([
                'event_id'      => $request->event_id,
                'event_type_id' => $request->event_type_id,
                'proponents' => $request->proponents,
                'collaborators' => $request->collaborators,
                'participants' => $request->participants,
                'partners' => $request->partners,
                'implementationDate' => $request->implementationDate,
                'area' => $request->area,
                'budgetRequirement' => $request->budgetRequirement,
                'budgetRequested' => $request->budgetRequested,
                'background' => $request->background,
                'otherInfo' => $request->otherInfo,
            ]);

            foreach ($request->project_objectives as $project_objective) {
                Form2ProjectObjective::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'objectives' => $project_objective['objectives'],
                    'strategies' => $project_objective['strategies'],
                ]);
            }
            foreach ($request->project_impact_outcomes as $project_impact_outcome) {
                Form2ProjectImpactOutcome::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'impact' => $project_impact_outcome['impact'],
                    'outcome' => $project_impact_outcome['outcome'],
                    'linkage' => $project_impact_outcome['linkage'],
                ]);
            }
            foreach ($request->project_risks as $project_risk) {
                Form2ProjectRisk::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'risk_identification' => $project_risk['risk_identification'],
                    'risk_mitigation' => $project_risk['risk_mitigation'],
                ]);
            }
            foreach ($request->project_staffings as $project_staffing) {
                Form2ProjectStaffing::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'staff' => $project_staffing['staff'],
                    'responsibilities' => $project_staffing['responsibilities'],
                    'contact' => $project_staffing['contact'],
                ]);
            }
            foreach ($request->project_work_plans as $project_work_plan) {
                Form2ProjectWorkPlan::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'phaseDate' => $project_work_plan['phaseDate'],
                    'activities' => $project_work_plan['activities'],
                    'targets' => $project_work_plan['targets'],
                    'indicators' => $project_work_plan['indicators'],
                    'personnel' => $project_work_plan['personnel'],
                    'resources' => $project_work_plan['resources'],
                    'cost' => $project_work_plan['cost'],
                ]);
            }
            foreach ($request->project_detailed_budgets as $project_detailed_budget) {
                Form2ProjectDetailedBudget::create([
                    'form2_project_proposals_id' => $project_proposal->id,
                    'item' => $project_detailed_budget['item'],
                    'description' => $project_detailed_budget['description'],
                    'quantity' => $project_detailed_budget['quantity'],
                    'amount' => $project_detailed_budget['amount'],
                    'source' => $project_detailed_budget['source'],
                ]);
            }

            return response()->json([
                'status' => 201,
                'message' => 'new project proposal created'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ],  500);
        }

    }
    public function update(Request $request, $id) {
        // Validation (same fields as create, plus optional ids for nested arrays)
        $request->validate([
            "event_type_id"     => 'sometimes|integer|exists:event_types,id',
            'proponents'        => 'sometimes',
            "collaborators"     => 'sometimes',
            "participants"      => 'sometimes',
            "partners"          => 'sometimes',
            "implementationDate"=> 'sometimes',
            "area"              => 'sometimes',
            "budgetRequirement" => 'sometimes|numeric',
            "budgetRequested"   => 'sometimes|numeric',
            "background"        => 'sometimes',
            "otherInfo"         => 'sometimes',

            // project_objectives
            'project_objectives'                 => 'sometimes|array',
            'project_objectives.*.id'            => 'sometimes|integer|exists:project_objectives,id',
            'project_objectives.*.objectives'     => 'sometimes',
            'project_objectives.*.strategies'    => 'sometimes',

            // project_impact_outcomes
            'project_impact_outcomes'                => 'sometimes|array',
            'project_impact_outcomes.*.id'           => 'sometimes|integer|exists:project_impact_outcomes,id',
            'project_impact_outcomes.*.impact'       => 'sometimes',
            'project_impact_outcomes.*.outcome'      => 'sometimes',
            'project_impact_outcomes.*.linkage'      => 'sometimes',

            // project_risks
            'project_risks'                  => 'sometimes|array',
            'project_risks.*.id'             => 'sometimes|integer|exists:project_risks,id',
            'project_risks.*.risk_identification'           => 'sometimes',
            'project_risks.*.risk_mitigation'     => 'sometimes',

            // project_staffings
            'project_staffings'                      => 'sometimes|array',
            'project_staffings.*.id'                 => 'sometimes|integer|exists:project_staffings,id',
            'project_staffings.*.staff'              => 'sometimes',
            'project_staffings.*.responsibilities'   => 'sometimes',
            'project_staffings.*.contact'            => 'sometimes',

            // project_work_plans
            'project_work_plans'                     => 'sometimes|array',
            'project_work_plans.*.id'                => 'sometimes|integer|exists:project_work_plans,id',
            'project_work_plans.*.phaseDate'         => 'sometimes',
            'project_work_plans.*.activities'        => 'sometimes',
            'project_work_plans.*.targets'           => 'sometimes',
            'project_work_plans.*.indicators'        => 'sometimes',
            'project_work_plans.*.personnel'         => 'sometimes',
            'project_work_plans.*.resources'         => 'sometimes',
            'project_work_plans.*.cost'              => 'sometimes|numeric',

            // project_detailed_budgets
            'project_detailed_budgets'               => 'sometimes|array',
            'project_detailed_budgets.*.id'          => 'sometimes|integer|exists:project_detailed_budgets,id',
            'project_detailed_budgets.*.item'        => 'sometimes',
            'project_detailed_budgets.*.description' => 'sometimes',
            'project_detailed_budgets.*.quantity'    => 'sometimes|numeric',
            'project_detailed_budgets.*.amount'      => 'sometimes|numeric',
            'project_detailed_budgets.*.source'      => 'sometimes',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $proposal = Form2ProjectProposal::findOrFail($id);
                
                $proposal->update([
                    'is_updated' => true,
                    'is_revised' => false
                ]);
                // Update main proposal
                $proposal->update([
                    'event_type_id'     => $request->event_type_id,
                    'proponents'        => $request->proponents,
                    'collaborators'     => $request->collaborators,
                    'participants'      => $request->participants,
                    'partners'          => $request->partners,
                    'implementationDate'=> $request->implementationDate,
                    'area'              => $request->area,
                    'budgetRequirement' => $request->budgetRequirement,
                    'budgetRequested'   => $request->budgetRequested,
                    'background'        => $request->background,
                    'otherInfo'         => $request->otherInfo,
                ]);

                // ---------- OBJECTIVES ----------
                $keepObjectiveIds = [];
                foreach ($request->input('project_objectives', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        // Note: your create() uses 'objectives' column fed by 'objective' key
                        'objectives'           => $row['objectives']  ?? null,
                        'strategies'           => $row['strategies'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectObjective::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectObjective::create($data);
                    }
                    $keepObjectiveIds[] = $model->id;
                }
                if (!empty($keepObjectiveIds)) {
                    Form2ProjectObjective::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepObjectiveIds)
                        ->delete();
                } else {
                    Form2ProjectObjective::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                // ---------- IMPACT & OUTCOMES ----------
                $keepImpactIds = [];
                foreach ($request->input('project_impact_outcomes', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        'impact'               => $row['impact']   ?? null,
                        'outcome'              => $row['outcome']  ?? null,
                        'linkage'              => $row['linkage']  ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectImpactOutcome::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectImpactOutcome::create($data);
                    }
                    $keepImpactIds[] = $model->id;
                }
                if (!empty($keepImpactIds)) {
                    Form2ProjectImpactOutcome::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepImpactIds)
                        ->delete();
                } else {
                    Form2ProjectImpactOutcome::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                // ---------- RISKS ----------
                $keepRiskIds = [];
                foreach ($request->input('project_risks', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        'risk_identification'       => $row['risk_identification'] ?? null,
                        'risk_mitigation'           => $row['risk_mitigation'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectRisk::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectRisk::create($data);
                    }
                    $keepRiskIds[] = $model->id;
                }
                if (!empty($keepRiskIds)) {
                    Form2ProjectRisk::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepRiskIds)
                        ->delete();
                } else {
                    Form2ProjectRisk::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                // ---------- STAFFINGS ----------
                $keepStaffIds = [];
                foreach ($request->input('project_staffings', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        'staff'                => $row['staff']            ?? null,
                        'responsibilities'     => $row['responsibilities'] ?? null,
                        'contact'              => $row['contact']          ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectStaffing::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectStaffing::create($data);
                    }
                    $keepStaffIds[] = $model->id;
                }
                if (!empty($keepStaffIds)) {
                    Form2ProjectStaffing::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepStaffIds)
                        ->delete();
                } else {
                    Form2ProjectStaffing::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                // ---------- WORK PLANS ----------
                $keepPlanIds = [];
                foreach ($request->input('project_work_plans', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        'phaseDate'            => $row['phaseDate']  ?? null,
                        'activities'           => $row['activities'] ?? null,
                        'targets'              => $row['targets']    ?? null,
                        'indicators'           => $row['indicators'] ?? null,
                        'personnel'            => $row['personnel']  ?? null,
                        'resources'            => $row['resources']  ?? null,
                        'cost'                 => $row['cost']       ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectWorkPlan::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectWorkPlan::create($data);
                    }
                    $keepPlanIds[] = $model->id;
                }
                if (!empty($keepPlanIds)) {
                    Form2ProjectWorkPlan::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepPlanIds)
                        ->delete();
                } else {
                    Form2ProjectWorkPlan::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                // ---------- DETAILED BUDGETS ----------
                $keepBudgetIds = [];
                foreach ($request->input('project_detailed_budgets', []) as $row) {
                    $data = [
                        'form2_project_proposals_id' => $proposal->id,
                        'item'                 => $row['item']        ?? null,
                        'description'          => $row['description'] ?? null,
                        'quantity'             => $row['quantity']    ?? null,
                        'amount'               => $row['amount']      ?? null,
                        'source'               => $row['source']      ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = Form2ProjectDetailedBudget::updateOrCreate(
                            ['id' => $row['id'], 'form2_project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = Form2ProjectDetailedBudget::create($data);
                    }
                    $keepBudgetIds[] = $model->id;
                }
                if (!empty($keepBudgetIds)) {
                    Form2ProjectDetailedBudget::where('form2_project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepBudgetIds)
                        ->delete();
                } else {
                    Form2ProjectDetailedBudget::where('form2_project_proposals_id', $proposal->id)->delete();
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'project proposal updated successfully',
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
            "commex_remarks" => 'sometimes|string|nullable',
            "dean_remarks" => 'sometimes|string|nullable',
            "asd_remarks" => 'sometimes|string|nullable',
            "ad_remarks" => 'sometimes|string|nullable',
        ]);

        try {
            $proposal = Form2ProjectProposal::find($request->id);

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
            $proposal = Form2ProjectProposal::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form2'; // your table name

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
                'message' => 'Form 2 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
