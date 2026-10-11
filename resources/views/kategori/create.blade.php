<x-layout title="Tambah Kategori Baru">

    <!-- Header Section -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}" class="text-decoration-none text-del">Kategori</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Kategori Baru</li>
            </ol>
        </nav>
        <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-tag text-del"></i>
            <span>Tambah Kategori Buku Baru</span>
        </h2>
        <p class="text-muted small mb-0 mt-1">Daftarkan kategori klasifikasi baru untuk pengelompokan buku.</p>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header card-header-del px-4 py-3">
                    <h5 class="mb-0 fw-bold fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        <span>Formulir Kategori Buku</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kategori.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Kode Kategori -->
                            <div class="col-md-6">
                                <label for="kode_kategori" class="form-label fw-semibold small text-secondary">
                                    Kode Kategori <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                    <input type="text"
                                           name="kode_kategori"
                                           id="kode_kategori"
                                           class="form-control border-start-0 @error('kode_kategori') is-invalid @enderror"
                                           placeholder="Contoh: RPL-01, AI-02"
                                           value="{{ old('kode_kategori') }}"
                                           required>
                                    @error('kode_kategori')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small">Harus unik dan belum pernah digunakan.</div>
                            </div>

                            <!-- Nama Kategori -->
                            <div class="col-md-6">
                                <label for="nama_kategori" class="form-label fw-semibold small text-secondary">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag-fill"></i></span>
                                    <input type="text"
                                           name="nama_kategori"
                                           id="nama_kategori"
                                           class="form-control border-start-0 @error('nama_kategori') is-invalid @enderror"
                                           placeholder="Contoh: Rekayasa Perangkat Lunak"
                                           value="{{ old('nama_kategori') }}"
                                           required>
                                    @error('nama_kategori')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small">Minimal 3 karakter.</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-del px-4 py-2 rounded-3 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout>