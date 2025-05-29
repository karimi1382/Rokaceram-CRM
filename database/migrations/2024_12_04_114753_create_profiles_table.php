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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the User table
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('profile_picture')->nullable();
            $table->foreignId('personel_id')->nullable()->constrained('users')->onDelete('set null'); // Self-referencing user table
            $table->string('customer_type')->nullable(); // customer type (admin, personnel, manager, distributor)
            $table->timestamp('last_login_at')->nullable(); // last login date

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
