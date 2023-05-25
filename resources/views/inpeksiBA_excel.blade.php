<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export To Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('table-to-excel-master/dist/tableToExcel.js') }}"></script>
</head>

<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
    }

    .my-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .my-table th {
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 17px;
    }

    .my-table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    th,
    td {
        border: 1px solid black;
        text-align: center;
        padding: 2px;
    }

    /* The rest of your CSS */

    .sticky-footer {
        margin-top: auto;
        /* Push the footer to the bottom */
    }



    .header {
        display: flex;
        align-items: center;
    }

    .text-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-left: 5px;
    }

    .logo-container {
        display: flex;
        align-items: center;
    }


    .logo {
        height: 60px;
        width: auto;
        align-items: flex-start;
    }

    .pt-name,
    .qc-name {
        margin: 0;
        padding-left: 1px;
    }

    .text-container {
        margin-left: 15px;
    }

    .right-container {
        text-align: right;

    }

    .form-inline {
        display: flex;
        align-items: center;
    }

    .custom-tables-container {
        display: flex;
        justify-content: space-between;
    }

    .custom-table {
        border-collapse: collapse;
        width: 45%;
    }

    .custom-table,
    .custom-table th,
    .custom-table td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }



    .table-1-no-border td {
        border: none;
    }


    /*body {*/
    /*    filter: blur(5px);*/
    /*} */
</style>

<body>

    <style>
        .custom-border {
            border: 1px solid #000;
            padding: 8px;

        }
    </style>

    <div class="d-flex justify-content-center custom-border">
        <h2 class="text-center">BERITA ACARA REKAPITULASI PEMERIKSAAN KUALITAS PANEN QUALITY CONTROL</h2>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                <div>
                    <img src="{{ asset('img/logo-SSS.png') }}" style="height:60px">
                </div>
            </td>
            <td style="width:30%;border:0;">

                <p style="text-align: left; font-size: 20px;">PT. SAWIT SUMBERMAS SARANA,TBK</p>
                <p style="text-align: left;">QUALITY CONTROL</p>

            </td>
            <td style=" width: 20%;border:0;">
            </td>
            <td style="vertical-align: middle; text-align: right;width:40%;border:0;">
                <div class="right-container">
                    <div class="text-container">
                        <div class="afd" style="font-size: 20px;">Periode pemeriksaan ke: _______________</div>
                        <div class="afd" style="font-size: 20px;">ESTATE/ AFD: {{$data['est']}} {{$data['afd']}}</div>
                        <div class="afd" style="font-size: 20px;">TANGGAL: {{$data['tanggal']}}</div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    @if($data['reg'] == 2)

    <div class="ml-3 mr-3 mb-3">
        <div class="row text-center tbl-fixed">
            <table id="headshot">
                <thead style="color: white;">
                    <tr>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">Est.</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">Afd.</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="freeze-col align-middle" class="align-middle" colspan="6" rowspan="2" bgcolor="#588434">DATA BLOK SAMPEL</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="freeze-col align-middle" class="align-middle" colspan="17" bgcolor="#588434">Mutu Ancak (MA)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" colspan="8" bgcolor="blue">Mutu Transport (MT)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="freeze-col align-middle" class="align-middle" colspan="23" bgcolor="#ffc404" style="color: #000000;">Mutu Buah (MB)
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">All Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">Kategori</th>
                        </th>
                    </tr>
                    <tr>
                        {{-- Table Mutu Ancak --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="6" bgcolor="#588434">Brondolan Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="7" bgcolor="#588434">Buah Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="3" bgcolor="#588434">Pelepah Sengkleh</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" rowspan="2" bgcolor="#588434">Total Skor</th>

                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" rowspan="2" bgcolor="blue">TPH Sampel</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" colspan="3" bgcolor="blue">Brd Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" colspan="3" bgcolor="blue">Buah Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" rowspan="2" bgcolor="blue">Total Skor</th>

                        {{-- Table Mutu Buah --}}
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">TPH Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Janjang
                            Sampel</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Mentah (A)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Matang (N)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Lewat Matang
                            (O)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Janjang Kosong
                            (E)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Tidak Standar
                            V-Cut</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="2" bgcolor="#ffc404" style="color: #000000;">Abnormal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Penggunaan
                            Karung Brondolan</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Skor</th>
                    </tr>
                    <tr>
                        {{-- Table Mutu Ancak --}}
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Ancak Pemanen</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Jumlah Pokok Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Luas Ha Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Jumlah Jjg Panen</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">AKP Realisasi</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Status Panen</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">P</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">K</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">GL</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Total Brd</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Brd/JJG</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">S</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M1</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M2</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M3</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Total JJG</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Pokok </th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        {{-- Table Mutu Trans --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Butir</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Butir/TPH</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Jjg/TPH</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Skor</th>
                        {{-- table mutu Buah --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Ya</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $mergedData = array_merge_recursive($data['mutuAncak_total'], $data['mutuTransport_total'],$data['mutuBuah_total']);
                    @endphp
                    @foreach ($mergedData as $key => $items)
                    @foreach ($items as $key2 => $items2)
                    @if (is_array($items2))
                    @php

                    $skor_brd = $items2['skor_brd'] ?? 0;
                    $skor_buah = $items2['skor_buah'] ?? 0;
                    $skor_palepah = $items2['skor_palepah'] ?? 0;
                    $skor_totalancak = $skor_brd + $skor_buah + $skor_palepah;


                    $skor_totalancak = ($skor_brd || $skor_buah || $skor_palepah) ? $skor_totalancak : 0;

                    @endphp

                    <tr>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key2}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['pemanen']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jml_pokok_sampel']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" class="blok_est">{{$items2['luas_ha']?? '-'}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jml_jjg_panen']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['akp_real']?? '-'}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['status_panen']?? '-'}}</td>

                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['status_panen']?? '-'}}</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['p_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['k_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['gl_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['total_brd_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['btr_jjg_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_brd']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhts_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm1_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm2_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm3_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['tot_jjg_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jjg_tgl_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_buah']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['ps_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['PerPSMA']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_palepah']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="tot_ancakest">{{$skor_totalancak }}</td>
                        <!-- mutu trans  -->

                        @php
                        $skor_brdTrans = $items2['skor_bt'] ?? 0;
                        $skor_buahTrans = $items2['skoring_restan'] ?? 0;

                        $skor_transtod = $skor_brdTrans + $skor_buahTrans ;


                        $skor_transtod = ($skor_brdTrans || $skor_buahTrans || $skor_palepah) ? $skor_transtod : 0;
                        $tph_sample_modified = 0;
                        if (array_key_exists('status_panen', $items2) && $items2['status_panen'] <= 3) { if (array_key_exists('luas_ha', $items2)) { $tph_sample_modified=round($items2['luas_ha'] * 1.3 ,2); } } else { $tph_sample_modified=$items2['tph_sample'] ?? 0 ; } @endphp <!-- mutu transport -->
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" class="cell-23"> {{$tph_sample_modified}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bt_total']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_bt']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['restan_total']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_restan']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skoring_restan']?? '-'}}</td>
                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="trans_skorest">{{$skor_transtod }}</td>
                            <!-- mutu buah  -->
                            @php
                            $skor_mentah = $items2['skor_mentah'] ?? 0;
                            $skor_matang = $items2['skor_matang'] ?? 0;
                            $skor_over = $items2['skor_over'] ?? 0;
                            $skor_kosong = $items2['skor_kosong'] ?? 0;
                            $skor_vcut = $items2['skor_vcut'] ?? 0;
                            $skor_karung = $items2['skor_karung'] ?? 0;

                            $skor_buahtod = $skor_mentah + $skor_matang + $skor_over + $skor_kosong + $skor_vcut + $skor_karung ;

                            // Display '-' when all keys are missing
                            $skor_buahtod = ($skor_mentah || $skor_matang || $skor_over || $skor_kosong || $skor_vcut || $skor_karung) ? $skor_buahtod : 0;

                            $grand_total_skor = $skor_totalancak + $skor_transtod + $skor_buahtod;
                            if (!function_exists('skor_kategori_akhir')) {
                            function skor_kategori_akhir($skor)
                            {
                            if ($skor >= 95.0 && $skor <= 100.0) { $color="#4874c4" ; $text="EXCELLENT" ; return array($color, $text); } else if ($skor>= 85.0 && $skor < 95.0) { $color="#00ff2e" ; $text="GOOD" ; return array($color, $text); } else if ($skor>= 75.0 && $skor < 85.0) { $color="yellow" ; $text="SATISFACTORY" ; return array($color, $text); } else if ($skor>= 65.0 && $skor < 75.0) { $color="orange" ; $text="FAIR" ; return array($color, $text); } else if ($skor < 65.0) { $color="red" ; $text="POOR" ; return array($color, $text); } } } $Kategori=skor_kategori_akhir($grand_total_skor); if ($grand_total_skor>= 95.0 && $grand_total_skor <= 100.0) { $colors="4874c4" ; } else if ($grand_total_skor>= 85.0 && $grand_total_skor < 95.0) { $colors="00ff2e" ; } else if ($grand_total_skor>= 75.0 && $grand_total_skor < 85.0) { $colors="ffff00" ; } else if ($grand_total_skor>= 65.0 && $grand_total_skor < 75.0) { $colors="ffa500" ; } else if ($grand_total_skor < 65.0) { $colors="ff0000" ; } @endphp <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['blok_mb']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_janjang']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_mentah'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahMentah']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_mentah']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_masak']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahMasak'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_matang'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_over']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahOver'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_over'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_empty'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenPerJanjang']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_kosong']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_vcut'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenVcut'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_vcut'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_abnormal']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenAbr'] ?? '-'}}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['alas_mb']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenKrgBrd']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_karung']?? '-' }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="buah_est">{{$skor_buahtod }}</td>
                                                            <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$colors}}" data-a-v="middle" id="est_totalSkor">{{$grand_total_skor }}</td>

                                                            <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="{{$colors}}" style="background-color: {{ $Kategori[0] }};">{{ $Kategori[1] }}</td>


                    </tr>

                    @endif
                    @endforeach
                    @endforeach
                    @foreach ($mergedData as $key => $items)
                    <tr>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" colspan="3">{{$key}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jml_pokok_ma']}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_luas_ha_ma']}}</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="blok_luas">aw</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jml_jjg_panen_ma']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['akp_real_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['p_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['k_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['gl_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['total_brd_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['btr_jjg_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_brd_ma($items['btr_jjg_ma_est'])}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhts_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm1_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm2_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm3_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jjg_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['jjg_tgl_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_Ma($items['jjg_tgl_ma_est'])}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['ps_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['PerPSMA_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_palepah_ma($items['PerPSMA_est'])}}</td>
                        @php
                        $tot_skors = skor_brd_ma($items['btr_jjg_ma_est']) + skor_buah_Ma($items['jjg_tgl_ma_est']) + skor_palepah_ma($items['PerPSMA_est']);
                        @endphp
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="ancak">{{$tot_skors }}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="tph_sample_total"></td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="cell-24">{{$items['bt_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="cell-25">{{$items['bt_tph_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="skor_brd_tinggal">{{skor_brd_tinggal($items['bt_tph_total'] ?? 0)}}</td>


                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="jjg_tph_total"> {{$items['jjg_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="totals_tphjjg">{{$items['jjg_tph_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="skor_buah_tinggal">{{skor_buah_tinggal($items['jjg_tph_total'] ?? 0)}}</td>
                        @php
                        $trans_tod = skor_brd_tinggal($items['bt_tph_total']?? 0) + skor_buah_tinggal($items['jjg_tph_total'] ?? 0) ;
                        @endphp
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">pke</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="trans_tod">{{$trans_tod}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items['tot_blok'] ?? 0 }}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jjg'] ?? 0 }}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_mentah'] ?? 0 }}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahMentah'] ?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_mentah_mb($items['tot_PersenBuahMentah'] ?? 0 )}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_matang'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahMasak'] ?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_masak_mb($items['tot_PersenBuahMasak'] ?? 0)}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_over'] ?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahOver'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_over_mb($items['tot_PersenBuahOver'] ?? 0)}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_empty']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenPerJanjang']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_jangkos_mb($items['tot_PersenPerJanjang']?? 0)}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_vcut']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenVcut']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_vcut_mb($items['tot_PersenVcut']?? 0)}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_abr']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenAbr']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_krg_brd']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenKrgBrd']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_abr_mb($items['tot_PersenKrgBrd']?? 0)}}</td>



                        @php
                        $total_buahskor = skor_buah_mentah_mb($items['tot_PersenBuahMentah']?? 0) +skor_buah_masak_mb($items['tot_PersenBuahMasak'] ?? 0) + skor_buah_over_mb($items['tot_PersenBuahOver']?? 0) +skor_jangkos_mb($items['tot_PersenPerJanjang']?? 0) +skor_vcut_mb($items['tot_PersenVcut']?? 0) + skor_abr_mb($items['tot_PersenKrgBrd']?? 0);
                        $total_skoring = $skor_totalancak + $skor_transtod + $total_buahskor;
                        $skor_kategori_akhirx = skor_kategori_akhir($total_skoring);
                        $grand_total_skor_kategori = skor_kategori_akhir($total_skoring);

                        if ($total_skoring >= 95.0 && $total_skoring <= 100.0) { $color="4874c4" ; } else if ($total_skoring>= 85.0 && $total_skoring < 95.0) { $color="00ff2e" ; } else if ($total_skoring>= 75.0 && $total_skoring < 85.0) { $color="ffff00" ; } else if ($total_skoring>= 65.0 && $total_skoring < 75.0) { $color="ffa500" ; } else if ($total_skoring < 65.0) { $color="ff0000" ; } @endphp <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" id="buah">{{ $total_buahskor }}</td>

                                        <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}" data-a-v="middle" id="total_skoring"></td>

                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="{{$color}}" style="background-color: {{ $skor_kategori_akhirx[0] }};" id="bg_skoring">{{ $skor_kategori_akhirx[1] }}</td>
                    </tr>
                    @endforeach

                </tbody>


            </table>
        </div>
    </div>

    @else

    <div class="ml-3 mr-3 mb-3">
        <div class="row text-center tbl-fixed">
            <table id="headshot">
                <thead style="color: white;">
                    <tr>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">Est.</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">Afd.</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="freeze-col align-middle" class="align-middle" colspan="4" rowspan="2" bgcolor="#588434">DATA BLOK SAMPEL</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="freeze-col align-middle" class="align-middle" colspan="17" bgcolor="#588434">Mutu Ancak (MA)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" colspan="8" bgcolor="blue">Mutu Transport (MT)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="freeze-col align-middle" class="align-middle" colspan="23" bgcolor="#ffc404" style="color: #000000;">Mutu Buah (MB)
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">All Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="freeze-col align-middle" class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">Kategori</th>
                        </th>
                    </tr>
                    <tr>
                        {{-- Table Mutu Ancak --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="6" bgcolor="#588434">Brondolan Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="7" bgcolor="#588434">Buah Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" colspan="3" bgcolor="#588434">Pelepah Sengkleh</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" rowspan="2" bgcolor="#588434">Total Skor</th>

                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" rowspan="2" bgcolor="blue">TPH Sampel</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" colspan="3" bgcolor="blue">Brd Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" colspan="3" bgcolor="blue">Buah Tinggal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class=" align-middle" rowspan="2" bgcolor="blue">Total Skor</th>

                        {{-- Table Mutu Buah --}}
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">TPH Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Janjang
                            Sampel</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Mentah (A)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Matang (N)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Lewat Matang
                            (O)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Janjang Kosong
                            (E)</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Tidak Standar
                            V-Cut</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="2" bgcolor="#ffc404" style="color: #000000;">Abnormal</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Penggunaan
                            Karung Brondolan</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Skor</th>
                    </tr>
                    <tr>
                        {{-- Table Mutu Ancak --}}
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Jumlah Pokok Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Luas Ha Sampel</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Jumlah Jjg Panen</th>
                        <th data-a-wrap="true" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">AKP Realisasi</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">P</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">K</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">GL</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Total Brd</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Brd/JJG</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">S</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M1</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M2</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">M3</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Total JJG</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Pokok </th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="588434" class="align-middle" bgcolor="#588434">Skor</th>
                        {{-- Table Mutu Trans --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Butir</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Butir/TPH</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Skor</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Jjg/TPH</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="1c5870" class="align-middle" bgcolor="blue">Skor</th>
                        {{-- table mutu Buah --}}
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>

                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Ya</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $mergedData = array_merge_recursive($data['mutuAncak_total'], $data['mutuTransport_total'],$data['mutuBuah_total']);
                    @endphp
                    @foreach ($mergedData as $key => $items)
                    @foreach ($items as $key2 => $items2)
                    @if (is_array($items2))
                    @php
                    $skor_brd = $items2['skor_brd'] ?? 0;
                    $skor_buah = $items2['skor_buah'] ?? 0;
                    $skor_palepah = $items2['skor_palepah'] ?? 0;
                    $skor_totalancak = $skor_brd + $skor_buah + $skor_palepah;

                    // Display '-' when all keys are missing
                    $skor_totalancak = ($skor_brd || $skor_buah || $skor_palepah) ? $skor_totalancak : 0;

                    @endphp

                    <tr>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key2}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jml_pokok_sampel']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['luas_ha']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jml_jjg_panen']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['akp_real']?? '-'}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['p_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['k_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['gl_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['total_brd_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['btr_jjg_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_brd']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhts_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm1_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm2_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bhtm3_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['tot_jjg_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['jjg_tgl_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_buah']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['ps_ma']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['PerPSMA']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_palepah']?? '-'}}</td>
                        <!-- <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td> -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$skor_totalancak }}</td>
                        <!-- mutu trans  -->

                        @php
                        $skor_brdTrans = $items2['skor_bt'] ?? 0;
                        $skor_buahTrans = $items2['skoring_restan'] ?? 0;

                        $skor_transtod = $skor_brdTrans + $skor_buahTrans ;

                        // Display '-' when all keys are missing
                        $skor_transtod = ($skor_brdTrans || $skor_buahTrans || $skor_palepah) ? $skor_transtod : 0;
                        @endphp

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['tph_sample']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['bt_total']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_bt']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['restan_total']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skor_restan']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items2['skoring_restan']?? '-'}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$skor_transtod }}</td>
                        <!-- mutu buah  -->
                        @php
                        $skor_mentah = $items2['skor_mentah'] ?? 0;
                        $skor_matang = $items2['skor_matang'] ?? 0;
                        $skor_over = $items2['skor_over'] ?? 0;
                        $skor_kosong = $items2['skor_kosong'] ?? 0;
                        $skor_vcut = $items2['skor_vcut'] ?? 0;
                        $skor_karung = $items2['skor_karung'] ?? 0;

                        $skor_buahtod = $skor_mentah + $skor_matang + $skor_over + $skor_kosong + $skor_vcut + $skor_karung ;

                        // Display '-' when all keys are missing
                        $skor_buahtod = ($skor_mentah || $skor_matang || $skor_over || $skor_kosong || $skor_vcut || $skor_karung) ? $skor_buahtod : 0;

                        $grand_total_skor = $skor_totalancak + $skor_transtod + $skor_buahtod;
                        if (!function_exists('skor_kategori_akhir')) {
                        function skor_kategori_akhir($skor)
                        {
                        if ($skor >= 95.0 && $skor <= 100.0) { $color="#4874c4" ; $text="EXCELLENT" ; return array($color, $text); } else if ($skor>= 85.0 && $skor < 95.0) { $color="#00ff2e" ; $text="GOOD" ; return array($color, $text); } else if ($skor>= 75.0 && $skor < 85.0) { $color="yellow" ; $text="SATISFACTORY" ; return array($color, $text); } else if ($skor>= 65.0 && $skor < 75.0) { $color="orange" ; $text="FAIR" ; return array($color, $text); } else if ($skor < 65.0) { $color="red" ; $text="POOR" ; return array($color, $text); } } } $Kategori=skor_kategori_akhir($grand_total_skor); if ($grand_total_skor>= 95.0 && $grand_total_skor <= 100.0) { $colors="4874c4" ; } else if ($grand_total_skor>= 85.0 && $grand_total_skor < 95.0) { $colors="00ff2e" ; } else if ($grand_total_skor>= 75.0 && $grand_total_skor < 85.0) { $colors="ffff00" ; } else if ($grand_total_skor>= 65.0 && $grand_total_skor < 75.0) { $colors="ffa500" ; } else if ($grand_total_skor < 65.0) { $colors="ff0000" ; } @endphp <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['blok_mb']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_janjang']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_mentah'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahMentah']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_mentah']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_masak']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahMasak'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_matang'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_over']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenBuahOver'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_over'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_empty'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenPerJanjang']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_kosong']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_vcut'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenVcut'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_vcut'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['jml_abnormal']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenAbr'] ?? '-'}}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['alas_mb']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['PersenKrgBrd']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $items2['skor_karung']?? '-' }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$skor_buahtod }}</td>
                                                        <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$colors}}" data-a-v="middle">{{$grand_total_skor }}</td>

                                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="{{$colors}}" style="background-color: {{ $Kategori[0] }};">{{ $Kategori[1] }}</td>


                    </tr>

                    @endif
                    @endforeach
                    @endforeach
                    @foreach ($mergedData as $key => $items)
                    <tr>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jml_pokok_ma']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_luas_ha_ma']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jml_jjg_panen_ma']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['akp_real_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['p_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['k_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['gl_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['total_brd_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['btr_jjg_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_brd_ma($items['btr_jjg_ma_est'])}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhts_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm1_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm2_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bhtm3_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jjg_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['jjg_tgl_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_Ma($items['jjg_tgl_ma_est'])}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['ps_ma_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['PerPSMA_est']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_palepah_ma($items['PerPSMA_est'])}}</td>
                        @php
                        $tot_skors = skor_brd_ma($items['btr_jjg_ma_est']) + skor_buah_Ma($items['jjg_tgl_ma_est']) + skor_palepah_ma($items['PerPSMA_est']);
                        @endphp
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tot_skors}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tph_sample_total']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bt_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['bt_tph_total'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_brd_tinggal($items['bt_tph_total']?? 0)}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['jjg_total']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['jjg_tph_total']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_tinggal($items['jjg_tph_total']?? 0)}}</td>
                        @php
                        $trans_tod = skor_brd_tinggal($items['bt_tph_total']?? 0) + skor_buah_tinggal($items['jjg_tph_total']?? 0) ;
                        @endphp

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$trans_tod}}</td>

                        <!-- mutu buah  -->
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_blok']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_jjg'] ?? 0 }}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_mentah'] ?? 0 }}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahMentah'] ?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_mentah_mb($items['tot_PersenBuahMentah'] ?? 0 )}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_matang'] ?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahMasak'] ?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_masak_mb($items['tot_PersenBuahMasak'] ?? 0)}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_over']}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenBuahOver']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_buah_over_mb($items['tot_PersenBuahOver']?? 0)}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_empty']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenPerJanjang']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_jangkos_mb($items['tot_PersenPerJanjang']?? 0)}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_vcut']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenVcut']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_vcut_mb($items['tot_PersenVcut']?? 0)}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_abr']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenAbr']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_krg_brd']?? 0}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items['tot_PersenKrgBrd']?? 0}}</td>

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{skor_abr_mb($items['tot_PersenKrgBrd']?? 0)}}</td>



                        @php
                        $total_buahskor = skor_buah_mentah_mb($items['tot_PersenBuahMentah'] ?? 0 ) + skor_buah_masak_mb($items['tot_PersenBuahMasak'] ?? 0) + skor_buah_over_mb($items['tot_PersenBuahOver']?? 0) + skor_jangkos_mb($items['tot_PersenPerJanjang']?? 0) + skor_vcut_mb($items['tot_PersenVcut']?? 0) + skor_abr_mb($items['tot_PersenKrgBrd']?? 0);
                        $total_skoring = $tot_skors + $trans_tod + $total_buahskor;
                        $skor_kategori_akhirx = skor_kategori_akhir($total_skoring);
                        $grand_total_skor_kategori = skor_kategori_akhir($total_skoring);

                        if ($total_skoring >= 95.0 && $total_skoring <= 100.0) { $color="4874c4" ; } else if ($total_skoring>= 85.0 && $total_skoring < 95.0) { $color="00ff2e" ; } else if ($total_skoring>= 75.0 && $total_skoring < 85.0) { $color="ffff00" ; } else if ($total_skoring>= 65.0 && $total_skoring < 75.0) { $color="ffa500" ; } else if ($total_skoring < 65.0) { $color="ff0000" ; } @endphp <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $total_buahskor }}</td>

                                        <td data-b-a-s="thin" data-a-h="center" data-fill-color="{{$color}}" data-a-v="middle">{{ $total_skoring }}</td>

                                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="{{$color}}" style="background-color: {{ $skor_kategori_akhirx[0] }};">{{ $skor_kategori_akhirx[1] }}</td>
                    </tr>
                    @endforeach

                </tbody>


            </table>
        </div>
    </div>
    @endif

    <button onclick="exportToExcel()">Export to Excel</button>




    <script>
        const reg = "{{ $data['reg'] }}";

        // Convert the string to a number
        const regNumber = parseInt(reg);

        // Now you can use the `regNumber` variable in your JavaScript code
        if (regNumber === 2) {
            function updateTPHSampleTotal() {
                // Get all the TPH sample cells with the class "cell-23"
                const tphSampleCells = document.getElementsByClassName("cell-23");

                // Calculate the sum of TPH sample values
                let sum = 0;
                for (let i = 0; i < tphSampleCells.length; i++) {
                    const cellValue = tphSampleCells[i].innerText;
                    sum += cellValue ? parseFloat(cellValue) : 0;
                }

                // Update the TPH sample total cell
                document.getElementById("tph_sample_total").innerText = sum;
            }


            // Call the function after the table is rendered
            updateTPHSampleTotal();

            function skor_brd_tinggal(skor) {
                if (skor <= 3) {
                    return 10;
                } else if (skor >= 3 && skor <= 5) {
                    return 8;
                } else if (skor >= 5 && skor <= 7) {
                    return 6;
                } else if (skor >= 7 && skor <= 9) {
                    return 4;
                } else if (skor >= 9 && skor <= 11) {
                    return 2;
                } else if (skor >= 11) {
                    return 0;
                }
            }

            function skor_buah_tinggal(skor) {
                if (skor <= 0.0) {
                    return 10;
                } else if (skor >= 0.0 && skor <= 0.5) {
                    return 8;
                } else if (skor >= 0.5 && skor <= 1) {
                    return 6;
                } else if (skor >= 1.0 && skor <= 1.5) {
                    return 4;
                } else if (skor >= 1.5 && skor <= 2.0) {
                    return 2;
                } else if (skor >= 2.0 && skor <= 2.5) {
                    return 0;
                } else if (skor >= 2.5 && skor <= 3.0) {
                    return -2;
                } else if (skor >= 3.0 && skor <= 3.5) {
                    return -4;
                } else if (skor >= 3.5 && skor <= 4.0) {
                    return -6;
                } else if (skor >= 4.0) {
                    return -8;
                }
            }


            function skor_kategori_akhir(skor) {
                let color, text;
                if (skor >= 95.0 && skor <= 100.0) {
                    color = "#4874c4";
                    text = "EXCELLENT";
                } else if (skor >= 85.0 && skor < 95.0) {
                    color = "#00ff2e";
                    text = "GOOD";
                } else if (skor >= 75.0 && skor < 85.0) {
                    color = "yellow";
                    text = "SATISFACTORY";
                } else if (skor >= 65.0 && skor < 75.0) {
                    color = "orange";
                    text = "FAIR";
                } else if (skor < 65.0) {
                    color = "red";
                    text = "POOR";
                }
                return {
                    color,
                    text
                };
            }

            document.addEventListener("DOMContentLoaded", function() {
                let blok_blok = 0;
                const blokElements = document.getElementsByClassName("blok_est");

                for (let i = 0; i < blokElements.length; i++) {
                    let blokValue = blokElements[i].innerText == '-' ? 0 : parseFloat(blokElements[i].innerText);
                    blok_blok += blokValue;
                }

                document.getElementById("blok_luas").innerText = blok_blok.toFixed(2);
            });

            function updateBtTphTotal() {
                // Get the bt_total and tph_sample_total cells
                const btTotalCell = document.getElementById("cell-24");

                const jjg_tph_total = document.getElementById("jjg_tph_total");
                const tphSampleTotalCell = document.getElementById("tph_sample_total");

                // Get the values from the cells
                let btTotalValue = btTotalCell.innerText == '-' ? 0 : parseFloat(btTotalCell.innerText);

                let tphSampleTotalValue = tphSampleTotalCell.innerText == '-' ? 0 : parseFloat(tphSampleTotalCell.innerText);
                let jjgTotalValue = jjg_tph_total.innerText == '-' ? 0 : parseFloat(jjg_tph_total.innerText);

                // Calculate the bt_tph_total value
                const btTphTotalValue = (btTotalValue / tphSampleTotalValue).toFixed(2);
                const jjgVal = (jjgTotalValue / tphSampleTotalValue).toFixed(2);




                // Update the bt_tph_total cell
                document.getElementById("cell-25").innerText = btTphTotalValue;
                document.getElementById("totals_tphjjg").innerText = jjgVal;

                const skorBrdTinggalValue = skor_brd_tinggal(btTphTotalValue);
                const skorBuahTinggalValue = skor_buah_tinggal(jjgVal);

                document.getElementById("skor_brd_tinggal").innerText = skorBrdTinggalValue;
                document.getElementById("skor_buah_tinggal").innerText = skorBuahTinggalValue;

                // Calculate the total value for trans_tod and update the cell
                const transTodTotal = skorBrdTinggalValue + skorBuahTinggalValue;
                document.getElementById("trans_tod").innerText = transTodTotal;

                const ancak = document.getElementById("ancak");

                const buah = document.getElementById("buah");

                // Get the values from the cells and parse them as numbers
                let ancakValue = parseFloat(ancak.innerText);

                let buahValue = parseFloat(buah.innerText);

                // Calculate the total for all the values
                const totalAll = ancakValue + transTodTotal + buahValue;

                // Update the total_Skorings cell
                document.getElementById("total_skoring").innerText = totalAll;
                const skorKategoriAkhirResult = skor_kategori_akhir(totalAll);

                // Update the bg_skoring cell
                document.getElementById("bg_skoring").innerText = skorKategoriAkhirResult.text;
                document.getElementById("bg_skoring").style.backgroundColor = skorKategoriAkhirResult.color;


            }


            // Call the function after the table is rendered
            updateBtTphTotal();

        } else {
            // Do something else when reg is not equal to 2
        }




        function exportToExcel() {
            var table = document.getElementById("headshot");

            TableToExcel.convert(document.getElementById("headshot"), {
                name: "Rekap BA QC Panen Reguler Reg {{$data['est']}} - {{$data['afd']}} - {{$data['tanggal']}}.xlsx",
                sheet: {
                    name: "Rekap BA QC Panen Reguler Reg  {{$data['est']}} - {{$data['afd']}}  - {{$data['tanggal']}}"
                }
            });

            // Close the window after exporting
            setTimeout(function() {
                window.close();
            }, 500);
        }

        // Call exportToExcel() when the page loads with a delay
        // setTimeout(function() {
        //     exportToExcel();
        // }, 500);
    </script>

</body>

</html>