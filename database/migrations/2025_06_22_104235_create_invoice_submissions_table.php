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
        Schema::create('invoice_submissions', function (Blueprint $table) {
            $table->id();
            $table->date('invoice_date');
            $table->foreignId('vendor_id')->constrained('vendor_masters')->onDelete('cascade');
            $table->string('invoice_number')->unique()->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('sent_to_user_id')->constrained('users')->onDelete('cascade');
            $table->json('invoice_files')->nullable();
            $table->string('status')->default('pending'); // Assuming a status field for tracking
            $table->text('notes')->nullable(); // Optional notes field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_submissions');
    }
};
