<body>
    @foreach ($dataSkor as $key3 => $item3)
    @php
    $luas_ha_wil = 0;
    $jml_blok_wil = 0;
    $sum_bt_tph_wil = 0;
    $sum_bt_jln_wil = 0;
    $sum_bt_bin_wil = 0;
    $sum_krg_wil = 0;
    $sumBuah_wil = 0;
    $sumRst_wil = 0;
    @endphp
    @foreach ($dataSkor[$key3] as $key => $item)
    @if (is_array($item))
    @foreach ($item as $key2 => $value)
    @if (is_array($value))
    @php
    $luas_ha_wil += check_array('luas_ha', $value);
    $jml_blok_wil += check_array('jml_blok', $value);
    $sum_bt_tph_wil += check_array('bt_tph', $value);
    $sum_bt_jln_wil += check_array('bt_jln', $value);
    $sum_bt_bin_wil += check_array('bt_bin', $value);
    $sum_krg_wil += check_array('sum_krg', $value);
    $sumBuah_wil += check_array('sumBuah', $value);
    $sumRst_wil += check_array('sumRst', $value);
    $skor_kategori_akhir = skor_kategori_akhir(check_array('allSkor', $value));
    @endphp
    <tr>
        <td>{{$key}}</td>
        <!-- <td>oke</td> -->
        <td> <a href="BaSidakTPH/{{$key}}/{{$key2}}/{{$tanggal}}/{{$regional}}"> {{$key2}}</a></td>
        <td>{{check_array('luas_ha', $value)}}</td>
        <td>{{check_array('jml_blok', $value)}}</td>
        <td>{{check_array('bt_tph', $value)}}</td>
        <td>{{check_array('bt_jln', $value)}}</td>
        <td>{{check_array('bt_bin', $value)}}</td>
        <td>{{check_array('tot_bt', $value)}}</td>
        <td>{{check_array('divBt', $value)}}</td>
        <td>{{check_array('skorBt', $value)}}</td>
        <td>{{check_array('sum_krg', $value)}}</td>
        <td>{{check_array('divKrg', $value)}}</td>
        <td>{{check_array('skorKrg', $value)}}</td>
        <td>{{check_array('sumBuah', $value)}}</td>
        <td>{{check_array('divBuah', $value)}}</td>
        <td>{{check_array('skorBuah', $value)}}</td>
        <td>{{check_array('sumRst', $value)}}</td>
        <td>{{check_array('divRst', $value)}}</td>
        <td>{{check_array('skorRst', $value)}}</td>
        <td bgcolor="{{ $skor_kategori_akhir[0] }}">{{check_array('allSkor', $value)}}</td>
        <td bgcolor="{{ $skor_kategori_akhir[0] }}">{{ $skor_kategori_akhir[1] }}</td>
    </tr>
    @endif
    @endforeach
    @php
    $skor_kategori_akhir_est = skor_kategori_akhir(check_array('allSkor_est', $item));
    @endphp
    <tr>
        <td style="background-color : #b0d48c; color: #000000;" colspan="2">{{$key}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('luas_ha_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('jml_blok_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bt_tph_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bt_jln_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bt_bin_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_bt_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('divBt_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('skorBt_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('sum_krg_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('divKrg_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('skorKrg_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('sumBuah_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('divBuah_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('skorBuah_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('sumRst_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('divRst_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('skorRst_est', $item)}}</td>
        <td style="background-color : {{$skor_kategori_akhir_est[0]}}; color: #000000;">{{check_array('allSkor_est', $item)}}</td>
        <td style="background-color : {{$skor_kategori_akhir_est[0]}}; color: #000000;">{{$skor_kategori_akhir_est[1]}}</td>
    </tr>
    @endif
    @endforeach
    @php
    $tot_bt_wil = ($sum_bt_tph_wil + $sum_bt_jln_wil + $sum_bt_bin_wil);
    $divBt_wil = round($tot_bt_wil / $jml_blok_wil, 2);
    $divKrg_wil = round($sum_krg_wil / $jml_blok_wil, 2);
    $divBuah_wil = round($sumBuah_wil / $jml_blok_wil, 2);
    $divRst_wil = round($sumRst_wil / $jml_blok_wil, 2);
    $skorBt_wil = skor_bt_tph($divBt_wil);
    $skorKrg_wil = skor_krg_tph($divKrg_wil);
    $skorBuah_wil = skor_buah_tph($divBuah_wil);
    $skorRst_wil = skor_rst_tph($divRst_wil);
    $totalSkorAkhirWil = $skorBt_wil + $skorKrg_wil + $skorBuah_wil + $skorRst_wil;
    $skor_kategori_akhir_wil = skor_kategori_akhir($totalSkorAkhirWil);
    @endphp
    <tr>
        <td style="background-color : yellow; color: #000000;" colspan="2">
            WIL-{{$key3}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{ $luas_ha_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_blok_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sum_bt_tph_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sum_bt_jln_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sum_bt_bin_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $tot_bt_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $divBt_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skorBt_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sum_krg_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $divKrg_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skorKrg_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sumBuah_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $divBuah_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skorBuah_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $sumRst_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $divRst_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skorRst_wil }}</td>
        <td style="background-color : {{ $skor_kategori_akhir_wil[0] }}; color: #000000;">{{ $totalSkorAkhirWil }}</td>
        <td style="background-color : {{ $skor_kategori_akhir_wil[0] }}; color: #000000;">{{ $skor_kategori_akhir_wil[1] }}</td>
    </tr>


    @if ($key3 === array_key_last($dataSkor))
    @else
    <tr style="border: none;">
        <td colspan="32" style="background-color : #fff;">&nbsp;</td>
    </tr>
    @endif
    @endforeach

    <!-- //plasma  -->
    @foreach ($dataSkorPlasma as $key => $itemx)
    @foreach ($itemx as $key2 => $plasma)
    @foreach ($plasma as $key3 => $item2)
    @if ($key3 == "WIL-I" || $key3 == "WIL-II" || $key3 == "WIL-III")
    @php
    $skor_kategori_akhirx = skor_kategori_akhir(check_array('allSkor', $item2));
    @endphp
    <tr>
        <td>{{ $key2 }}</td>
        <td>{{ $key3 }}</td>
        <td>{{ $item2['luas_ha'] }}</td>
        <td>{{ $item2['jml_blok'] }}</td>
        <td>{{ $item2['bt_tph'] }}</td>
        <td>{{ $item2['bt_jln'] }}</td>
        <td>{{ $item2['bt_bin'] }}</td>
        <td>{{ $item2['tot_bt'] }}</td>
        <td>{{ $item2['divBt'] }}</td>
        <td>{{ $item2['skorBt'] }}</td>
        <td>{{ $item2['sum_krg'] }}</td>
        <td>{{ $item2['divKrg'] }}</td>
        <td>{{ $item2['skorKrg'] }}</td>
        <td>{{ $item2['sumBuah'] }}</td>
        <td>{{ $item2['divBuah'] }}</td>
        <td>{{ $item2['skorBuah'] }}</td>
        <td>{{ $item2['sumRst'] }}</td>
        <td>{{ $item2['divRst'] }}</td>
        <td>{{ $item2['skorRst'] }}</td>
        <td bgcolor="{{ $skor_kategori_akhirx[0] }}">{{ check_array('allSkor', $item2) }}</td>
        <td bgcolor="{{ $skor_kategori_akhirx[0] }}">{{ $skor_kategori_akhirx[1] }}</td>
    </tr>
    @endif
    @endforeach
    @endforeach
    @endforeach
    <!-- plasma est / will  -->
    @foreach ($dataSkorPlasma as $key => $itemx)
    @foreach ($itemx as $key2 => $plasma)
    @php
    $skor_kategori_akhirm = skor_kategori_akhir(check_array('allSkor_est', $plasma));
    @endphp
    <tr>
        <td style="background-color : yellow; color: #000000;" colspan="2">
            {{$key2}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['luas_ha_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['jml_blok_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bt_tph_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bt_jln_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bt_bin_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_bt_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['divBt_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['skorBt_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['sum_krg_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['divKrg_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['skorKrg_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['sumBuah_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['divBuah_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['skorBuah_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['sumRst_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['divRst_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['skorRst_est']}}</td>
        <td bgcolor="{{ $skor_kategori_akhirm[0] }}">{{ check_array('allSkor_est', $plasma) }}</td>
        <td bgcolor="{{ $skor_kategori_akhirm[0] }}">{{ $skor_kategori_akhirm[1] }}</td>

    </tr>
    @endforeach
    @endforeach




</body>