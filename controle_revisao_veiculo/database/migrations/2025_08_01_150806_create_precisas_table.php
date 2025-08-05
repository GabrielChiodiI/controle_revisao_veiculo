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
        Schema::create('precisas', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_peca_codigo');
            $table->unsignedBigInteger('fk_servico_id_servico');
            $table->primary(['fk_peca_codigo', 'fk_servico_id_servico']);
            $table->foreign('fk_peca_codigo')->references('codigo')->on('pecas')->onDelete('set null');
            $table->foreign('fk_servico_id_servico')->references('id_servico')->on('servicos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precisas');
    }
};
