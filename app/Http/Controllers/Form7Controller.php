<?php

namespace App\Http\Controllers;

use App\Models\Form7;
use App\Models\FormRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form7Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $forms = Form7::with([
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
     * Create a new form6 record.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',

            'designation'   => 'nullable|string|max:255',
            'representing'  => 'nullable|string|max:255',
            'partnership'   => 'nullable|string|max:255',
            'entitled'      => 'nullable|string|max:255',
            'conducted_on'  => 'nullable|string',
            'behalf_of'     => 'nullable|string|max:255',
            'organization'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255'

        ]);

        try {
            $form = DB::transaction(function () use ($validated) {
                return Form7::create($validated);
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
     * Update the specified form6 record.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'designation'   => 'nullable|string|max:255',
            'representing'  => 'nullable|string|max:255',
            'partnership'   => 'nullable|string|max:255',
            'entitled'      => 'nullable|string|max:255',
            'conducted_on'  => 'nullable|string',
            'behalf_of'     => 'nullable|string|max:255',
            'organization'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255'
        ]);

        try {
            $form = DB::transaction(function () use ($validated, $id) {
                $form = Form7::findOrFail($id);
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
            "commex_remarks" => 'sometimes|string|nullable',
            "dean_remarks" => 'sometimes|string|nullable',
            "asd_remarks" => 'sometimes|string|nullable',
            "ad_remarks" => 'sometimes|string|nullable',
        ]);

        try {
            $proposal = Form7::find($request->id);

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
            $proposal = Form7::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form7'; // your table name

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
                'message' => 'Form 7 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
