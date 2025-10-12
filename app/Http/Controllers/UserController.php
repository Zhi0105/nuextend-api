<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller{

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

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function update_user_details(Request $request) {
        $validated = $request->validate([
            'user' => 'required|integer|exists:users,id',
            'department' => 'nullable|integer|exists:departments,id',
            'program' => 'nullable|integer|exists:programs,id',
            'file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            'skills' => 'nullable|array', 
            'skills.*' => 'integer|exists:skills,id',
            "officer" => 'nullable|integer',
            "role" => 'nullable|integer'
        ]);

        try {
            $user = User::findOrFail($validated['user']);

            $updateData = [];

            if (!empty($validated['department'])) {
                $updateData['department_id'] = $validated['department'];
            }

            if (!empty($validated['program'])) {
                $updateData['program_id'] = $validated['program'];
            }
            
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            if (!empty($validated['officer']) && !empty($validated['role'])) {
                $officer = User::findOrFail($validated['officer']);
                $officer->update([
                    'role_id' => $validated['role'],
                    'is_officer' => true
                ]);
            }

            
            // ✅ Update and sync skills
            if (array_key_exists('skills', $validated)) {
                $user->skill()->sync($validated['skills']);
            }

            if ($request->hasFile('file')) {
                $binaryData = file_get_contents($request->file('file')->getRealPath());
                $user->esign = $binaryData;
                $user->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'User updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating the user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
