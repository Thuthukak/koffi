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
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phoneNumber')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::table('barbers', function (Blueprint $table) {
            $table->integer('experience')->default(1)->after('bio');
            $table->decimal('rating', 2, 1)->default(4.0)->after('experience');
            $table->string('specialty', 50)->default('General Barber')->after('rating');
        });
    }

public function down()
{
    Schema::dropIfExists('barbers');
    Schema::table('barbers', function (Blueprint $table) {
        $table->dropColumn(['experience', 'rating', 'specialty']);
    });
}
};