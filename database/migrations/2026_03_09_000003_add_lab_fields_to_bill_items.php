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
        Schema::table('bill_items', function (Blueprint $table) {
            // Service type reference
            $table->string('service_type')->nullable()->after('bill_id'); // e.g., 'App\Models\LabTest', 'App\Models\LabGroup'
            $table->unsignedBigInteger('service_id')->nullable()->after('service_type');
            
            // Sample collection tracking
            $table->enum('sample_status', ['pending', 'collected', 'processing', 'completed', 'dispatched'])->default('pending')->after('net_amount');
            $table->string('sample_id')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->string('collected_by')->nullable();
            $table->text('collection_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn([
                'service_type',
                'service_id',
                'sample_status',
                'sample_id',
                'collected_at',
                'collected_by',
                'collection_notes',
            ]);
        });
    }
};
