<?php

namespace App\Http\Controllers;

use App\Models\Moddel;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index() {
        try {
            $models = Moddel::all();

            return response()->json([
                'status' => 200,
                'data' => $models
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
