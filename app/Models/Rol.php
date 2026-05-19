<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rol extends Model {
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    protected $fillable = ['nombre'];
    public function permisos() {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'id_rol', 'id_permiso');
    }
    public function usuarios() {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }
}
