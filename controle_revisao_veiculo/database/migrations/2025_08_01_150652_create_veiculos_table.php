<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->string('placa', 15)->primary();
            $table->string('marca', 20);
            $table->string('modelo', 20);
            $table->integer('ano');
            $table->unsignedBigInteger('fk_cliente_id_cliente');
            $table->foreign('fk_cliente_id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
