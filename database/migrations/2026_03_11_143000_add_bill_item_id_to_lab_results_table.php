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
        Schema::table('lab_results', function (Blueprint $table) {
            $table->foreignId('bill_item_id')->nullable()->after('id')->constrained('bill_items')->onDelete('cascade');
            // Make lab_order_id nullable since results can be tied to bill_item instead
            $table->foreignId('lab_order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->dropForeign(['bill_item_id']);
            $table->dropColumn('bill_item_id');
        });
    }
};
