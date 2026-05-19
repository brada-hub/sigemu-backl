<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class PersonaPolicy
{
    use HasRoleChecks;

    // Ver listado y detalle — todos los roles activos
    public function viewAny(Usuario $usuario): bool
    {
        return $this->tieneAcceso($usuario);
    }

    public function view(Usuario $usuario, Persona $persona): bool
    {
        return $this->tieneAcceso($usuario);
    }

    // Crear y editar — admin y secretario
    public function create(Usuario $usuario): bool
    {
        return $this->esAdminOSecretario($usuario);
    }

    public function update(Usuario $usuario, Persona $persona): bool
    {
        return $this->esAdminOSecretario($usuario);
    }

    // Eliminar (soft delete) — solo admin
    public function delete(Usuario $usuario, Persona $persona): bool
    {
        return $this->esAdmin($usuario);
    }

    // Restaurar desde soft delete — solo admin
    public function restore(Usuario $usuario, Persona $persona): bool
    {
        return $this->esAdmin($usuario);
    }
}
