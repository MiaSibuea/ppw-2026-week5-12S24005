<x-layout title="Katalog Koleksi Buku">

    <!-- Header / Title Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}" class="text-decoration-none text-del">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Katalog Buku</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>📚 Katalog Koleksi Buku</span>
                <span class="badge badge-del fs-6 fw-semibold">{{ $bukus->total() }} Judul</span>
            </h2>
            <p class="text-muted small mb-0 mt-1">Daftar buku akademik dan referensi ilmiah yang terdaftar dalam SIM Perpustakaan Del.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('buku.create') }}" class="btn btn-del shadow-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Buku Baru</span>
            </a>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="min-width: 140px;">ISBN</th>
                        <th style="min-width: 240px;">Judul Buku</th>
                        <th style="min-width: 160px;">Kategori</th>
                        <th style="min-width: 160px;">Penulis & Penerbit</th>
                        <th class="text-center" style="min-width: 80px;">Tahun</th>
                        <th class="text-center" style="min-width: 90px;">Stok</th>
                        <th class="text-center" style="min-width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bukus as $buku)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ ($bukus->currentPage() - 1) * $bukus->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border font-monospace px-2 py-1">
                                    {{ $buku->isbn }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('buku.show', $buku->id) }}" class="text-decoration-none text-dark link-primary">
                                        {{ $buku->judul }}
                                    </a>
                                </div>
                                @if($buku->sinopsis)
                                    <small class="text-muted d-block text-truncate" style="max-width: 280px;">
                                        {{ Str::limit($buku->sinopsis, 60) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-del px-2 py-1 rounded-pill">
                                    <i class="bi bi-tag-fill me-1 small"></i>{{ $buku->kategori->nama_kategori }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $buku->penulis }}</div>
                                <small class="text-muted"><i class="bi bi-building me-1"></i>{{ $buku->penerbit }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    {{ $buku->tahun_terbit }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($buku->stok > 10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check2 me-1"></i>{{ $buku->stok }} eks
                                    </span>
                                @elseif($buku->stok > 0)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $buku->stok }} eks
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Habis
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm shadow-none" role="group">
                                    <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-outline-secondary" title="Detail Buku">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-outline-primary" title="Ubah Buku">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku \'{{ addslashes($buku->judul) }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Buku">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <div class="mb-3 text-muted">
                                        <i class="bi bi-journal-x" style="font-size: 3.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Belum Ada Data Buku</h5>
                                    <p class="text-muted small mb-3">Katalog buku saat ini masih kosong. Silakan tambahkan buku baru ke sistem.</p>
                                    <a href="{{ route('buku.create') }}" class="btn btn-sm btn-del px-3 py-2 rounded-pill">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Buku Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if($bukus->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    Menampilkan <strong>{{ $bukus->firstItem() }}</strong> sampai <strong>{{ $bukus->lastItem() }}</strong> dari <strong>{{ $bukus->total() }}</strong> buku
                </small>
                <div>
                    {{ $bukus->links() }}
                </div>
            </div>
        @endif
    </div>

</x-layout>