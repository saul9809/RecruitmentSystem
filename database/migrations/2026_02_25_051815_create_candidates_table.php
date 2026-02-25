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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->unique();
            $table->string('candidate_address');
            $table->longText('cv')->nullable();
            $table->timestamps();
            $table->index(['candidate_name', 'candidate_email', 'candidate_phone', 'candidate_address'], 'candidate_index');
            $table->index(['candidate_name', 'candidate_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
