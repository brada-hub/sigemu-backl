<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PersonaRepositoryInterface;
use App\Repositories\PersonaRepository;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use App\Repositories\InscripcionRepository;
use App\Repositories\Contracts\PagoRepositoryInterface;
use App\Repositories\PagoRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PersonaRepositoryInterface::class,    PersonaRepository::class);
        $this->app->bind(InscripcionRepositoryInterface::class, InscripcionRepository::class);
        $this->app->bind(PagoRepositoryInterface::class,       PagoRepository::class);
    }
}
