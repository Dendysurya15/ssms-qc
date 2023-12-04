<body>
    @foreach ($dataSkor as $key3 => $item3)
    @php
    // Mutu Transport Wilayah
    $bt_total_wil = 0;
    $tph_total_wil = 0;
    $bt_tph_total_wil = 0;
    $jjg_total_wil = 0;
    $jjg_tph_total_wil = 0;

    // Mutu Buah Wilayah
    $blok_mb = 0;
    $alas_mb = 0;
    $tot_jjg_wil = 0;
    $tot_mentah_wil = 0;
    $tot_matang_wil = 0;
    $tot_over_wil = 0;
    $tot_empty_wil = 0;
    $tot_vcut_wil = 0;
    $tot_abr_wil = 0;
    $tot_krg_wil = 0;
    $tot_Permentah_wil = 0;
    $tot_Permatang_wil = 0;
    $tot_Perover_wil = 0;
    $tot_Perjangkos_wil = 0;
    $tot_Pervcut_wil = 0;
    $tot_Perabr_wil = 0;
    $tot_Perkrg_wil = 0;

    // Mutu Ancak Wilayah
    $jml_pokok_sm_wil = 0;
    $luas_ha_wil = 0;
    $jml_jjg_panen_wil = 0;
    $jml_brtp_wil = 0;
    $jml_brtk_wil = 0;
    $jml_brtgl_wil = 0;
    $jml_bhts_wil = 0;
    $jml_bhtm1_wil = 0;
    $jml_bhtm2_wil = 0;
    $jml_bhtm3_wil = 0;
    $jml_ps_wil = 0;
    $btr_jjg_ma_wil = 0;
    $jjg_tgl_ma_wil = 0;
    @endphp
    @foreach ($dataSkor[$key3] as $key => $item)
    @if (is_array($item))
    @foreach ($item as $key2 => $value)
    @if (is_array($value))
    @php
    $bt_total_wil += check_array('bt_total', $value);
    $tph_total_wil += check_array('tph_sample', $value);
    $jjg_total_wil += check_array('restan_total', $value);

    $blok_mb += check_array('blok_mb', $value);
    $alas_mb += check_array('alas_mb', $value);
    $tot_jjg_wil += check_array('jml_janjang', $value);
    $tot_mentah_wil += check_array('jml_mentah', $value);
    $tot_matang_wil += check_array('jml_masak', $value);
    $tot_over_wil += check_array('jml_over', $value);
    $tot_empty_wil += check_array('jml_empty', $value);
    $tot_abr_wil += check_array('jml_abnormal', $value);
    $tot_vcut_wil += check_array('jml_vcut', $value);

    $jml_pokok_sm_wil += check_array('jml_pokok_sampel', $value);
    $luas_ha_wil += check_array('luas_ha', $value);
    $jml_jjg_panen_wil += check_array('jml_jjg_panen', $value);
    $jml_brtp_wil += check_array('p_ma', $value);
    $jml_brtk_wil += check_array('k_ma', $value);
    $jml_brtgl_wil += check_array('gl_ma', $value);
    $jml_bhts_wil += check_array('bhts_ma', $value);
    $jml_bhtm1_wil += check_array('bhtm1_ma', $value);
    $jml_bhtm2_wil += check_array('bhtm2_ma', $value);
    $jml_bhtm3_wil += check_array('bhtm3_ma', $value);
    $jml_ps_wil += check_array('ps_ma', $value);

    $totalSkorAkhir = skor_brd_ma(check_array('btr_jjg_ma', $value)) + skor_buah_Ma(check_array('jjg_tgl_ma', $value)) +
    skor_palepah_ma(check_array('PerPSMA', $value)) + skor_brd_tinggal(check_array('skor', $value)) +
    skor_buah_tinggal(check_array('skor_restan', $value)) + skor_buah_mentah_mb(check_array('PersenBuahMentah', $value)) +
    skor_buah_masak_mb(check_array('PersenBuahMasak', $value))
    + skor_buah_over_mb(check_array('PersenBuahOver', $value)) +
    skor_jangkos_mb(check_array('PersenPerJanjang', $value)) +
    skor_buah_over_mb(check_array('PersenVcut', $value)) +
    skor_abr_mb(check_array('PersenKrgBrd', $value));
    $skor_kategori_akhir = skor_kategori_akhir($totalSkorAkhir);
    @endphp
    <tr>
        {{-- Bagian Mutu Transport --}}
        <td>{{$key}}</td>
        <!-- <td>{{$key2}}</td> -->
        <!-- <td> <a href="dataDetails/{{$key}}/{$key2}}" target="_blank"> {{$key2}}</a></td> -->
        <td> <a href="dataDetail/{{$key}}/{{$key2}}/{{$tanggal}}/{{$regional}}"> {{$key2}}</a></td>


        <td>{{check_array('jml_pokok_sampel', $value)}}</td>
        <td>{{check_array('luas_ha', $value)}}</td>
        <td>{{check_array('jml_jjg_panen', $value)}}</td>
        <td>{{check_array('akp_real', $value)}}</td>
        <td>{{check_array('p_ma', $value)}}</td>
        <td>{{check_array('k_ma', $value)}}</td>
        <td>{{check_array('gl_ma', $value)}}</td>
        <td>{{check_array('total_brd_ma', $value)}}</td>
        <td>{{round(check_array('btr_jjg_ma', $value),2)}}</td>
        <td>{{skor_brd_ma(check_array('btr_jjg_ma', $value))}}</td>
        <td>{{check_array('bhts_ma', $value)}}</td>
        <td>{{check_array('bhtm1_ma', $value)}}</td>
        <td>{{check_array('bhtm2_ma', $value)}}</td>
        <td>{{check_array('bhtm3_ma', $value)}}</td>
        <td>{{check_array('tot_jjg_ma', $value)}}</td>
        <td>{{round (check_array('jjg_tgl_ma', $value),2)}}</td>
        <td>{{skor_buah_Ma(check_array('jjg_tgl_ma', $value))}}</td>
        <td>{{check_array('ps_ma', $value)}}</td>
        <td>{{round(check_array('PerPSMA', $value),2)}}</td>
        <td>{{skor_palepah_ma(check_array('PerPSMA', $value))}}</td>
        <td>{{skor_brd_ma(check_array('btr_jjg_ma', $value)) + skor_buah_Ma(check_array('jjg_tgl_ma', $value)) + skor_palepah_ma(check_array('PerPSMA', $value))}}</td>

        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $key)
        @foreach ($value1 as $keys2 => $value2)
        @if($keys2 == $key2)
        <td>{{$value2['tph_sample']}}</td>
        <td>{{$value2['bt_total']}}</td>
        <td>{{round ($value2['skor'],2)}}</td>
        <td>{{skor_brd_tinggal($value2['skor'])}}</td>
        <td>{{$value2['restan_total']}}</td>
        <td>{{skor ($value2['skor_restan'],2)}}</td>
        <td>{{skor_buah_tinggal($value2['skor_restan'])}}</td>
        <td>{{skor_buah_tinggal($value2['skor_restan']) + skor_brd_tinggal($value2['skor'])}}</td>

        <td>{{$value2['blok_mb'] ?? 0}}</td>
        <td>{{$value2['jml_janjang'] ?? 0}}</td>
        <td>{{$value2['jml_mentah']?? 0}}</td>
        <td>{{round ($value2['PersenBuahMentah']?? 0,2)}}</td>
        <td>{{skor_buah_mentah_mb($value2['PersenBuahMentah']?? 0 )}}</td>

        <td>{{$value2['jml_masak']?? 0}}</td>
        <td>{{round($value2['PersenBuahMasak']?? 0,2)}}</td>
        <td>{{skor_buah_masak_mb($value2['PersenBuahMasak']?? 0)}}</td>

        <td>{{$value2['jml_over']?? 0}}</td>
        <td>{{round($value2['PersenBuahOver']?? 0,2)}}</td>
        <td>{{skor_buah_over_mb($value2['PersenBuahOver']?? 0)}}</td>

        <td>{{$value2['jml_empty']?? 0}}</td>
        <td>{{round ($value2['PersenPerJanjang']?? 0,2)}}</td>
        <td>{{skor_jangkos_mb($value2['PersenPerJanjang']?? 0)}}</td>

        <td>{{$value2['jml_vcut']?? 0}}</td>
        <td>{{round ($value2['PersenVcut']?? 0,2)}}</td>
        <td>{{skor_buah_over_mb($value2['PersenVcut']?? 0)}}</td>

        <td>{{$value2['jml_abnormal']?? 0}}</td>
        <td>{{$value2['PersenAbr']?? 0}}</td>

        <td>{{$value2['alas_mb']?? 0}} / {{$value2['blok_mb']?? 0}}</td>
        <td>{{round ($value2['PersenKrgBrd']?? 0,2)}}</td>
        <td>{{skor_abr_mb($value2['PersenKrgBrd']?? 0)}}</td>

        <td>{{
            skor_buah_mentah_mb($value2['PersenBuahMentah']?? 0) +
            skor_buah_masak_mb($value2['PersenBuahMasak']?? 0) +
            skor_buah_over_mb($value2['PersenBuahOver']?? 0) +
            skor_jangkos_mb($value2['PersenPerJanjang']?? 0) +
            skor_buah_over_mb($value2['PersenVcut']?? 0) +
            skor_abr_mb($value2['PersenKrgBrd']?? 0) 
        }}</td>


        @endif
        @endforeach
        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td>{{check_array('tph_sample', $value)}}</td>
        <td>{{check_array('bt_total', $value)}}</td>
        <td>{{ round(check_array('skor', $value),2)}}</td>
        <td>{{skor_brd_tinggal(check_array('skor', $value))}}</td>
        <td>{{check_array('restan_total', $value)}}</td>
        <td>{{round (check_array('skor_restan', $value),2)}}</td>
        <td>{{skor_buah_tinggal(check_array('skor_restan', $value))}}</td>
        <td>{{ skor_brd_tinggal(check_array('skor', $value)) + skor_buah_tinggal(check_array('skor_restan', $value)) }}</td>

        {{-- Bagian Mutu Buah - Buah Mentah --}}
        <td>{{check_array('blok_mb', $value)}}</td>
        <td>{{check_array('jml_janjang', $value)}}</td>
        <td>{{check_array('jml_mentah', $value)}}</td>
        <td>{{round (check_array('PersenBuahMentah', $value),2)}}</td>
        <td>{{skor_buah_mentah_mb(check_array('PersenBuahMentah', $value))}}</td>
        {{-- Bagian Mutu Buah - Buah Matang --}}
        <td>{{check_array('jml_masak', $value)}}</td>
        <td>{{round (check_array('PersenBuahMasak', $value),2)}}</td>
        <td>{{skor_buah_masak_mb(check_array('PersenBuahMasak', $value))}}</td>
        {{-- Bagian Mutu Buah - Lewat Matang --}}
        <td>{{check_array('jml_over', $value)}}</td>
        <td>{{round (check_array('PersenBuahOver', $value),2)}}</td>
        <td>{{skor_buah_over_mb(check_array('PersenBuahOver', $value))}}</td>
        {{-- Bagian Mutu Buah - Jangkos --}}
        <td>{{check_array('jml_empty', $value)}}</td>
        <td>{{round(check_array('PersenPerJanjang', $value),2)}}</td>
        <td>{{skor_jangkos_mb(check_array('PersenPerJanjang', $value))}}</td>
        {{-- Bagian Mutu Buah - Tidak Standar V-Cut --}}
        <td>{{check_array('jml_vcut', $value)}}</td>
        <td>{{round (check_array('PersenVcut', $value),2)}}</td>
        <td>{{skor_buah_over_mb(check_array('PersenVcut', $value))}}</td>
        {{-- Bagian Mutu Buah - Abnormal --}}
        <td>{{check_array('jml_abnormal', $value)}}</td>
        <td>{{check_array('PersenAbr', $value)}}</td>
        {{-- Bagian Mutu Buah - Karung Brondolan --}}
        <td>{{check_array('alas_mb', $value)}}/{{check_array('blok_mb', $value)}}</td>
        <td>{{check_array('PersenKrgBrd', $value)}}</td>
        <td>{{skor_abr_mb(check_array('PersenKrgBrd', $value))}}</td>
        <td>{{skor_buah_mentah_mb(check_array('PersenBuahMentah', $value)) +
            skor_buah_masak_mb(check_array('PersenBuahMasak', $value))
            + skor_buah_over_mb(check_array('PersenBuahOver', $value)) +
            skor_jangkos_mb(check_array('PersenPerJanjang', $value)) +
            skor_buah_over_mb(check_array('PersenVcut', $value)) +
            skor_abr_mb(check_array('PersenKrgBrd', $value))}}</td>
        @endif




        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $key)
        @foreach ($value1 as $keys2 => $value2)
        @if($keys2 == $key2)

        <td bgcolor="{{ $skor_kategori_akhir[0] }}">
            {{skor_brd_ma($value2['btr_jjg_ma']?? 0) + skor_buah_Ma($value2['jjg_tgl_ma']?? 0) +  skor_palepah_ma($value2['PerPSMA']?? 0) 
            +
          skor_buah_tinggal($value2['skor_restan']?? 0) + skor_brd_tinggal($value2['skor']?? 0) 
            +
           skor_buah_mentah_mb($value2['PersenBuahMentah']?? 0) +
            skor_buah_masak_mb($value2['PersenBuahMasak']?? 0) +
            skor_buah_over_mb($value2['PersenBuahOver']?? 0) +
            skor_jangkos_mb($value2['PersenPerJanjang']?? 0) +
            skor_buah_over_mb($value2['PersenVcut']?? 0) +
            skor_abr_mb($value2['PersenKrgBrd']?? 0) }}


        </td>
        @endif
        @endforeach
        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td bgcolor="{{ $skor_kategori_akhir[0] }}">{{ round ($totalSkorAkhir,2) }}</td>
        @endif

        <td bgcolor="{{ $skor_kategori_akhir[0] }}">{{ $skor_kategori_akhir[1] }}</td>
    </tr>
    @endif
    @endforeach
    @php
    $totalSkorAkhirEst = skor_brd_ma(check_array('btr_jjg_ma_est', $item)) +
    skor_buah_Ma(check_array('jjg_tgl_ma_est', $item)) +
    skor_palepah_ma(check_array('PerPSMA_est', $item)) +
    skor_brd_tinggal(check_array('bt_tph_total', $item))
    +skor_buah_tinggal(check_array('jjg_tph_total', $item))+
    skor_buah_mentah_mb(check_array('tot_PersenBuahMentah', $item)) +
    skor_buah_masak_mb(check_array('tot_PersenBuahMasak', $item))
    + skor_buah_over_mb(check_array('tot_PersenBuahOver', $item)) +
    skor_jangkos_mb(check_array('tot_PersenPerJanjang', $item)) +
    skor_buah_over_mb(check_array('tot_PersenVcut', $item)) +
    skor_abr_mb(check_array('tot_PersenKrgBrd', $item));
    $skor_kategori_akhir_est = skor_kategori_akhir($totalSkorAkhirEst);
    @endphp
    <tr>
        <td style="background-color : #b0d48c; color: #000000;" colspan="2">{{$key}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_jml_pokok_ma', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_luas_ha_ma', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_jml_jjg_panen_ma', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('akp_real_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('p_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('k_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('gl_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('total_brd_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{ round(check_array('btr_jjg_ma_est', $item),2)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{skor_brd_ma(check_array('btr_jjg_ma_est', $item))}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bhts_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bhtm1_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bhtm2_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('bhtm3_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_jjg_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{ round (check_array('jjg_tgl_ma_est', $item),2)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{skor_buah_Ma(check_array('jjg_tgl_ma_est', $item))}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{check_array('ps_ma_est', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{ round(check_array('PerPSMA_est', $item),2)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{skor_palepah_ma(check_array('PerPSMA_est', $item))}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{skor_brd_ma(check_array('btr_jjg_ma_est', $item)) + skor_buah_Ma(check_array('jjg_tgl_ma_est', $item)) + skor_palepah_ma(check_array('PerPSMA_est', $item))}}</td>

        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $key)

        <td style="background-color : #b0d48c; color: #000000;">{{$value1['tph_tod']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['total_bt']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['bt_tph']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['scorre_bt']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['total_rst']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['rst_tph']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['scorre_rst']}}</td>
        <td style="background-color : #b0d48c; color: #000000;">{{$value1['scorre_rst'] + $value1['scorre_bt'] }} </td>


        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tph_sample_total', $item)}}
        </td>
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('bt_total', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('bt_tph_total', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_brd_tinggal(check_array('bt_tph_total', $item)) }}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('jjg_total', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{round (check_array('jjg_tph_total', $item),2)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_tinggal(check_array('jjg_tph_total', $item)) }}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_brd_tinggal(check_array('bt_tph_total',
            $item))+skor_buah_tinggal(check_array('jjg_tph_total', $item))
            }}
        </td>
        @endif








        <td style="background-color : #b0d48c; color: #000000;">{{check_array('tot_blok', $item)}}</td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_jjg', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_mentah', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{round(check_array('tot_PersenBuahMentah', $item),1)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_mentah_mb(check_array('tot_PersenBuahMentah', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_matang', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenBuahMasak', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_masak_mb(check_array('tot_PersenBuahMasak', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_over', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenBuahOver', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_over_mb(check_array('tot_PersenBuahOver', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_empty', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenPerJanjang', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_jangkos_mb(check_array('tot_PersenPerJanjang', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_vcut', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenVcut', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_over_mb(check_array('tot_PersenVcut', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_abr', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenAbr', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_alas', $item)}}/{{check_array('tot_blok', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{check_array('tot_PersenKrgBrd', $item)}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_abr_mb(check_array('tot_PersenKrgBrd', $item))}}
        </td>
        <td style="background-color : #b0d48c; color: #000000;">
            {{skor_buah_mentah_mb(check_array('tot_PersenBuahMentah', $item)) +
            skor_buah_masak_mb(check_array('tot_PersenBuahMasak', $item))
            + skor_buah_over_mb(check_array('tot_PersenBuahOver', $item)) +
            skor_jangkos_mb(check_array('tot_PersenPerJanjang', $item)) +
            skor_buah_over_mb(check_array('tot_PersenVcut', $item)) +
            skor_abr_mb(check_array('tot_PersenKrgBrd', $item))}}
        </td>

        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $key)

        <td style="background-color : {{$skor_kategori_akhir_est[0]}}; color: #000000;">
            {{skor_brd_ma(check_array('btr_jjg_ma_est', $item)) + skor_buah_Ma(check_array('jjg_tgl_ma_est', $item)) + skor_palepah_ma(check_array('PerPSMA_est', $item)) +
                skor_buah_mentah_mb(check_array('tot_PersenBuahMentah', $item)) +
            skor_buah_masak_mb(check_array('tot_PersenBuahMasak', $item))
            + skor_buah_over_mb(check_array('tot_PersenBuahOver', $item)) +
            skor_jangkos_mb(check_array('tot_PersenPerJanjang', $item)) +
            skor_buah_over_mb(check_array('tot_PersenVcut', $item)) +
            skor_abr_mb(check_array('tot_PersenKrgBrd', $item))
            + $value1['scorre_rst'] + $value1['scorre_bt'] 
            }}

        </td>


        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td style="background-color : {{$skor_kategori_akhir_est[0]}}; color: #000000;">{{$totalSkorAkhirEst}}</td>
        @endif

        <td style="background-color : {{$skor_kategori_akhir_est[0]}}; color: #000000;">{{$skor_kategori_akhir_est[1]}}</td>
    </tr>
    @php
    $bt_tph_total_wil = $tph_total_wil == 0 ? $bt_total_wil : round($bt_total_wil / $tph_total_wil, 2);
    $jjg_tph_total_wil = $tph_total_wil == 0 ? $jjg_total_wil : round($jjg_total_wil / $tph_total_wil, 2);
    $tot_krg_wil = $alas_mb == 0 ? $blok_mb : round($blok_mb / $alas_mb, 2);
    $tot_Permentah_wil = ($tot_jjg_wil - $tot_abr_wil) == 0 ? $tot_mentah_wil : round(($tot_mentah_wil / ($tot_jjg_wil - $tot_abr_wil)) * 100, 2);
    $tot_Permatang_wil = ($tot_jjg_wil - $tot_abr_wil) == 0 ? $tot_matang_wil : round(($tot_matang_wil / ($tot_jjg_wil - $tot_abr_wil)) * 100, 2);
    $tot_Perover_wil = ($tot_jjg_wil - $tot_abr_wil) == 0 ? $tot_over_wil : round(($tot_over_wil / ($tot_jjg_wil - $tot_abr_wil)) * 100, 2);
    $tot_Perjangkos_wil = ($tot_jjg_wil - $tot_abr_wil) == 0 ? $tot_empty_wil : round(($tot_empty_wil / ($tot_jjg_wil - $tot_abr_wil)) * 100, 2);
    $tot_Pervcut_wil = count_percent($tot_vcut_wil, $tot_jjg_wil);
    $tot_Perabr_wil = count_percent($tot_abr_wil, $tot_jjg_wil);
    $tot_Perkrg_wil = count_percent($alas_mb, $blok_mb);

    $akp_real_wil = count_percent($jml_jjg_panen_wil, $jml_pokok_sm_wil);
    $tot_brd_wil = $jml_brtp_wil + $jml_brtk_wil + $jml_brtgl_wil;
    $btr_jjg_ma_wil = $jml_jjg_panen_wil == 0 ? $tot_brd_wil : round(($tot_brd_wil / $jml_jjg_panen_wil), 2);
    $tot_bt_wil = $jml_bhts_wil + $jml_bhtm1_wil + $jml_bhtm2_wil + $jml_bhtm3_wil;
    $bt_jjg_ma_wil = ($jml_jjg_panen_wil + $tot_bt_wil) == 0 ? $tot_bt_wil : round(($tot_bt_wil / ($jml_jjg_panen_wil + $tot_bt_wil)) * 100, 2);
    $PerPSMA_wil = count_percent($jml_ps_wil, $jml_pokok_sm_wil);
    @endphp
    @endif
    @endforeach
    @php
    $totalSkorAkhirWil = skor_brd_ma($btr_jjg_ma_wil) +
    skor_buah_Ma($bt_jjg_ma_wil) +
    skor_palepah_ma($PerPSMA_wil) +
    skor_brd_tinggal($bt_tph_total_wil)+
    skor_buah_tinggal($jjg_tph_total_wil)+
    skor_buah_mentah_mb($tot_Permentah_wil) +
    skor_buah_masak_mb($tot_Permatang_wil) +
    skor_buah_over_mb($tot_Perover_wil) +
    skor_jangkos_mb($tot_Perjangkos_wil) +
    skor_buah_over_mb($tot_Pervcut_wil) + skor_abr_mb($tot_Perkrg_wil);
    $skor_kategori_akhir_wil = skor_kategori_akhir($totalSkorAkhirWil);
    @endphp
    <tr>
        <td style="background-color : yellow; color: #000000;" colspan="2">
            WIL-{{$key3}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_pokok_sm_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $luas_ha_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_jjg_panen_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $akp_real_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_brtp_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_brtk_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_brtgl_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $tot_brd_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $btr_jjg_ma_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_brd_ma($btr_jjg_ma_wil)}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_bhts_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_bhtm1_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_bhtm2_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_bhtm3_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $tot_bt_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $bt_jjg_ma_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_buah_Ma($bt_jjg_ma_wil)}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $jml_ps_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $PerPSMA_wil }}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_palepah_ma($PerPSMA_wil)}}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_brd_ma($btr_jjg_ma_wil) + skor_buah_Ma($bt_jjg_ma_wil) + skor_palepah_ma($PerPSMA_wil)}}</td>

        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)

        <td style="background-color : yellow; color: #000000;">{{$value['tph_tod']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['total_bt']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['bt_tph']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['scorre_bt']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['total_rst']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['rst_tph']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['score_rst']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$value['score_rst'] + $value['scorre_bt'] }} </td>
        @endif
        @endforeach
        @else
        <td style="background-color : yellow; color: #000000;">{{ $tph_total_wil }}
        </td>
        <td style="background-color : yellow; color: #000000;">{{ $bt_total_wil }}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $bt_tph_total_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            skor_brd_tinggal($bt_tph_total_wil) }}
        </td>
        <td style="background-color : yellow; color: #000000;">{{ $jjg_total_wil }}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $jjg_tph_total_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            skor_buah_tinggal($jjg_tph_total_wil) }}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            skor_brd_tinggal($bt_tph_total_wil)+skor_buah_tinggal($jjg_tph_total_wil)}}
        </td>
        @endif





        <td style="background-color : yellow; color: #000000;">{{$blok_mb}}</td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_jjg_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_mentah_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Permentah_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_buah_mentah_mb($tot_Permentah_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_matang_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Permatang_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_buah_masak_mb($tot_Permatang_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_over_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Perover_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_buah_over_mb($tot_Perover_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_empty_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Perjangkos_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_jangkos_mb($tot_Perjangkos_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_vcut_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Pervcut_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_buah_over_mb($tot_Pervcut_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_abr_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Perabr_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{$alas_mb}}/{{$blok_mb}}</td>
        <td style="background-color : yellow; color: #000000;">{{
            $tot_Perkrg_wil}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_abr_mb($tot_Perkrg_wil)}}
        </td>
        <td style="background-color : yellow; color: #000000;">
            {{skor_buah_mentah_mb($tot_Permentah_wil) +
            skor_buah_masak_mb($tot_Permatang_wil) +
            skor_buah_over_mb($tot_Perover_wil) +
            skor_jangkos_mb($tot_Perjangkos_wil) +
            skor_buah_over_mb($tot_Pervcut_wil) + skor_abr_mb($tot_Perkrg_wil)}}
        </td>
        @if($regional == 2 || $regional == '2' )
        @foreach ($tph_trans as $keys => $value)
        @if($key3 == $keys)
        <td style="background-color : {{ $skor_kategori_akhir_wil[0] }}; color: #000000;">{{skor_buah_mentah_mb($tot_Permentah_wil) +
            skor_buah_masak_mb($tot_Permatang_wil) +
            skor_buah_over_mb($tot_Perover_wil) +
            skor_jangkos_mb($tot_Perjangkos_wil) +
            skor_buah_over_mb($tot_Pervcut_wil) + skor_abr_mb($tot_Perkrg_wil)+ $value['score_rst'] + $value['scorre_bt'] + skor_brd_ma($btr_jjg_ma_wil) + skor_buah_Ma($bt_jjg_ma_wil) + skor_palepah_ma($PerPSMA_wil)

        }}
        </td>
        @endif
        @endforeach
        @else
        <td style="background-color : {{ $skor_kategori_akhir_wil[0] }}; color: #000000;">{{ $totalSkorAkhirWil }}</td>
        @endif

        <td style="background-color : {{ $skor_kategori_akhir_wil[0] }}; color: #000000;">{{ $skor_kategori_akhir_wil[1] }}</td>
    </tr>
    @if ($key3 === array_key_last($dataSkor))
    @else
    <tr style="border: none;">
        <td colspan="32" style="background-color : #fff;">&nbsp;</td>
    </tr>
    @endif
    @endforeach



    <tr style="border: none;">
        <td colspan="32" style="background-color : #fff;">&nbsp;</td>
    </tr>

    @foreach ($dataSkor_ancak as $key => $itemx)
    @foreach ($itemx as $key2 => $plasma)
    @foreach ($plasma as $key3 => $item2)
    @if ($key3 == "WIL-I" || $key3 == "WIL-II" || $key3 == "WIL-III" || $key3 == "WIL-IV" || $key3 == "WIL-V" || $key3 == "WIL-VI" || $key3 == "WIL-VII" )
    @php
    $skor_kategori_akhirx = skor_kategori_akhir(check_array('allSkor', $item2));
    $total_skorAncak = $item2['Skor_brd'] + $item2['Skor_buah'] + $item2['Skor_ps'];
    @endphp
    <tr>
        <td>{{ $key2 }}</td>
        <!-- <td>{{ $key3 }}</td> -->
        <td> <a href="dataDetail/{{$key2}}/{{$key3}}/{{$tanggal}}/{{$regional}}"> {{$key3}}</a></td>
        <td>{{ $item2['jml_pokok_sampel'] }}</td>
        <td>{{ $item2['luas_ha'] }}</td>
        <td>{{ $item2['jml_jjg_panen'] }}</td>
        <td>{{ $item2['akp_real'] }}</td>
        <td>{{ $item2['p_ma'] }}</td>
        <td>{{ $item2['k_ma'] }}</td>
        <td>{{ $item2['gl_ma'] }}</td>
        <td>{{ $item2['total_brd_ma'] }}</td>
        <td>{{ $item2['btr_jjg_ma'] }}</td>
        <td>{{ $item2['Skor_brd'] }}</td>
        <td>{{ $item2['bhts_ma'] }}</td>
        <td>{{ $item2['bhtm1_ma'] }}</td>
        <td>{{ $item2['bhtm2_ma'] }}</td>
        <td>{{ $item2['bhtm3_ma'] }}</td>
        <td>{{ $item2['tot_jjg_ma'] }}</td>
        <td>{{ $item2['jjg_tgl_ma'] }}</td>
        <td>{{ $item2['Skor_buah'] }}</td>
        <td>{{ $item2['ps_ma'] }}</td>
        <td>{{ $item2['PerPSMA'] }}</td>
        <td>{{ $item2['Skor_ps'] }}</td>
        <td>{{ $total_skorAncak }}</td>

        @foreach($dataSkor_transport as $trans => $itemx)
        @foreach($itemx as $trans1 => $itemx2)
        @foreach($itemx2 as $trans2 => $itemx3)
        @if ($trans2 == $key3)
        @php

        $total_skortrans = $itemx3['Skor_bt'] + $itemx3['Skor_tph'];
        @endphp
        @if($regional == 2 || $regional == '2' )
        @foreach ($plasma_tph as $keys => $value)
        @if($trans == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $trans1)
        @foreach ($value1 as $keys2 => $value2)
        @if($keys2 == $trans2)
        <td id="trans_tph">{{$value2['tph_sample']}}</td>
        @endif
        @endforeach
        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td>{{ $itemx3['tph_sample'] }}</td>
        @endif


        <td>{{ $itemx3['bt_total'] }}</td>
        <td>{{ $itemx3['bt_tph'] }}</td>
        <td>{{ $itemx3['Skor_bt'] }}</td>

        <td>{{ $itemx3['restan_total'] }}</td>
        <td>{{ $itemx3['restan_tph'] }}</td>
        <td>{{ $itemx3['Skor_tph'] }}</td>
        <td>{{ $total_skortrans}}</td>

        @endif
        @endforeach
        @endforeach
        @endforeach

        @foreach($dataSkor_buah as $buah => $item_buah)
        @foreach($item_buah as $buah1 => $item_buah1)
        @foreach($item_buah1 as $buah2 => $item_buah2)
        @if ($buah2 == $key3)
        @php
        $total_skor_mutu_buahx = skor_buah_mentah_mb($item_buah2['PersenBuahMentah']) + skor_buah_masak_mb($item_buah2['PersenBuahMasak']) + skor_buah_over_mb($item_buah2['PersenBuahOver']) + skor_jangkos_mb($item_buah2['PersenPerJanjang']) + skor_vcut_mb($item_buah2['PersenVcut']) + skor_abr_mb($item_buah2['PersenKrgBrd']);
        $grand_total_skor = $total_skortrans + $total_skor_mutu_buahx + $total_skorAncak;
        $grand_total_skor_kategori = skor_kategori_akhir($grand_total_skor);
        @endphp


        <td>{{ $item_buah2['blok_mb'] }}</td>
        <td>{{ $item_buah2['jml_janjang'] }}</td>
        <td>{{ $item_buah2['jml_mentah'] }}</td>
        <td>{{ $item_buah2['PersenBuahMentah'] }}</td>
        <td> {{skor_buah_mentah_mb($item_buah2['PersenBuahMentah'])}}</td>
        <td>{{ $item_buah2['jml_masak'] }}</td>
        <td>{{ $item_buah2['PersenBuahMasak'] }}</td>
        <td> {{skor_buah_masak_mb($item_buah2['PersenBuahMasak'])}}</td>
        <td>{{ $item_buah2['jml_over'] }}</td>
        <td>{{ $item_buah2['PersenBuahOver'] }}</td>
        <td> {{skor_buah_over_mb($item_buah2['PersenBuahOver'])}}</td>
        <td>{{ $item_buah2['jml_empty'] }}</td>
        <td>{{ $item_buah2['PersenPerJanjang'] }}</td>
        <td> {{skor_jangkos_mb($item_buah2['PersenPerJanjang'])}}</td>
        <td>{{ $item_buah2['jml_vcut'] }}</td>
        <td>{{ $item_buah2['PersenVcut'] }}</td>
        <td> {{skor_vcut_mb($item_buah2['PersenVcut'])}}</td>
        <td>{{ $item_buah2['jml_abnormal'] }}</td>
        <td>{{ $item_buah2['PersenAbr'] }}</td>
        <td>{{ $item_buah2['alas_mb'] }}/{{ $item_buah2['blok_mb'] }}</td>
        <td>{{ $item_buah2['PersenKrgBrd'] }}</td>
        <td> {{skor_abr_mb($item_buah2['PersenKrgBrd'])}}</td>
        <td>{{ $total_skor_mutu_buahx }}</td>
        <td style="background-color: {{ $grand_total_skor_kategori[0] }};">{{ $grand_total_skor }}</td>
        <td style="background-color: {{ $skor_kategori_akhirx[0] }};">{{ $skor_kategori_akhirx[1] }}</td>
        @endif
        @endforeach
        @endforeach
        @endforeach
    </tr>
    @endif
    @endforeach
    @endforeach
    @endforeach


    @if($regional == 2 || $regional == '2' )

    <!-- plasma est / will  -->
    @foreach ($dataSkor_ancak as $key => $itemx)
    @foreach ($itemx as $key2 => $plasma)
    @php
    $skor_kategori_akhirm = skor_kategori_akhir(check_array('allSkor_est', $plasma));
    $skor_totalancak = skor_brd_ma($plasma['btr_jjg_ma_est']) + skor_buah_Ma($plasma['jjg_tgl_ma_est']) + $plasma['Skor_ps_est'];
    @endphp
    <tr>
        <td style="background-color : yellow; color: #000000;" colspan="2">
            {{$key2}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jml_pokok_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_luas_ha_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jml_jjg_panen_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['akp_real_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['p_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['k_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['gl_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['total_brd_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['btr_jjg_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_brd_ma($plasma['btr_jjg_ma_est'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhts_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm1_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm2_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm3_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jjg_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['jjg_tgl_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_buah_Ma($plasma['jjg_tgl_ma_est'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['ps_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['PerPSMA_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['Skor_ps_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$skor_totalancak}}</td>

        @foreach($dataSkor_transport as $trans => $itemx)
        @foreach($itemx as $trans1 => $itemx2)

        @if ($trans1 == $key2)
        @php
        $skor_totaltrans = skor_brd_tinggal($itemx2['bt_tph_total']) + skor_buah_tinggal($itemx2['jjg_tph_total']) ;
        @endphp

        @if($regional == 2 || $regional == '2' )
        @foreach ($plasma_tph as $keys => $value)
        @if($trans == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $trans1)

        <td style="background-color : yellow; color: #000000;">{{ $value1['tph_tod'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['total_bt'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['bt_tph'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_bt'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['total_rst'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['rst_tph'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_rst'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_rst'] + $value1['scorre_bt']  }}</td>

        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['tph_sample_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['bt_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['bt_tph_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ skor_brd_tinggal($itemx2['bt_tph_total']) }}</td>

        <td style="background-color : yellow; color: #000000;">{{ $itemx2['jjg_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['jjg_tph_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ skor_buah_tinggal($itemx2['jjg_tph_total']) }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skor_totaltrans }}</td>

        @endif





        @endif

        @endforeach
        @endforeach

        @foreach($dataSkor_buah as $buah => $item_buah)
        @foreach($item_buah as $buah1 => $item_buah1)

        @if ($buah1 == $key2)
        @php
        $total_skor_mutu_buah = skor_buah_mentah_mb($item_buah1['tot_PersenBuahMentah']) + skor_buah_masak_mb($item_buah1['tot_PersenBuahMasak']) + skor_buah_over_mb($item_buah1['tot_PersenBuahOver']) + skor_jangkos_mb($item_buah1['tot_PersenPerJanjang']) + skor_vcut_mb($item_buah1['tot_PersenVcut']) + skor_abr_mb($item_buah1['tot_PersenKrgBrd']);
        $grand_total_skor = $skor_totalancak + $skor_totaltrans + $total_skor_mutu_buah;
        $grand_total_skor_kategori = skor_kategori_akhir($grand_total_skor);
        @endphp


        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_blok'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_jjg'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_mentah'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahMentah'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_mentah_mb($item_buah1['tot_PersenBuahMentah'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_matang'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahMasak'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_masak_mb($item_buah1['tot_PersenBuahMasak'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_over'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahOver'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_over_mb($item_buah1['tot_PersenBuahOver'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_empty'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenPerJanjang'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_jangkos_mb($item_buah1['tot_PersenPerJanjang'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_vcut'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenVcut'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_vcut_mb($item_buah1['tot_PersenVcut'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_abr'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenAbr'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_alas'] }}/{{ $item_buah1['tot_blok'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenKrgBrd'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_abr_mb($item_buah1['tot_PersenKrgBrd'])}}</td>
        <td style="background-color : yellow; color: #000000;"> {{$total_skor_mutu_buah}}</td>




        @if($regional == 2 || $regional == '2' )
        @foreach ($plasma_tph as $keys => $value)
        @if($trans == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $trans1)

        <td style="background-color: {{ $grand_total_skor_kategori[0] }};">{{
            skor_buah_mentah_mb($item_buah1['tot_PersenBuahMentah']) + skor_buah_masak_mb($item_buah1['tot_PersenBuahMasak']) + skor_buah_over_mb($item_buah1['tot_PersenBuahOver']) + skor_jangkos_mb($item_buah1['tot_PersenPerJanjang']) + skor_vcut_mb($item_buah1['tot_PersenVcut']) + skor_abr_mb($item_buah1['tot_PersenKrgBrd'])
            +
            skor_brd_ma($plasma['btr_jjg_ma_est']) + skor_buah_Ma($plasma['jjg_tgl_ma_est']) + $plasma['Skor_ps_est']
            +
            $value1['scorre_rst'] + $value1['scorre_bt'] 
        }}</td>

        @endif
        @endforeach
        @endif
        @endforeach

        @else


        @endif

        <td style="background-color: {{ $skor_kategori_akhirx[0] }};">{{ $skor_kategori_akhirx[1] }}</td>
        @endif
        @endforeach
        @endforeach

    </tr>
    @endforeach
    @endforeach

    @else

    <!-- plasma est / will  -->
    @foreach ($dataSkor_ancak as $key => $itemx)
    @foreach ($itemx as $key2 => $plasma)
    @php
    $skor_kategori_akhirm = skor_kategori_akhir(check_array('allSkor_est', $plasma));
    $skor_totalancak = skor_brd_ma($plasma['btr_jjg_ma_est']) + skor_buah_Ma($plasma['jjg_tgl_ma_est']) + $plasma['Skor_ps_est'];
    @endphp
    <tr>
        <td style="background-color : yellow; color: #000000;" colspan="2">
            {{$key2}}
        </td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jml_pokok_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_luas_ha_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jml_jjg_panen_ma']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['akp_real_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['p_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['k_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['gl_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['total_brd_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['btr_jjg_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_brd_ma($plasma['btr_jjg_ma_est'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhts_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm1_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm2_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['bhtm3_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['tot_jjg_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['jjg_tgl_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{skor_buah_Ma($plasma['jjg_tgl_ma_est'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['ps_ma_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['PerPSMA_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$plasma['Skor_ps_est']}}</td>
        <td style="background-color : yellow; color: #000000;">{{$skor_totalancak}}</td>

        @foreach($dataSkor_transport as $trans => $itemx)
        @foreach($itemx as $trans1 => $itemx2)

        @if ($trans1 == $key2)
        @php
        $skor_totaltrans = skor_brd_tinggal($itemx2['bt_tph_total']) + skor_buah_tinggal($itemx2['jjg_tph_total']) ;
        @endphp

        @if($regional == 2 || $regional == '2' )
        @foreach ($plasma_tph as $keys => $value)
        @if($trans == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $trans1)

        <td style="background-color : yellow; color: #000000;">{{ $value1['tph_tod'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['total_bt'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['bt_tph'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_bt'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['total_rst'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['rst_tph'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_rst'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $value1['scorre_rst'] + $value1['scorre_bt']  }}</td>

        @endif
        @endforeach
        @endif
        @endforeach
        @else
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['tph_sample_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['bt_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['bt_tph_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ skor_brd_tinggal($itemx2['bt_tph_total']) }}</td>

        <td style="background-color : yellow; color: #000000;">{{ $itemx2['jjg_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $itemx2['jjg_tph_total'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ skor_buah_tinggal($itemx2['jjg_tph_total']) }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $skor_totaltrans }}</td>

        @endif





        @endif

        @endforeach
        @endforeach

        @foreach($dataSkor_buah as $buah => $item_buah)
        @foreach($item_buah as $buah1 => $item_buah1)

        @if ($buah1 == $key2)
        @php
        $total_skor_mutu_buah = skor_buah_mentah_mb($item_buah1['tot_PersenBuahMentah']) + skor_buah_masak_mb($item_buah1['tot_PersenBuahMasak']) + skor_buah_over_mb($item_buah1['tot_PersenBuahOver']) + skor_jangkos_mb($item_buah1['tot_PersenPerJanjang']) + skor_vcut_mb($item_buah1['tot_PersenVcut']) + skor_abr_mb($item_buah1['tot_PersenKrgBrd']);
        $grand_total_skor = $skor_totalancak + $skor_totaltrans + $total_skor_mutu_buah;
        $grand_total_skor_kategori = skor_kategori_akhir($grand_total_skor);
        @endphp


        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_blok'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_jjg'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_mentah'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahMentah'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_mentah_mb($item_buah1['tot_PersenBuahMentah'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_matang'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahMasak'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_masak_mb($item_buah1['tot_PersenBuahMasak'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_over'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenBuahOver'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_buah_over_mb($item_buah1['tot_PersenBuahOver'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_empty'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenPerJanjang'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_jangkos_mb($item_buah1['tot_PersenPerJanjang'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_vcut'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenVcut'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_vcut_mb($item_buah1['tot_PersenVcut'])}}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_abr'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenAbr'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_alas'] }}/{{ $item_buah1['tot_blok'] }}</td>
        <td style="background-color : yellow; color: #000000;">{{ $item_buah1['tot_PersenKrgBrd'] }}</td>
        <td style="background-color : yellow; color: #000000;"> {{skor_abr_mb($item_buah1['tot_PersenKrgBrd'])}}</td>
        <td style="background-color : yellow; color: #000000;"> {{$total_skor_mutu_buah}}</td>
        <td style="background-color: {{ $grand_total_skor_kategori[0] }};"> {{$grand_total_skor}}</td>
        <td style="background-color: {{ $skor_kategori_akhirx[0] }};">{{ $skor_kategori_akhirx[1] }}</td>



        @if($regional == 2 || $regional == '2' )
        @foreach ($plasma_tph as $keys => $value)
        @if($trans == $keys)
        @foreach ($value as $keys1 => $value1)
        @if($keys1 == $trans1)



        @endif
        @endforeach
        @endif
        @endforeach

        @else


        @endif


        @endif
        @endforeach
        @endforeach

    </tr>
    @endforeach
    @endforeach

    @endif
</body>