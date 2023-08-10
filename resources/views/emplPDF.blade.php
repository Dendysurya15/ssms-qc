<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="container">

        <div style="display: flex; justify-content: center; margin-top: 3px; margin-bottom: 2px; margin-left: 3px; margin-right: 3px; border: 1px solid black; background-color: #fff4cc">
            <h2 style="text-align: center;">PEMERIKSAAN PERUMAHAN</h2>
        </div>
        @foreach ($data['total'] as $key => $item)
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                    <div>
                        <img src="{{ asset('img/Logo-SSS.png') }}" style="height:90px;margin-top : 10px;margin-left: 10px">
                    </div>
                </td>
                <td style="width:30%;border:0;">

                    <p style="text-align: left; font-size: 20px;">PT. SAWIT SUMBERMAS SARANA,TBK</p>
                    <p style="text-align: left;">QUALITY CONTROL</p>

                </td>
                <td style=" width: 60%;border:0;">
                </td>
                <td style="vertical-align: middle; text-align: right;width:20%;border:0;">
                    <div class="right-container">
                        <div class="text-container" style="border:1px solid black">

                            <div style="font-size: 20px;border:1px solid black">ESTATE/ AFD: {{$item['est']}} / {{$item['afd']}} </div>
                            <div style="font-size: 20px;border:1px solid black">TANGGAL: {{$item['date']}} </div>
                            <div style="font-size: 20px;border:1px solid black">DIPERIKSA OLEH: {{$item['petugas']}}</div>

                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border: 1px solid black">
            <tr>
                <th colspan="12" style="text-align: center; background-color: #fff4cc">FOTO</th>
            </tr>
            <tbody>
                @php
                $baseURL = 'https://mobilepro.srs-ssms.com/storage/app/public/qc/';
                $foto_temuan = $item['foto_temuan'];
                $komentar_temuan = $item['komentar_temuan'];
                @endphp

                @for ($i = 0; $i < count($foto_temuan); $i++) @php $foto_info=explode('-', $foto_temuan[$i]); $foto_name=$foto_info[0]; $foto_type=$foto_info[1]; $foto_url=$baseURL . ($foto_type==='rmh' ? 'perumahan/' : ($foto_type==='lcp' ? 'landscape/' : 'lingkungan/' )) . $foto_name; $komentar=preg_replace('/-(rmh|lkn|lcp)$/', '' , $komentar_temuan[$i]); // Remove the suffix @endphp @if ($i % 4===0) <tr>
                    @endif

                    <td class="align-middle" width="25%" style="position: relative; border:1px solid black">
                        <img src="{{ $foto_url }}" style="width: 400pt; height: 250pt; object-fit: contain;">
                        <p style="text-align: center; font-size: 20px; font-weight: bold; border-bottom: 1px solid black; border-top: 1px solid black">{{$item['est']}} - {{$item['afd']}}</p>
                        <p style="text-align: center; font-size: 20px; font-weight: bold;">{{ $komentar }}</p>
                    </td>

                    @if ($i % 4 === 3 || $i === count($foto_temuan) - 1)
                    </tr>
                    @endif
                    @endfor
            </tbody>
        </table>

        @if (!$loop->last) <!-- Check if it's not the last iteration -->
        <div style="page-break-before: always;"></div>
        @endif
        @endforeach





    </div>
</body>

</html>