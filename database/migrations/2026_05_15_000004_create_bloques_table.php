<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bloques', function (Blueprint $table) {
            $table->id('id_bloque');
            $table->string('nombre');
            $table->unsignedBigInteger('id_fraternidad');
            $table->timestamps();

            $table->foreign('id_fraternidad')->references('id_fraternidad')->on('fraternidad')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('bloques');
    }
};
