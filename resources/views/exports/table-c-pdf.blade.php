<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; }
    </style>
</head>
<body>

<h3>Table C</h3>

<table>
    <thead>
        <tr>
            <th>Kode Toko</th>
            <th>Area Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->kode_toko }}</td>
            <td>{{ $row->area_sales }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
