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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 100)->nullable(false);
            $table->integer('tipo_id')->nullable(false);
            $table->text('consulta_sql')->nullable(false);
            $table->boolean('ativo')->nullable(false)->default(true);
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
        Schema::dropIfExists('checks');
    }
};
