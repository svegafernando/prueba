<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('categorias')->insert([
            'nombre' => 'Categoría predeterminada',
            'descripcion' => 'Esta es la categoría predeterminada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::table('categorias')
            ->where('nombre', 'Categoría predeterminada')
            ->delete();
    }
};
