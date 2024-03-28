<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use DateTime;
use Illuminate\Validation\Rules\Unique;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Expr\Isset_;

require_once(app_path('helpers.php'));

class emplacementsController extends Controller
{
    public function filterv2(Request $request)
    {

        $date = $request->input('date');

        $Reg = $request->input('reg');

        $QueryMTancakWil = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
            // ->whereYear('datetime', '2023')
            // ->where('datetime', 'like', '%' . $getDate . '%')
            ->where('datetime', 'like', '%' . $date . '%')
            // ->whereYear('datetime', $year)
            ->get();
        $QueryMTancakWil = $QueryMTancakWil->groupBy(['estate', 'afdeling']);
        $QueryMTancakWil = json_decode($QueryMTancakWil, true);

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
        //mutu buah
        $QueryMTbuahWil = DB::connection('mysql2')->table('mutu_buah')
            ->select(
                "mutu_buah.*",
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun')
            )
            ->where('datetime', 'like', '%' . $date . '%')
            ->get();
        $QueryMTbuahWil = $QueryMTbuahWil->groupBy(['estate', 'afdeling']);
        $QueryMTbuahWil = json_decode($QueryMTbuahWil, true);
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
        // dd($QueryMTbuahWil);
        //MUTU ANCAK
        $QueryTransWil = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
            )
            ->where('datetime', 'like', '%' . $date . '%')
            // ->whereYear('datetime', $year)
            ->get();
        $QueryTransWil = $QueryTransWil->groupBy(['estate', 'afdeling']);
        $QueryTransWil = json_decode($QueryTransWil, true);
        // dd($queryMTancak);
        foreach ($QueryTransWil as $estate => $afdelingArray) {
            $modifiedAfdelingArray = $afdelingArray;
            foreach ($afdelingArray as $afdeling => $data) {
                if ($estate === $afdeling) {
                    $modifiedAfdelingArray["OA"] = $data;
                    unset($modifiedAfdelingArray[$afdeling]);
                }
            }
            $QueryTransWil[$estate] = $modifiedAfdelingArray;
        }

        //afdeling
        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);
        //estate

        // $queryEste = DB::connection('mysql2')->table('estate')
        //     ->whereIn('wil', [1, 2, 3])->get();

        // dd($queryEste);
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3', 'CWS1', 'NBM', 'REG-1', 'SLM', 'SR', 'TC', 'SRS', 'SGM', 'SYM', 'SKM'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $Reg)
            ->get();
        $queryEste = json_decode($queryEste, true);
        $queryEstereg = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            // ->whereNotIn('estate.est', ['Plasma1'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $Reg)
            ->get();
        $queryEstereg = json_decode($queryEstereg, true);
        $queryEstePla = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $Reg)
            ->get();

        $queryEstePla = json_decode($queryEstePla, true);
        // dd($queryEstePla);

        $queryAsisten =  DB::connection('mysql2')->Table('asisten_qc')->get();
        // dd($QueryMTbuahWil);
        //end query
        $queryAsisten = json_decode($queryAsisten, true);
        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        //membuat array estate -> bulan -> afdeling
        //mutu Trans mengambil nilai
        $dataMTTrans = array();
        foreach ($QueryTransWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTTrans[$key][$key2][$key3] = $value3;
                }
            }
        }
        // dd($dataMTTrans);
        //membuat nilai default mutu Trans
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

        $defaultMtTransReg = array();
        foreach ($queryEstereg as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defaultMtTransReg[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }


        // dd($defPLAtrans, $defaultMtTrans);
        //mutu buah mengambil nilai
        $dataMTBuah = array();
        foreach ($QueryMTbuahWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataMTBuah[$key][$key2][$key3] = $value3;
                }
            }
        }


        //membuat nilai default mutu buah
        $defaultMTbuah = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultMTbuah[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }

        $defaultMTbuahReg = array();
        foreach ($queryEstereg as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultMTbuahReg[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        // dd($defaultMTbuah, $dataMTBuah);
        // mutu ancak
        $dataPerBulan = array();
        foreach ($QueryMTancakWil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $dataPerBulan[$key][$key2][$key3] = $value3;
                }
            }
        }
        $defaultNew = array();
        foreach ($queryEste as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }

        $defaultNew_reg = array();
        foreach ($queryEstereg as $est) {
            foreach ($queryAfd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew_reg[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        // menimpa mutu trans nilai default dengan nilaiyang ada
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

        $mutuAncakMergeReg = array();
        foreach ($defaultMtTransReg as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTTrans)) {
                    if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                        if (!empty($dataMTTrans[$estKey][$afdKey])) {
                            $mutuAncakMergeReg[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                        } else {
                            $mutuAncakMergeReg[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuAncakMergeReg[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuAncakMergeReg[$estKey][$afdKey] = $afdValue;
                }
            }
        }

        // dd($mutuAncakMerge);
        // menimpa mutu buah nilai default dengan nilaiyang ada
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

        $mutuBuahMergeReg = array();
        foreach ($defaultMTbuahReg as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTBuah)) {
                    if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                        if (!empty($dataMTBuah[$estKey][$afdKey])) {
                            $mutuBuahMergeReg[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                        } else {
                            $mutuBuahMergeReg[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuBuahMergeReg[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuBuahMergeReg[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        // dd($mutuBuahMerge);
        // menimpa mutu ancak nilai default dengan nilaiyang ada
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

        $mergedData_reg = array();
        foreach ($defaultNew_reg as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mergedData_reg[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mergedData_reg[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mergedData_reg[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mergedData_reg[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        // dd($mergedData_reg);
        // //membuat data mutu ancak berdasarakan wilayah 1,2,3
        $mtancakWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mergedData as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1[$value['wil']][$key2] = array_merge($mtancakWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }

        // dd($mtancakWIltab1);
        $mtBuahWIltab1 = array();
        foreach ($queryEste as $key => $value) {
            foreach ($mutuBuahMerge as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtBuahWIltab1[$value['wil']][$key2] = array_merge($mtBuahWIltab1[$value['wil']][$key2] ?? [], $value2);
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

        ///untuk regional
        $mtTransWiltab1_reg = array();
        foreach ($queryEstereg as $key => $value) {
            foreach ($mutuAncakMergeReg as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtTransWiltab1_reg[$value['wil']][$key2] = array_merge($mtTransWiltab1_reg[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }
        $mtBuahWIltab1_reg = array();
        foreach ($queryEstereg as $key => $value) {
            foreach ($mutuBuahMergeReg as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtBuahWIltab1_reg[$value['wil']][$key2] = array_merge($mtBuahWIltab1_reg[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }
        $mtancakWIltab1_reg = array();
        foreach ($queryEstereg as $key => $value) {
            foreach ($mergedData_reg as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1_reg[$value['wil']][$key2] = array_merge($mtancakWIltab1_reg[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }



        $TranscakReg2 = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y-%m-%d") as date')
            )
            ->where('datetime', 'like', '%' . $date . '%')
            ->orderBy('datetime', 'DESC')
            ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
            ->get();
        $AncakCakReg2 = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y-%m-%d") as date')
            )
            ->where('datetime', 'like', '%' . $date . '%')
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
            foreach ($queryEstereg as $est => $estval)
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
            foreach ($queryEstereg as $est => $estval)
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
                        if ($Reg === '2') {
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

        // dd($queryEstereg);
        //perhitungan untuk mutu trans perwilaya,estate dan afd
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

                    if ($Reg == '2' || $Reg == 2) {
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

                    if ($Reg == '2' || $Reg == 2) {
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
                    if ($Reg == '2' || $Reg == 2) {
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

        // dd($mtTranstab1Wil);

        $mtTranstab1Wil_reg = array();
        foreach ($mtTransWiltab1_reg as $key => $value) if (!empty($value)) {
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
                                    $mtTranstab1Wil_reg[$key][$key1][$key2]['tph_sampleNew'] = $trans2['total_tph'];
                                    $tot_sample = $trans2['total_tph'];
                                }
                            }
                        }
                    }

                    if ($Reg == '2' || $Reg == 2) {
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

                    if ($Reg == '2' || $Reg == 2) {
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
                        $mtTranstab1Wil_reg[$key][$key1][$key2]['check_data'] = 'ada';
                    } else {
                        $mtTranstab1Wil_reg[$key][$key1][$key2]['check_data'] = "kosong";
                    }
                    // dd($transNewdata);




                    $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $mtTranstab1Wil_reg[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

                    $mtTranstab1Wil_reg[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                    //PERHITUNGAN PERESTATE
                    if ($Reg == '2' || $Reg == 2) {
                        $dataBLokEst += $tot_sample;
                    } else {
                        $dataBLokEst += $dataBLok;
                    }

                    //PERHITUNGAN PERESTATE

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

                    $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['tph_sample'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_brd'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_buah'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $mtTranstab1Wil_reg[$key][$key1][$key2]['totalSkor'] = 0;
                }

                $mtTranstab1Wil_reg[$key][$key1]['tph_sample'] = $dataBLokEst;
                $mtTranstab1Wil_reg[$key][$key1]['total_brd'] = $sum_btEst;
                $mtTranstab1Wil_reg[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                $mtTranstab1Wil_reg[$key][$key1]['total_buah'] = $sum_rstEst;
                $mtTranstab1Wil_reg[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                $mtTranstab1Wil_reg[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                $mtTranstab1Wil_reg[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                $mtTranstab1Wil_reg[$key][$key1]['totalSkor'] = $totalSkorEst;

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
                $mtTranstab1Wil_reg[$key][$key1]['tph_sample'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['total_brd'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['total_brd/TPH'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['total_buah'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['skor_brdPertph'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTranstab1Wil_reg[$key][$key1]['totalSkor'] = 0;
            }
            $mtTranstab1Wil_reg[$key]['tph_sample'] = $dataBLokWil;
            $mtTranstab1Wil_reg[$key]['total_brd'] = $sum_btWil;
            $mtTranstab1Wil_reg[$key]['total_brd/TPH'] = $brdPertphWil;
            $mtTranstab1Wil_reg[$key]['total_buah'] = $sum_rstWil;
            $mtTranstab1Wil_reg[$key]['total_buahPerTPH'] = $buahPerTPHWil;
            $mtTranstab1Wil_reg[$key]['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
            $mtTranstab1Wil_reg[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
            $mtTranstab1Wil_reg[$key]['totalSkor'] = $totalSkorWil;
        } else {
            $mtTranstab1Wil_reg[$key]['tph_sample'] = 0;
            $mtTranstab1Wil_reg[$key]['total_brd'] = 0;
            $mtTranstab1Wil_reg[$key]['total_brd/TPH'] = 0;
            $mtTranstab1Wil_reg[$key]['total_buah'] = 0;
            $mtTranstab1Wil_reg[$key]['total_buahPerTPH'] = 0;
            $mtTranstab1Wil_reg[$key]['skor_brdPertph'] = 0;
            $mtTranstab1Wil_reg[$key]['skor_buahPerTPH'] = 0;
            $mtTranstab1Wil_reg[$key]['totalSkor'] = 0;
        }
        // dd($mtTranstab1Wil_reg);
        //perhitungan untuk mutu buah wilayah,estate dan afd
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
        // dd($mtBuahtab1Wil['1']);
        $mtBuahtab1Wil_reg = array();
        foreach ($mtBuahWIltab1_reg as $key => $value) if (is_array($value)) {
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
                    $combination_counts = array();
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $combination = $value3['blok'] . ' ' . $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $jum_ha = count($listBlokPerAfd);
                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];


                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }

                    $dataBLok = count($combination_counts);
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


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['tph_baris_bloks'] = $dataBLok;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_over'] = $sum_over;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['perVcut'] = $PerVcut;

                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

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
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_mentah'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perMentah'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_masak'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perMasak'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_over'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perOver'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_abnormal'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['perAbnormal'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_vcut'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['perVcut'] = 0;

                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['jum_kr'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['total_kr'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_mentah'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_masak'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_over'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_vcut'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_abnormal'] = 0;;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['skor_kr'] = 0;
                    $mtBuahtab1Wil_reg[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
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

                $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                $mtBuahtab1Wil_reg[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                $mtBuahtab1Wil_reg[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_mentah'] = $sum_bmtEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perMentah'] = $PerMthEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_masak'] = $sum_bmkEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perMasak'] = $PerMskEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_over'] = $sum_overEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perOver'] = $PerOverEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perabnormal'] = $PerAbrest;
                $mtBuahtab1Wil_reg[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                $mtBuahtab1Wil_reg[$key][$key1]['total_vcut'] = $sum_vcutEst;
                $mtBuahtab1Wil_reg[$key][$key1]['perVcut'] = $PerVcutest;
                $mtBuahtab1Wil_reg[$key][$key1]['jum_kr'] = $sum_krEst;
                $mtBuahtab1Wil_reg[$key][$key1]['kr_blok'] = $total_krEst;

                $mtBuahtab1Wil_reg[$key][$key1]['persen_kr'] = $per_krEst;

                // skoring
                $mtBuahtab1Wil_reg[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                $mtBuahtab1Wil_reg[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                $mtBuahtab1Wil_reg[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                $mtBuahtab1Wil_reg[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                $mtBuahtab1Wil_reg[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                $mtBuahtab1Wil_reg[$key][$key1]['TOTAL_SKOR'] = $totalSkorEst;

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
                $mtBuahtab1Wil_reg[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_mentah'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perMentah'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_masak'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perMasak'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_over'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perOver'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_abnormal'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perabnormal'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['total_vcut'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['perVcut'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['jum_kr'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['kr_blok'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahtab1Wil_reg[$key][$key1]['skor_mentah'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_masak'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_over'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_vcut'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_abnormal'] = 0;;
                $mtBuahtab1Wil_reg[$key][$key1]['skor_kr'] = 0;
                $mtBuahtab1Wil_reg[$key][$key1]['TOTAL_SKOR'] = 0;
            }

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
            $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
            $mtBuahtab1Wil_reg[$key]['tph_baris_blok'] = $jum_haWil;
            $mtBuahtab1Wil_reg[$key]['sampleJJG_total'] = $sum_SamplejjgWil;
            $mtBuahtab1Wil_reg[$key]['total_mentah'] = $sum_bmtWil;
            $mtBuahtab1Wil_reg[$key]['total_perMentah'] = $PerMthWil;
            $mtBuahtab1Wil_reg[$key]['total_masak'] = $sum_bmkWil;
            $mtBuahtab1Wil_reg[$key]['total_perMasak'] = $PerMskWil;
            $mtBuahtab1Wil_reg[$key]['total_over'] = $sum_overWil;
            $mtBuahtab1Wil_reg[$key]['total_perOver'] = $PerOverWil;
            $mtBuahtab1Wil_reg[$key]['total_abnormal'] = $sum_abnorWil;
            $mtBuahtab1Wil_reg[$key]['total_perabnormal'] = $PerAbrWil;
            $mtBuahtab1Wil_reg[$key]['total_jjgKosong'] = $sum_kosongjjgWil;
            $mtBuahtab1Wil_reg[$key]['total_perKosongjjg'] = $PerkosongjjgWil;
            $mtBuahtab1Wil_reg[$key]['total_vcut'] = $sum_vcutWil;
            $mtBuahtab1Wil_reg[$key]['per_vcut'] = $PerVcutWil;
            $mtBuahtab1Wil_reg[$key]['jum_kr'] = $sum_krWil;
            $mtBuahtab1Wil_reg[$key]['kr_blok'] = $total_krWil;

            $mtBuahtab1Wil_reg[$key]['persen_kr'] = $per_krWil;

            // skoring
            $mtBuahtab1Wil_reg[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMthWil);
            $mtBuahtab1Wil_reg[$key]['skor_masak'] = skor_buah_masak_mb($PerMskWil);
            $mtBuahtab1Wil_reg[$key]['skor_over'] = skor_buah_over_mb($PerOverWil);;
            $mtBuahtab1Wil_reg[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
            $mtBuahtab1Wil_reg[$key]['skor_vcut'] = skor_vcut_mb($PerVcutWil);
            $mtBuahtab1Wil_reg[$key]['skor_kr'] = skor_abr_mb($per_krWil);
            $mtBuahtab1Wil_reg[$key]['TOTAL_SKOR'] = $totalSkorWil;
        } else {
            $mtBuahtab1Wil_reg[$key]['tph_baris_blok'] = 0;
            $mtBuahtab1Wil_reg[$key]['sampleJJG_total'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_mentah'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_perMentah'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_masak'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_perMasak'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_over'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_perOver'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_abnormal'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_perabnormal'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_jjgKosong'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_perKosongjjg'] = 0;
            $mtBuahtab1Wil_reg[$key]['total_vcut'] = 0;
            $mtBuahtab1Wil_reg[$key]['per_vcut'] = 0;
            $mtBuahtab1Wil_reg[$key]['jum_kr'] = 0;
            $mtBuahtab1Wil_reg[$key]['kr_blok'] = 0;

            $mtBuahtab1Wil_reg[$key]['persen_kr'] = 0;

            // skoring
            $mtBuahtab1Wil_reg[$key]['skor_mentah'] = 0;
            $mtBuahtab1Wil_reg[$key]['skor_masak'] = 0;
            $mtBuahtab1Wil_reg[$key]['skor_over'] = 0;
            $mtBuahtab1Wil_reg[$key]['skor_jjgKosong'] = 0;
            $mtBuahtab1Wil_reg[$key]['skor_vcut'] = 0;

            $mtBuahtab1Wil_reg[$key]['skor_kr'] = 0;
            $mtBuahtab1Wil_reg[$key]['TOTAL_SKOR'] = 0;
        }
        // dd($mtBuahtab1Wil[1]['KNE']['OD']);

        // dd($mtancakWIltab1_reg);
        $mtancaktab1Wil_reg = array();
        foreach ($mtancakWIltab1_reg as $key => $value) if (!empty($value)) {
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
                    $tod_ah = 'kosong';
                    $skor_input = 0;
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
                        $tod_ah = $value3['jenis_input'];
                        $skor_input = $value3['skor_akhir'];
                    }


                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }


                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 1);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 1);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 3);
                    } else {
                        $perPl = 0;
                    }



                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['akp_rl'] = $akp;

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['p'] = $totalP_panen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['k'] = $totalK_panen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    // $mtancaktab1Wil_reg[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                    // $mtancaktab1Wil_reg[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    // total skor akhir
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_bh'] = skor_brd_ma($brdPerjjg);
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_brd'] = skor_buah_Ma($sumPerBH);
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_ps'] = skor_palepah_ma($perPl);
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['check_input'] = $tod_ah;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_input'] = $skor_input;

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
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['pokok_sample'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['ha_sample'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['akp_rl'] =  0;

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['p'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['k'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['tgl'] = 0;

                    // $mtancaktab1Wil_reg[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhts_s'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm1'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm2'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['bhtm3'] = 0;

                    // $mtancaktab1Wil_reg[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 3);
                    // data untuk pelepah sengklek

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhi0;

                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_bh'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_brd'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_ps'] = 0;
                    $mtancaktab1Wil_reg[$key][$key1][$key2]['skor_akhir'] = 0;
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
                    $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 1);
                } else {
                    $brdPerjjgEst = 0;
                }



                // dd($sumBHEst);
                if ($sumBHEst != 0) {
                    $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 1);
                } else {
                    $sumPerBHEst = 0;
                }

                if ($pokok_panenEst != 0) {
                    $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 1);
                } else {
                    $perPlEst = 0;
                }

                $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                //PENAMPILAN UNTUK PERESTATE
                $mtancaktab1Wil_reg[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                $mtancaktab1Wil_reg[$key][$key1]['ha_sample'] =  $jum_haEst;
                $mtancaktab1Wil_reg[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                $mtancaktab1Wil_reg[$key][$key1]['akp_rl'] =  $akpEst;

                $mtancaktab1Wil_reg[$key][$key1]['p'] = $p_panenEst;
                $mtancaktab1Wil_reg[$key][$key1]['k'] = $k_panenEst;
                $mtancaktab1Wil_reg[$key][$key1]['tgl'] = $brtgl_panenEst;

                // $mtancaktab1Wil_reg[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil_reg[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                $mtancaktab1Wil_reg[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                // data untuk buah tinggal
                $mtancaktab1Wil_reg[$key][$key1]['bhts_s'] = $bhtsEST;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm1'] = $bhtm1EST;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm2'] = $bhtm2EST;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm3'] = $bhtm3EST;
                $mtancaktab1Wil_reg[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                $mtancaktab1Wil_reg[$key][$key1]['palepah_per'] = $perPlEst;
                // total skor akhir
                $mtancaktab1Wil_reg[$key][$key1]['skor_bh'] =  skor_brd_ma($brdPerjjgEst);
                $mtancaktab1Wil_reg[$key][$key1]['skor_brd'] = skor_buah_Ma($sumPerBHEst);
                $mtancaktab1Wil_reg[$key][$key1]['skor_ps'] = skor_palepah_ma($perPlEst);
                $mtancaktab1Wil_reg[$key][$key1]['skor_akhir'] = $totalSkorEst;

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
                $mtancaktab1Wil_reg[$key][$key1]['pokok_sample'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['ha_sample'] =  0;
                $mtancaktab1Wil_reg[$key][$key1]['jumlah_panen'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['akp_rl'] =  0;

                $mtancaktab1Wil_reg[$key][$key1]['p'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['k'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['tgl'] = 0;

                // $mtancaktab1Wil_reg[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtancaktab1Wil_reg[$key][$key1]['brd/jjgest'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['buah/jjg'] = 0;
                // data untuk buah tinggal
                $mtancaktab1Wil_reg[$key][$key1]['bhts_s'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm1'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm2'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['bhtm3'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhir
                $mtancaktab1Wil_reg[$key][$key1]['skor_bh'] =  0;
                $mtancaktab1Wil_reg[$key][$key1]['skor_brd'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['skor_ps'] = 0;
                $mtancaktab1Wil_reg[$key][$key1]['skor_akhir'] = 0;
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


            $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

            $mtancaktab1Wil_reg[$key]['pokok_sample'] = $pokok_panenWil;
            $mtancaktab1Wil_reg[$key]['ha_sample'] =  $jum_haWil;
            $mtancaktab1Wil_reg[$key]['jumlah_panen'] = $janjang_panenWil;
            $mtancaktab1Wil_reg[$key]['akp_rl'] =  $akpWil;

            $mtancaktab1Wil_reg[$key]['p'] = $p_panenWil;
            $mtancaktab1Wil_reg[$key]['k'] = $k_panenWil;
            $mtancaktab1Wil_reg[$key]['tgl'] = $brtgl_panenWil;

            // $mtancaktab1Wil_reg[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil_reg[$key]['brd/jjgwil'] = $brdPerwil;
            $mtancaktab1Wil_reg[$key]['buah/jjgwil'] = $sumPerBHWil;
            $mtancaktab1Wil_reg[$key]['bhts_s'] = $bhts_panenWil;
            $mtancaktab1Wil_reg[$key]['bhtm1'] = $bhtm1_panenWil;
            $mtancaktab1Wil_reg[$key]['bhtm2'] = $bhtm2_panenWil;
            $mtancaktab1Wil_reg[$key]['bhtm3'] = $bhtm3_oanenWil;
            // $mtancaktab1Wil_reg[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil_reg[$key]['palepah_pokok'] = $pelepah_swil;

            $mtancaktab1Wil_reg[$key]['palepah_per'] = $perPiWil;
            // total skor akhir
            $mtancaktab1Wil_reg[$key]['skor_bh'] = skor_brd_ma($brdPerwil);
            $mtancaktab1Wil_reg[$key]['skor_brd'] = skor_buah_Ma($sumPerBHWil);
            $mtancaktab1Wil_reg[$key]['skor_ps'] = skor_palepah_ma($perPiWil);
            $mtancaktab1Wil_reg[$key]['skor_akhir'] = $totalWil;
        } else {
            $mtancaktab1Wil_reg[$key]['pokok_sample'] = 0;
            $mtancaktab1Wil_reg[$key]['ha_sample'] =  0;
            $mtancaktab1Wil_reg[$key]['jumlah_panen'] = 0;
            $mtancaktab1Wil_reg[$key]['akp_rl'] =  0;

            $mtancaktab1Wil_reg[$key]['p'] = 0;
            $mtancaktab1Wil_reg[$key]['k'] = 0;
            $mtancaktab1Wil_reg[$key]['tgl'] = 0;

            // $mtancaktab1Wil_reg[$key]['total_brd'] = $skor_bTinggal;
            $mtancaktab1Wil_reg[$key]['brd/jjgwil'] = 0;
            $mtancaktab1Wil_reg[$key]['buah/jjgwil'] = 0;
            $mtancaktab1Wil_reg[$key]['bhts_s'] = 0;
            $mtancaktab1Wil_reg[$key]['bhtm1'] = 0;
            $mtancaktab1Wil_reg[$key]['bhtm2'] = 0;
            $mtancaktab1Wil_reg[$key]['bhtm3'] = 0;
            // $mtancaktab1Wil_reg[$key]['jjgperBuah'] = number_format($sumPerBH, 3);
            // data untuk pelepah sengklek
            $mtancaktab1Wil_reg[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $mtancaktab1Wil_reg[$key]['skor_bh'] = 0;
            $mtancaktab1Wil_reg[$key]['skor_brd'] = 0;
            $mtancaktab1Wil_reg[$key]['skor_ps'] = 0;
            $mtancaktab1Wil_reg[$key]['skor_akhir'] = 0;
        }

        // dd($mtancaktab1Wil_reg);
        //Ancak regional
        $mtancakReg = array();
        $pkok = 0;
        $ha_sample = 0;
        $panen = 0;
        $p = 0;
        $k = 0;
        $tgl = 0;
        $bhts = 0;
        $bhtm1 = 0;
        $bhtm2 = 0;
        $bhtm3 = 0;
        $palepah = 0;
        foreach ($mtancaktab1Wil_reg as $key => $value) {
            $pkok += $value['pokok_sample'];
            $ha_sample += $value['ha_sample'];
            $panen += $value['jumlah_panen'];
            $p += $value['p'];
            $k += $value['k'];
            $tgl += $value['tgl'];
            $bhts += $value['bhts_s'];
            $bhtm1 += $value['bhtm1'];
            $bhtm2 += $value['bhtm2'];
            $bhtm3 += $value['bhtm3'];
            $palepah += $value['palepah_pokok'];
        }

        //

        if ($panen == 0 || $pkok  == 0) {

            $akpWil = 0;
        } else {

            $akpWil = round(($panen / $pkok) * 100, 3);
        }

        $totalPKTwil = $p + $k + $tgl;

        if ($totalPKTwil != 0) {
            $brdPerwil = round($totalPKTwil / $panen, 3);
        } else {
            $brdPerwil = 0;
        }

        $sumBHWil = $bhts +  $bhtm1 +  $bhtm2 +  $bhtm3;

        if ($sumBHWil != 0) {
            $sumPerBHWil = round($sumBHWil / ($panen + $sumBHWil) * 100, 3);
        } else {
            $sumPerBHWil = 0;
        }


        if ($pkok != 0) {
            $perPiWil = round(($palepah / $pkok) * 100, 3);
        } else {
            $perPiWil = 0;
        }


        $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

        $mtancakReg['reg']['pokok_sample'] = $pkok;
        $mtancakReg['reg']['ha_sample'] =  $ha_sample;
        $mtancakReg['reg']['jumlah_panen'] = $panen;
        $mtancakReg['reg']['akp_rl'] =  $akpWil;

        $mtancakReg['reg']['p'] = $p;
        $mtancakReg['reg']['k'] = $k;
        $mtancakReg['reg']['tgl'] = $tgl;

        // $mtancakReg['reg']['total_brd'] = $skor_bTinggal;
        $mtancakReg['reg']['palepah_pokok'] = $palepah;
        $mtancakReg['reg']['bhts_s'] = $bhts;
        $mtancakReg['reg']['bhtm1'] = $bhtm1;
        $mtancakReg['reg']['bhtm2'] = $bhtm2;
        $mtancakReg['reg']['bhtm3'] = $bhtm3;
        $mtancakReg['reg']['brd/jjgwil'] = $brdPerwil;
        $mtancakReg['reg']['perPalepah'] = $perPiWil;
        $mtancakReg['reg']['buah/jjgwil'] = $sumPerBHWil;
        // $mtancakReg['reg']['jjgperBuah'] = number_format($sumPerBH, 3);
        // data untuk pelepah sengklek

        // total skor akhir
        $mtancakReg['reg']['skor_bh'] = skor_brd_ma($brdPerwil);
        $mtancakReg['reg']['skor_brd'] = skor_buah_Ma($sumPerBHWil);
        $mtancakReg['reg']['skor_ps'] = skor_palepah_ma($perPiWil);
        $mtancakReg['reg']['skor_akhir'] = $totalWil;

        // dd($mtBuahtab1Wil);
        //endancak regional
        //Buah Regional
        $mtBuahreg = array();
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
        $jum_ha  = 0;
        $no_Vcut = 0;
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $jum_ha += $value['tph_baris_blok'];
            $sum_bmt += $value['total_mentah'];
            $sum_bmk += $value['total_masak'];
            $sum_over += $value['total_over'];
            $sum_kosongjjg += $value['total_jjgKosong'];
            $sum_vcut += $value['total_vcut'];
            $sum_kr += $value['jum_kr'];
            $sum_Samplejjg += $value['sampleJJG_total'];
            $sum_abnor += $value['total_abnormal'];
        }
        // dd($sum_vcut);
        // $no_Vcut = $sum_Samplejjg - $sum_vcut;

        $dataBLok = $jum_ha;
        if ($sum_kr != 0) {
            $total_kr = round($sum_kr / $dataBLok, 3);
        } else {
            $total_kr = 0;
        }

        $per_kr = round($total_kr * 100, 3);
        if ($sum_bmt != 0) {
            $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
        } else {
            $PerMth = 0;
        }
        if ($sum_bmk != 0) {
            $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
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
        if ($sum_Samplejjg != 0) {
            $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
        } else {
            $PerVcut = 0;
        }
        if ($sum_abnor != 0) {
            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 3);
        } else {
            $PerAbr = 0;
        }


        $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
        $mtBuahreg['reg']['tph_baris_blok'] = $dataBLok;
        $mtBuahreg['reg']['sampleJJG_total'] = $sum_Samplejjg;
        $mtBuahreg['reg']['total_mentah'] = $sum_bmt;
        $mtBuahreg['reg']['total_perMentah'] = $PerMth;
        $mtBuahreg['reg']['total_masak'] = $sum_bmk;
        $mtBuahreg['reg']['total_perMasak'] = $PerMsk;
        $mtBuahreg['reg']['total_over'] = $sum_over;
        $mtBuahreg['reg']['total_perOver'] = $PerOver;
        $mtBuahreg['reg']['total_abnormal'] = $sum_abnor;
        $mtBuahreg['reg']['total_jjgKosong'] = $sum_kosongjjg;
        $mtBuahreg['reg']['total_perKosongjjg'] = $Perkosongjjg;
        $mtBuahreg['reg']['total_vcut'] = $sum_vcut;

        $mtBuahreg['reg']['jum_kr'] = $sum_kr;
        $mtBuahreg['reg']['total_kr'] = $total_kr;
        $mtBuahreg['reg']['persen_kr'] = $per_kr;

        // skoring
        $mtBuahreg['reg']['skor_mentah'] = skor_buah_mentah_mb($PerMth);
        $mtBuahreg['reg']['skor_masak'] = skor_buah_masak_mb($PerMsk);
        $mtBuahreg['reg']['skor_over'] = skor_buah_over_mb($PerOver);
        $mtBuahreg['reg']['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
        $mtBuahreg['reg']['skor_vcut'] = skor_vcut_mb($PerVcut);

        $mtBuahreg['reg']['skor_kr'] = skor_abr_mb($per_kr);
        $mtBuahreg['reg']['TOTAL_SKOR'] = $totalSkor;

        // dd($mtBuahreg);
        //EndBuah regional
        //mutu trans reg
        $mttransReg = array();
        $sum_bt = 0;
        $sum_rst = 0;
        $brdPertph = 0;
        $buahPerTPH = 0;
        $totalSkor = 0;
        $dataBLok = 0;
        foreach ($mtTranstab1Wil_reg as $key => $value) {
            $dataBLok += $value['tph_sample'];
            $sum_bt += $value['total_brd'];
            $sum_rst += $value['total_buah'];
        }
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


        $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

        $mttransReg['reg']['tph_sample'] = $dataBLok;
        $mttransReg['reg']['total_brd'] = $sum_bt;
        $mttransReg['reg']['total_brd/TPH'] = $brdPertph;
        $mttransReg['reg']['total_buah'] = $sum_rst;
        $mttransReg['reg']['total_buahPerTPH'] = $buahPerTPH;
        $mttransReg['reg']['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
        $mttransReg['reg']['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
        $mttransReg['reg']['totalSkor'] = $totalSkor;


        // dd($mttransReg, $mtancakReg, $mtBuahreg);

        $RekapRegTable = array();
        foreach ($mttransReg as $key => $value) {
            foreach ($mtancakReg as $key1 => $value2) {
                foreach ($mtBuahreg as $key2 => $value3) if ($key == $key1 && $key1 == $key2) {
                    $RekapRegTable[$key] = $value['totalSkor'] + $value2['skor_akhir'] + $value3['TOTAL_SKOR'];
                }
            }
        }

        // dd($RekapRegTable);
        //endMututrans reg
        // dd($mtancaktab1Wil['1']['PLE']['skor_akhir'], $mtTranstab1Wil['1']['PLE']['totalSkor'], $mtBuahtab1Wil['1']['PLE']['TOTAL_SKOR']);
        $ptmuaAncak = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun')
            )
            // ->whereBetween('mutu_ancak_new.datetime', ['2023-04-06', '2023-04-12'])
            ->where('datetime', 'like', '%' . $date . '%')
            ->whereIn('estate', ['LDE', 'SKE', 'SRE'])
            ->get();

        $ptmuaAncak = $ptmuaAncak->groupBy(['estate', 'afdeling']);
        $ptmuaAncak = json_decode($ptmuaAncak, true);

        // dd($ptmuaAncak);
        $ptMuaBuah = DB::connection('mysql2')->table('mutu_buah')
            ->select(
                "mutu_buah.*",
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun')
            )
            // ->whereBetween('mutu_buah.datetime', ['2023-04-06', '2023-04-12'])
            ->where('datetime', 'like', '%' . $date . '%')
            ->whereIn('estate', ['LDE', 'SKE', 'SRE'])
            ->get();

        $ptMuaBuah = $ptMuaBuah->groupBy(['estate', 'afdeling']);
        $ptMuaBuah = json_decode($ptMuaBuah, true);

        $ptMuaTrans = DB::connection('mysql2')->table('mutu_transport')
            ->select(
                "mutu_transport.*",
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun')
            )
            // ->whereBetween('mutu_transport.datetime', ['2023-04-06', '2023-04-12'])
            ->where('datetime', 'like', '%' . $date . '%')
            ->whereIn('estate', ['LDE', 'SKE', 'SRE'])
            ->get();

        $ptMuaTrans = $ptMuaTrans->groupBy(['estate', 'afdeling']);
        $ptMuaTrans = json_decode($ptMuaTrans, true);
        // dd($ptmuaAncak, $ptMuaTrans, $ptMuaBuah);
        //merubah jika key estate and afdeling same maka jadikan key afdeling jadi OA
        $modifiedPtmuaAncak = [];
        foreach ($ptmuaAncak as $estate => $afdelings) {
            foreach ($afdelings as $afdeling => $data) {
                if ($estate == $afdeling) {
                    $modifiedPtmuaAncak[$estate]['OA'] = $data;
                } else {
                    $modifiedPtmuaAncak[$estate][$afdeling] = $data;
                }
            }
        }

        $ptmuaAncak = $modifiedPtmuaAncak;

        $modifiedPtmuaBuah = [];
        foreach ($ptMuaBuah as $estate => $afdelings) {
            foreach ($afdelings as $afdeling => $data) {
                if ($estate == $afdeling) {
                    $modifiedPtmuaBuah[$estate]['OA'] = $data;
                } else {
                    $modifiedPtmuaBuah[$estate][$afdeling] = $data;
                }
            }
        }

        $ptmuaBuah = $modifiedPtmuaBuah;

        $modifiedPtmuaTrans = [];
        foreach ($ptMuaTrans as $estate => $afdelings) {
            foreach ($afdelings as $afdeling => $data) {
                if ($estate == $afdeling) {
                    $modifiedPtmuaTrans[$estate]['OA'] = $data;
                } else {
                    $modifiedPtmuaTrans[$estate][$afdeling] = $data;
                }
            }
        }

        $ptmuaTrans = $modifiedPtmuaTrans;


        // dd($ptmuaAncak, $ptmuaTrans, $ptmuaBuah);




        $mtAncakMua = array();
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
        foreach ($ptmuaAncak as $key => $value)  if (!empty($value)) {
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
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
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
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    // dd($value2);
                    if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                    }
                    $jum_ha = count($listBlokPerAfd);
                    $totalPokok += $value2["sample"];
                    $totalPanen +=  $value2["jjg"];
                    $totalP_panen += $value2["brtp"];
                    $totalK_panen += $value2["brtk"];
                    $totalPTgl_panen += $value2["brtgl"];

                    $totalbhts_panen += $value2["bhts"];
                    $totalbhtm1_panen += $value2["bhtm1"];
                    $totalbhtm2_panen += $value2["bhtm2"];
                    $totalbhtm3_oanen += $value2["bhtm3"];

                    $totalpelepah_s += $value2["ps"];
                }

                if ($totalPokok != 0) {
                    $akp = round(($totalPanen / $totalPokok) * 100, 1);
                } else {
                    $akp = 0;
                }


                $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                if ($totalPanen != 0) {
                    $brdPerjjg = round($skor_bTinggal / $totalPanen, 1);
                } else {
                    $brdPerjjg = 0;
                }

                $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                if ($sumBH != 0) {
                    $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 1);
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
                    $mtAncakMua[$key][$key1]['check_data'] = 'ada';
                    // $mtAncakMua[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBH);
                    // $mtAncakMua[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                } else {
                    $mtAncakMua[$key][$key1]['check_data'] = 'kosong';
                    // $mtAncakMua[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    // $mtAncakMua[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                $mtAncakMua[$key][$key1]['pokok_sample'] = $totalPokok;
                $mtAncakMua[$key][$key1]['ha_sample'] = $jum_ha;
                $mtAncakMua[$key][$key1]['jumlah_panen'] = $totalPanen;
                $mtAncakMua[$key][$key1]['akp_rl'] = $akp;

                $mtAncakMua[$key][$key1]['p'] = $totalP_panen;
                $mtAncakMua[$key][$key1]['k'] = $totalK_panen;
                $mtAncakMua[$key][$key1]['tgl'] = $totalPTgl_panen;

                $mtAncakMua[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtAncakMua[$key][$key1]['brd/jjg'] = $brdPerjjg;

                // data untuk buah tinggal
                $mtAncakMua[$key][$key1]['bhts_s'] = $totalbhts_panen;
                $mtAncakMua[$key][$key1]['bhtm1'] = $totalbhtm1_panen;
                $mtAncakMua[$key][$key1]['bhtm2'] = $totalbhtm2_panen;
                $mtAncakMua[$key][$key1]['bhtm3'] = $totalbhtm3_oanen;
                $mtAncakMua[$key][$key1]['buah/jjg'] = $sumPerBH;

                $mtAncakMua[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 3);
                // data untuk pelepah sengklek

                $mtAncakMua[$key][$key1]['palepah_pokok'] = $totalpelepah_s;
                // total skor akhir
                $mtAncakMua[$key][$key1]['skor_bh'] = skor_brd_ma($brdPerjjg);
                $mtAncakMua[$key][$key1]['skor_brd'] = skor_buah_Ma($sumPerBH);
                $mtAncakMua[$key][$key1]['skor_ps'] = skor_palepah_ma($perPl);
                $mtAncakMua[$key][$key1]['skor_akhir'] = $ttlSkorMA;

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
                $mtAncakMua[$key][$key1]['pokok_sample'] = 0;
                $mtAncakMua[$key][$key1]['ha_sample'] = 0;
                $mtAncakMua[$key][$key1]['jumlah_panen'] = 0;
                $mtAncakMua[$key][$key1]['akp_rl'] =  0;

                $mtAncakMua[$key][$key1]['p'] = 0;
                $mtAncakMua[$key][$key1]['k'] = 0;
                $mtAncakMua[$key][$key1]['tgl'] = 0;

                // $mtAncakMua[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtAncakMua[$key][$key1]['brd/jjg'] = 0;

                // data untuk buah tinggal
                $mtAncakMua[$key][$key1]['bhts_s'] = 0;
                $mtAncakMua[$key][$key1]['bhtm1'] = 0;
                $mtAncakMua[$key][$key1]['bhtm2'] = 0;
                $mtAncakMua[$key][$key1]['bhtm3'] = 0;

                // $mtAncakMua[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 3);
                // data untuk pelepah sengklek

                $mtAncakMua[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhi0;

                $mtAncakMua[$key][$key1]['skor_bh'] = 0;
                $mtAncakMua[$key][$key1]['skor_brd'] = 0;
                $mtAncakMua[$key][$key1]['skor_ps'] = 0;
                $mtAncakMua[$key][$key1]['skor_akhir'] = 0;
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
                $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 1);
            } else {
                $brdPerjjgEst = 0;
            }



            // dd($sumBHEst);
            if ($sumBHEst != 0) {
                $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 1);
            } else {
                $sumPerBHEst = 0;
            }

            if ($pokok_panenEst != 0) {
                $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 1);
            } else {
                $perPlEst = 0;
            }

            $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

            if (!empty($nonZeroValues)) {
                $mtAncakMua[$key]['check_data'] = 'ada';
                // $mtAncakMua[$key]['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBHEst);
                // $mtAncakMua[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
            } else {
                $mtAncakMua[$key]['check_data'] = 'kosong';
                // $mtAncakMua[$key]['skor_brd'] = $skor_brd = 0;
                // $mtAncakMua[$key]['skor_ps'] = $skor_ps = 0;
            }

            // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;
            $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
            //PENAMPILAN UNTUK PERESTATE
            $mtAncakMua[$key]['pokok_sample'] = $pokok_panenEst;
            $mtAncakMua[$key]['ha_sample'] =  $jum_haEst;
            $mtAncakMua[$key]['jumlah_panen'] = $janjang_panenEst;
            $mtAncakMua[$key]['akp_rl'] =  $akpEst;

            $mtAncakMua[$key]['p'] = $p_panenEst;
            $mtAncakMua[$key]['k'] = $k_panenEst;
            $mtAncakMua[$key]['tgl'] = $brtgl_panenEst;

            $mtAncakMua[$key]['total_brd'] = $skor_bTinggal;
            $mtAncakMua[$key]['brd/jjgest'] = $brdPerjjgEst;
            $mtAncakMua[$key]['buah/jjg'] = $sumPerBHEst;

            // data untuk buah tinggal
            $mtAncakMua[$key]['bhts_s'] = $bhtsEST;
            $mtAncakMua[$key]['bhtm1'] = $bhtm1EST;
            $mtAncakMua[$key]['bhtm2'] = $bhtm2EST;
            $mtAncakMua[$key]['bhtm3'] = $bhtm3EST;
            $mtAncakMua[$key]['palepah_pokok'] = $pelepah_sEST;
            $mtAncakMua[$key]['palepah_per'] = $perPlEst;
            // total skor akhir
            $mtAncakMua[$key]['skor_bh'] =  skor_brd_ma($brdPerjjgEst);
            $mtAncakMua[$key]['skor_brd'] = skor_buah_Ma($sumPerBHEst);
            $mtAncakMua[$key]['skor_ps'] = skor_palepah_ma($perPlEst);
            $mtAncakMua[$key]['skor_akhir'] = $totalSkorEst;


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
            $value[$key]['pokok_sample'] = 0;
            $value[$key]['ha_sample'] =  0;
            $value[$key]['jumlah_panen'] = 0;
            $value[$key]['akp_rl'] =  0;

            $value[$key]['p'] = 0;
            $value[$key]['k'] = 0;
            $value[$key]['tgl'] = 0;

            // $value[$key]['total_brd'] = $skor_bTinggal;
            $value[$key]['brd/jjgest'] = 0;
            $value[$key]['buah/jjg'] = 0;
            // data untuk buah tinggal
            $value[$key]['bhts_s'] = 0;
            $value[$key]['bhtm1'] = 0;
            $value[$key]['bhtm2'] = 0;
            $value[$key]['bhtm3'] = 0;
            $value[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $value[$key]['skor_bh'] =  0;
            $value[$key]['skor_brd'] = 0;
            $value[$key]['skor_ps'] = 0;
            $value[$key]['skor_akhir'] = 0;
        }
        $totalPKTwil = $p_panenWil + $k_panenWil + $brtgl_panenWil;
        $sumBHWil = $bhts_panenWil +  $bhtm1_panenWil +  $bhtm2_panenWil +  $bhtm3_oanenWil;

        if ($janjang_panenWil == 0 || $pokok_panenWil == 0) {
            $akpWil = 0;
        } else {

            $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 3);
        }

        if ($totalPKTwil != 0) {
            $brdPerwil = round($totalPKTwil / $janjang_panenWil, 1);
        } else {
            $brdPerwil = 0;
        }

        // dd($sumBHEst);
        if ($sumBHWil != 0) {
            $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 1);
        } else {
            $sumPerBHWil = 0;
        }

        if ($pokok_panenWil != 0) {
            $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 1);
        } else {
            $perPiWil = 0;
        }


        $nonZeroValuesAncak = array_filter([$p_panenWil, $k_panenWil, $brtgl_panenWil, $bhts_panenWil, $bhtm1_panenWil, $bhtm2_panenWil, $bhtm3_oanenWil]);

        if (!empty($nonZeroValuesAncak)) {
            $mtAncakMua['check_data'] = 'ada';
            // $mtAncakMua['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBHEst);
            // $mtAncakMua['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
        } else {
            $mtAncakMua['check_data'] = 'kosong';
            // $mtAncakMua['skor_brd'] = $skor_brd = 0;
            // $mtAncakMua['skor_ps'] = $skor_ps = 0;
        }

        // $totalWil = $skor_bh + $skor_brd + $skor_ps;

        $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

        $mtAncakMua['pokok_sample'] = $pokok_panenWil;
        $mtAncakMua['ha_sample'] =  $jum_haWil;
        $mtAncakMua['jumlah_panen'] = $janjang_panenWil;
        $mtAncakMua['akp_rl'] =  $akpWil;

        $mtAncakMua['p'] = $p_panenWil;
        $mtAncakMua['k'] = $k_panenWil;
        $mtAncakMua['tgl'] = $brtgl_panenWil;

        $mtAncakMua['total_brd'] = $skor_bTinggal;
        $mtAncakMua['brd/jjgwil'] = $brdPerwil;
        $mtAncakMua['buah/jjgwil'] = $sumPerBHWil;
        $mtAncakMua['bhts_s'] = $bhts_panenWil;
        $mtAncakMua['bhtm1'] = $bhtm1_panenWil;
        $mtAncakMua['bhtm2'] = $bhtm2_panenWil;
        $mtAncakMua['bhtm3'] = $bhtm3_oanenWil;
        $mtAncakMua['jjgperBuah'] = number_format($sumPerBH, 3);
        // data untuk pelepah sengklek
        $mtAncakMua['palepah_pokok'] = $pelepah_swil;

        $mtAncakMua['palepah_per'] = $perPiWil;
        // total skor akhir
        $mtAncakMua['skor_bh'] = skor_brd_ma($brdPerwil);
        $mtAncakMua['skor_brd'] = skor_buah_Ma($sumPerBHWil);
        $mtAncakMua['skor_ps'] = skor_palepah_ma($perPiWil);
        $mtAncakMua['skor_akhir'] = $totalWil;
        // dd($mtAncakMua);
        // const sum_pokok_sample = array["SKE"]["pokok_sample"] + array["LDE"]["pokok_sample"];

        $mtBuahMua = array();
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
        foreach ($ptmuaBuah as $key => $value) if (is_array($value)) {
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
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
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
                $combination_counts = array();
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $combination = $value2['blok'] . ' ' . $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['tph_baris'];
                    if (!isset($combination_counts[$combination])) {
                        $combination_counts[$combination] = 0;
                    }
                    $jum_ha = count($listBlokPerAfd);
                    $sum_bmt += $value2['bmt'];
                    $sum_bmk += $value2['bmk'];
                    $sum_over += $value2['overripe'];
                    $sum_kosongjjg += $value2['empty_bunch'];
                    $sum_vcut += $value2['vcut'];
                    $sum_kr += $value2['alas_br'];


                    $sum_Samplejjg += $value2['jumlah_jjg'];
                    $sum_abnor += $value2['abnormal'];
                }

                $dataBLok = count($combination_counts);
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
                $nonZeroValues = array_filter([$sum_Samplejjg, $PerMth, $PerMsk, $PerOver, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

                if (!empty($nonZeroValues)) {
                    $mtBuahMua[$key][$key1]['check_data'] = 'ada';
                    // $mtBuahMua[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                    // $mtBuahMua[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                    // $mtBuahMua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                    // $mtBuahMua[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                    // $mtBuahMua[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                } else {
                    $mtBuahMua[$key][$key1]['check_data'] = 'kosong';
                    // $mtBuahMua[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    // $mtBuahMua[$key][$key1]['skor_over'] = $skor_over = 0;
                    // $mtBuahMua[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    // $mtBuahMua[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    // $mtBuahMua[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                $mtBuahMua[$key][$key1]['tph_baris_bloks'] = $dataBLok;
                $mtBuahMua[$key][$key1]['sampleJJG_total'] = $sum_Samplejjg;
                $mtBuahMua[$key][$key1]['total_mentah'] = $jml_mth;
                $mtBuahMua[$key][$key1]['total_perMentah'] = $PerMth;
                $mtBuahMua[$key][$key1]['total_masak'] = $jml_mtg;
                $mtBuahMua[$key][$key1]['total_perMasak'] = $PerMsk;
                $mtBuahMua[$key][$key1]['total_over'] = $sum_over;
                $mtBuahMua[$key][$key1]['total_perOver'] = $PerOver;
                $mtBuahMua[$key][$key1]['total_abnormal'] = $sum_abnor;
                $mtBuahMua[$key][$key1]['perAbnormal'] = $PerAbr;
                $mtBuahMua[$key][$key1]['total_jjgKosong'] = $sum_kosongjjg;
                $mtBuahMua[$key][$key1]['total_perKosongjjg'] = $Perkosongjjg;
                $mtBuahMua[$key][$key1]['total_vcut'] = $sum_vcut;
                $mtBuahMua[$key][$key1]['perVcut'] = $PerVcut;

                $mtBuahMua[$key][$key1]['jum_kr'] = $sum_kr;
                $mtBuahMua[$key][$key1]['total_kr'] = $total_kr;
                $mtBuahMua[$key][$key1]['persen_kr'] = $per_kr;

                // skoring
                $mtBuahMua[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                $mtBuahMua[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                $mtBuahMua[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);
                $mtBuahMua[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                $mtBuahMua[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);
                $mtBuahMua[$key][$key1]['skor_kr'] = skor_abr_mb($per_kr);

                $mtBuahMua[$key][$key1]['TOTAL_SKOR'] = $totalSkor;

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
                $mtBuahMua[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahMua[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahMua[$key][$key1]['total_mentah'] = 0;
                $mtBuahMua[$key][$key1]['total_perMentah'] = 0;
                $mtBuahMua[$key][$key1]['total_masak'] = 0;
                $mtBuahMua[$key][$key1]['total_perMasak'] = 0;
                $mtBuahMua[$key][$key1]['total_over'] = 0;
                $mtBuahMua[$key][$key1]['total_perOver'] = 0;
                $mtBuahMua[$key][$key1]['total_abnormal'] = 0;
                $mtBuahMua[$key][$key1]['perAbnormal'] = 0;
                $mtBuahMua[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahMua[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahMua[$key][$key1]['total_vcut'] = 0;
                $mtBuahMua[$key][$key1]['perVcut'] = 0;

                $mtBuahMua[$key][$key1]['jum_kr'] = 0;
                $mtBuahMua[$key][$key1]['total_kr'] = 0;
                $mtBuahMua[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahMua[$key][$key1]['skor_mentah'] = 0;
                $mtBuahMua[$key][$key1]['skor_masak'] = 0;
                $mtBuahMua[$key][$key1]['skor_over'] = 0;
                $mtBuahMua[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahMua[$key][$key1]['skor_vcut'] = 0;
                $mtBuahMua[$key][$key1]['skor_abnormal'] = 0;;
                $mtBuahMua[$key][$key1]['skor_kr'] = 0;
                $mtBuahMua[$key][$key1]['TOTAL_SKOR'] = 0;
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

            $nonZeroValues = array_filter([$sum_SamplejjgEst, $PerMthEst, $PerMskEst, $PerOverEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

            if (!empty($nonZeroValues)) {
                $mtBuahMua[$key]['check_data'] = 'ada';
                // $mtBuahMua[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                // $mtBuahMua[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                // $mtBuahMua[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                // $mtBuahMua[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                // $mtBuahMua[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
            } else {
                $mtBuahMua[$key]['check_data'] = 'kosong';
                // $mtBuahMua[$key]['skor_masak'] = $skor_masak = 0;
                // $mtBuahMua[$key]['skor_over'] = $skor_over = 0;
                // $mtBuahMua[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                // $mtBuahMua[$key]['skor_vcut'] = $skor_vcut =  0;
                // $mtBuahMua[$key]['skor_kr'] = $skor_kr = 0;
            }

            // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
            $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
            $mtBuahMua[$key]['tph_baris_blok'] = $jum_haEst;
            $mtBuahMua[$key]['sampleJJG_total'] = $sum_SamplejjgEst;
            $mtBuahMua[$key]['total_mentah'] = $sum_bmtEst;
            $mtBuahMua[$key]['total_perMentah'] = $PerMthEst;
            $mtBuahMua[$key]['total_masak'] = $sum_bmkEst;
            $mtBuahMua[$key]['total_perMasak'] = $PerMskEst;
            $mtBuahMua[$key]['total_over'] = $sum_overEst;
            $mtBuahMua[$key]['total_perOver'] = $PerOverEst;
            $mtBuahMua[$key]['total_abnormal'] = $sum_abnorEst;
            $mtBuahMua[$key]['total_perabnormal'] = $PerAbrest;
            $mtBuahMua[$key]['total_jjgKosong'] = $sum_kosongjjgEst;
            $mtBuahMua[$key]['total_perKosongjjg'] = $PerkosongjjgEst;
            $mtBuahMua[$key]['total_vcut'] = $sum_vcutEst;
            $mtBuahMua[$key]['perVcut'] = $PerVcutest;
            $mtBuahMua[$key]['jum_kr'] = $sum_krEst;
            $mtBuahMua[$key]['kr_blok'] = $total_krEst;

            $mtBuahMua[$key]['persen_kr'] = $per_krEst;

            // skoring
            $mtBuahMua[$key]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
            $mtBuahMua[$key]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
            $mtBuahMua[$key]['skor_over'] = skor_buah_over_mb($PerOverEst);;
            $mtBuahMua[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
            $mtBuahMua[$key]['skor_vcut'] = skor_vcut_mb($PerVcutest);
            $mtBuahMua[$key]['skor_kr'] = skor_abr_mb($per_krEst);
            $mtBuahMua[$key]['TOTAL_SKOR'] = $totalSkorEst;

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
            $mtBuahMua[$key]['tph_baris_blok'] = 0;
            $mtBuahMua[$key]['sampleJJG_total'] = 0;
            $mtBuahMua[$key]['total_mentah'] = 0;
            $mtBuahMua[$key]['total_perMentah'] = 0;
            $mtBuahMua[$key]['total_masak'] = 0;
            $mtBuahMua[$key]['total_perMasak'] = 0;
            $mtBuahMua[$key]['total_over'] = 0;
            $mtBuahMua[$key]['total_perOver'] = 0;
            $mtBuahMua[$key]['total_abnormal'] = 0;
            $mtBuahMua[$key]['total_perabnormal'] = 0;
            $mtBuahMua[$key]['total_jjgKosong'] = 0;
            $mtBuahMua[$key]['total_perKosongjjg'] = 0;
            $mtBuahMua[$key]['total_vcut'] = 0;
            $mtBuahMua[$key]['perVcut'] = 0;
            $mtBuahMua[$key]['jum_kr'] = 0;
            $mtBuahMua[$key]['kr_blok'] = 0;
            $mtBuahMua[$key]['persen_kr'] = 0;

            // skoring
            $mtBuahMua[$key]['skor_mentah'] = 0;
            $mtBuahMua[$key]['skor_masak'] = 0;
            $mtBuahMua[$key]['skor_over'] = 0;
            $mtBuahMua[$key]['skor_jjgKosong'] = 0;
            $mtBuahMua[$key]['skor_vcut'] = 0;
            $mtBuahMua[$key]['skor_abnormal'] = 0;;
            $mtBuahMua[$key]['skor_kr'] = 0;
            $mtBuahMua[$key]['TOTAL_SKOR'] = 0;
        }

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


        $nonZeroValuesBuah = array_filter([$sum_SamplejjgWil, $PerMthWil, $PerMskWil, $PerOverWil, $sum_abnorWil, $sum_kosongjjgWil, $sum_vcutWil]);

        if (!empty($nonZeroValuesBuah)) {
            $mtBuahMua['check_data'] = 'ada';
            // $mtBuahMua['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskWil);
            // $mtBuahMua['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverWil);
            // $mtBuahMua['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgWil);
            // $mtBuahMua['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutWil);
            // $mtBuahMua['skor_kr'] = $skor_kr =  skor_abr_mb($per_krWil);
        } else {
            $mtBuahMua['check_data'] = 'kosong';
            // $mtBuahMua['skor_masak'] = $skor_masak = 0;
            // $mtBuahMua['skor_over'] = $skor_over = 0;
            // $mtBuahMua['skor_jjgKosong'] = $skor_jjgKosong = 0;
            // $mtBuahMua['skor_vcut'] = $skor_vcut =  0;
            // $mtBuahMua['skor_kr'] = $skor_kr = 0;
        }

        // $totalSkorWil = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
        $totalSkorWil =  skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
        $mtBuahMua['tph_baris_blok'] = $jum_haWil;
        $mtBuahMua['sampleJJG_total'] = $sum_SamplejjgWil;
        $mtBuahMua['total_mentah'] = $sum_bmtWil;
        $mtBuahMua['total_perMentah'] = $PerMthWil;
        $mtBuahMua['total_masak'] = $sum_bmkWil;
        $mtBuahMua['total_perMasak'] = $PerMskWil;
        $mtBuahMua['total_over'] = $sum_overWil;
        $mtBuahMua['total_perOver'] = $PerOverWil;
        $mtBuahMua['total_abnormal'] = $sum_abnorWil;
        $mtBuahMua['total_perabnormal'] = $PerAbrWil;
        $mtBuahMua['total_jjgKosong'] = $sum_kosongjjgWil;
        $mtBuahMua['total_perKosongjjg'] = $PerkosongjjgWil;
        $mtBuahMua['total_vcut'] = $sum_vcutWil;
        $mtBuahMua['per_vcut'] = $PerVcutWil;
        $mtBuahMua['jum_kr'] = $sum_krWil;
        $mtBuahMua['kr_blok'] = $total_krWil;

        $mtBuahMua['persen_kr'] = $per_krWil;

        // skoring
        $mtBuahMua['skor_mentah'] = skor_buah_mentah_mb($PerMthWil);
        $mtBuahMua['skor_masak'] = skor_buah_masak_mb($PerMskWil);
        $mtBuahMua['skor_over'] = skor_buah_over_mb($PerOverWil);;
        $mtBuahMua['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
        $mtBuahMua['skor_vcut'] = skor_vcut_mb($PerVcutWil);
        $mtBuahMua['skor_kr'] = skor_abr_mb($per_krWil);
        $mtBuahMua['TOTAL_SKOR'] = $totalSkorWil;

        // dd($mtBuahMua);
        $mtTransMua = array();
        $dataBLokWil = 0;
        $sum_btWil = 0;
        $sum_rstWil = 0;
        foreach ($ptmuaTrans as $key => $value) if (!empty($value)) {
            $dataBLokEst = 0;
            $sum_btEst = 0;
            $sum_rstEst = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $sum_bt = 0;
                $sum_rst = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                $dataBLok = 0;
                $listBlokPerAfd = array();
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {

                    // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                    // }
                    $dataBLok = count($listBlokPerAfd);
                    $sum_bt += $value2['bt'];
                    $sum_rst += $value2['rst'];
                }

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


                $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                if (!empty($nonZeroValues)) {
                    $mtTransMua[$key][$key1]['check_data'] = 'ada';
                    // $mtTransMua[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                } else {
                    $mtTransMua[$key][$key1]['check_data'] = 'kosong';
                    // $mtTransMua[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                // $totalSkor = $skor_brd + $skor_buah ;

                $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                $mtTransMua[$key][$key1]['tph_sample'] = $dataBLok;
                $mtTransMua[$key][$key1]['total_brd'] = $sum_bt;
                $mtTransMua[$key][$key1]['total_brd/TPH'] = $brdPertph;
                $mtTransMua[$key][$key1]['total_buah'] = $sum_rst;
                $mtTransMua[$key][$key1]['total_buahPerTPH'] = $buahPerTPH;
                $mtTransMua[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                $mtTransMua[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                $mtTransMua[$key][$key1]['totalSkor'] = $totalSkor;

                //PERHITUNGAN PERESTATE
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

                // $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);


            } else {
                $mtTransMua[$key][$key1]['tph_sample'] = 0;
                $mtTransMua[$key][$key1]['total_brd'] = 0;
                $mtTransMua[$key][$key1]['total_brd/TPH'] = 0;
                $mtTransMua[$key][$key1]['total_buah'] = 0;
                $mtTransMua[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTransMua[$key][$key1]['skor_brdPertph'] = 0;
                $mtTransMua[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTransMua[$key][$key1]['totalSkor'] = 0;
            }

            $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

            if (!empty($nonZeroValues)) {
                $mtTransMua[$key]['check_data'] = 'ada';
                // $mtTransMua[$key]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
            } else {
                $mtTransMua[$key]['check_data'] = 'kosong';
                // $mtTransMua[$key]['skor_buahPerTPH'] = $skor_buah = 0;
            }

            // $totalSkorEst = $skor_brd + $skor_buah ;
            $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);

            $mtTransMua[$key]['tph_sample'] = $dataBLokEst;
            $mtTransMua[$key]['total_brd'] = $sum_btEst;
            $mtTransMua[$key]['total_brd/TPH'] = $brdPertphEst;
            $mtTransMua[$key]['total_buah'] = $sum_rstEst;
            $mtTransMua[$key]['total_buahPerTPH'] = $buahPerTPHEst;
            $mtTransMua[$key]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
            $mtTransMua[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
            $mtTransMua[$key]['totalSkor'] = $totalSkorEst;

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
            $mtTransMua[$key]['tph_sample'] = 0;
            $mtTransMua[$key]['total_brd'] = 0;
            $mtTransMua[$key]['total_brd/TPH'] = 0;
            $mtTransMua[$key]['total_buah'] = 0;
            $mtTransMua[$key]['total_buahPerTPH'] = 0;
            $mtTransMua[$key]['skor_brdPertph'] = 0;
            $mtTransMua[$key]['skor_buahPerTPH'] = 0;
            $mtTransMua[$key]['totalSkor'] = 0;
        }

        $nonZeroValuesTrans = array_filter([$sum_btWil, $sum_rstWil]);

        // if (!empty($nonZeroValuesTrans)) {
        //     $mtTransMua[$key]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertphWil);
        //     $mtTransMua[$key]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHWil);
        // } else {
        //     $mtTransMua[$key]['skor_brdPertph'] = $skor_brd = 0;
        //     $mtTransMua[$key]['skor_buahPerTPH'] = $skor_buah = 0;
        // }

        // $totalSkorWil = $skor_brd + $skor_buah ;
        $mtTransMua['tph_sample'] = $dataBLokWil;
        $mtTransMua['total_brd'] = $sum_btWil;
        $mtTransMua['total_brd/TPH'] = $brdPertphWil;
        $mtTransMua['total_buah'] = $sum_rstWil;
        $mtTransMua['total_buahPerTPH'] = $buahPerTPHWil;
        $mtTransMua['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
        $mtTransMua['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
        $mtTransMua['totalSkor'] = $totalSkorWil;

        $mtBuahMuaTotalSkor = $mtBuahMua['TOTAL_SKOR'];
        $mtAncakMuaSkorAkhir = $mtAncakMua['skor_akhir'];
        $mtTransMuaTotalSkor = $mtTransMua['totalSkor'];

        $sumOfAllScores = $mtBuahMuaTotalSkor + $mtAncakMuaSkorAkhir + $mtTransMuaTotalSkor;
        // dd($mtTranstab1Wil[5]['BTE']);

        //menggabunugkan smua total skor di mutu ancak transport dan buah jadi satu array
        $RekapWIlTabel = array();
        // dd($mtancaktab1Wil[4]['MRE'], $mtTranstab1Wil[4]['MRE'],$mtBuahtab1Wil[4]['MRE']);
        // dd($mtancaktab1Wil[3]['LDE']);

        // dd($mtancaktab1Wil[3], $mtBuahtab1Wil[3], $mtTranstab1Wil[3]);
        if ($Reg == 1 || $Reg == '1') {
            $newmua_ancak = [
                'Ancak' => [
                    'pokok_sample' => $mtancaktab1Wil[3]['LDE']['pokok_sample'] + $mtancaktab1Wil[3]['SRE']['pokok_sample'] + $mtancaktab1Wil[3]['SKE']['pokok_sample'],
                    'ha_sample' => $mtancaktab1Wil[3]['LDE']['ha_sample'] + $mtancaktab1Wil[3]['SRE']['ha_sample'] + $mtancaktab1Wil[3]['SKE']['ha_sample'],
                    'jumlah_panen' => $mtancaktab1Wil[3]['LDE']['jumlah_panen'] + $mtancaktab1Wil[3]['SRE']['jumlah_panen'] + $mtancaktab1Wil[3]['SKE']['jumlah_panen'],
                    'p' => $mtancaktab1Wil[3]['LDE']['p'] + $mtancaktab1Wil[3]['SRE']['p'] + $mtancaktab1Wil[3]['SKE']['p'],
                    'k' => $mtancaktab1Wil[3]['LDE']['k'] + $mtancaktab1Wil[3]['SRE']['k'] + $mtancaktab1Wil[3]['SKE']['k'],
                    'tgl' => $mtancaktab1Wil[3]['LDE']['tgl'] + $mtancaktab1Wil[3]['SRE']['tgl'] + $mtancaktab1Wil[3]['SKE']['tgl'],
                    'bhts_s' => $mtancaktab1Wil[3]['LDE']['bhts_s'] + $mtancaktab1Wil[3]['SRE']['bhts_s'] + $mtancaktab1Wil[3]['SKE']['bhts_s'],
                    'bhtm1' => $mtancaktab1Wil[3]['LDE']['bhtm1'] + $mtancaktab1Wil[3]['SRE']['bhtm1'] + $mtancaktab1Wil[3]['SKE']['bhtm1'],
                    'bhtm2' => $mtancaktab1Wil[3]['LDE']['bhtm2'] + $mtancaktab1Wil[3]['SRE']['bhtm2'] + $mtancaktab1Wil[3]['SKE']['bhtm2'],
                    'bhtm3' => $mtancaktab1Wil[3]['LDE']['bhtm3'] + $mtancaktab1Wil[3]['SRE']['bhtm3'] + $mtancaktab1Wil[3]['SKE']['bhtm3'],
                    'palepah_pokok' => $mtancaktab1Wil[3]['LDE']['palepah_pokok'] + $mtancaktab1Wil[3]['SRE']['OA']['palepah_pokok'] + $mtancaktab1Wil[3]['SKE']['OA']['palepah_pokok'],
                ]
            ];
            $newmua_buah = [
                'Buah' => [
                    'tph_baris_blok' => $mtBuahtab1Wil[3]['LDE']['tph_baris_blok'] + $mtBuahtab1Wil[3]['SRE']['tph_baris_blok'] + $mtBuahtab1Wil[3]['SKE']['tph_baris_blok'],
                    'sampleJJG_total' => $mtBuahtab1Wil[3]['LDE']['sampleJJG_total'] + $mtBuahtab1Wil[3]['SRE']['sampleJJG_total'] + $mtBuahtab1Wil[3]['SKE']['sampleJJG_total'],
                    'total_mentah' => $mtBuahtab1Wil[3]['LDE']['total_mentah'] + $mtBuahtab1Wil[3]['SRE']['total_mentah'] + $mtBuahtab1Wil[3]['SKE']['total_mentah'],
                    'total_masak' => $mtBuahtab1Wil[3]['LDE']['total_masak'] + $mtBuahtab1Wil[3]['SRE']['total_masak'] + $mtBuahtab1Wil[3]['SKE']['total_masak'],
                    'total_over' => $mtBuahtab1Wil[3]['LDE']['total_over'] + $mtBuahtab1Wil[3]['SRE']['total_over'] + $mtBuahtab1Wil[3]['SKE']['total_over'],
                    'total_abnormal' => $mtBuahtab1Wil[3]['LDE']['total_abnormal'] + $mtBuahtab1Wil[3]['SRE']['total_abnormal'] + $mtBuahtab1Wil[3]['SKE']['total_abnormal'],
                    'total_jjgKosong' => $mtBuahtab1Wil[3]['LDE']['total_jjgKosong'] + $mtBuahtab1Wil[3]['SRE']['total_jjgKosong'] + $mtBuahtab1Wil[3]['SKE']['total_jjgKosong'],
                    'total_vcut' => $mtBuahtab1Wil[3]['LDE']['total_vcut'] + $mtBuahtab1Wil[3]['SRE']['total_vcut'] + $mtBuahtab1Wil[3]['SKE']['total_vcut'],
                    'jum_kr' => $mtBuahtab1Wil[3]['LDE']['jum_kr'] + $mtBuahtab1Wil[3]['SRE']['jum_kr'] + $mtBuahtab1Wil[3]['SKE']['jum_kr'],
                ]
            ];
            $newmua_trans = [
                'Trans' => [
                    'tph_sample' => $mtTranstab1Wil[3]['LDE']['tph_sample'] + $mtTranstab1Wil[3]['SRE']['tph_sample'] + $mtTranstab1Wil[3]['SKE']['tph_sample'],
                    'total_brd' => $mtTranstab1Wil[3]['LDE']['total_brd'] + $mtTranstab1Wil[3]['SRE']['total_brd'] + $mtTranstab1Wil[3]['SKE']['total_brd'],
                    'total_buah' => $mtTranstab1Wil[3]['LDE']['total_buah'] + $mtTranstab1Wil[3]['SRE']['total_buah'] + $mtTranstab1Wil[3]['SKE']['total_buah'],
                ]
            ];


            $muacak = [];
            foreach ($newmua_ancak as $key => $value) {
                # code...
                // dd($value['bhts_s']);
                $sumBH = $value['bhts_s'] +  $value['bhtm1'] +  $value['bhtm2'] +  $value['bhtm3'];
                if ($sumBH != 0) {
                    $sumPerBH = round($sumBH / ($value['jumlah_panen'] + $sumBH) * 100, 3);
                } else {
                    $sumPerBH = 0;
                }



                $skor_bTinggal = $value['p'] + $value['k'] + $value['tgl'];

                if ($totalPanen != 0) {
                    $brdPerjjg = round($skor_bTinggal / $value['jumlah_panen'], 3);
                } else {
                    $brdPerjjg = 0;
                }



                if ($totalpelepah_s != 0) {
                    $perPl = round(($value['palepah_pokok'] / $value['pokok_sample']) * 100, 3);
                } else {
                    $perPl = 0;
                }
                $skor_bh = skor_buah_Ma($sumPerBH);
                $skor_brd = skor_brd_ma($brdPerjjg);
                $skor_ps = skor_palepah_ma($perPl);
                $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                $muacak[$key]['Skorancak'] = $ttlSkorMA;
                $muacak[$key]['skorbuah'] = $skor_bh;
                $muacak[$key]['skorbrd'] = $skor_brd;
                $muacak[$key]['skorps'] = $skor_ps;
            }

            $muatrans = [];
            foreach ($newmua_trans as $key => $value) {
                $dataBLok += $value['tph_sample'];
                $sum_bt += $value['total_brd'];
                $sum_rst += $value['total_buah'];

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

                $skor_bh = skor_brd_tinggal($brdPertphEst);
                $skor_brd = skor_buah_tinggal($buahPerTPH);

                $ttlSkorMA = $skor_bh + $skor_brd;
                $muatrans[$key]['skorTrans'] = $ttlSkorMA;
                $muatrans[$key]['skorbuah'] = $skor_bh;
                $muatrans[$key]['skorbrd'] = $skor_brd;
            }
            $muabuah = [];
            foreach ($newmua_buah as $key => $value) {

                $jml_mth = $value['total_mentah'];
                $jml_mtg = $value['total_masak'];
                $sum_Samplejjg = $value['sampleJJG_total'];
                $sum_abnor =  $value['total_abnormal'];
                $sum_over =  $value['total_over'];
                $sum_vcut =  $value['total_vcut'];
                $sum_kosongjjg =  $value['total_jjgKosong'];
                $sum_kr =  $value['jum_kr'];
                $dataBLok =  $value['tph_baris_blok'];
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
                if ($sum_vcut != 0) {
                    $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 3);
                } else {
                    $PerVcut = 0;
                }
                if ($sum_kosongjjg != 0) {
                    $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 3);
                } else {
                    $Perkosongjjg = 0;
                }
                if ($sum_kr != 0) {
                    $total_kr = round($sum_kr / $dataBLok, 3);
                } else {
                    $total_kr = 0;
                }

                $per_kr = round($total_kr * 100, 3);



                $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                $muabuah[$key]['skorbuah'] = $totalSkor;
            }
            // dd($muabuah);
            $totalestatemua = $muabuah['Buah']['skorbuah'] + $muatrans['Trans']['skorTrans'] + $muacak['Ancak']['Skorancak'];

            $newmua = [
                'ancak' => 0,
                'buah' => 0,
                'trans' => 0,
                'totalestmua' => $totalestatemua
            ];
        } else {
            $newmua = [
                'ancak' => 0,
                'buah' => 0,
                'trans' => 0,
                'totalestmua' => 0
            ];
        }
        // dd($newmua_ancak, $newmua_buah, $newmua_trans, $muacak, $muabuah, $muatrans,);



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



                                                // if ($key1 === 'SKE' || $key1 === 'LDE' || $key1 === 'SRE') {
                                                //     unset($RekapWIlTabel[$key][$key1]['OA']['TotalSkorEST']);
                                                // }
                                            }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        // dd($mtBuahMua, $mtAncakMua, $mtTransMua, $RekapWIlTabel);
        // dd($RekapWIlTabel);

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
        $RankingFinal = $RekapWIlTabel;

        $sortedArray = [];

        foreach ($RankingFinal as $key => $value) {
            $sortedArray[$key] = $value['TotalSkorWil'];
        }

        arsort($sortedArray);

        $rank = 1;
        foreach ($sortedArray as $key => $value) {
            $RankingFinal[$key]['rankWil'] = $rank++;
        }





        // Update key name from "KTE4" to "KTE" recursively
        // updateKeyRecursive($RankingFinal, "KTE4", "KTE");

        // Output the updated array
        // dd($RankingFinal);

        // dd($RankingFinal);


        $Wil1 = $RankingFinal[1] ?? $RankingFinal[4] ?? $RankingFinal[7] ?? $RankingFinal[10];
        $Wil2 = $RankingFinal[2] ?? $RankingFinal[5] ?? $RankingFinal[8] ?? $RankingFinal[11];
        $Wil3 = $RankingFinal[3] ?? $RankingFinal[6] ?? $RankingFinal[8] ?? $RankingFinal[11];


        // dd($Wil3);

        //buat tabel plasma 

        // dd($RankingFinal[1] && [2]);
        $GmWil1['skorwil_1'] = $Wil1['TotalSkorWil'];
        $GmWil2['skorwil_2'] = $Wil2['TotalSkorWil'];
        $GmWil3['skorwil_3'] = $Wil3['TotalSkorWil'];
        // foreach ($Wil1 as $key => $value) {
        // dd($GMperwil1);
        // penambahan GM dan WIlayah untuk di ambil namanya
        function processWil($Wil, $estValues)
        {
            $processedWil = array();
            $processedWil['Skor'] = $Wil['TotalSkorWil'];
            $processedWil['key'] = array_diff(array_keys($Wil), ['TotalSkorWil', 'rankWil']);
            $processedWil['est'] = '';
            $processedWil['afd'] = 'GM';


            //cek jika ada kunci yang sama
            $key_counts = array_count_values(array_keys($Wil));
            foreach ($key_counts as $key => $count) {
                if ($count > 1) {
                    //jika kunci ada buat array baru
                    $new_array = [
                        'Skor' => $Wil['TotalSkorWil'],
                        'key' => [$key],
                        'est' => '',
                        'afd' => 'GM',
                    ];
                    //tamhahkan array ke dalam procesedwil
                    $processedWil[] = $new_array;
                }
            }
            // dd($processedWil);
            //tambah key est bedasarkan key value
            if (array_key_exists($processedWil['key'][0], $estValues)) {
                $processedWil['est'] = $estValues[$processedWil['key'][0]];
            }

            return $processedWil;
        }

        $estValues = [
            'KNE' => 'WIL-I',
            'MRE' => 'WIL-IV',
            'BDE' => 'WIL-VII',
            'BKE' => 'WIL-II',
            'BTE' => 'WIL-V',
            'BHE' => 'WIL-VIII',
            'BGE' => 'WIL-III',
            'MLE' => 'WIL-VI',
            'SJE' => 'WIL-IX',
            'LM1' => 'WIL-X',
        ];

        $WilGMsatu = processWil($Wil1, $estValues);
        $wilGM2 = processWil($Wil2, $estValues);
        $wilGM3 = processWil($Wil3, $estValues);


        $GMarr = array();

        $GMarr['0'] = $WilGMsatu;
        $GMarr['1'] = $wilGM2;
        $GMarr['2'] = $wilGM3;


        //mencocokan afd dan est yang di ubah menjadi wil untuk mendapatkan nama
        $RHGM = array();
        foreach ($GMarr as $key => $value) if (is_array($value)) {
            // dd($key);
            // dd($value);
            $est = $value['est'];
            $skor = $value['Skor'];
            $EM = 'GM';
            $nama = '-';
            foreach ($queryAsisten as $value4) {
                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                    $nama = $value4['nama'];
                    break;
                }
            }

            $RHGM[] = array(
                'est' => $est,
                'skor' => $skor,
                'EM' => $EM,
                'nama' => $nama
            );
        }

        $RHGM = array_values($RHGM);

        // dd($RHGM);

        //     foreach ($value as $key1 => $value2) {
        //         $GmWil1['skor'] = $value2['TotalSkorWil'];
        //     }
        // }


        // $FormatTabEst1 = array_values($FormatTabEst1);
        // dd($FormatTabEst1);
        //table untuk bagian EM estate wil1
        $FormatTabEst1 = array();
        foreach ($Wil1 as $key => $value) if (is_array($value)) {
            $inc = 0;
            $est = $key;
            $skor = $value['TotalSkorEST'];
            $EM = 'EM';
            $rank = $value['rankEST'];
            $nama = '-';
            foreach ($queryAsisten as $key4 => $value4) {
                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                    $nama = $value4['nama'];
                    break;
                }
            }
            $FormatTabEst1[] = array(
                'est' => $est,
                'skor' => $skor,
                'EM' => $EM,
                'rank' => $rank,
                'nama' => $nama
            );
            $inc++;
        }

        $FormatTabEst1 = array_values($FormatTabEst1);
        // dd($FormatTabEst1);
        usort($FormatTabEst1, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        //table untuk bagian EM estate wil2
        $FormatTabEst2 = array();
        foreach ($Wil2 as $key => $value) if (is_array($value)) {
            $inc = 0;
            $est = $key;
            $skor = $value['TotalSkorEST'];
            $EM = 'EM';
            $rank = $value['rankEST'];
            $nama = '-';
            foreach ($queryAsisten as $key4 => $value4) {
                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                    $nama = $value4['nama'];
                    break;
                }
            }
            $FormatTabEst2[] = array(
                'est' => $est,
                'skor' => $skor,
                'EM' => $EM,
                'rank' => $rank,
                'nama' => $nama
            );
            $inc++;
        }

        $FormatTabEst2 = array_values($FormatTabEst2);
        //    dd($FormatTabEst1);
        usort($FormatTabEst2, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        //table untuk bagian EM estate wil3
        $FormatTabEst3 = array();
        foreach ($Wil3 as $key => $value) if (is_array($value)) {
            $inc = 0;
            $est = $key;
            $skor = $value['TotalSkorEST'];
            $EM = 'EM';
            $rank = $value['rankEST'];
            $nama = '-';
            foreach ($queryAsisten as $key4 => $value4) {
                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                    $nama = $value4['nama'];
                    break;
                }
            }
            $FormatTabEst3[] = array(
                'est' => $est,
                'skor' => $skor,
                'EM' => $EM,
                'rank' => $rank,
                'nama' => $nama
            );
            $inc++;
        }

        $FormatTabEst3 = array_values($FormatTabEst3);
        // dd($FormatTabEst1);
        usort($FormatTabEst3, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });

        //bagian unutk afdeling
        // dd($Wil1);
        $FormatTable1 = array();
        foreach ($Wil1 as $key => $value) {
            if (is_array($value)) {
                $inc = 0;
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        $est = $key;
                        $afd = $key2;
                        $skor = $value2['TotalSkor'];
                        $EM = 'EM';
                        $rank = $value2['rankAFD'];
                        $nama = '-';
                        $data = isset($value2['data']) ? $value2['data'] : 'ada'; // Check if 'data' key exists, otherwise use 'ada'
                        foreach ($queryAsisten as $key4 => $value4) {
                            if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $afd) {
                                $nama = $value4['nama'];
                                break;
                            }
                        }
                        $FormatTable1[] = array(
                            'est' => $est,
                            'afd' => $afd,
                            'skor' => $skor,
                            'EM' => $EM,
                            'rank' => $rank,
                            'nama' => $nama,
                            'data' => $data
                        );
                        $inc++;
                    }
                }
            }
        }


        $FormatTable1 = array_values($FormatTable1);

        // Sort the array based on the 'rank' key
        usort($FormatTable1, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });

        // // Print the sorted array
        // print_r($FormatTable1);

        // dd($FormatTable1);
        //wil2
        $FormatTable2 = array();
        foreach ($Wil2 as $key => $value) {
            if (is_array($value)) {
                $inc = 0;
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        $est = $key;
                        $afd = $key2;
                        $skor = $value2['TotalSkor'];
                        $EM = 'EM';
                        $rank = $value2['rankAFD'];
                        $nama = '-';
                        $data = isset($value2['data']) ? $value2['data'] : 'ada'; // Check if 'data' key exists, otherwise use 'ada'
                        foreach ($queryAsisten as $key4 => $value4) {
                            if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $afd) {
                                $nama = $value4['nama'];
                                break;
                            }
                        }
                        $FormatTable2[] = array(
                            'est' => $est,
                            'afd' => $afd,
                            'skor' => $skor,
                            'EM' => $EM,
                            'rank' => $rank,
                            'nama' => $nama,
                            'data' => $data
                        );
                        $inc++;
                    }
                }
            }
        }

        $FormatTable2 = array_values($FormatTable2);
        usort($FormatTable2, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        // dd($FormatTable2);
        //wil3
        $FormatTable3 = array();
        foreach ($Wil3 as $key => $value) {
            if (is_array($value)) {
                $inc = 0;
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        $est = $key;
                        $afd = $key2;
                        $skor = $value2['TotalSkor'];
                        $EM = 'EM';
                        $rank = $value2['rankAFD'];
                        $nama = '-';
                        $data = isset($value2['data']) ? $value2['data'] : 'ada'; // Check if 'data' key exists, otherwise use 'ada'
                        foreach ($queryAsisten as $key4 => $value4) {
                            if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $afd) {
                                $nama = $value4['nama'];
                                break;
                            }
                        }
                        $FormatTable3[] = array(
                            'est' => $est,
                            'afd' => $afd,
                            'skor' => $skor,
                            'EM' => $EM,
                            'rank' => $rank,
                            'nama' => $nama,
                            'data' => $data
                        );
                        $inc++;
                    }
                }
            }
        }

        $FormatTable3 = array_values($FormatTable3);
        usort($FormatTable3, function ($a, $b) {
            return $a['rank'] - $b['rank'];
        });


        // dd($FormatTable1);
        // dd($mtancaktab1Wil);
        // dd($DataTable1);
        // $queryEsta = DB::connection('mysql2')->table('estate')
        //     ->where('est', '!=', 'CWS')
        //     ->whereIn('wil', [1, 2, 3])->pluck('est');
        // $queryEsta = json_decode($queryEsta, true);

        $queryEsta = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3', 'CWS1', 'CWS2', 'CWS3', 'NBM', 'REG-1', 'SLM', 'SR', 'TC', 'SRS', 'SGM', 'SYM', 'SKM', 'SJM'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $Reg)
            // ->where('wil.regional', '3')
            ->pluck('est');

        // Convert the result into an array with numeric keys
        $queryEsta = array_values(json_decode($queryEsta, true));

        // dd($queryEsta);

        function movePlasmaAfterUpe($array)
        {
            // Find the index of "UPE"
            $indexUpe = array_search("UPE", $array);

            // Find the index of "PLASMA"
            $indexPlasma = array_search("Plasma1", $array);

            // Move "PLASMA" after "UPE"
            if ($indexUpe !== false && $indexPlasma !== false && $indexPlasma < $indexUpe) {
                $plasma = $array[$indexPlasma];
                array_splice($array, $indexPlasma, 1);
                array_splice($array, $indexUpe, 0, [$plasma]);
            }

            return $array;
        }

        // Usage:

        $queryEsta = movePlasmaAfterUpe($queryEsta);
        // dd($queryEsta);



        // Display the updated array
        // dd($reyEst);



        // dd($mtancaktab1Wil);
        $chartBTT = array();
        foreach ($mtancaktab1Wil as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (is_array($value2)) {
                    $chartBTT[$key2] = $value2['brd/jjgest'];
                }
            }
        }
        $array = $chartBTT;

        // Find the index of "UPE"
        $index = array_search("UPE", array_keys($array));

        // Move "PLASMA" after "UPE"
        if ($index !== false && isset($array["Plasma1"])) {
            $plasma = $array["Plasma1"];
            unset($array["Plasma1"]);
            $array = array_slice($array, 0, $index + 1, true) + ["Plasma1" => $plasma] + array_slice($array, $index + 1, null, true);
        }

        // Find the index of "UPE"

        // $arrayEst now contains the modified array


        // dd($chartBTT);
        //     "brd/jjgwil" => "0.24"
        // "buah/jjgwil" => "0.00"
        // // dd($RankingFinal, $chartBTT);
        // dd($mtancaktab1Wil);
        $chartBuahTT = array();
        foreach ($mtancaktab1Wil as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chartBuahTT[$key2] = $value2['buah/jjg'];
            }
        }
        $arrBuahBTT = $chartBuahTT;

        // dd($arrBuahBTT);
        // Find the index of "UPE"
        $index = array_search("UPE", array_keys($arrBuahBTT));

        // Move "PLASMA" after "UPE"
        if ($index !== false && isset($arrBuahBTT["Plasma1"])) {
            $plasma = $arrBuahBTT["Plasma1"];
            unset($arrBuahBTT["Plasma1"]);
            $arrBuahBTT = array_slice($arrBuahBTT, 0, $index + 1, true) + ["Plasma1" => $plasma] + array_slice($arrBuahBTT, $index + 1, null, true);
        }




        //table perbulan 
        // dd($queryEstePla);

        //untuk plasma tabel
        //transport
        $defPLAtrans = array();
        foreach ($queryEstePla as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defPLAtrans[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $mutuTransPLAmerge = array();
        foreach ($defPLAtrans as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTTrans)) {
                    if (array_key_exists($afdKey, $dataMTTrans[$estKey])) {
                        if (!empty($dataMTTrans[$estKey][$afdKey])) {
                            $mutuTransPLAmerge[$estKey][$afdKey] = $dataMTTrans[$estKey][$afdKey];
                        } else {
                            $mutuTransPLAmerge[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mutuTransPLAmerge[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mutuTransPLAmerge[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        //buah
        $defPlaBuah = array();
        foreach ($queryEstePla as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defPlaBuah[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        // dd($mutuTransPLAmerge);
        $mtBuahPlasma = array();
        foreach ($defPlaBuah as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataMTBuah)) {
                    if (array_key_exists($afdKey, $dataMTBuah[$estKey])) {
                        if (!empty($dataMTBuah[$estKey][$afdKey])) {
                            $mtBuahPlasma[$estKey][$afdKey] = $dataMTBuah[$estKey][$afdKey];
                        } else {
                            $mtBuahPlasma[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mtBuahPlasma[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mtBuahPlasma[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        // dd($mtBuahPlasma);
        //ancak
        $defAncakPlasma = array();
        foreach ($queryEstePla as $est) {
            // dd($est);
            foreach ($queryAfd as $afd) {
                // dd($afd);
                if ($est['est'] == $afd['est']) {
                    $defAncakPlasma[$est['est']][$afd['nama']]['null'] = 0;
                }
            }
        }
        $mtAncakPlasma = array();
        foreach ($defPlaBuah as $estKey => $afdArray) {
            foreach ($afdArray as $afdKey => $afdValue) {
                if (array_key_exists($estKey, $dataPerBulan)) {
                    if (array_key_exists($afdKey, $dataPerBulan[$estKey])) {
                        if (!empty($dataPerBulan[$estKey][$afdKey])) {
                            $mtAncakPlasma[$estKey][$afdKey] = $dataPerBulan[$estKey][$afdKey];
                        } else {
                            $mtAncakPlasma[$estKey][$afdKey] = $afdValue;
                        }
                    } else {
                        $mtAncakPlasma[$estKey][$afdKey] = $afdValue;
                    }
                } else {
                    $mtAncakPlasma[$estKey][$afdKey] = $afdValue;
                }
            }
        }
        // dd($transNewdata);
        //perhitungan mutu transport
        $mtPLA = array();
        foreach ($mutuTransPLAmerge as $key => $value) if (!empty($value)) {
            $dataBLokEst = 0;
            $sum_btEst = 0;
            $sum_rstEst = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $sum_bt = 0;
                $sum_rst = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                $dataBLok = 0;
                $listBlokPerAfd = array();
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {

                    // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                    // }
                    $dataBLok = count($listBlokPerAfd);
                    $sum_bt += $value2['bt'];
                    $sum_rst += $value2['rst'];
                }


                $tot_sample = 0;  // Define the variable outside of the foreach loop

                foreach ($transNewdata as $keys => $trans) {
                    if ($keys == $key) {
                        foreach ($trans as $keys2 => $trans2) {
                            if ($keys2 == $key1) {
                                // $mtPLA[$key][$key1][$key2]['tph_sampleNew'] = $trans2['total_tph'];
                                $tot_sample = $trans2['total_tph'];
                            }
                        }
                    }
                }

                if ($Reg == '2' || $Reg == 2) {
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

                if ($Reg == '2' || $Reg == 2) {
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
                    $mtPLA[$key][$key1]['check_data'] = 'ada';
                    // $mtPLA[$key][$key1]['skor_buahPerTPH'] = $skor_buah = skor_buah_tinggal($buahPerTPH);

                } else {
                    $mtPLA[$key][$key1]['check_data'] = 'kosong';
                    // $mtPLA[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;

                }

                // $totalSkor = $skor_brd + $skor_buah ;

                $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                if ($Reg == 2) {
                    $mtPLA[$key][$key1]['tph_sample'] = $tot_sample;
                } else {
                    $mtPLA[$key][$key1]['tph_sample'] = $dataBLok;
                }
                $mtPLA[$key][$key1]['total_brd'] = $sum_bt;
                $mtPLA[$key][$key1]['total_brd/TPH'] = $brdPertph;
                $mtPLA[$key][$key1]['total_buah'] = $sum_rst;
                $mtPLA[$key][$key1]['total_buahPerTPH'] = $buahPerTPH;
                $mtPLA[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                $mtPLA[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                $mtPLA[$key][$key1]['skorWil'] = $totalSkor;

                //PERHITUNGAN PERESTATE
                if ($Reg == '2' || $Reg == 2) {
                    $dataBLokEst += $tot_sample;
                } else {
                    $dataBLokEst += $dataBLok;
                }

                // $dataBLokEst += $dataBLok;
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

                $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                if (!empty($nonZeroValues)) {
                    $mtPLA[$key]['check_data'] = 'ada';
                    // $mtPLA[$key]['skor_buahPerTPH'] = $skor_buah = skor_buah_tinggal($buahPerTPHEst);

                } else {
                    $mtPLA[$key]['check_data'] = 'kosong';
                    // $mtPLA[$key]['skor_buahPerTPH'] = $skor_buah = 0;

                }

                // $totalSkorEst = $skor_brd + $skor_buah ;
                $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
            } else {
                $mtPLA[$key][$key1]['tph_sample'] = 0;
                $mtPLA[$key][$key1]['total_brd'] = 0;
                $mtPLA[$key][$key1]['total_brd/TPH'] = 0;
                $mtPLA[$key][$key1]['total_buah'] = 0;
                $mtPLA[$key][$key1]['total_buahPerTPH'] = 0;
                $mtPLA[$key][$key1]['skor_brdPertph'] = 0;
                $mtPLA[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtPLA[$key][$key1]['skorWil'] = 0;
            }
            $mtPLA[$key]['tph_sample'] = $dataBLokEst;
            $mtPLA[$key]['total_brd'] = $sum_btEst;
            $mtPLA[$key]['total_brd/TPH'] = $brdPertphEst;
            $mtPLA[$key]['total_buah'] = $sum_rstEst;
            $mtPLA[$key]['total_buahPerTPH'] = $buahPerTPHEst;
            // $mtPLA[$key]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
            // $mtPLA[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
            $mtPLA[$key]['skorPlasma'] = $totalSkorEst;
        } else {
            $mtPLA[$key]['tph_sample'] = 0;
            $mtPLA[$key]['total_brd'] = 0;
            $mtPLA[$key]['total_brd/TPH'] = 0;
            $mtPLA[$key]['total_buah'] = 0;
            $mtPLA[$key]['total_buahPerTPH'] = 0;
            $mtPLA[$key]['skor_brdPertph'] = 0;
            $mtPLA[$key]['skor_buahPerTPH'] = 0;
            $mtPLA[$key]['skorPlasma'] = 0;
        }


        // dd($mtPLA);
        //perhitungan mutu buah
        $mtPLABuah = array();

        foreach ($mtBuahPlasma as $key => $value) if (is_array($value)) {
            $jum_haEst  = 0;
            $sum_SamplejjgEst = 0;
            $sum_bmtEst = 0;
            $sum_bmkEst = 0;
            $sum_overEst = 0;
            $sum_abnorEst = 0;
            $sum_kosongjjgEst = 0;
            $sum_vcutEst = 0;
            $sum_krEst = 0;
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
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
                $listBlokPerAfd = [];
                foreach ($value1 as $key2 => $value2)  if (is_array($value2)) {

                    // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'], $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'] . ' ' . $value2['tph_baris'];
                    // }
                    $dtBlok = count($listBlokPerAfd);
                    $sum_bmt += $value2['bmt'];
                    $sum_bmk += $value2['bmk'];
                    $sum_over += $value2['overripe'];
                    $sum_kosongjjg += $value2['empty_bunch'];
                    $sum_vcut += $value2['vcut'];
                    $sum_kr += $value2['alas_br'];
                    $sum_Samplejjg += $value2['jumlah_jjg'];
                    $sum_abnor += $value2['abnormal'];
                }
                $jml_mth = ($sum_bmt + $sum_bmk);
                $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                // $dataBLok = count($combination_counts);
                if ($sum_kr != 0) {
                    $total_kr = round($sum_kr / $dtBlok, 3);
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

                $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

                if (!empty($nonZeroValues)) {
                    $mtPLABuah[$key][$key1]['check_data'] = 'ada';
                    // $mtPLABuah[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                    // $mtPLABuah[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                    // $mtPLABuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                    // $mtPLABuah[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                    // $mtPLABuah[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                } else {
                    $mtPLABuah[$key][$key1]['check_data'] = 'kosong';
                    // $mtPLABuah[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    // $mtPLABuah[$key][$key1]['skor_over'] = $skor_over = 0;
                    // $mtPLABuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    // $mtPLABuah[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    // $mtPLABuah[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                // $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


                $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                $mtPLABuah[$key][$key1]['tph_baris_blok'] = $dtBlok;
                $mtPLABuah[$key][$key1]['sampleJJG_total'] = $sum_Samplejjg;
                $mtPLABuah[$key][$key1]['total_mentah'] = $jml_mth;
                $mtPLABuah[$key][$key1]['total_perMentah'] = $PerMth;
                $mtPLABuah[$key][$key1]['total_masak'] = $jml_mtg;
                $mtPLABuah[$key][$key1]['total_perMasak'] = $PerMsk;
                $mtPLABuah[$key][$key1]['total_over'] = $sum_over;
                $mtPLABuah[$key][$key1]['total_perOver'] = $PerOver;
                $mtPLABuah[$key][$key1]['total_abnormal'] = $sum_abnor;
                $mtPLABuah[$key][$key1]['perAbnormal'] = $PerAbr;
                $mtPLABuah[$key][$key1]['total_jjgKosong'] = $sum_kosongjjg;
                $mtPLABuah[$key][$key1]['total_perKosongjjg'] = $Perkosongjjg;
                $mtPLABuah[$key][$key1]['total_vcut'] = $sum_vcut;
                $mtPLABuah[$key][$key1]['perVcut'] = $PerVcut;

                $mtPLABuah[$key][$key1]['jum_kr'] = $sum_kr;
                $mtPLABuah[$key][$key1]['total_kr'] = $total_kr;
                $mtPLABuah[$key][$key1]['persen_kr'] = $per_kr;

                // skoring
                $mtPLABuah[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                $mtPLABuah[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                $mtPLABuah[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);
                $mtPLABuah[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                $mtPLABuah[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);

                $mtPLABuah[$key][$key1]['skor_kr'] = skor_abr_mb($per_kr);
                $mtPLABuah[$key][$key1]['skorWil'] = $totalSkor;

                //perhitungan estate
                $jum_haEst += $dtBlok;
                $sum_SamplejjgEst += $sum_Samplejjg;
                $sum_bmtEst += $jml_mth;
                $sum_bmkEst += $jml_mtg;
                $sum_overEst += $sum_over;
                $sum_abnorEst += $sum_abnor;
                $sum_kosongjjgEst += $sum_kosongjjg;
                $sum_vcutEst += $sum_vcut;
                $sum_krEst += $sum_kr;
            } else {
                $mtPLABuah[$key][$key1]['tph_baris_blok'] = 0;
                $mtPLABuah[$key][$key1]['sampleJJG_total'] = 0;
                $mtPLABuah[$key][$key1]['total_mentah'] = 0;
                $mtPLABuah[$key][$key1]['total_perMentah'] = 0;
                $mtPLABuah[$key][$key1]['total_masak'] = 0;
                $mtPLABuah[$key][$key1]['total_perMasak'] = 0;
                $mtPLABuah[$key][$key1]['total_over'] = 0;
                $mtPLABuah[$key][$key1]['total_perOver'] = 0;
                $mtPLABuah[$key][$key1]['total_abnormal'] = 0;
                $mtPLABuah[$key][$key1]['perAbnormal'] = 0;
                $mtPLABuah[$key][$key1]['total_jjgKosong'] = 0;
                $mtPLABuah[$key][$key1]['total_perKosongjjg'] = 0;
                $mtPLABuah[$key][$key1]['total_vcut'] = 0;
                $mtPLABuah[$key][$key1]['perVcut'] = 0;

                $mtPLABuah[$key][$key1]['jum_kr'] = 0;
                $mtPLABuah[$key][$key1]['total_kr'] = 0;
                $mtPLABuah[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtPLABuah[$key][$key1]['skor_mentah'] = 0;
                $mtPLABuah[$key][$key1]['skor_masak'] = 0;
                $mtPLABuah[$key][$key1]['skor_over'] = 0;
                $mtPLABuah[$key][$key1]['skor_jjgKosong'] = 0;
                $mtPLABuah[$key][$key1]['skor_vcut'] = 0;
                $mtPLABuah[$key][$key1]['skor_abnormal'] = 0;;
                $mtPLABuah[$key][$key1]['skor_kr'] = 0;
                $mtPLABuah[$key][$key1]['skorWil'] = 0;
            }

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
                $mtPLABuah[$key]['check_data'] = 'ada';
                // $mtPLABuah[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                // $mtPLABuah[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                // $mtPLABuah[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                // $mtPLABuah[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                // $mtPLABuah[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
            } else {
                $mtPLABuah[$key]['check_data'] = 'kosong';
                // $mtPLABuah[$key]['skor_masak'] = $skor_masak = 0;
                // $mtPLABuah[$key]['skor_over'] = $skor_over = 0;
                // $mtPLABuah[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                // $mtPLABuah[$key]['skor_vcut'] = $skor_vcut =  0;
                // $mtPLABuah[$key]['skor_kr'] = $skor_kr = 0;
            }

            // $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;


            $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
            $mtPLABuah[$key]['tph_baris_blok'] = $jum_haEst;
            $mtPLABuah[$key]['sampleJJG_total'] = $sum_SamplejjgEst;
            $mtPLABuah[$key]['total_mentah'] = $sum_bmtEst;
            $mtPLABuah[$key]['total_perMentah'] = $PerMthEst;
            $mtPLABuah[$key]['total_masak'] = $sum_bmkEst;
            $mtPLABuah[$key]['total_perMasak'] = $PerMskEst;
            $mtPLABuah[$key]['total_over'] = $sum_overEst;
            $mtPLABuah[$key]['total_perOver'] = $PerOverEst;
            $mtPLABuah[$key]['total_abnormal'] = $sum_abnorEst;
            $mtPLABuah[$key]['total_perabnormal'] = $PerAbrest;
            $mtPLABuah[$key]['total_jjgKosong'] = $sum_kosongjjgEst;
            $mtPLABuah[$key]['total_perKosongjjg'] = $PerkosongjjgEst;
            $mtPLABuah[$key]['total_vcut'] = $sum_vcutEst;
            $mtPLABuah[$key]['perVcut'] = $PerVcutest;
            $mtPLABuah[$key]['jum_kr'] = $sum_krEst;
            $mtPLABuah[$key]['kr_blok'] = $total_krEst;

            $mtPLABuah[$key]['persen_kr'] = $per_krEst;

            // skoring
            $mtPLABuah[$key]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
            $mtPLABuah[$key]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
            $mtPLABuah[$key]['skor_over'] = skor_buah_over_mb($PerOverEst);;
            $mtPLABuah[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
            $mtPLABuah[$key]['skor_vcut'] = skor_vcut_mb($PerVcutest);
            $mtPLABuah[$key]['skor_kr'] = skor_abr_mb($per_krEst);
            $mtPLABuah[$key]['skorPlasma'] = $totalSkorEst;

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
            $mtPLABuah[$key]['tph_baris_blok'] = 0;
            $mtPLABuah[$key]['sampleJJG_total'] = 0;
            $mtPLABuah[$key]['total_mentah'] = 0;
            $mtPLABuah[$key]['total_perMentah'] = 0;
            $mtPLABuah[$key]['total_masak'] = 0;
            $mtPLABuah[$key]['total_perMasak'] = 0;
            $mtPLABuah[$key]['total_over'] = 0;
            $mtPLABuah[$key]['total_perOver'] = 0;
            $mtPLABuah[$key]['total_abnormal'] = 0;
            $mtPLABuah[$key]['total_perabnormal'] = 0;
            $mtPLABuah[$key]['total_jjgKosong'] = 0;
            $mtPLABuah[$key]['total_perKosongjjg'] = 0;
            $mtPLABuah[$key]['total_vcut'] = 0;
            $mtPLABuah[$key]['perVcut'] = 0;
            $mtPLABuah[$key]['jum_kr'] = 0;
            $mtPLABuah[$key]['kr_blok'] = 0;
            $mtPLABuah[$key]['persen_kr'] = 0;

            // skoring
            $mtPLABuah[$key]['skor_mentah'] = 0;
            $mtPLABuah[$key]['skor_masak'] = 0;
            $mtPLABuah[$key]['skor_over'] = 0;
            $mtPLABuah[$key]['skor_jjgKosong'] = 0;
            $mtPLABuah[$key]['skor_vcut'] = 0;
            $mtPLABuah[$key]['skor_abnormal'] = 0;;
            $mtPLABuah[$key]['skor_kr'] = 0;
            $mtPLABuah[$key]['skorPlasma'] = 0;
        }

        //mutu ancak
        $mtPLAancak = array();
        foreach ($mtAncakPlasma as $key => $value) if (!empty($value)) {
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
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
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
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                    }
                    $jum_ha = count($listBlokPerAfd);
                    $totalPokok += $value2["sample"];
                    $totalPanen +=  $value2["jjg"];
                    $totalP_panen += $value2["brtp"];
                    $totalK_panen += $value2["brtk"];
                    $totalPTgl_panen += $value2["brtgl"];

                    $totalbhts_panen += $value2["bhts"];
                    $totalbhtm1_panen += $value2["bhtm1"];
                    $totalbhtm2_panen += $value2["bhtm2"];
                    $totalbhtm3_oanen += $value2["bhtm3"];

                    $totalpelepah_s += $value2["ps"];
                }


                if ($totalPokok != 0) {
                    $akp = round(($totalPanen / $totalPokok) * 100, 1);
                } else {
                    $akp = 0;
                }


                $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                if ($totalPokok != 0) {
                    $brdPerjjg = round($skor_bTinggal / $totalPanen, 1);
                } else {
                    $brdPerjjg = 0;
                }

                $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                if ($sumBH != 0) {
                    $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 1);
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
                    $mtPLAancak[$key][$key1]['check_data'] = 'ada';
                    // $mtPLAancak[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBH);
                    // $mtPLAancak[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                } else {
                    $mtPLAancak[$key][$key1]['check_data'] = 'kosong';
                    // $mtPLAancak[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    // $mtPLAancak[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                // $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                $mtPLAancak[$key][$key1]['pokok_sample'] = $totalPokok;
                $mtPLAancak[$key][$key1]['ha_sample'] = $jum_ha;
                $mtPLAancak[$key][$key1]['jumlah_panen'] = $totalPanen;
                $mtPLAancak[$key][$key1]['akp_rl'] = $akp;

                $mtPLAancak[$key][$key1]['p'] = $totalP_panen;
                $mtPLAancak[$key][$key1]['k'] = $totalK_panen;
                $mtPLAancak[$key][$key1]['tgl'] = $totalPTgl_panen;

                $mtPLAancak[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtPLAancak[$key][$key1]['brd/jjg'] = $brdPerjjg;

                // data untuk buah tinggal
                $mtPLAancak[$key][$key1]['bhts_s'] = $totalbhts_panen;
                $mtPLAancak[$key][$key1]['bhtm1'] = $totalbhtm1_panen;
                $mtPLAancak[$key][$key1]['bhtm2'] = $totalbhtm2_panen;
                $mtPLAancak[$key][$key1]['bhtm3'] = $totalbhtm3_oanen;
                $mtPLAancak[$key][$key1]['buah/jjg'] = $sumPerBH;

                $mtPLAancak[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 3);
                // data untuk pelepah sengklek

                $mtPLAancak[$key][$key1]['palepah_pokok'] = $totalpelepah_s;
                // total skor akhir
                $mtPLAancak[$key][$key1]['skor_bh'] = skor_brd_ma($brdPerjjg);
                $mtPLAancak[$key][$key1]['skor_brd'] = skor_buah_Ma($sumPerBH);
                $mtPLAancak[$key][$key1]['skor_ps'] = skor_palepah_ma($perPl);
                $mtPLAancak[$key][$key1]['skorWil'] = $ttlSkorMA;

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
                $mtPLAancak[$key][$key1]['pokok_sample'] = 0;
                $mtPLAancak[$key][$key1]['ha_sample'] = 0;
                $mtPLAancak[$key][$key1]['jumlah_panen'] = 0;
                $mtPLAancak[$key][$key1]['akp_rl'] =  0;

                $mtPLAancak[$key][$key1]['p'] = 0;
                $mtPLAancak[$key][$key1]['k'] = 0;
                $mtPLAancak[$key][$key1]['tgl'] = 0;

                // $mtPLAancak[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtPLAancak[$key][$key1]['brd/jjg'] = 0;

                // data untuk buah tinggal
                $mtPLAancak[$key][$key1]['bhts_s'] = 0;
                $mtPLAancak[$key][$key1]['bhtm1'] = 0;
                $mtPLAancak[$key][$key1]['bhtm2'] = 0;
                $mtPLAancak[$key][$key1]['bhtm3'] = 0;

                // $mtPLAancak[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 3);
                // data untuk pelepah sengklek

                $mtPLAancak[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhi0;

                $mtPLAancak[$key][$key1]['skor_bh'] = 0;
                $mtPLAancak[$key][$key1]['skor_brd'] = 0;
                $mtPLAancak[$key][$key1]['skor_ps'] = 0;
                $mtPLAancak[$key][$key1]['skorWil'] = 0;
            }
            $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
            $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
            // dd($sumBHEst);
            if ($pokok_panenEst != 0) {
                $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 3);
            } else {
                $akpEst = 0;
            }

            if ($pokok_panenEst != 0) {
                $brdPerjjgEst = round($totalPKT / $janjang_panenEst, 1);
            } else {
                $brdPerjjgEst = 0;
            }



            // dd($sumBHEst);
            if ($sumBHEst != 0) {
                $sumPerBHEst = round($sumBHEst / ($janjang_panenEst + $sumBHEst) * 100, 1);
            } else {
                $sumPerBHEst = 0;
            }

            if ($pokok_panenEst != 0) {
                $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 1);
            } else {
                $perPlEst = 0;
            }

            $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

            if (!empty($nonZeroValues)) {
                $mtPLAancak[$key]['check_data'] = 'ada';
                // $mtPLAancak[$key]['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBHEst);
                // $mtPLAancak[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
            } else {
                $mtPLAancak[$key]['check_data'] = 'kosong';
                // $mtPLAancak[$key]['skor_brd'] = $skor_brd = 0;
                // $mtPLAancak[$key]['skor_ps'] = $skor_ps = 0;
            }

            // $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;
            $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
            //PENAMPILAN UNTUK PERESTATE
            $mtPLAancak[$key]['pokok_sample'] = $pokok_panenEst;
            $mtPLAancak[$key]['ha_sample'] =  $jum_haEst;
            $mtPLAancak[$key]['jumlah_panen'] = $janjang_panenEst;
            $mtPLAancak[$key]['akp_rl'] =  $akpEst;

            $mtPLAancak[$key]['p'] = $p_panenEst;
            $mtPLAancak[$key]['k'] = $k_panenEst;
            $mtPLAancak[$key]['tgl'] = $brtgl_panenEst;

            // $mtPLAancak[$key]['total_brd'] = $skor_bTinggal;
            $mtPLAancak[$key]['brd/jjgest'] = $brdPerjjgEst;
            $mtPLAancak[$key]['buah/jjg'] = $sumPerBHEst;

            // data untuk buah tinggal
            $mtPLAancak[$key]['bhts_s'] = $bhtsEST;
            $mtPLAancak[$key]['bhtm1'] = $bhtm1EST;
            $mtPLAancak[$key]['bhtm2'] = $bhtm2EST;
            $mtPLAancak[$key]['bhtm3'] = $bhtm3EST;
            $mtPLAancak[$key]['palepah_pokok'] = $pelepah_sEST;
            $mtPLAancak[$key]['palepah_per'] = $perPlEst;
            // total skor akhir
            $mtPLAancak[$key]['skor_bh'] =  skor_brd_ma($brdPerjjgEst);
            $mtPLAancak[$key]['skor_brd'] = skor_buah_Ma($sumPerBHEst);
            $mtPLAancak[$key]['skor_ps'] = skor_palepah_ma($perPlEst);
            $mtPLAancak[$key]['skorPlasma'] = $totalSkorEst;
        } else {
            $mtPLAancak[$key]['pokok_sample'] = 0;
            $mtPLAancak[$key]['ha_sample'] =  0;
            $mtPLAancak[$key]['jumlah_panen'] = 0;
            $mtPLAancak[$key]['akp_rl'] =  0;

            $mtPLAancak[$key]['p'] = 0;
            $mtPLAancak[$key]['k'] = 0;
            $mtPLAancak[$key]['tgl'] = 0;

            // $mtPLAancak[$key]['total_brd'] = $skor_bTinggal;
            $mtPLAancak[$key]['brd/jjgest'] = 0;
            $mtPLAancak[$key]['buah/jjg'] = 0;
            // data untuk buah tinggal
            $mtPLAancak[$key]['bhts_s'] = 0;
            $mtPLAancak[$key]['bhtm1'] = 0;
            $mtPLAancak[$key]['bhtm2'] = 0;
            $mtPLAancak[$key]['bhtm3'] = 0;
            $mtPLAancak[$key]['palepah_pokok'] = 0;
            // total skor akhir
            $mtPLAancak[$key]['skor_bh'] =  0;
            $mtPLAancak[$key]['skor_brd'] = 0;
            $mtPLAancak[$key]['skor_ps'] = 0;
            $mtPLAancak[$key]['skorPlasma'] = 0;
        }
        // dd($mtPLAancak);if (is_array($buah1) && $key1 == $cak1 && $cak1 == $bh1)
        //         mtPLA
        // mtPLAancak
        // mtPLABuah
        // dd($mtPLA, $mtPLABuah, $mtPLAancak);
        // dd($mtPLAancak);
        //rekap toal skor plasma
        $rekapPlasma = array();
        foreach ($mtPLA as $key => $trans) {
            foreach ($trans as $key2 => $trans1) {
                foreach ($mtPLAancak as $cak => $ancak) {
                    foreach ($ancak as $cak2 => $ancak1) {
                        foreach ($mtPLABuah as $bh => $buah) {
                            foreach ($buah as $bh2 => $buah1) if (is_array($buah1) && $key2 == $cak2 && $cak2 == $bh2) {
                                if ($trans1['check_data'] == 'kosong' && $ancak1['check_data'] === 'kosong' && $buah1['check_data'] === 'kosong') {

                                    $rekapPlasma[$key][$key2]['data'] = 'kosong';
                                }
                                if ($trans1['check_data'] == 'kosong' && $ancak1['check_data'] === 'kosong' && $buah1['check_data'] === 'kosong') {

                                    $rekapPlasma[$key][$key2]['Wil'] = 0;
                                } else {
                                    $rekapPlasma[$key][$key2]['Wil'] = $trans1['skorWil'] + $ancak1['skorWil'] + $buah1['skorWil'];
                                }
                                if ($trans1['check_data'] == 'kosong' && $ancak1['check_data'] === 'kosong' && $buah1['check_data'] === 'kosong') {

                                    $rekapPlasma[$key]['Plasma'] = 0;
                                } else {
                                    $rekapPlasma[$key]['Plasma'] = $trans['skorPlasma'] + $ancak['skorPlasma'] + $buah['skorPlasma'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // dd($rekapPlasma);

        //  buat ranking
        foreach ($rekapPlasma as $key1 => $estates)  if (is_array($estates)) {
            // $sortedData = array();
            $sortedDataEst = array();
            foreach ($estates as $estateName => $data) {
                // dd($data);
                if (is_array($data)) {
                    $sortedDataEst[] = array(
                        'key1' => $key1,
                        'estateName' => $estateName,
                        'data' => $data
                    );
                }
            }
            usort($sortedDataEst, function ($a, $b) {
                return $b['data']['Wil'] - $a['data']['Wil'];
            });
            $rank = 1;
            foreach ($sortedDataEst as $sortedest) {
                $rekapPlasma[$key1][$sortedest['estateName']]['rank'] = $rank;
                $rank++;
            }
            unset($sortedDataEst);
        }
        // dd($rekapPlasma);
        function sortAndAssignRanks($rekapPlasma)
        {
            foreach ($rekapPlasma as $key1 => $estates) {
                if (is_array($estates)) {
                    $sortedDataEst = array();
                    foreach ($estates as $estateName => $data) {
                        if (is_array($data) && isset($data['Wil'])) {
                            $sortedDataEst[] = array(
                                'key1' => $key1,
                                'estateName' => $estateName,
                                'data' => $data
                            );
                        }
                    }

                    usort($sortedDataEst, function ($a, $b) {
                        return $a['data']['rank'] - $b['data']['rank'];
                    });

                    $sortedRekapPlasma = array(); // Create a temporary array to store the sorted data
                    foreach ($sortedDataEst as $sortedest) {
                        $sortedRekapPlasma[$sortedest['estateName']] = $rekapPlasma[$key1][$sortedest['estateName']];
                    }

                    // Add the "Plasma" key back to the sorted array
                    if (isset($rekapPlasma[$key1]['Plasma'])) {
                        $sortedRekapPlasma['Plasma'] = $rekapPlasma[$key1]['Plasma'];
                    }

                    // Replace the original array with the sorted one
                    $rekapPlasma[$key1] = $sortedRekapPlasma;
                    unset($sortedDataEst);
                }
            }

            return $rekapPlasma;
        }

        // okekitacok
        // Call the function to sort the array and assign ranks
        $rekapPlasma = sortAndAssignRanks($rekapPlasma);



        // dd($rekapPlasma);
        $rankingPlasma = $rekapPlasma;

        //ubah format untuk mempermudahkan menampilkan di tabel
        $PlasmaEm = array();
        foreach ($rankingPlasma as $key => $value) if (is_array($value)) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                // dd($value1);
                $inc = 0;
                $est = $key;
                $skor = $value1['Wil'];
                // dd($skor);
                $EM = $key1;

                $rank = $value1['rank'];
                // $rank = $value1['rank'];
                $nama = '-';
                $data = isset($value1['data']) ? $value1['data'] : 'ada'; // Check if 'data' key exists, otherwise use 'ada'
                foreach ($queryAsisten as $key4 => $value4) {
                    if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                        $nama = $value4['nama'];
                        break;
                    }
                }
                $PlasmaEm[] = array(
                    'est' => $est,
                    'afd' => $EM,
                    'nama' => $nama,
                    'skor' => $skor,
                    'rank' => $rank,
                    'data' => $data

                );
                $inc++;
            }
        }

        $PlasmaEm = array_values($PlasmaEm);

        $PlsamaGMEM = array();
        $namaEM = '-';
        foreach ($rankingPlasma as $key => $value) {
            if (is_array($value)) {
                $inc = 0;
                $est = $key;
                $skor = $value['Plasma'];
                $EM = 'EM';
                // $GM = 'GM';
                // dd($value);
                foreach ($queryAsisten as $key4 => $value4) {

                    if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                        $namaEM = $value4['nama'];
                    }
                    // if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $GM) {
                    //     $namaGM = $value4['nama'];
                    // }
                }
                $inc++;
            }
        }

        $PlsamaGMEM[] = array(
            'est' => $est,
            'afd' => $EM,
            'namaEM' => $namaEM,
            'Skor' => $skor,

        );

        $PlsamaGMEM = array_values($PlsamaGMEM);

        // dd($rankingPlasma);
        $plasmaGM = array();
        $namaGM = '-';
        $GM = 'GM';
        $skor = 0;
        $est = '';

        foreach ($rankingPlasma as $key => $value) {
            if (is_array($value) && isset($value['Plasma'])) {
                $inc = 0;
                $est = $key;
                $skor = $value['Plasma'];

                foreach ($queryAsisten as $key4 => $value4) {
                    if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $GM) {
                        $namaGM = $value4['nama'];
                    }
                }
                $inc++;
            }
        }

        $plasmaGM[] = array(
            'est' => $est,
            'afd' => $GM,
            'namaEM' => $namaGM,
            'Skor' => $skor,
        );

        $plasmaGM = array_values($plasmaGM);




        $pt_muaSkor = [
            'Pt_mua' => $sumOfAllScores
        ];
        //
        // dd($sumOfAllScores);
        $keysToRemove = ["SRE", "LDE", "SKE"];
        $filteredBuah = [];

        foreach ($arrBuahBTT as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $filteredBuah[$key] = $value;
            }
        }


        // dd($arrBuahBTT);

        $chartPerwil = array();
        foreach ($mtancaktab1Wil as $key => $value) {
            $chartPerwil[$key] = $value['brd/jjgwil'];
        }
        // dd($mtancaktab1Wil);
        $buahPerwil = array();
        foreach ($mtancaktab1Wil as $key => $value) {
            $buahPerwil[$key] =  $value['buah/jjgwil'];
        }



        // mengambil nilai grafik mutu ancak dari pt.mua
        $ptMuachartBuah = $mtAncakMua['buah/jjgwil'];
        $ptMuachartBRD = $mtAncakMua['brd/jjgwil'];

        // dd($mtAncakMua);
        $arrChartbhMua = [
            'pt_muabuah' => $ptMuachartBuah
        ];
        $arrChartbhBRD = [
            'pt_muabrd' => $ptMuachartBRD
        ];


        $filteredBRD = [];

        foreach ($array as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $filteredBRD[$key] = $value;
            }
        }


        // membuat pt muah satu array dan di bagian bawah
        $filteredBuah["pt_muabuah"] = $arrChartbhMua["pt_muabuah"];
        $filteredBRD["pt_muabrd"] = $arrChartbhBRD["pt_muabrd"];
        // dd($filteredBuah);

        // mengambil mutu ancak di plasma
        $chartBuahTTPlas = array();
        foreach ($mtPLAancak as $key => $value) {


            $chartBuahTTPlas[$key] = $value['buah/jjg'];
        }
        $chartPlasBRD = array();
        foreach ($mtPLAancak as $key => $value) {


            $chartPlasBRD[$key] = $value['brd/jjgest'];
        }
        // menambahkan urutan plasma jika reg 1 tambahakan plasma sehabis key upe dan seterusnya
        if ($Reg == 1 || $Reg == '1') {
            $insertAfter = "UPE";
        } else if ($Reg == 2 || $Reg == '2') {
            $insertAfter = "SPE";
        } else {
            $insertAfter = "PKE";
        }


        // fungsi untuk menambah plasma ke dalam satu array untuk mutu ancak
        $result_brd = [];
        $added = false;
        foreach ($filteredBRD as $key => $value) {
            $result_brd[$key] = $value;
            if ($key === $insertAfter && !$added) {
                foreach ($chartPlasBRD as $k => $v) {
                    $result_brd[$k] = $v;
                }
                $added = true;
            }
        }
        $result_buah = [];
        $addeds = false;
        foreach ($filteredBuah as $key => $value) {
            $result_buah[$key] = $value;
            if ($key === $insertAfter && !$addeds) {
                foreach ($chartBuahTTPlas as $k => $v) {
                    $result_buah[$k] = $v;
                }
                $addeds = true;
            }
        }


        // grafik untuk mutu buah
        //  dd($mtBuahtab1Wil_reg);
        $chrtBuahMentah = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahMentah[$key2] = $value2['total_perMentah'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahMentahv2 = [];
        foreach ($chrtBuahMentah as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahMentahv2[$key] = $value;
            }
        }
        //   dd($chrtBuahMentahv2);
        $chrtBuahMsk = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahMsk[$key2] = $value2['total_perMasak'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahMskv2 = [];
        foreach ($chrtBuahMsk as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahMskv2[$key] = $value;
            }
        }
        $chrtBuahOver = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahOver[$key2] = $value2['total_perOver'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahOverv2 = [];
        foreach ($chrtBuahOver as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahOverv2[$key] = $value;
            }
        }
        $chrtBuahAbr = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahAbr[$key2] = $value2['total_perabnormal'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahAbrv2 = [];
        foreach ($chrtBuahAbr as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahAbrv2[$key] = $value;
            }
        }
        $chrtBuahKosng = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahKosng[$key2] = $value2['total_perKosongjjg'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahKosongv2 = [];
        foreach ($chrtBuahKosng as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahKosongv2[$key] = $value;
            }
        }
        $chrtBuahVcut = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrtBuahVcut[$key2] = $value2['perVcut'];
            }
        }
        //  untuk mengahous key LDE etc 
        $chrtBuahVcutv2 = [];
        foreach ($chrtBuahVcut as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrtBuahVcutv2[$key] = $value;
            }
        }



        // dd($mtAncakMua);
        $arrBuahMentah = [
            'pt_mua' => $mtBuahMua['total_perMentah']
        ];
        $arrBuahMasak = [
            'pt_mua' => $mtBuahMua['total_perMasak']
        ];
        $arrBuahMOver = [
            'pt_mua' => $mtBuahMua['total_perOver']
        ];
        $arrBuahAbnrm = [
            'pt_mua' => $mtBuahMua['total_perabnormal']
        ];
        $arrBuahKosong = [
            'pt_mua' => $mtBuahMua['total_perKosongjjg']
        ];
        $arrBuahVcut = [
            'pt_mua' => $mtBuahMua['per_vcut']
        ];

        // membuat pt muah satu array dan di bagian bawah
        $chrtBuahMentahv2["pt_mua"] = $arrBuahMentah["pt_mua"];
        $chrtBuahMskv2["pt_mua"] = $arrBuahMasak["pt_mua"];
        $chrtBuahOverv2["pt_mua"] = $arrBuahMOver["pt_mua"];
        $chrtBuahAbrv2["pt_mua"] = $arrBuahAbnrm["pt_mua"];
        $chrtBuahKosongv2["pt_mua"] = $arrBuahKosong["pt_mua"];
        $chrtBuahVcutv2["pt_mua"] = $arrBuahVcut["pt_mua"];





        $willBuah_Mentah = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Mentah[$key] = $value['total_perMentah'];
        }
        $willBuah_Masak = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Masak[$key] = $value['total_perMasak'];
        }
        $willBuah_Over = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Over[$key] = $value['total_perOver'];
        }

        $willBuah_Abr = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Abr[$key] = $value['total_perabnormal'];
        }
        $willBuah_Kosong = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Kosong[$key] = $value['total_perKosongjjg'];
        }
        $willBuah_Vcut = array();
        foreach ($mtBuahtab1Wil_reg as $key => $value) {
            $willBuah_Vcut[$key] = $value['per_vcut'];
        }


        // dd($willBuah_Vcut);
        $arrays = [
            &$chrTransbrdv2,
            &$chrTransbuahv2,
            &$chrtBuahMentahv2,
            &$chrtBuahMskv2,
            &$chrtBuahOverv2,
            &$chrtBuahAbrv2,
            &$chrtBuahKosongv2,
            &$chrtBuahVcutv2
        ];
        $insertAfterv2 = $Reg == '1' ? "UPE" : ($Reg == '2' ? "SPE" : "PKE");
        function moveElement(&$array, $key, $insertAfterv2)
        {
            if (!array_key_exists($key, $array) || !array_key_exists($insertAfterv2, $array)) {
                return false;
            }
            $newArray = [];
            foreach ($array as $k => $v) {
                if ($k === $key) continue;
                $newArray[$k] = $v;
                if ($k === $insertAfterv2) {
                    $newArray[$key] = $array[$key];
                }
            }
            $array = $newArray;
            return true;
        }

        //end grafik mutu buah

        // grafik untuk mutu transport

        $chrTransbrd = array();
        foreach ($mtTranstab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrTransbrd[$key2] = $value2['total_brd/TPH'];
            }
        }
        $chrTransbrdv2 = [];
        foreach ($chrTransbrd as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrTransbrdv2[$key] = $value;
            }
        }

        $chrTransbuah = array();
        foreach ($mtTranstab1Wil_reg as $key => $value) {
            foreach ($value as $key2 => $value2) if (is_array($value2)) {

                $chrTransbuah[$key2] = $value2['total_buahPerTPH'];
            }
        }
        $chrTransbuahv2 = [];
        foreach ($chrTransbuah as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $chrTransbuahv2[$key] = $value;
            }
        }


        $arrTransMentah = [
            'pt_mua' => $mtTransMua['total_brd/TPH']
        ];
        $arrTransMasak = [
            'pt_mua' => $mtTransMua['total_buahPerTPH']
        ];

        $chrTransbrdv2["pt_mua"] = $arrTransMentah["pt_mua"];
        $chrTransbuahv2["pt_mua"] = $arrTransMasak["pt_mua"];

        // unset($chrTransbrdv2["pt_mua"]);


        // dd($chrTransbuahv2);

        // Arrays to be modified


        if (in_array($Reg, ['2', '3', 2, 3])) {
            foreach ($arrays as &$array) {
                if (array_key_exists('pt_mua', $array)) {
                    unset($array['pt_mua']);
                }
            }
        }

        foreach ($arrays as &$array) {
            $plasmaKeys = preg_grep('/^Plasma/', array_keys($array));
            foreach ($plasmaKeys as $plasmaKey) {
                moveElement($array, $plasmaKey, $insertAfterv2);
            }
        }

        // transport perwilayah 

        $WilTransBRD = array();
        foreach ($mtTranstab1Wil_reg as $key => $value) {
            $WilTransBRD[$key] = $value['total_brd/TPH'];
        }

        $WilTransBuah = array();
        foreach ($mtTranstab1Wil_reg as $key => $value) {
            $WilTransBuah[$key] = $value['total_buahPerTPH'];
        }



        // Check if $Reg is not equal to 1 or '1'
        if ($Reg != 1 && $Reg != '1') {
            unset($result_brd['pt_muabrd']);
            unset($result_buah['pt_muabuah']);
            unset($chrTransbrdv2['pt_mua']);
            unset($chrTransbuahv2['pt_mua']);
            unset($chrtBuahMentahv2['pt_mua']);
            unset($chrtBuahMskv2['pt_mua']);
            unset($chrtBuahOverv2['pt_mua']);
            unset($chrtBuahAbrv2['pt_mua']);
            unset($chrtBuahKosongv2['pt_mua']);
            unset($chrtBuahVcutv2['pt_mua']);
        }


        // $queryEsta = updateKeyRecursive2($queryEsta);

        // dd($FormatTable1);

        function unsetPlasmaKeys(&$array)
        {
            $keydel = ['Plasma1', 'Plasma2', 'Plasma3', 'Plasma4']; // Array of keys to delete

            foreach ($array as $key => $value) {
                if (in_array($key, $keydel)) {
                    unset($array[$key]); // Unset the key if it exists in $keydel
                }
            }
        }
        unsetPlasmaKeys($result_brd);
        unsetPlasmaKeys($result_buah);

        $arrView = array();
        // dd($result_brd,$chrtBuahMentahv2);
        $arrView['chart_brd'] = $result_brd;
        $arrView['chart_buah'] = $result_buah;
        $arrView['chart_brdwil'] =  $chartPerwil;
        $arrView['chart_buahwil'] = $buahPerwil;
        $arrView['RekapRegTable'] =  $RekapRegTable;
        $arrView['GM_1'] =  $GmWil1;
        $arrView['GM_2'] =  $GmWil2;
        $arrView['GM_3'] =  $GmWil3;
        $arrView['asisten'] =  $queryAsisten;
        $arrView['data_tabelutama'] =  $FormatTable1;
        $arrView['data_tabelkedua'] =  $FormatTable2;
        $arrView['data_tabeketiga'] =  $FormatTable3;
        $arrView['data_Est1'] =  $FormatTabEst1;
        $arrView['data_Est2'] =  $FormatTabEst2;
        $arrView['data_Est3'] =  $FormatTabEst3;
        $arrView['data_GM'] =  $RHGM;
        $arrView['list_estate'] =  $queryEsta;
        $arrView['plasma'] =  $PlasmaEm;
        $arrView['plasmaEM'] =  $PlsamaGMEM;
        $arrView['plasmaGM'] =  $plasmaGM;
        $arrView['pt_mua'] =  $pt_muaSkor;
        $arrView['ptmuaBuah'] =  $ptMuachartBuah;
        $arrView['ptmuaBRD'] =  $ptMuachartBRD;

        // grafik mutu buah
        // dd($chrtBuahMentahv2);

        unsetPlasmaKeys($chrtBuahMentahv2);
        unsetPlasmaKeys($chrtBuahMskv2);
        unsetPlasmaKeys($chrtBuahOverv2);
        unsetPlasmaKeys($chrtBuahAbrv2);
        unsetPlasmaKeys($chrtBuahKosongv2);
        unsetPlasmaKeys($chrtBuahVcutv2);

        $arrView['mtbuah_mentah'] =  $chrtBuahMentahv2;
        $arrView['mtbuah_masak'] =  $chrtBuahMskv2;
        $arrView['mtbuah_over'] =  $chrtBuahOverv2;
        $arrView['mtbuah_abnr'] =  $chrtBuahAbrv2;
        $arrView['mtbuah_ksong'] =  $chrtBuahKosongv2;
        $arrView['mtbuah_vcut'] =  $chrtBuahVcutv2;

        $arrView['willBuah_Mentah'] =  $willBuah_Mentah;
        $arrView['willBuah_Masak'] =  $willBuah_Masak;
        $arrView['willBuah_Over'] =  $willBuah_Over;
        $arrView['willBuah_Abr'] =  $willBuah_Abr;
        $arrView['willBuah_Kosong'] =  $willBuah_Kosong;
        $arrView['willBuah_Vcut'] =  $willBuah_Vcut;
        // grafik mutu transport

        unsetPlasmaKeys($chrTransbrdv2);
        unsetPlasmaKeys($chrTransbuahv2);

        $arrView['mttrans_brd'] =  $chrTransbrdv2;
        $arrView['mttrans_buah'] =  $chrTransbuahv2;
        $arrView['mttrans_wilbrd'] =  $WilTransBRD;
        $arrView['mttrans_wilbuah'] =  $WilTransBuah;
        $arrView['list_asisten'] =  $queryAsisten;
        $arrView['newmua'] =  $newmua;
        // dd($FinalTahun);
        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }
}
