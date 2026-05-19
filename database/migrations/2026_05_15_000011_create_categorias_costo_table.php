<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias_costo', function (Blueprint $table) {
            $table->id('id_categoria_costo');
            $table->unsignedBigInteger('festividad_id');
            $table->unsignedBigInteger('id_tipo_fraterno');
            $table->string('nombre');
            $table->decimal('monto_total', 10, 2);
            $table->timestamps();

            $table->foreign('festividad_id')->references('id_festividad')->on('festividad')->onDelete('cascade');
            $table->foreign('id_tipo_fraterno')->references('id_tipo_fraterno')->on('tipo_fraterno')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('categorias_costo');
    }
};
