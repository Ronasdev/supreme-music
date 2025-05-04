<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajoute un champ transaction_id à la table orders pour stocker les identifiants
     * de transaction des paiements Orange Money
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ajout du champ transaction_id pour stocker l'ID de transaction du paiement Orange Money
            $table->string('transaction_id')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     * Supprime le champ transaction_id de la table orders
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Suppression du champ transaction_id
            $table->dropColumn('transaction_id');
        });
    }
};
