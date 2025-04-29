<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpoUnitsController extends Controller
{
    public function index(Request $request, \App\Models\RsiaSpo $spo)
    {
        $units = $spo->units()
            ->with('unit')
            ->get();


        return response()->json([
            'status' => true,
            'data'   => $units,
        ]);
    }
}
