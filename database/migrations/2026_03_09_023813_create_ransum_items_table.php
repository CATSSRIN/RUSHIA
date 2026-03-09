<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ransum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ransum_upload_id')->constrained('ransum_uploads')->cascadeOnDelete();
            $table->string('section')->nullable();
            $table->string('nama_ransum')->nullable();
            $table->string('kode_item')->nullable();
            $table->string('items')->nullable();
            $table->string('merk_spec')->nullable();
            $table->decimal('ppn', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('harga', 20, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('qty', 15, 4)->nullable();
            $table->decimal('non_bkp', 20, 2)->nullable();
            $table->decimal('bkp', 20, 2)->nullable();
            $table->decimal('ppn_11', 20, 2)->nullable();
            $table->text('ket_remarks')->nullable();
            $table->string('status_received')->nullable();
            $table->string('good_received')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ransum_items');
    }
};
