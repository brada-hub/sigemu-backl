<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pagos');
            $table->unsignedBigInteger('inscripcion_id');
            $table->unsignedBigInteger('registrado_por');
            $table->decimal('monto_pagado', 10, 2);
            $table->timestamp('fecha_pago')->useCurrent();
            $table->string('metodo_pago'); // QR, Efectivo, Transferencia
            $table->string('nro_comprobante')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('inscripcion_id')->references('id_inscripcion')->on('inscripcion')->onDelete('cascade');
            $table->foreign('registrado_por')->references('id_user')->on('usuario')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};
