<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\Usuario;
use App\Policies\Concerns\HasRoleChecks;

class PagoPolicy
{
    use HasRoleChecks;

    // Ver pagos — admin y tesorero únicamente
    public function viewAny(Usuario $usuario): bool
    {
        return $this->esAdminOTesorero($usuario);
    }

    public function view(Usuario $usuario, Pago $pago): bool
    {
        return $this->esAdminOTesorero($usuario);
    }

    // Registrar pago — admin y tesorero
    public function create(Usuario $usuario): bool
    {
        return $this->esAdminOTesorero($usuario);
    }

    // Eliminar pago — solo admin (operación delicada)
    public function delete(Usuario $usuario, Pago $pago): bool
    {
        return $this->esAdmin($usuario);
    }
}
