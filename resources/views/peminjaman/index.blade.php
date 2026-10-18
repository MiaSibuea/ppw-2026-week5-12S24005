
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - SIPUS-Del</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f5fb;
            font-family: Arial, sans-serif;
            color: #172033;
        }

        .navbar {
            background: #4c1d95;
        }

        .navbar-brand,
        .navbar a {
            color: white !important;
        }

        .main-container {
            max-width: 950px;
            margin: 28px auto;
            padding: 0 16px;
        }

        .panel {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .05);
            margin-bottom: 18px;
        }

        .btn-purple {
            background: #4c1d95;
            color: white;
        }

        .btn-purple:hover {
            background: #3b1475;
            color: white;
        }

        .table th {
            background: #f7f5fb;
            white-space: nowrap;
        }

        .table td,
        .table th {
            vertical-align: middle;
            font-size: 13px;
        }

        .badge-status {
            padding: 6px 9px;
            border-radius: 20px;
            font-size: 11px;
            white-space: nowrap;
        }

        .status-dipinjam {
            background: #fff0c2;
            color: #92400e;
        }

        .status-terlambat {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-dikembalikan {
            background: #d1fae5;
            color: #065f46;
        }

        footer {
            text-align: center;
            color: #64748b;
            font-size: 12px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('buku.index') }}">
            📚 SIPUS-Del
        </a>

        <div class="d-flex gap-3">
            <a class="nav-link" href="{{ route('buku.index') }}">Data Buku</a>
            <a class="nav-link" href="{{ route('kategori.index') }}">Kategori Buku</a>
        </div>
    </div>
</nav>

<div class="main-container">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">📊 Riwayat Peminjaman Buku</h4>
            <small class="text-secondary">
                Kelola transaksi peminjaman dan pengembalian buku.
            </small>
        </div>

        <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Kembali ke Katalog
        </a>
    </div>

    <div class="panel">
        <form action="{{ route('peminjaman.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="q" class="form-label fw-semibold">
                        Cari Peminjam
                    </label>
                    <input
                        type="text"
                        name="q"
                        id="q"
                        class="form-control"
                        placeholder="Nama, NIM, atau kode pinjam..."
                        value="{{ request('q') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold">
                        Filter Status
                    </label>
                    <select name="status" id="status" class="form-select">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>
                            Semua Status
                        </option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                            Dipinjam
                        </option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>
                            Terlambat
                        </option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                            Dikembalikan
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-purple flex-grow-1">
                        🔎 Cari
                    </button>
                    <a href="{{ route('peminjaman.index') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Daftar Transaksi</h6>
            <small class="text-secondary">
                Menampilkan {{ $peminjamans->firstItem() ?? 0 }}
                – {{ $peminjamans->lastItem() ?? 0 }}
                dari {{ $peminjamans->total() }} transaksi.
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Pinjam</th>
                        <th>Judul Buku</th>
                        <th>NIM</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($peminjamans as $peminjaman)
                        @php
                            $terlambat = $peminjaman->status === 'dipinjam'
                                && $peminjaman->tanggal_jatuh_tempo
                                && \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->lt(today());
                        @endphp

                        <tr>
                            <td>{{ $peminjamans->firstItem() + $loop->index }}</td>

                            <td>{{ $peminjaman->kode_pinjam }}</td>

                            <td>
                                {{ $peminjaman->buku->judul ?? 'Buku tidak ditemukan' }}
                            </td>

                            <td>{{ $peminjaman->nim_peminjam }}</td>

                            <td>{{ $peminjaman->nama_peminjam }}</td>

                            <td>
                                {{ $peminjaman->tanggal_pinjam
                                    ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $peminjaman->tanggal_jatuh_tempo
                                    ? \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $peminjaman->tanggal_kembali
                                    ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>
                                @if ($peminjaman->status === 'dikembalikan')
                                    <span class="badge-status status-dikembalikan">
                                        Dikembalikan
                                    </span>
                                @elseif ($terlambat)
                                    <span class="badge-status status-terlambat">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="badge-status status-dipinjam">
                                        Dipinjam
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($peminjaman->status === 'dipinjam')
                                    <form
                                        action="{{ route('peminjaman.kembalikan', $peminjaman->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin memproses pengembalian buku ini?')"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            Kembalikan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-secondary">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-secondary">
                                Tidak ada transaksi yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-secondary">
                Maksimal 10 transaksi per halaman.
            </small>

            {{ $peminjamans->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<footer>
    SIPUS-Del © 2026 — Sistem Informasi Perpustakaan
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>