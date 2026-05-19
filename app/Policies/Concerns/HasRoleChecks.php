<?php

namespace App\Policies\Concerns;

use App\Models\Usuario;

trait HasRoleChecks
{
    protected function esAdmin(Usuario $usuario): bool
    {
        return $usuario->activo && $usuario->rol && strtolower($usuario->rol->nombre) === 'admin';
    }

    protected function esTesorero(Usuario $usuario): bool
    {
        return $usuario->activo && $usuario->rol && strtolower($usuario->rol->nombre) === 'tesorero';
    }

    protected function esSecretario(Usuario $usuario): bool
    {
        return $usuario->activo && $usuario->rol && strtolower($usuario->rol->nombre) === 'secretario';
    }

    protected function esAdminOTesorero(Usuario $usuario): bool
    {
        if (!$usuario->activo || !$usuario->rol) return false;
        $rol = strtolower($usuario->rol->nombre);
        return in_array($rol, ['admin', 'tesorero']);
    }

    protected function esAdminOSecretario(Usuario $usuario): bool
    {
        if (!$usuario->activo || !$usuario->rol) return false;
        $rol = strtolower($usuario->rol->nombre);
        return in_array($rol, ['admin', 'secretario']);
    }

    protected function tieneAcceso(Usuario $usuario): bool
    {
        return $usuario->activo;
    }
}
