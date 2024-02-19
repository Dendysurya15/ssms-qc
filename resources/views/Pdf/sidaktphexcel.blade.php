<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script> -->

    <!-- <script src="{{asset('sheetjs/dist/xlsx.bundle.js')}}"></script> -->

    <script type="text/javascript" src="{{ asset('table-to-excel-master/dist/tableToExcel.js') }}"></script>
</head>


<body>


    <div class="row text-center tbl-fixed">
        <table class="table table-info table-striped" id="newweek1">
            <thead>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" rowspan="3">EST</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3">AFD</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+1</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+2</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+3</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+4</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+5</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+6</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+7</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> >H+7 </th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> All Skor</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> Kategori</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>

                </tr>
            </thead>

            <tbody>
                @foreach ($data['week1'] as $items)

                @php

                if($items['afd'] === 'EST'){
                $color = '76C5E8';
                }else if ($items['afd'] === 'Reg'){
                $color = 'FF7043';
                }else if ($items['afd'] === 'WIL'){
                $color = 'B8AE5B';
                }
                else{
                $color = 'EBEBEB';
                };

                if ($items['total_score'] >= 95) {
                $newktg = "EXCELLENT";
                $color2 = '5074c4';
                } elseif ($items['total_score'] >= 85) {
                $newktg = "GOOD";
                $color2 = '08fc2c';
                } elseif ($items['total_score'] >= 75) {
                $newktg = "SATISFACTORY";
                $color2 = 'ffdc04';
                } elseif ($items['total_score'] >= 65) {
                $newktg = "FAIR";
                $color2 = 'ffa404';
                } else {
                $newktg = "POOR";
                $color2 = 'ff0404';
                }

                @endphp
                <tr>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['est']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['afd']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang1']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang2']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang3']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang4']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang5']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang6']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang7']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['total_score']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color2}}">{{$newktg}}</td>

                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="row text-center tbl-fixed">
        <table class="table table-info table-striped" id="newweek2">
            <thead>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" rowspan="3">EST</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3">AFD</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+1</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+2</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+3</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+4</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+5</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+6</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+7</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> >H+7 </th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> All Skor</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> Kategori</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>

                </tr>
            </thead>

            <tbody>
                @foreach ($data['week2'] as $items)

                @php

                if($items['afd'] === 'EST'){
                $color = '76C5E8';
                }else if ($items['afd'] === 'Reg'){
                $color = 'FF7043';
                }else if ($items['afd'] === 'WIL'){
                $color = 'B8AE5B';
                }
                else{
                $color = 'EBEBEB';
                };

                if ($items['total_score'] >= 95) {
                $newktg = "EXCELLENT";
                $color2 = '5074c4';
                } elseif ($items['total_score'] >= 85) {
                $newktg = "GOOD";
                $color2 = '08fc2c';
                } elseif ($items['total_score'] >= 75) {
                $newktg = "SATISFACTORY";
                $color2 = 'ffdc04';
                } elseif ($items['total_score'] >= 65) {
                $newktg = "FAIR";
                $color2 = 'ffa404';
                } else {
                $newktg = "POOR";
                $color2 = 'ff0404';
                }

                @endphp
                <tr>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['est']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['afd']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang1']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang2']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang3']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang4']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang5']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang6']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang7']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['total_score']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color2}}">{{$newktg}}</td>

                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="row text-center tbl-fixed">
        <table class="table table-info table-striped" id="newweek3">
            <thead>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" rowspan="3">EST</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3">AFD</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+1</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+2</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+3</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+4</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+5</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+6</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+7</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> >H+7 </th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> All Skor</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> Kategori</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>

                </tr>
            </thead>

            <tbody>
                @foreach ($data['week3'] as $items)

                @php

                if($items['afd'] === 'EST'){
                $color = '76C5E8';
                }else if ($items['afd'] === 'Reg'){
                $color = 'FF7043';
                }else if ($items['afd'] === 'WIL'){
                $color = 'B8AE5B';
                }
                else{
                $color = 'EBEBEB';
                };

                if ($items['total_score'] >= 95) {
                $newktg = "EXCELLENT";
                $color2 = '5074c4';
                } elseif ($items['total_score'] >= 85) {
                $newktg = "GOOD";
                $color2 = '08fc2c';
                } elseif ($items['total_score'] >= 75) {
                $newktg = "SATISFACTORY";
                $color2 = 'ffdc04';
                } elseif ($items['total_score'] >= 65) {
                $newktg = "FAIR";
                $color2 = 'ffa404';
                } else {
                $newktg = "POOR";
                $color2 = 'ff0404';
                }

                @endphp
                <tr>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['est']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['afd']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang1']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang2']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang3']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang4']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang5']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang6']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang7']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['total_score']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color2}}">{{$newktg}}</td>

                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="row text-center tbl-fixed">
        <table class="table table-info table-striped" id="newweek4">
            <thead>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" rowspan="3">EST</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3">AFD</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+1</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+2</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+3</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+4</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+5</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+6</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+7</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> >H+7 </th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> All Skor</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> Kategori</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>

                </tr>
            </thead>

            <tbody>
                @foreach ($data['week4'] as $items)

                @php

                if($items['afd'] === 'EST'){
                $color = '76C5E8';
                }else if ($items['afd'] === 'Reg'){
                $color = 'FF7043';
                }else if ($items['afd'] === 'WIL'){
                $color = 'B8AE5B';
                }
                else{
                $color = 'EBEBEB';
                };

                if ($items['total_score'] >= 95) {
                $newktg = "EXCELLENT";
                $color2 = '5074c4';
                } elseif ($items['total_score'] >= 85) {
                $newktg = "GOOD";
                $color2 = '08fc2c';
                } elseif ($items['total_score'] >= 75) {
                $newktg = "SATISFACTORY";
                $color2 = 'ffdc04';
                } elseif ($items['total_score'] >= 65) {
                $newktg = "FAIR";
                $color2 = 'ffa404';
                } else {
                $newktg = "POOR";
                $color2 = 'ff0404';
                }

                @endphp
                <tr>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['est']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['afd']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang1']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang2']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang3']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang4']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang5']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang6']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang7']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['total_score']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color2}}">{{$newktg}}</td>

                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <div class="row text-center tbl-fixed">
        <table class="table table-info table-striped" id="newweek5">
            <thead>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" rowspan="3">EST</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3">AFD</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+1</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+2</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+3</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+4</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+5</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+6</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> H+7</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="10"> >H+7 </th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> All Skor</th>
                    <th data-b-a-s="medium" data-a-v="middle" data-a-h="center" rowspan="3"> Kategori</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="6">Brondolan Tinggal</th>
                    <th data-b-a-s="medium" data-a-h="center" colspan="4">Buah Tinggal</th>
                </tr>
                <tr>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di TPH</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Jalan</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Bin</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Di Karung</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Total Brd</th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle"> Skor</th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle"> Buah Sortiran / Buah Jatuh </th>
                    <th data-b-a-s="medium" data-a-wrap="true" data-a-h="center" data-a-v="middle">Restan Tidak Dilaporkan </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Total Jjg </th>
                    <th data-b-a-s="medium" data-a-h="center" data-a-v="middle">Skor</th>

                </tr>
            </thead>

            <tbody>
                @foreach ($data['week5'] as $items)

                @php

                if($items['afd'] === 'EST'){
                $color = '76C5E8';
                }else if ($items['afd'] === 'Reg'){
                $color = 'FF7043';
                }else if ($items['afd'] === 'WIL'){
                $color = 'B8AE5B';
                }
                else{
                $color = 'EBEBEB';
                };

                if ($items['total_score'] >= 95) {
                $newktg = "EXCELLENT";
                $color2 = '5074c4';
                } elseif ($items['total_score'] >= 85) {
                $newktg = "GOOD";
                $color2 = '08fc2c';
                } elseif ($items['total_score'] >= 75) {
                $newktg = "SATISFACTORY";
                $color2 = 'ffdc04';
                } elseif ($items['total_score'] >= 65) {
                $newktg = "FAIR";
                $color2 = 'ffa404';
                } else {
                $newktg = "POOR";
                $color2 = 'ff0404';
                }

                @endphp
                <tr>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['est']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['afd']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg1']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang1']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg2']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang2']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg3']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang3']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg4']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang4']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg5']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang5']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg6']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang6']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg7']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang7']}}</td>

                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tph8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['jalan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['bin8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['karung8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tot_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_brd8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['buah8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['restan8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['tod_jjg8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['skor_janjang8']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}">{{$items['total_score']}}</td>
                    <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color2}}">{{$newktg}}</td>

                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
    <button onclick="exportToExcel()">Export to Excel</button>

    <script>
        let reg = @json($data['reg']);
        let tanggal = @json($data['tanggal']);

        // console.log(reg);

        function exportToExcel() {
            // Get table elements for each week
            var table1 = document.getElementById("newweek1");
            var table2 = document.getElementById("newweek2");
            var table3 = document.getElementById("newweek3");
            var table4 = document.getElementById("newweek4");
            var table5 = document.getElementById("newweek5");

            // Convert each table to a sheet in the workbook
            var book = TableToExcel.tableToBook(table1, {
                sheet: {
                    name: "Rekap Data M1"
                }
            });
            TableToExcel.tableToSheet(book, table2, {
                sheet: {
                    name: "Rekap Data M2"
                }
            });
            TableToExcel.tableToSheet(book, table3, {
                sheet: {
                    name: "Rekap Data M3"
                }
            });
            TableToExcel.tableToSheet(book, table4, {
                sheet: {
                    name: "Rekap Data M4"
                }
            });
            TableToExcel.tableToSheet(book, table5, {
                sheet: {
                    name: "Rekap Data M5"
                }
            });

            // Save the workbook as an Excel file
            TableToExcel.save(book, `Rekap Sidak TPH REG ${reg} Bulan : ${tanggal}-.xlsx`);

            setTimeout(function() {
                window.close();
            }, 100);
        }
        setTimeout(function() {
            exportToExcel();
        }, 200);
    </script>

</body>

</html>