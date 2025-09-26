<?php

namespace App\Http\Controllers;

use App\Models\Form4;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form4Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $forms = Form4::with([
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
            'n' => 'nullable|boolean',
            'o' => 'nullable|boolean',
            'p' => 'nullable|boolean',

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
                return Form4::create($validated);
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
            'n' => 'nullable|boolean',
            'o' => 'nullable|boolean',
            'p' => 'nullable|boolean',

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
                $form = Form4::findOrFail($id);
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
}
