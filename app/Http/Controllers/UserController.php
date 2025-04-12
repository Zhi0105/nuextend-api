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

            $syncData = collect($request->organizations)->mapWithKeys(function ($org) {
                return [$org['id'] => ['role_id' => $org['role']]];
            })->toArray();

            $user->organizations()->sync($syncData);

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
}
