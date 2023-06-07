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
</style>

<body>
    @php
    $hasData = false; // Variable to track if there is any data to display
    @endphp
    @foreach($data['WeekReport1'] as $key => $item)

    <div class="content-wrapper">
        <div class="d-flex justify-content-center custom-border">
            <h2 class="text-center">REKAPITULASI PEMERIKSAAN SIDAK MUTU BUAH DI TPH, JALAN & BIN</h2>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                    <div>
                        <img src="{{ asset('img/Logo-SSS.png') }}" style="height:60px">
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
                            <div class="afd" style="font-size: 20px;">Wilayah: {{$key}} </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-sm text-center">
            <thead>
                <tr>
                    <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Estate</th>
                    <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">AFD</th>
                    <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Nama Asisten</th>
                    <th colspan="11" style="background-color: #ffc404; color: white;" class="text-center">MUTU BUAH</th>
                </tr>
                <tr>
                    <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Total Janjang Sample</th>
                    <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Jumlah Blok Sample</th>
                    <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Mentah</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Matang</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Lewat Matang (O)</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Janjang Kosong (E)</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Tidak Standar Vcut</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Abnormal</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Rat Damage</th>
                    <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Penggunaan Karung Brondolan</th>
                </tr>
                <tr>
                    <th style="background-color: #ffc404; color: white;" class="text-center">Tanpa Brondol</th>
                    <th style="background-color: #ffc404; color: white;" class="text-center">Kurang Brondol</th>

                </tr>
                <tr>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                    <th style="background-color: #ffc404; color: white;">Jjg</th>
                </tr>

            </thead>
            <tbody id="data_tahunTab">

                @foreach($item as $key2 => $item2)
                @if(is_array($item2))
                @foreach($item2 as $key3 => $item3)
                @if(is_array($item3))
                <tr>
                    <td>{{ $item2['afd'] }}</td>
                    <td>{{ $item3['afd'] }}</td>
                    <td>{{ $item3['nama_asisten'] ?? '-' }}</td>
                    <td>{{ $item3['Jumlah_janjang'] }}</td>
                    <td>{{ $item3['blok'] }}</td>
                    <td>{{ $item3['tnp_brd'] }}</td>
                    <td>{{ $item3['krg_brd'] }}</td>
                    <td>{{ $item3['jjg_matang'] }}</td>
                    <td>{{ $item3['lewat_matang'] }}</td>
                    <td>{{ $item3['janjang_kosong'] }}</td>
                    <td>{{ $item3['vcut'] }}</td>
                    <td>{{ $item3['abnormal'] }}</td>
                    <td>{{ $item3['rat_dmg'] }}</td>
                    <td>{{ $item3['karung'] }} / {{ $item3['blok'] }} </td>
                </tr>
                @php
                $hasData = true; // Set the flag to true when data is found
                @endphp
                @endif
                @endforeach
                @if ($hasData) <!-- Display the total row only if there is data -->
                @if (($item2['nama_asistenEM'] ?? null) || ($item2['Jumlah_janjang']?? null) || ($item2['blok']?? null) || ($item2['tnp_brd']?? null) || ($item2['krg_brd']?? null) || ($item2['jjg_matang']?? null) || ($item2['lewat_matang']?? null) || ($item2['janjang_kosong']?? null) || ($item2['vcut']?? null) || ($item2['abnormal']?? null) || ($item2['rat_dmg']?? null) ||( $item2['karung']?? null) || ($item2['blok']?? null))
                <tr>
                    <td style="background-color: #1e7898; color: black;" colspan="2">Total</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['nama_asistenEM'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['Jumlah_janjang'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['blok'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['tnp_brd'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['krg_brd'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['jjg_matang'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['lewat_matang'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['janjang_kosong'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['vcut'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['abnormal'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['rat_dmg'] ?? '-' }}</td>
                    <td style="background-color: #1e7898; color: black;">{{ $item2['karung'] ?? '-' }} / {{ $item2['blok'] ?? '-' }}</td>
                </tr>
                @endif
                @endif
                @endif
                @endforeach
                <tr>
                    <td style="background-color: #c3ae2d; color: black;" colspan="2">{{ $item['est'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['nama_asistenWil'] ?? '-'}}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['Jumlah_janjang'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['blok'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['tnp_brd'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['krg_brd'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['jjg_matang'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['lewat_matang'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['janjang_kosong'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['vcut'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['abnormal'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['rat_dmg'] }}</td>
                    <td style="background-color: #c3ae2d; color: black;">{{ $item['karung'] }} / {{ $item['blok'] }}</td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3 mb-2 px-3 border border-dark" style="max-width: 600px; margin: 0 auto;">
            <table class="table table-bordered table-sm text-center">
                <thead>
                    <tr>
                        <th colspan="3" style="background-color: #903c0c; color: white;" class="text-center">HIGHEST FINDING RECORD</th>
                    </tr>
                    <tr>
                        <th style="background-color: #903c0c; color: white;" class="text-center">KATEGORI</th>
                        <th style="background-color: #903c0c; color: white;" class="text-center">Afdeling</th>
                        <th style="background-color: #903c0c; color: white;" class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $desiredKeys = [
                        'Highest_tnp',
                        'Highest_krg',
                        'Highest_masak',
                        'Highest_lwtmtang',
                        'Highest_jjgkosng',
                        'Highest_vcut',
                        'Highest_karung',
                        'Highest_abnormal',
                        'Highest_rat_dmg',
                    ];
                    ?>
                    @foreach ($desiredKeys as $key)
                    @if (array_key_exists($key, $item))
                    <tr>
                        <td>{{$key}}</td>
                        <td>
                            @foreach ($item[$key] as $value)
                            {{$value}}
                            @endforeach
                        </td>
                        <td>
                            {{$item['value_'.$key]}}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($hasData) <!-- Add a condition to include the page break only if there is data -->
        <div style="page-break-after: always;"></div>
        @endif
    </div>

    @endforeach

    @foreach($data['blokReport'] as $key => $item)

    <div class="d-flex justify-content-center custom-border">
        <h2 class="text-center">REKAPITULASI PEMERIKSAAN PERBLOK SIDAK MUTU BUAH DI TPH, JALAN & BIN</h2>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                <div>
                    <img src="{{ asset('img/Logo-SSS.png') }}" style="height:60px">
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
                        <div class="afd" style="font-size: 20px;">Estate : {{$key}}: </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <table class="table table-bordered table-sm text-center">
        <thead>
            <tr>
                <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Afd</th>
                <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Blok</th>
                <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">STATUS'
                    (H+..)</th>
                <th colspan="11" style="background-color: #ffc404; color: white;" class="text-center">MUTU BUAH</th>
            </tr>
            <tr>
                <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Total Janjang Sample</th>
                <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Jumlah Blok Sample</th>
                <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Mentah</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Matang</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Lewat Matang (O)</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Janjang Kosong (E)</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Tidak Standar Vcut</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Abnormal</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Rat Damage</th>
                <th rowspan="2" style="background-color: #ffc404; color: white;" class="text-center">Penggunaan Karung Brondolan</th>
            </tr>
            <tr>
                <th style="background-color: #ffc404; color: white;" class="text-center">Tanpa Brondol</th>
                <th style="background-color: #ffc404; color: white;" class="text-center">Kurang Brondol</th>

            </tr>
            <tr>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
                <th style="background-color: #ffc404; color: white;">Jjg</th>
            </tr>

        </thead>
        <tbody>
            @foreach($item as $key2 => $item2)

            @foreach($item2 as $key3 => $item3)
            <tr>
                <td>{{$key2}}</td>
                <td>{{$key3}}</td>
            </tr>

            @endforeach
            <tr>
                <td colspan="2">Total</td>
            </tr>
            @endforeach
    </table>


    <table>
        <thead>
            <tr>
                <th colspan="4">Foto Temuan</th>
            </tr>
        </thead>
        <tbody>
            @php
            $counter = 0;
            @endphp

            @foreach($data['temuan'] as $tm => $itemx)
            @if($tm == $key)
            @foreach($itemx as $tm2 =>$itemx2)
            @if($tm2 === $key2)
            @foreach($itemx2 as $tm3 => $itemx3)
            @if($counter % 4 === 0)
            <tr>
                @endif
                <td>
                    <img style="margin: 0 5px 0 5px;" width="300" height="300" src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/{{$itemx3['foto_temuan']}}" alt="Image 1">
                    <p>{{$itemx3['komentar']}} - {{$itemx3['estate']}} -{{$itemx3['afd']}}</p>
                </td>
                @php
                $counter++;
                @endphp
                @if($counter % 4 === 0)
            </tr>
            @endif

            @endforeach
            @endif
            @endforeach
            @endif
            @endforeach

            @if($counter % 4 !== 0)
            </tr>
            @endif
        </tbody>
    </table>


    @if ($hasData) <!-- Add a condition to include the page break only if there is data -->
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach

</body>

</html>