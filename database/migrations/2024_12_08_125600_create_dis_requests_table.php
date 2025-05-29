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
        Schema::create('dis_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->float('request_size');
            $table->string('request_type'); // Dropdown or auto-filled
            $table->text('address');
            $table->string('tel_number');
            $table->string('request_owner');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed']); // Dropdown choices
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dis_requests');
    }
};
