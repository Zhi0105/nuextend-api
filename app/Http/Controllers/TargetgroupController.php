<?php

namespace App\Http\Controllers;

use App\Models\Targetgroup;
use Illuminate\Http\Request;

class TargetgroupController extends Controller
{
    public function index() {
        try {
            $targetgroups = Targetgroup::all();

            return response()->json([
                'status' => 200,
                'data' => $targetgroups
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' =>  $e->getCode(),
                'message' => $e->getMessage(),
            ],  $e->getCode());
        }
    }
}
