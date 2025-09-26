<?php

namespace App\Http\Controllers;

use App\Models\Form5;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form5Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $forms = Form5::with([
                'event',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'
            ])->get();

            return response()->json([
                'status' => 200,
                'data' => $forms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Create a new form45 record.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',

            'a' => 'nullable|boolean',
            'b' => 'nullable|boolean',
            'c' => 'nullable|boolean',
            'd' => 'nullable|boolean',
            'e' => 'nullable|boolean',
            'f' => 'nullable|boolean',
            'g' => 'nullable|boolean',
            'h' => 'nullable|boolean',
            'i' => 'nullable|boolean',
            'j' => 'nullable|boolean',
            'k' => 'nullable|boolean',
            'l' => 'nullable|boolean',
            'm' => 'nullable|boolean',
            'n' => 'nullable|boolean'
        ]);

        try {
            $form = DB::transaction(function () use ($validated) {
                return Form5::create($validated);
            });

            return response()->json([
                'status' => 201,
                'message' => 'Form created successfully',
                'data' => $form->load([
                    'event',
                    'commexApprover',
                    'deanApprover',
                    'asdApprover',
                    'adApprover'
                ]),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'a' => 'nullable|boolean',
            'b' => 'nullable|boolean',
            'c' => 'nullable|boolean',
            'd' => 'nullable|boolean',
            'e' => 'nullable|boolean',
            'f' => 'nullable|boolean',
            'g' => 'nullable|boolean',
            'h' => 'nullable|boolean',
            'i' => 'nullable|boolean',
            'j' => 'nullable|boolean',
            'k' => 'nullable|boolean',
            'l' => 'nullable|boolean',
            'm' => 'nullable|boolean',
            'n' => 'nullable|boolean'
        ]);

        try {
            $form = DB::transaction(function () use ($validated, $id) {
                $form = Form5::findOrFail($id);
                $form->update($validated);
                return $form;
            });

            return response()->json([
                'status' => 200,
                'message' => 'Form updated successfully',
                'data' => $form->load([
                    'event',
                    'commexApprover',
                    'deanApprover',
                    'asdApprover',
                    'adApprover'
                ]),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }


    public function approve(Request $request) {

        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "dean_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes',
            "ad_remarks" => 'sometimes',

        ]);

        try {
            $proposal = Form5::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id(); // current logged-in user

            $roleUpdateMap = [
                1  => ['is_commex' => true, 'commex_remarks' => $request->input('commex_remarks'), 'commex_approved_by' => $userId, 'commex_approve_date' => now()],
                9  => ['is_dean' => true, 'dean_remarks' => $request->input('dean_remarks'), 'dean_approved_by' => $userId, 'dean_approve_date' => now()],
                10 => ['is_asd' => true, 'asd_remarks' => $request->input('asd_remarks'), 'asd_approved_by' => $userId, 'asd_approve_date' => now()],
                11 => ['is_ad' => true, 'ad_remarks' => $request->input('ad_remarks'), 'ad_approved_by' => $userId, 'ad_approve_date' => now()],
            ];

            if (isset($roleUpdateMap[$request->role_id])) {
                $proposal->update($roleUpdateMap[$request->role_id]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Approved Successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
    public function reject(Request $request) {
        $request->validate([
            "id" => 'required|integer',
            "role_id" => 'required|integer',
            "commex_remarks" => 'sometimes',
            "dean_remarks" => 'sometimes',
            "asd_remarks" => 'sometimes',
            "ad_remarks" => 'sometimes',
        ]);

        try {
            $proposal = Form5::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $roleUpdateMap = [
                1  => ['is_commex' => false, 'commex_remarks' => $request->input('commex_remarks')],
                9  => ['is_dean' => false, 'dean_remarks' => $request->input('dean_remarks')],
                10 => ['is_asd' => false, 'asd_remarks' => $request->input('asd_remarks')],
                11 => ['is_ad' => false, 'ad_remarks' => $request->input('ad_remarks')],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;

            if ($updateData) {
                $proposal->update($updateData);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Form Rejected',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
