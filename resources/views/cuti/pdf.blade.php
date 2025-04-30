<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengajuan Cuti</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            margin: 40px;
            font-size: 14px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            font-size: 14px;
            margin: 0;
        }

        .policy-header {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .policy-content {
            text-align: justify;
            line-height: 1.6;
        }

        .policy-content ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .policy-content li {
            margin-bottom: 10px;
        }

        hr {
            border: 1px solid #000;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 6px 10px;
        }

        .cuti-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .cuti-table th, .cuti-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }

        .cuti-table th {
            background-color: #f0f0f0;
        }

        .signature {
            margin-top: 60px;
            width: 100%;
        }

        .signature div {
            width: 30%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }

        .signature-name {
            margin-top: 70px;
            text-align: center;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
        }

        .note {
            font-size: 12px;
            color: #666;
            margin-top: 40px;
        }

        /* Add page break before the content starts */
        .page-break {
            page-break-before: always;
        }

        /* Section Title Styling */
        .section-title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
        }

        /* Legal disclaimer */
        .legal-disclaimer {
            font-size: 12px;
            color: #555;
            margin-top: 40px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- First Page: Legal and Policy Information -->
    <div class="header">
        <img src="{{ public_path('image.png') }}" alt="Logo" style="width: 180px; height: auto;">
        <h1>PT. Contoh Perusahaan</h1>
        <p>Jl. Contoh Alamat No. 123, Jakarta</p>
    </div>

    <div class="policy-header">
        <h2>Kebijakan Pengajuan Cuti</h2>
    </div>

    <div class="policy-content">
        <p>Berikut adalah kebijakan terkait pengajuan cuti yang berlaku di PT. Contoh Perusahaan. Harap dipahami dan dipatuhi oleh seluruh karyawan yang akan mengajukan cuti.</p>
        
        <div class="section-title">Prosedur Pengajuan Cuti</div>
        <ul>
            <li><strong>Pengajuan:</strong> Karyawan harus mengajukan cuti paling lambat 7 hari sebelum tanggal mulai cuti.</li>
            <li><strong>Persetujuan:</strong> Semua pengajuan cuti harus disetujui oleh atasan langsung.</li>
            <li><strong>Pengajuan Khusus:</strong> Untuk cuti mendesak, pengajuan dapat dilakukan dengan pemberitahuan kurang dari 7 hari, tetapi tetap memerlukan persetujuan HRD.</li>
        </ul>

        <div class="section-title">Ketentuan Terkait Cuti</div>
        <ul>
            <li><strong>Durasi Cuti:</strong> Cuti tahunan yang tidak digunakan dalam satu tahun tidak akan terakumulasi untuk tahun berikutnya.</li>
            <li><strong>Gaji:</strong> Cuti tidak akan mempengaruhi gaji bulanan, kecuali untuk cuti yang melebihi jumlah yang diizinkan berdasarkan kebijakan perusahaan.</li>
        </ul>

        <p>Dengan mengajukan cuti, karyawan menyetujui dan menerima ketentuan serta prosedur yang berlaku.</p>
    </div>

    <div class="legal-disclaimer">
        <p>* Dokumen ini berlaku sesuai dengan ketentuan yang berlaku di PT. Contoh Perusahaan dan harus diikuti oleh seluruh karyawan yang mengajukan cuti.</p>
    </div>

    <div class="page-break"></div>

    <!-- Second Page: The leave application form -->
    <h3 style="text-align:center;">BUKTI PENGAJUAN CUTI KARYAWAN</h3>

    <table class="info-table">
        <tr>
            <td><strong>Nama</strong></td>
            <td>: {{ $cuti->user->name }}</td>
        </tr>
        <tr>
            <td><strong>Nomor Pengajuan</strong></td>
            <td>: LV{{ str_pad($cuti->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pengajuan</strong></td>
            <td>: {{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y H:i') }}</td>
        </tr>
    </table>

    <table class="cuti-table">
        <thead>
            <tr>
                <th>Alasan Cuti</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $cuti->alasan }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                <td>{{ ucfirst($cuti->status) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div>
            <p><b>Pemohon</b></p>
            <div class="signature-name">{{ $cuti->user->name }}</div>
        </div>
        <div>
            <p><b>Atasan</b></p>
            <div class="signature-name">Irfan Maulana</div>
        </div>
        <div>
            <p><b>HRD</b></p>
            <div class="signature-name">Annisa Sariwati</div>
        </div>
    </div>

    <p class="note">
        * Dokumen ini dicetak secara otomatis melalui sistem dan berlaku sebagai bukti sah pengajuan cuti karyawan.
    </p>

</body>
</html>
