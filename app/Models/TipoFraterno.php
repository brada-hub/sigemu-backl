<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoFraterno extends Model {
    use SoftDeletes;
    protected $table = 'tipo_fraterno';
    protected $primaryKey = 'id_tipo_fraterno';
    protected $fillable = ['nombre'];
    public function categoriasCosto() {
        return $this->hasMany(CategoriaCosto::class, 'id_tipo_fraterno', 'id_tipo_fraterno');
    }
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'id_tipo_fraterno', 'id_tipo_fraterno');
    }
}
