<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'SIPUS-Del' }} — Perpustakaan Institut Teknologi Del</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons 1.11.3 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --del-purple: #4C1D95;
            --del-purple-hover: #3B0764;
            --del-purple-light: #F3E8FF;
            --del-purple-border: #DDD6FE;
            --del-purple-dark: #2E1065;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Del Royal Purple Theme Classes */
        .navbar-del {
            background-color: var(--del-purple);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .btn-del {
            background-color: var(--del-purple);
            color: #ffffff;
            border: 1px solid var(--del-purple);
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-del:hover, .btn-del:focus {
            background-color: var(--del-purple-hover);
            border-color: var(--del-purple-hover);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(76, 29, 149, 0.25);
        }

        .btn-outline-del {
            background-color: transparent;
            color: var(--del-purple);
            border: 1.5px solid var(--del-purple);
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-del:hover {
            background-color: var(--del-purple);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(76, 29, 149, 0.2);
        }

        .badge-del {
            background-color: var(--del-purple-light);
            color: var(--del-purple);
            border: 1px solid var(--del-purple-border);
            font-weight: 600;
        }

        .text-del {
            color: var(--del-purple) !important;
        }

        .card-header-del {
            background-color: #FAF5FF;
            border-bottom: 1px solid var(--del-purple-border);
            color: var(--del-purple-dark);
        }

        .page-item.active .page-link {
            background-color: var(--del-purple) !important;
            border-color: var(--del-purple) !important;
        }

        .page-link {
            color: var(--del-purple);
        }

        .page-link:hover {
            color: var(--del-purple-hover);
            background-color: var(--del-purple-light);
        }

        /* Smooth UI enhancements */
        .card {
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .table-custom th {
            background-color: #F1F5F9;
            color: #475569;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #E2E8F0;
        }

        .table-custom td {
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
            vertical-align: middle;
        }

        .footer {
            margin-top: auto;
            background-color: #FFFFFF;
            border-top: 1px solid #E2E8F0;
        }
    </style>
</head>
<body>

    <!-- Header Navbar Bertema Del Royal Purple (#4C1D95) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-del shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('buku.index') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-white text-del rounded-circle shadow-sm" style="width: 38px; height: 38px; font-size: 1.15rem;">
                    <i class="bi bi-book-half"></i>
                </span>
                <div class="lh-sm">
                    <span class="fs-5 tracking-tight d-block">SIPUS-Del</span>
                    <small class="text-white-50 fs-6 fw-normal" style="font-size: 0.725rem !important;">Perpustakaan Institut Teknologi Del</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded-2 {{ request()->routeIs('buku.*') ? 'active bg-white bg-opacity-10 fw-semibold text-white' : 'text-white-50' }}" href="{{ route('buku.index') }}">
                            <i class="bi bi-collection me-1"></i> Data Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded-2 {{ request()->routeIs('kategori.*') ? 'active bg-white bg-opacity-10 fw-semibold text-white' : 'text-white-50' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags me-1"></i> Kategori Buku
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('buku.create') }}" class="btn btn-sm btn-light text-del fw-bold px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Buku
                    </a>
                    <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-outline-light px-3 py-2 rounded-pill">
                        <i class="bi bi-tag me-1"></i> Tambah Kategori
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Container -->
    <main class="container py-4 flex-grow-1">

        <!-- Flash Messages (Session Status) -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="flex-grow-1">
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="flex-grow-1">
                    <strong>Perhatian!</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
                    <strong>Terjadi Kesalahan Validasi:</strong>
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Slot Content -->
        {{ $slot }}

    </main>

    <!-- Footer -->
    <footer class="footer py-3 text-center text-muted small">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} <strong>SIPUS-Del</strong> — Institut Teknologi Del</span>
            <span>Praktikum Pemrograman & Pengujian Web (12S3101) &bull; Modul Minggu 05</span>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
