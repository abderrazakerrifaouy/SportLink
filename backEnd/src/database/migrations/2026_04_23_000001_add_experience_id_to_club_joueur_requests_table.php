<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_joueur_requests', function (Blueprint $table) {
            $table->foreignId('experience_id')
                ->nullable()
                ->after('status')
                ->constrained('experiences')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('club_joueur_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('experience_id');
        });
    }
};
