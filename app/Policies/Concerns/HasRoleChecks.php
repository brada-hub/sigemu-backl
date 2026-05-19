<?php

namespace App\Policies\Concerns;

use App\Models\Usuario;

trait HasRoleChecks
{
    protected function esAdmin(Usuario $usuario): bool
    {
        if (!$usuario->activo || !$usuario->rol) return false;
        $nombreRol = strtolower($usuario->rol->nombre);
        return $nombreRol === 'admin' || 
               $usuario->hasPermission('usuarios.gestionar') || 
               $usuario->hasPermission('roles.asignar');
    }

    protected function esTesorero(Usuario $usuario): bool
    {
        if (!$usuario->activo || !$usuario->rol) return false;
        $nombreRol = strtolower($usuario->rol->nombre);
        return $nombreRol === 'tesorero' || 
               $usuario->hasPermission('pagos.registrar') ||
               $usuario->hasPermission('pagos.ver');
    }

    protected function esSecretario(Usuario $usuario): bool
    {
        if (!$usuario->activo || !$usuario->rol) return false;
        $nombreRol = strtolower($usuario->rol->nombre);
        return $nombreRol === 'secretario' || 
               $usuario->hasPermission('inscripciones.crear');
    }

    protected function esAdminOTesorero(Usuario $usuario): bool
    {
        if (!$usuario->activo) return false;
        return $this->esAdmin($usuario) || $this->esTesorero($usuario);
    }

    protected function esAdminOSecretario(Usuario $usuario): bool
    {
        if (!$usuario->activo) return false;
        return $this->esAdmin($usuario) || $this->esSecretario($usuario);
    }

    protected function tieneAcceso(Usuario $usuario): bool
    {
        return $usuario->activo;
    }
}
