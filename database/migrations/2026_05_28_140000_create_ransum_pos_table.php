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
        Schema::create('ransum_pos', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('ransum_upload_id')->constrained('ransum_uploads')->cascadeOnDelete();
            $blueprint->string('supplier_key');
            $blueprint->string('vendor_name');
            $blueprint->string('po_number');
            $blueprint->string('pdf_path');
            $blueprint->string('status')->default('menunggu'); // menunggu, diproses, selesai
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ransum_pos');
    }
};
