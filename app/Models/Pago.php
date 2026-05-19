<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model {
    use SoftDeletes;
    protected $table = 'pagos';
    protected $primaryKey = 'id_pagos';
    protected $fillable = ['inscripcion_id', 'registrado_por', 'monto_pagado', 'fecha_pago', 'metodo_pago', 'nro_comprobante', 'observaciones'];
    public $timestamps = true;
    protected $casts = [
        'fecha_pago' => 'datetime',
    ];
    public function inscripcion() {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id', 'id_inscripcion');
    }
    public function registradoPor() {
        return $this->belongsTo(Usuario::class, 'registrado_por', 'id_user');
    }
}
