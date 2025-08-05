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
        Schema::create('realizas', function (Blueprint $table) {
            $table->string('fk_veiculo_placa', 15);
            $table->unsignedBigInteger('fk_revisao_id_revisao');
            $table->primary(['fk_veiculo_placa', 'fk_revisao_id_revisao']);
            $table->foreign('fk_veiculo_placa')->references('placa')->on('veiculos')->onDelete('restrict');
            $table->foreign('fk_revisao_id_revisao')->references('id_revisao')->on('revisoes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realizas');
    }
};
