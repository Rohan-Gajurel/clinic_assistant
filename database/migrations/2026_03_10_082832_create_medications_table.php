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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->string('drug_name');
            $table->enum('route', ['oral', 'intramuscular', 'intravenous', 'subcutaneous', 'topical', 'inhalation', 'rectal', 'sublingual'])->default('oral');
            $table->string('dose')->nullable();
            $table->string('dose_unit')->nullable();
            $table->enum('frequency', ['once_daily', 'twice_daily', 'thrice_daily', 'four_times_daily', 'as_needed', 'every_4_hours', 'every_6_hours', 'every_8_hours', 'every_12_hours', 'weekly'])->nullable();
            $table->integer('duration_value')->nullable();
            $table->enum('duration_unit', ['days', 'weeks', 'months'])->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
