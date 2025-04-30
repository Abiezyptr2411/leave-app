<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengajuan Cuti</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Bukti Pengajuan Cuti</h3>
    <hr>

    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <td>{{ $cuti->user->name }}</td>
        </tr>
        <tr>
            <th>Alasan Cuti</th>
            <td>{{ $cuti->alasan }}</td>
        </tr>
        <tr>
            <th>Tanggal Mulai</th>
            <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Selesai</th>
            <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($cuti->status) }}</td>
        </tr>
        <tr>
            <th>Tanggal Pengajuan</th>
            <td>{{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y H:i') }}</td>
        </tr>
    </table>

    <a href="{{ url('/cuti') }}" class="btn btn-secondary">Kembali</a>
</div>
</body>
</html>
