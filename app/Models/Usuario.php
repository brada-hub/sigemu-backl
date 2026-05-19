<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Usuario extends Authenticatable {
    use HasApiTokens, Notifiable;
    protected $table = 'usuario';
    protected $primaryKey = 'id_user';
    protected $fillable = ['id_persona', 'username', 'password', 'id_rol', 'activo', 'debe_cambiar_password'];
    protected $hidden = ['password', 'remember_token'];
    public function persona() {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }
    public function rol() {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
    public function pagosRegistrados() {
        return $this->hasMany(Pago::class, 'registrado_por', 'id_user');
    }
    public function hasPermission($permissionSlug) {
        if (!$this->rol) return false;
        return $this->rol->permisos()->where('slug', $permissionSlug)->exists();
    }
}
