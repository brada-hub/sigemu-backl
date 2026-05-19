<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Persona;
use App\Models\Festividad;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\CategoriaCosto;
use App\Models\Usuario;
use App\Policies\PersonaPolicy;
use App\Policies\FestividadPolicy;
use App\Policies\InscripcionPolicy;
use App\Policies\PagoPolicy;
use App\Policies\CategoriaCostoPolicy;
use App\Policies\UsuarioPolicy;
use App\Policies\ReportePolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Persona::class,     PersonaPolicy::class);
        Gate::policy(Festividad::class,  FestividadPolicy::class);
        Gate::policy(Inscripcion::class, InscripcionPolicy::class);
        Gate::policy(Pago::class,        PagoPolicy::class);
        Gate::policy(CategoriaCosto::class, CategoriaCostoPolicy::class);
        Gate::policy(Usuario::class,     UsuarioPolicy::class);
        Gate::define('ver-reportes', [ReportePolicy::class, 'ver']);
        Gate::define('ver-usuarios', [UsuarioPolicy::class, 'viewAny']);
        Gate::define('crear-usuarios', [UsuarioPolicy::class, 'create']);
        Gate::define('editar-usuarios', [UsuarioPolicy::class, 'update']);
        Gate::define('eliminar-usuarios', [UsuarioPolicy::class, 'delete']);
    }
}
