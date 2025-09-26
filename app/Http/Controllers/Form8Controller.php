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
            'references.*' => 'nullable|string',

            'is_commex' => 'nullable|boolean',
            'is_dean'   => 'nullable|boolean',
            'is_asd'    => 'nullable|boolean',
            'is_ad'     => 'nullable|boolean',

            'commex_remarks' => 'nullable|string',
            'dean_remarks'   => 'nullable|string',
            'asd_remarks'    => 'nullable|string',
            'ad_remarks'     => 'nullable|string',

            'commex_approved_by' => 'nullable|exists:users,id',
            'dean_approved_by'   => 'nullable|exists:users,id',
            'asd_approved_by'    => 'nullable|exists:users,id',
            'ad_approved_by'     => 'nullable|exists:users,id',

            'commex_approve_date' => 'nullable|date',
            'dean_approve_date'   => 'nullable|date',
            'asd_approve_date'    => 'nullable|date',
            'ad_approve_date'     => 'nullable|date',
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
            'references.*' => 'nullable|string',

            'is_commex' => 'nullable|boolean',
            'is_dean'   => 'nullable|boolean',
            'is_asd'    => 'nullable|boolean',
            'is_ad'     => 'nullable|boolean',

            'commex_remarks' => 'nullable|string',
            'dean_remarks'   => 'nullable|string',
            'asd_remarks'    => 'nullable|string',
            'ad_remarks'     => 'nullable|string',

            'commex_approved_by' => 'nullable|exists:users,id',
            'dean_approved_by'   => 'nullable|exists:users,id',
            'asd_approved_by'    => 'nullable|exists:users,id',
            'ad_approved_by'     => 'nullable|exists:users,id',

            'commex_approve_date' => 'nullable|date',
            'dean_approve_date'   => 'nullable|date',
            'asd_approve_date'    => 'nullable|date',
            'ad_approve_date'     => 'nullable|date',
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
}
