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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phoneNumber')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('status')->default('queued'); // in queue, skipped or done cutting
            $table->string('skipCount')->default(0); // number of times skipped
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('bookingSlot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
