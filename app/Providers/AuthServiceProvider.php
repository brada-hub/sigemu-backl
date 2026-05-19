<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Persona;
use App\Models\Festividad;
use App\Models\CategoriaCosto;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Usuario;
use App\Policies\PersonaPolicy;
use App\Policies\FestividadPolicy;
use App\Policies\CategoriaCostoPolicy;
use App\Policies\InscripcionPolicy;
use App\Policies\PagoPolicy;
use App\Policies\UsuarioPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Persona::class       => PersonaPolicy::class,
        Festividad::class    => FestividadPolicy::class,
        CategoriaCosto::class => CategoriaCostoPolicy::class,
        Inscripcion::class   => InscripcionPolicy::class,
        Pago::class          => PagoPolicy::class,
        Usuario::class       => UsuarioPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
