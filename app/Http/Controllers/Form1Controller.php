<?php

namespace App\Http\Controllers;

use App\Models\Form1ProgramProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'budgetSummaries',
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
            'duration'            => 'sometimes|string|max:255',
            'background'          => 'sometimes|string',
            'overall_goal'         => 'sometimes|string',
            'scholarly_connection' => 'sometimes|string',


            'programTeamMembers'   => 'sometimes|array',
            'programTeamMembers.*' => 'string|max:255',


            'cooperatingAgencies'   => 'sometimes|array',
            'cooperatingAgencies.*' => 'string|max:255',

            // componentProjects: array of objects
            'componentProjects'                           => 'sometimes|array',
            'componentProjects.*.title'   => 'sometimes|string|max:255',
            'componentProjects.*.outcomes'                => 'sometimes|string',
            'componentProjects.*.budget'                  => 'sometimes', // numeric|string

            // projects: array of objects
            'projects'                 => 'sometimes|array',
            'projects.*.title'  => 'sometimes|string|max:255',
            'projects.*.teamLeader'    => 'sometimes|string|max:255',
            'projects.*.teamMembers'   => 'sometimes|array',
            'projects.*.teamMembers.*' => 'string|max:255',
            'projects.*.objectives'    => 'sometimes|string',

            // activityPlans: array of objects
            'budgetSummaries'                => 'sometimes|array',
            'budgetSummaries.*.activities'     => 'sometimes',
            'budgetSummaries.*.outputs'      => 'sometimes',
            'budgetSummaries.*.timeline'     => 'sometimes',
            'budgetSummaries.*.personnel'    => 'sometimes',
        ]);

        $proposal = DB::transaction(function () use ($validated) {
            // 1) Create parent
            $proposal = Form1ProgramProposal::create([
                'duration'            => $validated['duration'] ?? null,
                'background'          => $validated['background'] ?? null,
                'overall_goal'         => $validated['overall_goal'] ?? null,
                'scholarly_connection' => $validated['scholarly_connection'] ?? null
            ]);

            // 2) Arrays of strings: Team Members & Cooperating Agencies
            if (!empty($validated['programTeamMembers'])) {
                $proposal->teamMembers()->createMany(
                    collect($validated['programTeamMembers'])->map(fn($n)=>['name'=>$n])->all()
                );
            }
            if (!empty($validated['cooperatingAgencies'])) {
                $proposal->cooperatingAgencies()->createMany(
                    collect($validated['cooperatingAgencies'])->map(fn($n)=>['name'=>$n])->all()
                );
            }

            // 3) componentProjects: array of objects
            if (!empty($validated['componentProjects'])) {
                $rows = collect($validated['componentProjects'])->map(function ($cp) {
                    return [
                        'title' => $cp['title'] ?? null,
                        'outcomes'              => $cp['outcomes'] ?? null,
                        'budget'                => $cp['budget'] ?? null,
                    ];
                })->all();

                $proposal->componentProjects()->createMany($rows);
            }
            // 4) projects: array of objects + nested teamMembers
            if (!empty($validated['projects'])) {
                foreach ($validated['projects'] as $proj) {
                    $projModel = $proposal->projects()->create([
                        'title' => $proj['title'] ?? null,
                        'teamLeader'   => $proj['teamLeader'] ?? null,
                        'objectives'   => $proj['objectives'] ?? null,
                    ]);

                    // if may nested teamMembers (strings)
                    if (!empty($proj['teamMembers']) && is_array($proj['teamMembers'])) {
                        // You’ll need a ProgramProjectMember model & relation (see below)
                        $projModel->teamMembers()->createMany(
                            collect($proj['teamMembers'])->map(fn($n)=>['name'=>$n])->all()
                        );
                    }
                }
            }

            // 5) activityPlans: array of objects
            if (!empty($validated['budgetSummaries'])) {
                $rows = collect($validated['budgetSummaries'])->map(function ($ap) {
                    return [
                        'activities'  => $ap['activities'] ?? null,
                        'outputs'   => $ap['outputs'] ?? null,
                        'timeline'  => $ap['timeline'] ?? null,
                        'personnel' => $ap['personnel'] ?? null,
                    ];
                })->all();

                $proposal->budgetSummaries()->createMany($rows);
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
                'budgetSummaries',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'
            ]),
        ], 201);
    }
    public function update(Request $request, $id) {
        // 1) Validate (pareho ng fields mo; dinagdagan ko ng *_delete at flexible team members / agencies)
        $validated = $request->validate([
            // parent fields
            'duration'            => 'sometimes|string|max:255',
            'background'          => 'sometimes|string',
            'overall_goal'         => 'sometimes|string',
            'scholarly_connection' => 'sometimes|string',

            // arrays of strings OR objects
            'programTeamMembers'               => 'sometimes|array',
            'programTeamMembers.*'             => 'nullable',
            'programTeamMembers.*.id'          => 'sometimes|integer|exists:program_team_members,id',
            'programTeamMembers.*.name'        => 'sometimes|string|max:255',
            'programTeamMembers.*._delete'     => 'sometimes|boolean',

            'cooperatingAgencies'              => 'sometimes|array',
            'cooperatingAgencies.*'            => 'nullable',
            'cooperatingAgencies.*.id'         => 'sometimes|integer|exists:program_cooperating_agencies,id',
            'cooperatingAgencies.*.name'       => 'sometimes|string|max:255',
            'cooperatingAgencies.*._delete'    => 'sometimes|boolean',

            // componentProjects
            'componentProjects'                             => 'sometimes|array',
            'componentProjects.*.id'                        => 'sometimes|integer|exists:program_component_projects,id',
            'componentProjects.*.title'     => 'sometimes|string|max:255',
            'componentProjects.*.outcomes'                  => 'sometimes|string',
            'componentProjects.*.budget'                    => 'sometimes',
            'componentProjects.*._delete'                   => 'sometimes|boolean',

            // projects (+ nested teamMembers)
            'projects'                          => 'sometimes|array',
            'projects.*.id'                     => 'sometimes|integer|exists:program_projects,id',
            'projects.*.title'           => 'sometimes|string|max:255',
            'projects.*.teamLeader'             => 'sometimes|string|max:255',
            'projects.*.objectives'             => 'sometimes|string',
            'projects.*._delete'                => 'sometimes|boolean',

            'projects.*.teamMembers'            => 'sometimes|array',
            'projects.*.teamMembers.*'          => 'nullable',
            'projects.*.teamMembers.*.id'       => 'sometimes|integer|exists:program_project_team_members,id',
            'projects.*.teamMembers.*.name'     => 'sometimes|string|max:255',
            'projects.*.teamMembers.*._delete'  => 'sometimes|boolean',

            // activityPlans
            'budgetSummaries'                 => 'sometimes|array',
            'budgetSummaries.*.id'            => 'sometimes|integer|exists:program_activity_plans,id',
            'budgetSummaries.*.activities'      => 'sometimes|string|max:255',
            'budgetSummaries.*.outputs'       => 'sometimes|string',
            'budgetSummaries.*.timeline'      => 'sometimes|string|max:255',
            'budgetSummaries.*.personnel'     => 'sometimes|string',
            'budgetSummaries.*._delete'       => 'sometimes|boolean',
        ]);

        $proposal = DB::transaction(function () use ($validated, $id) {
            $proposal = Form1ProgramProposal::findOrFail($id);

            // 2) Update parent (safe kung fillable ang mga keys)
            $proposal->update($validated);

            // ---------- PROGRAM TEAM MEMBERS (accepts strings OR {id,name,_delete}) ----------
            if (array_key_exists('programTeamMembers', $validated)) {
                $keepIds = [];
                foreach ($validated['programTeamMembers'] as $row) {
                    // normalize: allow plain string item
                    if (is_string($row)) {
                        $row = ['name' => $row];
                    }
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

            // // ---------- COOPERATING AGENCIES (also strings OR objects) ----------
            if (array_key_exists('cooperatingAgencies', $validated)) {
                $keepIds = [];
                foreach ($validated['cooperatingAgencies'] as $row) {
                    if (is_string($row)) {
                        $row = ['name' => $row];
                    }
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

            // ---------- COMPONENT PROJECTS ----------
            if (array_key_exists('componentProjects', $validated)) {
                $keepIds = [];
                foreach ($validated['componentProjects'] as $cp) {
                    if (!empty($cp['_delete']) && !empty($cp['id'])) {
                        $proposal->componentProjects()->whereKey($cp['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'title' => $cp['title'] ?? null,
                        'outcomes'              => $cp['outcomes'] ?? null,
                        'budget'                => $cp['budget'] ?? null,
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

            // ---------- PROJECTS (+ nested TEAM MEMBERS) ----------
            if (array_key_exists('projects', $validated)) {
                $keepProjectIds = [];

                foreach ($validated['projects'] as $proj) {
                    if (!empty($proj['_delete']) && !empty($proj['id'])) {
                        // delete project + its members
                        $proposal->projects()->whereKey($proj['id'])->each(function ($p) {
                            $p->teamMembers()->delete();
                            $p->delete();
                        });
                        continue;
                    }

                    $projPayload = [
                        'title' => $proj['title'] ?? null,
                        'teamLeader'   => $proj['teamLeader'] ?? null,
                        'objectives'   => $proj['objectives'] ?? null,
                    ];

                    if (!empty($proj['id'])) {
                        $proposal->projects()->whereKey($proj['id'])->update($projPayload);
                        $projModel = $proposal->projects()->find($proj['id']);
                    } else {
                        $projModel = $proposal->projects()->create($projPayload);
                    }

                    $keepProjectIds[] = $projModel->id;

                    // nested team members: accept strings OR {id,name,_delete}; do full prune style
                    if (array_key_exists('teamMembers', $proj)) {
                        $keepMemberIds = [];
                        foreach ($proj['teamMembers'] as $m) {
                            if (is_string($m)) {
                                $m = ['name' => $m];
                            }
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
                }

                // prune projects not present; cascade delete members
                $proposal->projects()->whereNotIn('id', $keepProjectIds ?: [0])->get()
                    ->each(function ($p) {
                        $p->teamMembers()->delete();
                        $p->delete();
                    });
            }

            // ---------- ACTIVITY PLANS ----------
            if (array_key_exists('budgetSummaries', $validated)) {
                $keepIds = [];
                foreach ($validated['budgetSummaries'] as $ap) {
                    if (!empty($ap['_delete']) && !empty($ap['id'])) {
                        $proposal->budgetSummaries()->whereKey($ap['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'activities'  => $ap['activities']  ?? null,
                        'outputs'   => $ap['outputs']   ?? null,
                        'timeline'  => $ap['timeline']  ?? null,
                        'personnel' => $ap['personnel'] ?? null,
                    ];

                    if (!empty($ap['id'])) {
                        $proposal->budgetSummaries()->whereKey($ap['id'])->update($payload);
                        $keepIds[] = $ap['id'];
                    } else {
                        $new = $proposal->budgetSummaries()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->budgetSummaries()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            return $proposal;
        });

        // 3) Load relationships for client-side sync
        return response()->json([
            'message' => 'Program proposal updated',
            'data' => $proposal->load([
                'teamMembers',
                'cooperatingAgencies',
                'componentProjects',
                'projects',
                'projects.teamMembers',
                'budgetSummaries',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'
            ]),
        ], 200);
    }
}
