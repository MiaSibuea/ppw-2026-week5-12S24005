
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login SIPUS-Del</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f3f0ff, #e9edff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border: none;
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 12px 35px rgba(60, 30, 120, 0.12);
        }

        .logo {
            width: 65px;
            height: 65px;
            border-radius: 18px;
            background: #4c1d95;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 16px;
        }

        .btn-login {
            background: #4c1d95;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
        }

        .btn-login:hover {
            background: #37136f;
            color: white;
        }

        .form-control {
            padding: 11px;
            border-radius: 9px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo">📚</div>

        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1">SIPUS-Del</h3>
            <p class="text-muted small mb-0">
                Sistem Informasi Perpustakaan
            </p>
            <p class="text-muted small">
                Silakan login untuk melanjutkan.
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    Email
                </label>

                <input type="email"
                       id="email"
                       name="email"
                       class="form-control"
                       placeholder="Masukkan email"
                       value="{{ old('email') }}"
                       autocomplete="username"
                       required
                       autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">
                    Password
                </label>

                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       autocomplete="current-password"
                       required>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Login
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                SIPUS-Del © 2026
            </small>
        </div>
    </div>

</body>
</html>