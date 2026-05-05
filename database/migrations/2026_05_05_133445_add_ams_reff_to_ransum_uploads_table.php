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
        Schema::table('ransum_uploads', function (Blueprint $table) {
            // Sesuaikan tipe datanya (string/integer) dan posisinya
            $table->string('ams_reff')->nullable()->after('id'); 
        });
    }

    public function down(): void
    {
        Schema::table('ransum_uploads', function (Blueprint $table) {
            $table->dropColumn('ams_reff');
        });
    }
};
