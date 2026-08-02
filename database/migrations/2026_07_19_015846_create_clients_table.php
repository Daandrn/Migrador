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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('host', 100)->nullable(false);
            $table->integer('port')->nullable(false);
            $table->string('user', 100)->nullable(false);
            $table->text('password')->nullable(false)->default('');
            $table->string('db_name', 100)->nullable(false);
            $table->string('driver', 100)->nullable(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
