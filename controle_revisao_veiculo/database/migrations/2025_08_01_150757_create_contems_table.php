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
        Schema::create('contems', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_servico_id_servico');
            $table->unsignedBigInteger('fk_revisao_id_revisao');
            $table->primary(['fk_servico_id_servico', 'fk_revisao_id_revisao']);
            $table->foreign('fk_servico_id_servico')->references('id_servico')->on('servicos')->onDelete('restrict');
            $table->foreign('fk_revisao_id_revisao')->references('id_revisao')->on('revisoes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contems');
    }
};
