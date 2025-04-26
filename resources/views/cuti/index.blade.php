<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Leave | Histories</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f8;
      margin: 0;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      background: linear-gradient(to bottom, #003c8f, #1976d2);
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px 0;
    }
    .sidebar .nav-link {
      color: #fff;
      padding: 10px 25px;
      display: flex;
      align-items: center;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background-color: rgba(255, 255, 255, 0.1);
    }
    .sidebar .nav-link i {
      margin-right: 10px;
    }
    .main-content {
      margin-left: 250px;
      padding: 20px;
    }
    .topbar {
      background-color: #fff;
      padding: 15px 25px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .table-card {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .badge-status {
      padding: 5px 12px;
      font-size: 0.85rem;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
    }
    .badge-status .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }
    .badge-success {
      background-color: #e6f4ea;
      color: #2e7d32;
    }
    .badge-success .dot { background-color: #2e7d32; }
    .badge-warning {
      background-color: #fff8e1;
      color: #f9a825;
    }
    .badge-warning .dot { background-color: #f9a825; }
    .badge-danger {
      background-color: #fdecea;
      color: #d32f2f;
    }
    .badge-danger .dot { background-color: #d32f2f; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h5 class="text-center mb-4">E-Leave | System App</h5>
  <a href="/dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="/cuti" class="nav-link"><i class="bi bi-receipt"></i> Leave Histories</a>
  <hr class="border-light mx-3">
  <a href="/logout" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <!-- Top Bar -->
  <div class="topbar">
    <div><strong>Selamat datang, {{ session('user_name') }}</strong></div>
    <div class="text-muted">cuti@sistem.com</div>
  </div>

  <!-- Flash Message -->
  @if(session('success'))
  <script>
      Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: '{{ session("success") }}',
          showConfirmButton: false,
          timer: 3000,
          background: '#d4edda',
          color: '#155724',
      });
  </script>
  @endif

  <!-- Filter -->
  <form method="GET" action="/cuti" class="mt-4 d-flex flex-wrap gap-3 align-items-center">
    <div class="input-group" style="max-width: 300px;">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari alasan cuti...">
    </div>
    <select name="status" class="form-select" style="max-width: 150px;">
      <option value="">Semua Status</option>
      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
      <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
      <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
    </select>
    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" style="max-width: 180px;">
    <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" style="max-width: 180px;">
    <button class="btn" style="background-color: #ff6f00; color: white; border-color: #ff6f00;">
        <i class="bi bi-funnel"></i> Pencarian
    </button>
    
    <div class="d-flex justify-content-end">
      <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-file-earmark-spreadsheet"></i> Spreadsheet
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#importModal">
              <i class="bi bi-upload me-2"></i> Import
            </a>
          </li>
          <li>
            <a id="exportExcelBtn" class="dropdown-item">
              <i class="bi bi-file-earmark-excel me-2"></i> Cetak Excel
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ url('/cuti/template') }}">
              <i class="bi bi-file-earmark-arrow-down me-2"></i> Download Template
            </a>
          </li>
        </ul>
      </div>
    </div>

    <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#cutiModal">
      <i class="bi bi-plus-circle me-1"></i> Ajukan Cuti
    </button>
  </form>

  <!-- Tabel Cuti -->
  <div class="table-card mt-4">
    <h6 class="mb-3">Daftar Cuti ({{ count($cutis) }})</h6>
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Alasan</th>
          <th>Tgl Mulai</th>
          <th>Tgl Selesai</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($cutis as $c)
        <tr>
          <td>{{ $c->alasan }}</td>
          <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') }}</td>
          <td>
            @php
              $status = strtolower($c->status);
              $badge = match ($status) {
                  'disetujui' => 'badge-success',
                  'ditolak' => 'badge-danger',
                  default => 'badge-warning',
              };
              $dot = "<span class='dot'></span>";
            @endphp
            <span class="badge-status {{ $badge }}">{!! $dot !!} {{ ucfirst($status) }}</span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center text-muted">Belum ada data cuti.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Ajukan Cuti -->
<div class="modal fade" id="cutiModal" tabindex="-1" aria-labelledby="cutiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/cuti" class="modal-content">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="cutiModalLabel"><i class="bi bi-calendar-plus me-2"></i>Ajukan Cuti</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label for="alasan" class="form-label">Alasan Cuti</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
            <input type="text" name="alasan" id="alasan" class="form-control" placeholder="Contoh: Urusan keluarga, sakit, dll" required>
          </div>
        </div>
        <div class="mb-3">
          <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
          </div>
        </div>
        <div class="mb-3">
          <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
          </div>
        </div>
        <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          Mohon pastikan tanggal dan alasan cuti sudah benar sebelum dikirim.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Kirim</button>
      </div>
    </form>
  </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="importForm" method="POST" action="/cuti/import" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="importModalLabel">
          <i class="bi bi-upload me-2"></i>Import Data Cuti
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label for="file" class="form-label">Pilih File Excel</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-file-earmark-excel"></i></span>
            <input type="file" name="file" id="file" class="form-control" accept=".xls, .xlsx" required>
          </div>
        </div>

        <div id="loadingSpinner" class="d-none text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p>Processing your import...</p>
        </div>

        <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          Pastikan file Anda sesuai format Template Import.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-upload me-1"></i> Upload
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('importForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const formData = new FormData(form);

  Swal.fire({
    toast: true,
    position: 'top-end',
    title: 'Sedang memproses...',
    text: 'Mohon tunggu beberapa saat.',
    background: '#cce5ff', // biru soft untuk loading
    color: '#004085', // biru tua untuk teks loading
    showConfirmButton: false,
    allowOutsideClick: false,
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  setTimeout(() => {
    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
      },
      body: formData,
    })
    .then(response => response.json())
    .then(data => {
      Swal.close(); // Tutup loading

      if (data.success) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: data.message,
          background: '#d4edda', // hijau soft untuk success
          color: '#155724', // hijau tua untuk teks
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: data.message,
          background: '#f8d7da', 
          color: '#721c24', 
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      }
    })
    .catch(error => {
      Swal.close();
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Terjadi Kesalahan!',
        text: 'Silakan coba lagi nanti.',
        background: '#f8d7da',
        color: '#721c24',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });
    });
  }, 1500);
});
</script>

<script>
  document.getElementById('exportExcelBtn').addEventListener('click', function() {
    const search = document.querySelector('input[name="search"]').value || '';
    const status = document.querySelector('select[name="status"]').value || '';
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]').value || '';
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]').value || '';

    // buat URL export dengan query string
    const url = `/cuti/export?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&tanggal_mulai=${encodeURIComponent(tanggalMulai)}&tanggal_selesai=${encodeURIComponent(tanggalSelesai)}`;

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('Gagal export file');
        }
        return response.blob();
      })
      .then(blob => {
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = "cuti-export.xlsx"; 
        link.click();
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Gagal export data!');
      });
  });
</script>

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
</body>
</html>
