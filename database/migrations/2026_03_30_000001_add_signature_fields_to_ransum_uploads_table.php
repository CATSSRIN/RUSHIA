<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ransum_uploads', function (Blueprint $table) {
            $table->string('pemohon')->nullable()->after('selisih_anggaran');
            $table->string('menyetujui')->nullable()->after('pemohon');
            $table->string('pemohon_photo')->nullable()->after('menyetujui');
            $table->string('menyetujui_photo')->nullable()->after('pemohon_photo');
        });
    }

    public function down(): void
    {
        Schema::table('ransum_uploads', function (Blueprint $table) {
            $table->dropColumn(['pemohon', 'menyetujui', 'pemohon_photo', 'menyetujui_photo']);
        });
    }
};
