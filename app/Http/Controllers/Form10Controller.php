<?php

namespace App\Http\Controllers;

use App\Models\Form10;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Form10Controller extends Controller
{
    public function index()
    {
        try {
            $form10 = Form10::with(
                'oaopb', 
                'commexApprover', 
                'deanApprover', 
                'asdApprover', 
                'adApprover')->get();

            return response()->json([
                'status' => 200,
                'data' => $form10
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
            'discussion' => 'sometimes|string',

            'oaopb' => 'sometimes|array',
            'oaopb.*.objectives' => 'sometimes|string',
            'oaopb.*.activities' => 'sometimes|string',
            'oaopb.*.outputs' => 'sometimes|string',
            'oaopb.*.personnel' => 'sometimes|string',
            'oaopb.*.budget' => 'sometimes|string',
        ]);

        $form10 = DB::transaction(function () use ($validated) {
            $form10 = Form10::create([
                'event_id' => $validated['event_id'],
                'discussion' => $validated['discussion'] ?? null,
            ]);

            if (!empty($validated['oaopb'])) {
                $rows = collect($validated['oaopb'])->map(fn($m) => [
                    'objectives' => $m['objectives'] ?? null,
                    'activities' => $m['activities'] ?? null,
                    'outputs' => $m['outputs'] ?? null,
                    'personnel' => $m['personnel'] ?? null,
                    'budget' => $m['budget'] ?? null,
                ])->all();

                $form10->oaopb()->createMany($rows);
            }

            return $form10;
        });

        return response()->json([
            'message' => 'Form10 created successfully',
            'data' => $form10->load('oaopb', 'commexApprover', 'deanApprover', 'asdApprover', 'adApprover'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'discussion' => 'sometimes|string',

            'oaopb' => 'sometimes|array',
            'oaopb.*.id' => 'sometimes|integer|exists:form10_oaopb,id',
            'oaopb.*.objectives' => 'sometimes|string',
            'oaopb.*.activities' => 'sometimes|string',
            'oaopb.*.outputs' => 'sometimes|string',
            'oaopb.*.personnel' => 'sometimes|string',
            'oaopb.*.budget' => 'sometimes|string',
            'oaopb.*._delete' => 'sometimes|boolean',
        ]);

        $form10 = DB::transaction(function () use ($validated, $id) {
            $form10 = Form10::findOrFail($id);
            $form10->update($validated);

            if (array_key_exists('oaopb', $validated)) {
                $keepIds = [];
                foreach ($validated['oaopb'] as $m) {
                    if (!empty($m['_delete']) && !empty($m['id'])) {
                        $form10->oaopb()->whereKey($m['id'])->delete();
                        continue;
                    }

                    $payload = [
                        'objectives' => $m['objectives'] ?? null,
                        'activities' => $m['activities'] ?? null,
                        'outputs' => $m['outputs'] ?? null,
                        'personnel' => $m['personnel'] ?? null,
                        'budget' => $m['budget'] ?? null,
                    ];

                    if (!empty($m['id'])) {
                        $form10->oaopb()->whereKey($m['id'])->update($payload);
                        $keepIds[] = $m['id'];
                    } else {
                        $new = $form10->oaopb()->create($payload);
                        $keepIds[] = $new->id;
                    }
                }
                $form10->oaopb()->whereNotIn('id', $keepIds ?: [0])->delete();
            }

            return $form10;
        });

        return response()->json([
            'message' => 'Form10 updated successfully',
            'data' => $form10->load('oaopb', 'commexApprover', 'deanApprover', 'asdApprover', 'adApprover'),
        ], 200);
    }
}
