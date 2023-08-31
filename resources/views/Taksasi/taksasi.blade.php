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

    .body {
        background-color: blue;
        margin: 0;
        /* Remove default body margin */
        padding: 0;
        /* Remove default body padding */
    }
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

    <table style="width: 100%; border-collapse: collapse;" id="header">
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

                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="ml-3 mr-3 mb-3">
        <div class="row text-center tbl-fixed">

            <table id="headshot" data-cols-width="8,8,10,10,15,10,8,10,8,5,15,8,8">
                <tr>
                    <th data-b-a-s="medium" colspan="13" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" bgcolor="#e0e4f4" style="color: #000000;"> Laporan Taksai Panen</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>

                <tr>
                    <th colspan="2" data-b-a-s="medium">Estate : {{$est}}</th>
                    <th colspan="8"></th>

                    <th colspan="3" data-b-a-s="medium">Tanggal Taksasi : {{$awal}}</th>


                </tr>
                <tr>
                    <th colspan="2" data-b-a-s="medium">WILAYAH : {{$wil}} </th>
                    <th colspan="8"></th>
                    <th colspan="3" data-b-a-s="medium">Tanggal Panen :{{$akhir}} </th>

                </tr>


                <tr>

                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" bgcolor="#e0e4f4" style="color: #000000;">Afdeling.</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="freeze-col align-middle" bgcolor="#e0e4f4" style="color: #000000;">Blok</th>

                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">LUAS (HA)</th>

                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">NO BARIS</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">SPH(Pkk/Ha)</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">BJR(Kg/Jjg)</th>
                    <th data-b-a-s="thin" data-a-wrap="true" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">SAMPLE PATH</th>
                    <th data-b-a-s="thin" data-a-wrap="true" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">POKOK SAMPLE</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">JANJANG</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">AKP</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">TAKSASI (Kg)</th>
                    <th data-b-a-s="thin" data-a-wrap="true" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">KEB. Pemanen(Orang) (Kg)</th>
                    <th data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-fill-color="e0e4f4" class="freeze-col align-middle" class="align-middle" bgcolor="#e0e4f4" style="color: #000000;">Ritase</th>

                    </th>
                </tr>

                </thead>
                <tbody>
                    @php
                    function addToTotal($value, &$total) {
                    if ($value !== '-') {
                    $total += $value;
                    } else {
                    $total = 0;
                    }
                    }


                    $est_luas = 0;
                    $est_sph = 0;
                    $est_bjr = 0;
                    $sample_path_est = 0;
                    $jumlah_pokok_est = 0;
                    $jumlah_janjang_est = 0;
                    $tot_akp_est = 0;
                    $taksasi_est = 0;
                    $pemanen_est = 0;
                    $ritase_est = 0;
                    $est_akp = 0;




                    @endphp
                    @foreach ($collection as $key =>$items)
                    @php
                    $luas_ha_group =0;
                    $sum_sph_group = 0;
                    $sum_bjr_group = 0;
                    $sample_path = 0;
                    $jumlah_pokok = 0;
                    $jumlah_janjang = 0;
                    $akp = 0;
                    $taksasi = 0;
                    $pemanen = 0;
                    $ritase = 0;


                    @endphp
                    @foreach ($items as $key1 => $item)
                    @php
                    addToTotal($item['luas'], $luas_ha_group);
                    addToTotal($item['sph'], $sum_sph_group);
                    addToTotal($item['bjr'], $sum_bjr_group);
                    addToTotal($item['jumlah_path'], $sample_path);
                    addToTotal($item['jumlah_pokok'], $jumlah_pokok);
                    addToTotal($item['jumlah_janjang'], $jumlah_janjang);
                    addToTotal($item['akp'], $akp);
                    addToTotal($item['taksasi'], $taksasi);
                    addToTotal($item['pemanen'], $pemanen);
                    addToTotal($item['ritase'], $ritase);

                    $avg_sph = count($items);


                    $tot_sph =round( $sum_sph_group /$avg_sph,2);
                    $tot_bjr =round( $sum_bjr_group /$avg_sph,2);
                    $tot_akp = ($jumlah_pokok !== 0) ? round(($jumlah_janjang / $jumlah_pokok) * 100, 2) : 0;

                    $tod_taksasi = round(($luas_ha_group * $tot_sph * $tot_bjr * $tot_akp) / 100, 2);
                    $tod_ritase = ($tod_taksasi > 0) ? ceil($tod_taksasi / 6500) : 0;


                    @endphp
                    <tr>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$key}}</td>
                        @if ($item['flags'] >= 1)

                        <td data-b-a-s="thin" data-a-h="center" data-fill-color="f8ec7c" data-a-v="middle">{{$item['blok']}}</td>

                        @else

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['blok']}}</td>
                        @endif

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['luas']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['no_baris']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['sph']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['bjr']}}</td>
                        @if ($item['jumlah_path'] == 1)
                        <td data-b-a-s="thin" data-a-h="center" data-f-color="ff1c14" data-a-v="middle">{{$item['jumlah_path']}}</td>

                        @else
                        <td data-b-a-s="thin" data-a-h="center" data-f-color="080404" data-a-v="middle">{{$item['jumlah_path']}}</td>

                        @endif

                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['jumlah_pokok']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['jumlah_janjang']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['akp']}} %</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['taksasi']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['pemanen']}}</td>
                        <td data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$item['ritase']}}</td>
                    </tr>


                    @endforeach
                    <tr>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" colspan="2" data-a-h="center" data-a-v="middle">Total</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle"> {{$luas_ha_group}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tot_sph}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tot_bjr}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$sample_path}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$jumlah_pokok}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$jumlah_janjang}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tot_akp}} %</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tod_taksasi}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$pemanen}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tod_ritase}}</td>


                    </tr>


                    @php


                    $avg_est = count($total);
                    $est_akp += $akp;
                    $est_luas += $luas_ha_group;
                    $est_sph += $tot_sph;
                    $est_bjr += $tot_bjr;
                    $sample_path_est += $sample_path;
                    $jumlah_pokok_est += $jumlah_pokok;
                    $jumlah_janjang_est += $jumlah_janjang;
                    $tot_akp_est += $tot_akp;
                    $taksasi_est += $taksasi;
                    $pemanen_est += $pemanen;
                    $ritase_est += $ritase;

                    $est_sphh= round($est_sph /$avg_est,2 );

                    $tot_bjr_est =round( $est_bjr /$avg_est,1);
                    $tot_akp_est = ($jumlah_pokok_est !== 0) ? round(($jumlah_janjang_est / $jumlah_pokok_est) * 100, 2) : 0;


                    $tod_ritase_est = ($tod_taksasi > 0) ? ceil($tod_taksasi / 6500) : 0;

                    $tod_taksasi_est = round(($est_luas * $est_sphh * $tot_bjr_est * $tot_akp_est) / 100, 2);
                    @endphp
                    @endforeach

                    <tr>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" colspan="2" data-a-h="center" data-a-v="middle">Estate</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$est_luas}} </td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">-</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ $est_sphh}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{($tot_bjr_est)}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$sample_path_est}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$jumlah_pokok_est}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$jumlah_janjang_est}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tot_akp_est}} %</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$tod_taksasi_est}} </td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$pemanen_est}}</td>
                        <td data-fill-color="e8ecdc" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{ceil($taksasi_est / 6500)}}</td>


                    </tr>

                    <tr>
                        <td></td>

                    </tr>


                    <tr>
                        <td></td>

                    </tr>


                    <tr>
                        <td>* Asumsi 1 rit = 6500 kg</td>
                    </tr>
                    <tr>
                        <td data-cols-width="4" data-fill-color="e8645c"></td>
                        <td>User melakukan taksasi kurang dari 100 m & kurang dari 4 min</td>
                    </tr>
                    <tr>
                        <td data-cols-width="4" data-fill-color="f88c44"></td>
                        <td>User melakukan taksasi kurang dari 4 min</td>
                    </tr>
                    <tr>
                        <td data-cols-width="4" data-fill-color="f8ec7c"></td>
                        <td>User melakukan taksasi kurang dari 100 m</td>
                    </tr>
                </tbody>


            </table>
        </div>
    </div>

    <button id="exportButton" style="display: none;">Export to Excel</button>



    <script>
        function autoExportDownloadAndClose() {
            var exportButton = document.getElementById('exportButton');
            var table = document.getElementById("headshot");

            exportButton.addEventListener('click', function() {
                TableToExcel.convert(table, {
                    name: "Laporan Taksasi Panen Est {{$est}} - {{$awal}}.xlsx",
                    sheet: {
                        name: "Laporan Taksasi Panen Est {{$est}} - {{$awal}}"
                    }
                });

                // Close the window after exporting
                // setTimeout(function() {
                //     window.close();
                // }, 500);
            });

            // Trigger the click event on the export button
            exportButton.click();
        }

        // Call the function when the page loads
        // window.onload = autoExportDownloadAndClose;
    </script>

</body>

</html>