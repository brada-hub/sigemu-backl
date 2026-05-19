<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('festividad_id');
            $table->unsignedBigInteger('id_bloque');
            $table->unsignedBigInteger('id_tipo_fraterno');
            $table->unsignedBigInteger('categoria_costo_id');
            $table->decimal('monto_asignado', 10, 2);
            $table->string('estado_pago')->default('Pendiente'); // Pendiente, Parcial, Pagado
            $table->timestamp('inscrito_at')->useCurrent();
            $table->timestamps();

            $table->foreign('persona_id')->references('id_persona')->on('persona')->onDelete('cascade');
            $table->foreign('festividad_id')->references('id_festividad')->on('festividad')->onDelete('cascade');
            $table->foreign('id_bloque')->references('id_bloque')->on('bloques')->onDelete('cascade');
            $table->foreign('id_tipo_fraterno')->references('id_tipo_fraterno')->on('tipo_fraterno')->onDelete('cascade');
            $table->foreign('categoria_costo_id')->references('id_categoria_costo')->on('categorias_costo')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('inscripcion');
    }
};
