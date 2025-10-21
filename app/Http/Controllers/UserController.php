<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use App\Models\Attendance;
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

    public function update_user_details(Request $request){
            $validated = $request->validate([
                'user' => 'required|integer|exists:users,id',
                'file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            ]);

            try {
                $user = User::findOrFail($validated['user']);

                if ($request->hasFile('file')) {
                    $binaryData = file_get_contents($request->file('file')->getRealPath());
                    $user->esign = $binaryData;
                    $user->save();
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'User e-signature updated successfully.',
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => 'An error occurred while updating the user e-signature.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

    public function assign_officer_role(Request $request)
    {
        $validated = $request->validate([
            'officer_user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id', // Assuming you have a roles table
        ]);

        try {
            $officer = User::findOrFail($validated['officer_user_id']);

            $officer->update([
                'role_id' => $validated['role_id'],
                'is_officer' => true
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Officer role assigned successfully.',
                'officer' => [
                    'id' => $officer->id,
                    'name' => $officer->name, // Assuming you have name field
                    'role_id' => $officer->role_id,
                    'is_officer' => $officer->is_officer
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while assigning officer role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function remove_officer_role(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);

            $user->update([
                'is_officer' => false,
                'role_id' => 4
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Officer role removed successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while removing officer role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function get_user_hours(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
    ]);

    try {
        $user = User::findOrFail($validated['user_id']);

        // Get attendance records for this user through participants using the Attendance model
        $attendanceRecords = Attendance::with(['participant'])
            ->whereHas('participant', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_attended', true)
            ->get();

        // Calculate total hours
        $totalMinutes = 0;

        foreach ($attendanceRecords as $record) {
            if ($record->time_in && $record->time_out) {
                $minutes = $record->time_in->diffInMinutes($record->time_out);
                $totalMinutes += $minutes;
            }
        }

        $totalHours = round($totalMinutes / 60, 2);

        return response()->json([
            'status' => 200,
            'message' => 'User hours calculated successfully.',
            'total_hours' => $totalHours
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An error occurred while calculating user hours.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
