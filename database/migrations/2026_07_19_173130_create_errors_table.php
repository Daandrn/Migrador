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
        Schema::create('verify_types', function (Blueprint $table) {
            $table->id();
            $table->text('descricao')->nullable(false);
            $table->timestamps();
        });

        DB::table('verify_types')->insert([
            [
                'id' => 1,
                'descricao' => 'Compras',
            ],
            [
                'id' => 2,
                'descricao' => 'Licitação',
            ],
            [
                'id' => 3,
                'descricao' => 'Contratos',
            ],
        ]);
        
        Schema::create('errors', function (Blueprint $table) {
            $table->id();
            $table->text('data')->nullable(false);
            $table->integer('tipo_id')->nullable(false);
            $table->timestamps();

            $table->index('tipo_id');

            $table->foreign('tipo_id')->references('id')->on('verify_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('errors');
        Schema::dropIfExists('verify_types');
    }
};
