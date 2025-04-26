<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function organization_assign(Request $request) {
        try {
            $request->validate([
                'user_id' => 'integer|required',
                'organizations' => 'required|array',
                'organizations.*.id' => 'integer|required',
                'organizations.*.role' => 'integer|required'
            ]);

            $user = User::find($request->user_id);

            if(!$user) {
                return response()->json([
                    'status' => 400,
                    'message' => 'User not found'
                ], 404);
            }

            // Get existing organization IDs
            $existingOrganizationIds = $user->organizations()->pluck('organizations.id')->toArray();

            $syncData = collect($request->organizations)
                ->filter(function ($org) use ($existingOrganizationIds) {
                    return !in_array($org['id'], $existingOrganizationIds); // Only new organizations
                })
                ->mapWithKeys(function ($org) {
                    return [$org['id'] => ['role_id' => $org['role']]];
                })
                ->toArray();

            // If no new organizations to assign
            if (empty($syncData)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'This person is already a member'
                ], 400);
            }

            $user->organizations()->attach($syncData);

            return response()->json([
                'status' => 200,
                'message' => 'User Assigned Successfully'
            ], 200);
            // $request->validate([
            //     'user_id' => 'integer|required',
            //     'organizations' => 'required|array',
            //     'organizations.*.id' => 'integer|required',
            //     'organizations.*.role' => 'integer|required'
            // ]);

            // $user = User::find($request->user_id);

            // if(!$user) {
            //     return response()->json([
            //         'status' => 400,
            //         'message' => 'User not found'
            //     ], 404);
            // }

            // $syncData = collect($request->organizations)->mapWithKeys(function ($org) {
            //     return [$org['id'] => ['role_id' => $org['role']]];
            // })->toArray();

            // $user->organizations()->syncWithoutDetaching($syncData);

            // return response()->json([
            //     'status' => 200,
            //     'message' => 'User Assigned Successfully'
            // ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
