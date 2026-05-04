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
        Schema::table('products', function (Blueprint $table) {
            $table->string('kode')->nullable()->after('id');
            $table->decimal('harga_supplier', 15, 2)->nullable()->after('price');
            $table->renameColumn('price', 'harga_jual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('harga_jual', 'price');
            $table->dropColumn('harga_supplier');
            $table->dropColumn('kode');
        });
    }
};
