<?php

namespace App\Policies;

use App\Models\Festividad;
use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class FestividadPolicy
{
    use HasRoleChecks;

    // Ver — todos los roles
    public function viewAny(Usuario $usuario): bool
    {
        return $this->tieneAcceso($usuario);
    }

    public function view(Usuario $usuario, Festividad $festividad): bool
    {
        return $this->tieneAcceso($usuario);
    }

    // Crear, editar, eliminar — solo admin
    public function create(Usuario $usuario): bool
    {
        return $this->esAdmin($usuario);
    }

    public function update(Usuario $usuario, Festividad $festividad): bool
    {
        return $this->esAdmin($usuario);
    }

    public function delete(Usuario $usuario, Festividad $festividad): bool
    {
        return $this->esAdmin($usuario);
    }
}
