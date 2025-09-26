<?php

namespace App\Http\Controllers;

use App\Models\Form7;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form6Controller extends Controller
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
            'conducted_on'  => 'nullable|date',
            'behalf_of'     => 'nullable|string|max:255',
            'organization'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',

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
            'conducted_on'  => 'nullable|date',
            'behalf_of'     => 'nullable|string|max:255',
            'organization'  => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',

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
}
