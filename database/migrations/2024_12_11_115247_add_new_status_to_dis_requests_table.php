<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('dis_requests', function (Blueprint $table) {
        // Modify the enum field to include 'In Progress'
        $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed', 'In Progress'])
              ->change();
    });
}

public function down()
{
    Schema::table('dis_requests', function (Blueprint $table) {
        // Revert back to the original statuses (before the new status was added)
        $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed'])
              ->change();
    });
}

};
