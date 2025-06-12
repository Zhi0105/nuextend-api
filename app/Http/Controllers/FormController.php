<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{

    public function index($id) {
        try {
        $forms = Form::where('event_id', $id)->with(['commexApprover', 'deanApprover', 'asdApprover', 'adApprover'])->get();

            return response()->json([
                'status' => 200,
                'data' => $forms
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:forms,code',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded.',
            ], 400);
        }

        $path = $request->file('file')->store('public/pdf');
        $url = Storage::url($path); // public URL

        // Get last numeric part of the code
        preg_match('/(\d+)$/', $validated['code'], $matches);
        $lastDigit = isset($matches[1]) ? (int)$matches[1] : null;

        // Default all true
        $approvers = [
            'is_commex' => true,
            'is_dean' => true,
            'is_asd' => true,
            'is_ad' => true,
        ];

        // Apply specific rules
        if (in_array($lastDigit, [4, 5])) {
            $approvers = [
                'is_commex' => false,
                'is_dean' => false,
                'is_asd' => false,
                'is_ad' => true,
            ];
        } elseif (in_array($lastDigit, [11, 12])) {
            $approvers = [
                'is_commex' => false,
                'is_dean' => true,
                'is_asd' => false,
                'is_ad' => true,
            ];
        }

        $form = Form::create([
            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'code' => $validated['code'],
            'file' => asset($url),
            'is_commex' => $approvers['is_commex'],
            'is_dean' => $approvers['is_dean'],
            'is_asd' => $approvers['is_asd'],
            'is_ad' => $approvers['is_ad'],
        ]);

        return response()->json([
            'message' => 'Uploaded successfully',
            'form' => $form
        ], 201);
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
            $form = Form::find($request->id);

            if (!$form) {
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
                $form->update($roleUpdateMap[$request->role_id]);
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
            $form = Form::find($request->id);

            if (!$form) {
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
                $form->update($updateData);
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
