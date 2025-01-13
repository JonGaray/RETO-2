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
        Schema::create('machinesmaintenances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('machines_id');
            $table->foreignId('maintenances_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machinesmaintenances');
    }
};
