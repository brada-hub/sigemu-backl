<?php

namespace App\Policies;

use App\Models\CategoriaCosto;
use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class CategoriaCostoPolicy
{
    use HasRoleChecks;

    public function viewAny(Usuario $usuario): bool
    {
        return $this->tieneAcceso($usuario);
    }

    public function view(Usuario $usuario, CategoriaCosto $categoria): bool
    {
        return $this->tieneAcceso($usuario);
    }

    // Gestión de categorías — solo admin
    public function create(Usuario $usuario): bool
    {
        return $this->esAdmin($usuario);
    }

    public function update(Usuario $usuario, CategoriaCosto $categoria): bool
    {
        return $this->esAdmin($usuario);
    }

    public function delete(Usuario $usuario, CategoriaCosto $categoria): bool
    {
        return $this->esAdmin($usuario);
    }
}
