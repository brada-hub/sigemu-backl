<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fraternidad;
use Illuminate\Http\JsonResponse;

class FraternidadController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Fraternidad::all());
    }
}
