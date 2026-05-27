<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        if ($perPage == 0) {
            $perPage = 10000;
        }

        $personas = Persona::with('sexo', 'tipoPersona')
            ->when($request->buscar, function ($q, $buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('primer_apellido', 'like', "%{$buscar}%")
                  ->orWhere('segundo_apellido', 'like', "%{$buscar}%")
                  ->orWhere('ci', 'like', "%{$buscar}%");
            })
            ->when($request->id_sexo, fn($q, $idSexo) => $q->where('id_sexo', $idSexo))
            ->when($request->id_tipo_persona, fn($q, $idTipoPersona) => $q->where('id_tipo_persona', $idTipoPersona))
            ->when($request->excluir_festividad, function ($q, $festividadId) {
                $q->whereDoesntHave('inscripciones', fn($qi) => $qi->where('festividad_id', $festividadId));
            })
            ->orderBy('primer_apellido')
            ->paginate($perPage);
            
        return response()->json($personas);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombres' => 'required|string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'primer_apellido' => 'required|string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'segundo_apellido' => 'nullable|string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'ci' => 'required|string|unique:persona,ci|regex:/^[A-Z0-9-]+$/i',
            'id_sexo' => 'required|exists:sexo,id_sexo',
            'id_tipo_persona' => 'nullable|exists:tipo_persona,id_tipo_persona',
            'celular' => 'required|string|regex:/^[67]\d{7}$/',
            'correo_personal' => 'required|email'
        ]);

        $persona = Persona::create([
            'nombres' => strtoupper($request->nombres),
            'primer_apellido' => strtoupper($request->primer_apellido),
            'segundo_apellido' => $request->segundo_apellido ? strtoupper($request->segundo_apellido) : null,
            'ci' => strtoupper($request->ci),
            'id_sexo' => $request->id_sexo,
            'id_tipo_persona' => $request->id_tipo_persona,
            'celular' => $request->celular,
            'correo_personal' => $request->correo_personal,
        ]);

        return response()->json($persona->load('sexo', 'tipoPersona'));
    }

    public function show(int $id): JsonResponse
    {
        $persona = Persona::with('sexo', 'tipoPersona')->findOrFail($id);
        return response()->json($persona);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $persona = Persona::findOrFail($id);
        $request->validate([
            'nombres' => 'string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'primer_apellido' => 'string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'segundo_apellido' => 'nullable|string|regex:/^[A-ZÁÉÍÓÚÑ ]+$/i',
            'ci' => 'string|regex:/^[A-Z0-9-]+$/i|unique:persona,ci,'.$id.',id_persona',
            'id_sexo' => 'exists:sexo,id_sexo',
            'id_tipo_persona' => 'nullable|exists:tipo_persona,id_tipo_persona',
            'celular' => 'string|regex:/^[67]\d{7}$/',
            'correo_personal' => 'email'
        ]);

        $persona->update([
            'nombres' => $request->has('nombres') ? strtoupper($request->nombres) : $persona->nombres,
            'primer_apellido' => $request->has('primer_apellido') ? strtoupper($request->primer_apellido) : $persona->primer_apellido,
            'segundo_apellido' => $request->has('segundo_apellido') ? ($request->segundo_apellido ? strtoupper($request->segundo_apellido) : null) : $persona->segundo_apellido,
            'ci' => $request->has('ci') ? strtoupper($request->ci) : $persona->ci,
            'id_sexo' => $request->has('id_sexo') ? $request->id_sexo : $persona->id_sexo,
            'id_tipo_persona' => array_key_exists('id_tipo_persona', $request->all()) ? $request->id_tipo_persona : $persona->id_tipo_persona,
            'celular' => $request->has('celular') ? $request->celular : $persona->celular,
            'correo_personal' => $request->has('correo_personal') ? $request->correo_personal : $persona->correo_personal,
        ]);

        return response()->json($persona->fresh('sexo', 'tipoPersona'));
    }

    public function destroy(int $id): JsonResponse
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return response()->json(['message' => 'Persona eliminada correctamente.']);
    }
}
