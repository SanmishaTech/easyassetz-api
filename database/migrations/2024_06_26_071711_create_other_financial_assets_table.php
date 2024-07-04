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
        Schema::create('other_financial_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('bank_service_provider')->nullable();
            $table->string('folio_number')->nullable();
            $table->string('branch_name')->nullable();
            $table->enum('nature_of_holding',['single','joint'])->nullable();
            $table->string('joint_holder_name')->nullable();
            $table->string('joint_holder_pan')->nullable();
            $table->string('additional_details')->nullable();
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_financial_assets');
    }
};
