<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pemeriksaan TPH & BIN Reg-I Periode {{$start}} - {{$last}} .pdf</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        @page {
            size: A3;
            margin: 0;
        }

        .page-break:before {
            container: "";
            page-break-before: always;
        }

        .responsive {
            width: 300px;
            max-width: 350px;
            height: 300px;
            display: block;
        }

        /* .responsive {
            width: 100px;
            max-width: 100px;
            height: 100px;
            display: block;
        } */
    </style>
</head>

<body>
    <div class="container ">
        <div class="pb-12 mt-4">
            <h4 class="text-center border border-secondary border-2"> REKAPITULASI PEMERIKSAAN BRONDOLAN TINGGAL DI
                TPH,
                JALAN &
                BIN </h4>
        </div>

        <table class="table ">
            <thead>
                <tr>
                    <td>logo</td>
                    <td>
                        <h6>PT. SAWIT SUMBERMAS SARANA, TBK</h6>
                        <p>QUALITY CONTROL</p>
                    </td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p class="float-end">Wilayah - I</p>
                    </td>

                    </td>
                </tr>
            </thead>
        </table>

        {{-- table rekap --}}

        <div class=" row col-12 text-center align-items-center">
            <table id="myTable" class="table table-sm table-bordered text-center" style="border: 1px solid black">
                <thead>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            ESTATE
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            AFDELING
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">NAMA
                            ASISTEN</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">HA
                            SAMPLE</th>
                        <th style="background-color : #e8ecdc; color: #000000;" colspan="2" class="text-center">
                            BRONDOLAN TINGGAL (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            KARUNG
                            BERISI BRONDOLAN (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">BUAH
                            TINGGAL TPH (JANJANG)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            RESTAN
                            TIDA DILAPORKAN (JANJANG)</th>
                    </tr>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">TPH</th>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">JALAN & BIN</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ha_total_wil = 0;
                    $tph_total_wil = 0;
                    $jlnBin_total_wil = 0;
                    $buahTinggla_total_wil = 0;
                    $karung_total_wil = 0;
                    $Restan_total_wil = 0;
                    @endphp
                    {{-- testing --}}
                    @foreach ($wil_1_sidak_tph as $key => $item)
                    @if (is_array($item))
                    @foreach ($item as $key2 => $value)
                    @if (is_array($value))
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$key2}}</td>
                        <td>-</td>
                        <td>{{$value['ha_sample']}}</td>
                        <td>{{$value['tph']}}</td>
                        <td>{{$value['jalan_bin']}}</td>
                        <td>{{$value['karung']}}</td>
                        <td>{{$value['buah_tinggal']}}</td>
                        <td>{{$value['restan_unreported']}}</td>
                        @php
                        $ha_total_wil += $value['ha_sample'];
                        $tph_total_wil += $value['tph'];
                        $jlnBin_total_wil += $value['jalan_bin'];
                        $karung_total_wil += $value['karung'];
                        $buahTinggla_total_wil += $value['buah_tinggal'];
                        $Restan_total_wil += $value['restan_unreported'];
                        @endphp
                    </tr>
                    @endif
                    @endforeach
                    {{-- Total Estate --}}
                    <tr>
                        <td style="background-color : #e8ecdc; color: #000000;" colspan="3">TOTAL</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['ha_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['brondolan_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['JalanBin_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;"> {{$item['Karung_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['BuahTinggal_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['RestanUnreported_total']}}
                        </td>
                    </tr>
                    {{-- Total WILAYAH --}}
                    @endif
                    @endforeach
                    <tr>
                        <td style="background-color : #ffe494; color: #000000;" colspan="3">TOTAL WILAYAH</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $ha_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $tph_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $jlnBin_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;"> {{ $karung_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $buahTinggla_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $Restan_total_wil }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- end tabel rekap --}}


        {{-- table highest funding record --}}
        <div class="table-responsive">
            <table id="Table1" class="table table-bordered table-sm ">
                <thead>
                    <th colspan="3" class="text-center" style="background-color : rgb(209, 160, 115);">
                        HIGHEST FINDING RECORD
                    </th>
                    <tr>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            KATEGORI</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            AFDELING</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            JUMLAH
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">BRONDOLAN TINGGAL</th>
                        @if(!empty($wil_1_sidak_tph_max)){
                        @foreach ($wil_1_sidak_tph_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['brondolan_maxx']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">KARUNG BERISI BRONDOLAN</th>
                        @if(!empty($wil_1_sidak_krng_max)){
                        @foreach ($wil_1_sidak_krng_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['karung_max']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">BUAH TINGGAL</th>
                        @if(!empty($wil_1_sidak_buah_max)){
                        @foreach ($wil_1_sidak_buah_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['buah_tgl_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">RESTAN TIDAK DILAPORKAN</th>
                        @if(!empty($wil_1_sidak_restant_max)){
                        @foreach ($wil_1_sidak_restant_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['restant_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                </tbody>



            </table>
        </div>

        {{-- end table highest funding record --}}
        {{-- Chart Pie --}}
        <div class="row ">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Chart HERE</h5>
                        <p class="card-text">Load..</p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="page-break-after: always;"></div>
    {{-- End Chart Pie --}}

    {{-- page2 --}}

    <div class="container ">
        <div class="pb-12 mt-4">
            <h4 class="text-center border border-secondary border-2"> REKAPITULASI PEMERIKSAAN BRONDOLAN TINGGAL DI
                TPH,
                JALAN &
                BIN </h4>
        </div>
        <table class="table ">
            <thead>
                <tr>
                    <td>logo</td>
                    <td>
                        <h6>PT. SAWIT SUMBERMAS SARANA, TBK</h6>
                        <p>QUALITY CONTROL</p>
                    </td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p class="float-end">Wilayah - II</p>
                    </td>

                    </td>
                </tr>
            </thead>
        </table>

        {{-- table rekap --}}

        <div class=" row col-12 text-center align-items-center">
            <table class="table table-sm table-bordered text-center" style="border: 1px solid black">
                <thead>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            ESTATE
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            AFDELING
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">NAMA
                            ASISTEN</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">HA
                            SAMPLE</th>
                        <th style="background-color : #e8ecdc; color: #000000;" colspan="2" class="text-center">
                            BRONDOLAN TINGGAL (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            KARUNG
                            BERISI BRONDOLAN (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">BUAH
                            TINGGAL TPH (JANJANG)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            RESTAN
                            TIDA DILAPORKAN (JANJANG)</th>
                    </tr>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">TPH</th>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">JALAN & BIN</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ha_total_wil = 0;
                    $tph_total_wil = 0;
                    $jlnBin_total_wil = 0;
                    $buahTinggla_total_wil = 0;
                    $karung_total_wil = 0;
                    $Restan_total_wil = 0;
                    @endphp
                    {{-- testing --}}
                    @foreach ($wil_2_sidak_tph as $key => $item)
                    @if (is_array($item))
                    @foreach ($item as $key2 => $value)
                    @if (is_array($value))
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$key2}}</td>
                        <td>-</td>
                        <td>{{$value['ha_sample']}}</td>
                        <td>{{$value['tph']}}</td>
                        <td>{{$value['jalan_bin']}}</td>
                        <td>{{$value['karung']}}</td>
                        <td>{{$value['buah_tinggal']}}</td>
                        <td>{{$value['restan_unreported']}}</td>
                        @php
                        $ha_total_wil += $value['ha_sample'];
                        $tph_total_wil += $value['tph'];
                        $jlnBin_total_wil += $value['jalan_bin'];
                        $karung_total_wil += $value['karung'];
                        $buahTinggla_total_wil += $value['buah_tinggal'];
                        $Restan_total_wil += $value['restan_unreported'];
                        @endphp
                    </tr>
                    @endif
                    @endforeach
                    {{-- Total Estate --}}
                    <tr>
                        <td style="background-color : #e8ecdc; color: #000000;" colspan="3">TOTAL</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['ha_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['brondolan_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['JalanBin_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;"> {{$item['Karung_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['BuahTinggal_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['RestanUnreported_total']}}
                        </td>
                    </tr>
                    {{-- Total WILAYAH --}}
                    @endif
                    @endforeach
                    <tr>
                        <td style="background-color : #ffe494; color: #000000;" colspan="3">TOTAL WILAYAH</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $ha_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $tph_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $jlnBin_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;"> {{ $karung_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $buahTinggla_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $Restan_total_wil }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- end tabel rekap --}}


        {{-- table highest funding record --}}


        <div class="table-responsive">
            <table id="Table1" class="table table-bordered table-sm ">
                <thead>
                    <th colspan="3" class="text-center" style="background-color : #f8ccac;">
                        HIGHEST FINDING RECORD
                    </th>
                    <tr>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            KATEGORI</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            AFDELING</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            JUMLAH
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">BRONDOLAN TINGGAL</th>
                        @if(!empty($wil_2_sidak_tph_max)){
                        @foreach ($wil_2_sidak_tph_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['brondolan_maxx']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">KARUNG BERISI BRONDOLAN</th>
                        @if(!empty($wil_2_sidak_krng_max)){
                        @foreach ($wil_2_sidak_krng_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['karung_max']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">BUAH TINGGAL</th>
                        @if(!empty($wil_2_sidak_buah_max)){
                        @foreach ($wil_2_sidak_buah_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['buah_tgl_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">RESTAN TIDAK DILAPORKAN</th>
                        @if(!empty($wil_2_sidak_restant_max)){
                        @foreach ($wil_2_sidak_restant_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['restant_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                </tbody>



            </table>
        </div>
        {{-- end table highest funding record --}}
        {{-- Chart Pie --}}
        <div class="row ">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Chart HERE</h5>
                        <p class="card-text">Load..</p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- End Chart Pie --}}
    <div style="page-break-after: always;"></div>

    {{-- page 3 --}}

    <div class="container ">
        <div class="pb-12 mt-4">
            <h4 class="text-center border border-secondary border-2"> REKAPITULASI PEMERIKSAAN BRONDOLAN TINGGAL DI
                TPH,
                JALAN &
                BIN </h4>
        </div>
        <table class="table ">
            <thead>
                <tr>
                    <td>logo</td>
                    <td>
                        <h6>PT. SAWIT SUMBERMAS SARANA, TBK</h6>
                        <p>QUALITY CONTROL</p>
                    </td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p class="float-end">Wilayah - III</p>
                    </td>

                    </td>
                </tr>
            </thead>
        </table>

        {{-- table rekap --}}

        <div class=" row col-12 text-center align-items-center">
            <table class="table table-sm table-bordered text-center" style="border: 1px solid black">
                <thead>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            ESTATE
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            AFDELING
                        </th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">NAMA
                            ASISTEN</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">HA
                            SAMPLE</th>
                        <th style="background-color : #e8ecdc; color: #000000;" colspan="2" class="text-center">
                            BRONDOLAN TINGGAL (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            KARUNG
                            BERISI BRONDOLAN (JUMLAH)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">BUAH
                            TINGGAL TPH (JANJANG)</th>
                        <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                            RESTAN
                            TIDA DILAPORKAN (JANJANG)</th>
                    </tr>
                    <tr>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">TPH</th>
                        <th style="background-color : #e8ecdc; color: #000000;" class="text-center">JALAN & BIN</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ha_total_wil = 0;
                    $tph_total_wil = 0;
                    $jlnBin_total_wil = 0;
                    $buahTinggla_total_wil = 0;
                    $karung_total_wil = 0;
                    $Restan_total_wil = 0;
                    @endphp
                    {{-- testing --}}
                    @foreach ($wil_3_sidak_tph as $key => $item)
                    @if (is_array($item))
                    @foreach ($item as $key2 => $value)
                    @if (is_array($value))
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$key2}}</td>
                        <td>-</td>
                        <td>{{$value['ha_sample']}}</td>
                        <td>{{$value['tph']}}</td>
                        <td>{{$value['jalan_bin']}}</td>
                        <td>{{$value['karung']}}</td>
                        <td>{{$value['buah_tinggal']}}</td>
                        <td>{{$value['restan_unreported']}}</td>
                        @php
                        $ha_total_wil += $value['ha_sample'];
                        $tph_total_wil += $value['tph'];
                        $jlnBin_total_wil += $value['jalan_bin'];
                        $karung_total_wil += $value['karung'];
                        $buahTinggla_total_wil += $value['buah_tinggal'];
                        $Restan_total_wil += $value['restan_unreported'];
                        @endphp
                    </tr>
                    @endif
                    @endforeach
                    {{-- Total Estate --}}
                    <tr>
                        <td style="background-color : #e8ecdc; color: #000000;" colspan="3">TOTAL</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['ha_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['brondolan_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['JalanBin_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;"> {{$item['Karung_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['BuahTinggal_total']}}</td>
                        <td style="background-color : #e8ecdc; color: #000000;">{{$item['RestanUnreported_total']}}
                        </td>
                    </tr>
                    {{-- Total WILAYAH --}}
                    @endif
                    @endforeach
                    <tr>
                        <td style="background-color : #ffe494; color: #000000;" colspan="3">TOTAL WILAYAH</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $ha_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $tph_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $jlnBin_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;"> {{ $karung_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $buahTinggla_total_wil }}</td>
                        <td style="background-color : #ffe494; color: #000000;">{{ $Restan_total_wil }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- end tabel rekap --}}


        {{-- table highest funding record --}}

        <div class="table-responsive">
            <table id="Table1" class="table table-bordered table-sm ">
                <thead>
                    <th colspan="3" class="text-center" style="background-color : rgb(209, 160, 115);">
                        HIGHEST FINDING RECORD
                    </th>
                    <tr>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            KATEGORI</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            AFDELING</th>
                        <th scope="col" style="background-color : #b0d48c; color: #0f0f0f;" class="text-center ">
                            JUMLAH
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">BRONDOLAN TINGGAL</th>
                        @if(!empty($wil_3_sidak_tph_max)){
                        @foreach ($wil_3_sidak_tph_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['brondolan_maxx']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">KARUNG BERISI BRONDOLAN</th>
                        @if(!empty($wil_3_sidak_krng_max)){
                        @foreach ($wil_3_sidak_krng_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['karung_max']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">BUAH TINGGAL</th>
                        @if(!empty($wil_3_sidak_buah_max)){
                        @foreach ($wil_3_sidak_buah_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['buah_tgl_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                    <tr>
                        <th scope="row">RESTAN TIDAK DILAPORKAN</th>
                        @if(!empty($wil_3_sidak_restant_max)){
                        @foreach ($wil_3_sidak_restant_max as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <td>{{$key}} {{$key2}}</td>
                        <td>{{$items['restant_max_fix']}}</td>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        }
                        @endif
                    </tr>
                </tbody>


            </table>
        </div>
        {{-- end table highest funding record --}}
        {{-- Chart Pie --}}
        <div class="row ">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Chart HERE</h5>
                        <p class="card-text">Load..</p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End Chart Pie -->
    <div style="page-break-after: always;"></div>


    {{-- TABLE UNTUK DATA PERSETATE PER TABGGAL --}}

    <div class="container ">

        <div>
            @foreach ($DataPerTanggal as $key => $item)
            <div class="pb-12 mt-4">
                <h4 class="text-center border border-secondary border-2"> REKAPITULASI PER BLOK PEMERIKSAAN
                    BRONDOLAN
                    TINGGAL DI TPH & BIN </h4>
            </div>
            <div>
                <div>Estate : {{$item['estate']}}</div>
                <div>Tanggal: {{$item['tanggal']}}</div>

                <div class=" row col-12 text-center align-items-center">
                    <table class="table table-sm table-bordered text-center print-friendly"
                        style="border: 1px solid black">
                        <thead>
                            <tr>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    AFDELING
                                </th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    BLOK
                                </th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    NAMA
                                    STATUS
                                    (H+..)</th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    HA
                                    HA
                                    SAMPLE</th>
                                <th style="background-color : #e8ecdc; color: #000000;" colspan="2" class="text-center">
                                    BRONDOLAN TINGGAL (JUMLAH)</th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    KARUNG
                                    BERISI BRONDOLAN (JUMLAH)</th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    BUAH
                                    TINGGAL TPH (JANJANG)</th>
                                <th style="background-color : #e8ecdc; color: #000000;" rowspan="2" class="text-center">
                                    RESTAN
                                    TIDA DILAPORKAN (JANJANG)</th>
                            </tr>
                            <tr>
                                <th style="background-color : #e8ecdc; color: #000000;" class="text-center">TPH</th>
                                <th style="background-color : #e8ecdc; color: #000000;" class="text-center">JALAN &
                                    BIN
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $ha_total_wil = 0;
                            $tph_total_wil = 0;
                            $jlnBin_total_wil = 0;
                            $buahTinggla_total_wil = 0;
                            $karung_total_wil = 0;
                            $Restan_total_wil = 0;
                            $binDanJalan = 0;
                            @endphp
                            @foreach ($item as $key2 =>$value)
                            @if (is_array($value))
                            @foreach ($value as $key3 => $value2)
                            @if (is_array($value2))
                            <tr>
                                @php
                                $binDanJalan = $value2['bt_jalan'] + $value2['bt_bin'];
                                @endphp
                                <td>{{$key2}}</td>
                                <td>{{$key3}}</td>
                                <td>-</td>
                                <td>{{$value2['luas']}}</td>
                                <td>{{$value2['bt_tph']}}</td>
                                <td>{{$binDanJalan}}</td>
                                <td>{{$value2['jum_karung']}}</td>
                                <td>{{$value2['buah_tinggal']}}</td>
                                <td>{{$value2['restan_unreported']}}</td>
                                @php
                                $ha_total_wil += $value2['luas'];
                                $tph_total_wil += $value2['bt_tph'];
                                $jlnBin_total_wil += $binDanJalan;
                                $karung_total_wil += $value2['jum_karung'];
                                $buahTinggla_total_wil += $value2['buah_tinggal'];
                                $Restan_total_wil += $value2['restan_unreported'];
                                @endphp
                            </tr>
                            @endif
                            @endforeach
                            <tr>
                                <td style="background-color : #e8ecdc; color: #000000;" colspan="3">TOTAL</td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{$value['ha_total']}}</td>
                                <td style="background-color : #e8ecdc; color: #000000;">
                                    {{$value['brondolan_total']}}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{$value['JalanBin_total']}}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;"> {{$value['Karung_total']}}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;">
                                    {{$value['BuahTinggal_total']}}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;">
                                    {{$value['RestanUnreported_total']}}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            <tr>
                                <td style="background-color : #e8ecdc; color: #000000;" colspan="3">GRAND TOTAL</td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{ $ha_total_wil }}</td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{ $tph_total_wil }}</td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{ $jlnBin_total_wil }}</td>
                                <td style="background-color : #e8ecdc; color: #000000;"> {{ $karung_total_wil }}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{ $buahTinggla_total_wil }}
                                </td>
                                <td style="background-color : #e8ecdc; color: #000000;">{{ $Restan_total_wil }}</td>
                            </tr>
                        </tbody>

                </div>
                <div style="page-break-after: always;"></div>
                @endforeach

                {{-- Table Untuk FOTO --}}

                <div class="container ">
                    <div class="pb-12 mt-4">
                        <h4 class="text-center border border-secondary border-2"> MAIN ISSUE FOTO TEMUAN PEMERIKSAAN
                            BRONDOLAN TINGGAL DI TPH, JALAN & BIN </h4>
                    </div>
                    <table class="table ">
                        <thead>
                            <tr>
                                <td>logo</td>
                                <td>
                                    <h6>PT. SAWIT SUMBERMAS SARANA, TBK</h6>
                                    <p>QUALITY CONTROL</p>
                                </td>

                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <p class="float-end">Regional-I </p>
                                </td>

                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <table class="table table-sm table-bordered text-center" style="border: 1px solid black">
                    <thead>
                        <tr>
                            <th style="background-color: #d8dce4; color: #000000;" colspan="12" class="text-center">
                                FOTO
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($will_1_foto))
                        @php
                        $counter = 0;
                        @endphp
                        @foreach ($will_1_foto as $key => $item)
                        @if (is_array($item))
                        @foreach ($item as $key2 => $items)
                        @if(is_array($items) && !empty($items))
                        <tr>
                            @foreach($items as $key3 => $items1)
                            @if(is_array($items1) && !empty($items1))
                            <td style="width: 50%;">
                                <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/{{$items1['foto_temuan']}}"
                                    alt="" class="responsive">
                                {{$key2}} {{$key3}} {{$items1['blok']}}
                            </td>
                            @php
                            $counter++;
                            @endphp
                            @if($counter == 3)
                        </tr>
                        <tr>
                            @php
                            $counter = 0;
                            @endphp
                            @endif
                            @endif
                            @endforeach
                        </tr>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        @endif
                    </tbody>
                </table>


                {{-- <div style="page-break-after: always;"></div> --}}








                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                    crossorigin="anonymous">
                </script>

</body>

</html>