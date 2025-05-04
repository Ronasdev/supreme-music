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
        Schema::table('users', function (Blueprint $table) {
            // Ajout d'une colonne 'role' de type string, qui peut être 'user' ou 'admin'
            // La valeur par défaut est 'user' pour que tous les utilisateurs créés soient des utilisateurs normaux
            // sauf indication contraire
            $table->string('role')->default('user')->after('password');
            
            // Ajout d'un index pour accélérer les recherches basées sur le rôle
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           // Suppression de la colonne 'role' en cas de rollback de la migration
           $table->dropColumn('role');
        });
    }
};
