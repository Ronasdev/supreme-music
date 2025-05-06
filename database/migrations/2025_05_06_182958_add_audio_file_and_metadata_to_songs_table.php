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
        Schema::table('songs', function (Blueprint $table) {
            // Vérification de la présence des colonnes avant de les ajouter
            if (!Schema::hasColumn('songs', 'audio_file')) {
                $table->string('audio_file')->nullable()->after('album_id');
            }
            
            if (!Schema::hasColumn('songs', 'genre')) {
                $table->string('genre')->nullable()->after('artist');
            }
            
            if (!Schema::hasColumn('songs', 'year')) {
                $table->string('year')->nullable()->after('genre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Vérification de la présence des colonnes avant de les supprimer
            $dropColumns = [];
            
            if (Schema::hasColumn('songs', 'audio_file')) {
                $dropColumns[] = 'audio_file';
            }
            
            if (Schema::hasColumn('songs', 'genre')) {
                $dropColumns[] = 'genre';
            }
            
            if (Schema::hasColumn('songs', 'year')) {
                $dropColumns[] = 'year';
            }
            
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
