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
        DB::statement(
            "ALTER TABLE revisoes 
            ALTER COLUMN data_fim TYPE TIMESTAMP 
            USING ((date(data_inicio)::text || ' ' || data_fim::text)::timestamp)"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE revisoes ALTER COLUMN data_fim TYPE TIME USING data_fim::time');
    }
};
