<?php

namespace App\Http\Controllers;

use App\Models\Form11;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form11Controller extends Controller
{
    // INDEX
    public function index()
    {
        try {
            $forms = Form11::with('travelDetails')->get();

            return response()->json([
                'status' => 200,
                'data'   => $forms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // CREATE
    public function create(Request $request)
    {
        $validated = $request->validate([
            'event_id'              => 'required|integer|exists:events,id',
            'transportation_medium' => 'sometimes|string|max:255',
            'driver'                => 'sometimes|string|max:255',

            'travelDetails'                 => 'sometimes|array',
            'travelDetails.*.date'          => 'sometimes|date',
            'travelDetails.*.from'          => 'sometimes|string|max:255',
            'travelDetails.*.to'            => 'sometimes|string|max:255',
            'travelDetails.*.departure'     => 'sometimes|date',
            'travelDetails.*.arrival'       => 'sometimes|date',
            'travelDetails.*.purpose'       => 'sometimes|string|max:255',
        ]);

        $form = DB::transaction(function () use ($validated) {
            // Parent
            $form = Form11::create([
                'event_id'              => $validated['event_id'],
                'transportation_medium' => $validated['transportation_medium'] ?? null,
                'driver'                => $validated['driver'] ?? null,
            ]);

            // Child
            if (!empty($validated['travelDetails'])) {
                $rows = collect($validated['travelDetails'])->map(function ($t) {
                    $departure = !empty($t['departure']) ? Carbon::parse($t['departure']) : null;
                    $arrival   = !empty($t['arrival'])   ? Carbon::parse($t['arrival'])   : null;

                    $tripDuration = null;
                    if ($departure && $arrival) {
                        $diffInMinutes = $arrival->diffInMinutes($departure);
                        $hours   = floor($diffInMinutes / 60);
                        $minutes = $diffInMinutes % 60;
                        $tripDuration = sprintf('%02dh %02dm', $hours, $minutes);
                    }

                    return [
                        'date'          => $t['date'] ?? null,
                        'from'          => $t['from'] ?? null,
                        'to'            => $t['to'] ?? null,
                        'departure'     => $departure,
                        'arrival'       => $arrival,
                        'trip_duration' => $tripDuration,
                        'purpose'       => $t['purpose'] ?? null,
                    ];
                })->all();

                $form->travelDetails()->createMany($rows);
            }

            return $form;
        });

        return response()->json([
            'message' => 'Form11 created',
            'data'    => $form->load('travelDetails'),
        ], 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'transportation_medium' => 'sometimes|string|max:255',
            'driver'                => 'sometimes|string|max:255',

            'travelDetails'                 => 'sometimes|array',
            'travelDetails.*.id'            => 'sometimes|integer|exists:form11_travel_details,id',
            'travelDetails.*.date'          => 'sometimes|date',
            'travelDetails.*.from'          => 'sometimes|string|max:255',
            'travelDetails.*.to'            => 'sometimes|string|max:255',
            'travelDetails.*.departure'     => 'sometimes|date',
            'travelDetails.*.arrival'       => 'sometimes|date',
            'travelDetails.*.purpose'       => 'sometimes|string|max:255',
            'travelDetails.*._delete'       => 'sometimes|boolean',
        ]);

        $form = DB::transaction(function () use ($validated, $id) {
            $form = Form11::findOrFail($id);

            // update parent
            $form->update([
                'transportation_medium' => $validated['transportation_medium'] ?? $form->transportation_medium,
                'driver'                => $validated['driver'] ?? $form->driver,
            ]);

            // update child
            if (array_key_exists('travelDetails', $validated)) {
                $keepIds = [];
                foreach ($validated['travelDetails'] as $row) {
                    if (!empty($row['_delete']) && !empty($row['id'])) {
                        $form->travelDetails()->whereKey($row['id'])->delete();
                        continue;
                    }

                    $departure = !empty($row['departure']) ? Carbon::parse($row['departure']) : null;
                    $arrival   = !empty($row['arrival'])   ? Carbon::parse($row['arrival'])   : null;

                    $tripDuration = null;
                    if ($departure && $arrival) {
                        $diffInMinutes = $arrival->diffInMinutes($departure);
                        $hours   = floor($diffInMinutes / 60);
                        $minutes = $diffInMinutes % 60;
                        $tripDuration = sprintf('%02dh %02dm', $hours, $minutes);
                    }

                    $payload = [
                        'date'          => $row['date'] ?? null,
                        'from'          => $row['from'] ?? null,
                        'to'            => $row['to'] ?? null,
                        'departure'     => $departure,
                        'arrival'       => $arrival,
                        'trip_duration' => $tripDuration,
                        'purpose'       => $row['purpose'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $form->travelDetails()->whereKey($row['id'])->update($payload);
                        $keepIds[] = $row['id'];
                    } else {
                        $new = $form->travelDetails()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }

                // prune old rows
                $form->travelDetails()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            return $form;
        });

        return response()->json([
            'message' => 'Form11 updated',
            'data'    => $form->load('travelDetails'),
        ], 200);
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
            $proposal = Form11::find($request->id);

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
            $proposal = Form11::find($request->id);

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
