<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index() {
        try {
            $organizations = Organization::with('events')->get();

            return response()->json([
                'status' => 200,
                'data' => $organizations
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
            "name" => "required|string|unique:organizations,name",
        ]);

        try {
            $organization = Organization::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 201,
                'data' => $organization
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }

    }
    public function update(Request $request) {
        $request->validate([
            "id" => "required",
            "name" => "required|string"
        ]);

        try {
            $organization = Organization::find($request->id);

            if(!$organization) {
                return response()->json([
                    'status' => 404,
                    'message' => "No organization found"
                ], 404);
            }

            Organization::where('id', $request->id)->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 200,
                "message" => "Organization successfully updated"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }

    }
    public function delete(Request $request) {
        $request->validate([
            "id" => "required",
        ]);

        try {
            $organization = Organization::find($request->id);

            if(!$organization) {
                return response()->json([
                    'status' => 404,
                    'message' => "No organization found"
                ], 404);
            }

            Organization::where('id', $request->id)->delete();

            return response()->json([
                'status' => 200,
                "message" => "Organization successfully remove"
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function members($id) {
        $organization = Organization::find($id);
        $members = $organization->users;

        if(!$organization) {
            return response()->json([
                'status' => 404,
                'message' => 'Organization not found'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'data' => $members
        ], 200);
    }
    public function role_change(Request $request) {
        $request->validate([
            "organization_id" => "required",
            "assigner_id" => "required",
            "assigner_role" => "required",
            "assignee_id" => "required",
            "assignee_role" => "required"
        ]);

        $assigner = OrganizationMember::where('user_id', $request->assigner_id)->first();
        $assignee = OrganizationMember::where('user_id', $request->assignee_id)->first();
        $roles = OrganizationMember::where('user_id', $request->assignee_id)
        ->whereIn('role_id', [6, 7])
        ->get()
        ->keyBy('role_id');

        $hasLeaderRole = $roles->get(6);
        $hasOrganizerRole = $roles->get(7);

        if(!$assigner) {
            return response()->json([
                'status' => 404,
                'message' => 'assigner not found'
            ], 404);
        }
        if(!$assignee) {
            return response()->json([
                'status' => 404,
                'message' => 'assignee not found'
            ], 404);
        }

        if(($hasOrganizerRole || $hasLeaderRole) && ($request->assignee_role === 6 || $request->assignee_role === 7)) {
            return response()->json([
                "status" => 400,
                "message" => "must not already be a Leader/Organizer of another organization"
            ], 400);
        }

        // FOR LEADERS
        if($request->assigner_role === 6) {
            if($request->assignee_role === 6) {
                OrganizationMember::where('user_id', $request->assignee_id)->where('organization_id', $request->organization_id)->update([
                    'role_id' => $request->assignee_role
                ]);
                OrganizationMember::where('user_id', $request->assigner_id)->where('organization_id', $request->organization_id)->update([
                    'role_id' => 8
                ]);
            } else {
                OrganizationMember::where('user_id', $request->assignee_id)->where('organization_id', $request->organization_id)->update([
                    'role_id' => $request->assignee_role
                ]);
            }
            return response()->json([
                'status' => 200,
                'message' => 'role changed successful'
            ], 200);
        }
        // FOR ORGANIZERS
        if($request->assigner_role === 7) {
            if($request->assignee_role === 8) {
                return response()->json([
                    'status' => 400,
                    'message' => "Organizer cannot assign leader role to anyone."
                ], 400);
            }

            else {
                if($request->assignee_role === 7) {
                    OrganizationMember::where('user_id', $request->assignee_id)->where('organization_id', $request->organization_id)->update([
                        'role_id' => $request->assignee_role
                    ]);
                    OrganizationMember::where('user_id', $request->assigner_id)->where('organization_id', $request->organization_id)->update([
                        'role_id' => 8
                    ]);
                } else {
                    OrganizationMember::where('user_id', $request->assignee_id)->where('organization_id', $request->organization_id)->update([
                        'role_id' => $request->assignee_role
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'role changed successful'
                ], 200);
            }
        }
    }
}
