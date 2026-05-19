<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoFraterno;
use Illuminate\Http\JsonResponse;

class TipoFraternoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TipoFraterno::all());
    }
}
