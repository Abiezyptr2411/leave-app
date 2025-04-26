<!DOCTYPE html>
<html>
<head><title>Ajukan Cuti</title></head>
<body>
    <h2>Ajukan Cuti</h2>
    <form method="POST" action="/cuti">
        @csrf
        Alasan: <input type="text" name="alasan"><br>
        Tanggal Mulai: <input type="date" name="tanggal_mulai"><br>
        Tanggal Selesai: <input type="date" name="tanggal_selesai"><br>
        <button type="submit">Kirim</button>
    </form>
    <a href="/cuti">Kembali</a>
</body>
</html>
