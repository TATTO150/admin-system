<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_tipo_equipo', function (Blueprint $table) {
            $table->boolean('eliminable')->default(true); // Columna 'eliminable' con valor por defecto 'true'
        });
    }

    public function down()
    {
        Schema::table('tbl_tipo_equipo', function (Blueprint $table) {
            $table->dropColumn('eliminable'); // Elimina la columna si se revierte la migración
        });
    }
};
