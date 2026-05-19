<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_user');
            $table->unsignedBigInteger('id_persona');
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('id_rol');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_persona')->references('id_persona')->on('persona')->onDelete('cascade');
            $table->foreign('id_rol')->references('id_rol')->on('rol')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('usuario');
    }
};
