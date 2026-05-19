<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscripcion extends Model {
    use SoftDeletes;
    protected $table = 'inscripcion';
    protected $primaryKey = 'id_inscripcion';
    protected $fillable = ['persona_id', 'festividad_id', 'id_bloque', 'id_tipo_fraterno', 'categoria_costo_id', 'monto_asignado', 'estado_pago', 'inscrito_at'];
    public $timestamps = true;
    protected $casts = [
        'inscrito_at' => 'datetime',
    ];
    public function persona() {
        return $this->belongsTo(Persona::class, 'persona_id', 'id_persona');
    }
    public function festividad() {
        return $this->belongsTo(Festividad::class, 'festividad_id', 'id_festividad');
    }
    public function bloque() {
        return $this->belongsTo(Bloque::class, 'id_bloque', 'id_bloque');
    }
    public function tipoFraterno() {
        return $this->belongsTo(TipoFraterno::class, 'id_tipo_fraterno', 'id_tipo_fraterno');
    }
    public function categoriaCosto() {
        return $this->belongsTo(CategoriaCosto::class, 'categoria_costo_id', 'id_categoria_costo');
    }
    public function pagos() {
        return $this->hasMany(Pago::class, 'inscripcion_id', 'id_inscripcion');
    }
}
