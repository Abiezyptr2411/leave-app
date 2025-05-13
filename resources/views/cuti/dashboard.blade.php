<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Leave | Dashboard</title>
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
    .summary-card {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .summary-value {
      font-size: 24px;
      font-weight: bold;
    }
    .summary-label {
      font-size: 14px;
      color: gray;
    }
    .alert-orange {
      background-color: #ff6f00;
      color: white;
      padding: 10px 20px;
      border-radius: 4px;
      margin-top: 20px;
    }
    .alert-blue {
      background-color: #e3f2fd;
      padding: 10px 20px;
      border-radius: 4px;
      margin-top: 10px;
    }
    .statistics-card {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }
    .bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.3); /* Soft yellow */
    color: #ff5722; /* Soft orange */
    }

    .bg-success-soft {
        background-color: rgba(76, 175, 80, 0.3); /* Soft green */
        color: #388e3c; /* Soft green */
    }

    .bg-danger-soft {
        background-color: rgba(244, 67, 54, 0.3); /* Soft red */
        color: #d32f2f; /* Soft red */
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h5 class="text-center mb-4">E-Leave | System App</h5>
  <a href="/dashboard" class="nav-link active"><i class="bi bi-bank2"></i> Dashboard</a>
  <a href="/cuti" class="nav-link"><i class="bi bi-calendar2-check"></i> Leave Histories</a>

  <!-- @if (session('role') == 3)
    <a href="/document-upload" class="nav-link"><i class="bi bi-upload"></i> Document Upload</a>
  @endif -->

  <a href="/document-upload" class="nav-link"><i class="bi bi-upload"></i> Document Upload</a>

  <hr class="border-light mx-3">
  <a href="/logout" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="topbar">
    <div><strong>Dashboard</strong></div>
    <div class="text-muted">Version 1.0.0</div>
  </div>

  <div class="alert-orange mt-3">
    Halo, {{ session('user_name') }} Selamat datang di aplikasi pengajuan cuti divisi Building Management</a>
  </div>
  <div class="alert-blue mt-2">
    <i class="bi bi-info-circle"></i> <b>Catatan:</b> Pengajuan cuti dapat dilakukan maksimal 14 hari sebelum tanggal pengajuan cuti kamu.
  </div>

  <!-- Statistics Section -->
  <div class="row mt-4 g-3">
    <div class="col-md-3">
      <div class="statistics-card">
        <div class="summary-value">{{ $totalCuti }}</div>
        <div class="summary-label">Total Cuti</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="statistics-card">
        <div class="summary-value">{{ $cutiPending }}</div>
        <div class="summary-label">Cuti Menunggu</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="statistics-card">
        <div class="summary-value">{{ $cutiDisetujui }}</div>
        <div class="summary-label">Cuti Disetujui</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="statistics-card">
        <div class="summary-value">{{ $cutiDitolak }}</div>
        <div class="summary-label">Cuti Ditolak</div>
      </div>
    </div>
  </div>

  <!-- Detail Karyawan -->
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
         Detail Informasi Staff
        </div>
        <div class="card-body d-flex align-items-center">
        <img src="{{ session('photo') ? asset('storage/' . session('photo')) : asset('img/default.png') }}"
            alt="Foto Profil" class="rounded-circle me-4" width="100" height="100" style="object-fit: cover;">
          <div>
          <p class="mb-1 text-muted">Staff ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ session('nik') }}</strong></p>
            <p class="mb-1 text-muted">Staff Name&nbsp;: <strong>{{ session('user_name') }}</strong></p>
            <p class="mb-1 text-muted">Departure&nbsp;&nbsp;&nbsp;: <strong>{{ session('division') }}</strong></p>
            <p class="mb-1 text-muted">Position&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
                <strong>
                    {{ session('role') == 1 ? 'Team Leader' : (session('role') == 2 ? 'Building Manager' : 'Staff Building') }}
                </strong>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- approval list -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Riwayat Permohonan Cuti
            </div>
            <div class="card-body p-3" style="max-height: 500px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #1565c0 #e3f2fd;">
                @forelse($cutiPendingList as $cuti)
                    <div class="mb-4 position-relative ps-4 border-start border-3" style="border-color: #e3f2fd;">
                        <div class="position-absolute top-0 start-0 translate-middle" style="background-color: #e3f2fd; width: 16px; height: 16px; border-radius: 50%;"></div>
                        <div class="fw-bold">{{ $cuti->user->name }}</div>
                        <div class="small text-muted">
                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}
                        </div>
                        <div class="mb-2">{{ $cuti->alasan }}</div>
                        @if($role != 0)
                            <form action="{{ route('cuti.approve', $cuti->id) }}" method="POST" class="d-inline approve-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-approve" style="background-color: #e3f2fd; color: #1565c0;">
                                    <i class="bi bi-check-circle"></i> Setujui
                                </button>
                            </form>

                            <form action="{{ route('cuti.reject', $cuti->id) }}" method="POST" class="d-inline reject-form">
                                @csrf
                                <button type="button" class="btn btn-sm btn-reject" style="background-color: #ff6f00; color: white;">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                            </form>

                        @else
                        <span class="badge {{ $cuti->status_badge }}">
                            {{ $cuti->status_label }}
                        </span>

                        @endif
                    </div>
                @empty
                    <div class="text-muted text-center">Tidak ada cuti pending.</div>
                @endforelse
            </div>
        </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div id="transactionChart" style="height: 400px;"></div>
          <div id="chartData"
              data-labels='@json($chartData->pluck("status_label"))'
              data-values='@json($chartData->pluck("total"))'>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
  $(document).ready(function () {
    const labels = $('#chartData').data('labels');
    const values = $('#chartData').data('values');
    const numericValues = values.map(value => parseFloat(value));
    const pieData = labels.map((label, index) => {
        return { name: label, y: numericValues[index] };
    });

    Highcharts.chart('transactionChart', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Statistik Pengajuan Cuti (Berdasarkan Status)'
        },
        tooltip: {
            pointFormat: '<b>{point.name}</b>: {point.y} cuti ({point.percentage:.1f}%)'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y}'
                }
            }
        },
        series: [{
            name: 'Jumlah Cuti',
            colorByPoint: true,
            data: pieData
        }],
        credits: {
            enabled: false
        }
    });
  });
</script>

<script>
    // Approve
    document.querySelectorAll('.btn-approve').forEach(button => {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Setujui cuti ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            })
        });
    });

    // Reject
    document.querySelectorAll('.btn-reject').forEach(button => {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Tolak cuti ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            })
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session("success") }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#d4edda',
        color: '#155724',
    });
</script>
@endif

</body>
</html>
