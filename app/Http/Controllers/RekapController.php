<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use DateTime;

require '../app/helpers.php';

class RekapController extends Controller
{
    //

    public function index()
    {

        // $year = 2023;
        // $data = [];
        // $chunkSize = 1000;

        // DB::connection('mysql2')->table('sidak_mutu_buah')
        //     ->select(
        //         "sidak_mutu_buah.*",
        //         DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%M") as bulan'),
        //         DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%Y") as tahun')
        //     )
        //     ->whereYear('datetime', $year)
        //     ->orderBy('datetime', 'asc')
        //     ->chunk($chunkSize, function ($results) use (&$data) {
        //         foreach ($results as $result) {
        //             // Grouping logic here, if needed
        //             $data[] = $result;
        //             // Adjust this according to your grouping requirements
        //         }
        //     });

        // $data = collect($data)->groupBy(['estate', 'afdeling']);
        // $queryEste = json_decode($data, true);




        // dd($queryEste['KNE']['OA']);

        $optionREg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->whereNotIn('reg.id', [5])
            // ->where('wil.regional', 1)
            ->get();


        $optionREg = json_decode($optionREg, true);
        return view('Rekap.index', [
            'option_reg' => $optionREg,
        ]);
    }


    public function olahdata(Request $request)
    {
        $regional = $request->input('reg');
        $bulan = $request->input('bulan');

        // dd($bulan);
        $queryAsisten = DB::connection('mysql2')->table('asisten_qc')
            ->select('asisten_qc.*')
            ->get();
        $queryAsisten = json_decode($queryAsisten, true);
        // dd($value2['datetime'], $endDate);
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $queryEste = json_decode($queryEste, true);

        // dd($queryEste);

        $muaest = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            // ->where('estate.emp', '!=', 1)
            ->whereIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get('est');
        $muaest = json_decode($muaest, true);

        // dd($muaest, $queryEste);

        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);

        $queryMTbuah = DB::connection('mysql2')->table('sidak_mutu_buah')
            ->select(
                "sidak_mutu_buah.*",
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%Y") as tahun')
            )
            // ->whereBetween('sidak_mutu_buah.datetime', [$startDate, $endDate])
            ->where('sidak_mutu_buah.datetime', 'like', '%' . $bulan . '%')

            // ->whereBetween('sidak_mutu_buah.datetime', ['2023-04-03', '2023-04-09'])
            ->get();
        $queryMTbuah = $queryMTbuah->groupBy(['estate', 'afdeling']);
        $queryMTbuah = json_decode($queryMTbuah, true);

        $databulananBuah = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $keytph => $value3) {

                    $databulananBuah[$key][$key2][$keytph] = $value3;
                }
            }
        }

        $defPerbulanWil = array();

        foreach ($queryEste as $key2 => $value2) {
            foreach ($queryAfd as $key3 => $value3) {
                if ($value2['est'] == $value3['est']) {
                    $defPerbulanWil[$value2['est']][$value3['nama']] = 0;
                }
            }
        }


        // dd($defaultmua);
        foreach ($defPerbulanWil as $estateKey => $afdelingArray) {
            foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                    $defPerbulanWil[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                }
            }
        }


        // dd($defaultmua);



        // dd($sidak_buah_mua);

        $sidak_buah = array();
        // dd($defPerbulanWil);

        foreach ($defPerbulanWil as $key => $value) {
            $jjg_samplex = 0;
            $tnpBRDx = 0;
            $krgBRDx = 0;
            $abrx = 0;
            $overripex = 0;
            $emptyx = 0;
            $vcutx = 0;
            $rdx = 0;
            $dataBLokx = 0;
            $sum_krx = 0;
            $csrms = 0;
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    $jjg_sample = 0;
                    $tnpBRD = 0;
                    $krgBRD = 0;
                    $abr = 0;
                    $skor_total = 0;
                    $overripe = 0;
                    $empty = 0;
                    $vcut = 0;
                    $rd = 0;
                    $sum_kr = 0;
                    $allSkor = 0;
                    $combination_counts = array();
                    $newblok = 0;
                    $csfxr = count($value1);
                    foreach ($value1 as $key2 => $value2) {
                        $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $newblok = count($value1);
                        $jjg_sample += $value2['jumlah_jjg'];
                        $tnpBRD += $value2['bmt'];
                        $krgBRD += $value2['bmk'];
                        $abr += $value2['abnormal'];
                        $overripe += $value2['overripe'];
                        $empty += $value2['empty_bunch'];
                        $vcut += $value2['vcut'];
                        $rd += $value2['rd'];
                        $sum_kr += $value2['alas_br'];
                    }
                    // $dataBLok = count($combination_counts);
                    $dataBLok = $newblok;
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }
                    $per_kr = round($total_kr * 100, 2);
                    $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                    $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                    $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                    $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                    $sidak_buah[$key][$key1]['blok'] = $dataBLok;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = $tnpBRD;
                    $sidak_buah[$key][$key1]['krg_brd'] = $krgBRD;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = $skor_total;
                    $sidak_buah[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                    $sidak_buah[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                    $sidak_buah[$key][$key1]['lewat_matang'] = $overripe;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                    $sidak_buah[$key][$key1]['janjang_kosong'] = $empty;
                    $sidak_buah[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                    $sidak_buah[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                    $sidak_buah[$key][$key1]['vcut'] = $vcut;
                    $sidak_buah[$key][$key1]['karung'] = $sum_kr;
                    $sidak_buah[$key][$key1]['vcut_persen'] = $skor_vcut;
                    $sidak_buah[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                    $sidak_buah[$key][$key1]['abnormal'] = $abr;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['rat_dmg'] = $rd;
                    $sidak_buah[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['TPH'] = $total_kr;
                    $sidak_buah[$key][$key1]['persen_krg'] = $per_kr;
                    $sidak_buah[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                    $sidak_buah[$key][$key1]['All_skor'] = $allSkor;
                    $sidak_buah[$key][$key1]['csfxr'] = $csfxr;
                    $sidak_buah[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                    $jjg_samplex += $jjg_sample;
                    $tnpBRDx += $tnpBRD;
                    $krgBRDx += $krgBRD;
                    $abrx += $abr;
                    $overripex += $overripe;
                    $emptyx += $empty;
                    $vcutx += $vcut;

                    $rdx += $rd;

                    $dataBLokx += $newblok;
                    $sum_krx += $sum_kr;
                    $csrms += $csfxr;
                } else {

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = 0;
                    $sidak_buah[$key][$key1]['blok'] = 0;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = 0;
                    $sidak_buah[$key][$key1]['krg_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = 0;
                    $sidak_buah[$key][$key1]['total_jjg'] = 0;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = 0;
                    $sidak_buah[$key][$key1]['skor_total'] = 0;
                    $sidak_buah[$key][$key1]['jjg_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = 0;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = 0;
                    $sidak_buah[$key][$key1]['lewat_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  0;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = 0;
                    $sidak_buah[$key][$key1]['janjang_kosong'] = 0;
                    $sidak_buah[$key][$key1]['persen_kosong'] = 0;
                    $sidak_buah[$key][$key1]['skor_kosong'] = 0;
                    $sidak_buah[$key][$key1]['vcut'] = 0;
                    $sidak_buah[$key][$key1]['karung'] = 0;
                    $sidak_buah[$key][$key1]['vcut_persen'] = 0;
                    $sidak_buah[$key][$key1]['vcut_skor'] = 0;
                    $sidak_buah[$key][$key1]['abnormal'] = 0;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = 0;
                    $sidak_buah[$key][$key1]['rat_dmg'] = 0;
                    $sidak_buah[$key][$key1]['rd_persen'] = 0;
                    $sidak_buah[$key][$key1]['TPH'] = 0;
                    $sidak_buah[$key][$key1]['persen_krg'] = 0;
                    $sidak_buah[$key][$key1]['skor_kr'] = 0;
                    $sidak_buah[$key][$key1]['All_skor'] = 0;
                    $sidak_buah[$key][$key1]['kategori'] = 0;
                    $sidak_buah[$key][$key1]['csfxr'] = 0;
                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                }
            }
            if ($sum_krx != 0) {
                $total_kr = round($sum_krx / $dataBLokx, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

            $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

            $sidak_buah[$key]['jjg_mantah'] = $jjg_mth;
            $sidak_buah[$key]['persen_jjgmentah'] = $skor_jjgMTh;

            if ($jjg_samplex == 0 && $tnpBRDx == 0 &&   $krgBRDx == 0 && $abrx == 0 && $overripex == 0 && $emptyx == 0 &&  $vcutx == 0 &&  $rdx == 0 && $sum_krx == 0) {
                $sidak_buah[$key]['check_arr'] = 'kosong';
                $sidak_buah[$key]['All_skor'] = 0;
            } else {
                $sidak_buah[$key]['check_arr'] = 'ada';
                $sidak_buah[$key]['All_skor'] = $allSkor;
            }

            $sidak_buah[$key]['Jumlah_janjang'] = $jjg_samplex;
            $sidak_buah[$key]['csrms'] = $csrms;
            $sidak_buah[$key]['blok'] = $dataBLokx;
            $sidak_buah[$key]['EM'] = 'EM';
            $sidak_buah[$key]['Nama_assist'] = $nama_em;
            $sidak_buah[$key]['nama_staff'] = '-';
            $sidak_buah[$key]['tnp_brd'] = $tnpBRDx;
            $sidak_buah[$key]['krg_brd'] = $krgBRDx;
            $sidak_buah[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
            $sidak_buah[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


            $sidak_buah[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
            $sidak_buah[$key]['persen_totalJjg'] = $skor_total;
            $sidak_buah[$key]['skor_total'] = sidak_brdTotal($skor_total);
            $sidak_buah[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
            $sidak_buah[$key]['persen_jjgMtang'] = $skor_jjgMSk;
            $sidak_buah[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
            $sidak_buah[$key]['lewat_matang'] = $overripex;
            $sidak_buah[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
            $sidak_buah[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
            $sidak_buah[$key]['janjang_kosong'] = $emptyx;
            $sidak_buah[$key]['persen_kosong'] = $skor_jjgKosong;
            $sidak_buah[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
            $sidak_buah[$key]['vcut'] = $vcutx;
            $sidak_buah[$key]['vcut_persen'] = $skor_vcut;
            $sidak_buah[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
            $sidak_buah[$key]['abnormal'] = $abrx;

            $sidak_buah[$key]['rat_dmg'] = $rdx;

            $sidak_buah[$key]['karung'] = $sum_krx;
            $sidak_buah[$key]['TPH'] = $total_kr;
            $sidak_buah[$key]['persen_krg'] = $per_kr;
            $sidak_buah[$key]['skor_kr'] = sidak_PengBRD($per_kr);
            // $sidak_buah[$key]['All_skor'] = $allSkor;
            $sidak_buah[$key]['kategori'] = sidak_akhir($allSkor);
        }


        $mutu_buah = array();
        foreach ($queryEste as $key => $value) {
            foreach ($sidak_buah as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mutu_buah[$value['wil']][$key2] = array_merge($mutu_buah[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }



        // dd($mutu_buah);



        // sidaktph 

        $ancakFA = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select(
                "sidak_tph.*",
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal'),
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'),
                DB::raw("
                CASE 
                WHEN status = '' THEN 1
                WHEN status = '0' THEN 1
                WHEN LOCATE('>H+', status) > 0 THEN '8'
                WHEN LOCATE('H+', status) > 0 THEN 
                    CASE 
                        WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1) > 8 THEN '8'
                        ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1)
                    END
                WHEN status REGEXP '^[0-9]+$' AND status > 8 THEN '8'
                WHEN LENGTH(status) > 1 AND status NOT LIKE '%H+%' AND status NOT LIKE '%>H+%' AND LOCATE(',', status) > 0 THEN SUBSTRING_INDEX(status, ',', 1)
                ELSE status
            END AS statuspanen")
            ) // Change the format to "%Y-%m-%d"
            ->where('sidak_tph.datetime', 'like', '%' . $bulan . '%')
            ->orderBy('status', 'asc')
            ->get();

        $ancakFA = $ancakFA->groupBy(['est', 'afd', 'statuspanen', 'tanggal', 'blok']);
        $ancakFA = json_decode($ancakFA, true);

        $dateString = $bulan;
        $dateParts = date_parse($dateString);
        $year = $dateParts['year'];
        $month = $dateParts['month'];

        $year = $year;
        $month = $month;

        if ($regional == 3) {

            $weeks = [];
            $firstDayOfMonth = strtotime("$year-$month-01");
            $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

            $weekNumber = 1;

            // Find the first Saturday of the month or the last Saturday of the previous month
            $firstSaturday = strtotime("last Saturday", $firstDayOfMonth);

            // Set the start date to the first Saturday
            $startDate = $firstSaturday;

            while ($startDate <= $lastDayOfMonth) {
                $endDate = strtotime("next Friday", $startDate);
                if ($endDate > $lastDayOfMonth) {
                    $endDate = $lastDayOfMonth;
                }

                $weeks[$weekNumber] = [
                    'start' => date('Y-m-d', $startDate),
                    'end' => date('Y-m-d', $endDate),
                ];

                // Update start date to the next Saturday
                $startDate = strtotime("next Saturday", $endDate);

                $weekNumber++;
            }
        } else {
            $weeks = [];
            $firstDayOfMonth = strtotime("$year-$month-01");
            $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

            $weekNumber = 1;
            $startDate = $firstDayOfMonth;

            while ($startDate <= $lastDayOfMonth) {
                $endDate = strtotime("next Sunday", $startDate);
                if ($endDate > $lastDayOfMonth) {
                    $endDate = $lastDayOfMonth;
                }

                $weeks[$weekNumber] = [
                    'start' => date('Y-m-d', $startDate),
                    'end' => date('Y-m-d', $endDate),
                ];

                $nextMonday = strtotime("next Monday", $endDate);

                // Check if the next Monday is still within the current month.
                if (date('m', $nextMonday) == $month) {
                    $startDate = $nextMonday;
                } else {
                    // If the next Monday is in the next month, break the loop.
                    break;
                }

                $weekNumber++;
            }
        }

        // dd($weeks);




        // dd($weeks);

        $WeekStatus = [];

        foreach ($ancakFA as $key => $value) {
            $WeekStatus[$key] = [];

            foreach ($value as $estKey => $est) {
                $WeekStatus[$key][$estKey] = [];

                foreach ($weeks as $weekKey => $week) {
                    // dd($weekKey);
                    $WeekStatus[$key][$estKey]["week" . ($weekKey + 0)] = [];

                    foreach ($est as $statusKey => $status) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $WeekStatus[$key][$estKey]["week" . ($weekKey + 0)][$statusKey] = $newStatus;
                        }
                    }
                }
            }
        }
        // dd($WeekStatus);
        // dd($queryEste, $queryAfd);
        $defaultWeek = array();

        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultWeek[$est['est']][$afd['nama']] = 0;
                }
            }
        }

        foreach ($defaultWeek as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($WeekStatus as $dataKey => $dataValue) {

                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {

                            if ($dataEstKey == $monthKey) {
                                $defaultWeek[$key][$monthKey] = $dataEstValue;
                            }
                        }
                    }
                }
            }
        }


        // dd($defaultweekmua);


        // dd($dividenmua);

        // dd($newDefaultWeek['KNE']['OA']);
        $dividen = [];

        foreach ($defaultWeek as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {

                    $dividenn = count($value1);
                }
                $dividen[$key][$key1]['dividen'] = $dividenn;
            } else {
                $dividen[$key][$key1]['dividen'] = 0;
            }
        }

        // dd($defaultWeek);

        // dd($dividen, $defaultWeek);
        $newSidak = array();
        foreach ($defaultWeek as $key => $value) {
            $total_skoreest = 0;
            $tot_estAFd = 0;
            $new_dvdAfd = 0;
            $new_dvdAfdest = 0;
            $total_estkors = 0;
            $total_skoreafd = 0;
            $devest = count($value);
            // dd($devest);
            // dd($value);
            $v2check5 = 0;
            $newpembagi3 = 0;
            foreach ($value as $key1 => $value2)  if (is_array($value2)) {

                $tot_afdscore = 0;
                $totskor_brd1 = 0;
                $totskor_janjang1 = 0;
                $total_skoreest = 0;
                $newpembagi1 = 0;
                $v2check4 = 0;
                foreach ($value2 as $key2 => $value3) {


                    $total_brondolan = 0;
                    $total_janjang = 0;
                    $tod_brd = 0;
                    $tod_jjg = 0;
                    $totskor_brd = 0;
                    $totskor_janjang = 0;
                    $tot_brdxm = 0;
                    $tod_janjangxm = 0;
                    $v2check3 = 0;

                    foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                        $tph1 = 0;
                        $jalan1 = 0;
                        $bin1 = 0;
                        $karung1 = 0;
                        $buah1 = 0;
                        $restan1 = 0;
                        $v2check2 = 0;

                        foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                            $tph = 0;
                            $jalan = 0;
                            $bin = 0;
                            $karung = 0;
                            $buah = 0;
                            $restan = 0;
                            $v2check = count($value5);
                            foreach ($value5 as $key5 => $value6) {
                                $sum_bt_tph = 0;
                                $sum_bt_jalan = 0;
                                $sum_bt_bin = 0;
                                $sum_jum_karung = 0;
                                $sum_buah_tinggal = 0;
                                $sum_restan_unreported = 0;
                                $sum_all_restan_unreported = 0;

                                foreach ($value6 as $key6 => $value7) {
                                    // dd($value7);
                                    // dd($value7);
                                    $sum_bt_tph += $value7['bt_tph'];
                                    $sum_bt_jalan += $value7['bt_jalan'];
                                    $sum_bt_bin += $value7['bt_bin'];
                                    $sum_jum_karung += $value7['jum_karung'];


                                    $sum_buah_tinggal += $value7['buah_tinggal'];
                                    $sum_restan_unreported += $value7['restan_unreported'];
                                }
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['tph'] = $sum_bt_tph;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['jalan'] = $sum_bt_jalan;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['bin'] = $sum_bt_bin;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['karung'] = $sum_jum_karung;

                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['buah'] = $sum_buah_tinggal;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['restan'] = $sum_restan_unreported;


                                $tph += $sum_bt_tph;
                                $jalan += $sum_bt_jalan;
                                $bin += $sum_bt_bin;
                                $karung += $sum_jum_karung;
                                $buah += $sum_buah_tinggal;
                                $restan += $sum_restan_unreported;
                            }

                            $newSidak[$key][$key1][$key2][$key3][$key4]['tph'] = $tph;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['jalan'] = $jalan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['bin'] = $bin;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['karung'] = $karung;

                            $newSidak[$key][$key1][$key2][$key3][$key4]['buah'] = $buah;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['restan'] = $restan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['v2check'] = $v2check;

                            $tph1 += $tph;
                            $jalan1 += $jalan;
                            $bin1 += $bin;
                            $karung1 += $karung;
                            $buah1 += $buah;
                            $restan1 += $restan;
                            $v2check2 += $v2check;
                        }
                        // dd($key3);
                        $status_panen = $key3;

                        [$panen_brd, $panen_jjg] = calculatePanen($status_panen);

                        // untuk brondolan gabungan dari bt-tph,bt-jalan,bt-bin,jum-karung 
                        $total_brondolan =  round(($tph1 + $jalan1 + $bin1 + $karung1) * $panen_brd / 100, 1);
                        $total_janjang =  round(($buah1 + $restan1) * $panen_jjg / 100, 1);
                        $tod_brd = $tph1 + $jalan1 + $bin1 + $karung1;
                        $tod_jjg = $buah1 + $restan1;
                        $newSidak[$key][$key1][$key2][$key3]['tphx'] = $tph1;
                        $newSidak[$key][$key1][$key2][$key3]['jalan'] = $jalan1;
                        $newSidak[$key][$key1][$key2][$key3]['bin'] = $bin1;
                        $newSidak[$key][$key1][$key2][$key3]['karung'] = $karung1;
                        $newSidak[$key][$key1][$key2][$key3]['tot_brd'] = $tod_brd;

                        $newSidak[$key][$key1][$key2][$key3]['buah'] = $buah1;
                        $newSidak[$key][$key1][$key2][$key3]['restan'] = $restan1;
                        $newSidak[$key][$key1][$key2][$key3]['skor_brd'] = $total_brondolan;
                        $newSidak[$key][$key1][$key2][$key3]['skor_janjang'] = $total_janjang;
                        $newSidak[$key][$key1][$key2][$key3]['tod_jjg'] = $tod_jjg;
                        $newSidak[$key][$key1][$key2][$key3]['v2check2'] = $v2check2;

                        $totskor_brd += $total_brondolan;
                        $totskor_janjang += $total_janjang;
                        $tot_brdxm += $tod_brd;
                        $tod_janjangxm += $tod_jjg;
                        $v2check3 += $v2check2;
                    } else {
                        $newSidak[$key][$key1][$key2][$key3]['tphx'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['jalan'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['bin'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['karung'] = 0;

                        $newSidak[$key][$key1][$key2][$key3]['buah'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['restan'] = 0;

                        $newSidak[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['skor_janjang'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['tot_brd'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['tod_jjg'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['v2check2'] = 0;
                    }


                    $total_estkors = $totskor_brd + $totskor_janjang;
                    if ($total_estkors != 0) {

                        $checkscore = 100 - ($total_estkors);

                        if ($checkscore < 0) {
                            $newscore = 0;
                            $newSidak[$key][$key1][$key2]['mines'] = 'ada';
                        } else {
                            $newscore = $checkscore;
                            $newSidak[$key][$key1][$key2]['mines'] = 'tidak';
                        }

                        $newSidak[$key][$key1][$key2]['all_score'] = $newscore;
                        $newSidak[$key][$key1][$key2]['check_data'] = 'ada';

                        $total_skoreafd = $newscore;
                        $newpembagi = 1;
                    } else if ($v2check3 != 0) {
                        $checkscore = 100 - ($total_estkors);

                        if ($checkscore < 0) {
                            $newscore = 0;
                            $newSidak[$key][$key1][$key2]['mines'] = 'ada';
                        } else {
                            $newscore = $checkscore;
                            $newSidak[$key][$key1][$key2]['mines'] = 'tidak';
                        }
                        $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak[$key][$key1][$key2]['check_data'] = 'ada';

                        $total_skoreafd = $newscore;

                        $newpembagi = 1;
                    } else {
                        $newSidak[$key][$key1][$key2]['all_score'] = 0;
                        $newSidak[$key][$key1][$key2]['check_data'] = 'null';
                        $total_skoreafd = 0;
                        $newpembagi = 0;
                    }
                    // $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                    $newSidak[$key][$key1][$key2]['total_brd'] = $tot_brdxm;
                    $newSidak[$key][$key1][$key2]['total_brdSkor'] = $totskor_brd;
                    $newSidak[$key][$key1][$key2]['total_janjang'] = $tod_janjangxm;
                    $newSidak[$key][$key1][$key2]['total_janjangSkor'] = $totskor_janjang;
                    $newSidak[$key][$key1][$key2]['total_skor'] = $total_skoreafd;
                    $newSidak[$key][$key1][$key2]['janjang_brd'] = $totskor_brd + $totskor_janjang;
                    $newSidak[$key][$key1][$key2]['v2check3'] = $v2check3;
                    $newSidak[$key][$key1][$key2]['newpembagi'] = $newpembagi;

                    $totskor_brd1 += $totskor_brd;
                    $totskor_janjang1 += $totskor_janjang;
                    $total_skoreest += $total_skoreafd;
                    $newpembagi1 += $newpembagi;
                    $v2check4 += $v2check3;
                }



                // dd($deviden);

                $namaGM = '-';
                foreach ($queryAsisten as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }

                $deviden = count($value2);

                $new_dvd = $dividen_x ?? 0;
                $new_dvdest = $devidenEst_x ?? 0;


                if ($v2check4 != 0 && $total_skoreest == 0) {
                    $tot_afdscore = 100;
                    $newpembagi2 = 1;
                } else if ($v2check4 != 0) {
                    $tot_afdscore = round($total_skoreest / $newpembagi1, 1);
                    $newpembagi2 = 1;
                } else if ($newpembagi1 == 0 && $v2check4 == 0) {
                    $tot_afdscore = 0;
                    $newpembagi2 = 0;
                }


                if ($tot_afdscore < 0) {
                    # code...
                    $newscore = 0;
                } else {
                    $newscore = $tot_afdscore;
                }
                // $newSidak[$key][$key1]['deviden'] = $deviden;

                $newSidak[$key][$key1]['total_brd'] = $totskor_brd1;
                $newSidak[$key][$key1]['total_janjang'] = $totskor_janjang1;
                $newSidak[$key][$key1]['new_deviden'] = $new_dvd;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
                $newSidak[$key][$key1]['total_skoreest'] = $total_skoreest;
                if ($v2check4 == 0) {
                    $newSidak[$key][$key1]['total_score'] = '-';
                } else {
                    $newSidak[$key][$key1]['total_score'] = $newscore;
                }

                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['devidenest'] = $newpembagi1;
                $newSidak[$key][$key1]['v2check4'] = $v2check4;

                $tot_estAFd += $newscore;
                $new_dvdAfd += $new_dvd;
                $new_dvdAfdest += $new_dvdest;
                $v2check5 += $v2check4;
                $newpembagi3 += $newpembagi2;
            } else {
                $newSidak[$key][$key1]['total_brd'] = 0;
                $newSidak[$key][$key1]['total_janjang'] = 0;
                $newSidak[$key][$key1]['new_deviden'] = 0;
                $newSidak[$key][$key1]['asisten'] = 0;
                $newSidak[$key][$key1]['total_skoreest'] = 0;
                $newSidak[$key][$key1]['total_score'] = '-';
                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['devidenest'] = 0;
                $newSidak[$key][$key1]['v2check4'] = 0;
            }


            if ($v2check5 != 0) {
                $total_skoreest = round($tot_estAFd / $newpembagi3, 1);
            } else if ($v2check5 != 0 && $tot_estAFd == 0) {
                $total_skoreest = 100;
            } else {
                $total_skoreest = 0;
            }

            // dd($value);

            $namaGM = '-';
            foreach ($queryAsisten as $asisten) {
                if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                    $namaGM = $asisten['nama'];
                    break;
                }
            }
            if ($new_dvdAfd != 0) {
                $newSidak[$key]['deviden'] = 1;
            } else {
                $newSidak[$key]['deviden'] = 0;
            }

            $newSidak[$key]['total_skorest'] = $tot_estAFd;
            $newSidak[$key]['score_estate'] = $total_skoreest;
            $newSidak[$key]['asisten'] = $namaGM;
            $newSidak[$key]['estate'] = $key;
            $newSidak[$key]['afd'] = 'GM';
            $newSidak[$key]['afdeling'] = $newpembagi3;
            $newSidak[$key]['v2check5'] = $v2check5;
        }
        // dd($newSidak);
        $sidaktph = array();
        foreach ($queryEste as $key => $value) {
            foreach ($newSidak as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $sidaktph[$value['wil']][$key2] = array_merge($sidaktph[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // dd($newSidak);
        // dd($sidaktph[4]['MRE']['OA']);

        // dd($newDefaultWeek['KNE'], $newSidak['KNE']['OA']);

        // qc inspeksi 
        $QueryMTancakWil = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
            // ->whereYear('datetime', '2023')
            // ->where('datetime', 'like', '%' . $getDate . '%')
            ->where('datetime', 'like', '%' . $bulan . '%')
            // ->whereYear('datetime', $year)
            ->orderBy('afdeling', 'asc')
            ->get();
        $QueryMTancakWil = $QueryMTancakWil->groupBy(['estate', 'afdeling']);
        $QueryMTancakWil = json_decode($QueryMTancakWil, true);

        $dataPerBulan = array();
        foreach ($QueryMTancakWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataPerBulan[$key][$key2][$key3] = $value3;
                }
            }
        }

        // dd($QueryMTancakWil);
        // dd($QueryMTancakWil);

        $defaultNew = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }

        $defaultNewmua = array();
        foreach ($muaest as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNewmua[$est['est']][$afd['est']]['null'] = 0;
                }
            }
        }
        $mergedDatamua = array();
        foreach ($defaultNewmua as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mergedDatamua[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mergedDatamua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedDatamua[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mergedDatamua[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtancakWIltab1mua = array();
        foreach ($muaest as $key => $value) {
            foreach ($mergedDatamua as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1mua[$value['wil']][$key2] = array_merge($mtancakWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // dd($mtancakWIltab1mua);
        $mergedData = array();
        foreach ($defaultNew as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mergedData[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mergedData[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedData[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mergedData[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtancakWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mergedData as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1[$value['wil']][$key2] = array_merge($mtancakWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // dd($mtancakWIltab1);

        $QueryMTbuahWil = DB::connection('mysql2')->table('mutu_buah')
            ->select(
                "mutu_buah.*",
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun')
            )
            ->where('datetime', 'like', '%' . $bulan . '%')
            ->orderBy('afdeling', 'asc')
            ->get();
        $QueryMTbuahWil = $QueryMTbuahWil->groupBy(['estate', 'afdeling']);
        $QueryMTbuahWil = json_decode($QueryMTbuahWil, true);

        // dd($QueryMTbuahWil);

        $dataMTBuah = array();
        foreach ($QueryMTbuahWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTBuah[$key][$key2][$key3] = $value3;
                }
            }
        }

        $defaultMTbuah = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultMTbuah[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }



        // dd($mtBuahWIltab1mua);




        $mutuBuahMerge = array();
        foreach ($defaultMTbuah as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTBuah)) {
                    if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                        if (!empty($dataMTBuah[$estKey][$afdKey])) {
                            $mutuBuahMerge[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                        } else {
                            $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtBuahWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuBuahMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtBuahWIltab1[$value['wil']][$key2] = array_merge($mtBuahWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }






        $mtancaktab1Wil = array();
        foreach ($mtancakWIltab1 as $key => $value) if (!empty($value)) {
            $pokok_panenWil = 0;
            $jum_haWil = 0;
            $janjang_panenWil = 0;
            $p_panenWil = 0;
            $k_panenWil = 0;
            $brtgl_panenWil = 0;
            $bhts_panenWil = 0;
            $bhtm1_panenWil = 0;
            $bhtm2_panenWil = 0;
            $bhtm3_oanenWil = 0;
            $pelepah_swil = 0;
            $totalPKTwil = 0;
            $sumBHWil = 0;
            $akpWil = 0;
            $brdPerwil = 0;
            $sumPerBHWil = 0;
            $perPiWil = 0;
            $totalWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value2)) {
                $pokok_panenEst = 0;
                $jum_haEst =  0;
                $janjang_panenEst =  0;
                $akpEst =  0;
                $p_panenEst =  0;
                $k_panenEst =  0;
                $brtgl_panenEst = 0;
                $skor_bTinggalEst =  0;
                $brdPerjjgEst =  0;
                $bhtsEST = 0;
                $bhtm1EST = 0;
                $bhtm2EST = 0;
                $bhtm3EST = 0;
                $pelepah_sEST = 0;

                $skor_bhEst =  0;
                $skor_brdPerjjgEst =  0;

                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {

                    $akp = 0;
                    $skor_bTinggal = 0;
                    $brdPerjjg = 0;
                    $pokok_panen = 0;
                    $janjang_panen = 0;
                    $p_panen = 0;
                    $k_panen = 0;
                    $bhts_panen  = 0;
                    $bhtm1_panen  = 0;
                    $bhtm2_panen  = 0;
                    $bhtm3_oanen  = 0;
                    $ttlSkorMA = 0;
                    $listBlokPerAfd = array();
                    $jum_ha = 0;
                    $pelepah_s = 0;
                    $skor_brdPerjjg = 0;
                    $skor_bh = 0;
                    $skor_perPl = 0;
                    $totalPokok = 0;
                    $totalPanen = 0;
                    $totalP_panen = 0;
                    $totalK_panen = 0;
                    $totalPTgl_panen = 0;
                    $totalbhts_panen = 0;
                    $totalbhtm1_panen = 0;
                    $totalbhtm2_panen = 0;
                    $totalbhtm3_oanen = 0;
                    $totalpelepah_s = 0;
                    $total_brd = 0;
                    $check_input = 'kosong';
                    $nilai_input = 0;
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);

                        $totalPokok += $value3["sample"];
                        $totalPanen +=  $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen += $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];

                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];

                        $totalpelepah_s += $value3["ps"];
                        $check_input = $value3["jenis_input"];
                        $nilai_input = $value3["skor_akhir"];
                    }


                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }


                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                    } else {
                        $perPl = 0;
                    }





                    $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                    if (!empty($nonZeroValues)) {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                    } else {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                    }

                    // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                    $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] = $akp;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = $totalP_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = $totalK_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                    $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_per'] = $perPl;
                    // total skor akhir

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                    $mtancaktab1Wil[$key][$key1][$key2]['check_input'] = $check_input;
                    $mtancaktab1Wil[$key][$key1][$key2]['nilai_input'] = $nilai_input;

                    $pokok_panenEst += $totalPokok;

                    $jum_haEst += $jum_ha;
                    $janjang_panenEst += $totalPanen;

                    $p_panenEst += $totalP_panen;
                    $k_panenEst += $totalK_panen;
                    $brtgl_panenEst += $totalPTgl_panen;

                    // bagian buah tinggal
                    $bhtsEST   += $totalbhts_panen;
                    $bhtm1EST += $totalbhtm1_panen;
                    $bhtm2EST   += $totalbhtm2_panen;
                    $bhtm3EST   += $totalbhtm3_oanen;
                    // data untuk pelepah sengklek
                    $pelepah_sEST += $totalpelepah_s;
                } else {
                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] =  0;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhi0;

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_bh'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = 0;
                }

                $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                // dd($sumBHEst);
                if ($pokok_panenEst != 0) {
                    $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                } else {
                    $akpEst = 0;
                }

                if ($janjang_panenEst != 0) {
                    $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                } else {
                    $brdPerjjgEst = 0;
                }



                // dd($sumBHEst);
                if ($sumBHEst != 0) {
                    $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                } else {
                    $sumPerBHEst = 0;
                }

                if ($pokok_panenEst != 0) {
                    $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                } else {
                    $perPlEst = 0;
                }


                $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                if (!empty($nonZeroValues)) {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                } else {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                //PENAMPILAN UNTUK PERESTATE
                $mtancaktab1Wil[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  $jum_haEst;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  $akpEst;

                $mtancaktab1Wil[$key][$key1]['p'] = $p_panenEst;
                $mtancaktab1Wil[$key][$key1]['k'] = $k_panenEst;
                $mtancaktab1Wil[$key][$key1]['tgl'] = $brtgl_panenEst;

                $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = $bhtsEST;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = $bhtm1EST;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = $bhtm2EST;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = $bhtm3EST;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                $mtancaktab1Wil[$key][$key1]['palepah_per'] = $perPlEst;
                // total skor akhir

                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = $totalSkorEst;

                //perhitungn untuk perwilayah

                $pokok_panenWil += $pokok_panenEst;
                $jum_haWil += $jum_haEst;
                $janjang_panenWil += $janjang_panenEst;
                $p_panenWil += $p_panenEst;
                $k_panenWil += $k_panenEst;
                $brtgl_panenWil += $brtgl_panenEst;
                // bagian buah tinggal
                $bhts_panenWil += $bhtsEST;
                $bhtm1_panenWil += $bhtm1EST;
                $bhtm2_panenWil += $bhtm2EST;
                $bhtm3_oanenWil += $bhtm3EST;
                $pelepah_swil += $pelepah_sEST;
            } else {
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = 0;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  0;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = 0;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  0;

                $mtancaktab1Wil[$key][$key1]['p'] = 0;
                $mtancaktab1Wil[$key][$key1]['k'] = 0;
                $mtancaktab1Wil[$key][$key1]['tgl'] = 0;

                // $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = 0;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = 0;
                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = 0;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhir
                $mtancaktab1Wil[$key][$key1]['skor_bh'] =  0;
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = 0;
            }
            $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
            $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

            if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                $akpWil = 0;
            } else {

                $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
            }

            if ($totalPKTwil != 0) {
                $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
            } else {
                $brdPerwil = 0;
            }

            // dd($sumBHEst);
            if ($sumBHWil != 0) {
                $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
            } else {
                $sumPerBHWil = 0;
            }

            if ($pokok_panenWil != 0) {
                $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
            } else {
                $perPiWil = 0;
            }

            $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

            if (!empty($nonZeroValues)) {
                $mtancaktab1Wil[$key]['check_data'] = 'ada';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtancaktab1Wil[$key]['check_data'] = 'kosong';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }

            // $totalWil = $skor_bh + $skor_brd + $skor_ps;
            $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

            $mtancaktab1Wil[$key]['pokok_sample'] = $pokok_panenWil;
            $mtancaktab1Wil[$key]['ha_sample'] =  $jum_haWil;
            $mtancaktab1Wil[$key]['jumlah_panen'] = $janjang_panenWil;
            $mtancaktab1Wil[$key]['akp_rl'] =  $akpWil;

            $mtancaktab1Wil[$key]['p'] = $p_panenWil;
            $mtancaktab1Wil[$key]['k'] = $k_panenWil;
            $mtancaktab1Wil[$key]['tgl'] = $brtgl_panenWil;
            $mtancaktab1Wil[$key]['total_brd'] = $totalPKTwil;

            $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = $brdPerwil;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['bhts_s'] = $bhts_panenWil;
            $mtancaktab1Wil[$key]['bhtm1'] = $bhtm1_panenWil;
            $mtancaktab1Wil[$key]['bhtm2'] = $bhtm2_panenWil;
            $mtancaktab1Wil[$key]['bhtm3'] = $bhtm3_oanenWil;
            $mtancaktab1Wil[$key]['total_buah'] = $sumBHWil;
            $mtancaktab1Wil[$key]['total_buah_per'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = $pelepah_swil;

            $mtancaktab1Wil[$key]['palepah_per'] = $perPiWil;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = skor_buah_Ma($sumPerBHWil);
            $mtancaktab1Wil[$key]['skor_brd'] = skor_brd_ma($brdPerwil);
            $mtancaktab1Wil[$key]['skor_ps'] = skor_palepah_ma($perPiWil);
            $mtancaktab1Wil[$key]['skor_akhir'] = $totalWil;
        } else {
            $mtancaktab1Wil[$key]['pokok_sample'] = 0;
            $mtancaktab1Wil[$key]['ha_sample'] =  0;
            $mtancaktab1Wil[$key]['jumlah_panen'] = 0;
            $mtancaktab1Wil[$key]['akp_rl'] =  0;

            $mtancaktab1Wil[$key]['p'] = 0;
            $mtancaktab1Wil[$key]['k'] = 0;
            $mtancaktab1Wil[$key]['tgl'] = 0;

            // $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['bhts_s'] = 0;
            $mtancaktab1Wil[$key]['bhtm1'] = 0;
            $mtancaktab1Wil[$key]['bhtm2'] = 0;
            $mtancaktab1Wil[$key]['bhtm3'] = 0;
            // $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = 0;
            $mtancaktab1Wil[$key]['skor_brd'] = 0;
            $mtancaktab1Wil[$key]['skor_ps'] = 0;
            $mtancaktab1Wil[$key]['skor_akhir'] = 0;
        }

        $mtBuahtab1Wil = array();
        foreach ($mtBuahWIltab1 as $key => $value) if (is_array($value)) {
            $jum_haWil = 0;
            $sum_SamplejjgWil = 0;
            $sum_bmtWil = 0;
            $sum_bmkWil = 0;
            $sum_overWil = 0;
            $sum_abnorWil = 0;
            $sum_kosongjjgWil = 0;
            $sum_vcutWil = 0;
            $sum_krWil = 0;
            $no_Vcutwil = 0;

            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                $jum_haEst  = 0;
                $sum_SamplejjgEst = 0;
                $sum_bmtEst = 0;
                $sum_bmkEst = 0;
                $sum_overEst = 0;
                $sum_abnorEst = 0;
                $sum_kosongjjgEst = 0;
                $sum_vcutEst = 0;
                $sum_krEst = 0;
                $no_VcutEst = 0;

                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
                    $dataBLok = 0;
                    $sum_Samplejjg = 0;
                    $PerMth = 0;
                    $PerMsk = 0;
                    $PerOver = 0;
                    $sum_abnor = 0;
                    $sum_kosongjjg = 0;
                    $Perkosongjjg = 0;
                    $sum_vcut = 0;
                    $PerVcut = 0;
                    $PerAbr = 0;
                    $sum_kr = 0;
                    $total_kr = 0;
                    $per_kr = 0;
                    $totalSkor = 0;
                    $jum_ha = 0;
                    $no_Vcut = 0;
                    $jml_mth = 0;
                    $jml_mtg = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = [];
                    $dtBlok = 0;
                    // $combination_counts = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                        $dtBlok = count($listBlokPerAfd);

                        // $jum_ha = count($listBlokPerAfd);
                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];


                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }

                    // $dataBLok = count($combination_counts);
                    $dataBLok = $dtBlok;
                    $jml_mth = ($sum_bmt + $sum_bmk);
                    $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 3);
                    } else {
                        $total_kr = 0;
                    }


                    $per_kr = round($total_kr * 100, 3);
                    if ($jml_mth != 0) {
                        $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMth = 0;
                    }
                    if ($jml_mtg != 0) {
                        $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMsk = 0;
                    }
                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerOver = 0;
                    }
                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $Perkosongjjg = 0;
                    }
                    if ($sum_vcut != 0) {
                        $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_abnor != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerAbr = 0;
                    }

                    $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                    if (!empty($nonZeroValues)) {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                    } else {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = $sum_over;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = $PerVcut;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                    //perhitungan estate
                    $jum_haEst += $dataBLok;
                    $sum_SamplejjgEst += $sum_Samplejjg;
                    $sum_bmtEst += $jml_mth;
                    $sum_bmkEst += $jml_mtg;
                    $sum_overEst += $sum_over;
                    $sum_abnorEst += $sum_abnor;
                    $sum_kosongjjgEst += $sum_kosongjjg;
                    $sum_vcutEst += $sum_vcut;
                    $sum_krEst += $sum_kr;
                } else {
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = 0;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_abnormal'] = 0;;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                }
                $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                if ($sum_krEst != 0) {
                    $total_krEst = round($sum_krEst / $jum_haEst, 3);
                } else {
                    $total_krEst = 0;
                }
                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }

                if ($sum_bmtEst != 0) {
                    $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMthEst = 0;
                }

                if ($sum_bmkEst != 0) {
                    $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMskEst = 0;
                }

                if ($sum_overEst != 0) {
                    $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerOverEst = 0;
                }
                if ($sum_kosongjjgEst != 0) {
                    $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerkosongjjgEst = 0;
                }
                if ($sum_vcutEst != 0) {
                    $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerVcutest = 0;
                }
                if ($sum_abnorEst != 0) {
                    $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerAbrest = 0;
                }
                // $per_kr = round($sum_kr * 100);
                $per_krEst = round($total_krEst * 100, 3);


                $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                if (!empty($nonZeroValues)) {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                } else {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = $sum_bmtEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = $PerMthEst;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = $sum_bmkEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = $PerMskEst;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = $sum_overEst;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = $PerOverEst;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = $PerAbrest;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = $sum_vcutEst;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = $PerVcutest;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = $sum_krEst;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = $total_krEst;

                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = $per_krEst;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                //hitung perwilayah
                $jum_haWil += $jum_haEst;
                $sum_SamplejjgWil += $sum_SamplejjgEst;
                $sum_bmtWil += $sum_bmtEst;
                $sum_bmkWil += $sum_bmkEst;
                $sum_overWil += $sum_overEst;
                $sum_abnorWil += $sum_abnorEst;
                $sum_kosongjjgWil += $sum_kosongjjgEst;
                $sum_vcutWil += $sum_vcutEst;
                $sum_krWil += $sum_krEst;
            } else {
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_abnormal'] = 0;;
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = 0;
            }

            // if ($sum_kr != 0) {
            //     $total_kr = round($sum_kr / $dataBLok, 3);
            // } else {
            //     $total_kr = 0;
            // }



            if ($sum_krWil != 0) {
                $total_krWil = round($sum_krWil / $jum_haWil, 3);
            } else {
                $total_krWil = 0;
            }

            if ($sum_bmtWil != 0) {
                $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMthWil = 0;
            }


            if ($sum_bmkWil != 0) {
                $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMskWil = 0;
            }
            if ($sum_overWil != 0) {
                $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerOverWil = 0;
            }
            if ($sum_kosongjjgWil != 0) {
                $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerkosongjjgWil = 0;
            }
            if ($sum_vcutWil != 0) {
                $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerVcutWil = 0;
            }
            if ($sum_abnorWil != 0) {
                $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerAbrWil = 0;
            }
            $per_krWil = round($total_krWil * 100, 3);

            $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

            if (!empty($nonZeroValues)) {
                $mtBuahtab1Wil[$key]['check_data'] = 'ada';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskWil);
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverWil);
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgWil);
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutWil);
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krWil);
            } else {
                $mtBuahtab1Wil[$key]['check_data'] = 'kosong';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak = 0;
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over = 0;
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  0;
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr = 0;
            }

            // $totalSkorWil = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


            $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = $jum_haWil;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = $sum_SamplejjgWil;
            $mtBuahtab1Wil[$key]['total_mentah'] = $sum_bmtWil;
            $mtBuahtab1Wil[$key]['total_perMentah'] = $PerMthWil;
            $mtBuahtab1Wil[$key]['total_masak'] = $sum_bmkWil;
            $mtBuahtab1Wil[$key]['total_perMasak'] = $PerMskWil;
            $mtBuahtab1Wil[$key]['total_over'] = $sum_overWil;
            $mtBuahtab1Wil[$key]['total_perOver'] = $PerOverWil;
            $mtBuahtab1Wil[$key]['total_abnormal'] = $sum_abnorWil;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = $PerAbrWil;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = $sum_kosongjjgWil;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = $PerkosongjjgWil;
            $mtBuahtab1Wil[$key]['total_vcut'] = $sum_vcutWil;
            $mtBuahtab1Wil[$key]['per_vcut'] = $PerVcutWil;
            $mtBuahtab1Wil[$key]['jum_kr'] = $sum_krWil;
            $mtBuahtab1Wil[$key]['kr_blok'] = $total_krWil;

            $mtBuahtab1Wil[$key]['persen_kr'] = $per_krWil;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMthWil);
            $mtBuahtab1Wil[$key]['skor_masak'] = skor_buah_masak_mb($PerMskWil);
            $mtBuahtab1Wil[$key]['skor_over'] = skor_buah_over_mb($PerOverWil);;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
            $mtBuahtab1Wil[$key]['skor_vcut'] = skor_vcut_mb($PerVcutWil);
            $mtBuahtab1Wil[$key]['skor_kr'] = skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = $totalSkorWil;
        } else {
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = 0;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = 0;
            $mtBuahtab1Wil[$key]['total_mentah'] = 0;
            $mtBuahtab1Wil[$key]['total_perMentah'] = 0;
            $mtBuahtab1Wil[$key]['total_masak'] = 0;
            $mtBuahtab1Wil[$key]['total_perMasak'] = 0;
            $mtBuahtab1Wil[$key]['total_over'] = 0;
            $mtBuahtab1Wil[$key]['total_perOver'] = 0;
            $mtBuahtab1Wil[$key]['total_abnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = 0;
            $mtBuahtab1Wil[$key]['total_vcut'] = 0;
            $mtBuahtab1Wil[$key]['per_vcut'] = 0;
            $mtBuahtab1Wil[$key]['jum_kr'] = 0;
            $mtBuahtab1Wil[$key]['kr_blok'] = 0;

            $mtBuahtab1Wil[$key]['persen_kr'] = 0;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = 0;
            $mtBuahtab1Wil[$key]['skor_masak'] = 0;
            $mtBuahtab1Wil[$key]['skor_over'] = 0;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['skor_vcut'] = 0;

            $mtBuahtab1Wil[$key]['skor_kr'] = 0;
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = 0;
        }
        // dd($mtBuahtab1Wil);

        $TranscakReg2 = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y-%m-%d") as date')
            )
            ->where('datetime', 'like', '%' . $bulan . '%')
            ->orderBy('datetime', 'DESC')
            ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
            ->get();
        $AncakCakReg2 = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y-%m-%d") as date')
            )
            ->where('datetime', 'like', '%' . $bulan . '%')
            ->orderBy('datetime', 'DESC')
            ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
            ->get();

        $TranscakReg2 = $TranscakReg2->groupBy(['estate', 'afdeling', 'date', 'blok']);
        $AncakCakReg2 = $AncakCakReg2->groupBy(['estate', 'afdeling', 'date', 'blok']);

        // dd($TranscakReg2);

        // dd($TranscakReg2[1]);


        $DataTransGroupReg2 = json_decode($TranscakReg2, true);


        $groupedDataAcnakreg2 = json_decode($AncakCakReg2, true);
        // dd($groupedDataAcnakreg2);


        $dataMTTransRegs2 = array();
        foreach ($DataTransGroupReg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {

                                    foreach ($value3 as $key4 => $value4) {

                                        $dataMTTransRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }

        // dd($dataMTTransRegs2, $dataMTTransRegs2);
        $dataAncaksRegs2 = array();
        foreach ($groupedDataAcnakreg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {
                                    foreach ($value3 as $key4 => $value4) {
                                        $dataAncaksRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }
        // dd($dataMTTransRegs2);
        $ancakRegss2 = array();

        foreach ($dataAncaksRegs2 as $key => $value) {
            foreach ($value as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3) {
                    $sum = 0; // Initialize sum variable
                    $count = 0; // Initialize count variable
                    foreach ($value3 as $key3 => $value4) {
                        $listBlok = array();
                        $firstEntry = $value4[0];
                        foreach ($value4 as $key4 => $value5) {
                            // dd($value5['sph']);
                            if (!in_array($value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'], $listBlok)) {
                                if ($value5['sph'] != 0) {
                                    $listBlok[] = $value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'];
                                }
                            }
                            $jml_blok = count($listBlok);

                            if ($firstEntry['luas_blok'] != 0) {
                                $first = $firstEntry['luas_blok'];
                            } else {
                                $first = '-';
                            }
                        }
                        if ($first != '-') {
                            $sum += $first;
                            $count++;
                        }
                        $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'] = $first;
                        if ($regional === '2') {
                            $status_panen = explode(",", $value5['status_panen']);
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $status_panen[0];
                        } else {
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $value5['status_panen'];
                        }
                    }
                }
            }
        }
        $transNewdata = array();
        foreach ($dataMTTransRegs2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {

                    foreach ($value2 as $key3 => $value3) {
                        $sum_bt = 0;
                        $sum_Restan = 0;
                        $tph_sample = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key4 => $value4) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            $sum_Restan += $value4['rst'];
                            $tph_sample = count($listBlokPerAfd);
                            $sum_bt += $value4['bt'];
                        }
                        $panenKey = 0;
                        $LuasKey = 0;
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['status_panen'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['status_panen'] = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                            $panenKey = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                        }
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['luas_blok'] = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                            $LuasKey = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                        }


                        if ($panenKey !== 0 && $panenKey <= 3) {
                            if (count($value4) == 1 && $value4[0]['blok'] == '0') {
                                $tph_sample = $value4[0]['tph_baris'];
                                $sum_bt = $value4[0]['bt'];
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($LuasKey) * 1.3, 3);
                            }
                        } else {
                            $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = $tph_sample;
                        }



                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                        $transNewdata[$key][$key1][$key2][$key3]['afdeling'] = $value4['afdeling'];
                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                    }
                }
            }
        }
        foreach ($ancakRegss2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {
                    $tph_tod = 0;
                    foreach ($value2 as $key3 => $value3) {
                        if (!isset($transNewdata[$key][$key1][$key2][$key3])) {
                            $transNewdata[$key][$key1][$key2][$key3] = $value3;

                            if ($value3['status_panen'] <= 3) {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($value3['luas_blok']) * 1.3, 3);
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = 0;
                            }
                        }
                        // If 'tph_sample' key exists, add its value to $tph_tod
                        if (isset($value3['tph_sample'])) {
                            $tph_tod += $value3['tph_sample'];
                        }
                    }
                }
                // Store total_tph for each $key1 after iterating all $key2

            }
        }
        foreach ($transNewdata as $key => &$value) {
            foreach ($value as $key1 => &$value1) {
                $tph_sample_total = 0; // initialize the total
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            if (isset($value3['tph_sample'])) {
                                $tph_sample_total += $value3['tph_sample'];
                            }
                        }
                    }
                }
                $value1['total_tph'] = $tph_sample_total;
            }
        }
        unset($value); // unset the reference
        unset($value1); // unset the reference
        // dd($transNewdata);

        $defaultMtTrans = array();
        foreach ($queryEste as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defaultMtTrans[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $QueryTransWil = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
            )
            ->where('datetime', 'like', '%' . $bulan . '%')
            // ->whereYear('datetime', $year)
            ->get();
        $QueryTransWil = $QueryTransWil->groupBy(['estate', 'afdeling']);
        $QueryTransWil = json_decode($QueryTransWil, true);


        // dd($QueryTransWil);
        $dataMTTrans = array();
        foreach ($QueryTransWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTTrans[$key][$key2][$key3] = $value3;
                }
            }
        }
        $mutuAncakMerge = array();
        foreach ($defaultMtTrans as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTTrans)) {
                    if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                        if (!empty($dataMTTrans[$estKey][$afdKey])) {
                            $mutuAncakMerge[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                        } else {
                            $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtTransWiltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuAncakMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtTransWiltab1[$value['wil']][$key2] = array_merge($mtTransWiltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }

        // dd($mtTranstab1Wilmua, $sidak_buah_mua);



        $mtTranstab1Wil = array();
        foreach ($mtTransWiltab1 as $key => $value) if (!empty($value)) {
            $dataBLokWil = 0;
            $sum_btWil = 0;
            $sum_rstWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLokEst = 0;
                $sum_btEst = 0;
                $sum_rstEst = 0;
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        // }
                        $dataBLok = count($listBlokPerAfd);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }
                    $tot_sample = 0;  // Define the variable outside of the foreach loop

                    foreach ($transNewdata as $keys => $trans) {
                        if ($keys == $key1) {
                            foreach ($trans as $keys2 => $trans2) {
                                if ($keys2 == $key2) {
                                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sampleNew'] = $trans2['total_tph'];
                                    $tot_sample = $trans2['total_tph'];
                                }
                            }
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $tot_sample, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $tot_sample, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    }


                    $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                    if (!empty($nonZeroValues)) {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                    } else {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = "kosong";
                    }
                    // dd($transNewdata);




                    $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                    //PERHITUNGAN PERESTATE
                    if ($regional == '2' || $regional == 2) {
                        $dataBLokEst += $tot_sample;
                    } else {
                        $dataBLokEst += $dataBLok;
                    }

                    $sum_btEst += $sum_bt;
                    $sum_rstEst += $sum_rst;

                    if ($dataBLokEst != 0) {
                        $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                    } else {
                        $brdPertphEst = 0;
                    }

                    if ($dataBLokEst != 0) {
                        $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                    } else {
                        $buahPerTPHEst = 0;
                    }

                    // dd($mtTranstab1Wil);
                    $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = 0;
                }

                $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                if (!empty($nonZeroValues)) {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                // $totalSkorEst = $skor_brd + $skor_buah ;


                $mtTranstab1Wil[$key][$key1]['tph_sample'] = $dataBLokEst;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = $sum_btEst;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = $sum_rstEst;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = $totalSkorEst;

                //perhitungan per wil
                $dataBLokWil += $dataBLokEst;
                $sum_btWil += $sum_btEst;
                $sum_rstWil += $sum_rstEst;

                if ($dataBLokWil != 0) {
                    $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                } else {
                    $brdPertphWil = 0;
                }
                if ($dataBLokWil != 0) {
                    $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                } else {
                    $buahPerTPHWil = 0;
                }

                $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
            } else {
                $mtTranstab1Wil[$key][$key1]['tph_sample'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = 0;
            }

            $nonZeroValues = array_filter([$sum_btWil, $sum_rstWil]);


            if (!empty($nonZeroValues)) {
                $mtTranstab1Wil[$key]['check_data'] = 'ada';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtTranstab1Wil[$key]['check_data'] = 'kosong';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }
            $mtTranstab1Wil[$key]['tph_sample'] = $dataBLokWil;
            $mtTranstab1Wil[$key]['total_brd'] = $sum_btWil;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = $brdPertphWil;
            $mtTranstab1Wil[$key]['total_buah'] = $sum_rstWil;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = $buahPerTPHWil;
            $mtTranstab1Wil[$key]['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
            $mtTranstab1Wil[$key]['totalSkor'] = $totalSkorWil;
        } else {
            $mtTranstab1Wil[$key]['tph_sample'] = 0;
            $mtTranstab1Wil[$key]['total_brd'] = 0;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = 0;
            $mtTranstab1Wil[$key]['total_buah'] = 0;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['skor_brdPertph'] = 0;
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['totalSkor'] = 0;
        }



        foreach ($mtancaktab1Wil as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) {
                        if (is_array($value2)) {
                            foreach ($mtBuahtab1Wil as $bh => $buah) {
                                foreach ($buah as $bh1 => $buah1) {
                                    if (is_array($buah1)) {
                                        foreach ($buah1 as $bh2 => $buah2) {
                                            if (is_array($buah2)) {
                                                foreach ($mtTranstab1Wil as $tr => $trans) {
                                                    foreach ($trans as $tr1 => $trans1) {
                                                        if (is_array($trans1)) {
                                                            foreach ($trans1 as $tr2 => $trans2) {
                                                                if (is_array($trans2)) {
                                                                    if (
                                                                        $bh == $key &&
                                                                        $bh == $tr &&
                                                                        $bh1 == $key1 &&
                                                                        $bh1 == $tr1 &&
                                                                        $bh2 == $key2 &&
                                                                        $bh2 == $tr2
                                                                    ) {
                                                                        // dd($trans2);
                                                                        // dd($key);
                                                                        if ($value2['check_input'] === 'manual' && $value2['nilai_input'] != 0) {
                                                                            $RekapWIlTabel[$key][$key1][$key2]['data'] = 'ada';
                                                                        } else if ($trans2['check_data'] === 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                                            $RekapWIlTabel[$key][$key1][$key2]['data'] = 'kosong';
                                                                        }

                                                                        if ($value2['check_input'] === 'manual') {
                                                                            $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['nilai_input'];
                                                                        } else  if ($trans2['check_data'] === 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                                            $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = 0;
                                                                        } else {
                                                                            $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['skor_akhir'] + $buah2['TOTAL_SKOR'] + $trans2['totalSkor'];
                                                                        }


                                                                        if ($trans1['check_data'] === 'kosong' && $buah1['check_data'] === 'kosong' && $value1['check_data'] === 'kosong') {
                                                                            $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = 0;
                                                                            $RekapWIlTabel[$key][$key1]['data'] = 'kosong';
                                                                        } else {
                                                                            $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = $value1['skor_akhir'] + $buah1['TOTAL_SKOR'] + $trans1['totalSkor'];
                                                                        }


                                                                        if ($value1['check_data'] === 'kosong' && $buah1['check_data'] === 'kosong' && $trans1['check_data'] === 'kosong') {
                                                                            $RekapWIlTabel[$key][$key1]['dataEst'] = 'kosong';
                                                                        }

                                                                        // dd($value,$buah,$trans);
                                                                        if ($trans['check_data'] === 'kosong' && $buah['check_data'] === 'kosong' && $value['check_data'] === 'kosong') {
                                                                            $RekapWIlTabel[$key]['TotalSkorWil'] = 0;
                                                                        } else {
                                                                            $RekapWIlTabel[$key]['TotalSkorWil'] = $value['skor_akhir'] + $buah['TOTAL_SKOR'] + $trans['totalSkor'];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // dd($RekapWIlTabel);
        // dd($mtancaktab1Wil, $mtBuahtab1Wil, $mtTranstab1Wil, $RekapWIlTabel);

        foreach ($RekapWIlTabel as $key1 => $estates)  if (is_array($estates)) {
            $sortedData = array();
            $sortedDataEst = array();

            foreach ($estates as $estateName => $data) {
                if (is_array($data)) {
                    $sortedDataEst[] = array(
                        'key1' => $key1,
                        'estateName' => $estateName,
                        'data' => $data
                    );
                    foreach ($data as $key2 => $scores) {
                        if (is_array($scores)) {
                            $sortedData[] = array(
                                'estateName' => $estateName,
                                'key2' => $key2,
                                'scores' => $scores
                            );
                        }
                    }
                }
            }

            //mengurutkan untuk nilai afd
            usort($sortedData, function ($a, $b) {
                return $b['scores']['TotalSkor'] - $a['scores']['TotalSkor'];
            });
            //mengurutkan untuk nilai estate
            usort($sortedDataEst, function ($a, $b) {
                return $b['data']['TotalSkorEST'] - $a['data']['TotalSkorEST'];
            });

            //menambahkan nilai rank ke dalam afd
            $rank = 1;
            foreach ($sortedData as $sortedEstate) {
                $RekapWIlTabel[$key1][$sortedEstate['estateName']][$sortedEstate['key2']]['rankAFD'] = $rank;
                $rank++;
            }

            //menambahkan nilai rank ke dalam estate
            $rank = 1;
            foreach ($sortedDataEst as $sortedest) {
                $RekapWIlTabel[$key1][$sortedest['estateName']]['rankEST'] = $rank;
                $rank++;
            }


            unset($sortedData, $sortedDataEst);
        }

        // dd($RekapWIlTabel);
        $qcinspeksi = $RekapWIlTabel;

        $sortedArray = [];

        foreach ($qcinspeksi as $key => $value) {
            $sortedArray[$key] = $value['TotalSkorWil'];
        }

        arsort($sortedArray);

        $rank = 1;
        foreach ($sortedArray as $key => $value) {
            $qcinspeksi[$key]['rankWil'] = $rank++;
        }
        // dd($qcinspeksi);

        // untuk get mua ===============================================

        // mutu_sidak_buah mua
        if ($regional == 1) {
            $defaultmua = array();

            foreach ($muaest as $key2 => $value2) {
                foreach ($queryAfd as $key3 => $value3) {
                    if ($value2['est'] == $value3['est']) {
                        $defaultmua[$value2['est']][$value3['est']] = 0;
                    }
                }
            }
            foreach ($defaultmua as $estateKey => $afdelingArray) {
                foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                    if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                        $defaultmua[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                    }
                }
            }

            $sidak_buah_mua = array();
            // dd($defaultmua);
            $jjg_samplexy = 0;
            $tnpBRDxy = 0;
            $krgBRDxy = 0;
            $abrxy = 0;
            $overripexy = 0;
            $emptyxy = 0;
            $vcutxy = 0;
            $rdxy = 0;
            $dataBLokxy = 0;
            $sum_krxy = 0;
            $csrmsy = 0;
            foreach ($defaultmua as $key => $value) {
                $jjg_samplex = 0;
                $tnpBRDx = 0;
                $krgBRDx = 0;
                $abrx = 0;
                $overripex = 0;
                $emptyx = 0;
                $vcutx = 0;
                $rdx = 0;
                $dataBLokx = 0;
                $sum_krx = 0;
                $csrms = 0;
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        $jjg_sample = 0;
                        $tnpBRD = 0;
                        $krgBRD = 0;
                        $abr = 0;
                        $skor_total = 0;
                        $overripe = 0;
                        $empty = 0;
                        $vcut = 0;
                        $rd = 0;
                        $sum_kr = 0;
                        $allSkor = 0;
                        $combination_counts = array();
                        $newblok = 0;
                        $csfxr = count($value1);
                        foreach ($value1 as $key2 => $value2) {
                            $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                            if (!isset($combination_counts[$combination])) {
                                $combination_counts[$combination] = 0;
                            }
                            $newblok = count($value1);
                            $jjg_sample += $value2['jumlah_jjg'];
                            $tnpBRD += $value2['bmt'];
                            $krgBRD += $value2['bmk'];
                            $abr += $value2['abnormal'];
                            $overripe += $value2['overripe'];
                            $empty += $value2['empty_bunch'];
                            $vcut += $value2['vcut'];
                            $rd += $value2['rd'];
                            $sum_kr += $value2['alas_br'];
                        }
                        // $dataBLok = count($combination_counts);
                        $dataBLok = $newblok;
                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 2);
                        } else {
                            $total_kr = 0;
                        }
                        $per_kr = round($total_kr * 100, 2);
                        $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                        $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                        $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                        $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                        $sidak_buah_mua[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                        $sidak_buah_mua[$key][$key1]['blok'] = $dataBLok;
                        $sidak_buah_mua[$key][$key1]['est'] = $key;
                        $sidak_buah_mua[$key][$key1]['afd'] = $key1;
                        $sidak_buah_mua[$key][$key1]['nama_staff'] = '-';
                        $sidak_buah_mua[$key][$key1]['tnp_brd'] = $tnpBRD;
                        $sidak_buah_mua[$key][$key1]['krg_brd'] = $krgBRD;
                        $sidak_buah_mua[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                        $sidak_buah_mua[$key][$key1]['persen_totalJjg'] = $skor_total;
                        $sidak_buah_mua[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                        $sidak_buah_mua[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                        $sidak_buah_mua[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                        $sidak_buah_mua[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                        $sidak_buah_mua[$key][$key1]['lewat_matang'] = $overripe;
                        $sidak_buah_mua[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                        $sidak_buah_mua[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                        $sidak_buah_mua[$key][$key1]['janjang_kosong'] = $empty;
                        $sidak_buah_mua[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                        $sidak_buah_mua[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                        $sidak_buah_mua[$key][$key1]['vcut'] = $vcut;
                        $sidak_buah_mua[$key][$key1]['karung'] = $sum_kr;
                        $sidak_buah_mua[$key][$key1]['vcut_persen'] = $skor_vcut;
                        $sidak_buah_mua[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                        $sidak_buah_mua[$key][$key1]['abnormal'] = $abr;
                        $sidak_buah_mua[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['rat_dmg'] = $rd;
                        $sidak_buah_mua[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['TPH'] = $total_kr;
                        $sidak_buah_mua[$key][$key1]['persen_krg'] = $per_kr;
                        $sidak_buah_mua[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                        $sidak_buah_mua[$key][$key1]['All_skor'] = $allSkor;
                        $sidak_buah_mua[$key][$key1]['csfxr'] = $csfxr;
                        $sidak_buah_mua[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidak_buah_mua[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                        $jjg_samplex += $jjg_sample;
                        $tnpBRDx += $tnpBRD;
                        $krgBRDx += $krgBRD;
                        $abrx += $abr;
                        $overripex += $overripe;
                        $emptyx += $empty;
                        $vcutx += $vcut;

                        $rdx += $rd;

                        $dataBLokx += $newblok;
                        $sum_krx += $sum_kr;
                        $csrms += $csfxr;
                    } else {

                        $sidak_buah_mua[$key][$key1]['Jumlah_janjang'] = 0;
                        $sidak_buah_mua[$key][$key1]['blok'] = 0;
                        $sidak_buah_mua[$key][$key1]['est'] = $key;
                        $sidak_buah_mua[$key][$key1]['afd'] = $key1;
                        $sidak_buah_mua[$key][$key1]['nama_staff'] = '-';
                        $sidak_buah_mua[$key][$key1]['tnp_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['krg_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['persenTNP_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['persenKRG_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['total_jjg'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_totalJjg'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_total'] = 0;
                        $sidak_buah_mua[$key][$key1]['jjg_matang'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_jjgMtang'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_jjgMatang'] = 0;
                        $sidak_buah_mua[$key][$key1]['lewat_matang'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_lwtMtng'] =  0;
                        $sidak_buah_mua[$key][$key1]['skor_lewatMTng'] = 0;
                        $sidak_buah_mua[$key][$key1]['janjang_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut'] = 0;
                        $sidak_buah_mua[$key][$key1]['karung'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut_skor'] = 0;
                        $sidak_buah_mua[$key][$key1]['abnormal'] = 0;
                        $sidak_buah_mua[$key][$key1]['abnormal_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['rat_dmg'] = 0;
                        $sidak_buah_mua[$key][$key1]['rd_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['TPH'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_krg'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_kr'] = 0;
                        $sidak_buah_mua[$key][$key1]['All_skor'] = 0;
                        $sidak_buah_mua[$key][$key1]['kategori'] = 0;
                        $sidak_buah_mua[$key][$key1]['csfxr'] = 0;
                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidak_buah_mua[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                    }
                }
                if ($sum_krx != 0) {
                    $total_kr = round($sum_krx / $dataBLokx, 2);
                } else {
                    $total_kr = 0;
                }
                $per_kr = round($total_kr * 100, 2);
                $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

                $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                $em = 'EM';

                $nama_em = '';

                // dd($key1);
                foreach ($queryAsisten as $ast => $asisten) {
                    if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                        $nama_em = $asisten['nama'];
                    }
                }
                $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

                $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

                $sidak_buah_mua[$key]['jjg_mantah'] = $jjg_mth;
                $sidak_buah_mua[$key]['persen_jjgmentah'] = $skor_jjgMTh;

                if ($csrms == 0) {
                    $sidak_buah_mua[$key]['check_arr'] = 'kosong';
                    $sidak_buah_mua[$key]['All_skor'] = '-';
                } else {
                    $sidak_buah_mua[$key]['check_arr'] = 'ada';
                    $sidak_buah_mua[$key]['All_skor'] = $allSkor;
                }

                $sidak_buah_mua[$key]['Jumlah_janjang'] = $jjg_samplex;
                $sidak_buah_mua[$key]['csrms'] = $csrms;
                $sidak_buah_mua[$key]['blok'] = $dataBLokx;
                $sidak_buah_mua[$key]['EM'] = 'EM';
                $sidak_buah_mua[$key]['Nama_assist'] = $nama_em;
                $sidak_buah_mua[$key]['nama_staff'] = '-';
                $sidak_buah_mua[$key]['tnp_brd'] = $tnpBRDx;
                $sidak_buah_mua[$key]['krg_brd'] = $krgBRDx;
                $sidak_buah_mua[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidak_buah_mua[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidak_buah_mua[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
                $sidak_buah_mua[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


                $sidak_buah_mua[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
                $sidak_buah_mua[$key]['persen_totalJjg'] = $skor_total;
                $sidak_buah_mua[$key]['skor_total'] = sidak_brdTotal($skor_total);
                $sidak_buah_mua[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
                $sidak_buah_mua[$key]['persen_jjgMtang'] = $skor_jjgMSk;
                $sidak_buah_mua[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                $sidak_buah_mua[$key]['lewat_matang'] = $overripex;
                $sidak_buah_mua[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
                $sidak_buah_mua[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                $sidak_buah_mua[$key]['janjang_kosong'] = $emptyx;
                $sidak_buah_mua[$key]['persen_kosong'] = $skor_jjgKosong;
                $sidak_buah_mua[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                $sidak_buah_mua[$key]['vcut'] = $vcutx;
                $sidak_buah_mua[$key]['vcut_persen'] = $skor_vcut;
                $sidak_buah_mua[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                $sidak_buah_mua[$key]['abnormal'] = $abrx;

                $sidak_buah_mua[$key]['rat_dmg'] = $rdx;

                $sidak_buah_mua[$key]['karung'] = $sum_krx;
                $sidak_buah_mua[$key]['TPH'] = $total_kr;
                $sidak_buah_mua[$key]['persen_krg'] = $per_kr;
                $sidak_buah_mua[$key]['skor_kr'] = sidak_PengBRD($per_kr);
                // $sidak_buah_mua[$key]['All_skor'] = $allSkor;
                $sidak_buah_mua[$key]['kategori'] = sidak_akhir($allSkor);

                $jjg_samplexy += $jjg_samplex;
                $tnpBRDxy += $tnpBRDx;
                $krgBRDxy += $krgBRDx;
                $abrxy += $abrx;
                $overripexy += $overripex;
                $emptyxy += $emptyx;
                $vcutxy += $vcutx;
                $rdxy += $rdx;
                $dataBLokxy += $dataBLokx;
                $sum_krxy += $sum_krx;
                $csrmsy += $csrms;
            }
            if ($sum_krxy != 0) {
                $total_kr = round($sum_krxy / $dataBLokxy, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplexy - $abrxy != 0 ? (($tnpBRDxy + $krgBRDxy) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplexy - $abrxy != 0 ? (($jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy)) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplexy - $abrxy != 0 ? ($overripexy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplexy - $abrxy != 0 ? ($emptyxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplexy != 0 ? ($vcutxy / $jjg_samplexy) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mthxy = $tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy;

            $skor_jjgMTh = ($jjg_samplexy - $abrxy != 0) ? round($jjg_mth / ($jjg_samplexy - $abrxy) * 100, 2) : 0;
            if ($csrmsy == 0) {
                $check_arr = 'kosong';
                $All_skor = '-';
            } else {
                $check_arr = 'ada';
                $All_skor = $allSkor;
            };
            $sidak_buah_mua['PT.MUA'] = [
                'jjg_mantah' => $jjg_mthxy,
                'persen_jjgmentah' => $skor_jjgMTh,
                'check_arr' => $check_arr,
                'All_skor' => $All_skor,
                'Jumlah_janjang' => $jjg_samplexy,
                'csrms' => $csrmsy,
                'blok' => $dataBLokxy,
                'EM' => 'EM',
                'Nama_assist' => $nama_em,
                'nama_staff' => '-',
                'tnp_brd' => $tnpBRDxy,
                'krg_brd' => $krgBRDxy,
                'persenTNP_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($tnpBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'persenKRG_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($krgBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'abnormal_persen' => round(($jjg_samplexy != 0 ? ($abrxy / $jjg_samplexy) * 100 : 0), 2),
                'rd_persen' => round(($jjg_samplexy != 0 ? ($rdxy / $jjg_samplexy) * 100 : 0), 2),
                'total_jjg' => $tnpBRDxy + $krgBRDxy,
                'persen_totalJjg' => $skor_total,
                'skor_total' => sidak_brdTotal($skor_total),
                'jjg_matang' => $jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy),
                'persen_jjgMtang' => $skor_jjgMSk,
                'skor_jjgMatang' => sidak_matangSKOR($skor_jjgMSk),
                'lewat_matang' => $overripexy,
                'persen_lwtMtng' =>  $skor_lewatMTng,
                'skor_lewatMTng' => sidak_lwtMatang($skor_lewatMTng),
                'janjang_kosong' => $emptyxy,
                'persen_kosong' => $skor_jjgKosong,
                'skor_kosong' => sidak_jjgKosong($skor_jjgKosong),
                'vcut' => $vcutxy,
                'vcut_persen' => $skor_vcut,
                'vcut_skor' => sidak_tangkaiP($skor_vcut),
                'abnormal' => $abrxy,
                'rat_dmg' => $rdxy,
                'karung' => $sum_krxy,
                'TPH' => $total_kr,
                'persen_krg' => $per_kr,
                'skor_kr' => sidak_PengBRD($per_kr),
                'kategori' => sidak_akhir($allSkor),
            ];


            // sidak_tph mua 

            $defaultweekmua = array();

            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultweekmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            foreach ($defaultweekmua as $key => $estValue) {
                foreach ($estValue as $monthKey => $monthValue) {
                    foreach ($WeekStatus as $dataKey => $dataValue) {

                        if ($dataKey == $key) {
                            foreach ($dataValue as $dataEstKey => $dataEstValue) {

                                if ($dataEstKey == $monthKey) {
                                    $defaultweekmua[$key][$monthKey] = $dataEstValue;
                                }
                            }
                        }
                    }
                }
            }
            $dividenmua = [];

            foreach ($defaultweekmua as $key => $value) {
                foreach ($value as $key1 => $value1) if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) if (is_array($value2)) {

                        $dividenn = count($value1);
                    }
                    $dividenmua[$key][$key1]['dividen'] = $dividenn;
                } else {
                    $dividenmua[$key][$key1]['dividen'] = 0;
                }
            }

            $tot_estAFdx = 0;
            $new_dvdAfdx = 0;
            $new_dvdAfdesx = 0;
            $v2check5x = 0;
            $newSidak_mua = array();
            foreach ($defaultweekmua as $key => $value) {
                $dividen_afd = 0;
                $total_skoreest = 0;
                $tot_estAFd = 0;
                $new_dvdAfd = 0;
                $new_dvdAfdest = 0;
                $total_estkors = 0;
                $total_skoreafd = 0;

                $deviden = 0;
                $devest = count($value);
                // dd($devest);
                // dd($value);
                $v2check5 = 0;
                foreach ($value as $key1 => $value2)  if (is_array($value2)) {

                    $tot_afdscore = 0;
                    $totskor_brd1 = 0;
                    $totskor_janjang1 = 0;
                    $total_skoreest = 0;
                    $v2check4 = 0;
                    foreach ($value2 as $key2 => $value3) {


                        $total_brondolan = 0;
                        $total_janjang = 0;
                        $tod_brd = 0;
                        $tod_jjg = 0;
                        $totskor_brd = 0;
                        $totskor_janjang = 0;
                        $tot_brdxm = 0;
                        $tod_janjangxm = 0;
                        $v2check3 = 0;

                        foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                            $tph1 = 0;
                            $jalan1 = 0;
                            $bin1 = 0;
                            $karung1 = 0;
                            $buah1 = 0;
                            $restan1 = 0;
                            $v2check2 = 0;

                            foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                                $tph = 0;
                                $jalan = 0;
                                $bin = 0;
                                $karung = 0;
                                $buah = 0;
                                $restan = 0;
                                $v2check = count($value5);
                                foreach ($value5 as $key5 => $value6) {
                                    $sum_bt_tph = 0;
                                    $sum_bt_jalan = 0;
                                    $sum_bt_bin = 0;
                                    $sum_jum_karung = 0;
                                    $sum_buah_tinggal = 0;
                                    $sum_restan_unreported = 0;
                                    $sum_all_restan_unreported = 0;

                                    foreach ($value6 as $key6 => $value7) {
                                        // dd($value7);
                                        // dd($value7);
                                        $sum_bt_tph += $value7['bt_tph'];
                                        $sum_bt_jalan += $value7['bt_jalan'];
                                        $sum_bt_bin += $value7['bt_bin'];
                                        $sum_jum_karung += $value7['jum_karung'];


                                        $sum_buah_tinggal += $value7['buah_tinggal'];
                                        $sum_restan_unreported += $value7['restan_unreported'];
                                    }
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['tph'] = $sum_bt_tph;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['jalan'] = $sum_bt_jalan;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['bin'] = $sum_bt_bin;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['karung'] = $sum_jum_karung;

                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['buah'] = $sum_buah_tinggal;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['restan'] = $sum_restan_unreported;


                                    $tph += $sum_bt_tph;
                                    $jalan += $sum_bt_jalan;
                                    $bin += $sum_bt_bin;
                                    $karung += $sum_jum_karung;
                                    $buah += $sum_buah_tinggal;
                                    $restan += $sum_restan_unreported;
                                }

                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['tph'] = $tph;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['jalan'] = $jalan;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['bin'] = $bin;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['karung'] = $karung;

                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['buah'] = $buah;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['restan'] = $restan;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['v2check'] = $v2check;

                                $tph1 += $tph;
                                $jalan1 += $jalan;
                                $bin1 += $bin;
                                $karung1 += $karung;
                                $buah1 += $buah;
                                $restan1 += $restan;
                                $v2check2 += $v2check;
                            }
                            // dd($key3);
                            $status_panen = $key3;

                            [$panen_brd, $panen_jjg] = calculatePanen($status_panen);

                            // untuk brondolan gabungan dari bt-tph,bt-jalan,bt-bin,jum-karung 
                            $total_brondolan =  round(($tph1 + $jalan1 + $bin1 + $karung1) * $panen_brd / 100, 1);
                            $total_janjang =  round(($buah1 + $restan1) * $panen_jjg / 100, 1);
                            $tod_brd = $tph1 + $jalan1 + $bin1 + $karung1;
                            $tod_jjg = $buah1 + $restan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tphx'] = $tph1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['jalan'] = $jalan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['bin'] = $bin1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['karung'] = $karung1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tot_brd'] = $tod_brd;

                            $newSidak_mua[$key][$key1][$key2][$key3]['buah'] = $buah1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['restan'] = $restan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_brd'] = $total_brondolan;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_janjang'] = $total_janjang;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tod_jjg'] = $tod_jjg;
                            $newSidak_mua[$key][$key1][$key2][$key3]['v2check2'] = $v2check2;

                            $totskor_brd += $total_brondolan;
                            $totskor_janjang += $total_janjang;
                            $tot_brdxm += $tod_brd;
                            $tod_janjangxm += $tod_jjg;
                            $v2check3 += $v2check2;
                        } else {
                            $newSidak_mua[$key][$key1][$key2][$key3]['tphx'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['jalan'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['bin'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['karung'] = 0;

                            $newSidak_mua[$key][$key1][$key2][$key3]['buah'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['restan'] = 0;

                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_janjang'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tot_brd'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tod_jjg'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['v2check2'] = 0;
                        }


                        $total_estkors = $totskor_brd + $totskor_janjang;
                        if ($total_estkors != 0) {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else if ($v2check3 != 0) {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 0;
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'null';
                            $total_skoreafd = 0;
                        }
                        // $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak_mua[$key][$key1][$key2]['total_brd'] = $tot_brdxm;
                        $newSidak_mua[$key][$key1][$key2]['total_brdSkor'] = $totskor_brd;
                        $newSidak_mua[$key][$key1][$key2]['total_janjang'] = $tod_janjangxm;
                        $newSidak_mua[$key][$key1][$key2]['total_janjangSkor'] = $totskor_janjang;
                        $newSidak_mua[$key][$key1][$key2]['total_skor'] = $total_skoreafd;
                        $newSidak_mua[$key][$key1][$key2]['janjang_brd'] = $totskor_brd + $totskor_janjang;
                        $newSidak_mua[$key][$key1][$key2]['v2check3'] = $v2check3;

                        $totskor_brd1 += $totskor_brd;
                        $totskor_janjang1 += $totskor_janjang;
                        $total_skoreest += $total_skoreafd;
                        $v2check4 += $v2check3;
                    }


                    // dd($newSidak_mua);

                    foreach ($dividenmua as $keyx => $value) {
                        if ($keyx == $key) {
                            foreach ($value as $keyx1 => $value2) {
                                if ($keyx1 == $key1) {
                                    // dd($value2);
                                    $dividen_x = $value2['dividen'];
                                    if ($value2['dividen'] != 0) {
                                        $devidenEst_x = 1;
                                    } else {
                                        $devidenEst_x = 0;
                                    }
                                    // dd($dividen);
                                }
                            }
                        }
                    }

                    // dd($deviden);

                    $namaGM = '-';
                    foreach ($queryAsisten as $asisten) {

                        // dd($asisten);
                        if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                            $namaGM = $asisten['nama'];
                            break;
                        }
                    }

                    $deviden = count($value2);

                    $new_dvd = $dividen_x ?? 0;
                    $new_dvdest = $devidenEst_x ?? 0;


                    if ($v2check4 != 0 && $total_skoreest == 0) {
                        $tot_afdscore = 100;
                    } else if ($new_dvd != 0) {
                        $tot_afdscore = round($total_skoreest / $new_dvd, 1);
                    } else if ($new_dvd == 0 && $v2check4 == 0) {
                        $tot_afdscore = 0;
                    }


                    if ($tot_afdscore < 0) {
                        # code...
                        $newscore = 0;
                    } else {
                        $newscore = $tot_afdscore;
                    }
                    // $newSidak_mua[$key][$key1]['deviden'] = $deviden;

                    $newSidak_mua[$key][$key1]['total_brd'] = $totskor_brd1;
                    $newSidak_mua[$key][$key1]['total_janjang'] = $totskor_janjang1;
                    $newSidak_mua[$key][$key1]['new_deviden'] = $new_dvd;
                    $newSidak_mua[$key][$key1]['asisten'] = $namaGM;
                    if ($v2check4 == 0) {
                        $newSidak_mua[$key][$key1]['total_score'] = '-';
                    } else {
                        $newSidak_mua[$key][$key1]['total_score'] = $newscore;
                    }

                    $newSidak_mua[$key][$key1]['est'] = $key;
                    $newSidak_mua[$key][$key1]['afd'] = $key1;
                    $newSidak_mua[$key][$key1]['devidenest'] = $devest;
                    $newSidak_mua[$key][$key1]['v2check4'] = $v2check4;

                    $tot_estAFd += $newscore;
                    $new_dvdAfd += $new_dvd;
                    $new_dvdAfdest += $new_dvdest;
                    $v2check5 += $v2check4;
                }

                $dividen_afd = count($value);
                if ($v2check5 != 0) {
                    $total_skoreest = round($tot_estAFd / $devest, 1);
                    $checkdata = 'ada';
                } else if ($v2check5 != 0 && $devest != 0) {
                    $checkdata = 'ada';
                    $total_skoreest = 0;
                } else {
                    $total_skoreest = '-';
                    $checkdata = 'kosong';
                }

                // dd($value);

                $namaGM = '-';
                foreach ($queryAsisten as $asisten) {
                    if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                if ($new_dvdAfd != 0) {
                    $newSidak_mua[$key]['deviden'] = 1;
                } else {
                    $newSidak_mua[$key]['deviden'] = 0;
                }

                $newSidak_mua[$key]['total_skorest'] = $tot_estAFd;
                $newSidak_mua[$key]['checkdata'] = $checkdata;
                $newSidak_mua[$key]['score_estate'] = $total_skoreest;
                $newSidak_mua[$key]['asisten'] = $namaGM;
                $newSidak_mua[$key]['estate'] = $key;
                $newSidak_mua[$key]['afd'] = 'GM';
                $newSidak_mua[$key]['afdeling'] = $devest;
                $newSidak_mua[$key]['v2check5'] = $v2check5;

                if ($v2check5 != 0) {
                    $devidenlast = 1;
                } else {
                    $devidenlast = 0;
                }
                $devmuxa[] = $devidenlast;

                $tot_estAFdx  += $tot_estAFd;
                $new_dvdAfdx  += $new_dvdAfd;
                $new_dvdAfdesx += $new_dvdAfdest;
                $v2check5x += $v2check5;
            }
            $devmuxax = array_sum($devmuxa);

            if ($v2check5x != 0) {
                $total_skoreestxyz = round($tot_estAFdx / $devmuxax, 1);
                $checkdata = 'ada';
            } else if ($v2check5x != 0 && $devmuxax != 0) {
                $total_skoreestxyz = 0;
                $checkdata = 'ada';
            } else {
                $total_skoreestxyz = '-';
                $checkdata = 'kosong';
            }

            // dd($value);

            $namaGMnewSidak_mua = '-';
            foreach ($queryAsisten as $asisten) {
                if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                    $namaGMnewSidak_mua = $asisten['nama'];
                    break;
                }
            }
            $newSidak_mua['PT.MUA'] = [
                'deviden' => $devmuxax,
                'checkdata' => $checkdata,
                'total_skorest' => $tot_estAFdx,
                'score_estate' => $total_skoreestxyz,
                'asisten' => $namaGM,
                'estate' => $key,
                'afd' => $namaGMnewSidak_mua,
                'afdeling' => $devmuxax,
                'v2check6' => $v2check5,
            ];

            // qc inspeksi mua 

            $defaultNewmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultNewmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            $mergedDatamua = array();
            foreach ($defaultNewmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataPerBulan)) {
                        if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                            if (!empty($dataPerBulan[$estKey][$afdKey])) {
                                $mergedDatamua[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                            } else {
                                $mergedDatamua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mergedDatamua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedDatamua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtancakWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mergedDatamua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtancakWIltab1mua[$value['wil']][$key2] = array_merge($mtancakWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            // dd($mtancakWIltab1mua);
            $qcancakmua = array();
            foreach ($mtancakWIltab1mua as $key => $value) if (!empty($value)) {
                $pokok_panenWil = 0;
                $jum_haWil = 0;
                $janjang_panenWil = 0;
                $p_panenWil = 0;
                $k_panenWil = 0;
                $brtgl_panenWil = 0;
                $bhts_panenWil = 0;
                $bhtm1_panenWil = 0;
                $bhtm2_panenWil = 0;
                $bhtm3_oanenWil = 0;
                $pelepah_swil = 0;
                $totalPKTwil = 0;
                $sumBHWil = 0;
                $akpWil = 0;
                $brdPerwil = 0;
                $sumPerBHWil = 0;
                $perPiWil = 0;
                $totalWil = 0;
                foreach ($value as $key1 => $value1) if (!empty($value2)) {
                    $pokok_panenEst = 0;
                    $jum_haEst =  0;
                    $janjang_panenEst =  0;
                    $akpEst =  0;
                    $p_panenEst =  0;
                    $k_panenEst =  0;
                    $brtgl_panenEst = 0;
                    $brdPerjjgEst =  0;
                    $bhtsEST = 0;
                    $bhtm1EST = 0;
                    $bhtm2EST = 0;
                    $bhtm3EST = 0;
                    $pelepah_sEST = 0;
                    $check2 = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $akp = 0;
                        $skor_bTinggal = 0;
                        $brdPerjjg = 0;
                        $ttlSkorMA = 0;
                        $listBlokPerAfd = array();
                        $jum_ha = 0;
                        $totalPokok = 0;
                        $totalPanen = 0;
                        $totalP_panen = 0;
                        $totalK_panen = 0;
                        $totalPTgl_panen = 0;
                        $totalbhts_panen = 0;
                        $totalbhtm1_panen = 0;
                        $totalbhtm2_panen = 0;
                        $totalbhtm3_oanen = 0;
                        $totalpelepah_s = 0;
                        $check_input = 'kosong';
                        $nilai_input = 0;
                        // dd($value2['SKE']);
                        $check1 = count($value2);
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                                $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            }
                            $jum_ha = count($listBlokPerAfd);

                            $totalPokok += $value3["sample"];
                            $totalPanen +=  $value3["jjg"];
                            $totalP_panen += $value3["brtp"];
                            $totalK_panen += $value3["brtk"];
                            $totalPTgl_panen += $value3["brtgl"];

                            $totalbhts_panen += $value3["bhts"];
                            $totalbhtm1_panen += $value3["bhtm1"];
                            $totalbhtm2_panen += $value3["bhtm2"];
                            $totalbhtm3_oanen += $value3["bhtm3"];

                            $totalpelepah_s += $value3["ps"];
                            $check_input = $value3["jenis_input"];
                            $nilai_input = $value3["skor_akhir"];
                        }


                        if ($totalPokok != 0) {
                            $akp = round(($totalPanen / $totalPokok) * 100, 1);
                        } else {
                            $akp = 0;
                        }


                        $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                        if ($totalPanen != 0) {
                            $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                        } else {
                            $brdPerjjg = 0;
                        }

                        $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                        if ($sumBH != 0) {
                            $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                        } else {
                            $sumPerBH = 0;
                        }

                        if ($totalpelepah_s != 0) {
                            $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                        } else {
                            $perPl = 0;
                        }





                        $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                        if ($check1 != 0) {
                            $qcancakmua[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcancakmua[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                            // $qcancakmua[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                        } else {
                            $qcancakmua[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcancakmua[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                            // $qcancakmua[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                        }

                        // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                        $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                        $qcancakmua[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                        $qcancakmua[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                        $qcancakmua[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                        $qcancakmua[$key][$key1][$key2]['akp_rl'] = $akp;

                        $qcancakmua[$key][$key1][$key2]['p'] = $totalP_panen;
                        $qcancakmua[$key][$key1][$key2]['k'] = $totalK_panen;
                        $qcancakmua[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                        $qcancakmua[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcancakmua[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                        // data untuk buah tinggal
                        $qcancakmua[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                        $qcancakmua[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                        $qcancakmua[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcancakmua[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                        $qcancakmua[$key][$key1][$key2]['palepah_per'] = $perPl;
                        // total skor akhir

                        $qcancakmua[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                        $qcancakmua[$key][$key1][$key2]['check_input'] = $check_input;
                        $qcancakmua[$key][$key1][$key2]['nilai_input'] = $nilai_input;
                        $qcancakmua[$key][$key1][$key2]['check1'] = $check1;

                        $pokok_panenEst += $totalPokok;

                        $jum_haEst += $jum_ha;
                        $janjang_panenEst += $totalPanen;

                        $p_panenEst += $totalP_panen;
                        $k_panenEst += $totalK_panen;
                        $brtgl_panenEst += $totalPTgl_panen;

                        // bagian buah tinggal
                        $bhtsEST   += $totalbhts_panen;
                        $bhtm1EST += $totalbhtm1_panen;
                        $bhtm2EST   += $totalbhtm2_panen;
                        $bhtm3EST   += $totalbhtm3_oanen;
                        // data untuk pelepah sengklek
                        $pelepah_sEST += $totalpelepah_s;
                        $check2 += $check1;
                    } else {
                        $qcancakmua[$key][$key1][$key2]['check_data'] = 'kosong';
                        $qcancakmua[$key][$key1][$key2]['check1'] = 0;
                        $qcancakmua[$key][$key1][$key2]['pokok_sample'] = 0;
                        $qcancakmua[$key][$key1][$key2]['ha_sample'] = 0;
                        $qcancakmua[$key][$key1][$key2]['jumlah_panen'] = 0;
                        $qcancakmua[$key][$key1][$key2]['akp_rl'] =  0;

                        $qcancakmua[$key][$key1][$key2]['p'] = 0;
                        $qcancakmua[$key][$key1][$key2]['k'] = 0;
                        $qcancakmua[$key][$key1][$key2]['tgl'] = 0;

                        // $qcancakmua[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcancakmua[$key][$key1][$key2]['brd/jjg'] = 0;

                        // data untuk buah tinggal
                        $qcancakmua[$key][$key1][$key2]['bhts_s'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm1'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm2'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm3'] = 0;

                        // $qcancakmua[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcancakmua[$key][$key1][$key2]['palepah_pokok'] = 0;
                        // total skor akhi0;

                        $qcancakmua[$key][$key1][$key2]['skor_bh'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_brd'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_ps'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_akhir'] = 0;
                    }

                    $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                    $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                    // dd($sumBHEst);
                    if ($pokok_panenEst != 0) {
                        $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                    } else {
                        $akpEst = 0;
                    }

                    if ($janjang_panenEst != 0) {
                        $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                    } else {
                        $brdPerjjgEst = 0;
                    }



                    // dd($sumBHEst);
                    if ($sumBHEst != 0) {
                        $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                    } else {
                        $sumPerBHEst = 0;
                    }

                    if ($pokok_panenEst != 0) {
                        $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                    } else {
                        $perPlEst = 0;
                    }


                    $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                    if ($check2 != 0) {
                        $qcancakmua[$key][$key1]['check_data'] = 'ada';
                    } else {
                        $qcancakmua[$key][$key1]['check_data'] = 'kosong';
                    }

                    // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                    $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                    //PENAMPILAN UNTUK PERESTATE
                    $qcancakmua[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                    $qcancakmua[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    $qcancakmua[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                    $qcancakmua[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                    $qcancakmua[$key][$key1]['ha_sample'] =  $jum_haEst;
                    $qcancakmua[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                    $qcancakmua[$key][$key1]['akp_rl'] =  $akpEst;

                    $qcancakmua[$key][$key1]['p'] = $p_panenEst;
                    $qcancakmua[$key][$key1]['k'] = $k_panenEst;
                    $qcancakmua[$key][$key1]['tgl'] = $brtgl_panenEst;

                    $qcancakmua[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcancakmua[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                    $qcancakmua[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                    // data untuk buah tinggal
                    $qcancakmua[$key][$key1]['bhts_s'] = $bhtsEST;
                    $qcancakmua[$key][$key1]['bhtm1'] = $bhtm1EST;
                    $qcancakmua[$key][$key1]['bhtm2'] = $bhtm2EST;
                    $qcancakmua[$key][$key1]['bhtm3'] = $bhtm3EST;
                    $qcancakmua[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                    $qcancakmua[$key][$key1]['palepah_per'] = $perPlEst;
                    // total skor akhir

                    $qcancakmua[$key][$key1]['skor_akhir'] = $totalSkorEst;

                    //perhitungn untuk perwilayah

                    $pokok_panenWil += $pokok_panenEst;
                    $jum_haWil += $jum_haEst;
                    $janjang_panenWil += $janjang_panenEst;
                    $p_panenWil += $p_panenEst;
                    $k_panenWil += $k_panenEst;
                    $brtgl_panenWil += $brtgl_panenEst;
                    // bagian buah tinggal
                    $bhts_panenWil += $bhtsEST;
                    $bhtm1_panenWil += $bhtm1EST;
                    $bhtm2_panenWil += $bhtm2EST;
                    $bhtm3_oanenWil += $bhtm3EST;
                    $pelepah_swil += $pelepah_sEST;
                } else {
                    $qcancakmua[$key][$key1]['pokok_sample'] = 0;
                    $qcancakmua[$key][$key1]['check2'] = 0;
                    $qcancakmua[$key][$key1]['ha_sample'] =  0;
                    $qcancakmua[$key][$key1]['jumlah_panen'] = 0;
                    $qcancakmua[$key][$key1]['akp_rl'] =  0;

                    $qcancakmua[$key][$key1]['p'] = 0;
                    $qcancakmua[$key][$key1]['k'] = 0;
                    $qcancakmua[$key][$key1]['tgl'] = 0;

                    // $qcancakmua[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcancakmua[$key][$key1]['brd/jjgest'] = 0;
                    $qcancakmua[$key][$key1]['buah/jjg'] = 0;
                    // data untuk buah tinggal
                    $qcancakmua[$key][$key1]['bhts_s'] = 0;
                    $qcancakmua[$key][$key1]['bhtm1'] = 0;
                    $qcancakmua[$key][$key1]['bhtm2'] = 0;
                    $qcancakmua[$key][$key1]['bhtm3'] = 0;
                    $qcancakmua[$key][$key1]['palepah_pokok'] = 0;
                    // total skor akhir
                    $qcancakmua[$key][$key1]['skor_bh'] =  0;
                    $qcancakmua[$key][$key1]['skor_brd'] = 0;
                    $qcancakmua[$key][$key1]['skor_ps'] = 0;
                    $qcancakmua[$key][$key1]['skor_akhir'] = 0;
                }
                $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
                $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

                if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                    $akpWil = 0;
                } else {

                    $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
                }

                if ($totalPKTwil != 0) {
                    $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
                } else {
                    $brdPerwil = 0;
                }

                // dd($sumBHEst);
                if ($sumBHWil != 0) {
                    $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
                } else {
                    $sumPerBHWil = 0;
                }

                if ($pokok_panenWil != 0) {
                    $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
                } else {
                    $perPiWil = 0;
                }

                $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                };

                // $totalWil = $skor_bh + $skor_brd + $skor_ps;
                $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);
                $qcancakmua[$key]['PT.MUA'] = [
                    'pokok_sample' => $pokok_panenWil,
                    'check_data' => $data,
                    'ha_sample' =>  $jum_haWil,
                    'jumlah_panen' => $janjang_panenWil,
                    'akp_rl' =>  $akpWil,
                    'p' => $p_panenWil,
                    'k' => $k_panenWil,
                    'tgl' => $brtgl_panenWil,
                    'total_brd' => $totalPKTwil,
                    'total_brd' => $skor_bTinggal,
                    'brd/jjgwil' => $brdPerwil,
                    'buah/jjgwil' => $sumPerBHWil,
                    'bhts_s' => $bhts_panenWil,
                    'bhtm1' => $bhtm1_panenWil,
                    'bhtm2' => $bhtm2_panenWil,
                    'bhtm3' => $bhtm3_oanenWil,
                    'total_buah' => $sumBHWil,
                    'total_buah_per' => $sumPerBHWil,
                    'jjgperBuah' => number_format($sumPerBH, 3),
                    'palepah_pokok' => $pelepah_swil,
                    'palepah_per' => $perPiWil,
                    'skor_bh' => skor_buah_Ma($sumPerBHWil),
                    'skor_brd' => skor_brd_ma($brdPerwil),
                    'skor_ps' => skor_palepah_ma($perPiWil),
                    'skor_akhir' => $totalWil,
                ];
            }
            // dd($qcancakmua);
            foreach ($qcancakmua as $key => $value) {
                $qcancakmua = $value;
            }

            $defaultMTbuahmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultMTbuahmua[$est['est']][$afd['est']]['null'] = 0;
                    }
                }
            }
            $mutuBuahMergemua = array();
            foreach ($defaultMTbuahmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTBuah)) {
                        if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                            if (!empty($dataMTBuah[$estKey][$afdKey])) {
                                $mutuBuahMergemua[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                            } else {
                                $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtBuahWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuBuahMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtBuahWIltab1mua[$value['wil']][$key2] = array_merge($mtBuahWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            $qcbuahmua = array();
            foreach ($mtBuahWIltab1mua as $key => $value) if (is_array($value)) {
                $jum_haWil = 0;
                $sum_SamplejjgWil = 0;
                $sum_bmtWil = 0;
                $sum_bmkWil = 0;
                $sum_overWil = 0;
                $sum_abnorWil = 0;
                $sum_kosongjjgWil = 0;
                $sum_vcutWil = 0;
                $sum_krWil = 0;
                $no_Vcutwil = 0;

                foreach ($value as $key1 => $value1) if (is_array($value1)) {
                    $jum_haEst  = 0;
                    $sum_SamplejjgEst = 0;
                    $sum_bmtEst = 0;
                    $sum_bmkEst = 0;
                    $sum_overEst = 0;
                    $sum_abnorEst = 0;
                    $sum_kosongjjgEst = 0;
                    $sum_vcutEst = 0;
                    $sum_krEst = 0;
                    $no_VcutEst = 0;

                    foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                        $sum_bmt = 0;
                        $sum_bmk = 0;
                        $sum_over = 0;
                        $dataBLok = 0;
                        $sum_Samplejjg = 0;
                        $PerMth = 0;
                        $PerMsk = 0;
                        $PerOver = 0;
                        $sum_abnor = 0;
                        $sum_kosongjjg = 0;
                        $Perkosongjjg = 0;
                        $sum_vcut = 0;
                        $PerVcut = 0;
                        $PerAbr = 0;
                        $sum_kr = 0;
                        $total_kr = 0;
                        $per_kr = 0;
                        $totalSkor = 0;
                        $jum_ha = 0;
                        $no_Vcut = 0;
                        $jml_mth = 0;
                        $jml_mtg = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = [];
                        $dtBlok = 0;
                        // $combination_counts = array();
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                            $dtBlok = count($listBlokPerAfd);

                            // $jum_ha = count($listBlokPerAfd);
                            $sum_bmt += $value3['bmt'];
                            $sum_bmk += $value3['bmk'];
                            $sum_over += $value3['overripe'];
                            $sum_kosongjjg += $value3['empty_bunch'];
                            $sum_vcut += $value3['vcut'];
                            $sum_kr += $value3['alas_br'];


                            $sum_Samplejjg += $value3['jumlah_jjg'];
                            $sum_abnor += $value3['abnormal'];
                        }

                        // $dataBLok = count($combination_counts);
                        $dataBLok = $dtBlok;
                        $jml_mth = ($sum_bmt + $sum_bmk);
                        $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 3);
                        } else {
                            $total_kr = 0;
                        }


                        $per_kr = round($total_kr * 100, 3);
                        if ($jml_mth != 0) {
                            $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMth = 0;
                        }
                        if ($jml_mtg != 0) {
                            $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMsk = 0;
                        }
                        if ($sum_over != 0) {
                            $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerOver = 0;
                        }
                        if ($sum_kosongjjg != 0) {
                            $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $Perkosongjjg = 0;
                        }
                        if ($sum_vcut != 0) {
                            $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerVcut = 0;
                        }

                        if ($sum_abnor != 0) {
                            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerAbr = 0;
                        }

                        $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                        if (!empty($nonZeroValues)) {
                            $qcbuahmua[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcbuahmua[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                            // $qcbuahmua[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                            // $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                            // $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                            // $qcbuahmua[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                        } else {
                            $qcbuahmua[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcbuahmua[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                        }

                        // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                        $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                        $qcbuahmua[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                        $qcbuahmua[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                        $qcbuahmua[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                        $qcbuahmua[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                        $qcbuahmua[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                        $qcbuahmua[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                        $qcbuahmua[$key][$key1][$key2]['total_over'] = $sum_over;
                        $qcbuahmua[$key][$key1][$key2]['total_perOver'] = $PerOver;
                        $qcbuahmua[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                        $qcbuahmua[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                        $qcbuahmua[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                        $qcbuahmua[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                        $qcbuahmua[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                        $qcbuahmua[$key][$key1][$key2]['perVcut'] = $PerVcut;

                        $qcbuahmua[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                        $qcbuahmua[$key][$key1][$key2]['total_kr'] = $total_kr;
                        $qcbuahmua[$key][$key1][$key2]['persen_kr'] = $per_kr;

                        // skoring
                        $qcbuahmua[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                        $qcbuahmua[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                        $qcbuahmua[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                        $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                        $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                        $qcbuahmua[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                        $qcbuahmua[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                        //perhitungan estate
                        $jum_haEst += $dataBLok;
                        $sum_SamplejjgEst += $sum_Samplejjg;
                        $sum_bmtEst += $jml_mth;
                        $sum_bmkEst += $jml_mtg;
                        $sum_overEst += $sum_over;
                        $sum_abnorEst += $sum_abnor;
                        $sum_kosongjjgEst += $sum_kosongjjg;
                        $sum_vcutEst += $sum_vcut;
                        $sum_krEst += $sum_kr;
                    } else {
                        $qcbuahmua[$key][$key1][$key2]['tph_baris_blok'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['sampleJJG_total'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_mentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perMentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_masak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perMasak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_over'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perOver'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_abnormal'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['perAbnormal'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_jjgKosong'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_vcut'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['perVcut'] = 0;

                        $qcbuahmua[$key][$key1][$key2]['jum_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['persen_kr'] = 0;

                        // skoring
                        $qcbuahmua[$key][$key1][$key2]['skor_mentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_masak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_over'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_abnormal'] = 0;;
                        $qcbuahmua[$key][$key1][$key2]['skor_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                    }
                    $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                    if ($sum_krEst != 0) {
                        $total_krEst = round($sum_krEst / $jum_haEst, 3);
                    } else {
                        $total_krEst = 0;
                    }
                    // if ($sum_kr != 0) {
                    //     $total_kr = round($sum_kr / $dataBLok, 3);
                    // } else {
                    //     $total_kr = 0;
                    // }

                    if ($sum_bmtEst != 0) {
                        $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMthEst = 0;
                    }

                    if ($sum_bmkEst != 0) {
                        $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMskEst = 0;
                    }

                    if ($sum_overEst != 0) {
                        $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerOverEst = 0;
                    }
                    if ($sum_kosongjjgEst != 0) {
                        $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerkosongjjgEst = 0;
                    }
                    if ($sum_vcutEst != 0) {
                        $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerVcutest = 0;
                    }
                    if ($sum_abnorEst != 0) {
                        $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerAbrest = 0;
                    }
                    // $per_kr = round($sum_kr * 100);
                    $per_krEst = round($total_krEst * 100, 3);


                    $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                    if (!empty($nonZeroValues)) {
                        $qcbuahmua[$key][$key1]['check_data'] = 'ada';
                        // $qcbuahmua[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                        // $qcbuahmua[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                        // $qcbuahmua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                        // $qcbuahmua[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                        // $qcbuahmua[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                    } else {
                        $qcbuahmua[$key][$key1]['check_data'] = 'kosong';
                        // $qcbuahmua[$key][$key1]['skor_masak'] = $skor_masak = 0;
                        // $qcbuahmua[$key][$key1]['skor_over'] = $skor_over = 0;
                        // $qcbuahmua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $qcbuahmua[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                        // $qcbuahmua[$key][$key1]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                    $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                    $qcbuahmua[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                    $qcbuahmua[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                    $qcbuahmua[$key][$key1]['total_mentah'] = $sum_bmtEst;
                    $qcbuahmua[$key][$key1]['total_perMentah'] = $PerMthEst;
                    $qcbuahmua[$key][$key1]['total_masak'] = $sum_bmkEst;
                    $qcbuahmua[$key][$key1]['total_perMasak'] = $PerMskEst;
                    $qcbuahmua[$key][$key1]['total_over'] = $sum_overEst;
                    $qcbuahmua[$key][$key1]['total_perOver'] = $PerOverEst;
                    $qcbuahmua[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                    $qcbuahmua[$key][$key1]['total_perabnormal'] = $PerAbrest;
                    $qcbuahmua[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                    $qcbuahmua[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                    $qcbuahmua[$key][$key1]['total_vcut'] = $sum_vcutEst;
                    $qcbuahmua[$key][$key1]['perVcut'] = $PerVcutest;
                    $qcbuahmua[$key][$key1]['jum_kr'] = $sum_krEst;
                    $qcbuahmua[$key][$key1]['kr_blok'] = $total_krEst;

                    $qcbuahmua[$key][$key1]['persen_kr'] = $per_krEst;

                    // skoring
                    $qcbuahmua[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                    $qcbuahmua[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                    $qcbuahmua[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                    $qcbuahmua[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                    $qcbuahmua[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                    $qcbuahmua[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                    $qcbuahmua[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                    //hitung perwilayah
                    $jum_haWil += $jum_haEst;
                    $sum_SamplejjgWil += $sum_SamplejjgEst;
                    $sum_bmtWil += $sum_bmtEst;
                    $sum_bmkWil += $sum_bmkEst;
                    $sum_overWil += $sum_overEst;
                    $sum_abnorWil += $sum_abnorEst;
                    $sum_kosongjjgWil += $sum_kosongjjgEst;
                    $sum_vcutWil += $sum_vcutEst;
                    $sum_krWil += $sum_krEst;
                } else {
                    $qcbuahmua[$key][$key1]['tph_baris_blok'] = 0;
                    $qcbuahmua[$key][$key1]['sampleJJG_total'] = 0;
                    $qcbuahmua[$key][$key1]['total_mentah'] = 0;
                    $qcbuahmua[$key][$key1]['total_perMentah'] = 0;
                    $qcbuahmua[$key][$key1]['total_masak'] = 0;
                    $qcbuahmua[$key][$key1]['total_perMasak'] = 0;
                    $qcbuahmua[$key][$key1]['total_over'] = 0;
                    $qcbuahmua[$key][$key1]['total_perOver'] = 0;
                    $qcbuahmua[$key][$key1]['total_abnormal'] = 0;
                    $qcbuahmua[$key][$key1]['total_perabnormal'] = 0;
                    $qcbuahmua[$key][$key1]['total_jjgKosong'] = 0;
                    $qcbuahmua[$key][$key1]['total_perKosongjjg'] = 0;
                    $qcbuahmua[$key][$key1]['total_vcut'] = 0;
                    $qcbuahmua[$key][$key1]['perVcut'] = 0;
                    $qcbuahmua[$key][$key1]['jum_kr'] = 0;
                    $qcbuahmua[$key][$key1]['kr_blok'] = 0;
                    $qcbuahmua[$key][$key1]['persen_kr'] = 0;

                    // skoring
                    $qcbuahmua[$key][$key1]['skor_mentah'] = 0;
                    $qcbuahmua[$key][$key1]['skor_masak'] = 0;
                    $qcbuahmua[$key][$key1]['skor_over'] = 0;
                    $qcbuahmua[$key][$key1]['skor_jjgKosong'] = 0;
                    $qcbuahmua[$key][$key1]['skor_vcut'] = 0;
                    $qcbuahmua[$key][$key1]['skor_abnormal'] = 0;;
                    $qcbuahmua[$key][$key1]['skor_kr'] = 0;
                    $qcbuahmua[$key][$key1]['TOTAL_SKOR'] = 0;
                }

                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }



                if ($sum_krWil != 0) {
                    $total_krWil = round($sum_krWil / $jum_haWil, 3);
                } else {
                    $total_krWil = 0;
                }

                if ($sum_bmtWil != 0) {
                    $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMthWil = 0;
                }


                if ($sum_bmkWil != 0) {
                    $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMskWil = 0;
                }
                if ($sum_overWil != 0) {
                    $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerOverWil = 0;
                }
                if ($sum_kosongjjgWil != 0) {
                    $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerkosongjjgWil = 0;
                }
                if ($sum_vcutWil != 0) {
                    $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerVcutWil = 0;
                }
                if ($sum_abnorWil != 0) {
                    $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerAbrWil = 0;
                }
                $per_krWil = round($total_krWil * 100, 3);

                $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                }


                $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
                $qcbuahmua[$key]['PT.MUA'] = [
                    'check_data' => $data,
                    'tph_baris_blok' => $jum_haWil,
                    'sampleJJG_total' => $sum_SamplejjgWil,
                    'total_mentah' => $sum_bmtWil,
                    'total_perMentah' => $PerMthWil,
                    'total_masak' => $sum_bmkWil,
                    'total_perMasak' => $PerMskWil,
                    'total_over' => $sum_overWil,
                    'total_perOver' => $PerOverWil,
                    'total_abnormal' => $sum_abnorWil,
                    'total_perabnormal' => $PerAbrWil,
                    'total_jjgKosong' => $sum_kosongjjgWil,
                    'total_perKosongjjg' => $PerkosongjjgWil,
                    'total_vcut' => $sum_vcutWil,
                    'per_vcut' => $PerVcutWil,
                    'jum_kr' => $sum_krWil,
                    'kr_blok' => $total_krWil,
                    'persen_kr' => $per_krWil,
                    'skor_mentah' => skor_buah_mentah_mb($PerMthWil),
                    'skor_masak' => skor_buah_masak_mb($PerMskWil),
                    'skor_over' => skor_buah_over_mb($PerOverWil),
                    'skor_jjgKosong' => skor_jangkos_mb($PerkosongjjgWil),
                    'skor_vcut' => skor_vcut_mb($PerVcutWil),
                    'skor_kr' => skor_abr_mb($per_krWil),
                    'TOTAL_SKOR' => $totalSkorWil,
                ];
            }
            foreach ($qcbuahmua as $key => $value) {
                # code...
                $qcbuahmua = $value;
            }

            $defaultMtTransmua = array();
            foreach ($muaest as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defaultMtTransmua[$est['est']][$afd['est']]['null'] = 0;
                    }
                }
            }
            $mutuAncakMergemua = array();
            foreach ($defaultMtTransmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTTrans)) {
                        if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                            if (!empty($dataMTTrans[$estKey][$afdKey])) {
                                $mutuAncakMergemua[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                            } else {
                                $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtTransWiltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuAncakMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtTransWiltab1mua[$value['wil']][$key2] = array_merge($mtTransWiltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            // dd($mtTransWiltab1mua);
            $qctransmua = array();
            foreach ($mtTransWiltab1mua as $key => $value) if (!empty($value)) {
                $dataBLokWil = 0;
                $sum_btWil = 0;
                $sum_rstWil = 0;
                foreach ($value as $key1 => $value1) if (!empty($value1)) {
                    $dataBLokEst = 0;
                    $sum_btEst = 0;
                    $sum_rstEst = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            // }
                            $dataBLok = count($listBlokPerAfd);
                            $sum_bt += $value3['bt'];
                            $sum_rst += $value3['rst'];
                        }
                        $tot_sample = 0;  // Define the variable outside of the foreach loop


                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        };

                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        };


                        $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                        if (!empty($nonZeroValues)) {
                            $qctransmua[$key][$key1][$key2]['check_data'] = 'ada';
                        } else {
                            $qctransmua[$key][$key1][$key2]['check_data'] = "kosong";
                        }
                        // dd($transNewdata);




                        $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        $qctransmua[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        $qctransmua[$key][$key1][$key2]['total_brd'] = $sum_bt;
                        $qctransmua[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                        $qctransmua[$key][$key1][$key2]['total_buah'] = $sum_rst;
                        $qctransmua[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                        $qctransmua[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                        $dataBLokEst += $dataBLok;
                        $sum_btEst += $sum_bt;
                        $sum_rstEst += $sum_rst;

                        if ($dataBLokEst != 0) {
                            $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                        } else {
                            $brdPertphEst = 0;
                        }

                        if ($dataBLokEst != 0) {
                            $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                        } else {
                            $buahPerTPHEst = 0;
                        }

                        // dd($qctransmua);
                        $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                    } else {
                        $qctransmua[$key][$key1][$key2]['tph_sample'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_brd'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_brd/TPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_buah'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['skor_brdPertph'] = 0;
                        $qctransmua[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['totalSkor'] = 0;
                    }

                    $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                    if (!empty($nonZeroValues)) {
                        $qctransmua[$key][$key1]['check_data'] = 'ada';
                        // $qctransmua[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                    } else {
                        $qctransmua[$key][$key1]['check_data'] = 'kosong';
                        // $qctransmua[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                    }

                    // $totalSkorEst = $skor_brd + $skor_buah ;


                    $qctransmua[$key][$key1]['tph_sample'] = $dataBLokEst;
                    $qctransmua[$key][$key1]['total_brd'] = $sum_btEst;
                    $qctransmua[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                    $qctransmua[$key][$key1]['total_buah'] = $sum_rstEst;
                    $qctransmua[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                    $qctransmua[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                    $qctransmua[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                    $qctransmua[$key][$key1]['totalSkor'] = $totalSkorEst;

                    //perhitungan per wil
                    $dataBLokWil += $dataBLokEst;
                    $sum_btWil += $sum_btEst;
                    $sum_rstWil += $sum_rstEst;

                    if ($dataBLokWil != 0) {
                        $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                    } else {
                        $brdPertphWil = 0;
                    }
                    if ($dataBLokWil != 0) {
                        $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                    } else {
                        $buahPerTPHWil = 0;
                    }

                    $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
                } else {
                    $qctransmua[$key][$key1]['tph_sample'] = 0;
                    $qctransmua[$key][$key1]['total_brd'] = 0;
                    $qctransmua[$key][$key1]['total_brd/TPH'] = 0;
                    $qctransmua[$key][$key1]['total_buah'] = 0;
                    $qctransmua[$key][$key1]['total_buahPerTPH'] = 0;
                    $qctransmua[$key][$key1]['skor_brdPertph'] = 0;
                    $qctransmua[$key][$key1]['skor_buahPerTPH'] = 0;
                    $qctransmua[$key][$key1]['totalSkor'] = 0;
                }

                $nonZeroValues = array_filter([$sum_btWil, $sum_rstWil]);


                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                }
                $qctransmua[$key]['PT.MUA'] = [
                    'check_data' => $data,
                    'tph_sample' => $dataBLokWil,
                    'total_brd' => $sum_btWil,
                    'total_brd/TPH' => $brdPertphWil,
                    'total_buah' => $sum_rstWil,
                    'total_buahPerTPH' => $buahPerTPHWil,
                    'skor_brdPertph' =>   skor_brd_tinggal($brdPertphWil),
                    'skor_buahPerTPH' => skor_buah_tinggal($buahPerTPHWil),
                    'totalSkor' => $totalSkorWil,
                ];
            }

            foreach ($qctransmua as $key => $value) {
                $qctransmua = $value;
            }
            // dd($qcancakmua, $qcbuahmua, $qctransmua);

            $qcinspeksimua = [];
            foreach ($qcancakmua as $key => $value) {
                foreach ($qcbuahmua as $key1 => $value1) {
                    foreach ($qctransmua as $key2 => $value2)   if (
                        $key == $key1
                        && $key == $key2
                    ) {
                        if ($value['check_data'] == 'kosong' && $value1['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                            $qcinspeksimua[$key]['TotalSkor'] = '-';
                            $qcinspeksimua[$key]['checkdata'] = 'kosong';
                        } else {
                            $qcinspeksimua[$key]['TotalSkor'] = $value['skor_akhir'] + $value1['TOTAL_SKOR'] + $value2['totalSkor'];
                            $qcinspeksimua[$key]['checkdata'] = 'ada';
                        }

                        $qcinspeksimua[$key]['est'] = $key;
                        $qcinspeksimua[$key]['afd'] = 'OA';
                    }
                }
            }

            $rekapmua = [];
            foreach ($qcinspeksimua as $key => $value) {
                if (
                    isset($sidak_buah_mua[$key]) &&
                    isset($newSidak_mua[$key])
                ) {
                    $valtph2 = $newSidak_mua[$key];
                    $valmtb = $sidak_buah_mua[$key];
                    $skortph = $valtph2['score_estate'] ?? null;
                    $skormtb = $valmtb['All_skor'] ?? null;

                    if ($valmtb['check_arr'] == 'ada') {
                        $databh = 1;
                    } else {
                        $databh = 0;
                    }
                    if ($value['checkdata'] == 'ada') {
                        $dataqc = 1;
                    } else {
                        $dataqc = 0;
                    }
                    if ($valtph2['checkdata'] == 'ada') {
                        $datatph = 1;
                    } else {
                        $datatph = 0;
                    }
                    // dd($key);
                    foreach ($queryAsisten as $keyx => $valuex) {
                        if ($valuex['est'] === $key && $valuex['afd'] === 'OA') {
                            $rekapmua[$key]['asistenafd'] = $valuex['nama'] ?? '-';
                            break;
                        } elseif ($valuex['est'] === $key && $valuex['afd'] === 'EM') {
                            $rekapmua[$key]['manager'] = $valuex['nama'] ?? '-';
                        }
                    }


                    $check = $databh + $dataqc + $datatph;
                    $rekapmua[$key]['skorqc'] = $value['TotalSkor'];
                    // $rekapmua[$key]['nama'] = $value['TotalSkor'];
                    $rekapmua[$key]['skor_mutubuah'] = $skormtb;
                    $rekapmua[$key]['skortph'] = $skortph;
                    $rekapmua[$key]['check'] = $check;

                    $a = $value['TotalSkor'];
                    $b = $skormtb;
                    $c = $skortph;

                    // Convert '-' to 0, keeping other values unchanged
                    $a = ($a === '-') ? 0 : $a;
                    $b = ($b === '-') ? 0 : $b;
                    $c = ($c === '-') ? 0 : $c;

                    $rekapmua[$key]['skorestate'] = round(($a + $b + $c) / $check, 2);
                }
            }
            // dd($qcinspeksimua, $sidak_buah_mua, $newSidak_mua, $rekapmua);
            // dd($rekapmua);

            // foreach ($rekapmua as $key => $value) {
            //     # code...
            // }
        } else {
            $rekapmua = [];
        }


        // dd($qcinspeksimua, $sidak_buah_mua, $newSidak_mua, $rekapmua);
        // dd($rekapmua);
        // ending ================================================================
        // dd($qcinspeksi, $sidaktph, $mutu_buah);

        $rekapafd = [];
        foreach ($qcinspeksi as $keyqc => $valqc) {
            foreach ($valqc as $keyqc1 => $valqc1) {
                if (is_array($valqc1)) {

                    foreach ($valqc1 as $keyqc2 => $valqc2) {
                        $datacheck = [];
                        $datacheck2 = [];
                        $countAda = 0;
                        $countAda2 = 0;
                        $totalest = 0;

                        if (is_array($valqc2)) {
                            if (
                                isset($sidaktph[$keyqc][$keyqc1][$keyqc2]) &&
                                isset($mutu_buah[$keyqc][$keyqc1][$keyqc2])
                            ) {
                                $valtph2 = $sidaktph[$keyqc][$keyqc1][$keyqc2];
                                $valbh2 = $mutu_buah[$keyqc][$keyqc1][$keyqc2];
                                $valtph1 = $sidaktph[$keyqc][$keyqc1];
                                $valbh1 = $mutu_buah[$keyqc][$keyqc1];
                                // Extracting values
                                $skor_tph = $valtph2['total_score'] ?? null;
                                $skor_qc = $valqc2['TotalSkor'] ?? null;
                                $skor_buah = $valbh2['All_skor'] ?? null;
                                $tph_check = $valtph2['v2check4'];
                                $qc_check = $valqc2['data'] ?? 'ada';
                                $buah_check = $valbh2['csfxr'];

                                // dd($valtph2);

                                if ($tph_check != 0 && $skor_tph != 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } elseif ($tph_check != 0 && $skor_tph == 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } else {
                                    $tph = 'kosong';
                                    $tphskor = 0;
                                }

                                if ($buah_check != 0 && $skor_buah != 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } elseif ($buah_check != 0 && $skor_buah == 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } else {
                                    $buah = 'kosong';
                                    $buahskor = 0;
                                }
                                if ($qc_check != 'kosong' && $skor_qc != 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } elseif ($qc_check != 'kosong' && $skor_qc == 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } else {
                                    $qc = 'kosong';
                                    $qcskor = 0;
                                }

                                $datacheck[] = [$tph, $qc, $buah];
                                foreach ($datacheck[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda++;
                                    }
                                }
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === $keyqc2) {
                                    $rekapafd[$keyqc][$keyqc1][$keyqc2]['nama'] = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['tph_check'] = $tph;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['bgcolor'] = 'white';
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_tph'] = $tphskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['qc_check'] = $qc;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_qc'] = $qcskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['buah_check'] = $buah;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_buah'] = $buahskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['datacheck'] = $datacheck;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['validasi'] = $countAda;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['est'] = $keyqc1;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['afd'] = $keyqc2;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['total'] = $countAda !== 0 ? round(($tphskor + $qcskor + $buahskor) / $countAda, 2) : 0;

                                // dd($valbh1);

                                $skor_tph2 = $valtph1['score_estate'] ?? null;
                                $skor_qc2 = $valqc1['TotalSkorEST'] ?? null;
                                $skor_buah2 = $valbh1['All_skor'] ?? null;
                                $tph_check2 = $valtph1['v2check5'];
                                $qc_check2 = $valqc1['data'] ?? 'ada';
                                $buah_check2 = $valbh1['csrms'];


                                if ($tph_check2 != 0 && $skor_tph2 != 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } elseif ($tph_check2 != 0 && $skor_tph2 == 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } else {
                                    $tph2 = 'kosong';
                                    $tphskor2 = 0;
                                }

                                if ($buah_check2 != 0 && $skor_buah2 != 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } elseif ($buah_check2 != 0 && $skor_buah2 == 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } else {
                                    $buah2 = 'kosong';
                                    $buahskor2 = 0;
                                }
                                if ($qc_check2 != 'kosong' && $skor_qc2 != 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } elseif ($qc_check2 != 'kosong' && $skor_qc2 == 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } else {
                                    $qc2 = 'kosong';
                                    $qcskor2 = 0;
                                }

                                $datacheck2[] = [$tph2, $qc2, $buah2];
                                foreach ($datacheck2[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda2++;
                                    }
                                }
                                $namaqc = '-';
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === 'EM') {
                                    $namaqc = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $totalest = $countAda2 !== 0 ? round(($tphskor2 + $qcskor2 + $buahskor2) / $countAda2, 2) : 0;

                                // Assuming $estate is an individual entry and not part of $estates array for sorting
                                $estate = [
                                    'tph_check' => $tph2,
                                    'nama' => $namaqc,
                                    'bgcolor' => '#a0978d',
                                    'skor_tph' => $tphskor2,
                                    'qc_check' => $qc2,
                                    'skor_qc' => $qcskor2,
                                    'buah_check' => $buah2,
                                    'skor_buah' => $buahskor2,
                                    'validasi' => $countAda2,
                                    'est' => $keyqc1,
                                    'afd' => 'EM',
                                    'total' => $totalest
                                    // 'rank' => ???
                                ];

                                $totale = $totalest;
                            }
                        }
                    }
                    $rekapafd[$keyqc][$keyqc1]['est'] = $estate;
                    $getallest[] = $estate;
                }
            }
        }

        foreach ($rekapafd as $key => $value) {
            $estTotals = []; // Initialize an array to hold 'est' totals within this index

            foreach ($value as $estKey => $estValue) {
                if (isset($estValue['est'])) {
                    $est = $estValue['est'];
                    $afdElements = $estValue;
                    unset($afdElements['est']);

                    $totalsAfd = [];
                    foreach ($afdElements as $afdKey => $afdValue) {
                        $totalsAfd[$afdKey] = $afdValue['total'];
                    }

                    arsort($totalsAfd);

                    $rank = 1;
                    foreach ($totalsAfd as $afdKey => $totalAfd) {
                        $rekapafd[$key][$estKey][$afdKey]['rank'] = $rank;
                        $rank++;
                    }

                    // Accumulate 'est' totals within this index
                    $estTotals[$estKey] = $est['total'];
                }
            }

            // Sort 'est' totals within this index
            arsort($estTotals);

            // Assign ranks to each 'est' element within this index based on the sorted order of totals
            $rank = 1;
            foreach ($estTotals as $estKey => $totalEst) {
                $rekapafd[$key][$estKey]['est']['rank'] = $rank;
                $rank++;
            }
        }

        // dd($rekapafd);
        // dd($sidak_buah_mua, $newSidak_mua, $qcinspeksimua);

        // dd($rekapafd);
        $arr = array();
        $arr['rekapafd'] = $rekapafd;
        $arr['rekapmua'] = $rekapmua;

        echo json_encode($arr);
        exit();

        // dd($rekap, $sidaktph, $mutu_buah, $qcinspeksi);
    }

    public function getdatayear(Request $request)
    {
        // $regional = 1;
        // $tahun = 2024;

        $regional = $request->input('reg');
        $tahun = $request->input('bulan');

        // dd($tahun, $regional);
        $queryAsisten = DB::connection('mysql2')->table('asisten_qc')
            ->select('asisten_qc.*')
            ->get();
        $queryAsisten = json_decode($queryAsisten, true);
        // dd($value2['datetime'], $endDate);
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $queryEste = json_decode($queryEste, true);

        $defafd = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*', 'estate.*', 'afdeling.nama as afdnama')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $defafd = $defafd->groupBy(['wil', 'est', 'afdnama']);
        $defafd = json_decode($defafd, true);
        $defafdmua = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*', 'estate.*', 'afdeling.nama as afdnama')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->whereIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $defafdmua = $defafdmua->groupBy(['wil', 'est', 'est']);
        $defafdmua = json_decode($defafdmua, true);

        // dd($defafdmua);

        $muaest = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            // ->where('estate.emp', '!=', 1)
            ->whereIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get('est');
        $muaest = json_decode($muaest, true);

        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);


        $data = [];
        $chunkSize = 1000;

        DB::connection('mysql2')->table('sidak_mutu_buah')
            ->select(
                "sidak_mutu_buah.*",
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%Y") as tahun')
            )
            ->join('estate', 'estate.est', '=', 'sidak_mutu_buah.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->chunk($chunkSize, function ($results) use (&$data) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $data[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $data = collect($data)->groupBy(['estate', 'afdeling']);
        $queryMTbuah = json_decode($data, true);


        // dd($queryMTbuah);
        $databulananBuah = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $keytph => $value3) {

                    $databulananBuah[$key][$key2][$keytph] = $value3;
                }
            }
        }

        $defPerbulanWil = array();

        foreach ($queryEste as $key2 => $value2) {
            foreach ($queryAfd as $key3 => $value3) {
                if ($value2['est'] == $value3['est']) {
                    $defPerbulanWil[$value2['est']][$value3['nama']] = 0;
                }
            }
        }



        foreach ($defPerbulanWil as $estateKey => $afdelingArray) {
            foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                    $defPerbulanWil[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                }
            }
        }

        $sidak_buah = array();
        // dd($defPerbulanWil);

        foreach ($defPerbulanWil as $key => $value) {
            $jjg_samplex = 0;
            $tnpBRDx = 0;
            $krgBRDx = 0;
            $abrx = 0;
            $overripex = 0;
            $emptyx = 0;
            $vcutx = 0;
            $rdx = 0;
            $dataBLokx = 0;
            $sum_krx = 0;
            $csrms = 0;
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    $jjg_sample = 0;
                    $tnpBRD = 0;
                    $krgBRD = 0;
                    $abr = 0;
                    $skor_total = 0;
                    $overripe = 0;
                    $empty = 0;
                    $vcut = 0;
                    $rd = 0;
                    $sum_kr = 0;
                    $allSkor = 0;
                    $combination_counts = array();
                    $newblok = 0;
                    $csfxr = count($value1);
                    foreach ($value1 as $key2 => $value2) {
                        $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $newblok = count($value1);
                        $jjg_sample += $value2['jumlah_jjg'];
                        $tnpBRD += $value2['bmt'];
                        $krgBRD += $value2['bmk'];
                        $abr += $value2['abnormal'];
                        $overripe += $value2['overripe'];
                        $empty += $value2['empty_bunch'];
                        $vcut += $value2['vcut'];
                        $rd += $value2['rd'];
                        $sum_kr += $value2['alas_br'];
                    }
                    // $dataBLok = count($combination_counts);
                    $dataBLok = $newblok;
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }
                    $per_kr = round($total_kr * 100, 2);
                    $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                    $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                    $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                    $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                    $sidak_buah[$key][$key1]['blok'] = $dataBLok;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = $tnpBRD;
                    $sidak_buah[$key][$key1]['krg_brd'] = $krgBRD;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = $skor_total;
                    $sidak_buah[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                    $sidak_buah[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                    $sidak_buah[$key][$key1]['lewat_matang'] = $overripe;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                    $sidak_buah[$key][$key1]['janjang_kosong'] = $empty;
                    $sidak_buah[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                    $sidak_buah[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                    $sidak_buah[$key][$key1]['vcut'] = $vcut;
                    $sidak_buah[$key][$key1]['karung'] = $sum_kr;
                    $sidak_buah[$key][$key1]['vcut_persen'] = $skor_vcut;
                    $sidak_buah[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                    $sidak_buah[$key][$key1]['abnormal'] = $abr;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['rat_dmg'] = $rd;
                    $sidak_buah[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['TPH'] = $total_kr;
                    $sidak_buah[$key][$key1]['persen_krg'] = $per_kr;
                    $sidak_buah[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                    $sidak_buah[$key][$key1]['All_skor'] = $allSkor;
                    $sidak_buah[$key][$key1]['csfxr'] = $csfxr;
                    $sidak_buah[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                    $jjg_samplex += $jjg_sample;
                    $tnpBRDx += $tnpBRD;
                    $krgBRDx += $krgBRD;
                    $abrx += $abr;
                    $overripex += $overripe;
                    $emptyx += $empty;
                    $vcutx += $vcut;

                    $rdx += $rd;

                    $dataBLokx += $newblok;
                    $sum_krx += $sum_kr;
                    $csrms += $csfxr;
                } else {

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = 0;
                    $sidak_buah[$key][$key1]['blok'] = 0;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = 0;
                    $sidak_buah[$key][$key1]['krg_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = 0;
                    $sidak_buah[$key][$key1]['total_jjg'] = 0;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = 0;
                    $sidak_buah[$key][$key1]['skor_total'] = 0;
                    $sidak_buah[$key][$key1]['jjg_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = 0;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = 0;
                    $sidak_buah[$key][$key1]['lewat_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  0;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = 0;
                    $sidak_buah[$key][$key1]['janjang_kosong'] = 0;
                    $sidak_buah[$key][$key1]['persen_kosong'] = 0;
                    $sidak_buah[$key][$key1]['skor_kosong'] = 0;
                    $sidak_buah[$key][$key1]['vcut'] = 0;
                    $sidak_buah[$key][$key1]['karung'] = 0;
                    $sidak_buah[$key][$key1]['vcut_persen'] = 0;
                    $sidak_buah[$key][$key1]['vcut_skor'] = 0;
                    $sidak_buah[$key][$key1]['abnormal'] = 0;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = 0;
                    $sidak_buah[$key][$key1]['rat_dmg'] = 0;
                    $sidak_buah[$key][$key1]['rd_persen'] = 0;
                    $sidak_buah[$key][$key1]['TPH'] = 0;
                    $sidak_buah[$key][$key1]['persen_krg'] = 0;
                    $sidak_buah[$key][$key1]['skor_kr'] = 0;
                    $sidak_buah[$key][$key1]['All_skor'] = 0;
                    $sidak_buah[$key][$key1]['kategori'] = 0;
                    $sidak_buah[$key][$key1]['csfxr'] = 0;
                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                }
            }
            if ($sum_krx != 0) {
                $total_kr = round($sum_krx / $dataBLokx, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

            $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

            $sidak_buah[$key]['jjg_mantah'] = $jjg_mth;
            $sidak_buah[$key]['persen_jjgmentah'] = $skor_jjgMTh;

            if ($jjg_samplex == 0 && $tnpBRDx == 0 &&   $krgBRDx == 0 && $abrx == 0 && $overripex == 0 && $emptyx == 0 &&  $vcutx == 0 &&  $rdx == 0 && $sum_krx == 0) {
                $sidak_buah[$key]['check_arr'] = 'kosong';
                $sidak_buah[$key]['All_skor'] = 0;
            } else {
                $sidak_buah[$key]['check_arr'] = 'ada';
                $sidak_buah[$key]['All_skor'] = $allSkor;
            }

            $sidak_buah[$key]['Jumlah_janjang'] = $jjg_samplex;
            $sidak_buah[$key]['csrms'] = $csrms;
            $sidak_buah[$key]['blok'] = $dataBLokx;
            $sidak_buah[$key]['EM'] = 'EM';
            $sidak_buah[$key]['Nama_assist'] = $nama_em;
            $sidak_buah[$key]['nama_staff'] = '-';
            $sidak_buah[$key]['tnp_brd'] = $tnpBRDx;
            $sidak_buah[$key]['krg_brd'] = $krgBRDx;
            $sidak_buah[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
            $sidak_buah[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


            $sidak_buah[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
            $sidak_buah[$key]['persen_totalJjg'] = $skor_total;
            $sidak_buah[$key]['skor_total'] = sidak_brdTotal($skor_total);
            $sidak_buah[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
            $sidak_buah[$key]['persen_jjgMtang'] = $skor_jjgMSk;
            $sidak_buah[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
            $sidak_buah[$key]['lewat_matang'] = $overripex;
            $sidak_buah[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
            $sidak_buah[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
            $sidak_buah[$key]['janjang_kosong'] = $emptyx;
            $sidak_buah[$key]['persen_kosong'] = $skor_jjgKosong;
            $sidak_buah[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
            $sidak_buah[$key]['vcut'] = $vcutx;
            $sidak_buah[$key]['vcut_persen'] = $skor_vcut;
            $sidak_buah[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
            $sidak_buah[$key]['abnormal'] = $abrx;

            $sidak_buah[$key]['rat_dmg'] = $rdx;

            $sidak_buah[$key]['karung'] = $sum_krx;
            $sidak_buah[$key]['TPH'] = $total_kr;
            $sidak_buah[$key]['persen_krg'] = $per_kr;
            $sidak_buah[$key]['skor_kr'] = sidak_PengBRD($per_kr);
            // $sidak_buah[$key]['All_skor'] = $allSkor;
            $sidak_buah[$key]['kategori'] = sidak_akhir($allSkor);
        }


        $mutu_buah = array();
        foreach ($queryEste as $key => $value) {
            foreach ($sidak_buah as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mutu_buah[$value['wil']][$key2] = array_merge($mutu_buah[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // dd($mutu_buah);

        // sidak tph 

        $datatph = [];

        DB::connection('mysql2')->table('sidak_tph')
            ->select(
                "sidak_tph.*",
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'),
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal'),
                DB::raw("
                CASE 
                WHEN status = '' THEN 1
                WHEN status = '0' THEN 1
                WHEN LOCATE('>H+', status) > 0 THEN '8'
                WHEN LOCATE('H+', status) > 0 THEN 
                    CASE 
                        WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1) > 8 THEN '8'
                        ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1)
                    END
                WHEN status REGEXP '^[0-9]+$' AND status > 8 THEN '8'
                WHEN LENGTH(status) > 1 AND status NOT LIKE '%H+%' AND status NOT LIKE '%>H+%' AND LOCATE(',', status) > 0 THEN SUBSTRING_INDEX(status, ',', 1)
                ELSE status
            END AS statuspanen")
            )

            ->join('estate', 'estate.est', '=', 'sidak_tph.est')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->chunk($chunkSize, function ($results) use (&$datatph) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $datatph[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });


        $datatph = collect($datatph)->groupBy(['est', 'afd', 'bulan', 'tanggal', 'statuspanen', 'blok']);
        $ancakFA = json_decode($datatph, true);


        $year = $tahun;
        if ($regional == 3) {
            $months = [];

            for ($month = 1; $month <= 12; $month++) {
                $weeks = [];
                $firstDayOfMonth = strtotime("$year-$month-01");
                $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

                $weekNumber = 1;

                // Find the first Saturday of the month or the last Saturday of the previous month
                $firstSaturday = strtotime("last Saturday", $firstDayOfMonth);

                // Set the start date to the first Saturday
                $startDate = $firstSaturday;

                while ($startDate <= $lastDayOfMonth) {
                    $endDate = strtotime("next Friday", $startDate);
                    if ($endDate > $lastDayOfMonth) {
                        $endDate = $lastDayOfMonth;
                    }

                    $weeks[$weekNumber] = [
                        'start' => date('Y-m-d', $startDate),
                        'end' => date('Y-m-d', $endDate),
                    ];

                    // Update start date to the next Saturday
                    $startDate = strtotime("next Saturday", $endDate);

                    $weekNumber++;
                }

                $monthName = date('F', mktime(0, 0, 0, $month, 1));
                $months[$monthName] = $weeks;
            }

            $weeksdata = $months;
        } else {
            $months = [];
            for ($month = 1; $month <= 12; $month++) {
                $weeks = [];
                $firstDayOfMonth = strtotime("$year-$month-01");
                $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

                $weekNumber = 1;
                $startDate = $firstDayOfMonth;

                while ($startDate <= $lastDayOfMonth) {
                    $endDate = strtotime("next Sunday", $startDate);
                    if ($endDate > $lastDayOfMonth) {
                        $endDate = $lastDayOfMonth;
                    }

                    $weeks[$weekNumber] = [
                        'start' => date('Y-m-d', $startDate),
                        'end' => date('Y-m-d', $endDate),
                    ];

                    $nextMonday = strtotime("next Monday", $endDate);

                    // Check if the next Monday is still within the current month.
                    if (date('m', $nextMonday) == $month) {
                        $startDate = $nextMonday;
                    } else {
                        // If the next Monday is in the next month, break the loop.
                        break;
                    }

                    $weekNumber++;
                }

                $monthName = date('F', mktime(0, 0, 0, $month, 1));
                $months[$monthName] = $weeks;
            }

            $weeksdata = $months;
        }






        // dd($weeksdata);

        $result = [];

        foreach ($ancakFA as $category => $subCategories) {
            foreach ($subCategories as $subCategory => $monthlyData) {
                foreach ($monthlyData as $month => $dailyData) {
                    foreach ($dailyData as $date => $data) {

                        foreach ($weeksdata[$month] as $weekNumber => $week) {
                            if (strtotime($date) >= strtotime($week['start']) && strtotime($date) <= strtotime($week['end'])) {
                                // Create a new entry for the week if not exists
                                // dd('sex');
                                if (!isset($result[$category][$subCategory][$month]['week' . $weekNumber])) {
                                    $result[$category][$subCategory][$month]['week' . $weekNumber] = [];
                                }
                                // Assign data to the corresponding week
                                $result[$category][$subCategory][$month]['week' . $weekNumber] = $data;
                            }
                        }
                    }
                }
            }
        }
        $newSidak = array();

        // dd($result);

        foreach ($result as $key => $value1) {
            $v2check6 = 0;
            $tot_estAFd6 = 0;
            $totskor_brd6 = 0;
            $totskor_janjang6 = 0;
            foreach ($value1 as $key1 => $value2) {
                $total_skoreest = 0;
                $tot_estAFd = 0;
                $totskor_brd2 = 0;
                $totskor_janjang2 = 0;
                $total_estkors = 0;
                $total_skoreafd = 0;

                $deviden = 0;

                $v2check5 = 0;
                $tot_afdscoremonth = 0;
                $devest = count($value1);
                foreach ($value2 as $key2 => $value3) {
                    $tot_afdscore = 0;
                    $totskor_brd1 = 0;
                    $totskor_janjang1 = 0;
                    $total_skoreest = 0;
                    $v2check4 = 0;
                    $devidenmonth = count($value2);
                    foreach ($value3 as $key3 => $value4) {
                        $total_brondolan = 0;
                        $total_janjang = 0;
                        $tod_brd = 0;
                        $tod_jjg = 0;
                        $totskor_brd = 0;
                        $totskor_janjang = 0;
                        $tot_brdxm = 0;
                        $tod_janjangxm = 0;
                        $v2check3 = 0;

                        $deviden = count($value3);
                        foreach ($value4 as $key4 => $value5) {
                            $tph = 0;
                            $jalan = 0;
                            $bin = 0;
                            $karung = 0;
                            $buah = 0;
                            $restan = 0;
                            $v2check = count($value5);
                            foreach ($value5 as $key5 => $value6) {
                                $sum_bt_tph = 0;
                                $sum_bt_jalan = 0;
                                $sum_bt_bin = 0;
                                $sum_jum_karung = 0;
                                $sum_buah_tinggal = 0;
                                $sum_restan_unreported = 0;
                                foreach ($value6 as $key6 => $value7) {
                                    $sum_bt_tph += $value7['bt_tph'];
                                    $sum_bt_jalan += $value7['bt_jalan'];
                                    $sum_bt_bin += $value7['bt_bin'];
                                    $sum_jum_karung += $value7['jum_karung'];


                                    $sum_buah_tinggal += $value7['buah_tinggal'];
                                    $sum_restan_unreported += $value7['restan_unreported'];
                                } # code... dd($value3);

                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['tph'] = $sum_bt_tph;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['jalan'] = $sum_bt_jalan;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['bin'] = $sum_bt_bin;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['karung'] = $sum_jum_karung;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['buah'] = $sum_buah_tinggal;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['restan'] = $sum_restan_unreported;

                                $tph += $sum_bt_tph;
                                $jalan += $sum_bt_jalan;
                                $bin += $sum_bt_bin;
                                $karung += $sum_jum_karung;
                                $buah += $sum_buah_tinggal;
                                $restan += $sum_restan_unreported;
                            } # code... dd($value3);
                            $status_panen = $key4;
                            [$panen_brd, $panen_jjg] = calculatePanen($status_panen);

                            $total_brondolan =  round(($tph + $jalan + $bin + $karung) * $panen_brd / 100, 1);
                            $total_janjang =  round(($buah + $restan) * $panen_jjg / 100, 1);
                            $tod_brd = $tph + $jalan + $bin + $karung;
                            $tod_jjg = $buah + $restan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['tph'] = $tph;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['jalan'] = $jalan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['bin'] = $bin;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['karung'] = $karung;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['tot_brd'] = $tod_brd;

                            $newSidak[$key][$key1][$key2][$key3][$key4]['buah'] = $buah;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['restan'] = $restan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['skor_brd'] = $total_brondolan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['skor_janjang'] = $total_janjang;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['tod_jjg'] = $tod_jjg;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['v2check2'] = $v2check;


                            $totskor_brd += $total_brondolan;
                            $totskor_janjang += $total_janjang;
                            $tot_brdxm += $tod_brd;
                            $tod_janjangxm += $tod_jjg;
                            $v2check3 += $v2check;
                        } # code... dd($value3);
                        $total_estkors = $totskor_brd + $totskor_janjang;
                        if ($total_estkors != 0) {
                            $newSidak[$key][$key1][$key2][$key3]['all_score'] = 100 - ($total_estkors);
                            $newSidak[$key][$key1][$key2][$key3]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else if ($v2check3 != 0) {
                            $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                            $newSidak[$key][$key1][$key2]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else {
                            $newSidak[$key][$key1][$key2][$key3]['all_score'] = 0;
                            $newSidak[$key][$key1][$key2][$key3]['check_data'] = 'null';
                            $total_skoreafd = 0;
                        }
                        // $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak[$key][$key1][$key2][$key3]['total_brd'] = $tot_brdxm;
                        $newSidak[$key][$key1][$key2][$key3]['total_brdSkor'] = $totskor_brd;
                        $newSidak[$key][$key1][$key2][$key3]['total_janjang'] = $tod_janjangxm;
                        $newSidak[$key][$key1][$key2][$key3]['total_janjangSkor'] = $totskor_janjang;
                        $newSidak[$key][$key1][$key2][$key3]['total_skor'] = $total_skoreafd;
                        $newSidak[$key][$key1][$key2][$key3]['janjang_brd'] = $totskor_brd + $totskor_janjang;
                        $newSidak[$key][$key1][$key2][$key3]['v2check3'] = $v2check3;

                        $totskor_brd1 += $totskor_brd;
                        $totskor_janjang1 += $totskor_janjang;
                        $total_skoreest += $total_skoreafd;
                        $v2check4 += $v2check3;

                        // dd($key3);
                    } # code...


                    if ($v2check4 != 0 && $total_skoreest == 0) {
                        $tot_afdscore = 100;
                    } else if ($deviden != 0) {
                        $tot_afdscore = round($total_skoreest / $deviden, 2);
                    } else if ($deviden == 0 && $v2check4 == 0) {
                        $tot_afdscore = 0;
                    }

                    // $newSidak[$key][$key1]['deviden'] = $deviden;

                    $newSidak[$key][$key1][$key2]['total_brd'] = $totskor_brd1;
                    $newSidak[$key][$key1][$key2]['total_janjang'] = $totskor_janjang1;
                    if ($v2check4 == 0) {
                        $newSidak[$key][$key1][$key2]['total_score'] = '-';
                    } else {
                        $newSidak[$key][$key1][$key2]['total_score'] = $tot_afdscore;
                    }


                    $newSidak[$key][$key1][$key2]['deviden'] = $deviden;
                    $newSidak[$key][$key1][$key2]['v2check4'] = $v2check4;

                    $tot_estAFd += $tot_afdscore;
                    $totskor_brd2 += $totskor_brd1;
                    $totskor_janjang2 += $totskor_janjang1;
                    // $new_dvdAfd += $new_dvd;
                    // $new_dvdAfdest += $new_dvdest;
                    $v2check5 += $v2check4;
                } # code...


                if ($v2check5 != 0 && $tot_estAFd == 0) {
                    $tot_afdscoremonth = 100;
                } else if ($devidenmonth != 0) {
                    $score =  round($tot_estAFd / $devidenmonth, 2);
                    if ($score < 0) {
                        $tot_afdscoremonth = 0;
                    } else {
                        $tot_afdscoremonth = $score;
                    }
                } else if ($devidenmonth == 0 && $v2check5 == 0) {
                    $tot_afdscoremonth = 0;
                }

                $newSidak[$key][$key1]['deviden'] = $devidenmonth;
                if ($v2check4 == 0) {
                    $newSidak[$key][$key1]['total_score'] = 0;
                } else {
                    $newSidak[$key][$key1]['total_score'] = $tot_afdscoremonth;
                }
                $newSidak[$key][$key1]['total_brd'] = $totskor_brd2;
                $newSidak[$key][$key1]['total_janjang'] = $totskor_janjang2;
                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['v2check5'] = $v2check5;

                $v2check6 += $v2check5;
                $tot_estAFd6 += $tot_afdscoremonth;
                $totskor_brd6 += $totskor_brd2;
                $totskor_janjang6 += $totskor_janjang2;
            }
            if ($v2check6 != 0 && $tot_estAFd6 == 0) {
                $todest = 100;
            } else if ($devest != 0) {
                // $todest = $tot_estAFd6 . '/' . $devest;
                $todest = round($tot_estAFd6 / $devest, 2);
            } else if ($devest == 0 && $v2check6 == 0) {
                $todest = 0;
            }

            $newSidak[$key]['deviden'] = $devest;
            if ($v2check4 == 0) {
                $newSidak[$key]['total_score'] = 0;
            } else {
                $newSidak[$key]['total_score'] = $todest;
            }
            $newSidak[$key]['total_brd'] = $totskor_brd6;
            $newSidak[$key]['total_janjang'] = $totskor_janjang6;
            $newSidak[$key]['est'] = $key;
            $newSidak[$key]['afd'] = $key1;
            $newSidak[$key]['v2check6'] = $v2check6;
        }

        // dd($);

        // dd($newSidak);
        $newsidakend = [];
        foreach ($defafd as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $divest = 0;
                $scoreest = 0;
                $totbrd = 0;
                $totjjg = 0;
                $v2check5 = 0;
                foreach ($value2 as $key3 => $value3) {
                    foreach ($newSidak as $keysidak => $valsidak) {
                        if ($key2 == $keysidak) {
                            foreach ($valsidak as $keysidak1 => $valsidak1) {
                                if ($keysidak1 == $key3) {
                                    // Key exists, assign values
                                    $deviden = $valsidak1['deviden'];
                                    $totalscore = $valsidak1['total_score'];
                                    $totalbrd = $valsidak1['total_brd'];
                                    $total_janjang = $valsidak1['total_janjang'];
                                    $v2check4 = $valsidak1['v2check5'];

                                    $newsidakend[$key][$key2][$key3]['deviden'] = $deviden;
                                    $newsidakend[$key][$key2][$key3]['total_score'] = $totalscore;
                                    $newsidakend[$key][$key2][$key3]['total_brd'] = $totalbrd;
                                    $newsidakend[$key][$key2][$key3]['total_janjang'] = $total_janjang;
                                    $newsidakend[$key][$key2][$key3]['v2check4'] = $v2check4;

                                    $divest += $deviden;
                                    $scoreest += $totalscore;
                                    $totbrd += $totalbrd;
                                    $totjjg += $total_janjang;
                                    $v2check5 += $v2check4;
                                }
                            }
                        }
                    }
                    // If key not found, set default values
                    if (!isset($newsidakend[$key][$key2][$key3])) {
                        $newsidakend[$key][$key2][$key3]['deviden'] = 0;
                        $newsidakend[$key][$key2][$key3]['total_score'] = 0;
                        $newsidakend[$key][$key2][$key3]['total_brd'] = 0;
                        $newsidakend[$key][$key2][$key3]['total_janjang'] = 0;
                        $newsidakend[$key][$key2][$key3]['v2check4'] = 0;
                    }
                }
                // Assign calculated values outside the innermost loop
                $newsidakend[$key][$key2]['deviden'] = $divest;
                $newsidakend[$key][$key2]['v2check5'] = $v2check5;
                $newsidakend[$key][$key2]['score_estate'] = ($divest !== 0) ? round($scoreest / $divest, 2) : 0;
                $newsidakend[$key][$key2]['totbrd'] = $totbrd;
                $newsidakend[$key][$key2]['totjjg'] = $totjjg;
            }
        }
        // dd($newSidak, $newsidakend);
        // dd($weeksdata, $ancakFA, $result);

        // dd($weeks, $ancakFA['KNE']);

        // dd($newsidakend);

        // qc inspeksi 


        $datainspek = [];
        DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun')
            )
            ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->orderBy('afdeling', 'asc')
            ->chunk($chunkSize, function ($results) use (&$datainspek) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $datainspek[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $datainspek = collect($datainspek)->groupBy(['estate', 'afdeling']);
        $QueryMTancakWil = json_decode($datainspek, true);
        // dd($QueryMTancakWil);

        // dd($QueryMTancakWil);

        $defaultNew = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $dataPerBulan = array();
        foreach ($QueryMTancakWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataPerBulan[$key][$key2][$key3] = $value3;
                }
            }
        }
        $mergedData = array();
        foreach ($defaultNew as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mergedData[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mergedData[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedData[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mergedData[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtancakWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mergedData as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1[$value['wil']][$key2] = array_merge($mtancakWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        $datamtbuah = [];
        DB::connection('mysql2')->table('mutu_buah')
            ->select(
                "mutu_buah.*",
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun')
            )
            ->join('estate', 'estate.est', '=', 'mutu_buah.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->orderBy('afdeling', 'asc')
            ->chunk($chunkSize, function ($results) use (&$datamtbuah) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $datamtbuah[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $datamtbuah = collect($datamtbuah)->groupBy(['estate', 'afdeling']);
        $QueryMTbuahWil = json_decode($datamtbuah, true);


        $dataMTBuah = array();
        foreach ($QueryMTbuahWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTBuah[$key][$key2][$key3] = $value3;
                }
            }
        }

        $defaultMTbuah = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultMTbuah[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $mutuBuahMerge = array();
        foreach ($defaultMTbuah as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTBuah)) {
                    if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                        if (!empty($dataMTBuah[$estKey][$afdKey])) {
                            $mutuBuahMerge[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                        } else {
                            $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtBuahWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuBuahMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtBuahWIltab1[$value['wil']][$key2] = array_merge($mtBuahWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }

        $mtancaktab1Wil = array();
        foreach ($mtancakWIltab1 as $key => $value) if (!empty($value)) {
            $pokok_panenWil = 0;
            $jum_haWil = 0;
            $janjang_panenWil = 0;
            $p_panenWil = 0;
            $k_panenWil = 0;
            $brtgl_panenWil = 0;
            $bhts_panenWil = 0;
            $bhtm1_panenWil = 0;
            $bhtm2_panenWil = 0;
            $bhtm3_oanenWil = 0;
            $pelepah_swil = 0;
            $totalPKTwil = 0;
            $sumBHWil = 0;
            $akpWil = 0;
            $brdPerwil = 0;
            $sumPerBHWil = 0;
            $perPiWil = 0;
            $totalWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value2)) {
                $pokok_panenEst = 0;
                $jum_haEst =  0;
                $janjang_panenEst =  0;
                $akpEst =  0;
                $p_panenEst =  0;
                $k_panenEst =  0;
                $brtgl_panenEst = 0;
                $skor_bTinggalEst =  0;
                $brdPerjjgEst =  0;
                $bhtsEST = 0;
                $bhtm1EST = 0;
                $bhtm2EST = 0;
                $bhtm3EST = 0;
                $pelepah_sEST = 0;

                $skor_bhEst =  0;
                $skor_brdPerjjgEst =  0;

                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {

                    $akp = 0;
                    $skor_bTinggal = 0;
                    $brdPerjjg = 0;
                    $pokok_panen = 0;
                    $janjang_panen = 0;
                    $p_panen = 0;
                    $k_panen = 0;
                    $bhts_panen  = 0;
                    $bhtm1_panen  = 0;
                    $bhtm2_panen  = 0;
                    $bhtm3_oanen  = 0;
                    $ttlSkorMA = 0;
                    $listBlokPerAfd = array();
                    $jum_ha = 0;
                    $pelepah_s = 0;
                    $skor_brdPerjjg = 0;
                    $skor_bh = 0;
                    $skor_perPl = 0;
                    $totalPokok = 0;
                    $totalPanen = 0;
                    $totalP_panen = 0;
                    $totalK_panen = 0;
                    $totalPTgl_panen = 0;
                    $totalbhts_panen = 0;
                    $totalbhtm1_panen = 0;
                    $totalbhtm2_panen = 0;
                    $totalbhtm3_oanen = 0;
                    $totalpelepah_s = 0;
                    $total_brd = 0;
                    $check_input = 'kosong';
                    $nilai_input = 0;
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        // dd($value3);
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);

                        $totalPokok += $value3["sample"];
                        $totalPanen +=  $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen += $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];

                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];

                        $totalpelepah_s += $value3["ps"];
                        $check_input = $value3["jenis_input"];
                        $nilai_input = $value3["skor_akhir"];
                    }


                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }


                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                    } else {
                        $perPl = 0;
                    }





                    $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                    if (!empty($nonZeroValues)) {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                    } else {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                    }

                    // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                    $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] = $akp;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = $totalP_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = $totalK_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                    $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_per'] = $perPl;
                    // total skor akhir

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                    $mtancaktab1Wil[$key][$key1][$key2]['check_input'] = $check_input;
                    $mtancaktab1Wil[$key][$key1][$key2]['nilai_input'] = $nilai_input;

                    $pokok_panenEst += $totalPokok;

                    $jum_haEst += $jum_ha;
                    $janjang_panenEst += $totalPanen;

                    $p_panenEst += $totalP_panen;
                    $k_panenEst += $totalK_panen;
                    $brtgl_panenEst += $totalPTgl_panen;

                    // bagian buah tinggal
                    $bhtsEST   += $totalbhts_panen;
                    $bhtm1EST += $totalbhtm1_panen;
                    $bhtm2EST   += $totalbhtm2_panen;
                    $bhtm3EST   += $totalbhtm3_oanen;
                    // data untuk pelepah sengklek
                    $pelepah_sEST += $totalpelepah_s;
                } else {
                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] =  0;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhi0;

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_bh'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = 0;
                }

                $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                // dd($sumBHEst);
                if ($pokok_panenEst != 0) {
                    $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                } else {
                    $akpEst = 0;
                }

                if ($janjang_panenEst != 0) {
                    $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                } else {
                    $brdPerjjgEst = 0;
                }



                // dd($sumBHEst);
                if ($sumBHEst != 0) {
                    $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                } else {
                    $sumPerBHEst = 0;
                }

                if ($pokok_panenEst != 0) {
                    $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                } else {
                    $perPlEst = 0;
                }


                $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                if (!empty($nonZeroValues)) {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                } else {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                //PENAMPILAN UNTUK PERESTATE
                $mtancaktab1Wil[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  $jum_haEst;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  $akpEst;

                $mtancaktab1Wil[$key][$key1]['p'] = $p_panenEst;
                $mtancaktab1Wil[$key][$key1]['k'] = $k_panenEst;
                $mtancaktab1Wil[$key][$key1]['tgl'] = $brtgl_panenEst;

                $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = $bhtsEST;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = $bhtm1EST;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = $bhtm2EST;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = $bhtm3EST;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                $mtancaktab1Wil[$key][$key1]['palepah_per'] = $perPlEst;
                // total skor akhir

                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = $totalSkorEst;

                //perhitungn untuk perwilayah

                $pokok_panenWil += $pokok_panenEst;
                $jum_haWil += $jum_haEst;
                $janjang_panenWil += $janjang_panenEst;
                $p_panenWil += $p_panenEst;
                $k_panenWil += $k_panenEst;
                $brtgl_panenWil += $brtgl_panenEst;
                // bagian buah tinggal
                $bhts_panenWil += $bhtsEST;
                $bhtm1_panenWil += $bhtm1EST;
                $bhtm2_panenWil += $bhtm2EST;
                $bhtm3_oanenWil += $bhtm3EST;
                $pelepah_swil += $pelepah_sEST;
            } else {
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = 0;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  0;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = 0;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  0;

                $mtancaktab1Wil[$key][$key1]['p'] = 0;
                $mtancaktab1Wil[$key][$key1]['k'] = 0;
                $mtancaktab1Wil[$key][$key1]['tgl'] = 0;

                // $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = 0;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = 0;
                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = 0;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhir
                $mtancaktab1Wil[$key][$key1]['skor_bh'] =  0;
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = 0;
            }
            $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
            $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

            if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                $akpWil = 0;
            } else {

                $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
            }

            if ($totalPKTwil != 0) {
                $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
            } else {
                $brdPerwil = 0;
            }

            // dd($sumBHEst);
            if ($sumBHWil != 0) {
                $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
            } else {
                $sumPerBHWil = 0;
            }

            if ($pokok_panenWil != 0) {
                $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
            } else {
                $perPiWil = 0;
            }

            $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

            if (!empty($nonZeroValues)) {
                $mtancaktab1Wil[$key]['check_data'] = 'ada';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtancaktab1Wil[$key]['check_data'] = 'kosong';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }

            // $totalWil = $skor_bh + $skor_brd + $skor_ps;
            $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

            $mtancaktab1Wil[$key]['pokok_sample'] = $pokok_panenWil;
            $mtancaktab1Wil[$key]['ha_sample'] =  $jum_haWil;
            $mtancaktab1Wil[$key]['jumlah_panen'] = $janjang_panenWil;
            $mtancaktab1Wil[$key]['akp_rl'] =  $akpWil;

            $mtancaktab1Wil[$key]['p'] = $p_panenWil;
            $mtancaktab1Wil[$key]['k'] = $k_panenWil;
            $mtancaktab1Wil[$key]['tgl'] = $brtgl_panenWil;
            $mtancaktab1Wil[$key]['total_brd'] = $totalPKTwil;

            $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = $brdPerwil;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['bhts_s'] = $bhts_panenWil;
            $mtancaktab1Wil[$key]['bhtm1'] = $bhtm1_panenWil;
            $mtancaktab1Wil[$key]['bhtm2'] = $bhtm2_panenWil;
            $mtancaktab1Wil[$key]['bhtm3'] = $bhtm3_oanenWil;
            $mtancaktab1Wil[$key]['total_buah'] = $sumBHWil;
            $mtancaktab1Wil[$key]['total_buah_per'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = $pelepah_swil;

            $mtancaktab1Wil[$key]['palepah_per'] = $perPiWil;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = skor_buah_Ma($sumPerBHWil);
            $mtancaktab1Wil[$key]['skor_brd'] = skor_brd_ma($brdPerwil);
            $mtancaktab1Wil[$key]['skor_ps'] = skor_palepah_ma($perPiWil);
            $mtancaktab1Wil[$key]['skor_akhir'] = $totalWil;
        } else {
            $mtancaktab1Wil[$key]['pokok_sample'] = 0;
            $mtancaktab1Wil[$key]['ha_sample'] =  0;
            $mtancaktab1Wil[$key]['jumlah_panen'] = 0;
            $mtancaktab1Wil[$key]['akp_rl'] =  0;

            $mtancaktab1Wil[$key]['p'] = 0;
            $mtancaktab1Wil[$key]['k'] = 0;
            $mtancaktab1Wil[$key]['tgl'] = 0;

            // $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['bhts_s'] = 0;
            $mtancaktab1Wil[$key]['bhtm1'] = 0;
            $mtancaktab1Wil[$key]['bhtm2'] = 0;
            $mtancaktab1Wil[$key]['bhtm3'] = 0;
            // $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = 0;
            $mtancaktab1Wil[$key]['skor_brd'] = 0;
            $mtancaktab1Wil[$key]['skor_ps'] = 0;
            $mtancaktab1Wil[$key]['skor_akhir'] = 0;
        }

        $mtBuahtab1Wil = array();
        foreach ($mtBuahWIltab1 as $key => $value) if (is_array($value)) {
            $jum_haWil = 0;
            $sum_SamplejjgWil = 0;
            $sum_bmtWil = 0;
            $sum_bmkWil = 0;
            $sum_overWil = 0;
            $sum_abnorWil = 0;
            $sum_kosongjjgWil = 0;
            $sum_vcutWil = 0;
            $sum_krWil = 0;
            $no_Vcutwil = 0;

            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                $jum_haEst  = 0;
                $sum_SamplejjgEst = 0;
                $sum_bmtEst = 0;
                $sum_bmkEst = 0;
                $sum_overEst = 0;
                $sum_abnorEst = 0;
                $sum_kosongjjgEst = 0;
                $sum_vcutEst = 0;
                $sum_krEst = 0;
                $no_VcutEst = 0;

                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
                    $dataBLok = 0;
                    $sum_Samplejjg = 0;
                    $PerMth = 0;
                    $PerMsk = 0;
                    $PerOver = 0;
                    $sum_abnor = 0;
                    $sum_kosongjjg = 0;
                    $Perkosongjjg = 0;
                    $sum_vcut = 0;
                    $PerVcut = 0;
                    $PerAbr = 0;
                    $sum_kr = 0;
                    $total_kr = 0;
                    $per_kr = 0;
                    $totalSkor = 0;
                    $jum_ha = 0;
                    $no_Vcut = 0;
                    $jml_mth = 0;
                    $jml_mtg = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = [];
                    $dtBlok = 0;
                    // $combination_counts = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                        $dtBlok = count($listBlokPerAfd);

                        // $jum_ha = count($listBlokPerAfd);
                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];


                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }

                    // $dataBLok = count($combination_counts);
                    $dataBLok = $dtBlok;
                    $jml_mth = ($sum_bmt + $sum_bmk);
                    $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 3);
                    } else {
                        $total_kr = 0;
                    }


                    $per_kr = round($total_kr * 100, 3);
                    if ($jml_mth != 0) {
                        $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMth = 0;
                    }
                    if ($jml_mtg != 0) {
                        $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMsk = 0;
                    }
                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerOver = 0;
                    }
                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $Perkosongjjg = 0;
                    }
                    if ($sum_vcut != 0) {
                        $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_abnor != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerAbr = 0;
                    }

                    $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                    if (!empty($nonZeroValues)) {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                    } else {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = $sum_over;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = $PerVcut;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                    //perhitungan estate
                    $jum_haEst += $dataBLok;
                    $sum_SamplejjgEst += $sum_Samplejjg;
                    $sum_bmtEst += $jml_mth;
                    $sum_bmkEst += $jml_mtg;
                    $sum_overEst += $sum_over;
                    $sum_abnorEst += $sum_abnor;
                    $sum_kosongjjgEst += $sum_kosongjjg;
                    $sum_vcutEst += $sum_vcut;
                    $sum_krEst += $sum_kr;
                } else {
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = 0;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_abnormal'] = 0;;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                }
                $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                if ($sum_krEst != 0) {
                    $total_krEst = round($sum_krEst / $jum_haEst, 3);
                } else {
                    $total_krEst = 0;
                }
                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }

                if ($sum_bmtEst != 0) {
                    $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMthEst = 0;
                }

                if ($sum_bmkEst != 0) {
                    $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMskEst = 0;
                }

                if ($sum_overEst != 0) {
                    $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerOverEst = 0;
                }
                if ($sum_kosongjjgEst != 0) {
                    $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerkosongjjgEst = 0;
                }
                if ($sum_vcutEst != 0) {
                    $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerVcutest = 0;
                }
                if ($sum_abnorEst != 0) {
                    $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerAbrest = 0;
                }
                // $per_kr = round($sum_kr * 100);
                $per_krEst = round($total_krEst * 100, 3);


                $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                if (!empty($nonZeroValues)) {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                } else {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = $sum_bmtEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = $PerMthEst;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = $sum_bmkEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = $PerMskEst;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = $sum_overEst;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = $PerOverEst;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = $PerAbrest;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = $sum_vcutEst;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = $PerVcutest;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = $sum_krEst;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = $total_krEst;

                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = $per_krEst;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                //hitung perwilayah
                $jum_haWil += $jum_haEst;
                $sum_SamplejjgWil += $sum_SamplejjgEst;
                $sum_bmtWil += $sum_bmtEst;
                $sum_bmkWil += $sum_bmkEst;
                $sum_overWil += $sum_overEst;
                $sum_abnorWil += $sum_abnorEst;
                $sum_kosongjjgWil += $sum_kosongjjgEst;
                $sum_vcutWil += $sum_vcutEst;
                $sum_krWil += $sum_krEst;
            } else {
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_abnormal'] = 0;;
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = 0;
            }

            // if ($sum_kr != 0) {
            //     $total_kr = round($sum_kr / $dataBLok, 3);
            // } else {
            //     $total_kr = 0;
            // }



            if ($sum_krWil != 0) {
                $total_krWil = round($sum_krWil / $jum_haWil, 3);
            } else {
                $total_krWil = 0;
            }

            if ($sum_bmtWil != 0) {
                $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMthWil = 0;
            }


            if ($sum_bmkWil != 0) {
                $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMskWil = 0;
            }
            if ($sum_overWil != 0) {
                $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerOverWil = 0;
            }
            if ($sum_kosongjjgWil != 0) {
                $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerkosongjjgWil = 0;
            }
            if ($sum_vcutWil != 0) {
                $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerVcutWil = 0;
            }
            if ($sum_abnorWil != 0) {
                $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerAbrWil = 0;
            }
            $per_krWil = round($total_krWil * 100, 3);

            $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

            if (!empty($nonZeroValues)) {
                $mtBuahtab1Wil[$key]['check_data'] = 'ada';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskWil);
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverWil);
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgWil);
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutWil);
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krWil);
            } else {
                $mtBuahtab1Wil[$key]['check_data'] = 'kosong';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak = 0;
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over = 0;
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  0;
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr = 0;
            }

            // $totalSkorWil = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


            $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = $jum_haWil;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = $sum_SamplejjgWil;
            $mtBuahtab1Wil[$key]['total_mentah'] = $sum_bmtWil;
            $mtBuahtab1Wil[$key]['total_perMentah'] = $PerMthWil;
            $mtBuahtab1Wil[$key]['total_masak'] = $sum_bmkWil;
            $mtBuahtab1Wil[$key]['total_perMasak'] = $PerMskWil;
            $mtBuahtab1Wil[$key]['total_over'] = $sum_overWil;
            $mtBuahtab1Wil[$key]['total_perOver'] = $PerOverWil;
            $mtBuahtab1Wil[$key]['total_abnormal'] = $sum_abnorWil;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = $PerAbrWil;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = $sum_kosongjjgWil;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = $PerkosongjjgWil;
            $mtBuahtab1Wil[$key]['total_vcut'] = $sum_vcutWil;
            $mtBuahtab1Wil[$key]['per_vcut'] = $PerVcutWil;
            $mtBuahtab1Wil[$key]['jum_kr'] = $sum_krWil;
            $mtBuahtab1Wil[$key]['kr_blok'] = $total_krWil;

            $mtBuahtab1Wil[$key]['persen_kr'] = $per_krWil;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMthWil);
            $mtBuahtab1Wil[$key]['skor_masak'] = skor_buah_masak_mb($PerMskWil);
            $mtBuahtab1Wil[$key]['skor_over'] = skor_buah_over_mb($PerOverWil);;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
            $mtBuahtab1Wil[$key]['skor_vcut'] = skor_vcut_mb($PerVcutWil);
            $mtBuahtab1Wil[$key]['skor_kr'] = skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = $totalSkorWil;
        } else {
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = 0;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = 0;
            $mtBuahtab1Wil[$key]['total_mentah'] = 0;
            $mtBuahtab1Wil[$key]['total_perMentah'] = 0;
            $mtBuahtab1Wil[$key]['total_masak'] = 0;
            $mtBuahtab1Wil[$key]['total_perMasak'] = 0;
            $mtBuahtab1Wil[$key]['total_over'] = 0;
            $mtBuahtab1Wil[$key]['total_perOver'] = 0;
            $mtBuahtab1Wil[$key]['total_abnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = 0;
            $mtBuahtab1Wil[$key]['total_vcut'] = 0;
            $mtBuahtab1Wil[$key]['per_vcut'] = 0;
            $mtBuahtab1Wil[$key]['jum_kr'] = 0;
            $mtBuahtab1Wil[$key]['kr_blok'] = 0;

            $mtBuahtab1Wil[$key]['persen_kr'] = 0;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = 0;
            $mtBuahtab1Wil[$key]['skor_masak'] = 0;
            $mtBuahtab1Wil[$key]['skor_over'] = 0;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['skor_vcut'] = 0;

            $mtBuahtab1Wil[$key]['skor_kr'] = 0;
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = 0;
        }

        $trancakdata = [];
        DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y-%m-%d") as date')
            )
            ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->chunk($chunkSize, function ($results) use (&$trancakdata) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $trancakdata[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $TranscakReg2 = collect($trancakdata)->toArray();

        $ancakdatareg2 = [];
        DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'),
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y-%m-%d") as date')
            )
            ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->chunk($chunkSize, function ($results) use (&$ancakdatareg2) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $ancakdatareg2[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $AncakCakReg2 = collect($ancakdatareg2)->toArray();

        // dd($TranscakReg2);

        $DataTransGroupReg2 = [];
        foreach ($TranscakReg2 as $item) {
            $estate = $item->estate;
            $afdeling = $item->afdeling;
            $datetime = $item->datetime;
            $blok = $item->blok;
            $date = $item->date;

            if (!isset($DataTransGroupReg2[$estate])) {
                $DataTransGroupReg2[$estate] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling])) {
                $DataTransGroupReg2[$estate][$afdeling] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling][$date])) {
                $DataTransGroupReg2[$estate][$afdeling][$date] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling][$date][$blok])) {
                $DataTransGroupReg2[$estate][$afdeling][$date][$blok] = [];
            }

            $DataTransGroupReg2[$estate][$afdeling][$date][$blok][] = $item;
        }

        $DataTransGroupReg2 = json_decode(json_encode($DataTransGroupReg2), true);
        $groupedDataAcnakreg2 = [];
        foreach ($AncakCakReg2 as $item) {
            $estate = $item->estate;
            $afdeling = $item->afdeling;
            $datetime = $item->datetime;
            $blok = $item->blok;
            $date = $item->date;

            if (!isset($groupedDataAcnakreg2[$estate])) {
                $groupedDataAcnakreg2[$estate] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling])) {
                $groupedDataAcnakreg2[$estate][$afdeling] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling][$date])) {
                $groupedDataAcnakreg2[$estate][$afdeling][$date] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling][$date][$blok])) {
                $groupedDataAcnakreg2[$estate][$afdeling][$date][$blok] = [];
            }

            $groupedDataAcnakreg2[$estate][$afdeling][$date][$blok][] = $item;
        }

        $groupedDataAcnakreg2 = json_decode(json_encode($groupedDataAcnakreg2), true);

        $dataMTTransRegs2 = array();
        foreach ($DataTransGroupReg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {

                                    foreach ($value3 as $key4 => $value4) {

                                        $dataMTTransRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }
        $dataAncaksRegs2 = array();
        foreach ($groupedDataAcnakreg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {
                                    foreach ($value3 as $key4 => $value4) {
                                        $dataAncaksRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }
        // dd($dataAncaksRegs2);
        $ancakRegss2 = array();

        foreach ($dataAncaksRegs2 as $key => $value) {
            foreach ($value as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3) {
                    $sum = 0; // Initialize sum variable
                    $count = 0; // Initialize count variable
                    foreach ($value3 as $key3 => $value4) {
                        $listBlok = array();
                        $firstEntry = $value4[0];
                        foreach ($value4 as $key4 => $value5) {
                            // dd($value5['sph']);
                            if (!in_array($value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'], $listBlok)) {
                                if ($value5['sph'] != 0) {
                                    $listBlok[] = $value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'];
                                }
                            }
                            $jml_blok = count($listBlok);

                            if ($firstEntry['luas_blok'] != 0) {
                                $first = $firstEntry['luas_blok'];
                            } else {
                                $first = '-';
                            }
                        }
                        if ($first != '-') {
                            $sum += $first;
                            $count++;
                        }
                        $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'] = $first;
                        if ($regional === '2') {
                            $status_panen = explode(",", $value5['status_panen']);
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $status_panen[0];
                        } else {
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $value5['status_panen'];
                        }
                    }
                }
            }
        }
        $transNewdata = array();
        foreach ($dataMTTransRegs2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {

                    foreach ($value2 as $key3 => $value3) {
                        $sum_bt = 0;
                        $sum_Restan = 0;
                        $tph_sample = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key4 => $value4) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            $sum_Restan += $value4['rst'];
                            $tph_sample = count($listBlokPerAfd);
                            $sum_bt += $value4['bt'];
                        }
                        $panenKey = 0;
                        $LuasKey = 0;
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['status_panen'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['status_panen'] = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                            $panenKey = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                        }
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['luas_blok'] = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                            $LuasKey = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                        }


                        if ($panenKey !== 0 && $panenKey <= 3) {
                            if (count($value4) == 1 && $value4[0]['blok'] == '0') {
                                $tph_sample = $value4[0]['tph_baris'];
                                $sum_bt = $value4[0]['bt'];
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($LuasKey) * 1.3, 3);
                            }
                        } else {
                            $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = $tph_sample;
                        }



                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                        $transNewdata[$key][$key1][$key2][$key3]['afdeling'] = $value4['afdeling'];
                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                    }
                }
            }
        }
        foreach ($ancakRegss2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {
                    $tph_tod = 0;
                    foreach ($value2 as $key3 => $value3) {
                        if (!isset($transNewdata[$key][$key1][$key2][$key3])) {
                            $transNewdata[$key][$key1][$key2][$key3] = $value3;

                            if ($value3['status_panen'] <= 3) {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($value3['luas_blok']) * 1.3, 3);
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = 0;
                            }
                        }
                        // If 'tph_sample' key exists, add its value to $tph_tod
                        if (isset($value3['tph_sample'])) {
                            $tph_tod += $value3['tph_sample'];
                        }
                    }
                }
                // Store total_tph for each $key1 after iterating all $key2

            }
        }
        foreach ($transNewdata as $key => &$value) {
            foreach ($value as $key1 => &$value1) {
                $tph_sample_total = 0; // initialize the total
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            if (isset($value3['tph_sample'])) {
                                $tph_sample_total += $value3['tph_sample'];
                            }
                        }
                    }
                }
                $value1['total_tph'] = $tph_sample_total;
            }
        }
        unset($value); // unset the reference
        unset($value1); // unset the reference


        $defaultMtTrans = array();
        foreach ($queryEste as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defaultMtTrans[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $datatranswil = [];
        DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
            )
            ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereYear('datetime', $tahun)
            ->orderBy('datetime', 'asc')
            ->chunk($chunkSize, function ($results) use (&$datatranswil) {
                foreach ($results as $result) {
                    // Grouping logic here, if needed
                    $datatranswil[] = $result;
                    // Adjust this according to your grouping requirements
                }
            });

        $datatranswil = collect($datatranswil)->groupBy(['estate', 'afdeling']);
        $QueryTransWil = json_decode($datatranswil, true);

        // $QueryTransWil = DB::connection('mysql2')->table('mutu_transport')
        //     ->select(
        //         "mutu_transport.*",
        //         DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
        //         DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
        //     )
        //     ->where('datetime', 'like', '%' . $bulan . '%')
        //     // ->whereYear('datetime', $year)
        //     ->get();
        // $QueryTransWil = $QueryTransWil->groupBy(['estate', 'afdeling']);
        // $QueryTransWil = json_decode($QueryTransWil, true);
        $dataMTTrans = array();
        foreach ($QueryTransWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTTrans[$key][$key2][$key3] = $value3;
                }
            }
        }
        $mutuAncakMerge = array();
        foreach ($defaultMtTrans as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTTrans)) {
                    if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                        if (!empty($dataMTTrans[$estKey][$afdKey])) {
                            $mutuAncakMerge[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                        } else {
                            $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtTransWiltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuAncakMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtTransWiltab1[$value['wil']][$key2] = array_merge($mtTransWiltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }
        // dd($mtTransWiltab1);
        $mtTranstab1Wil = array();
        foreach ($mtTransWiltab1 as $key => $value) if (!empty($value)) {
            $dataBLokWil = 0;
            $sum_btWil = 0;
            $sum_rstWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLokEst = 0;
                $sum_btEst = 0;
                $sum_rstEst = 0;
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        // }
                        $dataBLok = count($listBlokPerAfd);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }
                    $tot_sample = 0;  // Define the variable outside of the foreach loop

                    foreach ($transNewdata as $keys => $trans) {
                        if ($keys == $key1) {
                            foreach ($trans as $keys2 => $trans2) {
                                if ($keys2 == $key2) {
                                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sampleNew'] = $trans2['total_tph'];
                                    $tot_sample = $trans2['total_tph'];
                                }
                            }
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $tot_sample, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $tot_sample, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    }


                    $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                    if (!empty($nonZeroValues)) {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                    } else {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = "kosong";
                    }
                    // dd($transNewdata);




                    $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                    //PERHITUNGAN PERESTATE
                    if ($regional == '2' || $regional == 2) {
                        $dataBLokEst += $tot_sample;
                    } else {
                        $dataBLokEst += $dataBLok;
                    }

                    $sum_btEst += $sum_bt;
                    $sum_rstEst += $sum_rst;

                    if ($dataBLokEst != 0) {
                        $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                    } else {
                        $brdPertphEst = 0;
                    }

                    if ($dataBLokEst != 0) {
                        $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                    } else {
                        $buahPerTPHEst = 0;
                    }

                    // dd($mtTranstab1Wil);
                    $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = 0;
                }

                $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                if (!empty($nonZeroValues)) {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                // $totalSkorEst = $skor_brd + $skor_buah ;


                $mtTranstab1Wil[$key][$key1]['tph_sample'] = $dataBLokEst;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = $sum_btEst;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = $sum_rstEst;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = $totalSkorEst;

                //perhitungan per wil
                $dataBLokWil += $dataBLokEst;
                $sum_btWil += $sum_btEst;
                $sum_rstWil += $sum_rstEst;

                if ($dataBLokWil != 0) {
                    $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                } else {
                    $brdPertphWil = 0;
                }
                if ($dataBLokWil != 0) {
                    $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                } else {
                    $buahPerTPHWil = 0;
                }

                $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
            } else {
                $mtTranstab1Wil[$key][$key1]['tph_sample'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = 0;
            }

            $nonZeroValues = array_filter([$sum_btWil, $sum_rstWil]);


            if (!empty($nonZeroValues)) {
                $mtTranstab1Wil[$key]['check_data'] = 'ada';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtTranstab1Wil[$key]['check_data'] = 'kosong';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }
            $mtTranstab1Wil[$key]['tph_sample'] = $dataBLokWil;
            $mtTranstab1Wil[$key]['total_brd'] = $sum_btWil;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = $brdPertphWil;
            $mtTranstab1Wil[$key]['total_buah'] = $sum_rstWil;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = $buahPerTPHWil;
            $mtTranstab1Wil[$key]['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
            $mtTranstab1Wil[$key]['totalSkor'] = $totalSkorWil;
        } else {
            $mtTranstab1Wil[$key]['tph_sample'] = 0;
            $mtTranstab1Wil[$key]['total_brd'] = 0;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = 0;
            $mtTranstab1Wil[$key]['total_buah'] = 0;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['skor_brdPertph'] = 0;
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['totalSkor'] = 0;
        }

        foreach ($mtancaktab1Wil as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    foreach ($mtBuahtab1Wil as $bh => $buah) {
                        foreach ($buah as $bh1 => $buah1) if (is_array($buah1)) {
                            foreach ($buah1 as $bh2 => $buah2) if (is_array($buah2)) {
                                foreach ($mtTranstab1Wil as $tr => $trans) {
                                    foreach ($trans as $tr1 => $trans1) if (is_array($trans1)) {
                                        foreach ($trans1 as $tr2 => $trans2) if (is_array($trans2))
                                            if (
                                                $bh == $key
                                                && $bh == $tr
                                                && $bh1 == $key1
                                                && $bh1 == $tr1
                                                && $bh2 == $key2
                                                && $bh2 == $tr2
                                            ) {
                                                // dd($trans2);
                                                // dd($key);
                                                if ($value2['check_input'] == 'manual' && $value2['nilai_input'] != 0) {
                                                    $RekapWIlTabel[$key][$key1][$key2]['data'] = 'ada';
                                                } else if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['data'] = 'kosong';
                                                }

                                                if ($value2['check_input'] == 'manual') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['nilai_input'];
                                                } else  if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = 0;
                                                } else {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['skor_akhir'] + $buah2['TOTAL_SKOR'] + $trans2['totalSkor'];
                                                }


                                                if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = 0;
                                                    $RekapWIlTabel[$key][$key1]['data'] = 'kosong';
                                                } else {
                                                    $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = $value1['skor_akhir'] + $buah1['TOTAL_SKOR'] + $trans1['totalSkor'];
                                                }


                                                if ($value1['check_data'] == 'kosong' && $buah1['check_data'] === 'kosong' && $trans1['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1]['dataEst'] = 'kosong';
                                                }

                                                // dd($value,$buah,$trans);
                                                if ($trans['check_data'] == 'kosong' && $buah['check_data'] === 'kosong' && $value['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key]['TotalSkorWil'] = 0;
                                                } else {
                                                    $RekapWIlTabel[$key]['TotalSkorWil'] = $value['skor_akhir'] + $buah['TOTAL_SKOR'] + $trans['totalSkor'];
                                                }
                                            }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($RekapWIlTabel as $key1 => $estates)  if (is_array($estates)) {
            $sortedData = array();
            $sortedDataEst = array();

            foreach ($estates as $estateName => $data) {
                if (is_array($data)) {
                    $sortedDataEst[] = array(
                        'key1' => $key1,
                        'estateName' => $estateName,
                        'data' => $data
                    );
                    foreach ($data as $key2 => $scores) {
                        if (is_array($scores)) {
                            $sortedData[] = array(
                                'estateName' => $estateName,
                                'key2' => $key2,
                                'scores' => $scores
                            );
                        }
                    }
                }
            }

            //mengurutkan untuk nilai afd
            usort($sortedData, function ($a, $b) {
                return $b['scores']['TotalSkor'] - $a['scores']['TotalSkor'];
            });
            //mengurutkan untuk nilai estate
            usort($sortedDataEst, function ($a, $b) {
                return $b['data']['TotalSkorEST'] - $a['data']['TotalSkorEST'];
            });

            //menambahkan nilai rank ke dalam afd
            $rank = 1;
            foreach ($sortedData as $sortedEstate) {
                $RekapWIlTabel[$key1][$sortedEstate['estateName']][$sortedEstate['key2']]['rankAFD'] = $rank;
                $rank++;
            }

            //menambahkan nilai rank ke dalam estate
            $rank = 1;
            foreach ($sortedDataEst as $sortedest) {
                $RekapWIlTabel[$key1][$sortedest['estateName']]['rankEST'] = $rank;
                $rank++;
            }


            unset($sortedData, $sortedDataEst);
        }

        // dd($RekapWIlTabel);
        $qcinspeksi = $RekapWIlTabel;

        $sortedArray = [];

        foreach ($qcinspeksi as $key => $value) {
            $sortedArray[$key] = $value['TotalSkorWil'];
        }

        arsort($sortedArray);

        $rank = 1;
        foreach ($sortedArray as $key => $value) {
            $qcinspeksi[$key]['rankWil'] = $rank++;
        }

        // dd($qcinspeksi, $newsidakend, $mutu_buah);

        if ($regional == 1) {

            // untuk get mua ===============================================

            // sudak mutu buah mua 
            $defPerbulanWilmua = array();

            foreach ($muaest as $key2 => $value2) {
                foreach ($queryAfd as $key3 => $value3) {
                    if ($value2['est'] == $value3['est']) {
                        $defPerbulanWilmua[$value2['est']][$value3['est']] = 0;
                    }
                }
            }
            foreach ($defPerbulanWilmua as $estateKey => $afdelingArray) {
                foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                    if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                        $defPerbulanWilmua[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                    }
                }
            }

            $sidakbuahmuah = array();
            // dd($defPerbulanWil);
            $jjg_samplexy = 0;
            $tnpBRDxy = 0;
            $krgBRDxy = 0;
            $abrxy = 0;
            $overripexy = 0;
            $emptyxy = 0;
            $vcutxy = 0;
            $rdxy = 0;
            $dataBLokxy = 0;
            $sum_krxy = 0;
            $csrmsy = 0;
            foreach ($defPerbulanWilmua as $key => $value) {
                $jjg_samplex = 0;
                $tnpBRDx = 0;
                $krgBRDx = 0;
                $abrx = 0;
                $overripex = 0;
                $emptyx = 0;
                $vcutx = 0;
                $rdx = 0;
                $dataBLokx = 0;
                $sum_krx = 0;
                $csrms = 0;
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        $jjg_sample = 0;
                        $tnpBRD = 0;
                        $krgBRD = 0;
                        $abr = 0;
                        $skor_total = 0;
                        $overripe = 0;
                        $empty = 0;
                        $vcut = 0;
                        $rd = 0;
                        $sum_kr = 0;
                        $allSkor = 0;
                        $combination_counts = array();
                        $newblok = 0;
                        $csfxr = count($value1);
                        foreach ($value1 as $key2 => $value2) {
                            $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                            if (!isset($combination_counts[$combination])) {
                                $combination_counts[$combination] = 0;
                            }
                            $newblok = count($value1);
                            $jjg_sample += $value2['jumlah_jjg'];
                            $tnpBRD += $value2['bmt'];
                            $krgBRD += $value2['bmk'];
                            $abr += $value2['abnormal'];
                            $overripe += $value2['overripe'];
                            $empty += $value2['empty_bunch'];
                            $vcut += $value2['vcut'];
                            $rd += $value2['rd'];
                            $sum_kr += $value2['alas_br'];
                        }
                        // $dataBLok = count($combination_counts);
                        $dataBLok = $newblok;
                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 2);
                        } else {
                            $total_kr = 0;
                        }
                        $per_kr = round($total_kr * 100, 2);
                        $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                        $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                        $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                        $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                        $sidakbuahmuah[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                        $sidakbuahmuah[$key][$key1]['blok'] = $dataBLok;
                        $sidakbuahmuah[$key][$key1]['est'] = $key;
                        $sidakbuahmuah[$key][$key1]['afd'] = $key1;
                        $sidakbuahmuah[$key][$key1]['nama_staff'] = '-';
                        $sidakbuahmuah[$key][$key1]['tnp_brd'] = $tnpBRD;
                        $sidakbuahmuah[$key][$key1]['krg_brd'] = $krgBRD;
                        $sidakbuahmuah[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidakbuahmuah[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidakbuahmuah[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                        $sidakbuahmuah[$key][$key1]['persen_totalJjg'] = $skor_total;
                        $sidakbuahmuah[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                        $sidakbuahmuah[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                        $sidakbuahmuah[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                        $sidakbuahmuah[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                        $sidakbuahmuah[$key][$key1]['lewat_matang'] = $overripe;
                        $sidakbuahmuah[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                        $sidakbuahmuah[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                        $sidakbuahmuah[$key][$key1]['janjang_kosong'] = $empty;
                        $sidakbuahmuah[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                        $sidakbuahmuah[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                        $sidakbuahmuah[$key][$key1]['vcut'] = $vcut;
                        $sidakbuahmuah[$key][$key1]['karung'] = $sum_kr;
                        $sidakbuahmuah[$key][$key1]['vcut_persen'] = $skor_vcut;
                        $sidakbuahmuah[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                        $sidakbuahmuah[$key][$key1]['abnormal'] = $abr;
                        $sidakbuahmuah[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                        $sidakbuahmuah[$key][$key1]['rat_dmg'] = $rd;
                        $sidakbuahmuah[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                        $sidakbuahmuah[$key][$key1]['TPH'] = $total_kr;
                        $sidakbuahmuah[$key][$key1]['persen_krg'] = $per_kr;
                        $sidakbuahmuah[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                        $sidakbuahmuah[$key][$key1]['All_skor'] = $allSkor;
                        $sidakbuahmuah[$key][$key1]['csfxr'] = $csfxr;
                        $sidakbuahmuah[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidakbuahmuah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                        $jjg_samplex += $jjg_sample;
                        $tnpBRDx += $tnpBRD;
                        $krgBRDx += $krgBRD;
                        $abrx += $abr;
                        $overripex += $overripe;
                        $emptyx += $empty;
                        $vcutx += $vcut;

                        $rdx += $rd;

                        $dataBLokx += $newblok;
                        $sum_krx += $sum_kr;
                        $csrms += $csfxr;
                    } else {

                        $sidakbuahmuah[$key][$key1]['Jumlah_janjang'] = 0;
                        $sidakbuahmuah[$key][$key1]['blok'] = 0;
                        $sidakbuahmuah[$key][$key1]['est'] = $key;
                        $sidakbuahmuah[$key][$key1]['afd'] = $key1;
                        $sidakbuahmuah[$key][$key1]['nama_staff'] = '-';
                        $sidakbuahmuah[$key][$key1]['tnp_brd'] = 0;
                        $sidakbuahmuah[$key][$key1]['krg_brd'] = 0;
                        $sidakbuahmuah[$key][$key1]['persenTNP_brd'] = 0;
                        $sidakbuahmuah[$key][$key1]['persenKRG_brd'] = 0;
                        $sidakbuahmuah[$key][$key1]['total_jjg'] = 0;
                        $sidakbuahmuah[$key][$key1]['persen_totalJjg'] = 0;
                        $sidakbuahmuah[$key][$key1]['skor_total'] = 0;
                        $sidakbuahmuah[$key][$key1]['jjg_matang'] = 0;
                        $sidakbuahmuah[$key][$key1]['persen_jjgMtang'] = 0;
                        $sidakbuahmuah[$key][$key1]['skor_jjgMatang'] = 0;
                        $sidakbuahmuah[$key][$key1]['lewat_matang'] = 0;
                        $sidakbuahmuah[$key][$key1]['persen_lwtMtng'] =  0;
                        $sidakbuahmuah[$key][$key1]['skor_lewatMTng'] = 0;
                        $sidakbuahmuah[$key][$key1]['janjang_kosong'] = 0;
                        $sidakbuahmuah[$key][$key1]['persen_kosong'] = 0;
                        $sidakbuahmuah[$key][$key1]['skor_kosong'] = 0;
                        $sidakbuahmuah[$key][$key1]['vcut'] = 0;
                        $sidakbuahmuah[$key][$key1]['karung'] = 0;
                        $sidakbuahmuah[$key][$key1]['vcut_persen'] = 0;
                        $sidakbuahmuah[$key][$key1]['vcut_skor'] = 0;
                        $sidakbuahmuah[$key][$key1]['abnormal'] = 0;
                        $sidakbuahmuah[$key][$key1]['abnormal_persen'] = 0;
                        $sidakbuahmuah[$key][$key1]['rat_dmg'] = 0;
                        $sidakbuahmuah[$key][$key1]['rd_persen'] = 0;
                        $sidakbuahmuah[$key][$key1]['TPH'] = 0;
                        $sidakbuahmuah[$key][$key1]['persen_krg'] = 0;
                        $sidakbuahmuah[$key][$key1]['skor_kr'] = 0;
                        $sidakbuahmuah[$key][$key1]['All_skor'] = 0;
                        $sidakbuahmuah[$key][$key1]['kategori'] = 0;
                        $sidakbuahmuah[$key][$key1]['csfxr'] = 0;
                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidakbuahmuah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                    }
                }
                if ($sum_krx != 0) {
                    $total_kr = round($sum_krx / $dataBLokx, 2);
                } else {
                    $total_kr = 0;
                }
                $per_kr = round($total_kr * 100, 2);
                $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

                $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                $em = 'EM';

                $nama_em = '';

                // dd($key1);
                foreach ($queryAsisten as $ast => $asisten) {
                    if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                        $nama_em = $asisten['nama'];
                    }
                }
                $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

                $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

                $sidakbuahmuah[$key]['jjg_mantah'] = $jjg_mth;
                $sidakbuahmuah[$key]['persen_jjgmentah'] = $skor_jjgMTh;

                if ($csrms == 0) {
                    $sidakbuahmuah[$key]['check_arr'] = 'kosong';
                    $sidakbuahmuah[$key]['All_skor'] = '-';
                } else {
                    $sidakbuahmuah[$key]['check_arr'] = 'ada';
                    $sidakbuahmuah[$key]['All_skor'] = $allSkor;
                }

                $sidakbuahmuah[$key]['Jumlah_janjang'] = $jjg_samplex;
                $sidakbuahmuah[$key]['csrms'] = $csrms;
                $sidakbuahmuah[$key]['blok'] = $dataBLokx;
                $sidakbuahmuah[$key]['EM'] = 'EM';
                $sidakbuahmuah[$key]['Nama_assist'] = $nama_em;
                $sidakbuahmuah[$key]['nama_staff'] = '-';
                $sidakbuahmuah[$key]['tnp_brd'] = $tnpBRDx;
                $sidakbuahmuah[$key]['krg_brd'] = $krgBRDx;
                $sidakbuahmuah[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidakbuahmuah[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidakbuahmuah[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
                $sidakbuahmuah[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


                $sidakbuahmuah[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
                $sidakbuahmuah[$key]['persen_totalJjg'] = $skor_total;
                $sidakbuahmuah[$key]['skor_total'] = sidak_brdTotal($skor_total);
                $sidakbuahmuah[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
                $sidakbuahmuah[$key]['persen_jjgMtang'] = $skor_jjgMSk;
                $sidakbuahmuah[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                $sidakbuahmuah[$key]['lewat_matang'] = $overripex;
                $sidakbuahmuah[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
                $sidakbuahmuah[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                $sidakbuahmuah[$key]['janjang_kosong'] = $emptyx;
                $sidakbuahmuah[$key]['persen_kosong'] = $skor_jjgKosong;
                $sidakbuahmuah[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                $sidakbuahmuah[$key]['vcut'] = $vcutx;
                $sidakbuahmuah[$key]['vcut_persen'] = $skor_vcut;
                $sidakbuahmuah[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                $sidakbuahmuah[$key]['abnormal'] = $abrx;

                $sidakbuahmuah[$key]['rat_dmg'] = $rdx;

                $sidakbuahmuah[$key]['karung'] = $sum_krx;
                $sidakbuahmuah[$key]['TPH'] = $total_kr;
                $sidakbuahmuah[$key]['persen_krg'] = $per_kr;
                $sidakbuahmuah[$key]['skor_kr'] = sidak_PengBRD($per_kr);
                // $sidakbuahmuah[$key]['All_skor'] = $allSkor;
                $sidakbuahmuah[$key]['kategori'] = sidak_akhir($allSkor);


                $jjg_samplexy += $jjg_samplex;
                $tnpBRDxy += $tnpBRDx;
                $krgBRDxy += $krgBRDx;
                $abrxy += $abrx;
                $overripexy += $overripex;
                $emptyxy += $emptyx;
                $vcutxy += $vcutx;
                $rdxy += $rdx;
                $dataBLokxy += $dataBLokx;
                $sum_krxy += $sum_krx;
                $csrmsy += $csrms;
            }

            if ($sum_krxy != 0) {
                $total_kr = round($sum_krxy / $dataBLokxy, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplexy - $abrxy != 0 ? (($tnpBRDxy + $krgBRDxy) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplexy - $abrxy != 0 ? (($jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy)) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplexy - $abrxy != 0 ? ($overripexy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplexy - $abrxy != 0 ? ($emptyxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplexy != 0 ? ($vcutxy / $jjg_samplexy) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mthxy = $tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy;

            $skor_jjgMTh = ($jjg_samplexy - $abrxy != 0) ? round($jjg_mth / ($jjg_samplexy - $abrxy) * 100, 2) : 0;
            if ($csrmsy == 0) {
                $check_arr = 'kosong';
                $All_skor = '-';
            } else {
                $check_arr = 'ada';
                $All_skor = $allSkor;
            };
            $sidakbuahmuah['PT.MUA'] = [
                'jjg_mantah' => $jjg_mthxy,
                'persen_jjgmentah' => $skor_jjgMTh,
                'check_arr' => $check_arr,
                'All_skor' => $All_skor,
                'Jumlah_janjang' => $jjg_samplexy,
                'csrms' => $csrmsy,
                'blok' => $dataBLokxy,
                'EM' => 'EM',
                'Nama_assist' => $nama_em,
                'nama_staff' => '-',
                'tnp_brd' => $tnpBRDxy,
                'krg_brd' => $krgBRDxy,
                'persenTNP_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($tnpBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'persenKRG_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($krgBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'abnormal_persen' => round(($jjg_samplexy != 0 ? ($abrxy / $jjg_samplexy) * 100 : 0), 2),
                'rd_persen' => round(($jjg_samplexy != 0 ? ($rdxy / $jjg_samplexy) * 100 : 0), 2),
                'total_jjg' => $tnpBRDxy + $krgBRDxy,
                'persen_totalJjg' => $skor_total,
                'skor_total' => sidak_brdTotal($skor_total),
                'jjg_matang' => $jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy),
                'persen_jjgMtang' => $skor_jjgMSk,
                'skor_jjgMatang' => sidak_matangSKOR($skor_jjgMSk),
                'lewat_matang' => $overripexy,
                'persen_lwtMtng' =>  $skor_lewatMTng,
                'skor_lewatMTng' => sidak_lwtMatang($skor_lewatMTng),
                'janjang_kosong' => $emptyxy,
                'persen_kosong' => $skor_jjgKosong,
                'skor_kosong' => sidak_jjgKosong($skor_jjgKosong),
                'vcut' => $vcutxy,
                'vcut_persen' => $skor_vcut,
                'vcut_skor' => sidak_tangkaiP($skor_vcut),
                'abnormal' => $abrxy,
                'rat_dmg' => $rdxy,
                'karung' => $sum_krxy,
                'TPH' => $total_kr,
                'persen_krg' => $per_kr,
                'skor_kr' => sidak_PengBRD($per_kr),
                'kategori' => sidak_akhir($allSkor),
            ];

            // dd($sidakbuahmuah);

            // sidak tph 

            // dd($newSidak);

            $newsidakendmua = [];
            foreach ($defafdmua as $key => $value) {
                $divest1 = 0;
                $scoreest1 = 0;
                $totbrd1 = 0;
                $totjjg1 = 0;
                $v2check51 = 0;
                foreach ($value as $key2 => $value2) {
                    $divest = 0;
                    $scoreest = 0;
                    $totbrd = 0;
                    $totjjg = 0;
                    $v2check5 = 0;
                    foreach ($value2 as $key3 => $value3) {
                        foreach ($newSidak as $keysidak => $valsidak) {
                            if ($key2 == $keysidak) {
                                foreach ($valsidak as $keysidak1 => $valsidak1) {
                                    if ($keysidak1 == $key3) {
                                        // Key exists, assign values
                                        $deviden = $valsidak1['deviden'];
                                        $totalscore = $valsidak1['total_score'];
                                        $totalbrd = $valsidak1['total_brd'];
                                        $total_janjang = $valsidak1['total_janjang'];
                                        $v2check4 = $valsidak1['v2check5'];

                                        $newsidakendmua[$key][$key2][$key3]['deviden'] = $deviden;
                                        $newsidakendmua[$key][$key2][$key3]['total_score'] = $totalscore;
                                        $newsidakendmua[$key][$key2][$key3]['total_brd'] = $totalbrd;
                                        $newsidakendmua[$key][$key2][$key3]['total_janjang'] = $total_janjang;
                                        $newsidakendmua[$key][$key2][$key3]['v2check4'] = $v2check4;

                                        $divest += $deviden;
                                        $scoreest += $totalscore;
                                        $totbrd += $totalbrd;
                                        $totjjg += $total_janjang;
                                        $v2check5 += $v2check4;
                                    }
                                }
                            }
                        }
                        // If key not found, set default values
                        if (!isset($newsidakendmua[$key][$key2][$key3])) {
                            $newsidakendmua[$key][$key2][$key3]['deviden'] = 0;
                            $newsidakendmua[$key][$key2][$key3]['total_score'] = 0;
                            $newsidakendmua[$key][$key2][$key3]['total_brd'] = 0;
                            $newsidakendmua[$key][$key2][$key3]['total_janjang'] = 0;
                            $newsidakendmua[$key][$key2][$key3]['v2check4'] = 0;
                        }
                    }
                    if ($v2check5 != 0) {
                        $data = 'ada';
                        $estatescorx = ($divest !== 0) ? round($scoreest / $divest, 2) : 0;
                        $newskaxa = ($divest !== 0) ? round($scoreest / $divest, 2) : 0;
                    } else {
                        $data = 'kosong';
                        $estatescorx = 0;
                        $newskaxa = '-';
                    }

                    // Assign calculated values outside the innermost loop
                    $newsidakendmua[$key][$key2]['deviden'] = $divest;
                    $newsidakendmua[$key][$key2]['v2check5'] = $v2check5;
                    $newsidakendmua[$key][$key2]['checkdata'] = $data;
                    $newsidakendmua[$key][$key2]['score_estate'] = $newskaxa;
                    $newsidakendmua[$key][$key2]['totbrd'] = $totbrd;
                    $newsidakendmua[$key][$key2]['totjjg'] = $totjjg;


                    $divest1 +=  $divest;
                    $scoreest1 += $estatescorx;
                    $totbrd1 += $totbrd;
                    $totjjg1 +=  $totjjg;
                    $v2check51 += $v2check5;
                }
                if ($v2check51 != 0) {
                    $data = 'ada';
                    $skor = round($scoreest1 / $divest1, 2);
                } else {
                    $data = 'kosong';
                    $skor = '-';
                }
                $newsidakendmua[$key]['PT.MUA'] = [
                    'deviden' => $divest1,
                    'v2check6' => $v2check51,
                    'checkdata' => $data,
                    'score_estate' => $skor

                ];
            }
            // dd($newsidakendmua);
            foreach ($newsidakendmua as $key => $value) {
                # code...
                $newsidakendmua = $value;
            }

            // sidak qcinspeksi 

            // mutu ancak 
            $defaultNewmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultNewmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            $mergedDatamua = array();
            foreach ($defaultNewmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataPerBulan)) {
                        if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                            if (!empty($dataPerBulan[$estKey][$afdKey])) {
                                $mergedDatamua[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                            } else {
                                $mergedDatamua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mergedDatamua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedDatamua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtancakWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mergedDatamua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtancakWIltab1mua[$value['wil']][$key2] = array_merge($mtancakWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }
            $qcinsepksicak = array();
            foreach ($mtancakWIltab1mua as $key => $value) if (!empty($value)) {
                $pokok_panenWil = 0;
                $jum_haWil = 0;
                $janjang_panenWil = 0;
                $p_panenWil = 0;
                $k_panenWil = 0;
                $brtgl_panenWil = 0;
                $bhts_panenWil = 0;
                $bhtm1_panenWil = 0;
                $bhtm2_panenWil = 0;
                $bhtm3_oanenWil = 0;
                $pelepah_swil = 0;
                $totalPKTwil = 0;
                $sumBHWil = 0;
                $akpWil = 0;
                $brdPerwil = 0;
                $sumPerBHWil = 0;
                $perPiWil = 0;
                $totalWil = 0;
                $checking3 = 0;
                foreach ($value as $key1 => $value1) if (!empty($value2)) {
                    $pokok_panenEst = 0;
                    $jum_haEst =  0;
                    $janjang_panenEst =  0;
                    $akpEst =  0;
                    $p_panenEst =  0;
                    $k_panenEst =  0;
                    $brtgl_panenEst = 0;
                    $brdPerjjgEst =  0;
                    $bhtsEST = 0;
                    $bhtm1EST = 0;
                    $bhtm2EST = 0;
                    $bhtm3EST = 0;
                    $pelepah_sEST = 0;
                    $checking2 = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $akp = 0;
                        $skor_bTinggal = 0;
                        $brdPerjjg = 0;
                        $ttlSkorMA = 0;
                        $listBlokPerAfd = array();
                        $jum_ha = 0;
                        $totalPokok = 0;
                        $totalPanen = 0;
                        $totalP_panen = 0;
                        $totalK_panen = 0;
                        $totalPTgl_panen = 0;
                        $totalbhts_panen = 0;
                        $totalbhtm1_panen = 0;
                        $totalbhtm2_panen = 0;
                        $totalbhtm3_oanen = 0;
                        $totalpelepah_s = 0;
                        $check_input = 'kosong';
                        $nilai_input = 0;
                        $checking1 = count($value2);
                        // dd($value2);
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            // dd($value3);
                            if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                                $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            }
                            $jum_ha = count($listBlokPerAfd);

                            $totalPokok += $value3["sample"];
                            $totalPanen +=  $value3["jjg"];
                            $totalP_panen += $value3["brtp"];
                            $totalK_panen += $value3["brtk"];
                            $totalPTgl_panen += $value3["brtgl"];

                            $totalbhts_panen += $value3["bhts"];
                            $totalbhtm1_panen += $value3["bhtm1"];
                            $totalbhtm2_panen += $value3["bhtm2"];
                            $totalbhtm3_oanen += $value3["bhtm3"];

                            $totalpelepah_s += $value3["ps"];
                            $check_input = $value3["jenis_input"];
                            $nilai_input = $value3["skor_akhir"];
                        }


                        if ($totalPokok != 0) {
                            $akp = round(($totalPanen / $totalPokok) * 100, 1);
                        } else {
                            $akp = 0;
                        }


                        $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                        if ($totalPanen != 0) {
                            $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                        } else {
                            $brdPerjjg = 0;
                        }

                        $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                        if ($sumBH != 0) {
                            $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                        } else {
                            $sumPerBH = 0;
                        }

                        if ($totalpelepah_s != 0) {
                            $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                        } else {
                            $perPl = 0;
                        }





                        $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                        if ($checking1 != 0) {
                            $qcinsepksicak[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcinsepksicak[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                            // $qcinsepksicak[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                        } else {
                            $qcinsepksicak[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcinsepksicak[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                            // $qcinsepksicak[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                        }

                        // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                        $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                        $qcinsepksicak[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                        $qcinsepksicak[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                        $qcinsepksicak[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                        $qcinsepksicak[$key][$key1][$key2]['akp_rl'] = $akp;

                        $qcinsepksicak[$key][$key1][$key2]['p'] = $totalP_panen;
                        $qcinsepksicak[$key][$key1][$key2]['k'] = $totalK_panen;
                        $qcinsepksicak[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                        $qcinsepksicak[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcinsepksicak[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                        // data untuk buah tinggal
                        $qcinsepksicak[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                        $qcinsepksicak[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                        $qcinsepksicak[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcinsepksicak[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                        $qcinsepksicak[$key][$key1][$key2]['palepah_per'] = $perPl;
                        // total skor akhir

                        $qcinsepksicak[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                        $qcinsepksicak[$key][$key1][$key2]['check_input'] = $check_input;
                        $qcinsepksicak[$key][$key1][$key2]['nilai_input'] = $nilai_input;
                        $qcinsepksicak[$key][$key1][$key2]['checking1'] = $checking1;

                        $pokok_panenEst += $totalPokok;

                        $jum_haEst += $jum_ha;
                        $janjang_panenEst += $totalPanen;

                        $p_panenEst += $totalP_panen;
                        $k_panenEst += $totalK_panen;
                        $brtgl_panenEst += $totalPTgl_panen;

                        // bagian buah tinggal
                        $bhtsEST   += $totalbhts_panen;
                        $bhtm1EST += $totalbhtm1_panen;
                        $bhtm2EST   += $totalbhtm2_panen;
                        $bhtm3EST   += $totalbhtm3_oanen;
                        // data untuk pelepah sengklek
                        $pelepah_sEST += $totalpelepah_s;
                        $checking2 += $checking1;
                    } else {
                        $qcinsepksicak[$key][$key1][$key2]['pokok_sample'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['ha_sample'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['jumlah_panen'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['akp_rl'] =  0;
                        $qcinsepksicak[$key][$key1][$key2]['check_data'] = 'kosong';
                        $qcinsepksicak[$key][$key1][$key2]['p'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['k'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['tgl'] = 0;

                        // $qcinsepksicak[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcinsepksicak[$key][$key1][$key2]['brd/jjg'] = 0;

                        // data untuk buah tinggal
                        $qcinsepksicak[$key][$key1][$key2]['bhts_s'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm1'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm2'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['bhtm3'] = 0;

                        // $qcinsepksicak[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcinsepksicak[$key][$key1][$key2]['palepah_pokok'] = 0;
                        // total skor akhi0;

                        $qcinsepksicak[$key][$key1][$key2]['skor_bh'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['skor_brd'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['skor_ps'] = 0;
                        $qcinsepksicak[$key][$key1][$key2]['skor_akhir'] = 0;
                    }

                    $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                    $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                    // dd($sumBHEst);
                    if ($pokok_panenEst != 0) {
                        $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                    } else {
                        $akpEst = 0;
                    }

                    if ($janjang_panenEst != 0) {
                        $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                    } else {
                        $brdPerjjgEst = 0;
                    }



                    // dd($sumBHEst);
                    if ($sumBHEst != 0) {
                        $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                    } else {
                        $sumPerBHEst = 0;
                    }

                    if ($pokok_panenEst != 0) {
                        $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                    } else {
                        $perPlEst = 0;
                    }


                    $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                    if ($checking2 != 0) {
                        $qcinsepksicak[$key][$key1]['check_data'] = 'ada';
                        // $qcinsepksicak[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                        // $qcinsepksicak[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                    } else {
                        $qcinsepksicak[$key][$key1]['check_data'] = 'kosong';
                        // $qcinsepksicak[$key][$key1]['skor_brd'] = $skor_brd = 0;
                        // $qcinsepksicak[$key][$key1]['skor_ps'] = $skor_ps = 0;
                    }

                    // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                    $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                    //PENAMPILAN UNTUK PERESTATE
                    $qcinsepksicak[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                    $qcinsepksicak[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    $qcinsepksicak[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                    $qcinsepksicak[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                    $qcinsepksicak[$key][$key1]['ha_sample'] =  $jum_haEst;
                    $qcinsepksicak[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                    $qcinsepksicak[$key][$key1]['akp_rl'] =  $akpEst;

                    $qcinsepksicak[$key][$key1]['p'] = $p_panenEst;
                    $qcinsepksicak[$key][$key1]['k'] = $k_panenEst;
                    $qcinsepksicak[$key][$key1]['tgl'] = $brtgl_panenEst;

                    $qcinsepksicak[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcinsepksicak[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                    $qcinsepksicak[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                    // data untuk buah tinggal
                    $qcinsepksicak[$key][$key1]['bhts_s'] = $bhtsEST;
                    $qcinsepksicak[$key][$key1]['bhtm1'] = $bhtm1EST;
                    $qcinsepksicak[$key][$key1]['bhtm2'] = $bhtm2EST;
                    $qcinsepksicak[$key][$key1]['bhtm3'] = $bhtm3EST;
                    $qcinsepksicak[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                    $qcinsepksicak[$key][$key1]['palepah_per'] = $perPlEst;
                    // total skor akhir

                    $qcinsepksicak[$key][$key1]['skor_akhir'] = $totalSkorEst;

                    //perhitungn untuk perwilayah

                    $pokok_panenWil += $pokok_panenEst;
                    $jum_haWil += $jum_haEst;
                    $janjang_panenWil += $janjang_panenEst;
                    $p_panenWil += $p_panenEst;
                    $k_panenWil += $k_panenEst;
                    $brtgl_panenWil += $brtgl_panenEst;
                    // bagian buah tinggal
                    $bhts_panenWil += $bhtsEST;
                    $bhtm1_panenWil += $bhtm1EST;
                    $bhtm2_panenWil += $bhtm2EST;
                    $bhtm3_oanenWil += $bhtm3EST;
                    $pelepah_swil += $pelepah_sEST;
                    $checking3 += $checking2;
                } else {
                    $qcinsepksicak[$key][$key1]['pokok_sample'] = 0;
                    $qcinsepksicak[$key][$key1]['ha_sample'] =  0;
                    $qcinsepksicak[$key][$key1]['jumlah_panen'] = 0;
                    $qcinsepksicak[$key][$key1]['akp_rl'] =  0;

                    $qcinsepksicak[$key][$key1]['p'] = 0;
                    $qcinsepksicak[$key][$key1]['k'] = 0;
                    $qcinsepksicak[$key][$key1]['tgl'] = 0;

                    // $qcinsepksicak[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcinsepksicak[$key][$key1]['brd/jjgest'] = 0;
                    $qcinsepksicak[$key][$key1]['buah/jjg'] = 0;
                    // data untuk buah tinggal
                    $qcinsepksicak[$key][$key1]['bhts_s'] = 0;
                    $qcinsepksicak[$key][$key1]['bhtm1'] = 0;
                    $qcinsepksicak[$key][$key1]['bhtm2'] = 0;
                    $qcinsepksicak[$key][$key1]['bhtm3'] = 0;
                    $qcinsepksicak[$key][$key1]['palepah_pokok'] = 0;
                    // total skor akhir
                    $qcinsepksicak[$key][$key1]['skor_bh'] =  0;
                    $qcinsepksicak[$key][$key1]['skor_brd'] = 0;
                    $qcinsepksicak[$key][$key1]['skor_ps'] = 0;
                    $qcinsepksicak[$key][$key1]['skor_akhir'] = 0;
                }
                $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
                $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

                if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                    $akpWil = 0;
                } else {

                    $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
                }

                if ($totalPKTwil != 0) {
                    $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
                } else {
                    $brdPerwil = 0;
                }

                // dd($sumBHEst);
                if ($sumBHWil != 0) {
                    $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
                } else {
                    $sumPerBHWil = 0;
                }

                if ($pokok_panenWil != 0) {
                    $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
                } else {
                    $perPiWil = 0;
                }

                $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

                if ($checking3 != 0) {
                    $check_data = 'ada';
                } else {
                    $check_data = 'kosong';
                }

                // $totalWil = $skor_bh + $skor_brd + $skor_ps;
                $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

                $qcinsepksicak[$key]['PT.MUA'] = [
                    'pokok_sample' => $pokok_panenWil,
                    'ha_sample' =>  $jum_haWil,
                    'check_data' =>  $check_data,
                    'checking3' =>  $checking3,
                    'jumlah_panen' => $janjang_panenWil,
                    'akp_rl' =>  $akpWil,
                    'p' => $p_panenWil,
                    'k' => $k_panenWil,
                    'tgl' => $brtgl_panenWil,
                    'total_brd' => $totalPKTwil,
                    'total_brd' => $skor_bTinggal,
                    'brd/jjgwil' => $brdPerwil,
                    'buah/jjgwil' => $sumPerBHWil,
                    'bhts_s' => $bhts_panenWil,
                    'bhtm1' => $bhtm1_panenWil,
                    'bhtm2' => $bhtm2_panenWil,
                    'bhtm3' => $bhtm3_oanenWil,
                    'total_buah' => $sumBHWil,
                    'total_buah_per' => $sumPerBHWil,
                    'jjgperBuah' => number_format($sumPerBH, 3),
                    'palepah_pokok' => $pelepah_swil,
                    'palepah_per' => $perPiWil,
                    'skor_bh' => skor_buah_Ma($sumPerBHWil),
                    'skor_brd' => skor_brd_ma($brdPerwil),
                    'skor_ps' => skor_palepah_ma($perPiWil),
                    'skor_akhir' => $totalWil,
                ];
            }
            foreach ($qcinsepksicak as $key => $value) {
                $qcinsepksicak = $value;
            }
            //mutubuah
            $defaultMTbuahmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultMTbuahmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            $mutuBuahMergemua = array();
            foreach ($defaultMTbuahmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTBuah)) {
                        if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                            if (!empty($dataMTBuah[$estKey][$afdKey])) {
                                $mutuBuahMergemua[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                            } else {
                                $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtBuahWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuBuahMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtBuahWIltab1mua[$value['wil']][$key2] = array_merge($mtBuahWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }
            $qcinsmuabuah = array();
            foreach ($mtBuahWIltab1mua as $key => $value) if (is_array($value)) {
                $jum_haWil = 0;
                $sum_SamplejjgWil = 0;
                $sum_bmtWil = 0;
                $sum_bmkWil = 0;
                $sum_overWil = 0;
                $sum_abnorWil = 0;
                $sum_kosongjjgWil = 0;
                $sum_vcutWil = 0;
                $sum_krWil = 0;
                $no_Vcutwil = 0;

                foreach ($value as $key1 => $value1) if (is_array($value1)) {
                    $jum_haEst  = 0;
                    $sum_SamplejjgEst = 0;
                    $sum_bmtEst = 0;
                    $sum_bmkEst = 0;
                    $sum_overEst = 0;
                    $sum_abnorEst = 0;
                    $sum_kosongjjgEst = 0;
                    $sum_vcutEst = 0;
                    $sum_krEst = 0;
                    $no_VcutEst = 0;

                    foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                        $sum_bmt = 0;
                        $sum_bmk = 0;
                        $sum_over = 0;
                        $dataBLok = 0;
                        $sum_Samplejjg = 0;
                        $PerMth = 0;
                        $PerMsk = 0;
                        $PerOver = 0;
                        $sum_abnor = 0;
                        $sum_kosongjjg = 0;
                        $Perkosongjjg = 0;
                        $sum_vcut = 0;
                        $PerVcut = 0;
                        $PerAbr = 0;
                        $sum_kr = 0;
                        $total_kr = 0;
                        $per_kr = 0;
                        $totalSkor = 0;
                        $jum_ha = 0;
                        $no_Vcut = 0;
                        $jml_mth = 0;
                        $jml_mtg = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = [];
                        $dtBlok = 0;
                        // $combination_counts = array();
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                            $dtBlok = count($listBlokPerAfd);

                            // $jum_ha = count($listBlokPerAfd);
                            $sum_bmt += $value3['bmt'];
                            $sum_bmk += $value3['bmk'];
                            $sum_over += $value3['overripe'];
                            $sum_kosongjjg += $value3['empty_bunch'];
                            $sum_vcut += $value3['vcut'];
                            $sum_kr += $value3['alas_br'];


                            $sum_Samplejjg += $value3['jumlah_jjg'];
                            $sum_abnor += $value3['abnormal'];
                        }

                        // $dataBLok = count($combination_counts);
                        $dataBLok = $dtBlok;
                        $jml_mth = ($sum_bmt + $sum_bmk);
                        $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 3);
                        } else {
                            $total_kr = 0;
                        }


                        $per_kr = round($total_kr * 100, 3);
                        if ($jml_mth != 0) {
                            $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMth = 0;
                        }
                        if ($jml_mtg != 0) {
                            $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMsk = 0;
                        }
                        if ($sum_over != 0) {
                            $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerOver = 0;
                        }
                        if ($sum_kosongjjg != 0) {
                            $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $Perkosongjjg = 0;
                        }
                        if ($sum_vcut != 0) {
                            $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerVcut = 0;
                        }

                        if ($sum_abnor != 0) {
                            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerAbr = 0;
                        }

                        $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                        if (!empty($nonZeroValues)) {
                            $qcinsmuabuah[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                        } else {
                            $qcinsmuabuah[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                            // $qcinsmuabuah[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                        }

                        // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                        $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                        $qcinsmuabuah[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                        $qcinsmuabuah[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                        $qcinsmuabuah[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                        $qcinsmuabuah[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                        $qcinsmuabuah[$key][$key1][$key2]['total_over'] = $sum_over;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perOver'] = $PerOver;
                        $qcinsmuabuah[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                        $qcinsmuabuah[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                        $qcinsmuabuah[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                        $qcinsmuabuah[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                        $qcinsmuabuah[$key][$key1][$key2]['perVcut'] = $PerVcut;

                        $qcinsmuabuah[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                        $qcinsmuabuah[$key][$key1][$key2]['total_kr'] = $total_kr;
                        $qcinsmuabuah[$key][$key1][$key2]['persen_kr'] = $per_kr;

                        // skoring
                        $qcinsmuabuah[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                        $qcinsmuabuah[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                        $qcinsmuabuah[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                        $qcinsmuabuah[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                        $qcinsmuabuah[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                        $qcinsmuabuah[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                        $qcinsmuabuah[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                        //perhitungan estate
                        $jum_haEst += $dataBLok;
                        $sum_SamplejjgEst += $sum_Samplejjg;
                        $sum_bmtEst += $jml_mth;
                        $sum_bmkEst += $jml_mtg;
                        $sum_overEst += $sum_over;
                        $sum_abnorEst += $sum_abnor;
                        $sum_kosongjjgEst += $sum_kosongjjg;
                        $sum_vcutEst += $sum_vcut;
                        $sum_krEst += $sum_kr;
                    } else {
                        $qcinsmuabuah[$key][$key1][$key2]['tph_baris_blok'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['sampleJJG_total'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_mentah'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perMentah'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_masak'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perMasak'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_over'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perOver'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_abnormal'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['perAbnormal'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_jjgKosong'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_vcut'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['perVcut'] = 0;

                        $qcinsmuabuah[$key][$key1][$key2]['jum_kr'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['total_kr'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['persen_kr'] = 0;

                        // skoring
                        $qcinsmuabuah[$key][$key1][$key2]['skor_mentah'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_masak'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_over'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_vcut'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_abnormal'] = 0;;
                        $qcinsmuabuah[$key][$key1][$key2]['skor_kr'] = 0;
                        $qcinsmuabuah[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                    }
                    $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                    if ($sum_krEst != 0) {
                        $total_krEst = round($sum_krEst / $jum_haEst, 3);
                    } else {
                        $total_krEst = 0;
                    }
                    // if ($sum_kr != 0) {
                    //     $total_kr = round($sum_kr / $dataBLok, 3);
                    // } else {
                    //     $total_kr = 0;
                    // }

                    if ($sum_bmtEst != 0) {
                        $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMthEst = 0;
                    }

                    if ($sum_bmkEst != 0) {
                        $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMskEst = 0;
                    }

                    if ($sum_overEst != 0) {
                        $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerOverEst = 0;
                    }
                    if ($sum_kosongjjgEst != 0) {
                        $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerkosongjjgEst = 0;
                    }
                    if ($sum_vcutEst != 0) {
                        $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerVcutest = 0;
                    }
                    if ($sum_abnorEst != 0) {
                        $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerAbrest = 0;
                    }
                    // $per_kr = round($sum_kr * 100);
                    $per_krEst = round($total_krEst * 100, 3);


                    $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                    if (!empty($nonZeroValues)) {
                        $qcinsmuabuah[$key][$key1]['check_data'] = 'ada';
                        // $qcinsmuabuah[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                        // $qcinsmuabuah[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                        // $qcinsmuabuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                        // $qcinsmuabuah[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                        // $qcinsmuabuah[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                    } else {
                        $qcinsmuabuah[$key][$key1]['check_data'] = 'kosong';
                        // $qcinsmuabuah[$key][$key1]['skor_masak'] = $skor_masak = 0;
                        // $qcinsmuabuah[$key][$key1]['skor_over'] = $skor_over = 0;
                        // $qcinsmuabuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $qcinsmuabuah[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                        // $qcinsmuabuah[$key][$key1]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                    $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                    $qcinsmuabuah[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                    $qcinsmuabuah[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                    $qcinsmuabuah[$key][$key1]['total_mentah'] = $sum_bmtEst;
                    $qcinsmuabuah[$key][$key1]['total_perMentah'] = $PerMthEst;
                    $qcinsmuabuah[$key][$key1]['total_masak'] = $sum_bmkEst;
                    $qcinsmuabuah[$key][$key1]['total_perMasak'] = $PerMskEst;
                    $qcinsmuabuah[$key][$key1]['total_over'] = $sum_overEst;
                    $qcinsmuabuah[$key][$key1]['total_perOver'] = $PerOverEst;
                    $qcinsmuabuah[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                    $qcinsmuabuah[$key][$key1]['total_perabnormal'] = $PerAbrest;
                    $qcinsmuabuah[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                    $qcinsmuabuah[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                    $qcinsmuabuah[$key][$key1]['total_vcut'] = $sum_vcutEst;
                    $qcinsmuabuah[$key][$key1]['perVcut'] = $PerVcutest;
                    $qcinsmuabuah[$key][$key1]['jum_kr'] = $sum_krEst;
                    $qcinsmuabuah[$key][$key1]['kr_blok'] = $total_krEst;

                    $qcinsmuabuah[$key][$key1]['persen_kr'] = $per_krEst;

                    // skoring
                    $qcinsmuabuah[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                    $qcinsmuabuah[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                    $qcinsmuabuah[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                    $qcinsmuabuah[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                    $qcinsmuabuah[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                    $qcinsmuabuah[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                    $qcinsmuabuah[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                    //hitung perwilayah
                    $jum_haWil += $jum_haEst;
                    $sum_SamplejjgWil += $sum_SamplejjgEst;
                    $sum_bmtWil += $sum_bmtEst;
                    $sum_bmkWil += $sum_bmkEst;
                    $sum_overWil += $sum_overEst;
                    $sum_abnorWil += $sum_abnorEst;
                    $sum_kosongjjgWil += $sum_kosongjjgEst;
                    $sum_vcutWil += $sum_vcutEst;
                    $sum_krWil += $sum_krEst;
                } else {
                    $qcinsmuabuah[$key][$key1]['tph_baris_blok'] = 0;
                    $qcinsmuabuah[$key][$key1]['sampleJJG_total'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_mentah'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_perMentah'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_masak'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_perMasak'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_over'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_perOver'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_abnormal'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_perabnormal'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_jjgKosong'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_perKosongjjg'] = 0;
                    $qcinsmuabuah[$key][$key1]['total_vcut'] = 0;
                    $qcinsmuabuah[$key][$key1]['perVcut'] = 0;
                    $qcinsmuabuah[$key][$key1]['jum_kr'] = 0;
                    $qcinsmuabuah[$key][$key1]['kr_blok'] = 0;
                    $qcinsmuabuah[$key][$key1]['persen_kr'] = 0;

                    // skoring
                    $qcinsmuabuah[$key][$key1]['skor_mentah'] = 0;
                    $qcinsmuabuah[$key][$key1]['skor_masak'] = 0;
                    $qcinsmuabuah[$key][$key1]['skor_over'] = 0;
                    $qcinsmuabuah[$key][$key1]['skor_jjgKosong'] = 0;
                    $qcinsmuabuah[$key][$key1]['skor_vcut'] = 0;
                    $qcinsmuabuah[$key][$key1]['skor_abnormal'] = 0;;
                    $qcinsmuabuah[$key][$key1]['skor_kr'] = 0;
                    $qcinsmuabuah[$key][$key1]['TOTAL_SKOR'] = 0;
                }

                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }



                if ($sum_krWil != 0) {
                    $total_krWil = round($sum_krWil / $jum_haWil, 3);
                } else {
                    $total_krWil = 0;
                }

                if ($sum_bmtWil != 0) {
                    $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMthWil = 0;
                }


                if ($sum_bmkWil != 0) {
                    $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMskWil = 0;
                }
                if ($sum_overWil != 0) {
                    $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerOverWil = 0;
                }
                if ($sum_kosongjjgWil != 0) {
                    $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerkosongjjgWil = 0;
                }
                if ($sum_vcutWil != 0) {
                    $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerVcutWil = 0;
                }
                if ($sum_abnorWil != 0) {
                    $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerAbrWil = 0;
                }
                $per_krWil = round($total_krWil * 100, 3);

                $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

                if (!empty($nonZeroValues)) {
                    $check_data = 'ada';
                } else {
                    $check_data = 'kosong';
                }

                // $totalSkorWil = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
                $qcinsmuabuah[$key]['PT.MUA'] = [
                    'tph_baris_blok' => $jum_haWil,
                    'check_data' => $check_data,
                    'sampleJJG_total' => $sum_SamplejjgWil,
                    'total_mentah' => $sum_bmtWil,
                    'total_perMentah' => $PerMthWil,
                    'total_masak' => $sum_bmkWil,
                    'total_perMasak' => $PerMskWil,
                    'total_over' => $sum_overWil,
                    'total_perOver' => $PerOverWil,
                    'total_abnormal' => $sum_abnorWil,
                    'total_perabnormal' => $PerAbrWil,
                    'total_jjgKosong' => $sum_kosongjjgWil,
                    'total_perKosongjjg' => $PerkosongjjgWil,
                    'total_vcut' => $sum_vcutWil,
                    'per_vcut' => $PerVcutWil,
                    'jum_kr' => $sum_krWil,
                    'kr_blok' => $total_krWil,

                    'persen_kr' => $per_krWil,

                    // skoring
                    'skor_mentah' => skor_buah_mentah_mb($PerMthWil),
                    'skor_masak' => skor_buah_masak_mb($PerMskWil),
                    'skor_over' => skor_buah_over_mb($PerOverWil),
                    'skor_jjgKosong' => skor_jangkos_mb($PerkosongjjgWil),
                    'skor_vcut' => skor_vcut_mb($PerVcutWil),
                    'skor_kr' => skor_abr_mb($per_krWil),
                    'TOTAL_SKOR' => $totalSkorWil,
                ];
            }

            foreach ($qcinsmuabuah as $key => $value) {
                $qcinsmuabuah = $value;
            }



            $defaultMtTransmua = array();
            foreach ($muaest as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defaultMtTransmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            $mutuAncakMergemua = array();
            foreach ($defaultMtTransmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTTrans)) {
                        if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                            if (!empty($dataMTTrans[$estKey][$afdKey])) {
                                $mutuAncakMergemua[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                            } else {
                                $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtTransWiltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuAncakMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtTransWiltab1mua[$value['wil']][$key2] = array_merge($mtTransWiltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }
            // dd($mtTransWiltab1mua);

            $qcinptransmua = array();
            foreach ($mtTransWiltab1mua as $key => $value) if (!empty($value)) {
                $dataBLokWil = 0;
                $sum_btWil = 0;
                $sum_rstWil = 0;
                $check3 = 0;
                foreach ($value as $key1 => $value1) if (!empty($value1)) {
                    $dataBLokEst = 0;
                    $sum_btEst = 0;
                    $sum_rstEst = 0;
                    $check2 = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();

                        $check1 = count($value2);
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            // }
                            $dataBLok = count($listBlokPerAfd);
                            $sum_bt += $value3['bt'];
                            $sum_rst += $value3['rst'];
                        }
                        $tot_sample = 0;  // Define the variable outside of the foreach loop


                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        }

                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                        $totalSkor =  skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                        if ($check1 != 0) {
                            $qcinptransmua[$key][$key1][$key2]['check_data'] = 'ada';
                        } else {
                            $qcinptransmua[$key][$key1][$key2]['check_data'] = "kosong";
                        }
                        // dd($transNewdata);






                        $qcinptransmua[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        $qcinptransmua[$key][$key1][$key2]['total_brd'] = $sum_bt;
                        $qcinptransmua[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                        $qcinptransmua[$key][$key1][$key2]['total_buah'] = $sum_rst;
                        $qcinptransmua[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                        $qcinptransmua[$key][$key1][$key2]['totalSkor'] = $totalSkor;
                        $qcinptransmua[$key][$key1][$key2]['check1'] = $check1;

                        //PERHITUNGAN PERESTATE
                        $dataBLokEst += $dataBLok;

                        $sum_btEst += $sum_bt;
                        $sum_rstEst += $sum_rst;
                        $check2 += $check1;

                        if ($dataBLokEst != 0) {
                            $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                        } else {
                            $brdPertphEst = 0;
                        }

                        if ($dataBLokEst != 0) {
                            $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                        } else {
                            $buahPerTPHEst = 0;
                        }

                        // dd($qcinptransmua);
                        $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                    } else {
                        $qcinptransmua[$key][$key1][$key2]['tph_sample'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['total_brd'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['total_brd/TPH'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['total_buah'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['skor_brdPertph'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                        $qcinptransmua[$key][$key1][$key2]['totalSkor'] = 0;
                    }

                    if ($check2 != 0) {
                        $qcinptransmua[$key][$key1]['check_data'] = 'ada';
                    } else {
                        $qcinptransmua[$key][$key1]['check_data'] = 'kosong';
                    }

                    // $totalSkorEst = $skor_brd + $skor_buah ;


                    $qcinptransmua[$key][$key1]['tph_sample'] = $dataBLokEst;
                    $qcinptransmua[$key][$key1]['total_brd'] = $sum_btEst;
                    $qcinptransmua[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                    $qcinptransmua[$key][$key1]['total_buah'] = $sum_rstEst;
                    $qcinptransmua[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                    $qcinptransmua[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                    $qcinptransmua[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                    $qcinptransmua[$key][$key1]['totalSkor'] = $totalSkorEst;
                    $qcinptransmua[$key][$key1]['check2'] = $check2;

                    //perhitungan per wil
                    $dataBLokWil += $dataBLokEst;
                    $sum_btWil += $sum_btEst;
                    $sum_rstWil += $sum_rstEst;
                    $check3 += $check2;

                    if ($dataBLokWil != 0) {
                        $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                    } else {
                        $brdPertphWil = 0;
                    }
                    if ($dataBLokWil != 0) {
                        $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                    } else {
                        $buahPerTPHWil = 0;
                    }

                    $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
                } else {
                    $qcinptransmua[$key][$key1]['tph_sample'] = 0;
                    $qcinptransmua[$key][$key1]['total_brd'] = 0;
                    $qcinptransmua[$key][$key1]['total_brd/TPH'] = 0;
                    $qcinptransmua[$key][$key1]['total_buah'] = 0;
                    $qcinptransmua[$key][$key1]['total_buahPerTPH'] = 0;
                    $qcinptransmua[$key][$key1]['skor_brdPertph'] = 0;
                    $qcinptransmua[$key][$key1]['skor_buahPerTPH'] = 0;
                    $qcinptransmua[$key][$key1]['totalSkor'] = 0;
                }

                if ($check3 != 0) {
                    $check_data = 'ada';
                } else {
                    $check_data = 'kosong';
                }

                $qcinptransmua[$key]['PT.MUA'] = [
                    'tph_sample' => $dataBLokWil,
                    'check_data' => $check_data,
                    'total_brd' => $sum_btWil,
                    'total_brd/TPH' => $brdPertphWil,
                    'total_buah' => $sum_rstWil,
                    'total_buahPerTPH' => $buahPerTPHWil,
                    'skor_brdPertph' =>   skor_brd_tinggal($brdPertphWil),
                    'skor_buahPerTPH' => skor_buah_tinggal($buahPerTPHWil),
                    'totalSkor' => $totalSkorWil,
                ];
            }
            foreach ($qcinptransmua as $key => $value) {
                $qcinptransmua = $value;
            }


            $qcinspeksimua = [];
            foreach ($qcinsepksicak as $key => $value) {
                foreach ($qcinsmuabuah as $key1 => $value1) {
                    foreach ($qcinptransmua as $key2 => $value2)   if (
                        $key == $key1
                        && $key == $key2
                    ) {
                        if ($value['check_data'] == 'kosong' && $value1['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                            $qcinspeksimua[$key]['TotalSkor'] = '-';
                            $qcinspeksimua[$key]['checkdata'] = 'kosong';
                        } else {
                            $qcinspeksimua[$key]['TotalSkor'] = $value['skor_akhir'] + $value1['TOTAL_SKOR'] + $value2['totalSkor'];
                            $qcinspeksimua[$key]['checkdata'] = 'ada';
                        }

                        $qcinspeksimua[$key]['est'] = $key;
                        $qcinspeksimua[$key]['afd'] = 'OA';
                    }
                }
            }


            $rekapmua = [];
            foreach ($qcinspeksimua as $key => $value) {
                if (
                    isset($sidakbuahmuah[$key]) &&
                    isset($newsidakendmua[$key])
                ) {
                    $valtph2 = $newsidakendmua[$key];
                    $valmtb = $sidakbuahmuah[$key];
                    $skortph = $valtph2['score_estate'] ?? null;
                    $skormtb = $valmtb['All_skor'] ?? null;

                    if ($valmtb['check_arr'] == 'ada') {
                        $databh = 1;
                    } else {
                        $databh = 0;
                    }
                    if ($value['checkdata'] == 'ada') {
                        $dataqc = 1;
                    } else {
                        $dataqc = 0;
                    }
                    if ($valtph2['checkdata'] == 'ada') {
                        $datatph = 1;
                    } else {
                        $datatph = 0;
                    }
                    // dd($key);
                    foreach ($queryAsisten as $keyx => $valuex) {
                        if ($valuex['est'] === $key && $valuex['afd'] === 'OA') {
                            $rekapmua[$key]['asistenafd'] = $valuex['nama'] ?? '-';
                            break;
                        } elseif ($valuex['est'] === $key && $valuex['afd'] === 'EM') {
                            $rekapmua[$key]['manager'] = $valuex['nama'] ?? '-';
                        }
                    }


                    $check = $databh + $dataqc + $datatph;
                    $rekapmua[$key]['skorqc'] = $value['TotalSkor'];
                    // $rekapmua[$key]['nama'] = $value['TotalSkor'];
                    $rekapmua[$key]['skor_mutubuah'] = $skormtb;
                    $rekapmua[$key]['skortph'] = $skortph;
                    $rekapmua[$key]['check'] = $check;

                    $a = $value['TotalSkor'];
                    $b = $skormtb;
                    $c = $skortph;

                    // Convert '-' to 0, keeping other values unchanged
                    $a = ($a === '-') ? 0 : $a;
                    $b = ($b === '-') ? 0 : $b;
                    $c = ($c === '-') ? 0 : $c;

                    $rekapmua[$key]['skorestate'] = round(($a + $b + $c) / $check, 2);
                }
            }
        } else {
            $rekapmua = [];
        }

        // dd($newsidakend);
        // dd($sidakbuahmuah, $mutu_buah);
        // end mua ====================================================================== 
        $rekapafd = [];
        foreach ($qcinspeksi as $keyqc => $valqc) {
            foreach ($valqc as $keyqc1 => $valqc1) {
                if (is_array($valqc1)) {

                    foreach ($valqc1 as $keyqc2 => $valqc2) {
                        $datacheck = [];
                        $datacheck2 = [];
                        $countAda = 0;
                        $countAda2 = 0;
                        $totalest = 0;

                        if (is_array($valqc2)) {
                            if (
                                isset($newsidakend[$keyqc][$keyqc1][$keyqc2]) &&
                                isset($mutu_buah[$keyqc][$keyqc1][$keyqc2])
                            ) {
                                $valtph2 = $newsidakend[$keyqc][$keyqc1][$keyqc2];
                                $valbh2 = $mutu_buah[$keyqc][$keyqc1][$keyqc2];
                                $valtph1 = $newsidakend[$keyqc][$keyqc1];
                                $valbh1 = $mutu_buah[$keyqc][$keyqc1];
                                // Extracting values
                                $skor_tph = $valtph2['total_score'] ?? null;
                                $skor_qc = $valqc2['TotalSkor'] ?? null;
                                $skor_buah = $valbh2['All_skor'] ?? null;
                                $tph_check = $valtph2['v2check4'];
                                $qc_check = $valqc2['data'] ?? 'ada';
                                $buah_check = $valbh2['csfxr'];

                                // dd($valtph2);

                                if ($tph_check != 0 && $skor_tph != 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } elseif ($tph_check != 0 && $skor_tph == 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } else {
                                    $tph = 'kosong';
                                    $tphskor = 0;
                                }

                                if ($buah_check != 0 && $skor_buah != 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } elseif ($buah_check != 0 && $skor_buah == 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } else {
                                    $buah = 'kosong';
                                    $buahskor = 0;
                                }
                                if ($qc_check != 'kosong' && $skor_qc != 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } elseif ($qc_check != 'kosong' && $skor_qc == 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } else {
                                    $qc = 'kosong';
                                    $qcskor = 0;
                                }

                                $datacheck[] = [$tph, $qc, $buah];
                                foreach ($datacheck[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda++;
                                    }
                                }
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === $keyqc2) {
                                    $rekapafd[$keyqc][$keyqc1][$keyqc2]['nama'] = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['tph_check'] = $tph;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['bgcolor'] = 'white';
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_tph'] = $tphskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['qc_check'] = $qc;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_qc'] = $qcskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['buah_check'] = $buah;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_buah'] = $buahskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['datacheck'] = $datacheck;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['validasi'] = $countAda;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['est'] = $keyqc1;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['afd'] = $keyqc2;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['total'] = $countAda !== 0 ? round(($tphskor + $qcskor + $buahskor) / $countAda, 2) : 0;

                                // dd($valbh1);

                                $skor_tph2 = $valtph1['score_estate'] ?? null;
                                $skor_qc2 = $valqc1['TotalSkorEST'] ?? null;
                                $skor_buah2 = $valbh1['All_skor'] ?? null;
                                $tph_check2 = $valtph1['v2check5'];
                                $qc_check2 = $valqc1['data'] ?? 'ada';
                                $buah_check2 = $valbh1['csrms'];


                                if ($tph_check2 != 0 && $skor_tph2 != 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } elseif ($tph_check2 != 0 && $skor_tph2 == 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } else {
                                    $tph2 = 'kosong';
                                    $tphskor2 = 0;
                                }

                                if ($buah_check2 != 0 && $skor_buah2 != 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } elseif ($buah_check2 != 0 && $skor_buah2 == 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } else {
                                    $buah2 = 'kosong';
                                    $buahskor2 = 0;
                                }
                                if ($qc_check2 != 'kosong' && $skor_qc2 != 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } elseif ($qc_check2 != 'kosong' && $skor_qc2 == 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } else {
                                    $qc2 = 'kosong';
                                    $qcskor2 = 0;
                                }

                                $datacheck2[] = [$tph2, $qc2, $buah2];
                                foreach ($datacheck2[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda2++;
                                    }
                                }
                                $namaqc = '-';
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === 'EM') {
                                    $namaqc = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $totalest = $countAda2 !== 0 ? round(($tphskor2 + $qcskor2 + $buahskor2) / $countAda2, 2) : 0;

                                // Assuming $estate is an individual entry and not part of $estates array for sorting
                                $estate = [
                                    'tph_check' => $tph2,
                                    'nama' => $namaqc,
                                    'bgcolor' => '#a0978d',
                                    'skor_tph' => $tphskor2,
                                    'qc_check' => $qc2,
                                    'skor_qc' => $qcskor2,
                                    'buah_check' => $buah2,
                                    'skor_buah' => $buahskor2,
                                    'validasi' => $countAda2,
                                    'est' => $keyqc1,
                                    'afd' => 'EM',
                                    'total' => $totalest
                                    // 'rank' => ???
                                ];

                                $totale = $totalest;
                            }
                        }
                    }
                    $rekapafd[$keyqc][$keyqc1]['est'] = $estate;
                    $getallest[] = $estate;
                }
            }
        }

        foreach ($rekapafd as $key => $value) {
            $estTotals = []; // Initialize an array to hold 'est' totals within this index

            foreach ($value as $estKey => $estValue) {
                if (isset($estValue['est'])) {
                    $est = $estValue['est'];
                    $afdElements = $estValue;
                    unset($afdElements['est']);

                    $totalsAfd = [];
                    foreach ($afdElements as $afdKey => $afdValue) {
                        $totalsAfd[$afdKey] = $afdValue['total'];
                    }

                    arsort($totalsAfd);

                    $rank = 1;
                    foreach ($totalsAfd as $afdKey => $totalAfd) {
                        $rekapafd[$key][$estKey][$afdKey]['rank'] = $rank;
                        $rank++;
                    }

                    // Accumulate 'est' totals within this index
                    $estTotals[$estKey] = $est['total'];
                }
            }

            // Sort 'est' totals within this index
            arsort($estTotals);

            // Assign ranks to each 'est' element within this index based on the sorted order of totals
            $rank = 1;
            foreach ($estTotals as $estKey => $totalEst) {
                $rekapafd[$key][$estKey]['est']['rank'] = $rank;
                $rank++;
            }
        }

        // dd($rekapafd);
        // dd($groupedDataAcnakreg2);
        $arr = array();
        $arr['rekapafd'] = $rekapafd;
        $arr['rekapmua'] = $rekapmua;

        echo json_encode($arr);
        exit();
    }

    public function getdataweek(Request $request)
    {


        $week = $request->input('week');

        $weekDateTime = new DateTime($week);
        $weekDateTime->setISODate((int)$weekDateTime->format('o'), (int)$weekDateTime->format('W'));

        $startDate = $weekDateTime->format('Y-m-d');
        $weekDateTime->modify('+6 days');
        $endDate = $weekDateTime->format('Y-m-d');

        // dd($startDate, $endDate);
        $regional = $request->input('reg');


        $queryAsisten = DB::connection('mysql2')->table('asisten_qc')
            ->select('asisten_qc.*')
            ->get();
        $queryAsisten = json_decode($queryAsisten, true);
        // dd($value2['datetime'], $endDate);
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->where('estate.emp', '!=', 1)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $queryEste = json_decode($queryEste, true);

        $muaest = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            // ->where('estate.emp', '!=', 1)
            ->whereIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get('est');
        $muaest = json_decode($muaest, true);


        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);

        $queryMTbuah = DB::connection('mysql2')->table('sidak_mutu_buah')
            ->select(
                "sidak_mutu_buah.*",
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(sidak_mutu_buah.datetime, "%Y") as tahun')
            )
            ->whereBetween('sidak_mutu_buah.datetime', [$startDate, $endDate])
            ->get();
        $queryMTbuah = $queryMTbuah->groupBy(['estate', 'afdeling']);
        $queryMTbuah = json_decode($queryMTbuah, true);


        $databulananBuah = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $keytph => $value3) {

                    $databulananBuah[$key][$key2][$keytph] = $value3;
                }
            }
        }

        $defPerbulanWil = array();

        foreach ($queryEste as $key2 => $value2) {
            foreach ($queryAfd as $key3 => $value3) {
                if ($value2['est'] == $value3['est']) {
                    $defPerbulanWil[$value2['est']][$value3['nama']] = 0;
                }
            }
        }



        foreach ($defPerbulanWil as $estateKey => $afdelingArray) {
            foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                    $defPerbulanWil[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                }
            }
        }

        $sidak_buah = array();
        // dd($defPerbulanWil);

        foreach ($defPerbulanWil as $key => $value) {
            $jjg_samplex = 0;
            $tnpBRDx = 0;
            $krgBRDx = 0;
            $abrx = 0;
            $overripex = 0;
            $emptyx = 0;
            $vcutx = 0;
            $rdx = 0;
            $dataBLokx = 0;
            $sum_krx = 0;
            $csrms = 0;
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    $jjg_sample = 0;
                    $tnpBRD = 0;
                    $krgBRD = 0;
                    $abr = 0;
                    $skor_total = 0;
                    $overripe = 0;
                    $empty = 0;
                    $vcut = 0;
                    $rd = 0;
                    $sum_kr = 0;
                    $allSkor = 0;
                    $combination_counts = array();
                    $newblok = 0;
                    $csfxr = count($value1);
                    foreach ($value1 as $key2 => $value2) {
                        $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $newblok = count($value1);
                        $jjg_sample += $value2['jumlah_jjg'];
                        $tnpBRD += $value2['bmt'];
                        $krgBRD += $value2['bmk'];
                        $abr += $value2['abnormal'];
                        $overripe += $value2['overripe'];
                        $empty += $value2['empty_bunch'];
                        $vcut += $value2['vcut'];
                        $rd += $value2['rd'];
                        $sum_kr += $value2['alas_br'];
                    }
                    // $dataBLok = count($combination_counts);
                    $dataBLok = $newblok;
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }
                    $per_kr = round($total_kr * 100, 2);
                    $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                    $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                    $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                    $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                    $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                    $sidak_buah[$key][$key1]['blok'] = $dataBLok;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = $tnpBRD;
                    $sidak_buah[$key][$key1]['krg_brd'] = $krgBRD;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                    $sidak_buah[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = $skor_total;
                    $sidak_buah[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                    $sidak_buah[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                    $sidak_buah[$key][$key1]['lewat_matang'] = $overripe;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                    $sidak_buah[$key][$key1]['janjang_kosong'] = $empty;
                    $sidak_buah[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                    $sidak_buah[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                    $sidak_buah[$key][$key1]['vcut'] = $vcut;
                    $sidak_buah[$key][$key1]['karung'] = $sum_kr;
                    $sidak_buah[$key][$key1]['vcut_persen'] = $skor_vcut;
                    $sidak_buah[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                    $sidak_buah[$key][$key1]['abnormal'] = $abr;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['rat_dmg'] = $rd;
                    $sidak_buah[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                    $sidak_buah[$key][$key1]['TPH'] = $total_kr;
                    $sidak_buah[$key][$key1]['persen_krg'] = $per_kr;
                    $sidak_buah[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                    $sidak_buah[$key][$key1]['All_skor'] = $allSkor;
                    $sidak_buah[$key][$key1]['csfxr'] = $csfxr;
                    $sidak_buah[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                    $jjg_samplex += $jjg_sample;
                    $tnpBRDx += $tnpBRD;
                    $krgBRDx += $krgBRD;
                    $abrx += $abr;
                    $overripex += $overripe;
                    $emptyx += $empty;
                    $vcutx += $vcut;

                    $rdx += $rd;

                    $dataBLokx += $newblok;
                    $sum_krx += $sum_kr;
                    $csrms += $csfxr;
                } else {

                    $sidak_buah[$key][$key1]['Jumlah_janjang'] = 0;
                    $sidak_buah[$key][$key1]['blok'] = 0;
                    $sidak_buah[$key][$key1]['est'] = $key;
                    $sidak_buah[$key][$key1]['afd'] = $key1;
                    $sidak_buah[$key][$key1]['nama_staff'] = '-';
                    $sidak_buah[$key][$key1]['tnp_brd'] = 0;
                    $sidak_buah[$key][$key1]['krg_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenTNP_brd'] = 0;
                    $sidak_buah[$key][$key1]['persenKRG_brd'] = 0;
                    $sidak_buah[$key][$key1]['total_jjg'] = 0;
                    $sidak_buah[$key][$key1]['persen_totalJjg'] = 0;
                    $sidak_buah[$key][$key1]['skor_total'] = 0;
                    $sidak_buah[$key][$key1]['jjg_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_jjgMtang'] = 0;
                    $sidak_buah[$key][$key1]['skor_jjgMatang'] = 0;
                    $sidak_buah[$key][$key1]['lewat_matang'] = 0;
                    $sidak_buah[$key][$key1]['persen_lwtMtng'] =  0;
                    $sidak_buah[$key][$key1]['skor_lewatMTng'] = 0;
                    $sidak_buah[$key][$key1]['janjang_kosong'] = 0;
                    $sidak_buah[$key][$key1]['persen_kosong'] = 0;
                    $sidak_buah[$key][$key1]['skor_kosong'] = 0;
                    $sidak_buah[$key][$key1]['vcut'] = 0;
                    $sidak_buah[$key][$key1]['karung'] = 0;
                    $sidak_buah[$key][$key1]['vcut_persen'] = 0;
                    $sidak_buah[$key][$key1]['vcut_skor'] = 0;
                    $sidak_buah[$key][$key1]['abnormal'] = 0;
                    $sidak_buah[$key][$key1]['abnormal_persen'] = 0;
                    $sidak_buah[$key][$key1]['rat_dmg'] = 0;
                    $sidak_buah[$key][$key1]['rd_persen'] = 0;
                    $sidak_buah[$key][$key1]['TPH'] = 0;
                    $sidak_buah[$key][$key1]['persen_krg'] = 0;
                    $sidak_buah[$key][$key1]['skor_kr'] = 0;
                    $sidak_buah[$key][$key1]['All_skor'] = 0;
                    $sidak_buah[$key][$key1]['kategori'] = 0;
                    $sidak_buah[$key][$key1]['csfxr'] = 0;
                    foreach ($queryAsisten as $ast => $asisten) {
                        if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                            $sidak_buah[$key][$key1]['nama_asisten'] = $asisten['nama'];
                        }
                    }
                }
            }
            if ($sum_krx != 0) {
                $total_kr = round($sum_krx / $dataBLokx, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

            $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

            $sidak_buah[$key]['jjg_mantah'] = $jjg_mth;
            $sidak_buah[$key]['persen_jjgmentah'] = $skor_jjgMTh;

            if ($jjg_samplex == 0 && $tnpBRDx == 0 &&   $krgBRDx == 0 && $abrx == 0 && $overripex == 0 && $emptyx == 0 &&  $vcutx == 0 &&  $rdx == 0 && $sum_krx == 0) {
                $sidak_buah[$key]['check_arr'] = 'kosong';
                $sidak_buah[$key]['All_skor'] = 0;
            } else {
                $sidak_buah[$key]['check_arr'] = 'ada';
                $sidak_buah[$key]['All_skor'] = $allSkor;
            }

            $sidak_buah[$key]['Jumlah_janjang'] = $jjg_samplex;
            $sidak_buah[$key]['csrms'] = $csrms;
            $sidak_buah[$key]['blok'] = $dataBLokx;
            $sidak_buah[$key]['EM'] = 'EM';
            $sidak_buah[$key]['Nama_assist'] = $nama_em;
            $sidak_buah[$key]['nama_staff'] = '-';
            $sidak_buah[$key]['tnp_brd'] = $tnpBRDx;
            $sidak_buah[$key]['krg_brd'] = $krgBRDx;
            $sidak_buah[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
            $sidak_buah[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
            $sidak_buah[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


            $sidak_buah[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
            $sidak_buah[$key]['persen_totalJjg'] = $skor_total;
            $sidak_buah[$key]['skor_total'] = sidak_brdTotal($skor_total);
            $sidak_buah[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
            $sidak_buah[$key]['persen_jjgMtang'] = $skor_jjgMSk;
            $sidak_buah[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
            $sidak_buah[$key]['lewat_matang'] = $overripex;
            $sidak_buah[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
            $sidak_buah[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
            $sidak_buah[$key]['janjang_kosong'] = $emptyx;
            $sidak_buah[$key]['persen_kosong'] = $skor_jjgKosong;
            $sidak_buah[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
            $sidak_buah[$key]['vcut'] = $vcutx;
            $sidak_buah[$key]['vcut_persen'] = $skor_vcut;
            $sidak_buah[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
            $sidak_buah[$key]['abnormal'] = $abrx;

            $sidak_buah[$key]['rat_dmg'] = $rdx;

            $sidak_buah[$key]['karung'] = $sum_krx;
            $sidak_buah[$key]['TPH'] = $total_kr;
            $sidak_buah[$key]['persen_krg'] = $per_kr;
            $sidak_buah[$key]['skor_kr'] = sidak_PengBRD($per_kr);
            // $sidak_buah[$key]['All_skor'] = $allSkor;
            $sidak_buah[$key]['kategori'] = sidak_akhir($allSkor);
        }


        $mutu_buah = array();
        foreach ($queryEste as $key => $value) {
            foreach ($sidak_buah as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mutu_buah[$value['wil']][$key2] = array_merge($mutu_buah[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // sidak tph 
        $ancakFA = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select(
                "sidak_tph.*",
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal'),
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'),
                DB::raw("
                CASE 
                WHEN status = '' THEN 1
                WHEN status = '0' THEN 1
                WHEN LOCATE('>H+', status) > 0 THEN '8'
                WHEN LOCATE('H+', status) > 0 THEN 
                    CASE 
                        WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1) > 8 THEN '8'
                        ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(status, 'H+', -1), ' ', 1)
                    END
                WHEN status REGEXP '^[0-9]+$' AND status > 8 THEN '8'
                WHEN LENGTH(status) > 1 AND status NOT LIKE '%H+%' AND status NOT LIKE '%>H+%' AND LOCATE(',', status) > 0 THEN SUBSTRING_INDEX(status, ',', 1)
                ELSE status
            END AS statuspanen")
            ) // Change the format to "%Y-%m-%d"
            ->whereBetween('sidak_tph.datetime', [$startDate, $endDate])
            ->orderBy('status', 'asc')
            ->get();

        $ancakFA = $ancakFA->groupBy(['est', 'afd', 'statuspanen', 'tanggal', 'blok']);
        $ancakFA = json_decode($ancakFA, true);


        $dateString = $startDate;
        $dateParts = date_parse($dateString);
        $year = $dateParts['year'];
        $month = $dateParts['month'];

        // dd($ancakFA);
        $year = $year; // Replace with the desired year
        $month = $month;   // Replace with the desired month (September in this example)


        if ($regional == 3) {

            $weeks = [];
            $firstDayOfMonth = strtotime("$year-$month-01");
            $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

            $weekNumber = 1;

            // Find the first Saturday of the month or the last Saturday of the previous month
            $firstSaturday = strtotime("last Saturday", $firstDayOfMonth);

            // Set the start date to the first Saturday
            $startDate = $firstSaturday;

            while ($startDate <= $lastDayOfMonth) {
                $endDate = strtotime("next Friday", $startDate);
                if ($endDate > $lastDayOfMonth) {
                    $endDate = $lastDayOfMonth;
                }

                $weeks[$weekNumber] = [
                    'start' => date('Y-m-d', $startDate),
                    'end' => date('Y-m-d', $endDate),
                ];

                // Update start date to the next Saturday
                $startDate = strtotime("next Saturday", $endDate);

                $weekNumber++;
            }
        } else {
            $weeks = [];
            $firstDayOfMonth = strtotime("$year-$month-01");
            $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));

            $weekNumber = 1;
            $startDate = $firstDayOfMonth;

            while ($startDate <= $lastDayOfMonth) {
                $endDate = strtotime("next Sunday", $startDate);
                if ($endDate > $lastDayOfMonth) {
                    $endDate = $lastDayOfMonth;
                }

                $weeks[$weekNumber] = [
                    'start' => date('Y-m-d', $startDate),
                    'end' => date('Y-m-d', $endDate),
                ];

                $nextMonday = strtotime("next Monday", $endDate);

                // Check if the next Monday is still within the current month.
                if (date('m', $nextMonday) == $month) {
                    $startDate = $nextMonday;
                } else {
                    // If the next Monday is in the next month, break the loop.
                    break;
                }

                $weekNumber++;
            }
        }



        $WeekStatus = [];

        // dd($weeks);
        // dd($startDate, $endDate, $weeks);

        foreach ($ancakFA as $key => $value) {
            $WeekStatus[$key] = [];

            foreach ($value as $estKey => $est) {
                $WeekStatus[$key][$estKey] = [];

                foreach ($weeks as $weekKey => $week) {
                    // dd($weekKey);
                    $WeekStatus[$key][$estKey]["week" . ($weekKey + 0)] = [];

                    foreach ($est as $statusKey => $status) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $WeekStatus[$key][$estKey]["week" . ($weekKey + 0)][$statusKey] = $newStatus;
                        }
                    }
                }
            }
        }
        // dd($WeekStatus);
        $defaultWeek = array();

        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultWeek[$est['est']][$afd['nama']] = 0;
                }
            }
        }

        // dd($defaultWeek);
        foreach ($defaultWeek as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($WeekStatus as $dataKey => $dataValue) {

                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {

                            if ($dataEstKey == $monthKey) {
                                $defaultWeek[$key][$monthKey] = $dataEstValue;
                            }
                        }
                    }
                }
            }
        }
        $dividen = [];

        foreach ($defaultWeek as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {

                    $dividenn = count($value1);
                }
                $dividen[$key][$key1]['dividen'] = $dividenn;
            } else {
                $dividen[$key][$key1]['dividen'] = 0;
            }
        }

        $newSidak = array();

        // dd($defaultWeek);

        foreach ($defaultWeek as $key => $value) {
            $dividen_afd = 0;
            $total_skoreest = 0;
            $tot_estAFd = 0;
            $new_dvdAfd = 0;
            $new_dvdAfdest = 0;
            $total_estkors = 0;
            $total_skoreafd = 0;

            $deviden = 0;
            $devest = count($value);
            // dd($devest);
            // dd($value);
            $v2check5 = 0;
            foreach ($value as $key1 => $value2)  if (is_array($value2)) {

                $tot_afdscore = 0;
                $totskor_brd1 = 0;
                $totskor_janjang1 = 0;
                $total_skoreest = 0;
                $v2check4 = 0;
                foreach ($value2 as $key2 => $value3) {


                    $total_brondolan = 0;
                    $total_janjang = 0;
                    $tod_brd = 0;
                    $tod_jjg = 0;
                    $totskor_brd = 0;
                    $totskor_janjang = 0;
                    $tot_brdxm = 0;
                    $tod_janjangxm = 0;
                    $v2check3 = 0;

                    foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                        $tph1 = 0;
                        $jalan1 = 0;
                        $bin1 = 0;
                        $karung1 = 0;
                        $buah1 = 0;
                        $restan1 = 0;
                        $v2check2 = 0;

                        foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                            $tph = 0;
                            $jalan = 0;
                            $bin = 0;
                            $karung = 0;
                            $buah = 0;
                            $restan = 0;
                            $v2check = count($value5);
                            foreach ($value5 as $key5 => $value6) {
                                $sum_bt_tph = 0;
                                $sum_bt_jalan = 0;
                                $sum_bt_bin = 0;
                                $sum_jum_karung = 0;
                                $sum_buah_tinggal = 0;
                                $sum_restan_unreported = 0;
                                $sum_all_restan_unreported = 0;

                                foreach ($value6 as $key6 => $value7) {
                                    // dd($value7);
                                    // dd($value7);
                                    $sum_bt_tph += $value7['bt_tph'];
                                    $sum_bt_jalan += $value7['bt_jalan'];
                                    $sum_bt_bin += $value7['bt_bin'];
                                    $sum_jum_karung += $value7['jum_karung'];


                                    $sum_buah_tinggal += $value7['buah_tinggal'];
                                    $sum_restan_unreported += $value7['restan_unreported'];
                                }
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['tph'] = $sum_bt_tph;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['jalan'] = $sum_bt_jalan;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['bin'] = $sum_bt_bin;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['karung'] = $sum_jum_karung;

                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['buah'] = $sum_buah_tinggal;
                                $newSidak[$key][$key1][$key2][$key3][$key4][$key5]['restan'] = $sum_restan_unreported;


                                $tph += $sum_bt_tph;
                                $jalan += $sum_bt_jalan;
                                $bin += $sum_bt_bin;
                                $karung += $sum_jum_karung;
                                $buah += $sum_buah_tinggal;
                                $restan += $sum_restan_unreported;
                            }

                            $newSidak[$key][$key1][$key2][$key3][$key4]['tph'] = $tph;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['jalan'] = $jalan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['bin'] = $bin;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['karung'] = $karung;

                            $newSidak[$key][$key1][$key2][$key3][$key4]['buah'] = $buah;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['restan'] = $restan;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['v2check'] = $v2check;

                            $tph1 += $tph;
                            $jalan1 += $jalan;
                            $bin1 += $bin;
                            $karung1 += $karung;
                            $buah1 += $buah;
                            $restan1 += $restan;
                            $v2check2 += $v2check;
                        } else {
                            $newSidak[$key][$key1][$key2][$key3][$key4]['tph'] = 0;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['jalan'] = 0;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['bin'] = 0;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['karung'] = 0;

                            $newSidak[$key][$key1][$key2][$key3][$key4]['buah'] = 0;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['restan'] = 0;
                            $newSidak[$key][$key1][$key2][$key3][$key4]['v2check'] = 0;
                        }
                        // dd($key3);
                        $status_panen = $key3;

                        [$panen_brd, $panen_jjg] = calculatePanen($status_panen);

                        // untuk brondolan gabungan dari bt-tph,bt-jalan,bt-bin,jum-karung 
                        $total_brondolan =  round(($tph1 + $jalan1 + $bin1 + $karung1) * $panen_brd / 100, 1);
                        $total_janjang =  round(($buah1 + $restan1) * $panen_jjg / 100, 1);
                        $tod_brd = $tph1 + $jalan1 + $bin1 + $karung1;
                        $tod_jjg = $buah1 + $restan1;
                        $newSidak[$key][$key1][$key2][$key3]['tphx'] = $tph1;
                        $newSidak[$key][$key1][$key2][$key3]['jalan'] = $jalan1;
                        $newSidak[$key][$key1][$key2][$key3]['bin'] = $bin1;
                        $newSidak[$key][$key1][$key2][$key3]['karung'] = $karung1;
                        $newSidak[$key][$key1][$key2][$key3]['tot_brd'] = $tod_brd;

                        $newSidak[$key][$key1][$key2][$key3]['buah'] = $buah1;
                        $newSidak[$key][$key1][$key2][$key3]['restan'] = $restan1;
                        $newSidak[$key][$key1][$key2][$key3]['skor_brd'] = $total_brondolan;
                        $newSidak[$key][$key1][$key2][$key3]['skor_janjang'] = $total_janjang;
                        $newSidak[$key][$key1][$key2][$key3]['tod_jjg'] = $tod_jjg;
                        $newSidak[$key][$key1][$key2][$key3]['v2check2'] = $v2check2;

                        $totskor_brd += $total_brondolan;
                        $totskor_janjang += $total_janjang;
                        $tot_brdxm += $tod_brd;
                        $tod_janjangxm += $tod_jjg;
                        $v2check3 += $v2check2;
                    } else {
                        $newSidak[$key][$key1][$key2][$key3]['tphx'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['jalan'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['bin'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['karung'] = 0;

                        $newSidak[$key][$key1][$key2][$key3]['buah'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['restan'] = 0;

                        $newSidak[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['skor_janjang'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['tot_brd'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['tod_jjg'] = 0;
                        $newSidak[$key][$key1][$key2][$key3]['v2check2'] = 0;
                    }


                    $total_estkors = $totskor_brd + $totskor_janjang;
                    if ($total_estkors != 0) {
                        $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak[$key][$key1][$key2]['check_data'] = 'ada';

                        $total_skoreafd = 100 - ($total_estkors);
                    } else if ($v2check3 != 0) {
                        $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak[$key][$key1][$key2]['check_data'] = 'ada';

                        $total_skoreafd = 100 - ($total_estkors);
                    } else {
                        $newSidak[$key][$key1][$key2]['all_score'] = 0;
                        $newSidak[$key][$key1][$key2]['check_data'] = 'null';
                        $total_skoreafd = 0;
                    }
                    // $newSidak[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                    $newSidak[$key][$key1][$key2]['total_brd'] = $tot_brdxm;
                    $newSidak[$key][$key1][$key2]['total_brdSkor'] = $totskor_brd;
                    $newSidak[$key][$key1][$key2]['total_janjang'] = $tod_janjangxm;
                    $newSidak[$key][$key1][$key2]['total_janjangSkor'] = $totskor_janjang;
                    $newSidak[$key][$key1][$key2]['total_skor'] = $total_skoreafd;
                    $newSidak[$key][$key1][$key2]['janjang_brd'] = $totskor_brd + $totskor_janjang;
                    $newSidak[$key][$key1][$key2]['v2check3'] = $v2check3;

                    $totskor_brd1 += $totskor_brd;
                    $totskor_janjang1 += $totskor_janjang;
                    $total_skoreest += $total_skoreafd;
                    $v2check4 += $v2check3;
                }


                // dd($newSidak);

                foreach ($dividen as $keyx => $value) {
                    if ($keyx == $key) {
                        foreach ($value as $keyx1 => $value2) {
                            if ($keyx1 == $key1) {
                                // dd($value2);
                                $dividen_x = $value2['dividen'];
                                if ($value2['dividen'] != 0) {
                                    $devidenEst_x = 1;
                                } else {
                                    $devidenEst_x = 0;
                                }
                                // dd($dividen);
                            }
                        }
                    }
                }

                // dd($deviden);

                $namaGM = '-';
                foreach ($queryAsisten as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }

                $deviden = count($value2);

                $new_dvd = $dividen_x ?? 0;
                $new_dvdest = $devidenEst_x ?? 0;


                if ($v2check4 != 0 && $total_skoreest == 0) {
                    $tot_afdscore = 100;
                } else if ($new_dvd != 0) {
                    $tot_afdscore = round($total_skoreest / $new_dvd, 1);
                } else if ($new_dvd == 0 && $v2check4 == 0) {
                    $tot_afdscore = 0;
                }

                // $newSidak[$key][$key1]['deviden'] = $deviden;

                $newSidak[$key][$key1]['total_brd'] = $totskor_brd1;
                $newSidak[$key][$key1]['total_janjang'] = $totskor_janjang1;
                $newSidak[$key][$key1]['new_deviden'] = $new_dvd;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
                if ($v2check4 == 0) {
                    $newSidak[$key][$key1]['total_score'] = '-';
                } else {
                    $newSidak[$key][$key1]['total_score'] = $tot_afdscore;
                }

                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['devidenest'] = $devest;
                $newSidak[$key][$key1]['v2check4'] = $v2check4;

                $tot_estAFd += $tot_afdscore;
                $new_dvdAfd += $new_dvd;
                $new_dvdAfdest += $new_dvdest;
                $v2check5 += $v2check4;
            } else {
                $namaGM = '-';
                foreach ($queryAsisten as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $newSidak[$key][$key1]['total_brd'] = 0;
                $newSidak[$key][$key1]['total_janjang']  = 0;
                $newSidak[$key][$key1]['new_deviden']  = 0;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
                $newSidak[$key][$key1]['total_score']  = 0;
                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['devidenest']  = 0;
                $newSidak[$key][$key1]['v2check4'] = 0;
            }

            $dividen_afd = count($value);
            if ($new_dvdAfdest != 0) {
                $total_skoreest = round($tot_estAFd / $devest, 1);
            } else {
                $total_skoreest = 0;
            }

            // dd($value);

            $namaGM = '-';
            foreach ($queryAsisten as $asisten) {
                if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                    $namaGM = $asisten['nama'];
                    break;
                }
            }
            if ($new_dvdAfd != 0) {
                $newSidak[$key]['deviden'] = 1;
            } else {
                $newSidak[$key]['deviden'] = 0;
            }

            $newSidak[$key]['total_skorest'] = $tot_estAFd;
            $newSidak[$key]['score_estate'] = $total_skoreest;
            $newSidak[$key]['asisten'] = $namaGM;
            $newSidak[$key]['estate'] = $key;
            $newSidak[$key]['afd'] = 'GM';
            $newSidak[$key]['afdeling'] = $devest;
            $newSidak[$key]['v2check5'] = $v2check5;
        }


        // dd($newSidak);

        $sidaktph = array();
        foreach ($queryEste as $key => $value) {
            foreach ($newSidak as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $sidaktph[$value['wil']][$key2] = array_merge($sidaktph[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }


        // qc inspeksi 


        $QueryMTancakWil = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
            ->whereBetween('mutu_ancak_new.datetime', [$startDate, $endDate])
            ->get();
        $QueryMTancakWil = $QueryMTancakWil->groupBy(['estate', 'afdeling']);
        $QueryMTancakWil = json_decode($QueryMTancakWil, true);

        // dd($QueryMTancakWil, $startDate, $endDate);


        foreach ($QueryMTancakWil as $estate => $afdelingArray) {
            $modifiedAfdelingArray = $afdelingArray;
            foreach ($afdelingArray as $afdeling => $data) {
                if ($estate === $afdeling) {
                    $modifiedAfdelingArray["OA"] = $data;
                    unset($modifiedAfdelingArray[$afdeling]);
                }
            }
            $QueryMTancakWil[$estate] = $modifiedAfdelingArray;
        }


        // dd($QueryMTancakWil);

        $defaultNew = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $dataPerBulan = array();
        foreach ($QueryMTancakWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataPerBulan[$key][$key2][$key3] = $value3;
                }
            }
        }
        $mergedData = array();
        foreach ($defaultNew as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mergedData[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mergedData[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedData[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mergedData[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtancakWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mergedData as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1[$value['wil']][$key2] = array_merge($mtancakWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }
        $QueryMTbuahWil = DB::connection('mysql2')->table('mutu_buah')
            ->select(
                "mutu_buah.*",
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun')
            )
            ->whereBetween('mutu_buah.datetime', [$startDate, $endDate])
            ->get();
        $QueryMTbuahWil = $QueryMTbuahWil->groupBy(['estate', 'afdeling']);
        $QueryMTbuahWil = json_decode($QueryMTbuahWil, true);

        // dd($QueryMTancakWil); // dd($QueryMTbuahWil);
        foreach ($QueryMTbuahWil as $estate => $afdelingArray) {
            $modifiedAfdelingArray = $afdelingArray;
            foreach ($afdelingArray as $afdeling => $data) {
                if ($estate === $afdeling) {
                    $modifiedAfdelingArray["OA"] = $data;
                    unset($modifiedAfdelingArray[$afdeling]);
                }
            }
            $QueryMTbuahWil[$estate] = $modifiedAfdelingArray;
        }

        $dataMTBuah = array();
        foreach ($QueryMTbuahWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTBuah[$key][$key2][$key3] = $value3;
                }
            }
        }

        $defaultMTbuah = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultMTbuah[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $mutuBuahMerge = array();
        foreach ($defaultMTbuah as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTBuah)) {
                    if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                        if (!empty($dataMTBuah[$estKey][$afdKey])) {
                            $mutuBuahMerge[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                        } else {
                            $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuBuahMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtBuahWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuBuahMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtBuahWIltab1[$value['wil']][$key2] = array_merge($mtBuahWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }






        $mtancaktab1Wil = array();
        foreach ($mtancakWIltab1 as $key => $value) if (!empty($value)) {
            $pokok_panenWil = 0;
            $jum_haWil = 0;
            $janjang_panenWil = 0;
            $p_panenWil = 0;
            $k_panenWil = 0;
            $brtgl_panenWil = 0;
            $bhts_panenWil = 0;
            $bhtm1_panenWil = 0;
            $bhtm2_panenWil = 0;
            $bhtm3_oanenWil = 0;
            $pelepah_swil = 0;
            $totalPKTwil = 0;
            $sumBHWil = 0;
            $akpWil = 0;
            $brdPerwil = 0;
            $sumPerBHWil = 0;
            $perPiWil = 0;
            $totalWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value2)) {
                $pokok_panenEst = 0;
                $jum_haEst =  0;
                $janjang_panenEst =  0;
                $akpEst =  0;
                $p_panenEst =  0;
                $k_panenEst =  0;
                $brtgl_panenEst = 0;
                $skor_bTinggalEst =  0;
                $brdPerjjgEst =  0;
                $bhtsEST = 0;
                $bhtm1EST = 0;
                $bhtm2EST = 0;
                $bhtm3EST = 0;
                $pelepah_sEST = 0;

                $skor_bhEst =  0;
                $skor_brdPerjjgEst =  0;

                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {

                    $akp = 0;
                    $skor_bTinggal = 0;
                    $brdPerjjg = 0;
                    $pokok_panen = 0;
                    $janjang_panen = 0;
                    $p_panen = 0;
                    $k_panen = 0;
                    $bhts_panen  = 0;
                    $bhtm1_panen  = 0;
                    $bhtm2_panen  = 0;
                    $bhtm3_oanen  = 0;
                    $ttlSkorMA = 0;
                    $listBlokPerAfd = array();
                    $jum_ha = 0;
                    $pelepah_s = 0;
                    $skor_brdPerjjg = 0;
                    $skor_bh = 0;
                    $skor_perPl = 0;
                    $totalPokok = 0;
                    $totalPanen = 0;
                    $totalP_panen = 0;
                    $totalK_panen = 0;
                    $totalPTgl_panen = 0;
                    $totalbhts_panen = 0;
                    $totalbhtm1_panen = 0;
                    $totalbhtm2_panen = 0;
                    $totalbhtm3_oanen = 0;
                    $totalpelepah_s = 0;
                    $total_brd = 0;
                    $check_input = 'kosong';
                    $nilai_input = 0;
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);

                        $totalPokok += $value3["sample"];
                        $totalPanen +=  $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen += $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];

                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];

                        $totalpelepah_s += $value3["ps"];
                        $check_input = $value3["jenis_input"];
                        $nilai_input = $value3["skor_akhir"];
                    }


                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }


                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                    } else {
                        $perPl = 0;
                    }





                    $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                    if (!empty($nonZeroValues)) {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                    } else {
                        $mtancaktab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                        // $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                    }

                    // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                    $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] = $akp;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = $totalP_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = $totalK_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                    $mtancaktab1Wil[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                    $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_per'] = $perPl;
                    // total skor akhir

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                    $mtancaktab1Wil[$key][$key1][$key2]['check_input'] = $check_input;
                    $mtancaktab1Wil[$key][$key1][$key2]['nilai_input'] = $nilai_input;

                    $pokok_panenEst += $totalPokok;

                    $jum_haEst += $jum_ha;
                    $janjang_panenEst += $totalPanen;

                    $p_panenEst += $totalP_panen;
                    $k_panenEst += $totalK_panen;
                    $brtgl_panenEst += $totalPTgl_panen;

                    // bagian buah tinggal
                    $bhtsEST   += $totalbhts_panen;
                    $bhtm1EST += $totalbhtm1_panen;
                    $bhtm2EST   += $totalbhtm2_panen;
                    $bhtm3EST   += $totalbhtm3_oanen;
                    // data untuk pelepah sengklek
                    $pelepah_sEST += $totalpelepah_s;
                } else {
                    $mtancaktab1Wil[$key][$key1][$key2]['pokok_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['ha_sample'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['akp_rl'] =  0;

                    $mtancaktab1Wil[$key][$key1][$key2]['p'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['k'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['tgl'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $mtancaktab1Wil[$key][$key1][$key2]['bhts_s'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm1'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm2'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['bhtm3'] = 0;

                    // $mtancaktab1Wil[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhi0;

                    $mtancaktab1Wil[$key][$key1][$key2]['skor_bh'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_brd'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_ps'] = 0;
                    $mtancaktab1Wil[$key][$key1][$key2]['skor_akhir'] = 0;
                }

                $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                // dd($sumBHEst);
                if ($pokok_panenEst != 0) {
                    $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                } else {
                    $akpEst = 0;
                }

                if ($janjang_panenEst != 0) {
                    $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                } else {
                    $brdPerjjgEst = 0;
                }



                // dd($sumBHEst);
                if ($sumBHEst != 0) {
                    $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                } else {
                    $sumPerBHEst = 0;
                }

                if ($pokok_panenEst != 0) {
                    $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                } else {
                    $perPlEst = 0;
                }


                $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                if (!empty($nonZeroValues)) {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                } else {
                    $mtancaktab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    // $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                //PENAMPILAN UNTUK PERESTATE
                $mtancaktab1Wil[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  $jum_haEst;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  $akpEst;

                $mtancaktab1Wil[$key][$key1]['p'] = $p_panenEst;
                $mtancaktab1Wil[$key][$key1]['k'] = $k_panenEst;
                $mtancaktab1Wil[$key][$key1]['tgl'] = $brtgl_panenEst;

                $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = $bhtsEST;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = $bhtm1EST;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = $bhtm2EST;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = $bhtm3EST;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                $mtancaktab1Wil[$key][$key1]['palepah_per'] = $perPlEst;
                // total skor akhir

                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = $totalSkorEst;

                //perhitungn untuk perwilayah

                $pokok_panenWil += $pokok_panenEst;
                $jum_haWil += $jum_haEst;
                $janjang_panenWil += $janjang_panenEst;
                $p_panenWil += $p_panenEst;
                $k_panenWil += $k_panenEst;
                $brtgl_panenWil += $brtgl_panenEst;
                // bagian buah tinggal
                $bhts_panenWil += $bhtsEST;
                $bhtm1_panenWil += $bhtm1EST;
                $bhtm2_panenWil += $bhtm2EST;
                $bhtm3_oanenWil += $bhtm3EST;
                $pelepah_swil += $pelepah_sEST;
            } else {
                $mtancaktab1Wil[$key][$key1]['pokok_sample'] = 0;
                $mtancaktab1Wil[$key][$key1]['ha_sample'] =  0;
                $mtancaktab1Wil[$key][$key1]['jumlah_panen'] = 0;
                $mtancaktab1Wil[$key][$key1]['akp_rl'] =  0;

                $mtancaktab1Wil[$key][$key1]['p'] = 0;
                $mtancaktab1Wil[$key][$key1]['k'] = 0;
                $mtancaktab1Wil[$key][$key1]['tgl'] = 0;

                // $mtancaktab1Wil[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil[$key][$key1]['brd/jjgest'] = 0;
                $mtancaktab1Wil[$key][$key1]['buah/jjg'] = 0;
                // data untuk buah tinggal
                $mtancaktab1Wil[$key][$key1]['bhts_s'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm1'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm2'] = 0;
                $mtancaktab1Wil[$key][$key1]['bhtm3'] = 0;
                $mtancaktab1Wil[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhir
                $mtancaktab1Wil[$key][$key1]['skor_bh'] =  0;
                $mtancaktab1Wil[$key][$key1]['skor_brd'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_ps'] = 0;
                $mtancaktab1Wil[$key][$key1]['skor_akhir'] = 0;
            }
            $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
            $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

            if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                $akpWil = 0;
            } else {

                $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
            }

            if ($totalPKTwil != 0) {
                $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
            } else {
                $brdPerwil = 0;
            }

            // dd($sumBHEst);
            if ($sumBHWil != 0) {
                $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
            } else {
                $sumPerBHWil = 0;
            }

            if ($pokok_panenWil != 0) {
                $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
            } else {
                $perPiWil = 0;
            }

            $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

            if (!empty($nonZeroValues)) {
                $mtancaktab1Wil[$key]['check_data'] = 'ada';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtancaktab1Wil[$key]['check_data'] = 'kosong';
                // $mtancaktab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtancaktab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }

            // $totalWil = $skor_bh + $skor_brd + $skor_ps;
            $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

            $mtancaktab1Wil[$key]['pokok_sample'] = $pokok_panenWil;
            $mtancaktab1Wil[$key]['ha_sample'] =  $jum_haWil;
            $mtancaktab1Wil[$key]['jumlah_panen'] = $janjang_panenWil;
            $mtancaktab1Wil[$key]['akp_rl'] =  $akpWil;

            $mtancaktab1Wil[$key]['p'] = $p_panenWil;
            $mtancaktab1Wil[$key]['k'] = $k_panenWil;
            $mtancaktab1Wil[$key]['tgl'] = $brtgl_panenWil;
            $mtancaktab1Wil[$key]['total_brd'] = $totalPKTwil;

            $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = $brdPerwil;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['bhts_s'] = $bhts_panenWil;
            $mtancaktab1Wil[$key]['bhtm1'] = $bhtm1_panenWil;
            $mtancaktab1Wil[$key]['bhtm2'] = $bhtm2_panenWil;
            $mtancaktab1Wil[$key]['bhtm3'] = $bhtm3_oanenWil;
            $mtancaktab1Wil[$key]['total_buah'] = $sumBHWil;
            $mtancaktab1Wil[$key]['total_buah_per'] = $sumPerBHWil;
            $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = $pelepah_swil;

            $mtancaktab1Wil[$key]['palepah_per'] = $perPiWil;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = skor_buah_Ma($sumPerBHWil);
            $mtancaktab1Wil[$key]['skor_brd'] = skor_brd_ma($brdPerwil);
            $mtancaktab1Wil[$key]['skor_ps'] = skor_palepah_ma($perPiWil);
            $mtancaktab1Wil[$key]['skor_akhir'] = $totalWil;
        } else {
            $mtancaktab1Wil[$key]['pokok_sample'] = 0;
            $mtancaktab1Wil[$key]['ha_sample'] =  0;
            $mtancaktab1Wil[$key]['jumlah_panen'] = 0;
            $mtancaktab1Wil[$key]['akp_rl'] =  0;

            $mtancaktab1Wil[$key]['p'] = 0;
            $mtancaktab1Wil[$key]['k'] = 0;
            $mtancaktab1Wil[$key]['tgl'] = 0;

            // $mtancaktab1Wil[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil[$key]['brd/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['buah/jjgwil'] = 0;
            $mtancaktab1Wil[$key]['bhts_s'] = 0;
            $mtancaktab1Wil[$key]['bhtm1'] = 0;
            $mtancaktab1Wil[$key]['bhtm2'] = 0;
            $mtancaktab1Wil[$key]['bhtm3'] = 0;
            // $mtancaktab1Wil[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $mtancaktab1Wil[$key]['skor_bh'] = 0;
            $mtancaktab1Wil[$key]['skor_brd'] = 0;
            $mtancaktab1Wil[$key]['skor_ps'] = 0;
            $mtancaktab1Wil[$key]['skor_akhir'] = 0;
        }

        $mtBuahtab1Wil = array();
        foreach ($mtBuahWIltab1 as $key => $value) if (is_array($value)) {
            $jum_haWil = 0;
            $sum_SamplejjgWil = 0;
            $sum_bmtWil = 0;
            $sum_bmkWil = 0;
            $sum_overWil = 0;
            $sum_abnorWil = 0;
            $sum_kosongjjgWil = 0;
            $sum_vcutWil = 0;
            $sum_krWil = 0;
            $no_Vcutwil = 0;

            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                $jum_haEst  = 0;
                $sum_SamplejjgEst = 0;
                $sum_bmtEst = 0;
                $sum_bmkEst = 0;
                $sum_overEst = 0;
                $sum_abnorEst = 0;
                $sum_kosongjjgEst = 0;
                $sum_vcutEst = 0;
                $sum_krEst = 0;
                $no_VcutEst = 0;

                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
                    $dataBLok = 0;
                    $sum_Samplejjg = 0;
                    $PerMth = 0;
                    $PerMsk = 0;
                    $PerOver = 0;
                    $sum_abnor = 0;
                    $sum_kosongjjg = 0;
                    $Perkosongjjg = 0;
                    $sum_vcut = 0;
                    $PerVcut = 0;
                    $PerAbr = 0;
                    $sum_kr = 0;
                    $total_kr = 0;
                    $per_kr = 0;
                    $totalSkor = 0;
                    $jum_ha = 0;
                    $no_Vcut = 0;
                    $jml_mth = 0;
                    $jml_mtg = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = [];
                    $dtBlok = 0;
                    // $combination_counts = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                        $dtBlok = count($listBlokPerAfd);

                        // $jum_ha = count($listBlokPerAfd);
                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];


                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }

                    // $dataBLok = count($combination_counts);
                    $dataBLok = $dtBlok;
                    $jml_mth = ($sum_bmt + $sum_bmk);
                    $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 3);
                    } else {
                        $total_kr = 0;
                    }


                    $per_kr = round($total_kr * 100, 3);
                    if ($jml_mth != 0) {
                        $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMth = 0;
                    }
                    if ($jml_mtg != 0) {
                        $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerMsk = 0;
                    }
                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $PerOver = 0;
                    }
                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                    } else {
                        $Perkosongjjg = 0;
                    }
                    if ($sum_vcut != 0) {
                        $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_abnor != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                    } else {
                        $PerAbr = 0;
                    }

                    $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                    if (!empty($nonZeroValues)) {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                    } else {
                        $mtBuahtab1Wil[$key][$key1][$key2]['check_data'] = 'kosong';
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                        // $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = $sum_over;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = $PerVcut;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                    //perhitungan estate
                    $jum_haEst += $dataBLok;
                    $sum_SamplejjgEst += $sum_Samplejjg;
                    $sum_bmtEst += $jml_mth;
                    $sum_bmkEst += $jml_mtg;
                    $sum_overEst += $sum_over;
                    $sum_abnorEst += $sum_abnor;
                    $sum_kosongjjgEst += $sum_kosongjjg;
                    $sum_vcutEst += $sum_vcut;
                    $sum_krEst += $sum_kr;
                } else {
                    $mtBuahtab1Wil[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perMasak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perOver'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_abnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perAbnormal'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['perVcut'] = 0;

                    $mtBuahtab1Wil[$key][$key1][$key2]['jum_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['total_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_mentah'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_masak'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_over'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_vcut'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_abnormal'] = 0;;
                    $mtBuahtab1Wil[$key][$key1][$key2]['skor_kr'] = 0;
                    $mtBuahtab1Wil[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                }
                $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                if ($sum_krEst != 0) {
                    $total_krEst = round($sum_krEst / $jum_haEst, 3);
                } else {
                    $total_krEst = 0;
                }
                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }

                if ($sum_bmtEst != 0) {
                    $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMthEst = 0;
                }

                if ($sum_bmkEst != 0) {
                    $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerMskEst = 0;
                }

                if ($sum_overEst != 0) {
                    $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerOverEst = 0;
                }
                if ($sum_kosongjjgEst != 0) {
                    $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                } else {
                    $PerkosongjjgEst = 0;
                }
                if ($sum_vcutEst != 0) {
                    $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerVcutest = 0;
                }
                if ($sum_abnorEst != 0) {
                    $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                } else {
                    $PerAbrest = 0;
                }
                // $per_kr = round($sum_kr * 100);
                $per_krEst = round($total_krEst * 100, 3);


                $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                if (!empty($nonZeroValues)) {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                } else {
                    $mtBuahtab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtBuahtab1Wil[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_over'] = $skor_over = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    // $mtBuahtab1Wil[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = $sum_bmtEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = $PerMthEst;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = $sum_bmkEst;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = $PerMskEst;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = $sum_overEst;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = $PerOverEst;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = $PerAbrest;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = $sum_vcutEst;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = $PerVcutest;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = $sum_krEst;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = $total_krEst;

                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = $per_krEst;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                //hitung perwilayah
                $jum_haWil += $jum_haEst;
                $sum_SamplejjgWil += $sum_SamplejjgEst;
                $sum_bmtWil += $sum_bmtEst;
                $sum_bmkWil += $sum_bmkEst;
                $sum_overWil += $sum_overEst;
                $sum_abnorWil += $sum_abnorEst;
                $sum_kosongjjgWil += $sum_kosongjjgEst;
                $sum_vcutWil += $sum_vcutEst;
                $sum_krWil += $sum_krEst;
            } else {
                $mtBuahtab1Wil[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perMasak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perOver'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_abnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perabnormal'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahtab1Wil[$key][$key1]['total_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['perVcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['jum_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['kr_blok'] = 0;
                $mtBuahtab1Wil[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahtab1Wil[$key][$key1]['skor_mentah'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_masak'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_over'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_vcut'] = 0;
                $mtBuahtab1Wil[$key][$key1]['skor_abnormal'] = 0;;
                $mtBuahtab1Wil[$key][$key1]['skor_kr'] = 0;
                $mtBuahtab1Wil[$key][$key1]['TOTAL_SKOR'] = 0;
            }

            // if ($sum_kr != 0) {
            //     $total_kr = round($sum_kr / $dataBLok, 3);
            // } else {
            //     $total_kr = 0;
            // }



            if ($sum_krWil != 0) {
                $total_krWil = round($sum_krWil / $jum_haWil, 3);
            } else {
                $total_krWil = 0;
            }

            if ($sum_bmtWil != 0) {
                $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMthWil = 0;
            }


            if ($sum_bmkWil != 0) {
                $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerMskWil = 0;
            }
            if ($sum_overWil != 0) {
                $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerOverWil = 0;
            }
            if ($sum_kosongjjgWil != 0) {
                $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
            } else {
                $PerkosongjjgWil = 0;
            }
            if ($sum_vcutWil != 0) {
                $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerVcutWil = 0;
            }
            if ($sum_abnorWil != 0) {
                $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
            } else {
                $PerAbrWil = 0;
            }
            $per_krWil = round($total_krWil * 100, 3);

            $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

            if (!empty($nonZeroValues)) {
                $mtBuahtab1Wil[$key]['check_data'] = 'ada';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskWil);
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverWil);
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgWil);
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutWil);
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krWil);
            } else {
                $mtBuahtab1Wil[$key]['check_data'] = 'kosong';
                // $mtBuahtab1Wil[$key]['skor_masak'] = $skor_masak = 0;
                // $mtBuahtab1Wil[$key]['skor_over'] = $skor_over = 0;
                // $mtBuahtab1Wil[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                // $mtBuahtab1Wil[$key]['skor_vcut'] = $skor_vcut =  0;
                // $mtBuahtab1Wil[$key]['skor_kr'] = $skor_kr = 0;
            }

            // $totalSkorWil = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


            $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = $jum_haWil;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = $sum_SamplejjgWil;
            $mtBuahtab1Wil[$key]['total_mentah'] = $sum_bmtWil;
            $mtBuahtab1Wil[$key]['total_perMentah'] = $PerMthWil;
            $mtBuahtab1Wil[$key]['total_masak'] = $sum_bmkWil;
            $mtBuahtab1Wil[$key]['total_perMasak'] = $PerMskWil;
            $mtBuahtab1Wil[$key]['total_over'] = $sum_overWil;
            $mtBuahtab1Wil[$key]['total_perOver'] = $PerOverWil;
            $mtBuahtab1Wil[$key]['total_abnormal'] = $sum_abnorWil;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = $PerAbrWil;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = $sum_kosongjjgWil;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = $PerkosongjjgWil;
            $mtBuahtab1Wil[$key]['total_vcut'] = $sum_vcutWil;
            $mtBuahtab1Wil[$key]['per_vcut'] = $PerVcutWil;
            $mtBuahtab1Wil[$key]['jum_kr'] = $sum_krWil;
            $mtBuahtab1Wil[$key]['kr_blok'] = $total_krWil;

            $mtBuahtab1Wil[$key]['persen_kr'] = $per_krWil;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMthWil);
            $mtBuahtab1Wil[$key]['skor_masak'] = skor_buah_masak_mb($PerMskWil);
            $mtBuahtab1Wil[$key]['skor_over'] = skor_buah_over_mb($PerOverWil);;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
            $mtBuahtab1Wil[$key]['skor_vcut'] = skor_vcut_mb($PerVcutWil);
            $mtBuahtab1Wil[$key]['skor_kr'] = skor_abr_mb($per_krWil);
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = $totalSkorWil;
        } else {
            $mtBuahtab1Wil[$key]['tph_baris_blok'] = 0;
            $mtBuahtab1Wil[$key]['sampleJJG_total'] = 0;
            $mtBuahtab1Wil[$key]['total_mentah'] = 0;
            $mtBuahtab1Wil[$key]['total_perMentah'] = 0;
            $mtBuahtab1Wil[$key]['total_masak'] = 0;
            $mtBuahtab1Wil[$key]['total_perMasak'] = 0;
            $mtBuahtab1Wil[$key]['total_over'] = 0;
            $mtBuahtab1Wil[$key]['total_perOver'] = 0;
            $mtBuahtab1Wil[$key]['total_abnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_perabnormal'] = 0;
            $mtBuahtab1Wil[$key]['total_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['total_perKosongjjg'] = 0;
            $mtBuahtab1Wil[$key]['total_vcut'] = 0;
            $mtBuahtab1Wil[$key]['per_vcut'] = 0;
            $mtBuahtab1Wil[$key]['jum_kr'] = 0;
            $mtBuahtab1Wil[$key]['kr_blok'] = 0;

            $mtBuahtab1Wil[$key]['persen_kr'] = 0;

            // skoring
            $mtBuahtab1Wil[$key]['skor_mentah'] = 0;
            $mtBuahtab1Wil[$key]['skor_masak'] = 0;
            $mtBuahtab1Wil[$key]['skor_over'] = 0;
            $mtBuahtab1Wil[$key]['skor_jjgKosong'] = 0;
            $mtBuahtab1Wil[$key]['skor_vcut'] = 0;

            $mtBuahtab1Wil[$key]['skor_kr'] = 0;
            $mtBuahtab1Wil[$key]['TOTAL_SKOR'] = 0;
        }
        // dd($mtBuahtab1Wil);

        $TranscakReg2 = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y-%m-%d") as date')
            )
            ->whereBetween('mutu_transport.datetime', [$startDate, $endDate])
            ->orderBy('datetime', 'DESC')
            ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
            ->get();
        $AncakCakReg2 = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y-%m-%d") as date')
            )
            ->whereBetween('mutu_ancak_new.datetime', [$startDate, $endDate])
            ->orderBy('datetime', 'DESC')
            ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
            ->get();

        $DataTransGroupReg2 = [];
        foreach ($TranscakReg2 as $item) {
            $estate = $item->estate;
            $afdeling = $item->afdeling;
            $datetime = $item->datetime;
            $blok = $item->blok;
            $date = $item->date;

            if (!isset($DataTransGroupReg2[$estate])) {
                $DataTransGroupReg2[$estate] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling])) {
                $DataTransGroupReg2[$estate][$afdeling] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling][$date])) {
                $DataTransGroupReg2[$estate][$afdeling][$date] = [];
            }
            if (!isset($DataTransGroupReg2[$estate][$afdeling][$date][$blok])) {
                $DataTransGroupReg2[$estate][$afdeling][$date][$blok] = [];
            }

            $DataTransGroupReg2[$estate][$afdeling][$date][$blok][] = $item;
        }

        $DataTransGroupReg2 = json_decode(json_encode($DataTransGroupReg2), true);

        $groupedDataAcnakreg2 = [];
        foreach ($AncakCakReg2 as $item) {
            $estate = $item->estate;
            $afdeling = $item->afdeling;
            $datetime = $item->datetime;
            $blok = $item->blok;
            $date = $item->date;

            if (!isset($groupedDataAcnakreg2[$estate])) {
                $groupedDataAcnakreg2[$estate] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling])) {
                $groupedDataAcnakreg2[$estate][$afdeling] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling][$date])) {
                $groupedDataAcnakreg2[$estate][$afdeling][$date] = [];
            }
            if (!isset($groupedDataAcnakreg2[$estate][$afdeling][$date][$blok])) {
                $groupedDataAcnakreg2[$estate][$afdeling][$date][$blok] = [];
            }

            $groupedDataAcnakreg2[$estate][$afdeling][$date][$blok][] = $item;
        }

        $groupedDataAcnakreg2 = json_decode(json_encode($groupedDataAcnakreg2), true);

        $dataMTTransRegs2 = array();
        foreach ($DataTransGroupReg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {

                                    foreach ($value3 as $key4 => $value4) {

                                        $dataMTTransRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }
        $dataAncaksRegs2 = array();
        foreach ($groupedDataAcnakreg2 as $key => $value) {
            foreach ($queryEste as $est => $estval)
                if ($estval['est'] === $key) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($queryAfd as $afd => $afdval)
                            if ($afdval['est'] === $key && $afdval['nama'] === $key2) {
                                foreach ($value2 as $key3 => $value3) {
                                    foreach ($value3 as $key4 => $value4) {
                                        $dataAncaksRegs2[$afdval['est']][$afdval['nama']][$key3][$key4] = $value4;
                                    }
                                }
                            }
                    }
                }
        }
        // dd($dataAncaksRegs2);
        $ancakRegss2 = array();

        foreach ($dataAncaksRegs2 as $key => $value) {
            foreach ($value as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3) {
                    $sum = 0; // Initialize sum variable
                    $count = 0; // Initialize count variable
                    foreach ($value3 as $key3 => $value4) {
                        $listBlok = array();
                        $firstEntry = $value4[0];
                        foreach ($value4 as $key4 => $value5) {
                            // dd($value5['sph']);
                            if (!in_array($value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'], $listBlok)) {
                                if ($value5['sph'] != 0) {
                                    $listBlok[] = $value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'];
                                }
                            }
                            $jml_blok = count($listBlok);

                            if ($firstEntry['luas_blok'] != 0) {
                                $first = $firstEntry['luas_blok'];
                            } else {
                                $first = '-';
                            }
                        }
                        if ($first != '-') {
                            $sum += $first;
                            $count++;
                        }
                        $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'] = $first;
                        if ($regional === '2') {
                            $status_panen = explode(",", $value5['status_panen']);
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $status_panen[0];
                        } else {
                            $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'] = $value5['status_panen'];
                        }
                    }
                }
            }
        }
        $transNewdata = array();
        foreach ($dataMTTransRegs2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {

                    foreach ($value2 as $key3 => $value3) {
                        $sum_bt = 0;
                        $sum_Restan = 0;
                        $tph_sample = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key4 => $value4) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            $sum_Restan += $value4['rst'];
                            $tph_sample = count($listBlokPerAfd);
                            $sum_bt += $value4['bt'];
                        }
                        $panenKey = 0;
                        $LuasKey = 0;
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['status_panen'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['status_panen'] = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                            $panenKey = $ancakRegss2[$key][$key1][$key2][$key3]['status_panen'];
                        }
                        if (isset($ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'])) {
                            $transNewdata[$key][$key1][$key2][$key3]['luas_blok'] = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                            $LuasKey = $ancakRegss2[$key][$key1][$key2][$key3]['luas_blok'];
                        }


                        if ($panenKey !== 0 && $panenKey <= 3) {
                            if (count($value4) == 1 && $value4[0]['blok'] == '0') {
                                $tph_sample = $value4[0]['tph_baris'];
                                $sum_bt = $value4[0]['bt'];
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($LuasKey) * 1.3, 3);
                            }
                        } else {
                            $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = $tph_sample;
                        }



                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                        $transNewdata[$key][$key1][$key2][$key3]['afdeling'] = $value4['afdeling'];
                        $transNewdata[$key][$key1][$key2][$key3]['estate'] = $value4['estate'];
                    }
                }
            }
        }
        foreach ($ancakRegss2 as $key => $value) {
            foreach ($value as $key1 => $value1) {

                foreach ($value1 as $key2 => $value2) {
                    $tph_tod = 0;
                    foreach ($value2 as $key3 => $value3) {
                        if (!isset($transNewdata[$key][$key1][$key2][$key3])) {
                            $transNewdata[$key][$key1][$key2][$key3] = $value3;

                            if ($value3['status_panen'] <= 3) {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = round(floatval($value3['luas_blok']) * 1.3, 3);
                            } else {
                                $transNewdata[$key][$key1][$key2][$key3]['tph_sample'] = 0;
                            }
                        }
                        // If 'tph_sample' key exists, add its value to $tph_tod
                        if (isset($value3['tph_sample'])) {
                            $tph_tod += $value3['tph_sample'];
                        }
                    }
                }
                // Store total_tph for each $key1 after iterating all $key2

            }
        }
        foreach ($transNewdata as $key => &$value) {
            foreach ($value as $key1 => &$value1) {
                $tph_sample_total = 0; // initialize the total
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            if (isset($value3['tph_sample'])) {
                                $tph_sample_total += $value3['tph_sample'];
                            }
                        }
                    }
                }
                $value1['total_tph'] = $tph_sample_total;
            }
        }
        unset($value); // unset the reference
        unset($value1); // unset the reference


        $defaultMtTrans = array();
        foreach ($queryEste as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defaultMtTrans[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $QueryTransWil = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
            )
            ->whereBetween('mutu_transport.datetime', [$startDate, $endDate])
            ->get();
        $QueryTransWil = $QueryTransWil->groupBy(['estate', 'afdeling']);
        $QueryTransWil = json_decode($QueryTransWil, true);
        $dataMTTrans = array();
        foreach ($QueryTransWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTTrans[$key][$key2][$key3] = $value3;
                }
            }
        }
        $mutuAncakMerge = array();
        foreach ($defaultMtTrans as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTTrans)) {
                    if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                        if (!empty($dataMTTrans[$estKey][$afdKey])) {
                            $mutuAncakMerge[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                        } else {
                            $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuAncakMerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        $mtTransWiltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuAncakMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtTransWiltab1[$value['wil']][$key2] = array_merge($mtTransWiltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }
        // dd($transNewdata);
        $mtTranstab1Wil = array();
        foreach ($mtTransWiltab1 as $key => $value) if (!empty($value)) {
            $dataBLokWil = 0;
            $sum_btWil = 0;
            $sum_rstWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLokEst = 0;
                $sum_btEst = 0;
                $sum_rstEst = 0;
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        // }
                        $dataBLok = count($listBlokPerAfd);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }
                    $tot_sample = 0;  // Define the variable outside of the foreach loop

                    foreach ($transNewdata as $keys => $trans) {
                        if ($keys == $key1) {
                            foreach ($trans as $keys2 => $trans2) {
                                if ($keys2 == $key2) {
                                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sampleNew'] = $trans2['total_tph'];
                                    $tot_sample = $trans2['total_tph'];
                                }
                            }
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $tot_sample, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        }
                    }

                    if ($regional == '2' || $regional == 2) {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $tot_sample, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    } else {
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        }
                    }


                    $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                    if (!empty($nonZeroValues)) {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = 'ada';
                    } else {
                        $mtTranstab1Wil[$key][$key1][$key2]['check_data'] = "kosong";
                    }
                    // dd($transNewdata);




                    $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                    //PERHITUNGAN PERESTATE
                    if ($regional == '2' || $regional == 2) {
                        $dataBLokEst += $tot_sample;
                    } else {
                        $dataBLokEst += $dataBLok;
                    }

                    $sum_btEst += $sum_bt;
                    $sum_rstEst += $sum_rst;

                    if ($dataBLokEst != 0) {
                        $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                    } else {
                        $brdPertphEst = 0;
                    }

                    if ($dataBLokEst != 0) {
                        $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                    } else {
                        $buahPerTPHEst = 0;
                    }

                    // dd($mtTranstab1Wil);
                    $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1][$key2]['tph_sample'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buah'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $mtTranstab1Wil[$key][$key1][$key2]['totalSkor'] = 0;
                }

                $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                if (!empty($nonZeroValues)) {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'ada';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil[$key][$key1]['check_data'] = 'kosong';
                    // $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                // $totalSkorEst = $skor_brd + $skor_buah ;


                $mtTranstab1Wil[$key][$key1]['tph_sample'] = $dataBLokEst;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = $sum_btEst;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = $sum_rstEst;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = $totalSkorEst;

                //perhitungan per wil
                $dataBLokWil += $dataBLokEst;
                $sum_btWil += $sum_btEst;
                $sum_rstWil += $sum_rstEst;

                if ($dataBLokWil != 0) {
                    $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                } else {
                    $brdPertphWil = 0;
                }
                if ($dataBLokWil != 0) {
                    $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                } else {
                    $buahPerTPHWil = 0;
                }

                $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
            } else {
                $mtTranstab1Wil[$key][$key1]['tph_sample'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_brd/TPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buah'] = 0;
                $mtTranstab1Wil[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_brdPertph'] = 0;
                $mtTranstab1Wil[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTranstab1Wil[$key][$key1]['totalSkor'] = 0;
            }

            $nonZeroValues = array_filter([$sum_btWil, $sum_rstWil]);


            if (!empty($nonZeroValues)) {
                $mtTranstab1Wil[$key]['check_data'] = 'ada';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerwil);
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPiWil);
            } else {
                $mtTranstab1Wil[$key]['check_data'] = 'kosong';
                // $mtTranstab1Wil[$key]['skor_brd'] = $skor_brd = 0;
                // $mtTranstab1Wil[$key]['skor_ps'] = $skor_ps = 0;
            }
            $mtTranstab1Wil[$key]['tph_sample'] = $dataBLokWil;
            $mtTranstab1Wil[$key]['total_brd'] = $sum_btWil;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = $brdPertphWil;
            $mtTranstab1Wil[$key]['total_buah'] = $sum_rstWil;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = $buahPerTPHWil;
            $mtTranstab1Wil[$key]['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
            $mtTranstab1Wil[$key]['totalSkor'] = $totalSkorWil;
        } else {
            $mtTranstab1Wil[$key]['tph_sample'] = 0;
            $mtTranstab1Wil[$key]['total_brd'] = 0;
            $mtTranstab1Wil[$key]['total_brd/TPH'] = 0;
            $mtTranstab1Wil[$key]['total_buah'] = 0;
            $mtTranstab1Wil[$key]['total_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['skor_brdPertph'] = 0;
            $mtTranstab1Wil[$key]['skor_buahPerTPH'] = 0;
            $mtTranstab1Wil[$key]['totalSkor'] = 0;
        }

        foreach ($mtancaktab1Wil as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    foreach ($mtBuahtab1Wil as $bh => $buah) {
                        foreach ($buah as $bh1 => $buah1) if (is_array($buah1)) {
                            foreach ($buah1 as $bh2 => $buah2) if (is_array($buah2)) {
                                foreach ($mtTranstab1Wil as $tr => $trans) {
                                    foreach ($trans as $tr1 => $trans1) if (is_array($trans1)) {
                                        foreach ($trans1 as $tr2 => $trans2) if (is_array($trans2))
                                            if (
                                                $bh == $key
                                                && $bh == $tr
                                                && $bh1 == $key1
                                                && $bh1 == $tr1
                                                && $bh2 == $key2
                                                && $bh2 == $tr2
                                            ) {
                                                // dd($trans2);
                                                // dd($key);
                                                if ($value2['check_input'] == 'manual' && $value2['nilai_input'] != 0) {
                                                    $RekapWIlTabel[$key][$key1][$key2]['data'] = 'ada';
                                                } else if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['data'] = 'kosong';
                                                }

                                                if ($value2['check_input'] == 'manual') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['nilai_input'];
                                                } else  if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = 0;
                                                } else {
                                                    $RekapWIlTabel[$key][$key1][$key2]['TotalSkor'] = $value2['skor_akhir'] + $buah2['TOTAL_SKOR'] + $trans2['totalSkor'];
                                                }


                                                if ($trans2['check_data'] == 'kosong' && $buah2['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = 0;
                                                    $RekapWIlTabel[$key][$key1]['data'] = 'kosong';
                                                } else {
                                                    $RekapWIlTabel[$key][$key1]['TotalSkorEST'] = $value1['skor_akhir'] + $buah1['TOTAL_SKOR'] + $trans1['totalSkor'];
                                                }


                                                if ($value1['check_data'] == 'kosong' && $buah1['check_data'] === 'kosong' && $trans1['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key][$key1]['dataEst'] = 'kosong';
                                                }

                                                // dd($value,$buah,$trans);
                                                if ($trans['check_data'] == 'kosong' && $buah['check_data'] === 'kosong' && $value['check_data'] === 'kosong') {
                                                    $RekapWIlTabel[$key]['TotalSkorWil'] = 0;
                                                } else {
                                                    $RekapWIlTabel[$key]['TotalSkorWil'] = $value['skor_akhir'] + $buah['TOTAL_SKOR'] + $trans['totalSkor'];
                                                }
                                            }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($RekapWIlTabel as $key1 => $estates)  if (is_array($estates)) {
            $sortedData = array();
            $sortedDataEst = array();

            foreach ($estates as $estateName => $data) {
                if (is_array($data)) {
                    $sortedDataEst[] = array(
                        'key1' => $key1,
                        'estateName' => $estateName,
                        'data' => $data
                    );
                    foreach ($data as $key2 => $scores) {
                        if (is_array($scores)) {
                            $sortedData[] = array(
                                'estateName' => $estateName,
                                'key2' => $key2,
                                'scores' => $scores
                            );
                        }
                    }
                }
            }

            //mengurutkan untuk nilai afd
            usort($sortedData, function ($a, $b) {
                return $b['scores']['TotalSkor'] - $a['scores']['TotalSkor'];
            });
            //mengurutkan untuk nilai estate
            usort($sortedDataEst, function ($a, $b) {
                return $b['data']['TotalSkorEST'] - $a['data']['TotalSkorEST'];
            });

            //menambahkan nilai rank ke dalam afd
            $rank = 1;
            foreach ($sortedData as $sortedEstate) {
                $RekapWIlTabel[$key1][$sortedEstate['estateName']][$sortedEstate['key2']]['rankAFD'] = $rank;
                $rank++;
            }

            //menambahkan nilai rank ke dalam estate
            $rank = 1;
            foreach ($sortedDataEst as $sortedest) {
                $RekapWIlTabel[$key1][$sortedest['estateName']]['rankEST'] = $rank;
                $rank++;
            }


            unset($sortedData, $sortedDataEst);
        }

        // dd($RekapWIlTabel);
        $qcinspeksi = $RekapWIlTabel;

        $sortedArray = [];

        foreach ($qcinspeksi as $key => $value) {
            $sortedArray[$key] = $value['TotalSkorWil'];
        }

        arsort($sortedArray);

        $rank = 1;
        foreach ($sortedArray as $key => $value) {
            $qcinspeksi[$key]['rankWil'] = $rank++;
        }
        // dd($startDate, $endDate);
        // dd($qcinspeksi, $sidaktph, $mutu_buah);


        $rekapafd = [];
        foreach ($qcinspeksi as $keyqc => $valqc) {
            foreach ($valqc as $keyqc1 => $valqc1) {
                if (is_array($valqc1)) {

                    foreach ($valqc1 as $keyqc2 => $valqc2) {
                        $datacheck = [];
                        $datacheck2 = [];
                        $countAda = 0;
                        $countAda2 = 0;
                        $totalest = 0;

                        if (is_array($valqc2)) {
                            if (
                                isset($sidaktph[$keyqc][$keyqc1][$keyqc2]) &&
                                isset($mutu_buah[$keyqc][$keyqc1][$keyqc2])
                            ) {
                                $valtph2 = $sidaktph[$keyqc][$keyqc1][$keyqc2];
                                $valbh2 = $mutu_buah[$keyqc][$keyqc1][$keyqc2];
                                $valtph1 = $sidaktph[$keyqc][$keyqc1];
                                $valbh1 = $mutu_buah[$keyqc][$keyqc1];
                                // Extracting values
                                $skor_tph = $valtph2['total_score'] ?? null;
                                $skor_qc = $valqc2['TotalSkor'] ?? null;
                                $skor_buah = $valbh2['All_skor'] ?? null;
                                $tph_check = $valtph2['v2check4'];
                                $qc_check = $valqc2['data'] ?? 'ada';
                                $buah_check = $valbh2['csfxr'];

                                // dd($valtph2);

                                if ($tph_check != 0 && $skor_tph != 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } elseif ($tph_check != 0 && $skor_tph == 0) {
                                    $tph = 'ada';
                                    $tphskor = $skor_tph;
                                } else {
                                    $tph = 'kosong';
                                    $tphskor = 0;
                                }

                                if ($buah_check != 0 && $skor_buah != 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } elseif ($buah_check != 0 && $skor_buah == 0) {
                                    $buah = 'ada';
                                    $buahskor = $skor_buah;
                                } else {
                                    $buah = 'kosong';
                                    $buahskor = 0;
                                }
                                if ($qc_check != 'kosong' && $skor_qc != 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } elseif ($qc_check != 'kosong' && $skor_qc == 0) {
                                    $qc = 'ada';
                                    $qcskor = $skor_qc;
                                } else {
                                    $qc = 'kosong';
                                    $qcskor = 0;
                                }

                                $datacheck[] = [$tph, $qc, $buah];
                                foreach ($datacheck[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda++;
                                    }
                                }
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === $keyqc2) {
                                    $rekapafd[$keyqc][$keyqc1][$keyqc2]['nama'] = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['tph_check'] = $tph;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['bgcolor'] = 'white';
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_tph'] = $tphskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['qc_check'] = $qc;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_qc'] = $qcskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['buah_check'] = $buah;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['skor_buah'] = $buahskor;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['datacheck'] = $datacheck;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['validasi'] = $countAda;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['est'] = $keyqc1;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['afd'] = $keyqc2;
                                $rekapafd[$keyqc][$keyqc1][$keyqc2]['total'] = $countAda !== 0 ? round(($tphskor + $qcskor + $buahskor) / $countAda, 2) : 0;

                                // dd($valbh1);

                                $skor_tph2 = $valtph1['score_estate'] ?? null;
                                $skor_qc2 = $valqc1['TotalSkorEST'] ?? null;
                                $skor_buah2 = $valbh1['All_skor'] ?? null;
                                $tph_check2 = $valtph1['v2check5'];
                                $qc_check2 = $valqc1['data'] ?? 'ada';
                                $buah_check2 = $valbh1['csrms'];


                                if ($tph_check2 != 0 && $skor_tph2 != 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } elseif ($tph_check2 != 0 && $skor_tph2 == 0) {
                                    $tph2 = 'ada';
                                    $tphskor2 = $skor_tph2;
                                } else {
                                    $tph2 = 'kosong';
                                    $tphskor2 = 0;
                                }

                                if ($buah_check2 != 0 && $skor_buah2 != 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } elseif ($buah_check2 != 0 && $skor_buah2 == 0) {
                                    $buah2 = 'ada';
                                    $buahskor2 = $skor_buah2;
                                } else {
                                    $buah2 = 'kosong';
                                    $buahskor2 = 0;
                                }
                                if ($qc_check2 != 'kosong' && $skor_qc2 != 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } elseif ($qc_check2 != 'kosong' && $skor_qc2 == 0) {
                                    $qc2 = 'ada';
                                    $qcskor2 = $skor_qc2;
                                } else {
                                    $qc2 = 'kosong';
                                    $qcskor2 = 0;
                                }

                                $datacheck2[] = [$tph2, $qc2, $buah2];
                                foreach ($datacheck2[0] as $value) {
                                    if ($value === 'ada') {
                                        $countAda2++;
                                    }
                                }
                                $namaqc = '-';
                                foreach ($queryAsisten as $keyx => $valuex) if ($valuex['est'] === $keyqc1 && $valuex['afd'] === 'EM') {
                                    $namaqc = $valuex['nama'] ?? '-';
                                    break;
                                }
                                $totalest = $countAda2 !== 0 ? round(($tphskor2 + $qcskor2 + $buahskor2) / $countAda2, 2) : 0;

                                // Assuming $estate is an individual entry and not part of $estates array for sorting
                                $estate = [
                                    'tph_check' => $tph2,
                                    'nama' => $namaqc,
                                    'bgcolor' => '#a0978d',
                                    'skor_tph' => $tphskor2,
                                    'qc_check' => $qc2,
                                    'skor_qc' => $qcskor2,
                                    'buah_check' => $buah2,
                                    'skor_buah' => $buahskor2,
                                    'validasi' => $countAda2,
                                    'est' => $keyqc1,
                                    'afd' => 'EM',
                                    'total' => $totalest
                                    // 'rank' => ???
                                ];
                            }
                        }
                    }
                    $rekapafd[$keyqc][$keyqc1]['est'] = $estate;
                    $getallest[] = $estate;
                }
            }
        }
        // dd($qcinspeksi, $sidaktph, $mutu_buah, $rekapafd);

        // dd($rekapafd);


        foreach ($rekapafd as $key => $value) {
            $estTotals = []; // Initialize an array to hold 'est' totals within this index

            foreach ($value as $estKey => $estValue) {
                if (isset($estValue['est'])) {
                    $est = $estValue['est'];
                    $afdElements = $estValue;
                    unset($afdElements['est']);

                    $totalsAfd = [];
                    foreach ($afdElements as $afdKey => $afdValue) {
                        $totalsAfd[$afdKey] = $afdValue['total'];
                    }

                    arsort($totalsAfd);

                    $rank = 1;
                    foreach ($totalsAfd as $afdKey => $totalAfd) {
                        $rekapafd[$key][$estKey][$afdKey]['rank'] = $rank;
                        $rank++;
                    }

                    // Accumulate 'est' totals within this index
                    $estTotals[$estKey] = $est['total'];
                }
            }

            // Sort 'est' totals within this index
            arsort($estTotals);

            // Assign ranks to each 'est' element within this index based on the sorted order of totals
            $rank = 1;
            foreach ($estTotals as $estKey => $totalEst) {
                $rekapafd[$key][$estKey]['est']['rank'] = $rank;
                $rank++;
            }
        }

        // untuk get mua ===============================================

        // mutu_sidak_buah mua
        if ($regional == 1) {
            $defaultmua = array();

            foreach ($muaest as $key2 => $value2) {
                foreach ($queryAfd as $key3 => $value3) {
                    if ($value2['est'] == $value3['est']) {
                        $defaultmua[$value2['est']][$value3['est']] = 0;
                    }
                }
            }
            foreach ($defaultmua as $estateKey => $afdelingArray) {
                foreach ($afdelingArray as $afdelingKey => $afdelingValue) {
                    if (isset($databulananBuah[$estateKey]) && isset($databulananBuah[$estateKey][$afdelingKey])) {
                        $defaultmua[$estateKey][$afdelingKey] = $databulananBuah[$estateKey][$afdelingKey];
                    }
                }
            }

            $sidak_buah_mua = array();
            // dd($defaultmua);
            $jjg_samplexy = 0;
            $tnpBRDxy = 0;
            $krgBRDxy = 0;
            $abrxy = 0;
            $overripexy = 0;
            $emptyxy = 0;
            $vcutxy = 0;
            $rdxy = 0;
            $dataBLokxy = 0;
            $sum_krxy = 0;
            $csrmsy = 0;
            foreach ($defaultmua as $key => $value) {
                $jjg_samplex = 0;
                $tnpBRDx = 0;
                $krgBRDx = 0;
                $abrx = 0;
                $overripex = 0;
                $emptyx = 0;
                $vcutx = 0;
                $rdx = 0;
                $dataBLokx = 0;
                $sum_krx = 0;
                $csrms = 0;
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        $jjg_sample = 0;
                        $tnpBRD = 0;
                        $krgBRD = 0;
                        $abr = 0;
                        $skor_total = 0;
                        $overripe = 0;
                        $empty = 0;
                        $vcut = 0;
                        $rd = 0;
                        $sum_kr = 0;
                        $allSkor = 0;
                        $combination_counts = array();
                        $newblok = 0;
                        $csfxr = count($value1);
                        foreach ($value1 as $key2 => $value2) {
                            $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                            if (!isset($combination_counts[$combination])) {
                                $combination_counts[$combination] = 0;
                            }
                            $newblok = count($value1);
                            $jjg_sample += $value2['jumlah_jjg'];
                            $tnpBRD += $value2['bmt'];
                            $krgBRD += $value2['bmk'];
                            $abr += $value2['abnormal'];
                            $overripe += $value2['overripe'];
                            $empty += $value2['empty_bunch'];
                            $vcut += $value2['vcut'];
                            $rd += $value2['rd'];
                            $sum_kr += $value2['alas_br'];
                        }
                        // $dataBLok = count($combination_counts);
                        $dataBLok = $newblok;
                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 2);
                        } else {
                            $total_kr = 0;
                        }
                        $per_kr = round($total_kr * 100, 2);
                        $skor_total = round((($tnpBRD + $krgBRD) / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgMSk = round(($jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr)) / ($jjg_sample - $abr) * 100, 2);
                        $skor_lewatMTng =  round(($overripe / ($jjg_sample - $abr)) * 100, 2);
                        $skor_jjgKosong =  round(($empty / ($jjg_sample - $abr)) * 100, 2);
                        $skor_vcut =   round(($vcut / $jjg_sample) * 100, 2);
                        $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                        $sidak_buah_mua[$key][$key1]['Jumlah_janjang'] = $jjg_sample;
                        $sidak_buah_mua[$key][$key1]['blok'] = $dataBLok;
                        $sidak_buah_mua[$key][$key1]['est'] = $key;
                        $sidak_buah_mua[$key][$key1]['afd'] = $key1;
                        $sidak_buah_mua[$key][$key1]['nama_staff'] = '-';
                        $sidak_buah_mua[$key][$key1]['tnp_brd'] = $tnpBRD;
                        $sidak_buah_mua[$key][$key1]['krg_brd'] = $krgBRD;
                        $sidak_buah_mua[$key][$key1]['persenTNP_brd'] = round(($tnpBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['persenKRG_brd'] = round(($krgBRD / ($jjg_sample - $abr)) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['total_jjg'] = $tnpBRD + $krgBRD;
                        $sidak_buah_mua[$key][$key1]['persen_totalJjg'] = $skor_total;
                        $sidak_buah_mua[$key][$key1]['skor_total'] = sidak_brdTotal($skor_total);
                        $sidak_buah_mua[$key][$key1]['jjg_matang'] = $jjg_sample - ($tnpBRD + $krgBRD + $overripe + $empty + $abr);
                        $sidak_buah_mua[$key][$key1]['persen_jjgMtang'] = $skor_jjgMSk;
                        $sidak_buah_mua[$key][$key1]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                        $sidak_buah_mua[$key][$key1]['lewat_matang'] = $overripe;
                        $sidak_buah_mua[$key][$key1]['persen_lwtMtng'] =  $skor_lewatMTng;
                        $sidak_buah_mua[$key][$key1]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                        $sidak_buah_mua[$key][$key1]['janjang_kosong'] = $empty;
                        $sidak_buah_mua[$key][$key1]['persen_kosong'] = $skor_jjgKosong;
                        $sidak_buah_mua[$key][$key1]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                        $sidak_buah_mua[$key][$key1]['vcut'] = $vcut;
                        $sidak_buah_mua[$key][$key1]['karung'] = $sum_kr;
                        $sidak_buah_mua[$key][$key1]['vcut_persen'] = $skor_vcut;
                        $sidak_buah_mua[$key][$key1]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                        $sidak_buah_mua[$key][$key1]['abnormal'] = $abr;
                        $sidak_buah_mua[$key][$key1]['abnormal_persen'] = round(($abr / $jjg_sample) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['rat_dmg'] = $rd;
                        $sidak_buah_mua[$key][$key1]['rd_persen'] = round(($rd / $jjg_sample) * 100, 2);
                        $sidak_buah_mua[$key][$key1]['TPH'] = $total_kr;
                        $sidak_buah_mua[$key][$key1]['persen_krg'] = $per_kr;
                        $sidak_buah_mua[$key][$key1]['skor_kr'] = sidak_PengBRD($per_kr);
                        $sidak_buah_mua[$key][$key1]['All_skor'] = $allSkor;
                        $sidak_buah_mua[$key][$key1]['csfxr'] = $csfxr;
                        $sidak_buah_mua[$key][$key1]['kategori'] = sidak_akhir($allSkor);

                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidak_buah_mua[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                        $jjg_samplex += $jjg_sample;
                        $tnpBRDx += $tnpBRD;
                        $krgBRDx += $krgBRD;
                        $abrx += $abr;
                        $overripex += $overripe;
                        $emptyx += $empty;
                        $vcutx += $vcut;

                        $rdx += $rd;

                        $dataBLokx += $newblok;
                        $sum_krx += $sum_kr;
                        $csrms += $csfxr;
                    } else {

                        $sidak_buah_mua[$key][$key1]['Jumlah_janjang'] = 0;
                        $sidak_buah_mua[$key][$key1]['blok'] = 0;
                        $sidak_buah_mua[$key][$key1]['est'] = $key;
                        $sidak_buah_mua[$key][$key1]['afd'] = $key1;
                        $sidak_buah_mua[$key][$key1]['nama_staff'] = '-';
                        $sidak_buah_mua[$key][$key1]['tnp_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['krg_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['persenTNP_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['persenKRG_brd'] = 0;
                        $sidak_buah_mua[$key][$key1]['total_jjg'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_totalJjg'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_total'] = 0;
                        $sidak_buah_mua[$key][$key1]['jjg_matang'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_jjgMtang'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_jjgMatang'] = 0;
                        $sidak_buah_mua[$key][$key1]['lewat_matang'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_lwtMtng'] =  0;
                        $sidak_buah_mua[$key][$key1]['skor_lewatMTng'] = 0;
                        $sidak_buah_mua[$key][$key1]['janjang_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_kosong'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut'] = 0;
                        $sidak_buah_mua[$key][$key1]['karung'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['vcut_skor'] = 0;
                        $sidak_buah_mua[$key][$key1]['abnormal'] = 0;
                        $sidak_buah_mua[$key][$key1]['abnormal_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['rat_dmg'] = 0;
                        $sidak_buah_mua[$key][$key1]['rd_persen'] = 0;
                        $sidak_buah_mua[$key][$key1]['TPH'] = 0;
                        $sidak_buah_mua[$key][$key1]['persen_krg'] = 0;
                        $sidak_buah_mua[$key][$key1]['skor_kr'] = 0;
                        $sidak_buah_mua[$key][$key1]['All_skor'] = 0;
                        $sidak_buah_mua[$key][$key1]['kategori'] = 0;
                        $sidak_buah_mua[$key][$key1]['csfxr'] = 0;
                        foreach ($queryAsisten as $ast => $asisten) {
                            if ($key === $asisten['est'] && $key1 === $asisten['afd']) {
                                $sidak_buah_mua[$key][$key1]['nama_asisten'] = $asisten['nama'];
                            }
                        }
                    }
                }
                if ($sum_krx != 0) {
                    $total_kr = round($sum_krx / $dataBLokx, 2);
                } else {
                    $total_kr = 0;
                }
                $per_kr = round($total_kr * 100, 2);
                $skor_total = round(($jjg_samplex - $abrx != 0 ? (($tnpBRDx + $krgBRDx) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgMSk = round(($jjg_samplex - $abrx != 0 ? (($jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx)) / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_lewatMTng = round(($jjg_samplex - $abrx != 0 ? ($overripex / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_jjgKosong = round(($jjg_samplex - $abrx != 0 ? ($emptyx / ($jjg_samplex - $abrx)) * 100 : 0), 2);

                $skor_vcut = round(($jjg_samplex != 0 ? ($vcutx / $jjg_samplex) * 100 : 0), 2);

                $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

                $em = 'EM';

                $nama_em = '';

                // dd($key1);
                foreach ($queryAsisten as $ast => $asisten) {
                    if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                        $nama_em = $asisten['nama'];
                    }
                }
                $jjg_mth = $tnpBRDx + $krgBRDx + $overripex + $emptyx;

                $skor_jjgMTh = ($jjg_samplex - $abrx != 0) ? round($jjg_mth / ($jjg_samplex - $abrx) * 100, 2) : 0;

                $sidak_buah_mua[$key]['jjg_mantah'] = $jjg_mth;
                $sidak_buah_mua[$key]['persen_jjgmentah'] = $skor_jjgMTh;

                if ($csrms == 0) {
                    $sidak_buah_mua[$key]['check_arr'] = 'kosong';
                    $sidak_buah_mua[$key]['All_skor'] = '-';
                } else {
                    $sidak_buah_mua[$key]['check_arr'] = 'ada';
                    $sidak_buah_mua[$key]['All_skor'] = $allSkor;
                }

                $sidak_buah_mua[$key]['Jumlah_janjang'] = $jjg_samplex;
                $sidak_buah_mua[$key]['csrms'] = $csrms;
                $sidak_buah_mua[$key]['blok'] = $dataBLokx;
                $sidak_buah_mua[$key]['EM'] = 'EM';
                $sidak_buah_mua[$key]['Nama_assist'] = $nama_em;
                $sidak_buah_mua[$key]['nama_staff'] = '-';
                $sidak_buah_mua[$key]['tnp_brd'] = $tnpBRDx;
                $sidak_buah_mua[$key]['krg_brd'] = $krgBRDx;
                $sidak_buah_mua[$key]['persenTNP_brd'] = round(($jjg_samplex - $abrx != 0 ? ($tnpBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidak_buah_mua[$key]['persenKRG_brd'] = round(($jjg_samplex - $abrx != 0 ? ($krgBRDx / ($jjg_samplex - $abrx)) * 100 : 0), 2);
                $sidak_buah_mua[$key]['abnormal_persen'] = round(($jjg_samplex != 0 ? ($abrx / $jjg_samplex) * 100 : 0), 2);
                $sidak_buah_mua[$key]['rd_persen'] = round(($jjg_samplex != 0 ? ($rdx / $jjg_samplex) * 100 : 0), 2);


                $sidak_buah_mua[$key]['total_jjg'] = $tnpBRDx + $krgBRDx;
                $sidak_buah_mua[$key]['persen_totalJjg'] = $skor_total;
                $sidak_buah_mua[$key]['skor_total'] = sidak_brdTotal($skor_total);
                $sidak_buah_mua[$key]['jjg_matang'] = $jjg_samplex - ($tnpBRDx + $krgBRDx + $overripex + $emptyx + $abrx);
                $sidak_buah_mua[$key]['persen_jjgMtang'] = $skor_jjgMSk;
                $sidak_buah_mua[$key]['skor_jjgMatang'] = sidak_matangSKOR($skor_jjgMSk);
                $sidak_buah_mua[$key]['lewat_matang'] = $overripex;
                $sidak_buah_mua[$key]['persen_lwtMtng'] =  $skor_lewatMTng;
                $sidak_buah_mua[$key]['skor_lewatMTng'] = sidak_lwtMatang($skor_lewatMTng);
                $sidak_buah_mua[$key]['janjang_kosong'] = $emptyx;
                $sidak_buah_mua[$key]['persen_kosong'] = $skor_jjgKosong;
                $sidak_buah_mua[$key]['skor_kosong'] = sidak_jjgKosong($skor_jjgKosong);
                $sidak_buah_mua[$key]['vcut'] = $vcutx;
                $sidak_buah_mua[$key]['vcut_persen'] = $skor_vcut;
                $sidak_buah_mua[$key]['vcut_skor'] = sidak_tangkaiP($skor_vcut);
                $sidak_buah_mua[$key]['abnormal'] = $abrx;

                $sidak_buah_mua[$key]['rat_dmg'] = $rdx;

                $sidak_buah_mua[$key]['karung'] = $sum_krx;
                $sidak_buah_mua[$key]['TPH'] = $total_kr;
                $sidak_buah_mua[$key]['persen_krg'] = $per_kr;
                $sidak_buah_mua[$key]['skor_kr'] = sidak_PengBRD($per_kr);
                // $sidak_buah_mua[$key]['All_skor'] = $allSkor;
                $sidak_buah_mua[$key]['kategori'] = sidak_akhir($allSkor);

                $jjg_samplexy += $jjg_samplex;
                $tnpBRDxy += $tnpBRDx;
                $krgBRDxy += $krgBRDx;
                $abrxy += $abrx;
                $overripexy += $overripex;
                $emptyxy += $emptyx;
                $vcutxy += $vcutx;
                $rdxy += $rdx;
                $dataBLokxy += $dataBLokx;
                $sum_krxy += $sum_krx;
                $csrmsy += $csrms;
            }
            if ($sum_krxy != 0) {
                $total_kr = round($sum_krxy / $dataBLokxy, 2);
            } else {
                $total_kr = 0;
            }
            $per_kr = round($total_kr * 100, 2);
            $skor_total = round(($jjg_samplexy - $abrxy != 0 ? (($tnpBRDxy + $krgBRDxy) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgMSk = round(($jjg_samplexy - $abrxy != 0 ? (($jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy)) / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_lewatMTng = round(($jjg_samplexy - $abrxy != 0 ? ($overripexy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_jjgKosong = round(($jjg_samplexy - $abrxy != 0 ? ($emptyxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2);

            $skor_vcut = round(($jjg_samplexy != 0 ? ($vcutxy / $jjg_samplexy) * 100 : 0), 2);

            $allSkor = sidak_brdTotal($skor_total) +  sidak_matangSKOR($skor_jjgMSk) +  sidak_lwtMatang($skor_lewatMTng) + sidak_jjgKosong($skor_jjgKosong) + sidak_tangkaiP($skor_vcut) + sidak_PengBRD($per_kr);

            $em = 'EM';

            $nama_em = '';

            // dd($key1);
            foreach ($queryAsisten as $ast => $asisten) {
                if ($key1 === $asisten['est'] && $em === $asisten['afd']) {
                    $nama_em = $asisten['nama'];
                }
            }
            $jjg_mthxy = $tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy;

            $skor_jjgMTh = ($jjg_samplexy - $abrxy != 0) ? round($jjg_mth / ($jjg_samplexy - $abrxy) * 100, 2) : 0;
            if ($csrmsy == 0) {
                $check_arr = 'kosong';
                $All_skor = '-';
            } else {
                $check_arr = 'ada';
                $All_skor = $allSkor;
            };
            $sidak_buah_mua['PT.MUA'] = [
                'jjg_mantah' => $jjg_mthxy,
                'persen_jjgmentah' => $skor_jjgMTh,
                'check_arr' => $check_arr,
                'All_skor' => $All_skor,
                'Jumlah_janjang' => $jjg_samplexy,
                'csrms' => $csrmsy,
                'blok' => $dataBLokxy,
                'EM' => 'EM',
                'Nama_assist' => $nama_em,
                'nama_staff' => '-',
                'tnp_brd' => $tnpBRDxy,
                'krg_brd' => $krgBRDxy,
                'persenTNP_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($tnpBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'persenKRG_brd' => round(($jjg_samplexy - $abrxy != 0 ? ($krgBRDxy / ($jjg_samplexy - $abrxy)) * 100 : 0), 2),
                'abnormal_persen' => round(($jjg_samplexy != 0 ? ($abrxy / $jjg_samplexy) * 100 : 0), 2),
                'rd_persen' => round(($jjg_samplexy != 0 ? ($rdxy / $jjg_samplexy) * 100 : 0), 2),
                'total_jjg' => $tnpBRDxy + $krgBRDxy,
                'persen_totalJjg' => $skor_total,
                'skor_total' => sidak_brdTotal($skor_total),
                'jjg_matang' => $jjg_samplexy - ($tnpBRDxy + $krgBRDxy + $overripexy + $emptyxy + $abrxy),
                'persen_jjgMtang' => $skor_jjgMSk,
                'skor_jjgMatang' => sidak_matangSKOR($skor_jjgMSk),
                'lewat_matang' => $overripexy,
                'persen_lwtMtng' =>  $skor_lewatMTng,
                'skor_lewatMTng' => sidak_lwtMatang($skor_lewatMTng),
                'janjang_kosong' => $emptyxy,
                'persen_kosong' => $skor_jjgKosong,
                'skor_kosong' => sidak_jjgKosong($skor_jjgKosong),
                'vcut' => $vcutxy,
                'vcut_persen' => $skor_vcut,
                'vcut_skor' => sidak_tangkaiP($skor_vcut),
                'abnormal' => $abrxy,
                'rat_dmg' => $rdxy,
                'karung' => $sum_krxy,
                'TPH' => $total_kr,
                'persen_krg' => $per_kr,
                'skor_kr' => sidak_PengBRD($per_kr),
                'kategori' => sidak_akhir($allSkor),
            ];

            // dd($sidak_buah_mua);
            // sidak_tph mua 

            $defaultweekmua = array();

            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultweekmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            foreach ($defaultweekmua as $key => $estValue) {
                foreach ($estValue as $monthKey => $monthValue) {
                    foreach ($WeekStatus as $dataKey => $dataValue) {

                        if ($dataKey == $key) {
                            foreach ($dataValue as $dataEstKey => $dataEstValue) {

                                if ($dataEstKey == $monthKey) {
                                    $defaultweekmua[$key][$monthKey] = $dataEstValue;
                                }
                            }
                        }
                    }
                }
            }
            $dividenmua = [];

            foreach ($defaultweekmua as $key => $value) {
                foreach ($value as $key1 => $value1) if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) if (is_array($value2)) {

                        $dividenn = count($value1);
                    }
                    $dividenmua[$key][$key1]['dividen'] = $dividenn;
                } else {
                    $dividenmua[$key][$key1]['dividen'] = 0;
                }
            }

            $tot_estAFdx = 0;
            $new_dvdAfdx = 0;
            $new_dvdAfdesx = 0;
            $v2check5x = 0;
            $newSidak_mua = array();
            foreach ($defaultweekmua as $key => $value) {
                $dividen_afd = 0;
                $total_skoreest = 0;
                $tot_estAFd = 0;
                $new_dvdAfd = 0;
                $new_dvdAfdest = 0;
                $total_estkors = 0;
                $total_skoreafd = 0;

                $deviden = 0;
                $devest = count($value);
                // dd($devest);
                // dd($value);
                $v2check5 = 0;
                foreach ($value as $key1 => $value2)  if (is_array($value2)) {

                    $tot_afdscore = 0;
                    $totskor_brd1 = 0;
                    $totskor_janjang1 = 0;
                    $total_skoreest = 0;
                    $v2check4 = 0;
                    foreach ($value2 as $key2 => $value3) {


                        $total_brondolan = 0;
                        $total_janjang = 0;
                        $tod_brd = 0;
                        $tod_jjg = 0;
                        $totskor_brd = 0;
                        $totskor_janjang = 0;
                        $tot_brdxm = 0;
                        $tod_janjangxm = 0;
                        $v2check3 = 0;

                        foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                            $tph1 = 0;
                            $jalan1 = 0;
                            $bin1 = 0;
                            $karung1 = 0;
                            $buah1 = 0;
                            $restan1 = 0;
                            $v2check2 = 0;

                            foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                                $tph = 0;
                                $jalan = 0;
                                $bin = 0;
                                $karung = 0;
                                $buah = 0;
                                $restan = 0;
                                $v2check = count($value5);
                                foreach ($value5 as $key5 => $value6) {
                                    $sum_bt_tph = 0;
                                    $sum_bt_jalan = 0;
                                    $sum_bt_bin = 0;
                                    $sum_jum_karung = 0;
                                    $sum_buah_tinggal = 0;
                                    $sum_restan_unreported = 0;
                                    $sum_all_restan_unreported = 0;

                                    foreach ($value6 as $key6 => $value7) {
                                        // dd($value7);
                                        // dd($value7);
                                        $sum_bt_tph += $value7['bt_tph'];
                                        $sum_bt_jalan += $value7['bt_jalan'];
                                        $sum_bt_bin += $value7['bt_bin'];
                                        $sum_jum_karung += $value7['jum_karung'];


                                        $sum_buah_tinggal += $value7['buah_tinggal'];
                                        $sum_restan_unreported += $value7['restan_unreported'];
                                    }
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['tph'] = $sum_bt_tph;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['jalan'] = $sum_bt_jalan;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['bin'] = $sum_bt_bin;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['karung'] = $sum_jum_karung;

                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['buah'] = $sum_buah_tinggal;
                                    $newSidak_mua[$key][$key1][$key2][$key3][$key4][$key5]['restan'] = $sum_restan_unreported;


                                    $tph += $sum_bt_tph;
                                    $jalan += $sum_bt_jalan;
                                    $bin += $sum_bt_bin;
                                    $karung += $sum_jum_karung;
                                    $buah += $sum_buah_tinggal;
                                    $restan += $sum_restan_unreported;
                                }

                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['tph'] = $tph;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['jalan'] = $jalan;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['bin'] = $bin;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['karung'] = $karung;

                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['buah'] = $buah;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['restan'] = $restan;
                                $newSidak_mua[$key][$key1][$key2][$key3][$key4]['v2check'] = $v2check;

                                $tph1 += $tph;
                                $jalan1 += $jalan;
                                $bin1 += $bin;
                                $karung1 += $karung;
                                $buah1 += $buah;
                                $restan1 += $restan;
                                $v2check2 += $v2check;
                            }
                            // dd($key3);
                            $status_panen = $key3;

                            [$panen_brd, $panen_jjg] = calculatePanen($status_panen);

                            // untuk brondolan gabungan dari bt-tph,bt-jalan,bt-bin,jum-karung 
                            $total_brondolan =  round(($tph1 + $jalan1 + $bin1 + $karung1) * $panen_brd / 100, 1);
                            $total_janjang =  round(($buah1 + $restan1) * $panen_jjg / 100, 1);
                            $tod_brd = $tph1 + $jalan1 + $bin1 + $karung1;
                            $tod_jjg = $buah1 + $restan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tphx'] = $tph1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['jalan'] = $jalan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['bin'] = $bin1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['karung'] = $karung1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tot_brd'] = $tod_brd;

                            $newSidak_mua[$key][$key1][$key2][$key3]['buah'] = $buah1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['restan'] = $restan1;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_brd'] = $total_brondolan;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_janjang'] = $total_janjang;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tod_jjg'] = $tod_jjg;
                            $newSidak_mua[$key][$key1][$key2][$key3]['v2check2'] = $v2check2;

                            $totskor_brd += $total_brondolan;
                            $totskor_janjang += $total_janjang;
                            $tot_brdxm += $tod_brd;
                            $tod_janjangxm += $tod_jjg;
                            $v2check3 += $v2check2;
                        } else {
                            $newSidak_mua[$key][$key1][$key2][$key3]['tphx'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['jalan'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['bin'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['karung'] = 0;

                            $newSidak_mua[$key][$key1][$key2][$key3]['buah'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['restan'] = 0;

                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['skor_janjang'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tot_brd'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['tod_jjg'] = 0;
                            $newSidak_mua[$key][$key1][$key2][$key3]['v2check2'] = 0;
                        }


                        $total_estkors = $totskor_brd + $totskor_janjang;
                        if ($total_estkors != 0) {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else if ($v2check3 != 0) {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'ada';

                            $total_skoreafd = 100 - ($total_estkors);
                        } else {
                            $newSidak_mua[$key][$key1][$key2]['all_score'] = 0;
                            $newSidak_mua[$key][$key1][$key2]['check_data'] = 'null';
                            $total_skoreafd = 0;
                        }
                        // $newSidak_mua[$key][$key1][$key2]['all_score'] = 100 - ($total_estkors);
                        $newSidak_mua[$key][$key1][$key2]['total_brd'] = $tot_brdxm;
                        $newSidak_mua[$key][$key1][$key2]['total_brdSkor'] = $totskor_brd;
                        $newSidak_mua[$key][$key1][$key2]['total_janjang'] = $tod_janjangxm;
                        $newSidak_mua[$key][$key1][$key2]['total_janjangSkor'] = $totskor_janjang;
                        $newSidak_mua[$key][$key1][$key2]['total_skor'] = $total_skoreafd;
                        $newSidak_mua[$key][$key1][$key2]['janjang_brd'] = $totskor_brd + $totskor_janjang;
                        $newSidak_mua[$key][$key1][$key2]['v2check3'] = $v2check3;

                        $totskor_brd1 += $totskor_brd;
                        $totskor_janjang1 += $totskor_janjang;
                        $total_skoreest += $total_skoreafd;
                        $v2check4 += $v2check3;
                    }


                    // dd($newSidak_mua);

                    foreach ($dividenmua as $keyx => $value) {
                        if ($keyx == $key) {
                            foreach ($value as $keyx1 => $value2) {
                                if ($keyx1 == $key1) {
                                    // dd($value2);
                                    $dividen_x = $value2['dividen'];
                                    if ($value2['dividen'] != 0) {
                                        $devidenEst_x = 1;
                                    } else {
                                        $devidenEst_x = 0;
                                    }
                                    // dd($dividen);
                                }
                            }
                        }
                    }

                    // dd($deviden);

                    $namaGM = '-';
                    foreach ($queryAsisten as $asisten) {

                        // dd($asisten);
                        if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                            $namaGM = $asisten['nama'];
                            break;
                        }
                    }

                    $deviden = count($value2);

                    $new_dvd = $dividen_x ?? 0;
                    $new_dvdest = $devidenEst_x ?? 0;


                    if ($v2check4 != 0 && $total_skoreest == 0) {
                        $tot_afdscore = 100;
                    } else if ($new_dvd != 0) {
                        $tot_afdscore = round($total_skoreest / $new_dvd, 1);
                    } else if ($new_dvd == 0 && $v2check4 == 0) {
                        $tot_afdscore = 0;
                    }


                    if ($tot_afdscore < 0) {
                        # code...
                        $newscore = 0;
                    } else {
                        $newscore = $tot_afdscore;
                    }
                    // $newSidak_mua[$key][$key1]['deviden'] = $deviden;

                    $newSidak_mua[$key][$key1]['total_brd'] = $totskor_brd1;
                    $newSidak_mua[$key][$key1]['total_janjang'] = $totskor_janjang1;
                    $newSidak_mua[$key][$key1]['new_deviden'] = $new_dvd;
                    $newSidak_mua[$key][$key1]['asisten'] = $namaGM;
                    if ($v2check4 == 0) {
                        $newSidak_mua[$key][$key1]['total_score'] = '-';
                    } else {
                        $newSidak_mua[$key][$key1]['total_score'] = $newscore;
                    }

                    $newSidak_mua[$key][$key1]['est'] = $key;
                    $newSidak_mua[$key][$key1]['afd'] = $key1;
                    $newSidak_mua[$key][$key1]['devidenest'] = $devest;
                    $newSidak_mua[$key][$key1]['v2check4'] = $v2check4;

                    $tot_estAFd += $newscore;
                    $new_dvdAfd += $new_dvd;
                    $new_dvdAfdest += $new_dvdest;
                    $v2check5 += $v2check4;
                }

                $dividen_afd = count($value);
                if ($v2check5 != 0) {
                    $total_skoreest = round($tot_estAFd / $devest, 1);
                    $checkdata = 'ada';
                } else if ($v2check5 != 0 && $devest != 0) {
                    $checkdata = 'ada';
                    $total_skoreest = 0;
                } else {
                    $total_skoreest = '-';
                    $checkdata = 'kosong';
                }

                // dd($value);

                $namaGM = '-';
                foreach ($queryAsisten as $asisten) {
                    if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                if ($new_dvdAfd != 0) {
                    $newSidak_mua[$key]['deviden'] = 1;
                } else {
                    $newSidak_mua[$key]['deviden'] = 0;
                }

                $newSidak_mua[$key]['total_skorest'] = $tot_estAFd;
                $newSidak_mua[$key]['checkdata'] = $checkdata;
                $newSidak_mua[$key]['score_estate'] = $total_skoreest;
                $newSidak_mua[$key]['asisten'] = $namaGM;
                $newSidak_mua[$key]['estate'] = $key;
                $newSidak_mua[$key]['afd'] = 'GM';
                $newSidak_mua[$key]['afdeling'] = $devest;
                $newSidak_mua[$key]['v2check5'] = $v2check5;

                if ($v2check5 != 0) {
                    $devidenlast = 1;
                } else {
                    $devidenlast = 0;
                }
                $devmuxa[] = $devidenlast;

                $tot_estAFdx  += $tot_estAFd;
                $new_dvdAfdx  += $new_dvdAfd;
                $new_dvdAfdesx += $new_dvdAfdest;
                $v2check5x += $v2check5;
            }
            $devmuxax = array_sum($devmuxa);

            if ($v2check5x != 0) {
                $total_skoreestxyz = round($tot_estAFdx / $devmuxax, 1);
                $checkdata = 'ada';
            } else if ($v2check5x != 0 && $devmuxax != 0) {
                $total_skoreestxyz = 0;
                $checkdata = 'ada';
            } else {
                $total_skoreestxyz = '-';
                $checkdata = 'kosong';
            }

            // dd($value);

            $namaGMnewSidak_mua = '-';
            foreach ($queryAsisten as $asisten) {
                if ($asisten['est'] == $key && $asisten['afd'] == 'EM') {
                    $namaGMnewSidak_mua = $asisten['nama'];
                    break;
                }
            }
            $newSidak_mua['PT.MUA'] = [
                'deviden' => $devmuxax,
                'checkdata' => $checkdata,
                'total_skorest' => $tot_estAFdx,
                'score_estate' => $total_skoreestxyz,
                'asisten' => $namaGM,
                'estate' => $key,
                'afd' => $namaGMnewSidak_mua,
                'afdeling' => $devmuxax,
                'v2check6' => $v2check5,
            ];

            // dd($newSidak_mua);
            // qc inspeksi mua 

            $defaultNewmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultNewmua[$est['est']][$afd['est']] = 0;
                    }
                }
            }
            $mergedDatamua = array();
            foreach ($defaultNewmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataPerBulan)) {
                        if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                            if (!empty($dataPerBulan[$estKey][$afdKey])) {
                                $mergedDatamua[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                            } else {
                                $mergedDatamua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mergedDatamua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedDatamua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtancakWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mergedDatamua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtancakWIltab1mua[$value['wil']][$key2] = array_merge($mtancakWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            // dd($mtancakWIltab1mua);
            $qcancakmua = array();
            foreach ($mtancakWIltab1mua as $key => $value) if (!empty($value)) {
                $pokok_panenWil = 0;
                $jum_haWil = 0;
                $janjang_panenWil = 0;
                $p_panenWil = 0;
                $k_panenWil = 0;
                $brtgl_panenWil = 0;
                $bhts_panenWil = 0;
                $bhtm1_panenWil = 0;
                $bhtm2_panenWil = 0;
                $bhtm3_oanenWil = 0;
                $pelepah_swil = 0;
                $totalPKTwil = 0;
                $sumBHWil = 0;
                $akpWil = 0;
                $brdPerwil = 0;
                $sumPerBHWil = 0;
                $perPiWil = 0;
                $totalWil = 0;
                foreach ($value as $key1 => $value1) if (!empty($value2)) {
                    $pokok_panenEst = 0;
                    $jum_haEst =  0;
                    $janjang_panenEst =  0;
                    $akpEst =  0;
                    $p_panenEst =  0;
                    $k_panenEst =  0;
                    $brtgl_panenEst = 0;
                    $brdPerjjgEst =  0;
                    $bhtsEST = 0;
                    $bhtm1EST = 0;
                    $bhtm2EST = 0;
                    $bhtm3EST = 0;
                    $pelepah_sEST = 0;
                    $check2 = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $akp = 0;
                        $skor_bTinggal = 0;
                        $brdPerjjg = 0;
                        $ttlSkorMA = 0;
                        $listBlokPerAfd = array();
                        $jum_ha = 0;
                        $totalPokok = 0;
                        $totalPanen = 0;
                        $totalP_panen = 0;
                        $totalK_panen = 0;
                        $totalPTgl_panen = 0;
                        $totalbhts_panen = 0;
                        $totalbhtm1_panen = 0;
                        $totalbhtm2_panen = 0;
                        $totalbhtm3_oanen = 0;
                        $totalpelepah_s = 0;
                        $check_input = 'kosong';
                        $nilai_input = 0;
                        // dd($value2['SKE']);
                        $check1 = count($value2);
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                                $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            }
                            $jum_ha = count($listBlokPerAfd);

                            $totalPokok += $value3["sample"];
                            $totalPanen +=  $value3["jjg"];
                            $totalP_panen += $value3["brtp"];
                            $totalK_panen += $value3["brtk"];
                            $totalPTgl_panen += $value3["brtgl"];

                            $totalbhts_panen += $value3["bhts"];
                            $totalbhtm1_panen += $value3["bhtm1"];
                            $totalbhtm2_panen += $value3["bhtm2"];
                            $totalbhtm3_oanen += $value3["bhtm3"];

                            $totalpelepah_s += $value3["ps"];
                            $check_input = $value3["jenis_input"];
                            $nilai_input = $value3["skor_akhir"];
                        }


                        if ($totalPokok != 0) {
                            $akp = round(($totalPanen / $totalPokok) * 100, 1);
                        } else {
                            $akp = 0;
                        }


                        $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                        if ($totalPanen != 0) {
                            $brdPerjjg = round($skor_bTinggal / $totalPanen, 3);
                        } else {
                            $brdPerjjg = 0;
                        }

                        $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                        if ($sumBH != 0) {
                            $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 3);
                        } else {
                            $sumPerBH = 0;
                        }

                        if ($totalpelepah_s != 0) {
                            $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                        } else {
                            $perPl = 0;
                        }





                        $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                        if ($check1 != 0) {
                            $qcancakmua[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcancakmua[$key][$key1][$key2]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                            // $qcancakmua[$key][$key1][$key2]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                        } else {
                            $qcancakmua[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcancakmua[$key][$key1][$key2]['skor_brd'] = $skor_brd = 0;
                            // $qcancakmua[$key][$key1][$key2]['skor_ps'] = $skor_ps = 0;
                        }

                        // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                        $ttlSkorMA = $skor_bh = skor_buah_Ma($sumPerBH) + $skor_brd = skor_brd_ma($brdPerjjg) + $skor_ps = skor_palepah_ma($perPl);

                        $qcancakmua[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                        $qcancakmua[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                        $qcancakmua[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                        $qcancakmua[$key][$key1][$key2]['akp_rl'] = $akp;

                        $qcancakmua[$key][$key1][$key2]['p'] = $totalP_panen;
                        $qcancakmua[$key][$key1][$key2]['k'] = $totalK_panen;
                        $qcancakmua[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                        $qcancakmua[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcancakmua[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                        // data untuk buah tinggal
                        $qcancakmua[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                        $qcancakmua[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                        $qcancakmua[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                        $qcancakmua[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcancakmua[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                        $qcancakmua[$key][$key1][$key2]['palepah_per'] = $perPl;
                        // total skor akhir

                        $qcancakmua[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                        $qcancakmua[$key][$key1][$key2]['check_input'] = $check_input;
                        $qcancakmua[$key][$key1][$key2]['nilai_input'] = $nilai_input;
                        $qcancakmua[$key][$key1][$key2]['check1'] = $check1;

                        $pokok_panenEst += $totalPokok;

                        $jum_haEst += $jum_ha;
                        $janjang_panenEst += $totalPanen;

                        $p_panenEst += $totalP_panen;
                        $k_panenEst += $totalK_panen;
                        $brtgl_panenEst += $totalPTgl_panen;

                        // bagian buah tinggal
                        $bhtsEST   += $totalbhts_panen;
                        $bhtm1EST += $totalbhtm1_panen;
                        $bhtm2EST   += $totalbhtm2_panen;
                        $bhtm3EST   += $totalbhtm3_oanen;
                        // data untuk pelepah sengklek
                        $pelepah_sEST += $totalpelepah_s;
                        $check2 += $check1;
                    } else {
                        $qcancakmua[$key][$key1][$key2]['check_data'] = 'kosong';
                        $qcancakmua[$key][$key1][$key2]['check1'] = 0;
                        $qcancakmua[$key][$key1][$key2]['pokok_sample'] = 0;
                        $qcancakmua[$key][$key1][$key2]['ha_sample'] = 0;
                        $qcancakmua[$key][$key1][$key2]['jumlah_panen'] = 0;
                        $qcancakmua[$key][$key1][$key2]['akp_rl'] =  0;

                        $qcancakmua[$key][$key1][$key2]['p'] = 0;
                        $qcancakmua[$key][$key1][$key2]['k'] = 0;
                        $qcancakmua[$key][$key1][$key2]['tgl'] = 0;

                        // $qcancakmua[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                        $qcancakmua[$key][$key1][$key2]['brd/jjg'] = 0;

                        // data untuk buah tinggal
                        $qcancakmua[$key][$key1][$key2]['bhts_s'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm1'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm2'] = 0;
                        $qcancakmua[$key][$key1][$key2]['bhtm3'] = 0;

                        // $qcancakmua[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                        // data untuk pelepah sengklek

                        $qcancakmua[$key][$key1][$key2]['palepah_pokok'] = 0;
                        // total skor akhi0;

                        $qcancakmua[$key][$key1][$key2]['skor_bh'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_brd'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_ps'] = 0;
                        $qcancakmua[$key][$key1][$key2]['skor_akhir'] = 0;
                    }

                    $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                    $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                    // dd($sumBHEst);
                    if ($pokok_panenEst != 0) {
                        $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
                    } else {
                        $akpEst = 0;
                    }

                    if ($janjang_panenEst != 0) {
                        $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 3);
                    } else {
                        $brdPerjjgEst = 0;
                    }



                    // dd($sumBHEst);
                    if ($sumBHEst != 0) {
                        $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 3);
                    } else {
                        $sumPerBHEst = 0;
                    }

                    if ($pokok_panenEst != 0) {
                        $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 3);
                    } else {
                        $perPlEst = 0;
                    }


                    $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                    if ($check2 != 0) {
                        $qcancakmua[$key][$key1]['check_data'] = 'ada';
                    } else {
                        $qcancakmua[$key][$key1]['check_data'] = 'kosong';
                    }

                    // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                    $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                    //PENAMPILAN UNTUK PERESTATE
                    $qcancakmua[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBHEst);
                    $qcancakmua[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjgEst);
                    $qcancakmua[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                    $qcancakmua[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                    $qcancakmua[$key][$key1]['ha_sample'] =  $jum_haEst;
                    $qcancakmua[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                    $qcancakmua[$key][$key1]['akp_rl'] =  $akpEst;

                    $qcancakmua[$key][$key1]['p'] = $p_panenEst;
                    $qcancakmua[$key][$key1]['k'] = $k_panenEst;
                    $qcancakmua[$key][$key1]['tgl'] = $brtgl_panenEst;

                    $qcancakmua[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcancakmua[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                    $qcancakmua[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                    // data untuk buah tinggal
                    $qcancakmua[$key][$key1]['bhts_s'] = $bhtsEST;
                    $qcancakmua[$key][$key1]['bhtm1'] = $bhtm1EST;
                    $qcancakmua[$key][$key1]['bhtm2'] = $bhtm2EST;
                    $qcancakmua[$key][$key1]['bhtm3'] = $bhtm3EST;
                    $qcancakmua[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                    $qcancakmua[$key][$key1]['palepah_per'] = $perPlEst;
                    // total skor akhir

                    $qcancakmua[$key][$key1]['skor_akhir'] = $totalSkorEst;

                    //perhitungn untuk perwilayah

                    $pokok_panenWil += $pokok_panenEst;
                    $jum_haWil += $jum_haEst;
                    $janjang_panenWil += $janjang_panenEst;
                    $p_panenWil += $p_panenEst;
                    $k_panenWil += $k_panenEst;
                    $brtgl_panenWil += $brtgl_panenEst;
                    // bagian buah tinggal
                    $bhts_panenWil += $bhtsEST;
                    $bhtm1_panenWil += $bhtm1EST;
                    $bhtm2_panenWil += $bhtm2EST;
                    $bhtm3_oanenWil += $bhtm3EST;
                    $pelepah_swil += $pelepah_sEST;
                } else {
                    $qcancakmua[$key][$key1]['pokok_sample'] = 0;
                    $qcancakmua[$key][$key1]['check2'] = 0;
                    $qcancakmua[$key][$key1]['check_data'] = 'kosong';
                    $qcancakmua[$key][$key1]['ha_sample'] =  0;
                    $qcancakmua[$key][$key1]['jumlah_panen'] = 0;
                    $qcancakmua[$key][$key1]['akp_rl'] =  0;

                    $qcancakmua[$key][$key1]['p'] = 0;
                    $qcancakmua[$key][$key1]['k'] = 0;
                    $qcancakmua[$key][$key1]['tgl'] = 0;

                    // $qcancakmua[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $qcancakmua[$key][$key1]['brd/jjgest'] = 0;
                    $qcancakmua[$key][$key1]['buah/jjg'] = 0;
                    // data untuk buah tinggal
                    $qcancakmua[$key][$key1]['bhts_s'] = 0;
                    $qcancakmua[$key][$key1]['bhtm1'] = 0;
                    $qcancakmua[$key][$key1]['bhtm2'] = 0;
                    $qcancakmua[$key][$key1]['bhtm3'] = 0;
                    $qcancakmua[$key][$key1]['palepah_pokok'] = 0;
                    // total skor akhir
                    $qcancakmua[$key][$key1]['skor_bh'] =  0;
                    $qcancakmua[$key][$key1]['skor_brd'] = 0;
                    $qcancakmua[$key][$key1]['skor_ps'] = 0;
                    $qcancakmua[$key][$key1]['skor_akhir'] = 0;
                }
                $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
                $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

                if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
                    $akpWil = 0;
                } else {

                    $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
                }

                if ($totalPKTwil != 0) {
                    $brdPerwil = round($totalPKTwil / $janjang_panenWil, 3);
                } else {
                    $brdPerwil = 0;
                }

                // dd($sumBHEst);
                if ($sumBHWil != 0) {
                    $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 3);
                } else {
                    $sumPerBHWil = 0;
                }

                if ($pokok_panenWil != 0) {
                    $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 3);
                } else {
                    $perPiWil = 0;
                }

                $nonZeroValues = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                };

                // $totalWil = $skor_bh + $skor_brd + $skor_ps;
                $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);
                $qcancakmua[$key]['PT.MUA'] = [
                    'pokok_sample' => $pokok_panenWil,
                    'check_data' => $data,
                    'ha_sample' =>  $jum_haWil,
                    'jumlah_panen' => $janjang_panenWil,
                    'akp_rl' =>  $akpWil,
                    'p' => $p_panenWil,
                    'k' => $k_panenWil,
                    'tgl' => $brtgl_panenWil,
                    'total_brd' => $totalPKTwil,
                    'total_brd' => $skor_bTinggal,
                    'brd/jjgwil' => $brdPerwil,
                    'buah/jjgwil' => $sumPerBHWil,
                    'bhts_s' => $bhts_panenWil,
                    'bhtm1' => $bhtm1_panenWil,
                    'bhtm2' => $bhtm2_panenWil,
                    'bhtm3' => $bhtm3_oanenWil,
                    'total_buah' => $sumBHWil,
                    'total_buah_per' => $sumPerBHWil,
                    'jjgperBuah' => number_format($sumPerBH, 3),
                    'palepah_pokok' => $pelepah_swil,
                    'palepah_per' => $perPiWil,
                    'skor_bh' => skor_buah_Ma($sumPerBHWil),
                    'skor_brd' => skor_brd_ma($brdPerwil),
                    'skor_ps' => skor_palepah_ma($perPiWil),
                    'skor_akhir' => $totalWil,
                ];
            }
            // dd($qcancakmua);
            foreach ($qcancakmua as $key => $value) {
                $qcancakmua = $value;
            }

            $defaultMTbuahmua = array();
            foreach ($muaest as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultMTbuahmua[$est['est']][$afd['est']]['null'] = 0;
                    }
                }
            }
            $mutuBuahMergemua = array();
            foreach ($defaultMTbuahmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTBuah)) {
                        if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                            if (!empty($dataMTBuah[$estKey][$afdKey])) {
                                $mutuBuahMergemua[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                            } else {
                                $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtBuahWIltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuBuahMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtBuahWIltab1mua[$value['wil']][$key2] = array_merge($mtBuahWIltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            $qcbuahmua = array();
            foreach ($mtBuahWIltab1mua as $key => $value) if (is_array($value)) {
                $jum_haWil = 0;
                $sum_SamplejjgWil = 0;
                $sum_bmtWil = 0;
                $sum_bmkWil = 0;
                $sum_overWil = 0;
                $sum_abnorWil = 0;
                $sum_kosongjjgWil = 0;
                $sum_vcutWil = 0;
                $sum_krWil = 0;
                $no_Vcutwil = 0;

                foreach ($value as $key1 => $value1) if (is_array($value1)) {
                    $jum_haEst  = 0;
                    $sum_SamplejjgEst = 0;
                    $sum_bmtEst = 0;
                    $sum_bmkEst = 0;
                    $sum_overEst = 0;
                    $sum_abnorEst = 0;
                    $sum_kosongjjgEst = 0;
                    $sum_vcutEst = 0;
                    $sum_krEst = 0;
                    $no_VcutEst = 0;

                    foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                        $sum_bmt = 0;
                        $sum_bmk = 0;
                        $sum_over = 0;
                        $dataBLok = 0;
                        $sum_Samplejjg = 0;
                        $PerMth = 0;
                        $PerMsk = 0;
                        $PerOver = 0;
                        $sum_abnor = 0;
                        $sum_kosongjjg = 0;
                        $Perkosongjjg = 0;
                        $sum_vcut = 0;
                        $PerVcut = 0;
                        $PerAbr = 0;
                        $sum_kr = 0;
                        $total_kr = 0;
                        $per_kr = 0;
                        $totalSkor = 0;
                        $jum_ha = 0;
                        $no_Vcut = 0;
                        $jml_mth = 0;
                        $jml_mtg = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = [];
                        $dtBlok = 0;
                        // $combination_counts = array();
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                            $dtBlok = count($listBlokPerAfd);

                            // $jum_ha = count($listBlokPerAfd);
                            $sum_bmt += $value3['bmt'];
                            $sum_bmk += $value3['bmk'];
                            $sum_over += $value3['overripe'];
                            $sum_kosongjjg += $value3['empty_bunch'];
                            $sum_vcut += $value3['vcut'];
                            $sum_kr += $value3['alas_br'];


                            $sum_Samplejjg += $value3['jumlah_jjg'];
                            $sum_abnor += $value3['abnormal'];
                        }

                        // $dataBLok = count($combination_counts);
                        $dataBLok = $dtBlok;
                        $jml_mth = ($sum_bmt + $sum_bmk);
                        $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);

                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 3);
                        } else {
                            $total_kr = 0;
                        }


                        $per_kr = round($total_kr * 100, 3);
                        if ($jml_mth != 0) {
                            $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMth = 0;
                        }
                        if ($jml_mtg != 0) {
                            $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerMsk = 0;
                        }
                        if ($sum_over != 0) {
                            $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $PerOver = 0;
                        }
                        if ($sum_kosongjjg != 0) {
                            $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                        } else {
                            $Perkosongjjg = 0;
                        }
                        if ($sum_vcut != 0) {
                            $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerVcut = 0;
                        }

                        if ($sum_abnor != 0) {
                            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
                        } else {
                            $PerAbr = 0;
                        }

                        $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut, $dataBLok]);

                        if (!empty($nonZeroValues)) {
                            $qcbuahmua[$key][$key1][$key2]['check_data'] = 'ada';
                            // $qcbuahmua[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                            // $qcbuahmua[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                            // $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                            // $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                            // $qcbuahmua[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                        } else {
                            $qcbuahmua[$key][$key1][$key2]['check_data'] = 'kosong';
                            // $qcbuahmua[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                            // $qcbuahmua[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                        }

                        // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                        $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                        $qcbuahmua[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                        $qcbuahmua[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                        $qcbuahmua[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                        $qcbuahmua[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                        $qcbuahmua[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                        $qcbuahmua[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                        $qcbuahmua[$key][$key1][$key2]['total_over'] = $sum_over;
                        $qcbuahmua[$key][$key1][$key2]['total_perOver'] = $PerOver;
                        $qcbuahmua[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                        $qcbuahmua[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                        $qcbuahmua[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                        $qcbuahmua[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                        $qcbuahmua[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                        $qcbuahmua[$key][$key1][$key2]['perVcut'] = $PerVcut;

                        $qcbuahmua[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                        $qcbuahmua[$key][$key1][$key2]['total_kr'] = $total_kr;
                        $qcbuahmua[$key][$key1][$key2]['persen_kr'] = $per_kr;

                        // skoring
                        $qcbuahmua[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                        $qcbuahmua[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                        $qcbuahmua[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                        $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                        $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                        $qcbuahmua[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                        $qcbuahmua[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                        //perhitungan estate
                        $jum_haEst += $dataBLok;
                        $sum_SamplejjgEst += $sum_Samplejjg;
                        $sum_bmtEst += $jml_mth;
                        $sum_bmkEst += $jml_mtg;
                        $sum_overEst += $sum_over;
                        $sum_abnorEst += $sum_abnor;
                        $sum_kosongjjgEst += $sum_kosongjjg;
                        $sum_vcutEst += $sum_vcut;
                        $sum_krEst += $sum_kr;
                    } else {
                        $qcbuahmua[$key][$key1][$key2]['tph_baris_blok'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['sampleJJG_total'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_mentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perMentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_masak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perMasak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_over'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perOver'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_abnormal'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['perAbnormal'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_jjgKosong'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_vcut'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['perVcut'] = 0;

                        $qcbuahmua[$key][$key1][$key2]['jum_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['total_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['persen_kr'] = 0;

                        // skoring
                        $qcbuahmua[$key][$key1][$key2]['skor_mentah'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_masak'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_over'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_vcut'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['skor_abnormal'] = 0;;
                        $qcbuahmua[$key][$key1][$key2]['skor_kr'] = 0;
                        $qcbuahmua[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                    }
                    $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;

                    if ($sum_krEst != 0) {
                        $total_krEst = round($sum_krEst / $jum_haEst, 3);
                    } else {
                        $total_krEst = 0;
                    }
                    // if ($sum_kr != 0) {
                    //     $total_kr = round($sum_kr / $dataBLok, 3);
                    // } else {
                    //     $total_kr = 0;
                    // }

                    if ($sum_bmtEst != 0) {
                        $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMthEst = 0;
                    }

                    if ($sum_bmkEst != 0) {
                        $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerMskEst = 0;
                    }

                    if ($sum_overEst != 0) {
                        $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerOverEst = 0;
                    }
                    if ($sum_kosongjjgEst != 0) {
                        $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 3);
                    } else {
                        $PerkosongjjgEst = 0;
                    }
                    if ($sum_vcutEst != 0) {
                        $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerVcutest = 0;
                    }
                    if ($sum_abnorEst != 0) {
                        $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 3);
                    } else {
                        $PerAbrest = 0;
                    }
                    // $per_kr = round($sum_kr * 100);
                    $per_krEst = round($total_krEst * 100, 3);


                    $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                    if (!empty($nonZeroValues)) {
                        $qcbuahmua[$key][$key1]['check_data'] = 'ada';
                        // $qcbuahmua[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                        // $qcbuahmua[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                        // $qcbuahmua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                        // $qcbuahmua[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                        // $qcbuahmua[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                    } else {
                        $qcbuahmua[$key][$key1]['check_data'] = 'kosong';
                        // $qcbuahmua[$key][$key1]['skor_masak'] = $skor_masak = 0;
                        // $qcbuahmua[$key][$key1]['skor_over'] = $skor_over = 0;
                        // $qcbuahmua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                        // $qcbuahmua[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                        // $qcbuahmua[$key][$key1]['skor_kr'] = $skor_kr = 0;
                    }

                    // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                    $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                    $qcbuahmua[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                    $qcbuahmua[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                    $qcbuahmua[$key][$key1]['total_mentah'] = $sum_bmtEst;
                    $qcbuahmua[$key][$key1]['total_perMentah'] = $PerMthEst;
                    $qcbuahmua[$key][$key1]['total_masak'] = $sum_bmkEst;
                    $qcbuahmua[$key][$key1]['total_perMasak'] = $PerMskEst;
                    $qcbuahmua[$key][$key1]['total_over'] = $sum_overEst;
                    $qcbuahmua[$key][$key1]['total_perOver'] = $PerOverEst;
                    $qcbuahmua[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                    $qcbuahmua[$key][$key1]['total_perabnormal'] = $PerAbrest;
                    $qcbuahmua[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                    $qcbuahmua[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                    $qcbuahmua[$key][$key1]['total_vcut'] = $sum_vcutEst;
                    $qcbuahmua[$key][$key1]['perVcut'] = $PerVcutest;
                    $qcbuahmua[$key][$key1]['jum_kr'] = $sum_krEst;
                    $qcbuahmua[$key][$key1]['kr_blok'] = $total_krEst;

                    $qcbuahmua[$key][$key1]['persen_kr'] = $per_krEst;

                    // skoring
                    $qcbuahmua[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                    $qcbuahmua[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                    $qcbuahmua[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                    $qcbuahmua[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                    $qcbuahmua[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                    $qcbuahmua[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                    $qcbuahmua[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

                    //hitung perwilayah
                    $jum_haWil += $jum_haEst;
                    $sum_SamplejjgWil += $sum_SamplejjgEst;
                    $sum_bmtWil += $sum_bmtEst;
                    $sum_bmkWil += $sum_bmkEst;
                    $sum_overWil += $sum_overEst;
                    $sum_abnorWil += $sum_abnorEst;
                    $sum_kosongjjgWil += $sum_kosongjjgEst;
                    $sum_vcutWil += $sum_vcutEst;
                    $sum_krWil += $sum_krEst;
                } else {
                    $qcbuahmua[$key][$key1]['tph_baris_blok'] = 0;
                    $qcbuahmua[$key][$key1]['sampleJJG_total'] = 0;
                    $qcbuahmua[$key][$key1]['total_mentah'] = 0;
                    $qcbuahmua[$key][$key1]['total_perMentah'] = 0;
                    $qcbuahmua[$key][$key1]['total_masak'] = 0;
                    $qcbuahmua[$key][$key1]['total_perMasak'] = 0;
                    $qcbuahmua[$key][$key1]['total_over'] = 0;
                    $qcbuahmua[$key][$key1]['total_perOver'] = 0;
                    $qcbuahmua[$key][$key1]['total_abnormal'] = 0;
                    $qcbuahmua[$key][$key1]['total_perabnormal'] = 0;
                    $qcbuahmua[$key][$key1]['total_jjgKosong'] = 0;
                    $qcbuahmua[$key][$key1]['total_perKosongjjg'] = 0;
                    $qcbuahmua[$key][$key1]['total_vcut'] = 0;
                    $qcbuahmua[$key][$key1]['perVcut'] = 0;
                    $qcbuahmua[$key][$key1]['jum_kr'] = 0;
                    $qcbuahmua[$key][$key1]['kr_blok'] = 0;
                    $qcbuahmua[$key][$key1]['persen_kr'] = 0;

                    // skoring
                    $qcbuahmua[$key][$key1]['skor_mentah'] = 0;
                    $qcbuahmua[$key][$key1]['skor_masak'] = 0;
                    $qcbuahmua[$key][$key1]['skor_over'] = 0;
                    $qcbuahmua[$key][$key1]['skor_jjgKosong'] = 0;
                    $qcbuahmua[$key][$key1]['skor_vcut'] = 0;
                    $qcbuahmua[$key][$key1]['skor_abnormal'] = 0;;
                    $qcbuahmua[$key][$key1]['skor_kr'] = 0;
                    $qcbuahmua[$key][$key1]['TOTAL_SKOR'] = 0;
                }

                // if ($sum_kr != 0) {
                //     $total_kr = round($sum_kr / $dataBLok, 3);
                // } else {
                //     $total_kr = 0;
                // }



                if ($sum_krWil != 0) {
                    $total_krWil = round($sum_krWil / $jum_haWil, 3);
                } else {
                    $total_krWil = 0;
                }

                if ($sum_bmtWil != 0) {
                    $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMthWil = 0;
                }


                if ($sum_bmkWil != 0) {
                    $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerMskWil = 0;
                }
                if ($sum_overWil != 0) {
                    $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerOverWil = 0;
                }
                if ($sum_kosongjjgWil != 0) {
                    $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 3);
                } else {
                    $PerkosongjjgWil = 0;
                }
                if ($sum_vcutWil != 0) {
                    $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerVcutWil = 0;
                }
                if ($sum_abnorWil != 0) {
                    $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 3);
                } else {
                    $PerAbrWil = 0;
                }
                $per_krWil = round($total_krWil * 100, 3);

                $nonZeroValues = array_filter([$sum_SamplejjgWil, $sum_bmtWil, $sum_bmkWil, $sum_overWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                }


                $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
                $qcbuahmua[$key]['PT.MUA'] = [
                    'check_data' => $data,
                    'tph_baris_blok' => $jum_haWil,
                    'sampleJJG_total' => $sum_SamplejjgWil,
                    'total_mentah' => $sum_bmtWil,
                    'total_perMentah' => $PerMthWil,
                    'total_masak' => $sum_bmkWil,
                    'total_perMasak' => $PerMskWil,
                    'total_over' => $sum_overWil,
                    'total_perOver' => $PerOverWil,
                    'total_abnormal' => $sum_abnorWil,
                    'total_perabnormal' => $PerAbrWil,
                    'total_jjgKosong' => $sum_kosongjjgWil,
                    'total_perKosongjjg' => $PerkosongjjgWil,
                    'total_vcut' => $sum_vcutWil,
                    'per_vcut' => $PerVcutWil,
                    'jum_kr' => $sum_krWil,
                    'kr_blok' => $total_krWil,
                    'persen_kr' => $per_krWil,
                    'skor_mentah' => skor_buah_mentah_mb($PerMthWil),
                    'skor_masak' => skor_buah_masak_mb($PerMskWil),
                    'skor_over' => skor_buah_over_mb($PerOverWil),
                    'skor_jjgKosong' => skor_jangkos_mb($PerkosongjjgWil),
                    'skor_vcut' => skor_vcut_mb($PerVcutWil),
                    'skor_kr' => skor_abr_mb($per_krWil),
                    'TOTAL_SKOR' => $totalSkorWil,
                ];
            }
            foreach ($qcbuahmua as $key => $value) {
                # code...
                $qcbuahmua = $value;
            }

            // dd($qcbuahmua);

            $defaultMtTransmua = array();
            foreach ($muaest as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defaultMtTransmua[$est['est']][$afd['est']]['null'] = 0;
                    }
                }
            }
            $mutuAncakMergemua = array();
            foreach ($defaultMtTransmua as $estKey => $afdArray) {
                foreach ($afdArray as $afdKey => $afdValue) {
                    if (array_key_exists($estKey, $dataMTTrans)) {
                        if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                            if (!empty($dataMTTrans[$estKey][$afdKey])) {
                                $mutuAncakMergemua[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                            } else {
                                $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                            }
                        } else {
                            $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMergemua[$estKey][$afdKey] = $afdValue;
                    }
                }
            }
            $mtTransWiltab1mua = array();
            foreach ($muaest as $key => $value) {
                foreach ($mutuAncakMergemua as $key2 => $value2) {
                    if ($value['est'] == $key2) {
                        $mtTransWiltab1mua[$value['wil']][$key2] = array_merge($mtTransWiltab1mua[$value['wil']][$key2] ?? [], $value2);
                    }
                }
            }

            // dd($mtTransWiltab1mua);
            $qctransmua = array();
            foreach ($mtTransWiltab1mua as $key => $value) if (!empty($value)) {
                $dataBLokWil = 0;
                $sum_btWil = 0;
                $sum_rstWil = 0;
                foreach ($value as $key1 => $value1) if (!empty($value1)) {
                    $dataBLokEst = 0;
                    $sum_btEst = 0;
                    $sum_rstEst = 0;
                    foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();
                        foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            // }
                            $dataBLok = count($listBlokPerAfd);
                            $sum_bt += $value3['bt'];
                            $sum_rst += $value3['rst'];
                        }
                        $tot_sample = 0;  // Define the variable outside of the foreach loop


                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 3);
                        } else {
                            $brdPertph = 0;
                        };

                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 3);
                        } else {
                            $buahPerTPH = 0;
                        };


                        $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                        if (!empty($nonZeroValues)) {
                            $qctransmua[$key][$key1][$key2]['check_data'] = 'ada';
                        } else {
                            $qctransmua[$key][$key1][$key2]['check_data'] = "kosong";
                        }
                        // dd($transNewdata);




                        $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        $qctransmua[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        $qctransmua[$key][$key1][$key2]['total_brd'] = $sum_bt;
                        $qctransmua[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                        $qctransmua[$key][$key1][$key2]['total_buah'] = $sum_rst;
                        $qctransmua[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                        $qctransmua[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                        $dataBLokEst += $dataBLok;
                        $sum_btEst += $sum_bt;
                        $sum_rstEst += $sum_rst;

                        if ($dataBLokEst != 0) {
                            $brdPertphEst = round($sum_btEst / $dataBLokEst, 3);
                        } else {
                            $brdPertphEst = 0;
                        }

                        if ($dataBLokEst != 0) {
                            $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 3);
                        } else {
                            $buahPerTPHEst = 0;
                        }

                        // dd($qctransmua);
                        $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                    } else {
                        $qctransmua[$key][$key1][$key2]['tph_sample'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_brd'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_brd/TPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_buah'] = 0;
                        $qctransmua[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['skor_brdPertph'] = 0;
                        $qctransmua[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                        $qctransmua[$key][$key1][$key2]['totalSkor'] = 0;
                    }

                    $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                    if (!empty($nonZeroValues)) {
                        $qctransmua[$key][$key1]['check_data'] = 'ada';
                        // $qctransmua[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                    } else {
                        $qctransmua[$key][$key1]['check_data'] = 'kosong';
                        // $qctransmua[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                    }

                    // $totalSkorEst = $skor_brd + $skor_buah ;


                    $qctransmua[$key][$key1]['tph_sample'] = $dataBLokEst;
                    $qctransmua[$key][$key1]['total_brd'] = $sum_btEst;
                    $qctransmua[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                    $qctransmua[$key][$key1]['total_buah'] = $sum_rstEst;
                    $qctransmua[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                    $qctransmua[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                    $qctransmua[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                    $qctransmua[$key][$key1]['totalSkor'] = $totalSkorEst;

                    //perhitungan per wil
                    $dataBLokWil += $dataBLokEst;
                    $sum_btWil += $sum_btEst;
                    $sum_rstWil += $sum_rstEst;

                    if ($dataBLokWil != 0) {
                        $brdPertphWil = round($sum_btWil / $dataBLokWil, 3);
                    } else {
                        $brdPertphWil = 0;
                    }
                    if ($dataBLokWil != 0) {
                        $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 3);
                    } else {
                        $buahPerTPHWil = 0;
                    }

                    $totalSkorWil =   skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
                } else {
                    $qctransmua[$key][$key1]['tph_sample'] = 0;
                    $qctransmua[$key][$key1]['total_brd'] = 0;
                    $qctransmua[$key][$key1]['total_brd/TPH'] = 0;
                    $qctransmua[$key][$key1]['total_buah'] = 0;
                    $qctransmua[$key][$key1]['total_buahPerTPH'] = 0;
                    $qctransmua[$key][$key1]['skor_brdPertph'] = 0;
                    $qctransmua[$key][$key1]['skor_buahPerTPH'] = 0;
                    $qctransmua[$key][$key1]['totalSkor'] = 0;
                }

                $nonZeroValues = array_filter([$sum_btWil, $sum_rstWil]);


                if (!empty($nonZeroValues)) {
                    $data = 'ada';
                } else {
                    $data = 'kosong';
                }
                $qctransmua[$key]['PT.MUA'] = [
                    'check_data' => $data,
                    'tph_sample' => $dataBLokWil,
                    'total_brd' => $sum_btWil,
                    'total_brd/TPH' => $brdPertphWil,
                    'total_buah' => $sum_rstWil,
                    'total_buahPerTPH' => $buahPerTPHWil,
                    'skor_brdPertph' =>   skor_brd_tinggal($brdPertphWil),
                    'skor_buahPerTPH' => skor_buah_tinggal($buahPerTPHWil),
                    'totalSkor' => $totalSkorWil,
                ];
            }

            foreach ($qctransmua as $key => $value) {
                $qctransmua = $value;
            }
            // dd($qcancakmua, $qcbuahmua, $qctransmua);

            $qcinspeksimua = [];
            foreach ($qcancakmua as $key => $value) {
                foreach ($qcbuahmua as $key1 => $value1) {
                    foreach ($qctransmua as $key2 => $value2)   if (
                        $key == $key1
                        && $key == $key2
                    ) {
                        // dd($value);
                        if ($value['check_data'] == 'kosong' && $value1['check_data'] === 'kosong' && $value2['check_data'] === 'kosong') {
                            $qcinspeksimua[$key]['TotalSkor'] = '-';
                            $qcinspeksimua[$key]['checkdata'] = 'kosong';
                        } else {
                            $qcinspeksimua[$key]['TotalSkor'] = $value['skor_akhir'] + $value1['TOTAL_SKOR'] + $value2['totalSkor'];
                            $qcinspeksimua[$key]['checkdata'] = 'ada';
                        }

                        $qcinspeksimua[$key]['est'] = $key;
                        $qcinspeksimua[$key]['afd'] = 'OA';
                    }
                }
            }

            // dd($sidak_buah_mua);
            $rekapmua = [];
            foreach ($qcinspeksimua as $key => $value) {
                if (
                    isset($sidak_buah_mua[$key]) &&
                    isset($newSidak_mua[$key])
                ) {
                    $valtph2 = $newSidak_mua[$key];
                    $valmtb = $sidak_buah_mua[$key];
                    $skortph = $valtph2['score_estate'] ?? null;
                    $skormtb = $valmtb['All_skor'] ?? null;

                    if ($valmtb['check_arr'] == 'ada') {
                        $databh = 1;
                    } else {
                        $databh = 0;
                    }
                    if ($value['checkdata'] == 'ada') {
                        $dataqc = 1;
                    } else {
                        $dataqc = 0;
                    }
                    if ($valtph2['checkdata'] == 'ada') {
                        $datatph = 1;
                    } else {
                        $datatph = 0;
                    }
                    // dd($key);
                    foreach ($queryAsisten as $keyx => $valuex) {
                        if ($valuex['est'] === $key && $valuex['afd'] === 'OA') {
                            $rekapmua[$key]['asistenafd'] = $valuex['nama'] ?? '-';
                            break;
                        } elseif ($valuex['est'] === $key && $valuex['afd'] === 'EM') {
                            $rekapmua[$key]['manager'] = $valuex['nama'] ?? '-';
                        }
                    }


                    $check = $databh + $dataqc + $datatph;
                    $rekapmua[$key]['skorqc'] = $value['TotalSkor'];
                    // $rekapmua[$key]['nama'] = $value['TotalSkor'];
                    $rekapmua[$key]['skor_mutubuah'] = $skormtb;
                    $rekapmua[$key]['skortph'] = $skortph;
                    $rekapmua[$key]['check'] = $check;

                    $a = $value['TotalSkor'];
                    $b = $skormtb;
                    $c = $skortph;

                    // Convert '-' to 0, keeping other values unchanged
                    $a = ($a === '-') ? 0 : $a;
                    $b = ($b === '-') ? 0 : $b;
                    $c = ($c === '-') ? 0 : $c;

                    $rekapmua[$key]['skorestate'] = ($check != 0) ? round(($a + $b + $c) / $check, 2) : 0;
                }
            }
            // dd($qcinspeksimua, $sidak_buah_mua, $newSidak_mua, $rekapmua);
            // dd($rekapmua);

            // foreach ($rekapmua as $key => $value) {
            //     # code...
            // }
        } else {
            $rekapmua = [];
        }

        // dd($rekapafd);
        $arr = array();
        $arr['rekapafd'] = $rekapafd;
        $arr['rekapmua'] = $rekapmua;

        echo json_encode($arr);
        exit();
    }
}
