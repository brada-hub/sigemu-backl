<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Fraternidad;
use App\Models\Sexo;
use App\Models\TipoFraterno;
use App\Models\Bloque;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Festividad;
use App\Models\CategoriaCosto;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fraternidad
        $fraternidad = Fraternidad::create(['nombre' => 'MORENADA UNITEPC']);

        // 2. Sexo
        $sexoM = Sexo::create(['sexo' => 'MASCULINO']);
        $sexoF = Sexo::create(['sexo' => 'FEMENINO']);

        // 3. Tipo Fraterno
        $tipoNuevo = TipoFraterno::create(['nombre' => 'NUEVO']);
        $tipoAntiguo = TipoFraterno::create(['nombre' => 'ANTIGUO']);
        ;

        // 4. Bloques
        $bloqueCentral = Bloque::create(['nombre' => 'Central', 'id_fraternidad' => $fraternidad->id_fraternidad]);
        $bloqueIntocables = Bloque::create(['nombre' => 'Intocables', 'id_fraternidad' => $fraternidad->id_fraternidad]);

        // 5. Roles y Permisos
        $rolAdmin = Rol::create(['nombre' => 'Admin']);
        $rolTesorero = Rol::create(['nombre' => 'Tesorero']);
        $rolSecretario = Rol::create(['nombre' => 'Secretario']);

        // Definir permisos
        $permisos = [
            // Personas
            ['slug' => 'personas.ver', 'descripcion' => 'Ver lista de fraternos'],
            ['slug' => 'personas.crear', 'descripcion' => 'Registrar nuevos fraternos'],
            ['slug' => 'personas.editar', 'descripcion' => 'Editar datos de fraternos'],
            ['slug' => 'personas.eliminar', 'descripcion' => 'Eliminar fraternos'],
            // Festividades
            ['slug' => 'festividades.gestionar', 'descripcion' => 'Crear festividades y categorías de costo'],
            // Inscripciones
            ['slug' => 'inscripciones.crear', 'descripcion' => 'Inscribir fraternos a festividades'],
            ['slug' => 'inscripciones.retirar', 'descripcion' => 'Retirar fraternos de festividades'],
            // Pagos y Reportes
            ['slug' => 'pagos.registrar', 'descripcion' => 'Registrar cobros y pagos'],
            ['slug' => 'pagos.ver', 'descripcion' => 'Ver lista de pagos realizados'],
            ['slug' => 'reportes.ver', 'descripcion' => 'Ver estado de deudas y reportes'],
            ['slug' => 'pagos.eliminar', 'descripcion' => 'Eliminar o anular pagos'],
            // Usuarios y Sistema
            ['slug' => 'usuarios.gestionar', 'descripcion' => 'Crear y editar cuentas de usuario'],
            ['slug' => 'roles.asignar', 'descripcion' => 'Asignar roles y permisos'],
        ];

        $permisosIds = [];
        foreach ($permisos as $p) {
            $permiso = Permiso::create($p);
            $permisosIds[$p['slug']] = $permiso->id_permiso;
        }

        // Asignar permisos al Admin (Todos)
        $rolAdmin->permisos()->sync(array_values($permisosIds));

        // Asignar permisos al Tesorero
        $rolTesorero->permisos()->sync([
            $permisosIds['personas.ver'],
            $permisosIds['pagos.registrar'],
            $permisosIds['pagos.ver'],
            $permisosIds['reportes.ver'],
        ]);

        // Asignar permisos al Secretario
        $rolSecretario->permisos()->sync([
            $permisosIds['personas.ver'],
            $permisosIds['personas.crear'],
            $permisosIds['personas.editar'],
            $permisosIds['inscripciones.crear'],
            $permisosIds['inscripciones.retirar'],
        ]);

        // 6. Personas y Usuarios
        $personaAdmin = Persona::create([
            'nombres' => 'Admin',
            'primer_apellido' => 'Sistema',
            'ci' => '0000000',
            'id_sexo' => $sexoM->id_sexo,
            'celular' => '70000000',
            'correo_personal' => 'admin@morenada.com'
        ]);

        $usuarioAdmin = Usuario::create([
            'id_persona' => $personaAdmin->id_persona,
            'username' => 'admin',
            'password' => Hash::make('password'),
            'id_rol' => $rolAdmin->id_rol
        ]);

        // 7. Festividad y Categorias
        $festividad = Festividad::create([
            'nombre' => 'VIRGEN DE URKUPIÑA 2026',
            'fecha_inicio' => '14-08-2026',
            'fecha_fin' => '18-08-2026',
            'estado' => 'Activa'
        ]);

        CategoriaCosto::create([
            'festividad_id' => $festividad->id_festividad,
            'id_tipo_fraterno' => $tipoNuevo->id_tipo_fraterno,
            'nombre' => 'CUOTA COMPLETA NUEVO',
            'monto_total' => 1200
        ]);

        CategoriaCosto::create([
            'festividad_id' => $festividad->id_festividad,
            'id_tipo_fraterno' => $tipoAntiguo->id_tipo_fraterno,
            'nombre' => 'CUOTA COCMPLETA ANTIGUO',
            'monto_total' => 800
        ]);
    }
}
