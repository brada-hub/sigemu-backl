<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaCosto extends Model {
    use SoftDeletes;
    protected $table = 'categorias_costo';
    protected $primaryKey = 'id_categoria_costo';
    protected $fillable = ['festividad_id', 'id_tipo_fraterno', 'nombre', 'monto_total'];
    public function festividad() {
        return $this->belongsTo(Festividad::class, 'festividad_id', 'id_festividad');
    }
    public function tipoFraterno() {
        return $this->belongsTo(TipoFraterno::class, 'id_tipo_fraterno', 'id_tipo_fraterno');
    }
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'categoria_costo_id', 'id_categoria_costo');
    }
}
