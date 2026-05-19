<?php

namespace App\Policies;

use App\Models\Inscripcion;
use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class InscripcionPolicy
{
    use HasRoleChecks;

    // Ver — todos los roles
    public function viewAny(Usuario $usuario): bool
    {
        return $this->tieneAcceso($usuario);
    }

    public function view(Usuario $usuario, Inscripcion $inscripcion): bool
    {
        return $this->tieneAcceso($usuario);
    }

    // Inscribir — admin y secretario
    public function create(Usuario $usuario): bool
    {
        return $this->esAdminOSecretario($usuario);
    }

    // Retirar fraterno — admin y secretario
    public function retirar(Usuario $usuario, Inscripcion $inscripcion): bool
    {
        return $this->esAdminOSecretario($usuario);
    }

    // No se editan inscripciones directamente, solo se retiran
    public function update(Usuario $usuario, Inscripcion $inscripcion): bool
    {
        return $this->esAdmin($usuario);
    }

    public function delete(Usuario $usuario, Inscripcion $inscripcion): bool
    {
        return $this->esAdmin($usuario);
    }
}
