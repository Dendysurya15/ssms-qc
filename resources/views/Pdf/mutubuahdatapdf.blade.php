<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('table-to-excel-master/dist/tableToExcel.js') }}"></script>
</head>

<body>
    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
        <div class="table-wrapper">
            <table class="my-table" id="headshot" data-cols-width="10,10,10,20,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,20">
                <thead>
                    <tr>
                        <th data-fill-color="76C5E8" rowspan="4" class="sticky" data-a-h="center" data-a-v="middle" style="background-color: #883c0c;">No</th>

                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Est.</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Afd.</th>
                        <th data-a-wrap="true" data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Nama Staff</th>
                        <th data-a-h="center" data-a-v="middle" colspan="27" class="sticky" data-a-h="center" data-a-v="middle" data-fill-color="ffc404" style="background-color: #ffc404;">Mutu Buah</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="4" class="sticky" style="background-color: #a8a4a4;" rowspan="2">AlL Skor.</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="4" class="sticky" style="background-color: #a8a4a4;" rowspan="2">Katagori</th>
                    </tr>
                    <tr>
                        <th data-a-wrap="true" data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" rowspan="3" class="sticky-sub" style="background-color: #ffc404; white-space: nowrap;">Total Janjang Sample</th>

                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="7" class="sticky-sub" style="background-color: #ffc404;">Mentah</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Matang</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Lewat Matang (O)</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Janjang Kosong (E)</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;"> Tidak Standar Vcut</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="2" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Abnormal</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="2" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Rat Damage</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Penggunaan Karung Brondolan</th>
                    </tr>
                    <tr>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="2" class="sticky-second-row" style="background-color: #ffc404;">Tanpa Brondol</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="2" class="sticky-second-row" style="background-color: #ffc404;">Kurang Brondol</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" colspan="3" class="sticky-second-row" style="background-color: #ffc404;">Total</th>
                    </tr>

                    <tr>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">TPH</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">%</th>
                        <th data-fill-color="76C5E8" data-a-h="center" data-a-v="middle" class="sticky-third-row" style="background-color: #ffc404;">Skor</th>
                    </tr>
                </thead>
                <tbody id="data_weekTab2">
                    @php
                    $inc = 1;
                    @endphp

                    @foreach ($data['mutu_buah'] as $item)
                    @foreach ($item as $items)
                    @foreach ($items as $items1)

                    @php

                    if($items1['afd'] === 'EST'){
                    $color = '76C5E8';
                    }else if ($items1['afd'] === 'Reg'){
                    $color = 'FF7043';
                    }else if ($items1['afd'] === 'WIL'){
                    $color = 'B8AE5B';
                    }
                    else{
                    $color = 'EBEBEB';
                    };



                    if ($items1['kategori'] === 'EXCELLENT') {
                    $color2 = '5074c4';
                    } elseif($items1['kategori'] === 'GOOD') {
                    $color2 = '08fc2c';
                    } elseif ($items1['kategori'] === 'SATISFACTORY') {
                    $color2 = 'ffdc04';
                    } elseif ($items1['kategori'] === 'FAIR') {
                    $color2 = 'ffa404';
                    } else {
                    $color2 = 'ff0404';
                    }

                    @endphp
                    <tr>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$inc++}}</td>
                        <td data-a-wrap="true" data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['est']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['afd'] ?? ' ' }}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-a-wrap="true">{{$items1['nama_asisten']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['Jumlah_janjang']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['tnp_brd']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persenTNP_brd']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['krg_brd']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persenKRG_brd']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['total_jjg']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persen_totalJjg']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['skor_total']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['jjg_matang']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persen_jjgMtang']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['skor_jjgMatang']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['lewat_matang']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persen_lwtMtng']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['skor_lewatMTng']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['janjang_kosong']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persen_kosong']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['skor_kosong']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['vcut']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['vcut_persen']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['vcut_skor']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['abnormal']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['abnormal_persen']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['rat_dmg']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['rd_persen']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['TPH']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['persen_krg']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['skor_kr']}}</td>
                        <td data-fill-color="{{$color}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle">{{$items1['All_skor']}}</td>
                        <td data-fill-color="{{$color2}}" data-b-a-s="thin" data-a-h="center" data-a-v="middle" data-a-wrap="true">{{$items1['kategori']}}</td>
                    </tr>
                    @endforeach
                    @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <button onclick="exportToExcel()">Export to Excel</button>


    <script>
        // Define the exportToExcel function
        function exportToExcel() {
            var table = document.getElementById("headshot");

            TableToExcel.convert(document.getElementById("headshot"), {
                name: "Rekap Data Sidak Mutu Buah Reg {{$data['reg']}} / {{$data['tanggal']}}.xlsx",
                sheet: {
                    name: "Rekap Data Sidak Mutu Buah  Reg  {{$data['reg']}} /{{$data['tanggal']}}"
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