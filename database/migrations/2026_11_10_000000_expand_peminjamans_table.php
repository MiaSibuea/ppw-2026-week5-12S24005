<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('peminjamen') && ! Schema::hasTable('peminjamans')) {
            Schema::rename('peminjamen', 'peminjamans');
        }

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->foreignId('buku_id')->after('id')->constrained('bukus')->cascadeOnDelete();
            $table->string('nim_peminjam', 30)->after('buku_id')->index();
            $table->string('nama_peminjam', 100)->after('nim_peminjam');
            $table->string('status', 20)->default('dipinjam')->after('nama_peminjam')->index();
            $table->unsignedSmallInteger('durasi_hari')->default(14)->after('status');
            $table->date('tanggal_pinjam')->after('durasi_hari');
            $table->date('tanggal_jatuh_tempo')->after('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable()->after('tanggal_jatuh_tempo');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('peminjamans')) {
            return;
        }

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['buku_id']);
            $table->dropIndex(['nim_peminjam']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'buku_id',
                'nim_peminjam',
                'nama_peminjam',
                'status',
                'durasi_hari',
                'tanggal_pinjam',
                'tanggal_jatuh_tempo',
                'tanggal_kembali',
            ]);
        });

        if (! Schema::hasTable('peminjamen')) {
            Schema::rename('peminjamans', 'peminjamen');
        }
    }
};
