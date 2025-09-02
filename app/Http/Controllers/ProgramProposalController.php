<?php

namespace App\Http\Controllers;

use App\Models\ProgramProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramProposalController extends Controller
{
    public function index() {
        try {
            $program_proposals = ProgramProposal::with(
                'ProgramTeamMembers',
                'ProgramCooperatingAgencies',
                'ProgramComponentProjects',
                'ProgramProjects',
                'ProgramActivityPlans',
                'ProgramProjects.ProgramProjectTeamMembers'
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
            'title'               => 'sometimes|string|max:255',
            'implementer'         => 'sometimes|string|max:255',

            'programTeamMembers'   => 'sometimes|array',
            'programTeamMembers.*' => 'string|max:255',

            'targetGroup'         => 'required|string|max:255',

            'cooperatingAgencies'   => 'sometimes|array',
            'cooperatingAgencies.*' => 'string|max:255',

            'duration'            => 'sometimes|string|max:255',
            'proposalBudget'      => 'sometimes', // numeric|string depende sa column mo

            'background'          => 'sometimes|string',
            'overallGoal'         => 'sometimes|string',
            'scholarlyConnection' => 'sometimes|string',

            'coordinator'         => 'sometimes|string|max:255',
            'mobileNumber'        => 'sometimes|string|max:50',
            'email'               => 'sometimes|email|max:255',

            // componentProjects: array of objects
            'componentProjects'                           => 'sometimes|array',
            'componentProjects.*.componentProjectTitle'   => 'sometimes|string|max:255',
            'componentProjects.*.outcomes'                => 'sometimes|string',
            'componentProjects.*.budget'                  => 'sometimes', // numeric|string

            // projects: array of objects
            'projects'                 => 'sometimes|array',
            'projects.*.projectTitle'  => 'sometimes|string|max:255',
            'projects.*.teamLeader'    => 'sometimes|string|max:255',
            'projects.*.teamMembers'   => 'sometimes|array',
            'projects.*.teamMembers.*' => 'string|max:255',
            'projects.*.objectives'    => 'sometimes|string',

            // activityPlans: array of objects
            'activityPlans'                => 'sometimes|array',
            'activityPlans.*.activity'     => 'sometimes',
            'activityPlans.*.outputs'      => 'sometimes',
            'activityPlans.*.timeline'     => 'sometimes',
            'activityPlans.*.personnel'    => 'sometimes',
        ]);

        $proposal = DB::transaction(function () use ($validated) {
            // 1) Create parent
            $proposal = ProgramProposal::create([
                'title'               => $validated['title'] ?? null,
                'implementer'         => $validated['implementer'] ?? null,
                'targetGroup'         => $validated['targetGroup'],
                'duration'            => $validated['duration'] ?? null,
                'proposalBudget'      => $validated['proposalBudget'] ?? null,
                'background'          => $validated['background'] ?? null,
                'overallGoal'         => $validated['overallGoal'] ?? null,
                'scholarlyConnection' => $validated['scholarlyConnection'] ?? null,
                'coordinator'         => $validated['coordinator'] ?? null,
                'mobileNumber'        => $validated['mobileNumber'] ?? null,
                'email'               => $validated['email'] ?? null,
            ]);

            // 2) Arrays of strings: Team Members & Cooperating Agencies
            if (!empty($validated['programTeamMembers'])) {
                $proposal->ProgramTeamMembers()->createMany(
                    collect($validated['programTeamMembers'])->map(fn($n)=>['name'=>$n])->all()
                );
            }

            if (!empty($validated['cooperatingAgencies'])) {
                $proposal->ProgramCooperatingAgencies()->createMany(
                    collect($validated['cooperatingAgencies'])->map(fn($n)=>['name'=>$n])->all()
                );
            }

            // 3) componentProjects: array of objects
            if (!empty($validated['componentProjects'])) {
                $rows = collect($validated['componentProjects'])->map(function ($cp) {
                    return [
                        'componentProjectTitle' => $cp['componentProjectTitle'] ?? null,
                        'outcomes'              => $cp['outcomes'] ?? null,
                        'budget'                => $cp['budget'] ?? null,
                    ];
                })->all();

                $proposal->ProgramComponentProjects()->createMany($rows);
            }

            // 4) projects: array of objects + nested teamMembers
            if (!empty($validated['projects'])) {
                foreach ($validated['projects'] as $proj) {
                    $projModel = $proposal->ProgramProjects()->create([
                        'projectTitle' => $proj['projectTitle'] ?? null,
                        'teamLeader'   => $proj['teamLeader'] ?? null,
                        'objectives'   => $proj['objectives'] ?? null,
                    ]);

                    // if may nested teamMembers (strings)
                    if (!empty($proj['teamMembers']) && is_array($proj['teamMembers'])) {
                        // You’ll need a ProgramProjectMember model & relation (see below)
                        $projModel->ProgramProjectTeamMembers()->createMany(
                            collect($proj['teamMembers'])->map(fn($n)=>['name'=>$n])->all()
                        );
                    }
                }
            }

            // 5) activityPlans: array of objects
            if (!empty($validated['activityPlans'])) {
                $rows = collect($validated['activityPlans'])->map(function ($ap) {
                    return [
                        'activity'  => $ap['activity'] ?? null,
                        'outputs'   => $ap['outputs'] ?? null,
                        'timeline'  => $ap['timeline'] ?? null,
                        'personnel' => $ap['personnel'] ?? null,
                    ];
                })->all();

                $proposal->ProgramActivityPlans()->createMany($rows);
            }

            return $proposal;
        });

        return response()->json([
            'message' => 'Program proposal created',
            'data' => $proposal->load([
                'ProgramTeamMembers',
                'ProgramCooperatingAgencies',
                'ProgramComponentProjects',
                'ProgramProjects.ProgramProjectTeamMembers',
                'ProgramActivityPlans',
            ]),
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // 1) Validate (pareho ng fields mo; dinagdagan ko ng *_delete at flexible team members / agencies)
        $validated = $request->validate([
            // parent fields
            'title'               => 'sometimes|string|max:255',
            'implementer'         => 'sometimes|string|max:255',
            'targetGroup'         => 'sometimes|string|max:255',
            'duration'            => 'sometimes|string|max:255',
            'proposalBudget'      => 'sometimes',
            'background'          => 'sometimes|string',
            'overallGoal'         => 'sometimes|string',
            'scholarlyConnection' => 'sometimes|string',
            'coordinator'         => 'sometimes|string|max:255',
            'mobileNumber'        => 'sometimes|string|max:50',
            'email'               => 'sometimes|email|max:255',

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
            'componentProjects.*.componentProjectTitle'     => 'sometimes|string|max:255',
            'componentProjects.*.outcomes'                  => 'sometimes|string',
            'componentProjects.*.budget'                    => 'sometimes',
            'componentProjects.*._delete'                   => 'sometimes|boolean',

            // projects (+ nested teamMembers)
            'projects'                          => 'sometimes|array',
            'projects.*.id'                     => 'sometimes|integer|exists:program_projects,id',
            'projects.*.projectTitle'           => 'sometimes|string|max:255',
            'projects.*.teamLeader'             => 'sometimes|string|max:255',
            'projects.*.objectives'             => 'sometimes|string',
            'projects.*._delete'                => 'sometimes|boolean',

            'projects.*.teamMembers'            => 'sometimes|array',
            'projects.*.teamMembers.*'          => 'nullable',
            'projects.*.teamMembers.*.id'       => 'sometimes|integer|exists:program_project_team_members,id',
            'projects.*.teamMembers.*.name'     => 'sometimes|string|max:255',
            'projects.*.teamMembers.*._delete'  => 'sometimes|boolean',

            // activityPlans
            'activityPlans'                 => 'sometimes|array',
            'activityPlans.*.id'            => 'sometimes|integer|exists:program_activity_plans,id',
            'activityPlans.*.activity'      => 'sometimes|string|max:255',
            'activityPlans.*.outputs'       => 'sometimes|string',
            'activityPlans.*.timeline'      => 'sometimes|string|max:255',
            'activityPlans.*.personnel'     => 'sometimes|string',
            'activityPlans.*._delete'       => 'sometimes|boolean',
        ]);

        $proposal = DB::transaction(function () use ($validated, $id) {
            $proposal = ProgramProposal::findOrFail($id);

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
                        $proposal->ProgramTeamMembers()->whereKey($row['id'])->delete();
                        continue;
                    }

                    $payload = ['name' => $row['name'] ?? null];

                    if (!empty($row['id'])) {
                        $proposal->ProgramTeamMembers()->whereKey($row['id'])->update($payload);
                        $keepIds[] = $row['id'];
                    } else {
                        $new = $proposal->ProgramTeamMembers()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->ProgramTeamMembers()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---------- COOPERATING AGENCIES (also strings OR objects) ----------
            if (array_key_exists('cooperatingAgencies', $validated)) {
                $keepIds = [];
                foreach ($validated['cooperatingAgencies'] as $row) {
                    if (is_string($row)) {
                        $row = ['name' => $row];
                    }
                    if (!empty($row['_delete']) && !empty($row['id'])) {
                        $proposal->ProgramCooperatingAgencies()->whereKey($row['id'])->delete();
                        continue;
                    }

                    $payload = ['name' => $row['name'] ?? null];

                    if (!empty($row['id'])) {
                        $proposal->ProgramCooperatingAgencies()->whereKey($row['id'])->update($payload);
                        $keepIds[] = $row['id'];
                    } else {
                        $new = $proposal->ProgramCooperatingAgencies()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->ProgramCooperatingAgencies()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---------- COMPONENT PROJECTS ----------
            if (array_key_exists('componentProjects', $validated)) {
                $keepIds = [];
                foreach ($validated['componentProjects'] as $cp) {
                    if (!empty($cp['_delete']) && !empty($cp['id'])) {
                        $proposal->ProgramComponentProjects()->whereKey($cp['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'componentProjectTitle' => $cp['componentProjectTitle'] ?? null,
                        'outcomes'              => $cp['outcomes'] ?? null,
                        'budget'                => $cp['budget'] ?? null,
                    ];

                    if (!empty($cp['id'])) {
                        $proposal->ProgramComponentProjects()->whereKey($cp['id'])->update($payload);
                        $keepIds[] = $cp['id'];
                    } else {
                        $new = $proposal->ProgramComponentProjects()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->ProgramComponentProjects()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            // ---------- PROJECTS (+ nested TEAM MEMBERS) ----------
            if (array_key_exists('projects', $validated)) {
                $keepProjectIds = [];

                foreach ($validated['projects'] as $proj) {
                    if (!empty($proj['_delete']) && !empty($proj['id'])) {
                        // delete project + its members
                        $proposal->ProgramProjects()->whereKey($proj['id'])->each(function ($p) {
                            $p->ProgramProjectTeamMembers()->delete();
                            $p->delete();
                        });
                        continue;
                    }

                    $projPayload = [
                        'projectTitle' => $proj['projectTitle'] ?? null,
                        'teamLeader'   => $proj['teamLeader'] ?? null,
                        'objectives'   => $proj['objectives'] ?? null,
                    ];

                    if (!empty($proj['id'])) {
                        $proposal->ProgramProjects()->whereKey($proj['id'])->update($projPayload);
                        $projModel = $proposal->ProgramProjects()->find($proj['id']);
                    } else {
                        $projModel = $proposal->ProgramProjects()->create($projPayload);
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
                                $projModel->ProgramProjectTeamMembers()->whereKey($m['id'])->delete();
                                continue;
                            }

                            $payload = ['name' => $m['name'] ?? null];

                            if (!empty($m['id'])) {
                                $projModel->ProgramProjectTeamMembers()->whereKey($m['id'])->update($payload);
                                $keepMemberIds[] = $m['id'];
                            } else {
                                $new = $projModel->ProgramProjectTeamMembers()->create($payload);
                                $keepMemberIds[] = $new->id;
                            }
                        }
                        $projModel->ProgramProjectTeamMembers()->whereNotIn('id', $keepMemberIds ?: [0])->delete();
                    }
                }

                // prune projects not present; cascade delete members
                $proposal->ProgramProjects()->whereNotIn('id', $keepProjectIds ?: [0])->get()
                    ->each(function ($p) {
                        $p->ProgramProjectTeamMembers()->delete();
                        $p->delete();
                    });
            }

            // ---------- ACTIVITY PLANS ----------
            if (array_key_exists('activityPlans', $validated)) {
                $keepIds = [];
                foreach ($validated['activityPlans'] as $ap) {
                    if (!empty($ap['_delete']) && !empty($ap['id'])) {
                        $proposal->ProgramActivityPlans()->whereKey($ap['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'activity'  => $ap['activity']  ?? null,
                        'outputs'   => $ap['outputs']   ?? null,
                        'timeline'  => $ap['timeline']  ?? null,
                        'personnel' => $ap['personnel'] ?? null,
                    ];

                    if (!empty($ap['id'])) {
                        $proposal->ProgramActivityPlans()->whereKey($ap['id'])->update($payload);
                        $keepIds[] = $ap['id'];
                    } else {
                        $new = $proposal->ProgramActivityPlans()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $proposal->ProgramActivityPlans()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            return $proposal;
        });

        // 3) Load relationships for client-side sync
        return response()->json([
            'message' => 'Program proposal updated',
            'data' => $proposal->load([
                'ProgramTeamMembers',
                'ProgramCooperatingAgencies',
                'ProgramComponentProjects',
                'ProgramProjects.ProgramProjectTeamMembers',
                'ProgramActivityPlans',
            ]),
        ], 200);
    }
}
