<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('persona', function (Blueprint $table) {
            $table->id('id_persona');
            $table->string('nombres');
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('ci')->unique();
            $table->unsignedBigInteger('id_sexo');
            $table->string('celular')->nullable();
            $table->string('correo_personal')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_sexo')->references('id_sexo')->on('sexo')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('persona');
    }
};
