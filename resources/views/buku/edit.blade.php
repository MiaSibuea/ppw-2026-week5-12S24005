<x-layout title="Ubah Data Buku">

    <!-- Header Section -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('buku.index') }}" class="text-decoration-none text-del">Katalog Buku</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Buku</li>
            </ol>
        </nav>
        <h2 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-del"></i>
            <span>Ubah Data Buku</span>
        </h2>
        <p class="text-muted small mb-0 mt-1">Perbarui data buku <strong>"{{ $buku->judul }}"</strong> pada sistem katalog.</p>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header card-header-del px-4 py-3">
                    <h5 class="mb-0 fw-bold fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-sliders"></i>
                        <span>Formulir Pembaruan Data Buku</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('buku.update', $buku->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- ISBN -->
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-semibold small text-secondary">
                                    Nomor ISBN <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text"
                                           name="isbn"
                                           id="isbn"
                                           class="form-control border-start-0 @error('isbn') is-invalid @enderror"
                                           value="{{ old('isbn', $buku->isbn) }}"
                                           required>
                                    @error('isbn')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small">Nomor ISBN harus unik.</div>
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold small text-secondary">
                                    Kategori Buku <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag"></i></span>
                                    <select name="kategori_id"
                                            id="kategori_id"
                                            class="form-select border-start-0 @error('kategori_id') is-invalid @enderror"
                                            required>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $buku->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                                [{{ $kategori->kode_kategori }}] {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Judul Buku -->
                            <div class="col-12">
                                <label for="judul" class="form-label fw-semibold small text-secondary">
                                    Judul Lengkap Buku <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-book"></i></span>
                                    <input type="text"
                                           name="judul"
                                           id="judul"
                                           class="form-control border-start-0 @error('judul') is-invalid @enderror"
                                           value="{{ old('judul', $buku->judul) }}"
                                           required>
                                    @error('judul')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Penulis -->
                            <div class="col-md-6">
                                <label for="penulis" class="form-label fw-semibold small text-secondary">
                                    Penulis / Pengarang <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text"
                                           name="penulis"
                                           id="penulis"
                                           class="form-control border-start-0 @error('penulis') is-invalid @enderror"
                                           value="{{ old('penulis', $buku->penulis) }}"
                                           required>
                                    @error('penulis')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Penerbit -->
                            <div class="col-md-6">
                                <label for="penerbit" class="form-label fw-semibold small text-secondary">
                                    Penerbit <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text"
                                           name="penerbit"
                                           id="penerbit"
                                           class="form-control border-start-0 @error('penerbit') is-invalid @enderror"
                                           value="{{ old('penerbit', $buku->penerbit) }}"
                                           required>
                                    @error('penerbit')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tahun Terbit -->
                            <div class="col-md-6">
                                <label for="tahun_terbit" class="form-label fw-semibold small text-secondary">
                                    Tahun Terbit <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number"
                                           name="tahun_terbit"
                                           id="tahun_terbit"
                                           class="form-control border-start-0 @error('tahun_terbit') is-invalid @enderror"
                                           min="1900"
                                           max="{{ date('Y') + 1 }}"
                                           value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                                           required>
                                    @error('tahun_terbit')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stok -->
                            <div class="col-md-6">
                                <label for="stok" class="form-label fw-semibold small text-secondary">
                                    Jumlah Stok Buku <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box-seam"></i></span>
                                    <input type="number"
                                           name="stok"
                                           id="stok"
                                           class="form-control border-start-0 @error('stok') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('stok', $buku->stok) }}"
                                           required>
                                    @error('stok')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Sinopsis -->
                            <div class="col-12">
                                <label for="sinopsis" class="form-label fw-semibold small text-secondary">
                                    Sinopsis / Deskripsi Ringkas Buku
                                </label>
                                <textarea name="sinopsis"
                                          id="sinopsis"
                                          rows="4"
                                          class="form-control @error('sinopsis') is-invalid @enderror">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                                @error('sinopsis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-del px-4 py-2 rounded-3 shadow-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> Perbarui Data Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout>