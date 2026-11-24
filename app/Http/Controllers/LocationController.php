<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function districts(Division $division): JsonResponse
    {
        return response()->json(
            $division->districts()->orderBy('name')->get(['id', 'name', 'name_bn']),
        );
    }

    public function upazilas(District $district): JsonResponse
    {
        return response()->json(
            $district->upazilas()->orderBy('name')->get(['id', 'name', 'name_bn']),
        );
    }

    public function unions(Upazila $upazila): JsonResponse
    {
        return response()->json(
            $upazila->unions()->orderBy('name')->get(['id', 'name', 'name_bn']),
        );
    }
}
