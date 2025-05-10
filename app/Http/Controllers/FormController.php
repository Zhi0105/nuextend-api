<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{

    public function index($id) {
        try {
        $forms = Form::where('event_id', $id)->get();

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
            'is_dean' => 'boolean|sometimes',
            'is_asd' => 'boolean|sometimes',
            'is_ad' => 'boolean|sometimes',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded.',
            ], 400);
        }

        $path = $request->file('file')->store('public/pdf');
        $url = Storage::url($path); // This gives you a browser-accessible URL

        // Create the form entry
        $form = Form::create([
            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'code' => $validated['code'],
            'file' => asset($url),
            'is_dean' => $request->boolean('is_dean'),
            'is_asd' => $request->boolean('is_asd'),
            'is_ad' => $request->boolean('is_ad'),
        ]);
        return response()->json([
            'message' => 'Uploaded successfully',
            'form' => $form
        ], 201);
    }
}
