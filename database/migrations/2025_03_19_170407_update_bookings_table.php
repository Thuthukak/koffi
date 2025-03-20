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
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('queue_position')->nullable()->after('status');
            $table->timestamp('expected_start_time')->nullable()->after('queue_position');
            $table->integer('time_remaining')->default(0)->after('expected_start_time');
            $table->boolean('notification_sent')->default(false)->after('time_remaining');
            $table->timestamp('skipped_at')->nullable()->after('notification_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['queue_position', 'expected_start_time', 'time_remaining', 'notification_sent', 'skipped_at']);
        });
    }
};
