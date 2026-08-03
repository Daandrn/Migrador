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
            $table->increments('id');
            $table->text('description')->nullable(false);
            $table->boolean('active')->nullable(false)->default(true);
            $table->timestamps();
        });

        DB::table('verify_types')->insert([
            [
                'id' => 1,
                'description' => 'Compras',
            ],
            [
                'id' => 2,
                'description' => 'Licitação',
            ],
            [
                'id' => 3,
                'description' => 'Contratos',
            ],
        ]);
        
        Schema::create('verify_errors', function (Blueprint $table) {
            $table->id();
            $table->jsonb('data')->nullable(false);
            $table->unsignedInteger('type_id', false)->nullable(false);
            $table->timestamps();

            $table->index('type_id');

            $table->foreign('type_id')->references('id')->on('verify_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verify_errors');
        Schema::dropIfExists('verify_types');
    }
};
