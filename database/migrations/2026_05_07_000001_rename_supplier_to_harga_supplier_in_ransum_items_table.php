<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ransum_items', function (Blueprint $table) {
            $table->renameColumn('supplier', 'harga_supplier');
        });
    }

    public function down(): void
    {
        Schema::table('ransum_items', function (Blueprint $table) {
            $table->renameColumn('harga_supplier', 'supplier');
        });
    }
};
