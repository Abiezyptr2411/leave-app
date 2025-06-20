<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register | E-Cuti</title>
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

    <div class="login-wrapper" id="registerForm" style="display:none;">
        <div class="text-center mb-4">
            <div class="brand-header">Register</div>
            <small class="text-muted" id="liveClock">
                {{ now()->format('D M Y H:i:s') }}
            </small>
        </div>

        <form method="POST" action="/register" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
                <input type="hidden" name="division" value="Building Management">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="0">Staff</option>
                    <option value="1">Lead</option>
                    <option value="2">Manager</option>
                    <option value="3">Admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Foto Karyawan</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-outline-success">Daftar</button>
            </div>

            <div class="mt-3 text-center">
                <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
            </div>
        </form>
    </div>

    {{-- SweetAlert: Konfirmasi Admin Sebelum Register Ditampilkan --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: 'Konfirmasi Admin',
                input: 'password',
                inputLabel: 'Masukkan Password Admin',
                inputPlaceholder: 'Password rahasia',
                inputAttributes: {
                    maxlength: 20,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: false,
                confirmButtonText: 'Lanjutkan',
                preConfirm: (password) => {
                    if (password !== 'admin123') {
                        Swal.showValidationMessage('Password salah');
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('registerForm').style.display = 'block';
                }
            });
        });
    </script>

    {{-- SweetAlert: Error Message Handler --}}
    @if(session('error'))
    <script>
        let errorMsg = '{{ session("error") }}';

        if (errorMsg === 'AKUN SUDAH TERDAFTAR SEBELUMNYA') {
            Swal.fire({
                icon: 'error',
                title: 'Registrasi Gagal',
                text: 'Akun dengan email tersebut sudah terdaftar sebelumnya.',
                confirmButtonColor: '#dc3545',
                background: '#f8d7da',
                color: '#721c24',
            });
        } else {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: errorMsg,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f8d7da',
                color: '#721c24',
            });
        }
    </script>
    @endif

    {{-- Live Clock --}}
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
            const timePart = now.toLocaleTimeString('en-GB');

            document.getElementById('liveClock').innerText = `${datePart} ${timePart}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>

</body>

</html>