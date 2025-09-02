<?php

namespace App\Http\Controllers;

use App\Models\ProjectDetailedBudget;
use App\Models\ProjectImpactOutcome;
use App\Models\ProjectObjective;
use App\Models\ProjectProposal;
use App\Models\ProjectRisk;
use App\Models\ProjectStaffing;
use App\Models\ProjectWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectProposalController extends Controller
{
    public function index() {
        try {
            $project_proposals = ProjectProposal::with(
                'EventType',
                'ProjectObjectives',
                'ProjectImpactOutcomes',
                'ProjectRisks',
                'ProjectStaffings',
                'ProjectWorkPlans',
                'ProjectDetailedBudgets'
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
            "event_type_id" => 'required',
            "projectTitle" => 'sometimes',
            'proponents' => 'sometimes',
            "collaborators" => 'sometimes',
            "participants" => 'sometimes',
            "partners" => 'sometimes',
            "implementationDate" => "sometimes",
            "durationHours" => "sometimes",
            "area" => "sometimes",
            "budgetRequirement" => "sometimes",
            "budgetRequested" => "sometimes",
            "background" => "sometimes",
            "otherInfo" => "sometimes",
            "projectLeader" => "sometimes",
            "mobile" => "sometimes",
            "email" => "sometimes",


            'project_objectives' => 'array',
            'project_objectives.*.objective' => 'sometimes',
            'project_objectives.*.strategies' => 'sometimes',

            'project_impact_outcomes' => 'array',
            'project_impact_outcomes.*.impact' => 'sometimes',
            'project_impact_outcomes.*.outcome' => 'sometimes',
            'project_impact_outcomes.*.linkage' => 'sometimes',

            'project_risks' => 'array',
            'project_risks.*.risk' => 'sometimes',
            'project_risks.*.mitigation' => 'sometimes',

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
            $project_proposal = ProjectProposal::create([
                'event_type_id' => $request->event_type_id,
                'projectTitle' => $request->projectTitle,
                'proponents' => $request->proponents,
                'collaborators' => $request->collaborators,
                'participants' => $request->participants,
                'partners' => $request->partners,
                'implementationDate' => $request->implementationDate,
                'durationHours' => $request->durationHours,
                'area' => $request->area,
                'budgetRequirement' => $request->budgetRequirement,
                'budgetRequested' => $request->budgetRequested,
                'background' => $request->background,
                'otherInfo' => $request->otherInfo,
                'projectLeader' => $request->projectLeader,
                'mobile' => $request->mobile,
                'email' => $request->email,
            ]);

            foreach ($request->project_objectives as $project_objective) {
                ProjectObjective::create([
                    'project_proposals_id' => $project_proposal->id,
                    'objective' => $project_objective['objective'],
                    'strategies' => $project_objective['strategies'],
                ]);
            }
            foreach ($request->project_impact_outcomes as $project_impact_outcome) {
                ProjectImpactOutcome::create([
                    'project_proposals_id' => $project_proposal->id,
                    'impact' => $project_impact_outcome['impact'],
                    'outcome' => $project_impact_outcome['outcome'],
                    'linkage' => $project_impact_outcome['linkage'],
                ]);
            }
            foreach ($request->project_risks as $project_risk) {
                ProjectRisk::create([
                    'project_proposals_id' => $project_proposal->id,
                    'risk' => $project_risk['risk'],
                    'mitigation' => $project_risk['mitigation'],
                ]);
            }
            foreach ($request->project_staffings as $project_staffing) {
                ProjectStaffing::create([
                    'project_proposals_id' => $project_proposal->id,
                    'staff' => $project_staffing['staff'],
                    'responsibilities' => $project_staffing['responsibilities'],
                    'contact' => $project_staffing['contact'],
                ]);
            }
            foreach ($request->project_work_plans as $project_work_plan) {
                ProjectWorkPlan::create([
                    'project_proposals_id' => $project_proposal->id,
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
                ProjectDetailedBudget::create([
                    'project_proposals_id' => $project_proposal->id,
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
            "projectTitle"      => 'sometimes',
            'proponents'        => 'sometimes',
            "collaborators"     => 'sometimes',
            "participants"      => 'sometimes',
            "partners"          => 'sometimes',
            "implementationDate"=> 'sometimes',
            "durationHours"     => 'sometimes|numeric',
            "area"              => 'sometimes',
            "budgetRequirement" => 'sometimes|numeric',
            "budgetRequested"   => 'sometimes|numeric',
            "background"        => 'sometimes',
            "otherInfo"         => 'sometimes',
            "projectLeader"     => 'sometimes',
            "mobile"            => 'sometimes',
            "email"             => 'sometimes|email',

            // project_objectives
            'project_objectives'                 => 'sometimes|array',
            'project_objectives.*.id'            => 'sometimes|integer|exists:project_objectives,id',
            'project_objectives.*.objective'     => 'sometimes',
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
            'project_risks.*.risk'           => 'sometimes',
            'project_risks.*.mitigation'     => 'sometimes',

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
                $proposal = ProjectProposal::findOrFail($id);

                // Update main proposal
                $proposal->update([
                    'event_type_id'     => $request->event_type_id,
                    'projectTitle'      => $request->projectTitle,
                    'proponents'        => $request->proponents,
                    'collaborators'     => $request->collaborators,
                    'participants'      => $request->participants,
                    'partners'          => $request->partners,
                    'implementationDate'=> $request->implementationDate,
                    'durationHours'     => $request->durationHours,
                    'area'              => $request->area,
                    'budgetRequirement' => $request->budgetRequirement,
                    'budgetRequested'   => $request->budgetRequested,
                    'background'        => $request->background,
                    'otherInfo'         => $request->otherInfo,
                    'projectLeader'     => $request->projectLeader,
                    'mobile'            => $request->mobile,
                    'email'             => $request->email,
                ]);

                // ---------- OBJECTIVES ----------
                $keepObjectiveIds = [];
                foreach ($request->input('project_objectives', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        // Note: your create() uses 'objectives' column fed by 'objective' key
                        'objective'           => $row['objective']  ?? null,
                        'strategies'           => $row['strategies'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectObjective::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectObjective::create($data);
                    }
                    $keepObjectiveIds[] = $model->id;
                }
                if (!empty($keepObjectiveIds)) {
                    ProjectObjective::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepObjectiveIds)
                        ->delete();
                } else {
                    ProjectObjective::where('project_proposals_id', $proposal->id)->delete();
                }

                // ---------- IMPACT & OUTCOMES ----------
                $keepImpactIds = [];
                foreach ($request->input('project_impact_outcomes', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        'impact'               => $row['impact']   ?? null,
                        'outcome'              => $row['outcome']  ?? null,
                        'linkage'              => $row['linkage']  ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectImpactOutcome::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectImpactOutcome::create($data);
                    }
                    $keepImpactIds[] = $model->id;
                }
                if (!empty($keepImpactIds)) {
                    ProjectImpactOutcome::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepImpactIds)
                        ->delete();
                } else {
                    ProjectImpactOutcome::where('project_proposals_id', $proposal->id)->delete();
                }

                // ---------- RISKS ----------
                $keepRiskIds = [];
                foreach ($request->input('project_risks', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        'risk'                 => $row['risk']       ?? null,
                        'mitigation'           => $row['mitigation'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectRisk::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectRisk::create($data);
                    }
                    $keepRiskIds[] = $model->id;
                }
                if (!empty($keepRiskIds)) {
                    ProjectRisk::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepRiskIds)
                        ->delete();
                } else {
                    ProjectRisk::where('project_proposals_id', $proposal->id)->delete();
                }

                // ---------- STAFFINGS ----------
                $keepStaffIds = [];
                foreach ($request->input('project_staffings', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        'staff'                => $row['staff']            ?? null,
                        'responsibilities'     => $row['responsibilities'] ?? null,
                        'contact'              => $row['contact']          ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectStaffing::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectStaffing::create($data);
                    }
                    $keepStaffIds[] = $model->id;
                }
                if (!empty($keepStaffIds)) {
                    ProjectStaffing::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepStaffIds)
                        ->delete();
                } else {
                    ProjectStaffing::where('project_proposals_id', $proposal->id)->delete();
                }

                // ---------- WORK PLANS ----------
                $keepPlanIds = [];
                foreach ($request->input('project_work_plans', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        'phaseDate'            => $row['phaseDate']  ?? null,
                        'activities'           => $row['activities'] ?? null,
                        'targets'              => $row['targets']    ?? null,
                        'indicators'           => $row['indicators'] ?? null,
                        'personnel'            => $row['personnel']  ?? null,
                        'resources'            => $row['resources']  ?? null,
                        'cost'                 => $row['cost']       ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectWorkPlan::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectWorkPlan::create($data);
                    }
                    $keepPlanIds[] = $model->id;
                }
                if (!empty($keepPlanIds)) {
                    ProjectWorkPlan::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepPlanIds)
                        ->delete();
                } else {
                    ProjectWorkPlan::where('project_proposals_id', $proposal->id)->delete();
                }

                // ---------- DETAILED BUDGETS ----------
                $keepBudgetIds = [];
                foreach ($request->input('project_detailed_budgets', []) as $row) {
                    $data = [
                        'project_proposals_id' => $proposal->id,
                        'item'                 => $row['item']        ?? null,
                        'description'          => $row['description'] ?? null,
                        'quantity'             => $row['quantity']    ?? null,
                        'amount'               => $row['amount']      ?? null,
                        'source'               => $row['source']      ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $model = ProjectDetailedBudget::updateOrCreate(
                            ['id' => $row['id'], 'project_proposals_id' => $proposal->id],
                            $data
                        );
                    } else {
                        $model = ProjectDetailedBudget::create($data);
                    }
                    $keepBudgetIds[] = $model->id;
                }
                if (!empty($keepBudgetIds)) {
                    ProjectDetailedBudget::where('project_proposals_id', $proposal->id)
                        ->whereNotIn('id', $keepBudgetIds)
                        ->delete();
                } else {
                    ProjectDetailedBudget::where('project_proposals_id', $proposal->id)->delete();
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'project proposal updated  successfully',
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
