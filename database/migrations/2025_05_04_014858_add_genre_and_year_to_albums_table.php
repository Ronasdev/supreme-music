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
        Schema::table('albums', function (Blueprint $table) {
            // Vérification de l'existence des colonnes avant de les ajouter
            if (!Schema::hasColumn('albums', 'genre')) {
                $table->string('genre')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('albums', 'artist')) {
                $table->string('artist')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('albums', 'year')) {
                $table->year('year')->nullable()->after('genre');
            }
            
            if (!Schema::hasColumn('albums', 'views_count')) {
                $table->integer('views_count')->default(0)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            // Suppression conditionnelle des colonnes
            $columns = [];
            
            if (Schema::hasColumn('albums', 'genre')) {
                $columns[] = 'genre';
            }
            
            if (Schema::hasColumn('albums', 'artist')) {
                $columns[] = 'artist';
            }
            
            if (Schema::hasColumn('albums', 'year')) {
                $columns[] = 'year';
            }
            
            if (Schema::hasColumn('albums', 'views_count')) {
                $columns[] = 'views_count';
            }
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
