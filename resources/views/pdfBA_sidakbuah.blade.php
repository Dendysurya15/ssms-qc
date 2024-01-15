<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
        font-size: 16px;
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

    .table-center {
        margin-left: auto;
        margin-right: auto;
    }


    .custom-table,
    .custom-table th,
    .custom-table td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }

    .my-custom-table {
        margin-right: 100px;
        margin-left: 10px;
    }


    .table-1-no-border td {
        border: none;
    }

    .hide-row {
        visibility: collapse;
    }

    .signature-cell {
        vertical-align: bottom;
        text-align: center;
        border: 1px solid black;
    }
</style>

<body>

    <!-- ganti sessuai kebutuhan landsacpe/potrait -->

    <!-- potrait -->
    <!-- <div class="content-wrapper" style="border: 1px solid #000; padding: 50px; min-height: calc(594mm - 100px); width: 420mm; margin: 0 auto;"> -->
    <!-- --- -->
    <!-- landscape -->
    <div class="content-wrapper" style="border: 1px solid #000; padding: 30px;">
        <!-- -- -->

        <style>
            .custom-border {
                border: 1px solid #000;
                padding: 20px;
                margin-top: 50px;
                margin-bottom: 50px;
            }
        </style>

        <div class="d-flex justify-content-center custom-border">
            <h2 class="text-center">REKAPITULASI SIDAK PEMERIKSAAN MUTU BUAH

            </h2>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                    <div>
                        <img src="{{ asset('img/logo-SSS.png') }}" style="height:60px">
                    </div>
                </td>
                <td style="width:30%;border:0;">

                    <p style="text-align: left;">PT. SAWIT SUMBERMAS SARANA,TBK</p>
                    <p style="text-align: left;">QUALITY CONTROL</p>

                </td>
                <td style=" width: 20%;border:0;">
                </td>
                <td style="vertical-align: middle; text-align: right;width:40%;border:0;">
                    <div class="right-container">
                        <div class="text-container">

                            <div class="afd">ESTATE : {{$data['est']}} </div>
                            <div class="afd">TANGGAL: {{$data['tanggal']}}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">

        </div>

        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <div class="Wraping">
                <table class="my-table">
                    <thead>
                        <tr>
                            <th colspan="25">Mutu BUAH (MB)</th>
                            <th colspan="7">Keterangan</th>
                        </tr>
                        <tr>
                            <th colspan="2" rowspan="3">Afdeling</th>
                            <th colspan="2" rowspan="3">Blok</th>
                            <th rowspan="3">Total Janjang Sample</th>
                            <th colspan="6">Mentah (A)</th>
                            <th colspan="2" rowspan="2">Matang (N)</th>
                            <th colspan="2" rowspan="2">Lewat Matang (O)</th>
                            <th colspan="2" rowspan="2">Janjang Kosong (E)</th>
                            <th colspan="2" rowspan="2">Abnormal</th>
                            <th colspan="2" rowspan="2">Tidak Standar V-Cut</th>
                            <th colspan="2" rowspan="2">Rat Damage</th>
                            <th colspan="2" rowspan="2" style="border: 1px solid black;">Alas Brondol</th>

                            <!-- <th colspan="12" rowspan="25"></th> -->
                        </tr>
                        <tr>
                            <th colspan="2">0 Brondol</th>
                            <th colspan="2">Kurang Brondol</th>
                            <th colspan="2">Total</th>
                        </tr>
                        <tr>

                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                            <th>Jjg</th>
                            <th>%</th>
                        <tr>

                    </thead>
                    <tbody id="tab2" style="font-size: 13px;border:none">
                        @foreach ($data['sidak_buah'] as $items)
                        @foreach ($items as $item)
                        <tr>
                            @if ($item['afd'] === 'TOTAL')

                            <td colspan="2" style="background-color: #80A29E;">{{$item['estate']}}</td>
                            <td colspan="2" style="background-color: #80A29E;">{{$item['est']}}</td>
                            <td style="background-color: #80A29E;">{{$item['Jumlah_janjang']}}</td>
                            <td style="background-color: #80A29E;">{{$item['tnp_brd']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persenTNP_brd']}}</td>
                            <td style="background-color: #80A29E;">{{$item['krg_brd']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persenKRG_brd']}}</td>
                            <td style="background-color: #80A29E;">{{$item['total_jjg']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persen_totalJjg']}}</td>
                            <td style="background-color: #80A29E;">{{$item['jjg_matang']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persen_jjgMtang']}}</td>
                            <td style="background-color: #80A29E;">{{$item['lewat_matang']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persen_lwtMtng']}}</td>
                            <td style="background-color: #80A29E;">{{$item['janjang_kosong']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persen_kosong']}}</td>
                            <td style="background-color: #80A29E;">{{$item['abnormal']}}</td>
                            <td style="background-color: #80A29E;">{{$item['abnormal_persen']}}</td>
                            <td style="background-color: #80A29E;">{{$item['vcut']}}</td>
                            <td style="background-color: #80A29E;">{{$item['vcut_persen']}}</td>
                            <td style="background-color: #80A29E;">{{$item['rat_dmg']}}</td>
                            <td style="background-color: #80A29E;">{{$item['rd_persen']}}</td>
                            <td style="background-color: #80A29E;">{{$item['karung']}}</td>
                            <td style="background-color: #80A29E;">{{$item['persen_krg']}}</td>
                            @else
                            <td colspan="2">{{$item['estate']}}</td>
                            <td colspan="2">{{$item['est']}}</td>
                            <td>{{$item['Jumlah_janjang']}}</td>
                            <td>{{$item['tnp_brd']}}</td>
                            <td>{{$item['persenTNP_brd']}}</td>
                            <td>{{$item['krg_brd']}}</td>
                            <td>{{$item['persenKRG_brd']}}</td>
                            <td>{{$item['total_jjg']}}</td>
                            <td>{{$item['persen_totalJjg']}}</td>
                            <td>{{$item['jjg_matang']}}</td>
                            <td>{{$item['persen_jjgMtang']}}</td>
                            <td>{{$item['lewat_matang']}}</td>
                            <td>{{$item['persen_lwtMtng']}}</td>
                            <td>{{$item['janjang_kosong']}}</td>
                            <td>{{$item['persen_kosong']}}</td>
                            <td>{{$item['abnormal']}}</td>
                            <td>{{$item['abnormal_persen']}}</td>
                            <td>{{$item['vcut']}}</td>
                            <td>{{$item['vcut_persen']}}</td>
                            <td>{{$item['rat_dmg']}}</td>
                            <td>{{$item['rd_persen']}}</td>
                            <td>{{$item['karung']}}</td>
                            <td>{{$item['persen_krg']}}</td>
                            @endif




                        </tr>

                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 " style="padding: 5px;">
            <!-- Table 1 -->
            <table class="custom-table table-1-no-border" style="float: left; width: 33%;">
                <thead>
                    <tr class="table-1-no-border hide-row">
                        <th colspan="2" class="text-center"></th>
                    </tr>
                </thead>
            </table>
            <!-- Table 2 -->
            <table class="custom-table" style="float: left; width: 40%; border-collapse: collapse;" border="1">
                <thead>
                    <tr>
                        <th colspan="12" class="table-1-no-border hide-row">Demikian hasil pemeriksaan ini dengan sebenar-benarnya tanpa rekayasa dan paksaan dari Siapapun,</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="text-center">Dibuat Oleh</th>
                        <th colspan="3" class="text-center">Diterima Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr></tr>
                    <tr></tr>
                    <tr></tr>
                    <tr>
                        <td colspan="3" style="vertical-align: bottom; padding-top: 244px; text-align:center; border: 1px solid black;"></td>
                        <td colspan="3" style="vertical-align: bottom; text-align:center; border: 1px solid black;"></td>
                        <td colspan="3" style="vertical-align: bottom; text-align:center; border: 1px solid black;"></td>
                        <td colspan="3" style="vertical-align: bottom; text-align:center; border: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <td colspan="9" style="vertical-align: bottom; text-align:center; border: 1px solid black;">Quality Control</td>
                        <td colspan="3" style="vertical-align: bottom; text-align:center; border: 1px solid black;">Estate Manager</td>
                    </tr>
                </tbody>
            </table>
            <!-- Table 3 -->
            <table class="custom-table table-1-no-border" style="float: right; width: 34%;">
                <thead>
                    <tr class="hide-row">
                        <th colspan="2" class="text-center"></th>
                    </tr>
                </thead>
            </table>
            <div style="clear:both;"></div>
        </div>

        <div style="clear:both;"></div>
    </div>


</body>

</html>