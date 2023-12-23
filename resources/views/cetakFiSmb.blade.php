<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Document</title>

</head>
<style>
    table.table-bordered>thead>tr>th {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-active>thead>tr>th {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-active>tbody>tr>td {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-primary>thead>tr>th {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-primary>tbody>tr>td {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-bordered>tbody>tr>th {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-warning>thead>tr>th {
        border: 1px solid rgb(0, 0, 0);
    }

    table.table-warning>tbody>tr>td {
        border: 1px solid rgb(0, 0, 0);
    }

    body {
        font-size: 15px;
    }
</style>

<body>
    <table class="table table-bordered text-center">
        <thead>
            <tr bgcolor="#e0ecf4">
                <th style="font-size: 20px; font-weight: bold;">PEMERIKSAAN KUALITAS PANEN</th>
            </tr>
        </thead>
    </table>
    <table class="table table-bordered text-center" style="width: 25%; float:right;">
        <thead>
            <tr>
                <th>TANGGAL PEMERIKSAAN : {{ $tgl }}</th>
            </tr>
        </thead>
    </table><br><br><br>

    <table class="table table-bordered text-center">
        <thead>
            <tr bgcolor="#e8ecdc">
                <th class="align-middle" style="width: 10%;">EST</th>
                <th class="align-middle" style="width: 10%;">AFD</th>
                <th class="align-middle" style="width: 10%;">BLOK</th>
                <th class="align-middle">ISSUE</th>
                <th class="align-middle" style="width: 40%;">FOTO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataResult as $key => $item)
            <tr>
                <td class="align-middle">{{ $item['estate'] }}</td>
                <td class="align-middle">{{ $item['afdeling'] }}</td>
                <td class="align-middle">{{ $item['blok'] }}</td>
                <td class="align-middle">{{ $item['komen'] }}</td>
                <td class="align-middle">{{-- {{ $item['fotoTemuan'] }} --}}<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/{{$item['fotoTemuan']}}" style="weight:150pt;height:150pt"></td>
            </tr>
            @endforeach

            <tr bgcolor="#e8ecdc">
                <td colspan="4" class="align-middle" style="font-weight: bold">TOTAL TEMUAN</td>
                <td class="align-middle">{{ $totalTemuan }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>