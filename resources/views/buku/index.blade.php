
<x-layout title="Katalog Koleksi Buku">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">
                📚 Katalog Koleksi Buku
            </h2>
            <p class="text-muted small mb-0">
                Daftar buku akademik dan referensi ilmiah.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('buku.create') }}" class="btn btn-primary">
                + Tambah Buku Baru
            </a>

            <form action="{{ route('logout') }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Yakin ingin logout dari SIPUS-Del?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    Logout
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ISBN</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bukus as $buku)
                        <tr>
                            <td>
                                {{ ($bukus->currentPage() - 1) * $bukus->perPage() + $loop->iteration }}
                            </td>

                            <td>{{ $buku->isbn }}</td>

                            <td>
                                <strong>{{ $buku->judul }}</strong>
                                <div class="small text-muted">
                                    {{ $buku->penerbit }} · {{ $buku->tahun_terbit }}
                                </div>
                            </td>

                            <td>
                                {{ $buku->kategori->nama_kategori ?? '-' }}
                            </td>

                            <td>{{ $buku->penulis }}</td>

                            <td>
                                @if($buku->stok > 0)
                                    <span class="badge bg-success">
                                        {{ $buku->stok }} tersedia
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Habis
                                    </span>
                                @endif
                            </td>

                            <td style="min-width: 250px;">
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    <a href="{{ route('buku.show', $buku->id) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Detail
                                    </a>

                                    <a href="{{ route('buku.edit', $buku->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                </div>

                                @if($buku->stok > 0)
                                    <form action="{{ route('peminjaman.pinjam', $buku->id) }}"
                                          method="POST"
                                          class="border rounded-3 p-2">
                                        @csrf

                                        <input type="text"
                                               name="nim_peminjam"
                                               class="form-control form-control-sm mb-2"
                                               placeholder="NIM peminjam"
                                               maxlength="15"
                                               required>

                                        <input type="text"
                                               name="nama_peminjam"
                                               class="form-control form-control-sm mb-2"
                                               placeholder="Nama peminjam"
                                               maxlength="150"
                                               required>

                                        <button type="submit"
                                                class="btn btn-sm btn-success w-100"
                                                onclick="return confirm('Catat peminjaman buku ini?')">
                                            Pinjam Buku
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        Stok Habis
                                    </button>
                                @endif

                                <form action="{{ route('buku.destroy', $buku->id) }}"
                                      method="POST"
                                      class="mt-2"
                                      onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                        Hapus Buku
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Belum ada data buku.
                                <a href="{{ route('buku.create') }}">
                                    Tambah buku
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $bukus->links() }}
        </div>
    </div>

</x-layout>