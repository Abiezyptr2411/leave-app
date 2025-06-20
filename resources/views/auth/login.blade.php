<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Authentikasi | E-Cuti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 520px;
            padding: 50px 40px;
            background: #ffffff;
            color: #2c3e50;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            margin: 20px;
            /* Tambahan agar tidak nempel di sisi saat HP */
        }

        .brand-header {
            font-weight: 700;
            font-size: 32px;
            color: #2c3e50;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
        }

        .login-btn {
            background: linear-gradient(to right, #0d6efd, #0b5ed7);
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
        }

        .login-btn:hover {
            background: #0a53be;
        }

        .form-label {
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .login-wrapper {
                padding: 30px 20px;
            }

            .brand-header {
                font-size: 24px;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="login-wrapper">
        <div class="text-center mb-4">
            <div class="brand-header">PENGAJUAN CUTI</div>
            <small class="text-muted" id="liveClock">
                <?php echo date('D M Y H:i:s'); ?>
            </small>
        </div>

        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email atau Nama</label>
                <input type="text" name="email" class="form-control" placeholder="Masukkan email atau nama" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-success">Masuk</button>
            </div>

        </form>

        <!-- <div class="text-center mt-4">
            <span class="text-muted">Belum punya akun?</span>
            <a href="/register" class="text-decoration-none fw-bold text-success ms-1">
                Daftar Sekarang
            </a>
        </div> -->
    </div>

</body>

</html>

@if(session('error'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: '{{ session("error") }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#f8d7da',
        color: '#721c24',
    });
</script>
@endif

<script>
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'short',
            month: 'short',
            day: '2-digit',
            year: 'numeric'
        };
        const datePart = now.toLocaleDateString('en-US', options);
        const timePart = now.toLocaleTimeString('en-GB'); // 24-hour format with seconds

        document.getElementById('liveClock').innerText = `${datePart} ${timePart}`;
    }

    setInterval(updateClock, 1000); // Update every 1 second
    updateClock(); // Run immediately once
</script>