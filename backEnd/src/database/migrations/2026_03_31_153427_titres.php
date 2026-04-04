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
        Schema::create('titres', function (Blueprint $table) {
            $table->id();
            $table->string('nomTitre');
            $table->string('image')->nullable();
            $table->text('description');
            $table->integer('number');
            $table->foreignId('club_admin_id')->constrained('club_admins')->onDelete('cascade');
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
