<x-layout title="Detail Buku - {{ $buku->judul }}">

    <!-- Header Section -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('buku.index') }}" class="text-decoration-none text-del">Katalog Buku</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Buku</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-book text-del"></i>
                    <span>Detail Informasi Buku</span>
                </h2>
                <p class="text-muted small mb-0 mt-1">Informasi lengkap metadata katalog perpustakaan.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-outline-primary rounded-3 px-3">
                    <i class="bi bi-pencil-square me-1"></i> Ubah Data
                </a>
                <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3 px-3">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
                <a href="{{ route('buku.index') }}" class="btn btn-del rounded-3 px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header card-header-del px-4 py-3 d-flex justify-content-between align-items-center">
            <span class="badge bg-light text-secondary border font-monospace px-3 py-2 fs-6">
                <i class="bi bi-upc-scan me-1"></i> ISBN: {{ $buku->isbn }}
            </span>
            <span class="badge badge-del px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-tag-fill me-1"></i> {{ $buku->kategori->nama_kategori }}
            </span>
        </div>
        <div class="card-body p-4 p-md-5">
            <h3 class="fw-bold text-dark mb-3">{{ $buku->judul }}</h3>

            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <small class="text-muted d-block mb-1"><i class="bi bi-person me-1"></i> Penulis</small>
                        <div class="fw-bold text-dark">{{ $buku->penulis }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <small class="text-muted d-block mb-1"><i class="bi bi-building me-1"></i> Penerbit</small>
                        <div class="fw-bold text-dark">{{ $buku->penerbit }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar-event me-1"></i> Tahun Terbit</small>
                        <div class="fw-bold text-dark">{{ $buku->tahun_terbit }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <small class="text-muted d-block mb-1"><i class="bi bi-box-seam me-1"></i> Ketersediaan Stok</small>
                        <div>
                            @if($buku->stok > 10)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6">
                                    {{ $buku->stok }} Eksemplar
                                </span>
                            @elseif($buku->stok > 0)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fs-6">
                                    {{ $buku->stok }} Eksemplar
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6">
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sinopsis -->
            <div class="mt-4 pt-4 border-top">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-file-text text-del"></i>
                    <span>Sinopsis / Deskripsi Buku</span>
                </h5>
                <div class="p-4 bg-light bg-opacity-50 border rounded-3 text-secondary lh-lg">
                    {!! nl2br(e($buku->sinopsis ?? 'Tidak ada sinopsis atau deskripsi untuk buku ini.')) !!}
                </div>
            </div>

            <div class="mt-4 pt-3 d-flex justify-content-between text-muted small border-top">
                <span><i class="bi bi-clock-history me-1"></i> Dibuat: {{ $buku->created_at->format('d M Y, H:i') }}</span>
                <span><i class="bi bi-arrow-repeat me-1"></i> Terakhir Diperbarui: {{ $buku->updated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

</x-layout>
