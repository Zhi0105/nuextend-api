<?php

namespace App\Http\Controllers;

use App\Models\Form1ProgramProposal;
use App\Models\FormRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class Form1Controller extends Controller
{
    //
    public function index() {
        try {
            $program_proposals = Form1ProgramProposal::with(
                'teamMembers',
                'cooperatingAgencies',
                'componentProjects',
                'projects',
                'projects.teamMembers',
                'projects.budgetSummaries',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'

            )->get();

            return response()->json([
                'status' => 200,
                'data' => $program_proposals
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function create(Request $request) {
        $validated = $request->validate([
            // parent
            'event_id'              => 'required|integer|exists:events,id',
            'duration'              => 'sometimes|string|max:255',
            'background'            => 'sometimes|string|nullable',
            'overall_goal'          => 'sometimes|string|nullable',
            'scholarly_connection'  => 'sometimes|string|nullable',

            // arrays of strings (proposal-level)
            'programTeamMembers'    => 'sometimes|array',
            'programTeamMembers.*'  => 'string|max:255',
            'cooperatingAgencies'   => 'sometimes|array',
            'cooperatingAgencies.*' => 'string|max:255',

            // component projects (proposal-level)
            'componentProjects'                 => 'sometimes|array',
            'componentProjects.*.title'         => 'sometimes|string|max:255|nullable',
            'componentProjects.*.outcomes'      => 'sometimes|string|nullable',
            'componentProjects.*.budget'        => 'sometimes|nullable', // keep string/decimal as per schema

            // projects (proposal-level) + nested members + nested budget summaries
            'projects'                          => 'sometimes|array',
            'projects.*.title'                  => 'sometimes|string|max:255|nullable',
            'projects.*.teamLeader'             => 'sometimes|string|max:255|nullable',
            'projects.*.objectives'             => 'sometimes|string|nullable',
            'projects.*.teamMembers'            => 'sometimes|array',
            'projects.*.teamMembers.*'          => 'string|max:255',

            // NESTED under each project
            'projects.*.budgetSummaries'                    => 'sometimes|array',
            'projects.*.budgetSummaries.*.activities'       => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.outputs'          => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.timeline'         => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.personnel'        => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.budget'           => 'sometimes|nullable',
        ]);

        $proposal = DB::transaction(function () use ($validated) {
            // 1) Proposal
            $proposal = Form1ProgramProposal::create([
                'event_id'             => $validated['event_id'],
                'duration'             => $validated['duration'] ?? null,
                'background'           => $validated['background'] ?? null,
                'overall_goal'         => $validated['overall_goal'] ?? null,
                'scholarly_connection' => $validated['scholarly_connection'] ?? null,
            ]);

            // 2) Proposal-level team members
            if (!empty($validated['programTeamMembers'])) {
                    $proposal->teamMembers()->createMany(
                        collect($validated['programTeamMembers'])
                            ->filter(fn ($n) => filled($n)) // <— drop "", null
                            ->map(fn ($n) => ['name' => $n])
                            ->values()
                            ->all()
                    );
                }

            // 3) Cooperating agencies
            if (!empty($validated['cooperatingAgencies'])) {
                $proposal->cooperatingAgencies()->createMany(
                    collect($validated['cooperatingAgencies'])
                        ->map(fn ($n) => ['name' => $n])
                        ->all()
                );
            }

            // 4) Component projects
            if (!empty($validated['componentProjects'])) {
                $proposal->componentProjects()->createMany(
                    collect($validated['componentProjects'])->map(function ($cp) {
                        return [
                            'title'    => $cp['title'] ?? null,
                            'outcomes' => $cp['outcomes'] ?? null,
                            'budget'   => $cp['budget'] ?? null, // keep as string if schema is string
                        ];
                    })->all()
                );
            }

            // 5) Projects (+ nested team members + nested budget summaries)
            if (!empty($validated['projects'])) {
                foreach ($validated['projects'] as $proj) {
                    $projModel = $proposal->projects()->create([
                        'title'       => $proj['title'] ?? null,
                        'teamLeader'  => $proj['teamLeader'] ?? null,
                        'objectives'  => $proj['objectives'] ?? null,
                    ]);

                    // project team members
                    if (!empty($proj['teamMembers']) && is_array($proj['teamMembers'])) {
                        $projModel->teamMembers()->createMany(
                            collect($proj['teamMembers'])
                                ->filter(fn ($n) => filled($n)) // <— drop "", null
                                ->map(fn ($n) => ['name' => $n])
                                ->values()
                                ->all()
                        );
                    }
                    // project budget summaries (IMPORTANT: now under project)
                    if (!empty($proj['budgetSummaries']) && is_array($proj['budgetSummaries'])) {
                        $rows = collect($proj['budgetSummaries'])
                            ->map(function ($bs) {
                                // Optional: coerce date and budget
                                $timeline = $bs['timeline'] ?? null;  // expecting 'YYYY-MM-DD' string already
                                $budget   = $bs['budget'] ?? null;    // keep as nullable decimal/string

                                return [
                                    'activities' => $bs['activities'] ?? null,
                                    'outputs'    => $bs['outputs'] ?? null,
                                    'timeline'   => $timeline,
                                    'personnel'  => $bs['personnel'] ?? null,
                                    'budget'     => $budget,
                                ];
                            })
                            // drop rows that are entirely empty
                            ->filter(fn ($r) =>
                                filled($r['activities'])
                                || filled($r['outputs'])
                                || filled($r['timeline'])
                                || filled($r['personnel'])
                                || filled($r['budget'])
                            )
                            ->values();

                        if ($rows->isNotEmpty()) {
                            $projModel->budgetSummaries()->createMany($rows->all());
                        }
                    }
                }
            }

            return $proposal;
        });

        return response()->json([
            'message' => 'Program proposal created',
            'data' => $proposal->load([
                'teamMembers',
                'cooperatingAgencies',
                'componentProjects',
                'projects',
                'projects.teamMembers',
                'projects.budgetSummaries', // <- moved here
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover',
            ]),
        ], 201);
    }
    public function update(Request $request, $id) {
        $validated = $request->validate([
            // ---- Parent fields ----
            'duration'              => 'sometimes|string|max:255|nullable',
            'background'            => 'sometimes|string|nullable',
            'overall_goal'          => 'sometimes|string|nullable',
            'scholarly_connection'  => 'sometimes|string|nullable',

            // ---- Proposal-level team members (strings OR {id,name,_delete}) ----
            'programTeamMembers'                => 'sometimes|array',
            'programTeamMembers.*'              => 'nullable',
            'programTeamMembers.*.id'           => 'sometimes|integer|exists:form1_program_team_members,id',
            'programTeamMembers.*.name'         => 'sometimes|string|max:255|nullable',
            'programTeamMembers.*._delete'      => 'sometimes|boolean',

            // ---- Cooperating agencies (strings OR {id,name,_delete}) ----
            'cooperatingAgencies'               => 'sometimes|array',
            'cooperatingAgencies.*'             => 'nullable',
            'cooperatingAgencies.*.id'          => 'sometimes|integer|exists:form1_cooperating_agencies,id',
            'cooperatingAgencies.*.name'        => 'sometimes|string|max:255|nullable',
            'cooperatingAgencies.*._delete'     => 'sometimes|boolean',

            // ---- Component projects ----
            'componentProjects'                             => 'sometimes|array',
            'componentProjects.*.id'                        => 'sometimes|integer|exists:form1_component_projects,id',
            'componentProjects.*.title'                     => 'sometimes|string|max:255|nullable',
            'componentProjects.*.outcomes'                  => 'sometimes|string|nullable',
            'componentProjects.*.budget'                    => 'sometimes|nullable',
            'componentProjects.*._delete'                   => 'sometimes|boolean',

            // ---- Projects (+ nested teamMembers + nested budgetSummaries) ----
            'projects'                           => 'sometimes|array',
            'projects.*.id'                      => 'sometimes|integer|exists:form1_projects,id',
            'projects.*.title'                   => 'sometimes|string|max:255|nullable',
            'projects.*.teamLeader'              => 'sometimes|string|max:255|nullable',
            'projects.*.objectives'              => 'sometimes|string|nullable',
            'projects.*._delete'                 => 'sometimes|boolean',

            // project team members
            'projects.*.teamMembers'             => 'sometimes|array',
            'projects.*.teamMembers.*'           => 'nullable',
            'projects.*.teamMembers.*.id'        => 'sometimes|integer|exists:form1_project_team_members,id',
            'projects.*.teamMembers.*.name'      => 'sometimes|string|max:255|nullable',
            'projects.*.teamMembers.*._delete'   => 'sometimes|boolean',

            // ---- project budget summaries (MOVED UNDER PROJECT) ----
            'projects.*.budgetSummaries'                     => 'sometimes|array',
            'projects.*.budgetSummaries.*'                   => 'nullable',
            'projects.*.budgetSummaries.*.id'                => 'sometimes|integer|exists:form1_project_budget_summary,id',
            'projects.*.budgetSummaries.*.activities'        => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.outputs'           => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.timeline'          => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.personnel'         => 'sometimes|nullable',
            'projects.*.budgetSummaries.*.budget'            => 'sometimes|nullable',
            'projects.*.budgetSummaries.*._delete'           => 'sometimes|boolean',
        ]);

        $proposal = DB::transaction(function () use ($validated, $id) {
            $proposal = Form1ProgramProposal::findOrFail($id);

            // Update only parent columns (avoid tossing arrays into update())
            $proposal->update(Arr::only($validated, [
                'duration','background','overall_goal','scholarly_connection'
            ]));

            // ---- PROGRAM TEAM MEMBERS ----
            if (array_key_exists('programTeamMembers', $validated)) {
                $keepIds = [];
                foreach ($validated['programTeamMembers'] as $row) {
                    if (is_string($row)) $row = ['name' => $row];

                    if (!empty($row['_delete']) && !empty($row['id'])) {
                        $proposal->teamMembers()->whereKey($row['id'])->delete();
                        continue;
                    }

                    $payload = ['name' => $row['name'] ?? null];

                    if (!empty($row['id'])) {
                        $proposal->teamMembers()->whereKey($row['id'])->update($payload);
                        $keepIds[] = $row['id'];
                    } else {
                        $new = $proposal->teamMembers()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->teamMembers()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---- COOPERATING AGENCIES ----
            if (array_key_exists('cooperatingAgencies', $validated)) {
                $keepIds = [];
                foreach ($validated['cooperatingAgencies'] as $row) {
                    if (is_string($row)) $row = ['name' => $row];

                    if (!empty($row['_delete']) && !empty($row['id'])) {
                        $proposal->cooperatingAgencies()->whereKey($row['id'])->delete();
                        continue;
                    }

                    $payload = ['name' => $row['name'] ?? null];

                    if (!empty($row['id'])) {
                        $proposal->cooperatingAgencies()->whereKey($row['id'])->update($payload);
                        $keepIds[] = $row['id'];
                    } else {
                        $new = $proposal->cooperatingAgencies()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->cooperatingAgencies()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---- COMPONENT PROJECTS ----
            if (array_key_exists('componentProjects', $validated)) {
                $keepIds = [];
                foreach ($validated['componentProjects'] as $cp) {
                    if (!empty($cp['_delete']) && !empty($cp['id'])) {
                        $proposal->componentProjects()->whereKey($cp['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'title'    => $cp['title']    ?? null,
                        'outcomes' => $cp['outcomes'] ?? null,
                        'budget'   => $cp['budget']   ?? null,
                    ];

                    if (!empty($cp['id'])) {
                        $proposal->componentProjects()->whereKey($cp['id'])->update($payload);
                        $keepIds[] = $cp['id'];
                    } else {
                        $new = $proposal->componentProjects()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->componentProjects()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---- PROJECTS (+ nested TEAM MEMBERS + nested BUDGET SUMMARIES) ----
            if (array_key_exists('projects', $validated)) {
                $keepProjectIds = [];

                foreach ($validated['projects'] as $proj) {
                    if (!empty($proj['_delete']) && !empty($proj['id'])) {
                        // delete project + its members + its budget summaries
                        $proposal->projects()->whereKey($proj['id'])->each(function ($p) {
                            $p->teamMembers()->delete();
                            $p->budgetSummaries()->delete();
                            $p->delete();
                        });
                        continue;
                    }

                    $projPayload = [
                        'title'       => $proj['title'] ?? null,
                        'teamLeader'  => $proj['teamLeader'] ?? null,
                        'objectives'  => $proj['objectives'] ?? null,
                    ];

                    if (!empty($proj['id'])) {
                        $proposal->projects()->whereKey($proj['id'])->update($projPayload);
                        $projModel = $proposal->projects()->find($proj['id']);
                    } else {
                        $projModel = $proposal->projects()->create($projPayload);
                    }

                    $keepProjectIds[] = $projModel->id;

                    // ---- nested team members ----
                    if (array_key_exists('teamMembers', $proj)) {
                        $keepMemberIds = [];
                        foreach ($proj['teamMembers'] as $m) {
                            if (is_string($m)) $m = ['name' => $m];

                            if (!empty($m['_delete']) && !empty($m['id'])) {
                                $projModel->teamMembers()->whereKey($m['id'])->delete();
                                continue;
                            }

                            $payload = ['name' => $m['name'] ?? null];

                            if (!empty($m['id'])) {
                                $projModel->teamMembers()->whereKey($m['id'])->update($payload);
                                $keepMemberIds[] = $m['id'];
                            } else {
                                $new = $projModel->teamMembers()->create($payload);
                                $keepMemberIds[] = $new->id;
                            }
                        }
                        $projModel->teamMembers()->whereNotIn('id', $keepMemberIds ?: [0])->delete();
                    }

                    // ---- nested budget summaries (IMPORTANT: project-level) ----
                    if (array_key_exists('budgetSummaries', $proj)) {
                        $keepBudgetIds = [];
                        foreach ($proj['budgetSummaries'] as $bs) {
                            if (!empty($bs['_delete']) && !empty($bs['id'])) {
                                $projModel->budgetSummaries()->whereKey($bs['id'])->delete();
                                continue;
                            }

                            $payload = [
                                'activities' => $bs['activities'] ?? null,
                                'outputs'    => $bs['outputs'] ?? null,
                                'timeline'   => $bs['timeline'] ?? null,
                                'personnel'  => $bs['personnel'] ?? null,
                                'budget'     => $bs['budget'] ?? null,
                            ];

                            if (!empty($bs['id'])) {
                                $projModel->budgetSummaries()->whereKey($bs['id'])->update($payload);
                                $keepBudgetIds[] = $bs['id'];
                            } else {
                                $new = $projModel->budgetSummaries()->create($payload);
                                $keepBudgetIds[] = $new->id;
                            }
                        }
                        $projModel->budgetSummaries()->whereNotIn('id', $keepBudgetIds ?: [0])->delete();
                    }
                }

                // prune projects not present (also delete their nested children)
                $proposal->projects()->whereNotIn('id', $keepProjectIds ?: [0])->get()->each(function ($p) {
                    $p->teamMembers()->delete();
                    $p->budgetSummaries()->delete();
                    $p->delete();
                });
            }

            return $proposal;
        });

        return response()->json([
            'message' => 'Program proposal updated',
            'data' => $proposal->load([
                'teamMembers',
                'cooperatingAgencies',
                'componentProjects',
                'projects',
                'projects.teamMembers',
                'projects.budgetSummaries', // <-- now correct
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover',
            ]),
        ], 200);
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
            $proposal = Form1ProgramProposal::find($request->id);

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
            $proposal = Form1ProgramProposal::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form1'; // your table name

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
                'message' => 'Form 1 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
