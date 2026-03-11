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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained('lab_orders')->onDelete('cascade');
            $table->foreignId('lab_test_id')->constrained('lab_tests')->onDelete('cascade');
            
            // Result value - can be numeric or text based on lab_test result_type
            $table->decimal('numeric_value', 15, 4)->nullable();
            $table->text('text_value')->nullable();
            
            // Reference range from lab_test (can be overridden)
            $table->decimal('reference_from', 10, 2)->nullable();
            $table->decimal('reference_to', 10, 2)->nullable();
            $table->string('unit')->nullable();
            
            // Result status
            $table->enum('status', ['normal', 'high', 'low', 'critical', 'pending'])->default('pending');
            
            // Additional info
            $table->text('remarks')->nullable();
            $table->string('entered_by')->nullable();
            $table->timestamp('entered_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
