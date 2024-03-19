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
    <table id="export">
        <thead style="color: white;">
            <tr>

                <th data-fill-color="205c74" data-b-a-s="medium" data-a-h="center" data-a-v="middle" class="freeze-col align-middle" rowspan="3" data-fill-color="1c5870">Est.</th>
                <th data-fill-color="205c74" data-b-a-s="medium" data-a-h="center" data-a-v="middle" class="freeze-col align-middle" rowspan="3" data-fill-color="1c5870">Afd.</th>
                <th data-b-a-s="medium" data-a-h="center" data-a-v="middle" class="align-middle" colspan="4" rowspan="2" data-fill-color="588434">DATA BLOK
                    SAMPEL</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="17" data-fill-color="588434">Mutu Ancak (MA)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="8" data-fill-color="379fc9">Mutu Transport (MT)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="23" data-fill-color="ffc404" style="color: #000000;">Mutu Buah (MB)
                <th data-fill-color="205c74" data-b-a-s="medium" data-a-h="center" data-a-v="middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">All
                    Skor</th>
                <th data-fill-color="205c74" data-b-a-s="medium" data-a-h="center" data-a-v="middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">
                    Kategori</th>
                </th>
            </tr>
            <tr>
                {{-- Table Mutu Ancak --}}
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="6" data-fill-color="588434">Brondolan Tinggal
                </th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="7" data-fill-color="588434">Buah Tinggal</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="588434">Pelepah Sengkleh</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="588434">Total Skor</th>

                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="379fc9">TPH Sampel</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="379fc9">Brd Tinggal</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="379fc9">Buah Tinggal</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="379fc9">Total Skor</th>

                {{-- Table Mutu Buah --}}
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="ffc404" style="color: #000000;">TPH Sampel</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="ffc404" style="color: #000000;">Total Janjang
                    Sampel</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Mentah (A)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Matang (N)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Lewat Matang
                    (O)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Janjang Kosong
                    (E)</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Tidak Standar
                    V-Cut</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="2" data-fill-color="ffc404" style="color: #000000;">Abnormal</th>
                <th data-b-a-s="medium" data-a-h="center" class="align-middle" colspan="3" data-fill-color="ffc404" style="color: #000000;">Penggunaan
                    Karung Brondolan</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" rowspan="2" data-fill-color="ffc404" style="color: #000000;">Total Skor</th>
            </tr>
            <tr>
                {{-- Table Mutu Ancak --}}
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Jumlah Pokok Sampel</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Luas Ha Sampel</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Jumlah Jjg Panen</th>
                <th data-a-v="middle" data-a-wrap="true" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">AKP Realisasi</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">P</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">K</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">GL</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Total Brd</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Brd/JJG</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Skor</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">S</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">M1</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">M2</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">M3</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Total JJG</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Skor</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Pokok </th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="588434">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Butir</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Butir/TPH</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Skor</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Jjg/TPH</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="379fc9">Skor</th>
                {{-- table mutu Buah --}}
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Jjg</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>

                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Ya</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">%</th>
                <th data-a-v="middle" data-b-a-s="medium" data-a-h="center" class="align-middle" data-fill-color="ffc404" style="color: #000000;">Skor</th>
            </tr>
        </thead>

        <tbody id="dataInspeksi">
            @foreach ($data as $item)
            @foreach ($item as $items)
            @foreach ($items as $item1)
            @php

            if($item1['afd'] === 'est'){
            $color = '76C5E8';
            }else if ($item1['afd'] === 'wil'){
            $color = 'FF7043';
            }
            else{
            $color = 'EBEBEB';
            };


            $allskor = 0;


            if($item1['check_databh'] === 'ada' || $item1['check_datacak'] === 'ada' || $item1['check_datatrans'] === 'ada'){
            $allskor = $item1['skor_akhircak'] + $item1['totalSkortrans'] + $item1['TOTAL_SKORbh'];

            if ($allskor >= 95) {
            $newktg = "EXCELLENT";
            $color2 = '5074c4';
            } elseif ($allskor >= 85) {
            $newktg = "GOOD";
            $color2 = '08fc2c';
            } elseif ($allskor >= 75) {
            $newktg = "SATISFACTORY";
            $color2 = 'ffdc04';
            } elseif ($allskor >= 65) {
            $newktg = "FAIR";
            $color2 = 'ffa404';
            } else {
            $newktg = "POOR";
            $color2 = 'ff0404';
            }

            }else{
            $allskor = '-';
            $newktg = "-";
            $color2 = 'E2E2E2';
            }


            @endphp
            <tr>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['est']}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['afd']}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['pokok_samplecak'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['ha_samplecak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['jumlah_panencak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['akp_rlcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['pcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['kcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['tglcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_brdcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['brd/jjgcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_brdcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['bhts_scak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['bhtm1cak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['bhtm2cak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['bhtm3cak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_buahcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['buah/jjgcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_bhcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['palepah_pokokcak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['palepah_percak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_pscak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_akhircak']  : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">
                    {{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'?  $item1['tph_sampleNew'] : '-' }}
                </td>

                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_brdtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_brdperTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_brdPertphtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_buahtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_buahPerTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_buahPerTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{ $item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['totalSkortrans'] : '-'}}</td>

                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['tph_baris_bloksbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['sampleJJG_totalbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_mentahbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_perMentahbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_mentahbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_masakbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_perMasakbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_masakbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_overbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_perOverbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_overbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_jjgKosongbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_perKosongjjgbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_jjgKosongbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_vcutbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['perVcutbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_vcutbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['total_abnormalbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['perAbnormalbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['jum_krbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['persen_krbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['skor_krbh'] : '-'}}</td>
                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$item1['check_databh'] === 'ada' ||  $item1['check_datacak'] === 'ada'   ||  $item1['check_datatrans'] === 'ada'? $item1['TOTAL_SKORbh'] : '-'}}</td>

                <td data-fill-color="{{$color}}" data-b-a-s="medium" data-a-h="center">{{$allskor}}</td>
                <td data-fill-color="{{$color2}}" data-b-a-s="medium" data-a-h="center">{{$newktg}}</td>
            </tr>

            @endforeach
            @endforeach
            @endforeach
            <tr>
                @php

                if($datareg['afd'] === 'est'){
                $colorreg = '76C5E8';
                }else if ($datareg['afd'] === 'wil'){
                $colorreg = 'FF7043';
                }
                else{
                $colorreg = 'EBEBEB';
                };


                $allskorreg = 0;


                if($datareg['check_databh'] === 'ada' || $datareg['check_datacak'] === 'ada' || $datareg['check_datatrans'] === 'ada'){
                $allskorreg = $datareg['skor_akhircak'] + $datareg['totalSkortrans'] + $datareg['TOTAL_SKORbh'];

                if ($allskorreg >= 95) {
                $newktgreg = "EXCELLENT";
                $color2reg = '5074c4';
                } elseif ($allskorreg >= 85) {
                $newktgreg = "GOOD";
                $color2reg = '08fc2c';
                } elseif ($allskorreg >= 75) {
                $newktgreg = "SATISFACTORY";
                $color2reg = 'ffdc04';
                } elseif ($allskorreg >= 65) {
                $newktgreg = "FAIR";
                $color2reg = 'ffa404';
                } else {
                $newktgreg = "POOR";
                $color2reg = 'ff0404';
                }

                }else{
                $allskorreg = '-';
                $newktgreg = "-";
                $color2reg = 'E2E2E2';
                }


                @endphp
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['est'] }}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['afd'] }}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['pokok_samplecak'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['ha_samplecak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['jumlah_panencak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['akp_rlcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['pcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['kcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['tglcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_brdcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['brd/jjgcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_brdcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['bhts_scak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['bhtm1cak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['bhtm2cak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['bhtm3cak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_buahcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['buah/jjgcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_bhcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['palepah_pokokcak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['palepah_percak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_pscak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_akhircak']  : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">
                    {{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'?  $datareg['tph_sampleNew'] : '-' }}
                </td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_brdtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_brdperTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_brdPertphtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_buahtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_buahPerTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_buahPerTPHtrans'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{ $datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['totalSkortrans'] : '-'}}</td>

                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['tph_baris_bloksbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['sampleJJG_totalbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_mentahbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_perMentahbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_mentahbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_masakbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_perMasakbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_masakbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_overbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_perOverbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_overbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_jjgKosongbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_perKosongjjgbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_jjgKosongbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_vcutbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['perVcutbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_vcutbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['total_abnormalbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['perAbnormalbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['jum_krbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['persen_krbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['skor_krbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$datareg['check_databh'] === 'ada' ||  $datareg['check_datacak'] === 'ada'   ||  $datareg['check_datatrans'] === 'ada'? $datareg['TOTAL_SKORbh'] : '-'}}</td>
                <td data-fill-color="{{ $colorreg }}" data-b-a-s="medium" data-a-h="center">{{$allskorreg}}</td>
                <td data-fill-color="{{ $color2reg }}" data-b-a-s="medium" data-a-h="center">{{$newktgreg}}</td>
            </tr>

        </tbody>
    </table>

    <button onclick=" exportToExcel()">Export to Excel</button>

    <script>
        function exportToExcel() {
            var table = document.getElementById("export");

            TableToExcel.convert(document.getElementById("export"), {
                name: "Rekap Data QC Inspeksi Reg {{$reg}} / {{$bulan}} .xlsx",
                sheet: {
                    name: "Rekap Data QC inspeksi Reg {{$reg}} / {{$bulan}}"
                }
            });

            // Close the window after exporting
            setTimeout(function() {
                window.close();
            }, 100);
        }

        // Call exportToExcel() when the page loads with a delay
        setTimeout(function() {
            exportToExcel();
        }, 200);
    </script>
</body>

</html>