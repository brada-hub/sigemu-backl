<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fraternidad extends Model {
    use SoftDeletes;
    protected $table = 'fraternidad';
    protected $primaryKey = 'id_fraternidad';
    protected $fillable = ['nombre'];
    public function bloques() {
        return $this->hasMany(Bloque::class, 'id_fraternidad', 'id_fraternidad');
    }
}
