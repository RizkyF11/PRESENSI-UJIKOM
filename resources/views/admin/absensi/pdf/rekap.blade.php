<!DOCTYPE html>
<html>
<head>
    <style>
        body{
            font-family: sans-serif;
            font-size:12px;
        }

        h2{
            text-align:center;
            margin-bottom:5px;
        }

        .periode{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#0d6efd;
            color:white;
            padding:10px;
            border:1px solid black;
        }

        td{
            padding:8px;
            border:1px solid black;
            text-align:center;
        }

        tr:nth-child(even){
            background:#f2f2f2;
        }
    </style>
</head>
<body>

    <h2>LAPORAN REKAP ABSENSI KARYAWAN</h2>

    <div class="periode">
        Periode:
        {{ $tanggalMulai }}
        s/d
        {{ $tanggalSelesai }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Hari Kerja</th>
                <th>Hadir</th>
                <th>Terlambat (dari hadir)</th>
                <th>Izin</th>
                <th>Cuti</th>
                <th>Alpha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td>{{ $item['hari_kerja'] }}</td>
                <td>{{ $item['hadir'] }}</td>
                <td>{{ $item['terlambat'] }}</td>
                <td>{{ $item['izin'] }}</td>
                <td>{{ $item['cuti'] }}</td>
                <td>{{ $item['alpha'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>