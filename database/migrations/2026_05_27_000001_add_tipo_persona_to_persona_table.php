<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('persona', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tipo_persona')->nullable()->after('id_sexo');
            $table->foreign('id_tipo_persona')->references('id_tipo_persona')->on('tipo_persona')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('persona', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_persona']);
            $table->dropColumn('id_tipo_persona');
        });
    }
};
