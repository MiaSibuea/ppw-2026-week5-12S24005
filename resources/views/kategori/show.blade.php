<x-layout title="Detail Kategori - {{ $kategori->nama_kategori }}">

    <!-- Header Section -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}" class="text-decoration-none text-del">Kategori</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Kategori</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-tag-fill text-del"></i>
                    <span>Kategori: {{ $kategori->nama_kategori }}</span>
                </h2>
                <p class="text-muted small mb-0 mt-1">Daftar seluruh buku yang terdaftar di bawah klasifikasi kategori ini.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-outline-primary rounded-3 px-3">
                    <i class="bi bi-pencil-square me-1"></i> Ubah Kategori
                </a>
                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatan: Menghapus kategori ini juga akan menghapus seluruh buku di dalamnya! Yakin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3 px-3">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
                <a href="{{ route('kategori.index') }}" class="btn btn-del rounded-3 px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Kategori
                </a>
            </div>
        </div>
    </div>

    <!-- Category Info Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header card-header-del px-4 py-3 d-flex justify-content-between align-items-center">
            <span class="badge bg-light text-secondary border font-monospace px-3 py-2 fs-6">
                <i class="bi bi-hash me-1"></i> Kode: {{ $kategori->kode_kategori }}
            </span>
            <span class="badge badge-del px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-collection me-1"></i> {{ $kategori->bukus->count() }} Judul Terdaftar
            </span>
        </div>
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3">Daftar Koleksi Buku dalam Kategori Ini:</h5>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>ISBN</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategori->bukus as $buku)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border font-monospace">
                                        {{ $buku->isbn }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">
                                    <a href="{{ route('buku.show', $buku->id) }}" class="text-decoration-none text-dark link-primary">
                                        {{ $buku->judul }}
                                    </a>
                                </td>
                                <td>{{ $buku->penulis }}</td>
                                <td class="text-muted">{{ $buku->penerbit }}</td>
                                <td class="text-center">{{ $buku->tahun_terbit }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ $buku->stok }} eks
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-outline-secondary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-outline-primary" title="Ubah">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada buku yang didaftarkan pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layout>
