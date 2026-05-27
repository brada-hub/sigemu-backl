<?php

namespace Database\Seeders;

use App\Models\TipoPersona;
use Illuminate\Database\Seeder;

class TipoPersonaSeeder extends Seeder
{
    public function run(): void
    {
        TipoPersona::firstOrCreate(
            ['nombre' => 'ADMINISTRATIVO'],
            ['descripcion' => 'Personal administrativo de la institución']
        );

        TipoPersona::firstOrCreate(
            ['nombre' => 'ESTUDIANTE'],
            ['descripcion' => 'Estudiantes de la institución']
        );

        TipoPersona::firstOrCreate(
            ['nombre' => 'EXTERNO'],
            ['descripcion' => 'Personas externas a la institución']
        );
    }
}
