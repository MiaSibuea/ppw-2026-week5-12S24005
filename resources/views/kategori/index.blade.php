<x-layout title="Daftar Kategori Buku">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}" class="text-decoration-none text-del">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kategori Buku</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>🏷️ Manajemen Kategori Buku</span>
                <span class="badge badge-del fs-6 fw-semibold">{{ $kategoris->total() }} Kategori</span>
            </h2>
            <p class="text-muted small mb-0 mt-1">Klasifikasi bidang ilmu dan pengelompokan koleksi pustaka.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kategori.create') }}" class="btn btn-del shadow-sm px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Kategori Baru</span>
            </a>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th style="min-width: 160px;">Kode Kategori</th>
                        <th style="min-width: 250px;">Nama Kategori</th>
                        <th class="text-center" style="min-width: 140px;">Jumlah Koleksi</th>
                        <th class="text-center" style="min-width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $kategori)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ ($kategoris->currentPage() - 1) * $kategoris->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                    {{ $kategori->kode_kategori }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('kategori.show', $kategori->id) }}" class="text-decoration-none text-dark link-primary">
                                        {{ $kategori->nama_kategori }}
                                    </a>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-del px-2 py-1 rounded-pill">
                                    <i class="bi bi-book me-1"></i>{{ $kategori->bukus_count }} Buku
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm shadow-none" role="group">
                                    <a href="{{ route('kategori.show', $kategori->id) }}" class="btn btn-outline-secondary" title="Lihat Buku dalam Kategori">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-outline-primary" title="Ubah Kategori">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatan: Menghapus kategori \'{{ addslashes($kategori->nama_kategori) }}\' juga akan menghapus seluruh buku di dalamnya! Apakah Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Kategori">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <div class="mb-3 text-muted">
                                        <i class="bi bi-tags" style="font-size: 3.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Belum Ada Kategori Buku</h5>
                                    <p class="text-muted small mb-3">Silakan tambahkan data kategori terlebih dahulu sebelum menambahkan buku.</p>
                                    <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-del px-3 py-2 rounded-pill">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if($kategoris->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    Menampilkan <strong>{{ $kategoris->firstItem() }}</strong> sampai <strong>{{ $kategoris->lastItem() }}</strong> dari <strong>{{ $kategoris->total() }}</strong> kategori
                </small>
                <div>
                    {{ $kategoris->links() }}
                </div>
            </div>
        @endif
    </div>

</x-layout>