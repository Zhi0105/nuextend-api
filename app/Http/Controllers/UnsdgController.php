<?php

namespace App\Http\Controllers;

use App\Models\Unsdg;
use Illuminate\Http\Request;

class UnsdgController extends Controller
{
    public function index() {
        try {
            $unsdgs = Unsdg::all();

            return response()->json([
                'status' => 200,
                'data' => $unsdgs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
