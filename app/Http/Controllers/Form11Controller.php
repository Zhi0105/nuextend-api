<?php

namespace App\Http\Controllers;

use App\Models\Form11;
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
            'travelDetails.*.trip_duration' => 'sometimes|string',
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
                    return [
                        'date'          => $t['date'] ?? null,
                        'from'          => $t['from'] ?? null,
                        'to'            => $t['to'] ?? null,
                        'departure'     => $t['departure'] ?? null,
                        'arrival'       => $t['arrival'] ?? null,
                        'trip_duration' => $t['trip_duration'] ?? null,
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

            'travelDetails'                      => 'sometimes|array',
            'travelDetails.*.id'                 => 'sometimes|integer|exists:form11_travel_details,id',
            'travelDetails.*.date'               => 'sometimes|date',
            'travelDetails.*.from'               => 'sometimes|string|max:255',
            'travelDetails.*.to'                 => 'sometimes|string|max:255',
            'travelDetails.*.departure'          => 'sometimes|date',
            'travelDetails.*.arrival'            => 'sometimes|date',
            'travelDetails.*.trip_duration'      => 'sometimes|string',
            'travelDetails.*.purpose'            => 'sometimes|string|max:255',
            'travelDetails.*._delete'            => 'sometimes|boolean',
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

                    $payload = [
                        'date'          => $row['date'] ?? null,
                        'from'          => $row['from'] ?? null,
                        'to'            => $row['to'] ?? null,
                        'departure'     => $row['departure'] ?? null,
                        'arrival'       => $row['arrival'] ?? null,
                        'trip_duration' => $row['trip_duration'] ?? null,
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
}
