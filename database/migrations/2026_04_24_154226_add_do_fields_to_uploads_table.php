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
    Schema::table('uploads', function (Blueprint $table) {
        $table->string('no_do')->nullable();
        $table->date('request_date')->nullable();
        $table->date('delivery_date')->nullable();
        $table->string('po_number')->nullable();
        $table->string('etb_jkt')->nullable();
        $table->string('captain')->nullable();
        $table->string('deliver_to')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            //
        });
    }
};
