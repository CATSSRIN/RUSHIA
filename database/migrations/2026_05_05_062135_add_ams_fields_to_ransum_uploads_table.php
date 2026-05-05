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
            $table->string('ams_reff')->nullable()->after('deliver_to');
            $table->decimal('biaya_lembur', 20, 2)->nullable()->after('ams_reff');
            $table->decimal('sewa_perahu', 20, 2)->nullable()->after('biaya_lembur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ransum_uploads', function (Blueprint $table) {
            $table->dropColumn(['ams_reff', 'biaya_lembur', 'sewa_perahu']);
        });
    }
};
