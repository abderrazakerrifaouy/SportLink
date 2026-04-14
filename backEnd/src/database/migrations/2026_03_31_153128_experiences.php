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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idClub')->constrained('clubs')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->date('joinDate');
            $table->date('endDate')->nullable();
            $table->string('place');
            $table->enum('categoryType', ['SENIOR', 'ESPOIR', 'JUNIOR', 'CADET', 'MINIM']);
            $table->foreignId('joueur_id')->constrained('joueurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
