<?php


namespace App\Http\Controllers\incpectcomponent;

use Illuminate\Http\Request;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

require '../app/helpers.php';

use Illuminate\Support\Facades\Storage;


class makemapsController extends Controller
{

    public function plotBlok(Request $request)
    {
        $est = $request->get('est');
        $regData = $request->get('regData');
        $date = $request->get('date');

        // dd($regData);

        $queryTrans = DB::connection('mysql2')->table("mutu_transport")
            ->select("mutu_transport.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
            ->where('mutu_transport.estate', $est)
            ->whereYear('mutu_transport.datetime', $date)
            ->where('mutu_transport.afdeling', '!=', 'Pla')
            // ->where('mutu_transport.afd', 'OA')
            ->get();

        $DataEstate = $queryTrans->groupBy('blok');
        $DataEstate = json_decode($DataEstate, true);

        $queryBuah = DB::connection('mysql2')->table("mutu_buah")
            ->select("mutu_buah.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_buah.estate')
            ->where('mutu_buah.estate', $est)
            ->whereYear('mutu_buah.datetime', $date)
            ->where('mutu_buah.afdeling', '!=', 'Pla')
            // ->where('mutu_buah.afd', 'OA')
            ->get();

        $DataMTbuah = $queryBuah->groupBy('blok');
        $DataMTbuah = json_decode($DataMTbuah, true);

        $queryAncak = DB::connection('mysql2')->table("mutu_ancak_new")
            ->select("mutu_ancak_new.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
            ->where('mutu_ancak_new.estate', $est)
            ->whereYear('mutu_ancak_new.datetime', $date)
            ->where('mutu_ancak_new.afdeling', '!=', 'Pla')
            // ->where('mutu_ancak_new.afd', 'OA')
            ->get();

        $DataMTAncak = $queryAncak->groupBy('blok');
        $DataMTAncak = json_decode($DataMTAncak, true);
        $QueryEst = DB::connection('mysql2')
            ->table("estate")
            ->join('afdeling', 'afdeling.estate', 'estate.id')
            ->join('blok', 'blok.afdeling', '=', 'afdeling.id')
            ->select('blok.id', 'blok.nama', DB::raw('afdeling.nama as `afdeling`'), 'blok.lon', 'blok.lat')
            ->where('est', $est)
            // ->orderBy('lat', 'desc')
            ->get();


        $queryBlok = json_decode($QueryEst, true);
        // dd($dataAfdeling);
        // $bloks_afd = array_reduce($queryBlok->toArray(), function ($carry, $item) {
        //     $carry[$item->afdeling][$item->nama][] = $item;
        //     return $carry;
        // }, []);
        $bloks_afd = array_reduce($queryBlok, function ($carry, $item) {
            $carry[$item['afdeling']][$item['nama']][] = $item;
            return $carry;
        }, []);

        $plotBlokAlls = [];
        foreach ($bloks_afd as $key => $coord) {
            foreach ($coord as $key2 => $value) {
                foreach ($value as $key3 => $value1) {
                    $plotBlokAlls[$key][] = [$value1['lat'], $value1['lon']];
                }
            }
        }
        // dd($plotBlokAlls);

        $dataAfdeling = $QueryEst->groupBy('afdeling', 'nama');
        $dataAfdeling = json_decode($dataAfdeling, true);
        $coordinates = [];

        foreach ($dataAfdeling as $key => $afdelingItems) {
            $coords = [];
            foreach ($afdelingItems as $item) {
                $coords[] = [
                    'lat' => $item['lat'],
                    'lon' => $item['lon'],
                ];
            }
            $coordinates[$key] = $coords;
        }

        // dd($coordinates);

        $dataSkor = array();
        foreach ($DataEstate as $key => $value) {
            $sum_bt = 0;
            $sum_Restan = 0;
            $tph_sample = 0;
            $listBlokPerAfd = array();
            foreach ($value as $key2 => $value2) {
                if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                }
                $tph_sample = count($listBlokPerAfd);
                $sum_Restan += $value2['rst'];
                $sum_bt += $value2['bt'];
            }
            $skorTrans = skor_brd_tinggal(round($sum_bt / $tph_sample, 2)) + skor_buah_tinggal(round($sum_Restan / $tph_sample, 2));
            $dataSkor[$key][0]['skorTrans'] = $skorTrans;
        }

        foreach ($DataMTbuah as $key => $value) {
            $listBlokPerAfd = array();
            $janjang = 0;
            $Jjg_Mth = 0;
            $Jjg_Mth2 = 0;
            $Jjg_Over = 0;
            $Jjg_Empty = 0;
            $Jjg_Abr = 0;
            $Jjg_Vcut = 0;
            $Jjg_Als = 0;
            foreach ($value as $key2 => $value2) {
                if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                }
                $dtBlok = count($listBlokPerAfd);
                $janjang += $value2['jumlah_jjg'];
                $Jjg_Mth += $value2['bmt'];
                $Jjg_Mth2 += $value2['bmk'];
                $Jjg_Over += $value2['overripe'];
                $Jjg_Empty += $value2['empty_bunch'];
                $Jjg_Abr += $value2['abnormal'];
                $Jjg_Vcut += $value2['vcut'];
                $Jjg_Als += $value2['alas_br'];
            }
            $jml_mth = ($Jjg_Mth + $Jjg_Mth2);
            $jml_mtg = $janjang - ($jml_mth + $Jjg_Over + $Jjg_Empty + $Jjg_Abr);
            $perBuahMentah = ($janjang - $Jjg_Abr) != 0 ? round(($jml_mth / ($janjang - $Jjg_Abr)) * 100, 2) : 0;
            $perBuahMtg = ($janjang - $Jjg_Abr) != 0 ? round(($jml_mtg / ($janjang - $Jjg_Abr)) * 100, 2) : 0;
            $perBuahOver = ($janjang - $Jjg_Abr) != 0 ? round(($Jjg_Over / ($janjang - $Jjg_Abr)) * 100, 2) : 0;
            $perJangkos = ($janjang - $Jjg_Abr) != 0 ? round(($Jjg_Empty / ($janjang - $Jjg_Abr)) * 100, 2) : 0;

            $perVcut = count_percent($Jjg_Vcut, $janjang);
            $perAbnr = count_percent($Jjg_Abr, $janjang);
            $perKrgBrd = count_percent($Jjg_Als, $dtBlok);
            $skorBuah = skor_buah_mentah_mb($perBuahMentah) + skor_buah_masak_mb($perBuahMtg) + skor_buah_over_mb($perBuahOver) + skor_jangkos_mb($perJangkos) + skor_buah_over_mb($perVcut) + skor_abr_mb($perKrgBrd);

            $dataSkor[$key][0]['skorBuah'] = $skorBuah;
        }

        foreach ($DataMTAncak as $key => $value) {
            $listBlok = array();
            $sph = 0;
            $jml_pokok_sm = 0;
            $jml_jjg_panen = 0;
            $jml_brtp = 0;
            $jml_brtk = 0;
            $jml_brtgl = 0;
            $jml_bhts = 0;
            $jml_bhtm1 = 0;
            $jml_bhtm2 = 0;
            $jml_bhtm3 = 0;
            $jml_ps = 0;
            foreach ($value as $key2 => $value2) {
                if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlok)) {
                    if ($value2['sph'] != 0) {
                        $listBlok[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                        $sph += $value2['sph'];
                    }
                }
                $jml_blok = count($listBlok);
                $jml_pokok_sm += $value2['sample'];
                $jml_jjg_panen += $value2['jjg'];
                $jml_brtp += $value2['brtp'];
                $jml_brtk += $value2['brtk'];
                $jml_brtgl += $value2['brtgl'];
                $jml_bhts += $value2['bhts'];
                $jml_bhtm1 += $value2['bhtm1'];
                $jml_bhtm2 += $value2['bhtm2'];
                $jml_bhtm3 += $value2['bhtm2'];
                $jml_ps += $value2['ps'];
            }
            $jml_sph = $jml_blok == 0 ? $sph : ($sph / $jml_blok);
            $tot_brd = ($jml_brtp + $jml_brtk + $jml_brtgl);
            $tot_jjg = ($jml_bhts + $jml_bhtm1 + $jml_bhtm2 + $jml_bhtm3);
            $luas_ha = ($jml_sph != 0) ? round(($jml_pokok_sm / $jml_sph), 2) : 0;


            $perBrdt = ($jml_jjg_panen != 0) ? round(($tot_brd / $jml_jjg_panen), 2) : 0;
            $perBt = ($jml_jjg_panen != 0) ? round(($tot_jjg / ($jml_jjg_panen + $tot_jjg)) * 100, 2) : 0;

            $perPSMA = count_percent($jml_ps, $jml_pokok_sm);
            $skorAncak = skor_brd_ma($perBrdt) + skor_buah_Ma($perBt) + skor_palepah_ma($perPSMA);

            $dataSkor[$key][0]['skorAncak'] = $skorAncak;
        }
        // dd($regData);

        $dataSkorResult = array();
        $newData = '';

        // dd($regData,$est);
        foreach ($dataSkor as $key => $value) {
            foreach ($value as $key1 => $value1) {
                // dd($key);
                if ($est == "NBE") {
                    if (strlen($key) == 5) {
                        $newData = substr($key, 0, -2);
                    } else if (strlen($key) == 4) {
                        $newData = substr($key, 0, 1) . substr($key, 2);
                    }
                } else {
                    if (strlen($key) == 5) {
                        $sliced = substr($key, 0, -2);
                        $newData = substr_replace($sliced, '0', 1, 0);
                    } else if ($est == "KTE" || $est == "MKE" || $est == "PKE" || $est == "BSE" || $est == "BWE" || $est == "GDE") {
                        if (strlen($key) == 6  && substr($key, 0, 1) == 'H') {
                            $sliced = substr($key, 0, -2);
                            $newData = substr($sliced, 0, 1) . substr($sliced, 2);
                        } elseif (strlen($key) == 6) {
                            $newData = substr($key, 0, -3);
                        }
                    } else if ($est == "BDE") {
                        // $replace = substr_replace($key, '', 1, 1);
                        // $sliced = substr($replace, 0, -2);
                        $newData = substr($key, 0, -3);
                    } else if (strlen($key) == 8) {
                        $replace = substr_replace($key, '', 1, 1);
                        $sliced = substr($replace, 0, -2);
                        $newData = substr($key, 0, -3);
                    } else if (strlen($key) == 7) {
                        $replace = substr_replace($key, '', 1, 1);
                        $sliced = substr($replace, 0, -2);
                        $newData = substr($key, 0, -3);
                    } else if (strlen($key) == 6) {
                        $replace = substr_replace($key, '', 1, 1);
                        $sliced = substr($replace, 0, -2);
                        $newData = substr_replace($sliced, '0', 1, 0);
                    } else if (strlen($key) == 3) {
                        $sliced = $key;
                        $newData = substr_replace($sliced, '0', 1, 0);
                    } else if (strpos($key, 'CBI') !== false && strlen($key) == 9) {
                        $sliced = substr($key, 0, -6);
                        $newData = substr_replace($sliced, '0', 1, 0);
                    } else if (strpos($key, 'CBI') !== false) {
                        $newData = substr($key, 0, -4);
                    } else if (strpos($key, 'CB') !== false) {
                        $replace = substr_replace($key, '', 1, 1);
                        $sliced = substr($replace, 0, -3);
                        $newData = substr_replace($sliced, '0', 1, 0);
                    } else if ($regData == [7, 8]) {
                        $newData = substr($key, 0, 3);
                    } else if ($regData == [10, 11]) {
                        $newData = substr($key, 0, 4);
                    }
                }



                $skorTrans = check_array('skorTrans', $value1);
                $skorBuah = check_array('skorBuah', $value1);
                $skorAncak = check_array('skorAncak', $value1);
                // $skorAkhir = $skorTrans + $skorBuah + $skorAncak;
                if ($skorTrans != 0 && $skorAncak != 0) {
                    $skorAkhir = $skorTrans + $skorAncak + 34;
                } else if ($skorTrans != 0) {
                    $skorAkhir = $skorTrans + 34;
                } else if ($skorAncak != 0) {
                    $skorAkhir = $skorAncak + 34;
                } else {
                    $skorAkhir = 0;
                }

                if ($skorTrans == 0 && $skorAncak == 0) {
                    $check = 'empty';
                } else {
                    $check = 'data';
                }

                if ($check == 'data') {
                    $skor_kategori_akhir_est = skor_kategori_akhir($skorAkhir);
                } else {
                    $skor_kategori_akhir_est = 'xxx';
                }



                $dataSkorResult[$newData][0]['estate'] = $est;
                $dataSkorResult[$newData][0]['skorTrans'] = $skorTrans;
                $dataSkorResult[$newData][0]['skorBuah'] = $skorBuah;
                $dataSkorResult[$newData][0]['skorAncak'] = $skorAncak;
                $dataSkorResult[$newData][0]['blok'] = $newData;
                $dataSkorResult[$newData][0]['text'] = $skor_kategori_akhir_est[1];
                $dataSkorResult[$newData][0]['skorAkhir'] = $skorAkhir;
                $dataSkorResult[$newData][0]['check_data'] = $check;
            }
        }


        // end testing 

        $datas = array();
        foreach ($dataSkorResult as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
            }
        }

        $list_blok = array();
        foreach ($datas as $key => $value) {
            $list_blok[$est][] = $value['blok'];
        }


        $estateQuery = DB::connection('mysql2')->Table('estate')
            ->join('afdeling', 'afdeling.estate', 'estate.id')
            ->where('est', $est)
            ->get();

        $listIdAfd = array();
        foreach ($estateQuery as $key => $value) {
            $listIdAfd[] = $value->id;
        }
        $blokEstate =  DB::connection('mysql2')->Table('blok')->whereIn('afdeling', $listIdAfd)->groupBy('nama')->pluck('nama', 'id');
        $blokEstateFix[$est] = json_decode($blokEstate, true);




        // $values = array_values($blokEstateFix['BDE']);



        // dd($dataSkorResult,$blokEstateFix);
        $blokLatLn = array();
        foreach ($blokEstateFix as $key => $value) {
            $inc = 0;
            foreach ($value as $key2 => $data) {
                $nilai = 0;
                $kategori = 'x';

                if (isset($dataSkorResult[$data])) {
                    $value4 = $dataSkorResult[$data][0];
                    $nilai = $value4['skorAkhir'];
                    $kategori = $value4['text'];
                }

                $query = DB::connection('mysql2')->table('blok')
                    ->select('blok.*')
                    ->whereIn('blok.afdeling', $listIdAfd)
                    ->get();

                $latln = '';
                $queryAfd  = '';
                foreach ($query as $key3 => $val) {
                    if ($val->nama == $data) {
                        $latln .= '[' . $val->lon . ',' . $val->lat . '],';
                        $afd =  $val->afdeling;
                        $queryAfd = DB::connection('mysql2')->table('afdeling')
                            ->select('afdeling.*')
                            ->where('id', $afd)
                            ->first();

                        // $queryEst = DB::connection('mysql2')->table('afdeling')
                        // ->select('afdeling.*')
                        // ->whereIn('afdeling.id', $afd)
                        // ->pluck('nama');


                    }
                }

                $blokLatLn[$inc]['blok'] = $data;
                $blokLatLn[$inc]['estate'] = $est;
                $blokLatLn[$inc]['latln'] = rtrim($latln, ',');
                $blokLatLn[$inc]['nilai'] = $nilai;
                $blokLatLn[$inc]['afdeling'] = $queryAfd->nama;
                $blokLatLn[$inc]['kategori'] = $kategori;

                $inc++;
            }
        }



        $dataLegend = array();
        $excellent = array();
        $good = array();
        $satis = array();
        $fair = array();
        $poor = array();
        $empty = array();
        $dataLegend = array();
        foreach ($blokLatLn as $key => $value) {
            $skor = $value['nilai'];
            $data = $value['kategori'];
            if ($data == 'EXCELLENT') {
                $excellent[] = $value['nilai'];
            } else if ($data == 'GOOD') {
                $good[] = $value['nilai'];
            } else if ($data == 'SATISFACTORY') {
                $satis[] = $value['nilai'];
            } else if ($data == 'FAIR') {
                $fair[] = $value['nilai'];
            } else if ($data == 'POOR') {
                $poor[] = $value['nilai'];
            } else if ($data == 'x') {
                $empty[] = $value['nilai'];
            }
        }

        $tot_exc = count($excellent);
        $tot_good = count($good);
        $tot_satis = count($satis);
        $tot_fair = count($fair);
        $tot_poor = count($poor);
        $tot_empty = count($empty);

        $totalSkor = $tot_exc + $tot_good + $tot_satis + $tot_fair + $tot_poor + $tot_empty;

        $dataLegend['excellent'] = $tot_exc;
        $dataLegend['good'] = $tot_good;
        $dataLegend['satis'] = $tot_satis;
        $dataLegend['fair'] = $tot_fair;
        $dataLegend['poor'] = $tot_poor;
        $dataLegend['empty'] = $tot_empty;
        $dataLegend['total'] = $totalSkor;
        $dataLegend['perExc'] = count_percent($tot_exc, $totalSkor);
        $dataLegend['perGood'] = count_percent($tot_good, $totalSkor);
        $dataLegend['perSatis'] = count_percent($tot_satis, $totalSkor);
        $dataLegend['perFair'] = count_percent($tot_fair, $totalSkor);
        $dataLegend['perPoor'] = count_percent($tot_poor, $totalSkor);
        $dataLegend['perEmpty'] = count_percent($tot_empty, $totalSkor);


        // $filteredKeys = array_keys($dataSkorResult);

        // $diff = array_diff($filteredKeys, $values);
        // $sameCount = count($filteredKeys) - count($diff);

        // $result = [
        //     'same_count' => $sameCount,
        //     'same_keys' => array_intersect($values, $filteredKeys),
        //     'not_same_count' => count($diff),
        //     'not_same_keys' => $diff
        // ];




        // dd($values, $filteredKeys ,$dataSkor , $result);
        // dd($blokLatLn,$dataLegend);
        $highestValue = null;
        $estatesWithHighestNilai = [];
        $bloksWithHighestNilai = [];

        foreach ($blokLatLn as $value) {
            $nilai = $value['nilai'];
            $estate = $value['estate'];
            $blok = $value['blok'];

            if ($highestValue === null || $nilai > $highestValue) {
                $highestValue = $nilai;
                $estatesWithHighestNilai = [$estate];
                $bloksWithHighestNilai = [$blok];
            } elseif ($nilai === $highestValue) {
                $estatesWithHighestNilai[] = $estate;
                $bloksWithHighestNilai[] = $blok;
            }
        }

        $resultsHIgh = [
            'estate' => $estatesWithHighestNilai,
            'blok' => $bloksWithHighestNilai,
            'nilai' => $highestValue,
        ];

        $lowestValue = null;
        $estatesWithLowestNilai = [];
        $bloksWithLowestNilai = [];

        foreach ($blokLatLn as $value) {
            $nilai = $value['nilai'];
            $estate = $value['estate'];
            $blok = $value['blok'];

            if ($lowestValue === null && $nilai !== 0) {
                $lowestValue = $nilai;
                $estatesWithLowestNilai = [$estate];
                $bloksWithLowestNilai = [$blok];
            } elseif ($nilai < $lowestValue && $nilai !== 0) {
                $lowestValue = $nilai;
                $estatesWithLowestNilai = [$estate];
                $bloksWithLowestNilai = [$blok];
            } elseif ($nilai === $lowestValue && $nilai !== 0) {
                $estatesWithLowestNilai[] = $estate;
                $bloksWithLowestNilai[] = $blok;
            }
        }

        $resultsLow = [
            'estate' => $estatesWithLowestNilai,
            'blok' => $bloksWithLowestNilai,
            'nilai' => $lowestValue,
        ];


        // dd($blokLatLn,$dataLegend);


        $plot['blok'] = $blokLatLn;
        $plot['legend'] = $dataLegend;
        $plot['lowest'] = $resultsLow;
        $plot['highest'] = $resultsHIgh;
        $plot['afdeling'] = $plotBlokAlls;


        // dd($plotBlokAll);
        echo json_encode($plot);
    }

    public function getMapsdetail(Request $request)
    {
        $est = $request->input('est');
        $afd = $request->input('afd');
        $date = $request->input('Tanggal');

        // dd($est, $afd, $date);
        $queryPlotEst = DB::connection('mysql2')->table('estate_plot')
            ->select("estate_plot.*")
            ->where('est', $est)
            ->get();
        // $queryPlotEst = $queryPlotEst->groupBy(['estate', 'afdeling']);
        $queryPlotEst = json_decode($queryPlotEst, true);

        // dd($queryPlotEst);

        $convertedCoords = [];
        foreach ($queryPlotEst as $coord) {
            $convertedCoords[] = [$coord['lat'], $coord['lon']];
        }

        $afd = $request->input('afd');
        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->where('est', '=', $est)
            ->where('afdeling.nama', '=', $afd)
            ->get();
        $queryAfd = json_decode($queryAfd, true);

        $id = $queryAfd[0]['id'];

        $queryBlokMA = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select('mutu_ancak_new.*', 'mutu_ancak_new.blok as nama_blok')
            ->whereDate('mutu_ancak_new.datetime', $date)
            ->where('estate', $est)
            ->where('afdeling', $afd)
            ->orderBy('mutu_ancak_new.datetime', 'desc')
            ->groupBy('nama_blok')
            ->pluck('blok');
        $queryBlokMA = json_decode($queryBlokMA, true);


        $queryBlokMB = DB::connection('mysql2')->table('mutu_buah')
            ->select('mutu_buah.*', 'mutu_buah.blok as nama_blok')
            ->whereDate('mutu_buah.datetime', $date)
            ->where('estate', $est)
            ->where('afdeling', $afd)
            ->orderBy('mutu_buah.datetime', 'desc')
            ->groupBy('nama_blok')
            ->pluck('blok');

        $queryBlokMB = json_decode($queryBlokMB, true);
        $queryBlokMT = DB::connection('mysql2')->table('mutu_transport')
            ->select('mutu_transport.*', 'mutu_transport.blok as nama_blok')
            ->whereDate('mutu_transport.datetime', $date)
            ->where('estate', $est)
            ->where('afdeling', $afd)
            ->orderBy('mutu_transport.datetime', 'desc')
            ->groupBy('nama_blok')
            ->pluck('blok');

        $queryBlokMT = json_decode($queryBlokMT, true);

        //merge all blok untuk mendapatkan semua blok ma mb mt visit hari tersebut
        $allBlokSidakRaw = array_merge($queryBlokMA, $queryBlokMB, $queryBlokMT);

        //array unique mengambil eliminasi blok yg sama
        $allBlokSidakRaw = array_unique($allBlokSidakRaw);

        //sisipkan 0 setelah digit pertama


        $blokSidak = array();
        foreach ($allBlokSidakRaw as $value) {
            $length = strlen($value);
            $blokSidak[] = substr($value, 0, $length - 3);
            $modifiedStr2  = substr($value, 0, $length - 2);
            $blokSidak[] = substr($value, 0, $length - 2);
            $blokSidak[] =  substr_replace($modifiedStr2, "0", 1, 0);
        }

        // dd($allBlokRaw, $allBlok);
        $blokSidakResult = DB::connection('mysql2')
            ->table('blok')
            ->select('blok.nama as nama_blok_visit')
            ->where('afdeling', $id)
            ->whereIn('nama', $blokSidak)
            ->groupBy('nama_blok_visit')
            ->pluck('nama_blok_visit');

        $queryBlok = DB::connection('mysql2')->table('blok')
            ->select("blok.*")
            ->where('afdeling', '=', $id)
            ->get();
        $queryBlok = json_decode($queryBlok, true);

        $bloks_afd = array_reduce($queryBlok, function ($carry, $item) {
            $carry[$item['nama']][] = $item;
            return $carry;
        }, []);

        $bloks_afds = [];
        foreach ($bloks_afd as $blok => $coords) {
            foreach ($coords as $coord) {
                $bloks_afds[] = [
                    'blok' => $blok,
                    'lat' => $coord['lat'],
                    'lon' => $coord['lon'],
                ];
            }
        }

        $plotBlokAll = [];
        foreach ($bloks_afd as $key => $coord) {
            foreach ($coord as $key2 => $value) {
                $plotBlokAll[$key][] = [$value['lat'], $value['lon']];
            }
        }

        // Sort the coordinates in ascending order based on the first value


        //  dd($plotBlokAll);

        $queryTrans = DB::connection('mysql2')->table("mutu_transport")
            ->select("mutu_transport.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
            ->where('mutu_transport.estate', $est)
            ->where('mutu_transport.afdeling', $afd)
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_transport.afdeling', '!=', 'Pla')
            ->get();
        $queryTrans = json_decode($queryTrans, true);

        // dd($queryTrans);

        $queryBuah = DB::connection('mysql2')->table("mutu_buah")
            ->select("mutu_buah.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_buah.estate')
            ->where('mutu_buah.estate', $est)
            ->where('mutu_buah.afdeling', $afd)
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_buah.afdeling', '!=', 'Pla')
            ->get();
        $queryBuah = json_decode($queryBuah, true);

        $groupedTrans = array_reduce($queryTrans, function ($carry, $item) {
            $carry[$item['blok']][] = $item;
            return $carry;
        }, []);

        $groupedBuah = array_reduce($queryBuah, function ($carry, $item) {
            $carry[$item['blok']][] = $item;
            return $carry;
        }, []);

        // dd($groupedBuah);
        $buah_plot = [];

        foreach ($groupedBuah as $blok => $coords) {
            foreach ($coords as $coord) {
                $datetime = $coord['datetime'];
                $time = date('H:i:s', strtotime($datetime));

                $maps = $coord['app_version'];
                if (strpos($maps, ';GA') !== false) {
                    $maps = 'GPS Akurat';
                } elseif (strpos($maps, ';GL') !== false) {
                    $maps = 'GPS Liar';
                }

                $buah_plot[$blok][] = [
                    'blok' => $blok,
                    'lat' => $coord['lat'],
                    'lon' => $coord['lon'],
                    'foto_temuan' => $coord['foto_temuan'],
                    'komentar' => $coord['komentar'],
                    'tph_baris' => $coord['tph_baris'],
                    'status_panen' => $coord['status_panen'],
                    'jumlah_jjg' => $coord['jumlah_jjg'],
                    'bmt' => $coord['bmt'],
                    'bmk' => $coord['bmk'],
                    'overripe' => $coord['overripe'],
                    'empty_bunch' => $coord['empty_bunch'],
                    'abnormal' => $coord['abnormal'],
                    'vcut' => $coord['vcut'],
                    'alas_br' => $coord['alas_br'],
                    'maps' => $maps,
                    'time' => $time,
                ];
            }
        }




        // dd($buah_plot);
        $trans_plot = [];
        foreach ($groupedTrans as $blok => $coords) {
            foreach ($coords as $coord) {
                $datetime = $coord['datetime'];
                $time = date('H:i:s', strtotime($datetime));
                $maps = $coord['app_version'];
                if (strpos($maps, ';GA') !== false) {
                    $maps = 'GPS Akurat';
                } elseif (strpos($maps, ';GL') !== false) {
                    $maps = 'GPS Liar';
                }

                $trans_plot[$blok][] = [
                    'blok' => $blok,
                    'lat' => $coord['lat'],
                    'lon' => $coord['lon'],
                    'foto_temuan' => $coord['foto_temuan'],
                    'foto_fu' => $coord['foto_fu'],
                    'komentar' => $coord['komentar'],
                    'status_panen' => $coord['status_panen'],
                    'luas_blok' => $coord['luas_blok'],
                    'bt' => $coord['bt'],
                    'Rst' => $coord['rst'],
                    'maps' => $maps,
                    'time' => $time,
                ];
            }
        }



        $queryancak = DB::connection('mysql2')->table("mutu_ancak_new")
            ->select("mutu_ancak_new.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
            ->where('mutu_ancak_new.estate', $est)
            ->where('mutu_ancak_new.afdeling', $afd)
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_ancak_new.afdeling', '!=', 'Pla')
            ->get();
        $queryancak = json_decode($queryancak, true);

        $groupedAncak = array_reduce($queryancak, function ($carry, $item) {
            $carry[$item['blok']][] = $item;
            return $carry;
        }, []);

        $plotLine = array();
        foreach ($queryancak as $key => $value) {
            $plotLine[] =  '[' . $value['lat_awal'] . ',' . $value['lon_awal'] . '],[' . $value['lat_akhir'] . ',' . $value['lon_akhir'] . ']';
        }

        // dd($plotLine);   
        $queryancakFL = DB::connection('mysql2')->table("follow_up_ma")
            ->select("follow_up_ma.*", "estate.wil")
            ->join('estate', 'estate.est', '=', 'follow_up_ma.estate')
            ->where('follow_up_ma.estate', $est)
            ->where('follow_up_ma.afdeling', $afd)
            ->where('waktu_temuan', 'like', '%' . $date . '%')
            ->where('follow_up_ma.afdeling', '!=', 'Pla')
            ->get();
        $queryancakFL = json_decode($queryancakFL, true);

        $groupedAncakFL = array_reduce($queryancakFL, function ($carry, $item) {
            $carry[$item['blok']][] = $item;
            return $carry;
        }, []);
        // dd($groupedAncak['T01505'],$groupedAncakFL['T01505']);
        // dd($ancak_plot);
        $ancak_fa = [];
        foreach ($groupedAncakFL as $blok => $coords) {
            foreach ($coords as $coord) {
                // dd($coord);
                $datetime = $coord['waktu_temuan'];
                $time = date('H:i:s', strtotime($datetime));
                $ancak_fa[] = [
                    'blok' => $blok,
                    'estate' => $coord['estate'],
                    'afdeling' => $coord['afdeling'],
                    'br1' => $coord['br1'],
                    'br2' => $coord['br2'],
                    'jalur_masuk' => $coord['jalur_masuk'],
                    'foto_temuan1' => $coord['foto_temuan1'],
                    'foto_temuan2' => $coord['foto_temuan2'],
                    'foto_fu1' => $coord['foto_fu1'],
                    'foto_fu2' => $coord['foto_fu2'],
                    'komentar' => $coord['komentar'],
                    'lat' => $coord['lat'],
                    'lon' => $coord['lon'],
                    'time' => $time,
                ];
            }
        }

        $groupedArray = [];

        foreach ($ancak_fa as $item) {
            $blok = $item['blok'];
            if (!isset($groupedArray[$blok])) {
                $groupedArray[$blok] = [];
            }
            $groupedArray[$blok][] = $item;
        }
        // dd($groupedArray,$ancak_fa);


        $ancak_plot = [];

        foreach ($groupedAncak as $blok => $coords) {
            foreach ($coords as $coord) {
                $matchingAncakFa = [];

                foreach ($ancak_fa as $key => $value) {
                    if ($coord['blok'] == $value['blok'] && $coord['br1'] == $value['br1'] && $coord['br2'] == $value['br2'] && $coord['jalur_masuk'] == $value['jalur_masuk']) {
                        $matchingAncakFa[] = $value;
                    }
                }
                $maps = $coord['app_version'];
                if (strpos($maps, ';GA') !== false) {
                    $maps = 'GPS Akurat';
                } elseif (strpos($maps, ';GL') !== false) {
                    $maps = 'GPS Liar';
                }
                $ancak_fa_item = [
                    'blok' => $blok,
                    'estate' => $coord['estate'],
                    'afdeling' => $coord['afdeling'],
                    'br1' => $coord['br1'],
                    'br2' => $coord['br2'],
                    'jalur_masuk' => $coord['jalur_masuk'],
                    'ket' => 'Lokasi akhir',
                    'lat_' => $coord['lat_akhir'],
                    'lon_' => $coord['lon_akhir'],
                    'luas_blok' => $coord['luas_blok'],
                    'sph' => $coord['sph'],
                    'sample' => $coord['sample'],
                    'pokok_kuning' => $coord['pokok_kuning'],
                    'piringan_semak' => $coord['piringan_semak'],
                    'underpruning' => $coord['underpruning'],
                    'overpruning' => $coord['overpruning'],
                    'jjg' => $coord['jjg'],
                    'brtp' => $coord['brtp'],
                    'brtk' => $coord['brtk'],
                    'brtgl' => $coord['brtgl'],
                    'bhts' => $coord['bhts'],
                    'bhtm1' => $coord['bhtm1'],
                    'bhtm2' => $coord['bhtm2'],
                    'bhtm3' => $coord['bhtm3'],
                    'maps' => $maps,
                    'ps' => $coord['ps'],
                    'sp' => $coord['sp'],
                    'time' => date('H:i:s', strtotime($coord['datetime'])),
                ];

                if (!empty($matchingAncakFa)) {
                    $foto_temuan1 = [];
                    $foto_temuan2 = [];
                    $foto_fu1 = [];
                    $foto_fu2 = [];
                    $lat_ancak_fa = [];
                    $lon_ancak_fa = [];
                    $komentar = [];

                    foreach ($matchingAncakFa as $match) {
                        $foto_temuan1[] = 'foto_temuan1:' . (!empty($match['foto_temuan1']) ? $match['foto_temuan1'] : '') . ',foto_temuan2:' . (!empty($match['foto_temuan2']) ? $match['foto_temuan2'] : ' ') . ',foto_fu1:' . (!empty($match['foto_fu1']) ? $match['foto_fu1'] : ' ') . ',foto_fu2:' . (!empty($match['foto_fu2']) ? $match['foto_fu2'] : ' ') . ',lat:' . $match['lat'] . ',lon:' . $match['lon'] . ',komentar:' . $match['komentar'];

                        $foto_fu1[] = 'foto_fu1:' . (!empty($match['foto_fu1']) ? $match['foto_fu1'] : '') . ',foto_fu2:' . (!empty($match['foto_fu2']) ? $match['foto_fu2'] : ' ') . ',lat:' . $match['lat'] . ',lon:' . $match['lon'];
                    }


                    $ancak_fa_item['foto_temuan'] = $foto_temuan1;

                    // $ancak_fa_item['foto_fu'] = $foto_fu1;

                }



                $ancak_plot[] = $ancak_fa_item;
            }
        }





        // dd($trans_plot,$buah_plot,$ancak_plot); 

        return response()->json([
            'plot_line' => $plotLine,
            'coords' => $convertedCoords,
            'plot_blok_all' => $plotBlokAll,
            'trans_plot' => $trans_plot,
            'buah_plot' => $buah_plot,
            'ancak_plot' => $ancak_plot,
            'blok_sidak' => $blokSidakResult
        ]);
    }




    public function downloadMaptahun(Request $request)
    {
        $imgData = $request->input('imgData');
        $estData = $request->input('estData');
        $regData = $request->input('regData');
        $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgData));

        // dd($estData);
        // Generate a unique filename for the image, e.g., using a timestamp
        $filename = 'img_map_' . time() . '.png';

        // Define the public directory path
        $publicMapPath = public_path('img_map');
        $publicResultPath = public_path('img_result');

        // Check if the "img_map" directory exists, if not, create it
        if (!file_exists($publicMapPath)) {
            mkdir($publicMapPath, 0755, true);
        }

        // Save the image to the "img_map" directory
        file_put_contents($publicMapPath . '/' . $filename, $decodedImage);

        $files = File::files($publicMapPath);

        foreach ($files as $file) {
            $imageInfo = getimagesize($file);

            if ($imageInfo !== false) {
                list($width, $height, $type) = $imageInfo;

                $originalImage = imagecreatefromstring(file_get_contents($file));

                $minX = $width;
                $minY = $height;
                $maxX = 0;
                $maxY = 0;

                for ($x = 0; $x < $width; $x++) {
                    for ($y = 0; $y < $height; $y++) {
                        $pixelColor = imagecolorat($originalImage, $x, $y);
                        $color = imagecolorsforindex($originalImage, $pixelColor);
                        if ($color['red'] !== 0 || $color['green'] !== 0 || $color['blue'] !== 0) {
                            $minX = min($minX, $x);
                            $minY = min($minY, $y);
                            $maxX = max($maxX, $x);
                            $maxY = max($maxY, $y);
                        }
                    }
                }
                $cropWidth = $maxX - $minX + 1;
                $cropHeight = $maxY - $minY + 1;
                $croppedImage = imagecrop($originalImage, ['x' => $minX, 'y' => $minY, 'width' => $cropWidth, 'height' => $cropHeight]);

                $outputFile = $publicResultPath . '/' . basename($file);
                imagejpeg($croppedImage, $outputFile);

                imagedestroy($originalImage);
                imagedestroy($croppedImage);

                // Delete the original image from "img_map"
                unlink($file);
            }
        }


        // Provide the public URL for the saved image in "img_result"
        $publicUrl = asset('img_result/' . $filename);

        return response()->json(['message' => 'Image saved successfully', 'filename' => $filename, 'est' => $estData]);
    }




    public function pdfPage($filename, $est)
    {
        $url = $filename;
        $estData = $est;

        $pdf = PDF::loadView('pdfimgInspeksi', compact('url', 'estData'));

        $customPaper = array(360, 360, 360, 360);
        $pdf->set_paper('A2', 'landscape');

        $filename = 'Data Map Inspeksi per Blok' . '.pdf';

        return $pdf->stream($filename);
    }
}
