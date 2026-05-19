<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Festividad extends Model {
    use SoftDeletes;
    protected $table = 'festividad';
    protected $primaryKey = 'id_festividad';
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'estado'];
    public function categoriasCosto() {
        return $this->hasMany(CategoriaCosto::class, 'festividad_id', 'id_festividad');
    }
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'festividad_id', 'id_festividad');
    }
}
