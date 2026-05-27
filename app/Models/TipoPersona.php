<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model {
    protected $table = 'tipo_persona';
    protected $primaryKey = 'id_tipo_persona';
    protected $fillable = ['nombre', 'descripcion'];

    public function personas() {
        return $this->hasMany(Persona::class, 'id_tipo_persona', 'id_tipo_persona');
    }
}
