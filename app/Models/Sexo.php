<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sexo extends Model {
    protected $table = 'sexo';
    protected $primaryKey = 'id_sexo';
    protected $fillable = ['sexo'];
    public function personas() {
        return $this->hasMany(Persona::class, 'id_sexo', 'id_sexo');
    }
}
