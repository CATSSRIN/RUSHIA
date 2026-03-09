<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ransum_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('file_hash', 64)->unique();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('vessel_code')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('voyage')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('year')->nullable();
            $table->string('date_start')->nullable();
            $table->string('date_end')->nullable();
            $table->string('jumlah_hari_pensupplaian')->nullable();
            $table->string('eta')->nullable();
            $table->string('vessel_route')->nullable();
            $table->string('rute_sekarang')->nullable();
            $table->string('port_tujuan')->nullable();
            $table->string('currency')->nullable();
            $table->string('conversi_rupiah')->nullable();
            $table->string('jumlah_crew')->nullable();
            $table->string('vendor_name')->nullable();
            $table->decimal('barang_non_bkp', 20, 2)->nullable();
            $table->decimal('barang_bkp', 20, 2)->nullable();
            $table->decimal('pajak_11', 20, 2)->nullable();
            $table->decimal('budget', 20, 2)->nullable();
            $table->decimal('total_belanja_ransum', 20, 2)->nullable();
            $table->decimal('selisih_anggaran', 20, 2)->nullable();
            $table->enum('status', ['pending', 'imported'])->default('pending');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ransum_uploads');
    }
};
