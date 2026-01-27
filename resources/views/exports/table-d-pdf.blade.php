<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Table D - Data Sales</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h3 {
            text-align: center;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td {
            vertical-align: top;
        }
    </style>
</head>
<body>

    <h3>Data Sales (Table D)</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 30%">Kode Sales</th>
                <th style="width: 70%">Nama Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td>{{ $row->kode_sales }}</td>
                    <td>{{ $row->nama_sales }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align:center;">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
