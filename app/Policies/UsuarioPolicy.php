<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class UsuarioPolicy
{
    use HasRoleChecks;

    // Gestión de usuarios — solo admin
    public function viewAny(Usuario $usuario): bool
    {
        return $this->esAdmin($usuario);
    }

    public function view(Usuario $usuario, Usuario $model): bool
    {
        return $this->esAdmin($usuario);
    }

    public function create(Usuario $usuario): bool
    {
        return $this->esAdmin($usuario);
    }

    public function update(Usuario $usuario, ?Usuario $model = null): bool
    {
        return $this->esAdmin($usuario);
    }

    // Un admin no puede desactivarse a sí mismo
    public function delete(Usuario $usuario, ?Usuario $model = null): bool
    {
        if (!$model) return $this->esAdmin($usuario);
        return $this->esAdmin($usuario) && $usuario->id_user !== $model->id_user;
    }
}
