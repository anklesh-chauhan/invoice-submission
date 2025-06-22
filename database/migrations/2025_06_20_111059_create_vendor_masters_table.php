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
        Schema::create('vendor_masters', function (Blueprint $table) {
            $table->id();
            $table->string('VendorCode', 20)->unique();
            $table->string('VendorName', 100);
            $table->enum('VendorType', ['Supplier', 'Service', 'Contractor', 'Manufacturer']);
            $table->string('ContactPerson', 100)->nullable();
            $table->string('Email', 100)->unique()->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('AddressLine1', 100)->nullable();
            $table->string('AddressLine2', 100)->nullable();
            $table->string('City', 50)->nullable();
            $table->string('State', 50)->nullable();
            $table->string('Country', 50)->nullable();
            $table->string('PostalCode', 20)->nullable();
            $table->string('TaxID', 50)->nullable();
            $table->string('PaymentTerms', 50)->nullable();
            $table->string('Currency', 3)->nullable();
            $table->string('BankName', 100)->nullable();
            $table->string('BankAccountNumber', 50)->nullable();
            $table->string('RoutingNumber', 50)->nullable();
            $table->enum('Status', ['Active', 'Inactive', 'Suspended'])->default('Active');
            $table->text('Notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_masters');
    }
};
