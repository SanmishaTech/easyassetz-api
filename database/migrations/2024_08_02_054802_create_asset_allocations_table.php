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
        Schema::create('asset_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('will_id')->constrained()->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->enum('level', ['Primary', 'Secondary', 'Tertiary'])->nullable();
            $table->bigInteger('asset_id')->nullable(); // Change from bigInteger
            $table->string('asset_type')->nullable();
            $table->decimal('allocation',10,2)->nullable();
            $table->timestamps();

        
            // $table->id();
            // $table->foreignId('will_id')->constrained()->onDelete('cascade');
            // $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            // $table->enum('level', ['Primary', 'Secondary', 'Tertiary'])->nullable();
            // $table->unsignedBigInteger('asset_id')->nullable(); // Change from bigInteger
            // $table->string('asset_type')->nullable();
            // $table->decimal('allocation',10,2)->nullable();
            // $table->timestamps();
    
            // $table->index(['asset_id', 'asset_type']); // Index for better performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_allocations');
    }
};
