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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('host', 100)->nullable(false);
            $table->integer('porta')->nullable(false);
            $table->string('usuario', 100)->nullable(false);
            $table->string('senha', 100)->nullable(false);
            $table->string('nome_banco', 100)->nullable(false);
            $table->string('driver', 100)->nullable(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
