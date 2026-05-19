<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class ReportePolicy
{
    use HasRoleChecks;

    // Ver reportes y exportar — admin y tesorero
    public function ver(Usuario $usuario): bool
    {
        return $this->esAdminOTesorero($usuario);
    }

    public function exportar(Usuario $usuario): bool
    {
        return $this->esAdminOTesorero($usuario);
    }
}
