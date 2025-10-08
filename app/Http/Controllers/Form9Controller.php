<?php

namespace App\Http\Controllers;

use App\Models\Form9;
use App\Models\FormRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form9Controller extends Controller
{
    public function index()
    {
        try {
            $form9 = Form9::with(
                'logicModels',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover')->get();

            return response()->json([
                'status' => 200,
                'data' => $form9
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'findings_discussion' => 'sometimes|string',
            'conclusion_recommendations' => 'sometimes|string',

            'logicModels' => 'sometimes|array',
            'logicModels.*.objectives' => 'sometimes|string',
            'logicModels.*.inputs' => 'sometimes|string',
            'logicModels.*.activities' => 'sometimes|string',
            'logicModels.*.outputs' => 'sometimes|string',
            'logicModels.*.outcomes' => 'sometimes|string',
        ]);

        $form9 = DB::transaction(function () use ($validated) {
            $form9 = Form9::create([
                'event_id' => $validated['event_id'],
                'findings_discussion' => $validated['findings_discussion'] ?? null,
                'conclusion_recommendations' => $validated['conclusion_recommendations'] ?? null,
            ]);

            if (!empty($validated['logicModels'])) {
                $rows = collect($validated['logicModels'])->map(fn($m) => [
                    'objectives' => $m['objectives'] ?? null,
                    'inputs' => $m['inputs'] ?? null,
                    'activities' => $m['activities'] ?? null,
                    'outputs' => $m['outputs'] ?? null,
                    'outcomes' => $m['outcomes'] ?? null,
                ])->all();

                $form9->logicModels()->createMany($rows);
            }

            return $form9;
        });

        return response()->json([
            'message' => 'Form9 created successfully',
            'data' => $form9->load(
                'logicModels',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'findings_discussion' => 'sometimes|string',
            'conclusion_recommendations' => 'sometimes|string',

            'logicModels' => 'sometimes|array',
            'logicModels.*.id' => 'sometimes|integer|exists:form9_logic_models,id',
            'logicModels.*.objectives' => 'sometimes|string',
            'logicModels.*.inputs' => 'sometimes|string',
            'logicModels.*.activities' => 'sometimes|string',
            'logicModels.*.outputs' => 'sometimes|string',
            'logicModels.*.outcomes' => 'sometimes|string',
            'logicModels.*._delete' => 'sometimes|boolean',
        ]);

        $form9 = DB::transaction(function () use ($validated, $id) {
            $form9 = Form9::findOrFail($id);
            $form9->update(array_merge($validated, [
                'is_updated' => true,
                'is_revised' => false,
            ]));

            if (array_key_exists('logicModels', $validated)) {
                $keepIds = [];
                foreach ($validated['logicModels'] as $m) {
                    if (!empty($m['_delete']) && !empty($m['id'])) {
                        $form9->logicModels()->whereKey($m['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'objectives' => $m['objectives'] ?? null,
                        'inputs' => $m['inputs'] ?? null,
                        'activities' => $m['activities'] ?? null,
                        'outputs' => $m['outputs'] ?? null,
                        'outcomes' => $m['outcomes'] ?? null,
                    ];

                    if (!empty($m['id'])) {
                        $form9->logicModels()->whereKey($m['id'])->update($payload);
                        $keepIds[] = $m['id'];
                    } else {
                        $new = $form9->logicModels()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $form9->logicModels()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            return $form9;
        });

        return response()->json([
            'message' => 'Form9 updated successfully',
            'data' => $form9->load(
                'logicModels',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'),
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
            $proposal = Form9::find($request->id);

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

                $updateData['is_updated'] = false;

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
            $proposal = Form9::find($request->id);

            if (!$proposal) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Form not found',
                ], 404);
            }

            $userId = auth()->id();
            $formType = 'form9'; // your table name

            // Determine which flag to reset
            $roleUpdateMap = [
                1  => ['is_commex' => false],
                9  => ['is_dean' => false],
                10 => ['is_asd' => false],
                11 => ['is_ad' => false],
            ];

            $updateData = $roleUpdateMap[$request->role_id] ?? null;
            $updateData['is_revised'] = true;

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
                'message' => 'Form 9 sent for revision',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
