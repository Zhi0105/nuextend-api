<?php

namespace App\Http\Controllers;

use App\Models\Form9;
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
            $form9->update($validated);

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

   
}
