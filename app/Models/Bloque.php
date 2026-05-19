<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloque extends Model {
    use SoftDeletes;
    protected $table = 'bloques';
    protected $primaryKey = 'id_bloque';
    protected $fillable = ['nombre', 'id_fraternidad'];
    public function fraternidad() {
        return $this->belongsTo(Fraternidad::class, 'id_fraternidad', 'id_fraternidad');
    }
    public function inscripciones() {
        return $this->hasMany(Inscripcion::class, 'id_bloque', 'id_bloque');
    }
}
