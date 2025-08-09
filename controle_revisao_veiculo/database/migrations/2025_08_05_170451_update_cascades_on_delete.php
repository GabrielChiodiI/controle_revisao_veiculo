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
        // VEICULOS: FK para clientes ON DELETE CASCADE
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropForeign(['fk_cliente_id_cliente']);
            $table->foreign('fk_cliente_id_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->onDelete('cascade');
        });

        // REALIZAS: FK para veiculos e revisoes ON DELETE CASCADE
        Schema::table('realizas', function (Blueprint $table) {
            $table->dropForeign(['fk_veiculo_placa']);
            $table->dropForeign(['fk_revisao_id_revisao']);
            $table->foreign('fk_veiculo_placa')
                ->references('placa')
                ->on('veiculos')
                ->onDelete('cascade');
            $table->foreign('fk_revisao_id_revisao')
                ->references('id_revisao')
                ->on('revisoes')
                ->onDelete('cascade');
        });

        // CONTEMS: FK para revisoes ON DELETE CASCADE
        Schema::table('contems', function (Blueprint $table) {
            $table->dropForeign(['fk_revisao_id_revisao']);
            $table->foreign('fk_revisao_id_revisao')
                ->references('id_revisao')
                ->on('revisoes')
                ->onDelete('cascade');
            // fk_servico_id_servico já está como RESTRICT (não muda)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverte para ON DELETE RESTRICT
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropForeign(['fk_cliente_id_cliente']);
            $table->foreign('fk_cliente_id_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->onDelete('restrict');
        });

        Schema::table('realizas', function (Blueprint $table) {
            $table->dropForeign(['fk_veiculo_placa']);
            $table->dropForeign(['fk_revisao_id_revisao']);
            $table->foreign('fk_veiculo_placa')
                ->references('placa')
                ->on('veiculos')
                ->onDelete('restrict');
            $table->foreign('fk_revisao_id_revisao')
                ->references('id_revisao')
                ->on('revisoes')
                ->onDelete('restrict');
        });

        Schema::table('contems', function (Blueprint $table) {
            $table->dropForeign(['fk_revisao_id_revisao']);
            $table->foreign('fk_revisao_id_revisao')
                ->references('id_revisao')
                ->on('revisoes')
                ->onDelete('restrict');
        });
    }
};
