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
            $table->string('description', 100)->nullable(false);
            $table->integer('type_id')->nullable(false);
            $table->text('sql_query')->nullable(false);
            $table->boolean('active')->nullable(false)->default(true);
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
        Schema::dropIfExists('checks');
    }
};
