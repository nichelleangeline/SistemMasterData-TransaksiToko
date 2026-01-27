<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mapping Kode Toko</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            text-align: left; /* ⬅️ PAGE TETAP KIRI */
        }

        h3 {
            margin-bottom: 16px;
        }

        table {
            border-collapse: collapse;
            table-layout: auto;   /* ⬅️ IKUT KONTEN */
        }

        th, td {
            border: 1px solid #000; /* SATU GARIS */
            padding: 6px 12px;
            text-align: center;     /* ⬅️ KOLOM CENTER */
            white-space: nowrap;
        }

        th {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h3>Mapping Kode Toko</h3>

<table>
    <thead>
        <tr>
            <th>Kode Toko Baru</th>
            <th>Kode Toko Lama</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->kode_toko_baru }}</td>
            <td>{{ $row->kode_toko_lama ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
