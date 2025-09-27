<?php

namespace App\Http\Controllers;

use App\Models\Form8;
use App\Models\Form8Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form8Controller extends Controller
{
    /**
     * Display all form8 records with references.
     */
    public function index()
    {
        try {
            $forms = Form8::with([
                'event',
                'references',
                'commexApprover',
                'deanApprover',
                'asdApprover',
                'adApprover'
            ])->get();

            return response()->json([
                'status' => 200,
                'data' => $forms,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Create a new form8 record with references.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'proposed_title'          => 'nullable|string|max:255',
            'introduction'            => 'nullable|string',
            'method'                  => 'nullable|string',
            'findings_discussion'     => 'nullable|string',
            'implication_intervention'=> 'nullable|string',

            'references' => 'nullable|array',
            'references.*' => 'nullable|string'

        ]);

        try {
            $form = DB::transaction(function () use ($validated) {
                $form = Form8::create($validated);

                if (!empty($validated['references'])) {
                    foreach ($validated['references'] as $ref) {
                        Form8Reference::create([
                            'form8_id' => $form->id,
                            'reference' => $ref
                        ]);
                    }
                }

                return $form;
            });

            return response()->json([
                'status' => 201,
                'message' => 'Form8 created successfully',
                'data' => $form->load(['event', 'references']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
    /**
     * Update a form8 record and its references.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'proposed_title'          => 'nullable|string|max:255',
            'introduction'            => 'nullable|string',
            'method'                  => 'nullable|string',
            'findings_discussion'     => 'nullable|string',
            'implication_intervention'=> 'nullable|string',

            'references' => 'nullable|array',
            'references.*' => 'nullable|string'

        ]);

        try {
            $form = DB::transaction(function () use ($validated, $id) {
                $form = Form8::findOrFail($id);
                $form->update($validated);

                if (isset($validated['references'])) {
                    // Remove old references and re-insert
                    Form8Reference::where('form8_id', $form->id)->delete();
                    foreach ($validated['references'] as $ref) {
                        Form8Reference::create([
                            'form8_id' => $form->id,
                            'reference' => $ref
                        ]);
                    }
                }

                return $form;
            });

            return response()->json([
                'status' => 200,
                'message' => 'Form8 updated successfully',
                'data' => $form->load(['event', 'references']),
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
            $proposal = Form8::find($request->id);

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
            $proposal = Form8::find($request->id);

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
