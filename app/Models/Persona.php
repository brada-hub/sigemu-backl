<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model {
    use SoftDeletes;
    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    protected $fillable = ['nombres', 'primer_apellido', 'segundo_apellido', 'ci', 'id_sexo', 'id_tipo_persona', 'celular', 'correo_personal', 'activo'];
    public function sexo() {
        return $this->belongsTo(Sexo::class, 'id_sexo', 'id_sexo');
    }
    public function tipoPersona() {
        return $this->belongsTo(TipoPersona::class, 'id_tipo_persona', 'id_tipo_persona');
    }
    public function usuario() {
        return $this->hasOne(Usuario::class, 'id_persona', 'id_persona');
    }
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'persona_id', 'id_persona');
    }
}
