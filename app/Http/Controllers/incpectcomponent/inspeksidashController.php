<?php

namespace App\Http\Controllers\incpectcomponent;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\Session;

require '../app/helpers.php';


class inspeksidashController extends Controller
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

        // dd($mtancaktab1Wil, $mtTranstab1Wil, $mtBuahtab1Wil);


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

    public function filterTahun(Request $request)
    {
        $year = $request->input('year');
        $RegData = $request->input('regData');

        $queryAsisten =  DB::connection('mysql2')->Table('asisten_qc')->get();
        // dd($QueryMTbuahWil);
        //end query
        $queryAsisten = json_decode($queryAsisten, true);
        // Untuk table perhitungan berdasarkan tahun dashbouard utama
        $querySidak = DB::connection('mysql2')->table('mutu_transport')
            ->select("mutu_transport.*")
            // ->where('datetime', 'like', '%' . $getDate . '%')
            // ->where('datetime', 'like', '%' . '2023-01' . '%')
            ->get();
        $DataEstate = $querySidak->groupBy(['estate', 'afdeling']);
        // dd($DataEstate);
        $DataEstate = json_decode($DataEstate, true);


        // dd($queryEste);
        $querytahun = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_ancak_new')
                ->select("mutu_ancak_new.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $RegData)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($querytahun[$key1][$key2])) {
                        $querytahun[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $querytahun[$key1][$key2] = array_merge($querytahun[$key1][$key2], $value2);
                    }
                }
            }
        }

        $queryMTbuah = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_buah')
                ->select("mutu_buah.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_buah.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $RegData)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($queryMTbuah[$key1][$key2])) {
                        $queryMTbuah[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $queryMTbuah[$key1][$key2] = array_merge($queryMTbuah[$key1][$key2], $value2);
                    }
                }
            }
        }


        $queryMTtrans = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_transport')
                ->select("mutu_transport.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $RegData)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($queryMTtrans[$key1][$key2])) {
                        $queryMTtrans[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $queryMTtrans[$key1][$key2] = array_merge($queryMTtrans[$key1][$key2], $value2);
                    }
                }
            }
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


        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $RegData)
            ->get();


        $queryEste = json_decode($queryEste, true);
        // Find the index of the "PLASMA" array in the $queryEste array
        $plasmaIndex = array_search('Plasma1', array_column($queryEste, 'est'));

        // Find the index of the "UPE" array in the $queryEste array
        $upeIndex = array_search('UPE', array_column($queryEste, 'est'));

        // Remove the "PLASMA" array from its current position
        $plasma = array_splice($queryEste, $plasmaIndex, 1);

        // Insert the "PLASMA" array after the "UPE" array
        array_splice($queryEste, $upeIndex + 1, 0, $plasma);

        // dd($queryEste);
        $queryEste2 = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            // ->whereNotIn('estate.est', ['Plasma1', 'CWS1'])
            ->whereNotIn('estate.est', ['CWS1', 'CWS2', 'CWS3', 'Plasma1', 'Plasma2', 'Plasma3'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $RegData)
            ->get();

        $queryEste2 = json_decode($queryEste2, true);
        // dd($queryEste2);
        //end query

        $queryEstePla = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $RegData)
            ->get();

        $queryEstePla = json_decode($queryEstePla, true);
        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        //mutu ancak membuat nilai berdasrakan bulan
        $dataPerBulan = array();
        foreach ($querytahun as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataPerBulan)) {
                        $dataPerBulan[$month] = array();
                    }
                    if (!array_key_exists($key, $dataPerBulan[$month])) {
                        $dataPerBulan[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataPerBulan[$month][$key])) {
                        $dataPerBulan[$month][$key][$key2] = array();
                    }
                    $dataPerBulan[$month][$key][$key2][$key3] = $value3;
                }
            }
        }
        //mutu buah  membuat nilai berdasrakan bulan
        $dataPerBulanMTbh = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataPerBulanMTbh)) {
                        $dataPerBulanMTbh[$month] = array();
                    }
                    if (!array_key_exists($key, $dataPerBulanMTbh[$month])) {
                        $dataPerBulanMTbh[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataPerBulanMTbh[$month][$key])) {
                        $dataPerBulanMTbh[$month][$key][$key2] = array();
                    }
                    $dataPerBulanMTbh[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        // dd($dataPerBulanMTbh);
        //mutu transport memnuat nilai perbulan
        $dataBulananMTtrans = array();
        foreach ($queryMTtrans as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataBulananMTtrans)) {
                        $dataBulananMTtrans[$month] = array();
                    }
                    if (!array_key_exists($key, $dataBulananMTtrans[$month])) {
                        $dataBulananMTtrans[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataBulananMTtrans[$month][$key])) {
                        $dataBulananMTtrans[$month][$key][$key2] = array();
                    }
                    $dataBulananMTtrans[$month][$key][$key2][$key3] = $value3;
                }
            }
        }
        // dd($dataBulananMTtrans);

        //membuat nilai default 0 ke masing masing est-afdeling untuk di timpa nanti
        //membuat array estate -> bulan -> afdeling
        // mutu ancak
        $defaultNew = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultNew[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }


        // dd($defaultNew);
        //mutu buah
        $defaultMTbh = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultMTbh[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }
        // dd($defaultMTbh);
        //mutu transport
        $defaultTrans = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultTrans[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }


        //membuat nilai default untuk table terakhir tahunan EST > AFD

        // dd($defaultMTbh);
        //end  nilai defalt
        //bagian menimpa nilai dengan menggunakan defaultNEw
        //menimpa nilai default dengan value mutu ancak yang ada isinya sehingga yang tidak ada value menjadi 0
        foreach ($defaultNew as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataPerBulan as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultNew[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }



        // dd($defaultNew);
        // menimpa nilai defaultnew dengan value mutu buah yang ada isi nya
        // dd($defaultMTbh, $dataPerBulanMTbh);
        foreach ($defaultMTbh as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataPerBulanMTbh as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultMTbh[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($defaultMTbh);
        //menimpa nilai default mutu transport dengan yang memiliki value
        foreach ($defaultTrans as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataBulananMTtrans as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultTrans[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($defaultTrans);


        //bagian untuk table ke 2 menghitung berdasarkan wilayah
        //mutu ancak membuat nilai berdasrakan bulan
        //membuat nilai default mutu ancak untuk bulan>estate>afdeling>value = 0;
        // dd($dataPerBulan);
        $defPerbulanWil = array();
        foreach ($bulan as $key => $value) {
            foreach ($queryEste as $key2 => $value2) {
                foreach ($queryAfd as $key3 => $value3) {
                    if ($value2['est'] == $value3['est']) {
                        $defPerbulanWil[$value][$value2['est']][$value3['nama']] = 0;
                        // $defPerbulanWil[$value][$value2['est']][$value] = 0;
                    }
                }
            }
        }

        //menimpa nilai default di atas dengan dataperbulan mutu ancak yang ada isinya sehingga yang kosong menjadi 0
        foreach ($dataPerBulan as $key2 => $value2) {
            foreach ($value2 as $key3 => $value3) {
                foreach ($value3 as $key4 => $value4) {
                    // foreach ($defPerbulanWil[$key2][$key3][$key4] as $key => $value) {
                    $defPerbulanWil[$key2][$key3][$key4] = $value4;
                }
            }
        }
        //membuat data mutu ancak berdasarakan wilayah 1,2,3
        $mtAncakWil = array();
        foreach ($queryEste2 as $key => $value) {
            foreach ($defPerbulanWil as $key2 => $value2) {
                // dd($value2);
                foreach ($value2 as $key3 => $value3) {
                    if ($value['est'] == $key3) {
                        $mtAncakWil[$value['wil']][$key2][$key3] = $value3;
                    }
                }
            }
        }

        $newArrayANcak = [];
        foreach ($defaultNew as $key1 => $value1) {
            $newArrayANcak[$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $newArrayANcak[$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $item) {
                            // Change the key "status_panen" to "status_panenMA"
                            $item['status_panenMA'] = $item['status_panen'];
                            unset($item['status_panen']);

                            $item['luas_blokMa'] = $item['luas_blok'];
                            unset($item['luas_blok']);

                            $nestedDate = date('Y-m-d', strtotime($item['datetime'])); // Format datetime as Y-m-d
                            $nestedBlok = $item['blok']; // Group by "blok"

                            if (!isset($newArrayANcak[$key1][$key2][$key3][$nestedDate])) {
                                $newArrayANcak[$key1][$key2][$key3][$nestedDate] = [];
                            }
                            if (!isset($newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok])) {
                                $newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok] = [];
                            }
                            $newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok][] = $item;
                        }
                    } else {
                        $newArrayANcak[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }



        // dd($newArrayANcak['MRE']['June']);

        $newArrayTrans = [];
        foreach ($defaultTrans as $key1 => $value1) {
            $newArrayTrans[$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $newArrayTrans[$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $item) {
                            // Change the key "status_panen" to "status_panenMA"
                            $item['status_panenTran'] = $item['status_panen'];
                            unset($item['status_panen']);

                            $item['luas_blokTrans'] = $item['luas_blok'];
                            unset($item['luas_blok']);

                            $nestedDate = date('Y-m-d', strtotime($item['datetime'])); // Format datetime as Y-m-d
                            $nestedBlok = $item['blok']; // Group by "blok"

                            if (!isset($newArrayTrans[$key1][$key2][$key3][$nestedDate])) {
                                $newArrayTrans[$key1][$key2][$key3][$nestedDate] = [];
                            }
                            if (!isset($newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok])) {
                                $newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok] = [];
                            }
                            $newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok][] = $item;
                        }
                    } else {
                        $newArrayTrans[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }

        // dd($newArrayTrans['MRE']['June']['OC'], $newArrayANcak['MRE']['June']['OC']);
        $mutuTrans = array_replace_recursive($newArrayTrans, $newArrayANcak);


        //    dd($mutuTrans['NKE']['June']);
        $newTransv2 = array();
        foreach ($mutuTrans as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value)) {
                $reg_blok = 0;
                if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) {
                        $wil_blok = 0;
                        if (is_array($value2)) {

                            foreach ($value2 as $key3 => $value3) {

                                if (is_array($value3)) {

                                    $est_blok = 0; // Moved outside the innermost loop
                                    $largestLuasBlokMa = 0;
                                    foreach ($value3 as $key4 => $value4) {
                                        if (is_array($value4)) {
                                            $tot_blok = count($value4);
                                            $new_blok = 0;
                                            $luasBlokHanyaMa = '';
                                            $statusPanenHanyaMa = '';
                                            $status_panen = '';
                                            $luas_blok = 0;
                                            $incHanyaMa = 0;
                                            foreach ($value4 as $key5 => $value5) {
                                                $status_panen = $value5['status_panenMA'] ?? 'kosong';
                                                $luas_blok = $value5['luas_blokMa'] ?? 0;

                                                if (isset($value5['luas_blokMa'])) {
                                                    $luasBlokHanyaMa = $value5['luas_blokMa'];
                                                    $statusPanenHanyaMa = $value5['status_panenMA'];
                                                    $incHanyaMa++;
                                                }
                                            }

                                            if ($luasBlokHanyaMa != '' && $statusPanenHanyaMa != '') {
                                                $newTransv2[$key][$key1][$key2][$key3][$key4]['luas_blok'] = $luasBlokHanyaMa;
                                                $newTransv2[$key][$key1][$key2][$key3][$key4]['status_panen'] = $statusPanenHanyaMa;
                                                $status_panen = $statusPanenHanyaMa;
                                                if (strlen($statusPanenHanyaMa) == 3) {
                                                    $arrStatus = explode(',', $statusPanenHanyaMa);
                                                    $status_panen = $arrStatus[0];
                                                }

                                                if ($status_panen <= 3) {
                                                    $new_blok = round($luasBlokHanyaMa * 1.3, 2);
                                                } else {
                                                    $new_blok = $incHanyaMa;
                                                }
                                            } else {

                                                $status_panen = $status_panen;
                                                if (strlen($status_panen) == 3) {
                                                    $arrStatus = explode(',', $status_panen);
                                                    $status_panen = $arrStatus[0];
                                                }
                                                if ($status_panen <= 3 && $status_panen != 'kosong') {
                                                    $new_blok = round($luas_blok * 1.3, 2);
                                                } else {
                                                    $new_blok = $tot_blok;
                                                }
                                                $newTransv2[$key][$key1][$key2][$key3][$key4]['luas_blok'] = $luas_blok;
                                                $newTransv2[$key][$key1][$key2][$key3][$key4]['status_panen'] = $status_panen;
                                            }
                                            $newTransv2[$key][$key1][$key2][$key3][$key4]['tph_sampleNew'] = $new_blok;


                                            $est_blok += $new_blok;
                                        }
                                    }
                                    $newTransv2[$key][$key1][$key2][$key3]['tph_sampleEst'] = $est_blok;
                                    $wil_blok += $est_blok;
                                }
                            }
                        }
                        $newTransv2[$key][$key1][$key2]['tph_sampleWil'] = $wil_blok;
                        $reg_blok += $wil_blok;
                    }
                }
                $newTransv2[$key][$key1]['tph_sampleReg'] = $reg_blok;
            } else {
                $newTransv2[$key][$key1]['tph_sampleReg'] = 0;
            }
        }


        for ($i = 1; $i <= Carbon::now()->month; $i++) {
            $listExistDataBulan[] = Carbon::create()->month($i)->monthName;
        }
        //perhitungan data untuk mutu transport
        //menghitung afd perbulan
        $allBlokPerMonthTrans = array();
        $mutuTransAFD = array();

        // foreach ($defaultTrans as $key => $value) {
        //     foreach ($value as $key1 => $value2) {
        //         foreach ($value2 as $key2 => $value3)
        //             if (is_array($value3)) {
        //                 $sum_bt = 0;
        //                 $sum_rst = 0;
        //                 $brdPertph = 0;
        //                 $buahPerTPH = 0;
        //                 $totalSkor = 0;
        //                 $dataBLok = 0;
        //                 $listBlokPerAfd = array();
        //                 foreach ($value3 as $key3 => $value4) {
        //                     // dd($value4);
        //                     $allBlokPerMonthTrans[$key][$key1][$key2][$value4['id']] = $value4['blok'];
        //                     // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
        //                     $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
        //                     // }
        //                     $dataBLok = count($listBlokPerAfd);
        //                     $sum_bt += $value4['bt'];
        //                     $sum_rst += $value4['rst'];
        //                 }

        //                 $tot_sample = $dataBLok;
        //                 if ($RegData == 2) {

        //                    foreach ($newTransv2 as $keys => $value)if($keys == $key) {

        //                         foreach ($value as $keys1 => $value1) if($keys1 == $key1){

        //                             foreach ($value1 as $keys2 => $value2) if($keys2 == $key2){



        //                                 //    dd($value3);

        //                                      $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $value2['tph_sampleWil'];
        //                                      $tot_sample = $value2['tph_sampleWil'];



        //                             } 
        //                         } 
        //                    } 
        //                 }else {
        //                     $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $dataBLok;
        //                 }


        //                 if ($RegData == '2' || $RegData == 2) {
        //                     $brdPertph = calculateValue($sum_bt, $tot_sample);
        //                     $buahPerTPH = calculateValue($sum_rst, $tot_sample);
        //                 } else {
        //                     $brdPertph = calculateValue($sum_bt, $dataBLok);
        //                     $buahPerTPH = calculateValue($sum_rst, $dataBLok);
        //                 }





        //                 $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

        //                 if (!empty($nonZeroValues)) {
        //                     $mutuTransAFD[$key][$key1][$key2]['check_data'] = 'ada';

        //                 } else {
        //                     $mutuTransAFD[$key][$key1][$key2]['check_data'] = "kosong";

        //                 }
        //                 // dd($transNewdata);




        //                 $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);
        //                 // $totalSkor =  skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


        //                 $mutuTransAFD[$key][$key1][$key2]['total_brd'] = $sum_bt;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_buah'] = $sum_rst;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;

        //                 $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = $totalSkor;
        //             } else {
        //                 $tot_sample = 0;
        //                 if($RegData == 2){
        //                     foreach ($newTransv2 as $keys => $value)if($keys == $key) {

        //                         foreach ($value as $keys1 => $value1) if($keys1 == $key1){

        //                             foreach ($value1 as $keys2 => $value2) if($keys2 == $key2){
        //                                 $tot_sample = $value2['tph_sampleWil'];
        //                             }
        //                         }
        //                     }

        //                     if (in_array($key1, $listExistDataBulan)) {
        //                         $brdPertph = 0;
        //                         $buahPerTPH = 0;
        //                         $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $tot_sample;


        //                         $mutuTransAFD[$key][$key1][$key2]['totalSkor'] =  skor_brd_tinggal($brdPertph) +  skor_buah_tinggal($buahPerTPH);
        //                     }else{
        //                         $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = 0;
        //                         $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
        //                     }
        //                 } else {
        //                     $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $tot_sample;
        //                     $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
        //                 }
        //                 $mutuTransAFD[$key][$key1][$key2]['total_brd'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_buah'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
        //                 $mutuTransAFD[$key][$key1][$key2]['check_data'] = "reg2";








        //             }
        //     }
        // }

        foreach ($defaultTrans as $key => $value) {
            foreach ($value as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3)
                    if (is_array($value3)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key3 => $value4) {
                            // dd($value4);
                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            // }
                            $dataBLok = count($listBlokPerAfd);
                            $sum_bt += $value4['bt'];
                            $sum_rst += $value4['rst'];
                        }

                        if ($RegData == 2) {

                            foreach ($newTransv2 as $keys => $value) if ($keys == $key) {

                                foreach ($value as $keys1 => $value1) if ($keys1 == $key1) {

                                    foreach ($value1 as $keys2 => $value2) if ($keys2 == $key2) {



                                        //    dd($value3);

                                        $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $value2['tph_sampleWil'];
                                        $dataBLok = $value2['tph_sampleWil'];
                                    }
                                }
                            }
                        } else {
                            $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        }


                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 2);
                        } else {
                            $brdPertph = 0;
                        }
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 2);
                        } else {
                            $buahPerTPH = 0;
                        }

                        // skor_brd_tinggal($brdPertph);
                        // skor_buah_tinggal($buahPerTPH);
                        $nonZeroValues = array_filter([$brdPertph, $buahPerTPH]);

                        if (!empty($nonZeroValues)) {
                            $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                            $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                        } else {
                            $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] = $skor_brd = 0;
                            $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = $skor_buah = 0;
                        }

                        $totalSkor = $skor_brd + $skor_buah;

                        // $totalSkor =  skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        // $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd'] = $sum_bt;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                        $mutuTransAFD[$key][$key1][$key2]['total_buah'] = $sum_rst;
                        $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                        // $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] =  skor_brd_tinggal($brdPertph);
                        // $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                        $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = $totalSkor;
                    } else {
                        if ($RegData == 2) {

                            foreach ($newTransv2 as $keys => $value) if ($keys == $key) {

                                foreach ($value as $keys1 => $value1) if ($keys1 == $key1) {
                                    $total_skor = 0;
                                    foreach ($value1 as $keys2 => $value2) if ($keys2 == $key2) {



                                        //    dd($value3);

                                        $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $value2['tph_sampleWil'];
                                        $dataBLok = $value2['tph_sampleWil'];

                                        if ($dataBLok != 0) {
                                            $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 20;
                                        } else {
                                            $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
                                        }
                                    }
                                }
                            }
                        } else {
                            $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = 0;
                            $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
                        }
                        $mutuTransAFD[$key][$key1][$key2]['total_brd'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_buah'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    }
            }
        }

        // dd($mutuTransAFD['NNE']['July']);
        // dd($newTransv2['NKE']['June']);

        // hitungan per est per bulan
        $mutuTransEst = array();
        foreach ($mutuTransAFD as $key => $value) {
            foreach ($value as $key1 => $value2) {
                $total_sample = 0;
                $total_brd = 0;
                $total_buah = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                if (!empty($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        // dd($value3);
                        $total_sample += $value3['tph_sample'];
                        $total_brd += $value3['total_brd'];
                        $total_buah += $value3['total_buah'];
                    }

                    if ($total_sample != 0) {
                        $brdPertph = round($total_brd / $total_sample, 2);
                    } else {
                        $brdPertph = 0;
                    }

                    if ($total_sample != 0) {
                        $buahPerTPH = round($total_buah / $total_sample, 2);
                    } else {
                        $buahPerTPH = 0;
                    }

                    $nonZeroValues = array_filter([$brdPertph, $buahPerTPH]);

                    if (!empty($nonZeroValues)) {
                        $mutuTransEst[$key][$key1]['skor_brd'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                        $mutuTransEst[$key][$key1]['skor_buah'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                    } else {
                        $mutuTransEst[$key][$key1]['skor_brd'] = $skor_brd = 0;
                        $mutuTransEst[$key][$key1]['skor_buah'] = $skor_buah = 0;
                    }

                    $totalSkor = $skor_brd + $skor_buah;
                    // $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $mutuTransEst[$key][$key1]['total_sampleEST'] = $total_sample;
                    $mutuTransEst[$key][$key1]['total_brdEST'] = $total_brd;
                    $mutuTransEst[$key][$key1]['total_brdPertphEST'] = $brdPertph;
                    $mutuTransEst[$key][$key1]['total_buahEST'] = $total_buah;
                    $mutuTransEst[$key][$key1]['total_buahPertphEST'] = $buahPerTPH;
                    // $mutuTransEst[$key][$key1]['skor_brd'] = skor_brd_tinggal($brdPertph);
                    // $mutuTransEst[$key][$key1]['skor_buah'] = skor_buah_tinggal($buahPerTPH);
                    $mutuTransEst[$key][$key1]['total_skor_trans'] = $totalSkor;
                } else {
                    $mutuTransEst[$key][$key1]['total_sampleEST'] = 0;
                    $mutuTransEst[$key][$key1]['total_brdEST'] = 0;
                    $mutuTransEst[$key][$key1]['total_brdPertphEST'] = 0;
                    $mutuTransEst[$key][$key1]['total_buahEST'] = 0;
                    $mutuTransEst[$key][$key1]['total_buahPertphEST'] = 0;
                    $mutuTransEst[$key][$key1]['skor_brd'] = 0;
                    $mutuTransEst[$key][$key1]['skor_buah'] = 0;
                    $mutuTransEst[$key][$key1]['total_skor_trans'] = 0;
                }
            }
        }

        // dd($mutuTransEst['SLE']);
        //menghitung estate per tahun
        $mutuTransTahun = array();
        foreach ($mutuTransEst as $key => $value)
            if (!empty($value)) {
                $sum_brd = 0;
                $sum_buah = 0;
                $sum_TPH = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                foreach ($value as $key1 => $value2) {
                    // dd($value2);
                    $sum_brd += $value2['total_brdEST'];
                    $sum_buah += $value2['total_buahEST'];
                    $sum_TPH += $value2['total_sampleEST'];
                }

                if ($sum_TPH != 0) {
                    $brdPertph = round($sum_brd / $sum_TPH, 2);
                } else {
                    $brdPertph = 0;
                }


                if ($sum_TPH != 0) {
                    $buahPerTPH = round($sum_buah / $sum_TPH, 2);
                } else {
                    $buahPerTPH = 0;
                }

                $nonZeroValues = array_filter([$brdPertph, $buahPerTPH]);

                if (!empty($nonZeroValues)) {
                    $mutuTransTahun[$key]['skor_brd'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                    $mutuTransTahun[$key]['skor_buah'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                } else {
                    $mutuTransTahun[$key]['skor_brd'] = $skor_brd = 0;
                    $mutuTransTahun[$key]['skor_buah'] = $skor_buah = 0;
                }

                $totalSkor = $skor_brd + $skor_buah;
                // $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


                $mutuTransTahun[$key]['total_brd'] = $sum_brd;
                $mutuTransTahun[$key]['total_brdPerTPH'] = $brdPertph;
                $mutuTransTahun[$key]['total_buah'] = $sum_buah;
                $mutuTransTahun[$key]['buahPerTPh'] = $buahPerTPH;
                $mutuTransTahun[$key]['total_sample'] = $sum_TPH;
                // $mutuTransTahun[$key]['skor_brd'] = skor_brd_tinggal($brdPertph);
                // $mutuTransTahun[$key]['skor_buah'] = skor_buah_tinggal($buahPerTPH);
                $mutuTransTahun[$key]['skor_total'] = $totalSkor;
            } else {
                $mutuTransTahun[$key]['total_brd']  = 0;
                $mutuTransTahun[$key]['total_brdPerTPH'] = 0;
                $mutuTransTahun[$key]['total_buah'] = 0;
                $mutuTransTahun[$key]['buahPerTPh'] = 0;
                $mutuTransTahun[$key]['total_sample'] = 0;
                $mutuTransTahun[$key]['skor_brd']  = 0;
                $mutuTransTahun[$key]['skor_buah']  = 0;
                $mutuTransTahun[$key]['skor_total']  = 0;
            }
        // dd($mutuTransTahun['Plasma1'], $mutuTransEst['Plasma1']['March']);
        // untuk hitung hitungan 
        //perhitungan data untuk mutu buah afd per bulan
        $bulananBh = array();
        foreach ($defaultMTbh as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)
                    if (is_array($value2)) {
                        $sum_bmt = 0;
                        $sum_bmk = 0;
                        $sum_over = 0;
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
                        $no_Vcut = 0;
                        $jml_mth = 0;
                        $jml_mtg = 0;
                        $listBlokPerAfd = array();
                        $dataBLok = 0;
                        $listBlokPerAfd = [];
                        foreach ($value2 as $key3 => $value3) {

                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                            // }
                            $dtBlok = count($listBlokPerAfd);
                            $sum_bmt += $value3['bmt'];
                            $sum_bmk += $value3['bmk'];
                            $sum_over += $value3['overripe'];
                            $sum_kosongjjg += $value3['empty_bunch'];
                            $sum_vcut += $value3['vcut'];
                            $sum_kr += $value3['alas_br'];
                            $sum_Samplejjg += $value3['jumlah_jjg'];
                            $sum_abnor += $value3['abnormal'];
                        }

                        $jml_mth = ($sum_bmt + $sum_bmk);
                        $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                        // $dataBLok += $dtBlok;
                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dtBlok, 2);
                        } else {
                            $total_kr = 0;
                        }

                        $no_Vcut = $sum_Samplejjg - $sum_vcut;
                        $per_kr = round($total_kr * 100, 2);
                        if ($jml_mth != 0) {
                            $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        } else {
                            $PerMth = 0;
                        }
                        if ($jml_mtg != 0) {
                            $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        } else {
                            $PerMsk = 0;
                        }
                        if ($sum_over != 0) {
                            $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        } else {
                            $PerOver = 0;
                        }
                        if ($sum_kosongjjg != 0) {
                            $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        } else {
                            $Perkosongjjg = 0;
                        }
                        if ($sum_vcut != 0) {
                            $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                        } else {
                            $PerVcut = 0;
                        }

                        if ($sum_abnor != 0) {
                            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                        } else {
                            $PerAbr = 0;
                        }

                        $nonZeroValues = array_filter([$sum_Samplejjg, $jml_mth, $jml_mtg, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

                        if (!empty($nonZeroValues)) {
                            $bulananBh[$key][$key1][$key2]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                            $bulananBh[$key][$key1][$key2]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                            $bulananBh[$key][$key1][$key2]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                            $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                            $bulananBh[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                            $bulananBh[$key][$key1][$key2]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                        } else {
                            $bulananBh[$key][$key1][$key2]['skor_mentah'] = $skor_mentah = 0;
                            $bulananBh[$key][$key1][$key2]['skor_masak'] = $skor_masak = 0;
                            $bulananBh[$key][$key1][$key2]['skor_over'] = $skor_over = 0;
                            $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                            $bulananBh[$key][$key1][$key2]['skor_vcut'] = $skor_vcut =  0;
                            $bulananBh[$key][$key1][$key2]['skor_kr'] = $skor_kr = 0;
                        }

                        $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
                        // $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                        $bulananBh[$key][$key1][$key2]['tph_baris_blok'] = $dtBlok;
                        $bulananBh[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                        $bulananBh[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                        $bulananBh[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                        $bulananBh[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                        $bulananBh[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                        $bulananBh[$key][$key1][$key2]['total_over'] = $sum_over;
                        $bulananBh[$key][$key1][$key2]['total_perOver'] = $PerOver;
                        $bulananBh[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                        $bulananBh[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                        $bulananBh[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                        $bulananBh[$key][$key1][$key2]['total_vcut'] = $sum_vcut;

                        $bulananBh[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                        $bulananBh[$key][$key1][$key2]['total_kr'] = $total_kr;
                        $bulananBh[$key][$key1][$key2]['persen_kr'] = $per_kr;

                        // skoring
                        // $bulananBh[$key][$key1][$key2]['skor_mentah'] =  skor_buah_mentah_mb($PerMth);
                        // $bulananBh[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                        // $bulananBh[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                        // $bulananBh[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);
                        // $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                        // $bulananBh[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                        $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;
                        // if ($totalSkor != 0) {
                        //     $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;
                        // } else {
                        //     $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = 25;
                        // }
                    } else {

                        $bulananBh[$key][$key1][$key2]['tph_baris_blok'] = 0;
                        $bulananBh[$key][$key1][$key2]['sampleJJG_total'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_mentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perMentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_masak'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perMasak'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_over'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perOver'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_abnormal'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_jjgKosong'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_vcut'] = 0;
                        $bulananBh[$key][$key1][$key2]['jum_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['persen_kr'] = 0;

                        // skoring
                        $bulananBh[$key][$key1][$key2]['skor_mentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_masak'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_over'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_vcut'] = 0;

                        $bulananBh[$key][$key1][$key2]['skor_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                        // if ($totalSkor != 0) {
                        //     $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = 25;
                        // } else {
                        //     $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = 25;
                        // }
                    }
            }
        }
        // dd($bulananBh['Plasma1']['April']);
        //mutu buah perbulan per estate
        $bulananEST = array();
        foreach ($bulananBh as $key => $value) {
            foreach ($value as $key1 => $value2)
                if (!empty($value2)) {
                    $tph_blok = 0;
                    $jjgMth = 0;
                    $sampleJJG = 0;
                    $jjgAbn = 0;
                    $PerMth = 0;
                    $PerMsk = 0;
                    $PerOver = 0;
                    $Perkosongjjg = 0;
                    $PerVcut = 0;
                    $PerAbr = 0;
                    $per_kr = 0;
                    $jjgMsk = 0;
                    $jjgOver = 0;
                    $jjgKosng = 0;
                    $vcut = 0;
                    $jum_kr = 0;
                    $total_kr = 0;
                    $totalSkor = 0;
                    $no_Vcut = 0;

                    foreach ($value2 as $key2 => $value3) {
                        // dd($value3);
                        $tph_blok += $value3['tph_baris_blok'];
                        $sampleJJG += $value3['sampleJJG_total'];
                        $jjgMth += $value3['total_mentah'];
                        $jjgMsk += $value3['total_masak'];
                        $jjgOver += $value3['total_over'];
                        $jjgKosng += $value3['total_jjgKosong'];
                        $vcut += $value3['total_vcut'];
                        $jum_kr += $value3['jum_kr'];

                        $jjgAbn += $value3['total_abnormal'];
                    }
                    $no_Vcut = $sampleJJG - $vcut;
                    if ($jum_kr != 0) {
                        $total_kr = round($jum_kr / $tph_blok, 2);
                    } else {
                        $total_kr = 0;
                    }

                    if ($jjgMth != 0) {
                        $PerMth = round(($jjgMth / ($sampleJJG - $jjgAbn)) * 100, 2);
                    } else {
                        $PerMth = 0;
                    }

                    if ($jjgMsk != 0) {
                        $PerMsk = round(($jjgMsk / ($sampleJJG - $jjgAbn)) * 100, 2);
                    } else {
                        $PerMsk = 0;
                    }

                    if ($jjgOver != 0) {
                        $PerOver = round(($jjgOver / ($sampleJJG - $jjgAbn)) * 100, 2);
                    } else {
                        $PerOver = 0;
                    }

                    if ($jjgKosng != 0) {
                        $Perkosongjjg = round(($jjgKosng / ($sampleJJG - $jjgAbn)) * 100, 2);
                    } else {
                        $Perkosongjjg = 0;
                    }

                    if ($vcut != 0) {
                        $PerVcut = round(($vcut / $sampleJJG) * 100, 2);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($jjgAbn != 0) {
                        $PerAbr = round(($jjgAbn / $sampleJJG) * 100, 2);
                    } else {
                        $PerAbr = 0;
                    }

                    $per_kr = round($total_kr * 100, 2);

                    $nonZeroValues = array_filter([$sampleJJG, $jjgMth, $jjgMsk, $jjgOver, $jjgAbn, $jjgKosng, $vcut]);

                    if (!empty($nonZeroValues)) {
                        $bulananEST[$key][$key1]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                        $bulananEST[$key][$key1]['skor_msak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                        $bulananEST[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                        $bulananEST[$key][$key1]['skor_kosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                        $bulananEST[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                        $bulananEST[$key][$key1]['skor_karung'] = $skor_kr =  skor_abr_mb($per_kr);
                    } else {
                        $bulananEST[$key][$key1]['skor_mentah'] = $skor_mentah = 0;
                        $bulananEST[$key][$key1]['skor_msak'] = $skor_masak = 0;
                        $bulananEST[$key][$key1]['skor_over'] = $skor_over = 0;
                        $bulananEST[$key][$key1]['skor_kosong'] = $skor_jjgKosong = 0;
                        $bulananEST[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                        $bulananEST[$key][$key1]['skor_karung'] = $skor_kr = 0;
                    }

                    $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
                    // $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_jangkos_mb($Perkosongjjg) + skor_vcut_mb($PerVcut) + skor_abr_mb($per_kr);

                    $bulananEST[$key][$key1]['blok'] = $tph_blok;
                    $bulananEST[$key][$key1]['sample_jjg'] = $sampleJJG;

                    $bulananEST[$key][$key1]['jjg_mentah'] = $jjgMth;
                    $bulananEST[$key][$key1]['mentahPerjjg'] = $PerMth;

                    $bulananEST[$key][$key1]['jjg_msk'] = $jjgMsk;
                    $bulananEST[$key][$key1]['mskPerjjg'] = $PerMsk;

                    $bulananEST[$key][$key1]['jjg_over'] = $jjgOver;
                    $bulananEST[$key][$key1]['overPerjjg'] = $PerOver;

                    $bulananEST[$key][$key1]['jjg_kosong'] = $jjgKosng;
                    $bulananEST[$key][$key1]['kosongPerjjg'] = $Perkosongjjg;

                    $bulananEST[$key][$key1]['v_cut'] = $vcut;
                    $bulananEST[$key][$key1]['vcutPerjjg'] = $PerVcut;

                    $bulananEST[$key][$key1]['jjg_abr'] = $jjgAbn;
                    $bulananEST[$key][$key1]['krPer'] = $per_kr;

                    $bulananEST[$key][$key1]['jum_kr'] = $jum_kr;
                    $bulananEST[$key][$key1]['abrPerjjg'] = $PerAbr;

                    // $bulananEST[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    // $bulananEST[$key][$key1]['skor_msak'] = skor_buah_masak_mb($PerMsk);
                    // $bulananEST[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);
                    // $bulananEST[$key][$key1]['skor_kosong'] = skor_jangkos_mb($Perkosongjjg);
                    // $bulananEST[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);
                    // $bulananEST[$key][$key1]['skor_karung'] = skor_abr_mb($per_kr);
                    $bulananEST[$key][$key1]['totalSkor_buah'] = $totalSkor;
                } else {
                    $bulananEST[$key][$key1]['blok'] = 0;
                    $bulananEST[$key][$key1]['sample_jjg'] = 0;

                    $bulananEST[$key][$key1]['jjg_mentah'] = 0;
                    $bulananEST[$key][$key1]['mentahPerjjg'] = 0;

                    $bulananEST[$key][$key1]['jjg_msk'] = 0;
                    $bulananEST[$key][$key1]['mskPerjjg'] = 0;

                    $bulananEST[$key][$key1]['jjg_over'] = 0;
                    $bulananEST[$key][$key1]['overPerjjg'] = 0;

                    $bulananEST[$key][$key1]['jjg_kosong'] = 0;
                    $bulananEST[$key][$key1]['kosongPerjjg'] = 0;

                    $bulananEST[$key][$key1]['v_cut'] = 0;
                    $bulananEST[$key][$key1]['vcutPerjjg'] = 0;

                    $bulananEST[$key][$key1]['jjg_abr'] = 0;
                    $bulananEST[$key][$key1]['krPer'] = 0;

                    $bulananEST[$key][$key1]['jum_kr'] = 0;
                    $bulananEST[$key][$key1]['abrPerjjg'] = 0;

                    $bulananEST[$key][$key1]['skor_mentah'] = 0;
                    $bulananEST[$key][$key1]['skor_msak'] =  0;
                    $bulananEST[$key][$key1]['skor_over'] = 0;
                    $bulananEST[$key][$key1]['skor_kosong'] = 0;
                    $bulananEST[$key][$key1]['skor_vcut'] = 0;
                    $bulananEST[$key][$key1]['skor_karung'] = 0;

                    $bulananEST[$key][$key1]['totalSkor_buah'] = 0;
                }
        }

        // dd($bulananEST);
        // mutu buah pertahun 
        $TahunMtBuah = array();
        foreach ($bulananEST as $key => $value)
            if (!empty($value)) {
                $tph_blok = 0;
                $jjgMth = 0;
                $sampleJJG = 0;
                $jjgAbn = 0;
                $PerMth = 0;
                $PerMsk = 0;
                $PerOver = 0;
                $Perkosongjjg = 0;
                $PerVcut = 0;
                $PerAbr = 0;
                $per_kr = 0;
                $jjgMsk = 0;
                $jjgOver = 0;
                $jjgKosng = 0;
                $vcut = 0;
                $jum_kr = 0;
                $total_kr = 0;
                $totalSkor = 0;
                $no_Vcut = 0;
                foreach ($value as $key2 => $value2) {
                    $tph_blok += $value2['blok'];
                    $sampleJJG += $value2['sample_jjg'];
                    $jjgMth += $value2['jjg_mentah'];
                    $jjgMsk += $value2['jjg_msk'];
                    $jjgOver += $value2['jjg_over'];
                    $jjgKosng += $value2['jjg_kosong'];
                    $vcut += $value2['v_cut'];
                    $jum_kr += $value2['jum_kr'];

                    $jjgAbn += $value2['jjg_abr'];
                }
                $no_Vcut = $sampleJJG - $vcut;
                if ($jum_kr != 0) {
                    $total_kr = round($jum_kr / $tph_blok, 2);
                } else {
                    $total_kr = 0;
                }

                if ($jjgMth != 0) {
                    $PerMth = round(($jjgMth / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMth = 0;
                }

                if ($jjgMsk != 0) {
                    $PerMsk = round(($jjgMsk / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }

                if ($jjgOver != 0) {
                    $PerOver = round(($jjgOver / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerOver = 0;
                }

                if ($jjgKosng != 0) {
                    $Perkosongjjg = round(($jjgKosng / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }

                if ($vcut != 0) {
                    $PerVcut = round(($vcut / $sampleJJG) * 100, 2);
                } else {
                    $PerVcut = 0;
                }

                if ($jjgAbn != 0) {
                    $PerAbr = round(($jjgAbn / $sampleJJG) * 100, 2);
                } else {
                    $PerAbr = 0;
                }

                $per_kr = round($total_kr * 100, 2);


                $nonZeroValues = array_filter([$sampleJJG, $jjgMth, $jjgMsk, $jjgOver, $jjgAbn, $jjgKosng, $vcut]);

                if (!empty($nonZeroValues)) {
                    $TahunMtBuah[$key]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                    $TahunMtBuah[$key]['skor_msak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                    $TahunMtBuah[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                    $TahunMtBuah[$key]['skor_kosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                    $TahunMtBuah[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                    $TahunMtBuah[$key]['skor_karung'] = $skor_kr =  skor_abr_mb($per_kr);
                } else {
                    $TahunMtBuah[$key]['skor_mentah'] = $skor_mentah = 0;
                    $TahunMtBuah[$key]['skor_msak'] = $skor_masak = 0;
                    $TahunMtBuah[$key]['skor_over'] = $skor_over = 0;
                    $TahunMtBuah[$key]['skor_kosong'] = $skor_jjgKosong = 0;
                    $TahunMtBuah[$key]['skor_vcut'] = $skor_vcut =  0;
                    $TahunMtBuah[$key]['skor_karung'] = $skor_kr = 0;
                }

                $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
                // $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                $TahunMtBuah[$key]['blok'] = $tph_blok;
                $TahunMtBuah[$key]['sample_jjg'] = $sampleJJG;

                $TahunMtBuah[$key]['jjg_mentah'] = $jjgMth;
                $TahunMtBuah[$key]['mentahPerjjg'] = $PerMth;

                $TahunMtBuah[$key]['jjg_msk'] = $jjgMsk;
                $TahunMtBuah[$key]['mskPerjjg'] = $PerMsk;

                $TahunMtBuah[$key]['jjg_over'] = $jjgOver;
                $TahunMtBuah[$key]['overPerjjg'] = $PerOver;

                $TahunMtBuah[$key]['jjg_kosong'] = $jjgKosng;
                $TahunMtBuah[$key]['kosongPerjjg'] = $Perkosongjjg;

                $TahunMtBuah[$key]['v_cut'] = $vcut;
                $TahunMtBuah[$key]['vcutPerjjg'] = $PerVcut;

                $TahunMtBuah[$key]['jjg_abr'] = $jjgAbn;
                $TahunMtBuah[$key]['krPer'] = $per_kr;

                $TahunMtBuah[$key]['jum_kr'] = $jum_kr;
                $TahunMtBuah[$key]['abrPerjjg'] = $PerAbr;

                // $TahunMtBuah[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                // $TahunMtBuah[$key]['skor_msak'] =  skor_buah_masak_mb($PerMsk);
                // $TahunMtBuah[$key]['skor_over'] = skor_buah_over_mb($PerOver);
                // $TahunMtBuah[$key]['skor_kosong'] = skor_jangkos_mb($Perkosongjjg);
                // $TahunMtBuah[$key]['skor_vcut'] = skor_vcut_mb($PerVcut);
                // $TahunMtBuah[$key]['skor_karung'] = skor_abr_mb($per_kr);

                $TahunMtBuah[$key]['totalTahun_skor'] = $totalSkor;
            } else {
                $TahunMtBuah[$key]['blok'] = 0;
                $TahunMtBuah[$key]['sample_jjg'] = 0;

                $TahunMtBuah[$key]['jjg_mentah'] = 0;
                $TahunMtBuah[$key]['mentahPerjjg'] = 0;

                $TahunMtBuah[$key]['jjg_msk'] = 0;
                $TahunMtBuah[$key]['mskPerjjg'] = 0;

                $TahunMtBuah[$key]['jjg_over'] = 0;
                $TahunMtBuah[$key]['overPerjjg'] = 0;

                $TahunMtBuah[$key]['jjg_kosong'] = 0;
                $TahunMtBuah[$key]['kosongPerjjg'] = 0;

                $TahunMtBuah[$key]['v_cut'] = 0;
                $TahunMtBuah[$key]['vcutPerjjg'] = 0;

                $TahunMtBuah[$key]['jjg_abr'] = 0;
                $TahunMtBuah[$key]['krPer'] = 0;

                $TahunMtBuah[$key]['jum_kr'] = 0;
                $TahunMtBuah[$key]['abrPerjjg'] = 0;

                $TahunMtBuah[$key]['skor_mentah'] = 0;
                $TahunMtBuah[$key]['skor_msak'] =  0;
                $TahunMtBuah[$key]['skor_over'] = 0;
                $TahunMtBuah[$key]['skor_kosong'] = 0;
                $TahunMtBuah[$key]['skor_vcut'] = 0;
                $TahunMtBuah[$key]['skor_karung'] = 0;
                $TahunMtBuah[$key]['skor_abnormal'] = 0;
                $TahunMtBuah[$key]['totalTahun_skor'] = 0;
            }

        // dd($TahunMtBuah);
        // dd($bulananEST['Plasma1']['March'], $TahunMtBuah['Plasma1']);
        //end perhitungan data untuk mutu buah

        //perhitungan data untu mutu ancak
        //hitung per afdeling 
        $dataTahunEst = array();
        foreach ($defaultNew as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
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
                    foreach ($value3 as $key4 => $value4) if (is_array($value4)) {
                        if (!in_array($value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);


                        $totalPokok += $value4["sample"];
                        $totalPanen += $value4["jjg"];
                        $totalP_panen += $value4["brtp"];
                        $totalK_panen +=  $value4["brtk"];
                        $totalPTgl_panen += $value4["brtgl"];

                        $totalbhts_panen += $value4["bhts"];
                        $totalbhtm1_panen += $value4["bhtm1"];
                        $totalbhtm2_panen += $value4["bhtm2"];
                        $totalbhtm3_oanen += $value4["bhtm3"];
                        $totalpelepah_s += $value4["ps"];
                    }
                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }

                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 2);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 2);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                    } else {
                        $perPl = 0;
                    }

                    //
                    $nonZeroValues = array_filter([$totalP_panen, $totalK_panen, $totalPTgl_panen, $totalbhts_panen, $totalbhtm1_panen, $totalbhtm2_panen, $totalbhtm3_oanen]);

                    if (!empty($nonZeroValues)) {
                        $dataTahunEst[$key][$key2][$key3]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBH);
                        $dataTahunEst[$key][$key2][$key3]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                        $dataTahunEst[$key][$key2][$key3]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                    } else {
                        $dataTahunEst[$key][$key2][$key3]['skor_bh'] = $skor_bh = 0;
                        $dataTahunEst[$key][$key2][$key3]['skor_brd'] = $skor_brd = 0;
                        $dataTahunEst[$key][$key2][$key3]['skor_ps'] = $skor_ps = 0;
                    }

                    $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;

                    // $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                    $dataTahunEst[$key][$key2][$key3]['pokok_sample'] = $totalPokok;
                    $dataTahunEst[$key][$key2][$key3]['ha_sample'] = $jum_ha;
                    $dataTahunEst[$key][$key2][$key3]['jumlah_panen'] = $totalPanen;
                    $dataTahunEst[$key][$key2][$key3]['akp_rl'] = $akp;

                    $dataTahunEst[$key][$key2][$key3]['p'] = $totalP_panen;
                    $dataTahunEst[$key][$key2][$key3]['k'] = $totalK_panen;
                    $dataTahunEst[$key][$key2][$key3]['tgl'] = $totalPTgl_panen;

                    // $dataTahunEst[$key][$key2][$key3]['total_brd'] = $skor_bTinggal;
                    $dataTahunEst[$key][$key2][$key3]['brd/jjg'] = $brdPerjjg;
                    // data untuk buah tinggal
                    $dataTahunEst[$key][$key2][$key3]['bhts_s'] = $totalbhts_panen;
                    $dataTahunEst[$key][$key2][$key3]['bhtm1'] = $totalbhtm1_panen;
                    $dataTahunEst[$key][$key2][$key3]['bhtm2'] = $totalbhtm2_panen;
                    $dataTahunEst[$key][$key2][$key3]['bhtm3'] = $totalbhtm3_oanen;
                    $dataTahunEst[$key][$key2][$key3]['buah/jjg'] = $sumPerBH;
                    $dataTahunEst[$key][$key2][$key3]['palepah_pokok'] = $totalpelepah_s;
                    // total skor akhir
                    // $dataTahunEst[$key][$key2][$key3]['skor_bh'] = skor_buah_Ma($sumPerBH);
                    // $dataTahunEst[$key][$key2][$key3]['skor_brd'] = skor_brd_ma($brdPerjjg);
                    // $dataTahunEst[$key][$key2][$key3]['skor_ps'] = skor_palepah_ma($perPl);
                    $dataTahunEst[$key][$key2][$key3]['skor_akhir'] = $ttlSkorMA;
                } else {
                    $dataTahunEst[$key][$key2][$key3]['pokok_sample'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['ha_sample'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['jumlah_panen'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['akp_rl'] = 0;

                    $dataTahunEst[$key][$key2][$key3]['p'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['k'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['tgl'] = 0;

                    // $dataTahunEst[$key][$key2][$key3]['total_brd'] = $skor_bTinggal;
                    $dataTahunEst[$key][$key2][$key3]['brd/jjg'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['buah/jjg'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['skor_brd'] = 0;
                    // data untuk buah tinggal
                    $dataTahunEst[$key][$key2][$key3]['bhts_s'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['bhtm1'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['bhtm2'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['bhtm3'] = 0;

                    $dataTahunEst[$key][$key2][$key3]['skor_bh'] = 0;
                    // $dataTahunEst[$key][$key2][$key3]['jjgperBuah'] = number_format($sumPerBH, 2);
                    // data untuk pelepah sengklek
                    $dataTahunEst[$key][$key2][$key3]['skor_ps'] = 0;
                    $dataTahunEst[$key][$key2][$key3]['palepah_pokok'] = 0;
                    // total skor akhir
                    $dataTahunEst[$key][$key2][$key3]['skor_akhir'] = 0;
                }
            }
        }

        //hitung untuk per estate
        $FinalTahun = array();
        foreach ($dataTahunEst as $key => $value) {
            foreach ($value as $key1 => $value2) {
                $total_brd = 0;
                $total_buah = 0;
                $total_skor = 0;
                $sum_p = 0;
                $sum_k = 0;
                $sum_gl = 0;
                $sum_panen = 0;
                $total_BrdperJJG = 0;
                $sum_pokok = 0;
                $sum_s = 0;
                $sum_m1 = 0;
                $sum_m2 = 0;
                $sum_m3 = 0;
                $sumPerBH = 0;

                $sum_pelepah = 0;
                $perPl = 0;
                foreach ($value2 as $key2 => $value3) {
                    $sum_panen += $value3['jumlah_panen'];
                    $sum_pokok += $value3['pokok_sample'];
                    //brondolamn
                    $sum_p += $value3['p'];
                    $sum_k += $value3['k'];
                    $sum_gl += $value3['tgl'];
                    //buah tianggal
                    $sum_s += $value3['bhts_s'];
                    $sum_m1 += $value3['bhtm1'];
                    $sum_m2 += $value3['bhtm2'];
                    $sum_m3 += $value3['bhtm3'];
                    //pelepah
                    $sum_pelepah += $value3['palepah_pokok'];
                }

                $total_brd = $sum_p + $sum_k + $sum_gl;
                $total_buah = $sum_s + $sum_m1 + $sum_m2 + $sum_m3;

                if ($total_buah != 0) {
                    $sumPerBH = round($total_buah / ($totalPanen + $sumBH) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                if ($sum_pelepah != 0) {
                    $perPl = round(($sum_pelepah / $sum_pokok) * 100, 2);
                } else {
                    $perPl = 0;
                }

                // if ($pokok_panenWil != 0) {
                //     $perPiWil = round(($pelepah_swil / $pokok_panenWil) * 100, 1);
                // } else {
                //     $perPiWil = 0;
                // }

                if ($total_brd != 0) {
                    $total_BrdperJJG = round($total_brd / $sum_panen, 2);
                } else {
                    $total_BrdperJJG = 0;
                }

                if ($total_buah != 0) {
                    $sumPerBH = round($total_buah / ($sum_panen + $total_buah) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                $nonZeroValues = array_filter([$sum_p, $sum_k, $sum_gl, $sum_s, $sum_m1, $sum_m2, $sum_m3]);

                if (!empty($nonZeroValues)) {
                    $FinalTahun[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($total_BrdperJJG);
                    $FinalTahun[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBH);

                    $FinalTahun[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
                } else {
                    $FinalTahun[$key][$key1]['skor_bh'] = $skor_bh = 0;
                    $FinalTahun[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    $FinalTahun[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                $total_skor = $skor_bh + $skor_brd + $skor_ps;
                // $total_skor =   skor_brd_ma($total_BrdperJJG) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                $FinalTahun[$key][$key1]['total_p.k.gl'] = $total_brd ? $total_brd : 0;
                $FinalTahun[$key][$key1]['total_jumPanen'] = $sum_panen ? $sum_panen : 0;
                $FinalTahun[$key][$key1]['total_jumPokok'] = $sum_pokok ? $sum_pokok : 0;
                $FinalTahun[$key][$key1]['total_brd/jjg'] = $total_BrdperJJG ? $total_BrdperJJG : 0;
                // $FinalTahun[$key][$key1]['skor_brd'] =  skor_brd_ma($total_BrdperJJG) ?  skor_brd_ma($total_BrdperJJG) : 0;
                //buah tinggal
                $FinalTahun[$key][$key1]['s'] = $sum_s  ? $sum_s : 0;
                $FinalTahun[$key][$key1]['m1'] = $sum_m1 ? $sum_m1 : 0;
                $FinalTahun[$key][$key1]['m2'] = $sum_m2 ? $sum_m2 : 0;
                $FinalTahun[$key][$key1]['m3'] = $sum_m3 ? $sum_m3 : 0;
                $FinalTahun[$key][$key1]['total_bh'] = $total_buah ? $total_buah : 0;
                $FinalTahun[$key][$key1]['total_bh/jjg'] = $sumPerBH ? $sumPerBH : 0;
                // $FinalTahun[$key][$key1]['skor_bh'] =    skor_buah_Ma($sumPerBH) ?    skor_buah_Ma($sumPerBH) : 0;
                //palepah sengklek
                $FinalTahun[$key][$key1]['pokok_palepah'] = $sum_pelepah ? $sum_pelepah : 0;
                $FinalTahun[$key][$key1]['perPalepah'] = $perPl ? $perPl : 0;
                // $FinalTahun[$key][$key1]['skor_perPl'] =   skor_palepah_ma($perPl) ?   skor_palepah_ma($perPl) : 0;
                //total skor akhir
                $FinalTahun[$key][$key1]['skor_final_ancak'] = $total_skor ? $total_skor : 0;
            }
        }
        // dd($FinalTahun['PLE']);
        //hitung untuk perbulan per estate
        $Final_end = array();
        foreach ($FinalTahun as $key => $value) {
            $sum_Panen = 0;
            $sum_Pokok = 0;
            $sum_PKGL = 0;
            $BrdperPanen = 0;
            $sum_SM1M2M3 = 0;
            $sumPerBH = 0;
            $sum_pelepah = 0;
            $final_skorTH = 0;
            foreach ($value as $key1 => $value2) {
                $sum_Panen += $value2['total_jumPanen'];
                $sum_Pokok += $value2['total_jumPokok'];
                $sum_PKGL += $value2['total_p.k.gl'];
                $sum_SM1M2M3 += $value2['total_bh'];
                $sum_pelepah += $value2['pokok_palepah'];
            }

            if ($sum_PKGL != 0) {
                $BrdperPanen = round($sum_PKGL / $sum_Panen, 2);
            } else {
                $BrdperPanen = 0;
            }

            if ($sum_SM1M2M3 != 0) {
                $sumPerBH = round($sum_SM1M2M3 / ($sum_Panen + $sum_SM1M2M3) * 100, 2);
            } else {
                $sumPerBH = 0;
            }

            if ($sum_pelepah != 0) {
                $perPl = round(($sum_pelepah / $sum_Pokok) * 100, 2);
            } else {
                $perPl = 0;
            }

            $nonZeroValues = array_filter([$sum_PKGL, $sum_SM1M2M3, $sum_pelepah]);

            if (!empty($nonZeroValues)) {
                $Final_end[$key]['skor_brd'] = $skor_brd = skor_brd_ma($BrdperPanen);
                $Final_end[$key]['skor_buah'] = $skor_bh = skor_buah_Ma($sumPerBH);

                $Final_end[$key]['skor_palepah'] = $skor_ps = skor_palepah_ma($perPl);
            } else {
                $Final_end[$key]['skor_buah'] = $skor_bh = 0;
                $Final_end[$key]['skor_brd'] = $skor_brd = 0;
                $Final_end[$key]['skor_palepah'] = $skor_ps = 0;
            }

            $final_skorTH = $skor_bh + $skor_brd + $skor_ps;

            // $final_skorTH = skor_brd_ma($BrdperPanen) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);
            $Final_end[$key]['tahun/panen'] = $sum_Panen ? $sum_Panen : 0;
            $Final_end[$key]['tahun/pokok'] = $sum_Pokok ? $sum_Pokok : 0;
            $Final_end[$key]['total_brd'] = $sum_PKGL ? $sum_PKGL : 0;
            $Final_end[$key]['total_brd/panen'] = $BrdperPanen ? $BrdperPanen : 0;
            // $Final_end[$key]['skor_brd'] =     skor_brd_ma($BrdperPanen) ? skor_brd_ma($BrdperPanen) : 0;
            $Final_end[$key]['total_buah'] = $sum_SM1M2M3 ? $sum_SM1M2M3 : 0;
            $Final_end[$key]['total_buah/jjg'] = $sumPerBH ? $sumPerBH : 0;
            // $Final_end[$key]['skor_buah'] =     skor_buah_Ma($sumPerBH) ?     skor_buah_Ma($sumPerBH) : 0;
            // $Final_end[$key]['skor_palepah'] =  skor_palepah_ma($perPl) ?  skor_palepah_ma($perPl) : 0;
            $Final_end[$key]['skor_tahun'] = $final_skorTH ? $final_skorTH : 0;
        }
        // dd($Final_end);
        // dd($mutuTransEst['KNE']['April'], $bulananEST['KNE']['April'], $FinalTahun['KNE']['April']);
        // dd($mutuTransEst['RDE']['February'], $bulananEST['RDE']['February'], $FinalTahun['RDE']['February']);

        // end menghitung table untuk data pertahun
        $ancakBulan = array();
        foreach ($FinalTahun as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $ancakBulan{
                    $key}[$key1]['ancak'] = $value1['skor_final_ancak'];
            }
        }
        $transbulan = array();
        foreach ($mutuTransEst as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $transbulan{
                    $key}[$key1]['trans'] = $value1['total_skor_trans'];
            }
        }
        $buahBulan = array();
        foreach ($bulananEST as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $buahBulan{
                    $key}[$key1]['buah'] = $value1['totalSkor_buah'];
            }
        }
        // dd($ancakBulan, $transbulan, $buahBulan);
        // dd($mtBuahtab1Wil['1']['PLE'], $bulananEST['PLE']['February']);
        $RekapBulan = array();
        foreach ($ancakBulan as $key => $value) {
            foreach ($value as $key1 => $ancak) {
                foreach ($transbulan as $key2 => $value1) {
                    foreach ($value1 as $key3 => $trans) {
                        foreach ($buahBulan as $key4 => $value2)
                            if ($key == $key2 && $key2 == $key4) {
                                foreach ($value2 as $key5 => $buah) if ($key1 == $key3 && $key3 == $key5) {
                                    $RekapBulan[$key][$key1]['bulan_skor'] = $ancak['ancak'] + $trans['trans'] + $buah['buah'];
                                }
                            }
                    }
                }
            }
        }
        // dd($RekapBulan);


        $RekapTahun = array();
        foreach ($Final_end as $key => $value) {
            foreach ($mutuTransTahun as $key2 => $value2) {
                foreach ($TahunMtBuah as $key3 => $value3) {
                    if ($key == $key2 && $key2 == $key3) {
                        $RekapTahun[$key]['tahun_skor'] = $value['skor_tahun'] + $value2['skor_total'] + $value3['totalTahun_skor'];
                    }
                }
            }
        }
        // dd($RekapTahun);



        //perhitungan untuk data perwilayah
        $bulanMTancak = array();
        foreach ($mtAncakWil as $key => $value) {
            foreach ($value as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3) {
                    foreach ($value3 as $key3 => $value4)
                        if (!empty($value4)) {
                            $akp = 0;
                            $totalPKGL = 0;
                            $brdPerjjg = 0;
                            $pokok_panen = 0;
                            $janjang_panen = 0;
                            $p_panen = 0;
                            $k_panen = 0;
                            $brtgl_panen = 0;
                            $bhts_panen  = 0;
                            $bhtm1_panen  = 0;
                            $bhtm2_panen  = 0;
                            $bhtm3_oanen  = 0;
                            $ttlSkorMA = 0;
                            $listBlokPerAfd = array();
                            $jum_ha = 0;
                            $pelepah_s = 0;
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
                            foreach ($value4 as $key4 => $value5) {
                                // dd($value5);
                                if (!in_array($value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'], $listBlokPerAfd)) {
                                    $listBlokPerAfd[] = $value5['estate'] . ' ' . $value5['afdeling'] . ' ' . $value5['blok'];
                                }
                                $jum_ha = count($listBlokPerAfd);


                                $totalPokok += $value5["sample"];;
                                $totalPanen += $value5["jjg"];;
                                $totalP_panen += $value5["brtp"];;
                                $totalK_panen +=  $value5["brtk"];;
                                $totalPTgl_panen += $value5["brtgl"];;

                                $totalbhts_panen += $value5["bhts"];;
                                $totalbhtm1_panen += $value5["bhtm1"];;
                                $totalbhtm2_panen += $value5["bhtm2"];;
                                $totalbhtm3_oanen += $value5["bhtm3"];;

                                $totalpelepah_s += $value5["ps"];;
                            }
                            // $akp = round(($totalPanen / $totalPokok) * 100, 1);

                            if ($totalPokok != 0) {
                                $akp = round(($totalPanen / $totalPokok) * 100, 1);
                            } else {
                                $akp = 0;
                            }

                            $totalPKGL = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                            if ($totalPanen != 0) {
                                $brdPerjjg = round($skor_bTinggal / $totalPanen, 2);
                            } else {
                                $brdPerjjg = 0;
                            }

                            $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                            if ($sumBH != 0) {
                                $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 2);
                            } else {
                                $sumPerBH = 0;
                            }

                            if ($totalpelepah_s != 0) {
                                $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                            } else {
                                $perPl = 0;
                            }

                            $ttlSkorMA = skor_brd_ma($brdPerjjg);
                            +skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);
                            //

                            $bulanMTancak[$key][$key1][$key2][$key3]['pokok_sample'] = $totalPokok;
                            $bulanMTancak[$key][$key1][$key2][$key3]['ha_sample'] = $jum_ha;
                            $bulanMTancak[$key][$key1][$key2][$key3]['jumlah_panen'] = $totalPanen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['akp_rl'] =  $akp;

                            $bulanMTancak[$key][$key1][$key2][$key3]['p'] = $totalP_panen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['k'] = $totalK_panen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['tgl'] = $totalPTgl_panen;

                            // $bulanMTancak[$key][$key2][$key3]['total_brd'] = $skor_bTinggal;
                            $bulanMTancak[$key][$key1][$key2][$key3]['brd/jjg'] = $brdPerjjg;

                            // data untuk buah tinggal
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhts'] = $totalbhts_panen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm1'] = $totalbhtm1_panen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm2'] = $totalbhtm2_panen;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm3'] = $totalbhtm3_oanen;


                            // $bulanMTancak[$key][$key2][$key3]['jjgperBuah'] = number_format($sumPerBH, 2);
                            // data untuk pelepah sengklek

                            $bulanMTancak[$key][$key1][$key2][$key3]['palepah_pokok'] = $totalpelepah_s;
                            // total skor akhir

                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_brd'] = skor_brd_ma($brdPerjjg);
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_bh'] = skor_brd_ma($brdPerjjg);
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_ps'] = skor_brd_ma($brdPerjjg);
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_akhir'] = $ttlSkorMA;
                        } else {

                            $bulanMTancak[$key][$key1][$key2][$key3]['pokok_sample'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['ha_sample'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['jumlah_panen'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['akp_rl'] =  0;

                            $bulanMTancak[$key][$key1][$key2][$key3]['p'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['k'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['tgl'] = 0;

                            // $bulanMTancak[$key][$key2][$key3]['total_brd'] = $skor_bTinggal;
                            $bulanMTancak[$key][$key1][$key2][$key3]['brd/jjg'] = 0;

                            // data untuk buah tinggal
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhts'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm1'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm2'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['bhtm3'] = 0;


                            // $bulanMTancak[$key][$key2][$key3]['jjgperBuah'] = number_format($sumPerBH, 2);
                            // data untuk pelepah sengklek

                            $bulanMTancak[$key][$key1][$key2][$key3]['palepah_pokok'] = 0;
                            // total skor akhi0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_bh'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_ps'] = 0;
                            $bulanMTancak[$key][$key1][$key2][$key3]['skor_akhir'] = 0;
                        }
                }
            }
        }
        // dd($bulanMTancak['1']);
        //membuat perhitungan mutu ancak berdasarkan perbulan > est
        $bulanAncakEST = array();
        foreach ($bulanMTancak as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)
                    if (!empty($value2)) {
                        $pokok_sample = 0;
                        $jum_ha = 0;
                        $pokok_panen = 0;
                        $p_panen = 0;
                        $k_panen = 0;
                        $tgl_panen = 0;
                        $totalPKGL = 0;
                        $brdPerjjg = 0;
                        $bmts = 0;
                        $bhtm1 = 0;
                        $bhtm2 = 0;
                        $bhtm3 = 0;
                        $totalSM123 = 0;
                        $palepah_pokok = 0;
                        $perPl = 0;
                        $sumPerBH = 0;
                        $ttlSkorMA = 0;
                        foreach ($value2 as $key3 => $value3) {
                            // dd($value3);
                            $pokok_sample += $value3['pokok_sample'];
                            $jum_ha += $value3['ha_sample'];
                            $pokok_panen += $value3['jumlah_panen'];
                            $p_panen += $value3['p'];
                            $k_panen += $value3['k'];
                            $tgl_panen += $value3['tgl'];

                            $bmts += $value3['bhts'];
                            $bhtm1 += $value3['bhtm1'];
                            $bhtm2 += $value3['bhtm2'];
                            $bhtm3 += $value3['bhtm3'];

                            $palepah_pokok += $value3['palepah_pokok'];
                        }

                        $totalPKGL = $p_panen + $k_panen + $tgl_panen;

                        if ($pokok_panen != 0) {
                            $brdPerjjg = round($totalPKGL / $pokok_panen, 2);
                        } else {
                            $brdPerjjg = 0;
                        }

                        $totalSM123 = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
                        if ($totalSM123 != 0) {
                            $sumPerBH = round($totalSM123 / ($pokok_panen + $totalSM123) * 100, 2);
                        } else {
                            $sumPerBH = 0;
                        }

                        if ($palepah_pokok != 0) {
                            $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
                        } else {
                            $perPl = 0;
                        }


                        $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                        $bulanAncakEST[$key][$key1][$key2]['pokok_sample'] = $pokok_sample;
                        $bulanAncakEST[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                        $bulanAncakEST[$key][$key1][$key2]['pokok_panen'] = $pokok_panen;
                        $bulanAncakEST[$key][$key1][$key2]['p_panen'] = $p_panen;
                        $bulanAncakEST[$key][$key1][$key2]['k_panen'] = $k_panen;
                        $bulanAncakEST[$key][$key1][$key2]['tgl_panen'] = $tgl_panen;
                        $bulanAncakEST[$key][$key1][$key2]['brdPerjjg'] = $brdPerjjg;


                        $bulanAncakEST[$key][$key1][$key2]['bmts'] = $bmts;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm1'] = $bhtm1;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm2'] = $bhtm2;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm3'] = $bhtm3;
                        $bulanAncakEST[$key][$key1][$key2]['buahPerjjg'] = $sumPerBH;

                        $bulanAncakEST[$key][$key1][$key2]['palepah_pokok'] = $palepah_pokok;
                        $bulanAncakEST[$key][$key1][$key2]['perPl'] = $perPl;
                        $bulanAncakEST[$key][$key1][$key2]['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);
                        $bulanAncakEST[$key][$key1][$key2]['skor_bh'] = skor_buah_Ma($sumPerBH);
                        $bulanAncakEST[$key][$key1][$key2]['skor_perPl'] = skor_palepah_ma($perPl);
                        $bulanAncakEST[$key][$key1][$key2]['total_skor'] = $ttlSkorMA;
                    } else {
                        $bulanAncakEST[$key][$key1][$key2]['pokok_sample'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['ha_sample'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['pokok_panen'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['p_panen'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['k_panen'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['tgl_panen'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['brdPerjjg'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['skor_brdPerjjg'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['bmts'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm1'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm2'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['bhtm3'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['skor_bh'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['palepah_pokok'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['perPl'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['skor_perPl'] = 0;
                        $bulanAncakEST[$key][$key1][$key2]['total_skor'] = 0;;
                    }
            }
        }
        // dd($bulanAncakEST['1']);
        //membuat perhitungan mutu ancak berdasarkan perbulan semua estate
        $bulanAllEST = array();
        foreach ($bulanAncakEST as $key => $value) {
            foreach ($value as $key1 => $value1)
                if (!empty($value1)) {
                    $pokok_sample = 0;
                    $jum_ha = 0;
                    $pokok_panen = 0;
                    $p_panen = 0;
                    $k_panen = 0;
                    $tgl_panen = 0;
                    $totalPKGL = 0;
                    $brdPerjjg = 0;
                    $bmts = 0;
                    $bhtm1 = 0;
                    $bhtm2 = 0;
                    $bhtm3 = 0;
                    $totalSM123 = 0;
                    $palepah_pokok = 0;
                    $perPl = 0;

                    $ttlSkorMA = 0;

                    foreach ($value1 as $key2 => $value2) {
                        // dd($value2);
                        $pokok_sample += $value2['pokok_sample'];
                        $jum_ha += $value2['ha_sample'];
                        $pokok_panen += $value2['pokok_panen'];
                        $p_panen += $value2['p_panen'];
                        $k_panen += $value2['k_panen'];
                        $tgl_panen += $value2['tgl_panen'];

                        $bmts += $value2['bmts'];
                        $bhtm1 += $value2['bhtm1'];
                        $bhtm2 += $value2['bhtm2'];
                        $bhtm3 += $value2['bhtm3'];

                        $palepah_pokok += $value2['palepah_pokok'];
                    }
                    $totalPKGL = $p_panen + $k_panen + $tgl_panen;

                    if ($pokok_panen != 0) {
                        $brdPerjjg = round($totalPKGL / $pokok_panen, 2);
                    } else {
                        $brdPerjjg = 0;
                    }



                    $sumBH = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($pokok_panen + $sumBH) * 100, 2);
                    } else {
                        $sumPerBH = 0;
                    }



                    if ($palepah_pokok != 0) {
                        $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
                    } else {
                        $perPl = 0;
                    }
                    $nonZeroValues = array_filter([$p_panen, $k_panen, $tgl_panen, $bmts, $bhtm1, $bhtm2, $bhtm3]);

                    if (!empty($nonZeroValues)) {
                        $bulanAllEST[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBH);
                        $bulanAllEST[$key][$key1]['skor_brdPerjjg'] = $skor_brd = skor_brd_ma($brdPerjjg);
                        $bulanAllEST[$key][$key1]['skor_perPl'] = $skor_ps = skor_palepah_ma($perPl);
                    } else {
                        $bulanAllEST[$key][$key1]['skor_bh'] = $skor_bh = 0;
                        $bulanAllEST[$key][$key1]['skor_brdPerjjg'] = $skor_brd = 0;
                        $bulanAllEST[$key][$key1]['skor_perPl'] = $skor_ps = 0;
                    }

                    $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
                    // $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);


                    $bulanAllEST[$key][$key1]['pokok_sample'] = $pokok_sample;
                    $bulanAllEST[$key][$key1]['ha_sample'] = $jum_ha;
                    $bulanAllEST[$key][$key1]['pokok_panen'] = $pokok_panen;
                    $bulanAllEST[$key][$key1]['p_panen'] = $p_panen;
                    $bulanAllEST[$key][$key1]['k_panen'] = $k_panen;
                    $bulanAllEST[$key][$key1]['tgl_panen'] = $tgl_panen;
                    $bulanAllEST[$key][$key1]['total_brd'] = $totalPKGL;
                    $bulanAllEST[$key][$key1]['brdPerjjg'] = $brdPerjjg;


                    $bulanAllEST[$key][$key1]['bmts'] = $bmts;
                    $bulanAllEST[$key][$key1]['bhtm1'] = $bhtm1;
                    $bulanAllEST[$key][$key1]['bhtm2'] = $bhtm2;
                    $bulanAllEST[$key][$key1]['bhtm3'] = $bhtm3;
                    $bulanAllEST[$key][$key1]['total_buah'] = $sumBH;
                    $bulanAllEST[$key][$key1]['total_buah_per'] = $sumPerBH;

                    $bulanAllEST[$key][$key1]['palepah_pokok'] = $palepah_pokok;
                    $bulanAllEST[$key][$key1]['perPl'] = $perPl;
                    // $bulanAllEST[$key][$key1]['skor_bh'] = skor_buah_Ma($sumPerBH);
                    // $bulanAllEST[$key][$key1]['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);
                    // $bulanAllEST[$key][$key1]['skor_perPl'] = skor_palepah_ma($perPl);
                    $bulanAllEST[$key][$key1]['total_skor'] = $ttlSkorMA;
                } else {

                    $bulanAllEST[$key][$key1]['pokok_sample'] = 0;
                    $bulanAllEST[$key][$key1]['ha_sample'] = 0;
                    $bulanAllEST[$key][$key1]['pokok_panen'] = 0;
                    $bulanAllEST[$key][$key1]['p_panen'] = 0;
                    $bulanAllEST[$key][$key1]['k_panen'] = 0;
                    $bulanAllEST[$key][$key1]['tgl_panen'] = 0;
                    $bulanAllEST[$key][$key1]['brdPerjjg'] = 0;
                    $bulanAllEST[$key][$key1]['skor_brdPerjjg'] = 0;

                    $bulanAllEST[$key][$key1]['bmts'] = 0;
                    $bulanAllEST[$key][$key1]['bhtm1'] = 0;
                    $bulanAllEST[$key][$key1]['bhtm2'] = 0;
                    $bulanAllEST[$key][$key1]['bhtm3'] = 0;
                    $bulanAllEST[$key][$key1]['skor_bh'] = 0;

                    $bulanAllEST[$key][$key1]['palepah_pokok'] = 0;
                    $bulanAllEST[$key][$key1]['perPl'] = 0;
                    $bulanAllEST[$key][$key1]['skor_perPl'] = 0;
                    $bulanAllEST[$key][$key1]['total_skor'] = 0;
                }
        }

        // dd($bulanAllEST['1']);
        $WilMtAncakThn = array();
        //hitung tahunan mutu ancak untuk perwilayah 
        foreach ($bulanAllEST as $key => $value)
            if (!empty($value)) {
                $pokok_sample = 0;
                $jum_ha = 0;
                $pokok_panen = 0;
                $p_panen = 0;
                $k_panen = 0;
                $tgl_panen = 0;
                $totalPKGL = 0;
                $brdPerjjg = 0;
                $bmts = 0;
                $bhtm1 = 0;
                $bhtm2 = 0;
                $bhtm3 = 0;
                $totalSM123 = 0;
                $palepah_pokok = 0;
                $perPl = 0;
                $sumPerBH = 0;
                $ttlSkorMA = 0;
                foreach ($value as $key1 => $value1) {
                    // dd($value2);
                    $pokok_sample += $value1['pokok_sample'];
                    $jum_ha += $value1['ha_sample'];
                    $pokok_panen += $value1['pokok_panen'];
                    $p_panen += $value1['p_panen'];
                    $k_panen += $value1['k_panen'];
                    $tgl_panen += $value1['tgl_panen'];

                    $bmts += $value1['bmts'];
                    $bhtm1 += $value1['bhtm1'];
                    $bhtm2 += $value1['bhtm2'];
                    $bhtm3 += $value1['bhtm3'];

                    $palepah_pokok += $value1['palepah_pokok'];
                }
                $totalPKGL = $p_panen + $k_panen + $tgl_panen;

                if ($pokok_panen != 0) {
                    $brdPerjjg = round($totalPKGL / $pokok_panen, 2);
                } else {
                    $brdPerjjg = 0;
                }

                $totalSM123 = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
                if ($totalSM123 != 0) {
                    $sumPerBH = round($totalSM123 / ($pokok_panen + $totalSM123) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                if ($palepah_pokok != 0) {
                    $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
                } else {
                    $perPl = 0;
                }
                $nonZeroValues = array_filter([
                    $pokok_panen,
                    $p_panen,
                    $k_panen,
                    $pokok_sample,
                    $tgl_panen,
                    $bmts,
                    $bhtm1,
                    $bhtm2,
                    $bhtm3,
                    $palepah_pokok

                ]);

                if (!empty($nonZeroValues)) {
                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);
                } else {

                    $ttlSkorMA = 0;
                }






                $WilMtAncakThn[$key]['pokok_sample'] = $pokok_sample;
                $WilMtAncakThn[$key]['ha_sample'] = $jum_ha;
                $WilMtAncakThn[$key]['pokok_panen'] = $pokok_panen;
                $WilMtAncakThn[$key]['p_panen'] = $p_panen;
                $WilMtAncakThn[$key]['k_panen'] = $k_panen;
                $WilMtAncakThn[$key]['tgl_panen'] = $tgl_panen;
                $WilMtAncakThn[$key]['brdPerjjg'] = $brdPerjjg;
                $WilMtAncakThn[$key]['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);

                $WilMtAncakThn[$key]['bmts'] = $bmts;
                $WilMtAncakThn[$key]['bhtm1'] = $bhtm1;
                $WilMtAncakThn[$key]['bhtm2'] = $bhtm2;
                $WilMtAncakThn[$key]['bhtm3'] = $bhtm3;
                $WilMtAncakThn[$key]['bhPerjjg'] = $sumPerBH;
                $WilMtAncakThn[$key]['skor_bh'] = skor_buah_Ma($sumPerBH);

                $WilMtAncakThn[$key]['palepah_pokok'] = $palepah_pokok;
                $WilMtAncakThn[$key]['perPl'] = $perPl;
                $WilMtAncakThn[$key]['skor_perPl'] = skor_palepah_ma($perPl);
                $WilMtAncakThn[$key]['total_skor'] = $ttlSkorMA;
            }
        // dd($WilMtAncakThn);
        //menghitung region  perbulan all estate


        $mutuAncakReg = array();
        foreach ($querytahun as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $mutuAncakReg)) {
                        $mutuAncakReg[$month] = array();
                    }
                    if (!array_key_exists($key, $mutuAncakReg[$month])) {
                        $mutuAncakReg[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $mutuAncakReg[$month][$key])) {
                        $mutuAncakReg[$month][$key][$key2] = array();
                    }
                    $mutuAncakReg[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $RegperbulanMTancak = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $RegperbulanMTancak[$month][$est['est']][$afd['nama']] = 0;
                    }
                }
            }
        }
        // dd($dataPerBulan)
        //menimpa nilai default di atas dengan dataperbulan mutu transport yang ada isinya sehingga yang kosong menjadi 0

        foreach ($RegperbulanMTancak as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($mutuAncakReg as $dataKey => $dataValue) {
                    // dd($dataKey, $key);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $RegperbulanMTancak[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        // dd($RegperbulanMTancak);
        $regMTancakAFD = array();
        foreach ($RegperbulanMTancak as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
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
                    foreach ($value2 as $key3 => $value3) {
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);

                        // $pokok_panen = json_decode($value3["pokok_dipanen"], true);
                        // $jajang_panen = json_decode($value3["jjg_dipanen"], true);
                        // $brtp = json_decode($value3["brtp"], true);
                        // $brtk = json_decode($value3["brtk"], true);
                        // $brtgl = json_decode($value3["brtgl"], true);
                        // $pokok_panen  = count($pokok_panen);
                        // $janjang_panen = array_sum($jajang_panen);
                        // $p_panen = array_sum($brtp);
                        // $k_panen = array_sum($brtk);
                        // $brtgl_panen = array_sum($brtgl);
                        // $bhts = json_decode($value3["bhts"], true);
                        // $bhtm1 = json_decode($value3["bhtm1"], true);
                        // $bhtm2 = json_decode($value3["bhtm2"], true);
                        // $bhtm3 = json_decode($value3["bhtm3"], true);
                        // $bhts_panen = array_sum($bhts);
                        // $bhtm1_panen = array_sum($bhtm1);
                        // $bhtm2_panen = array_sum($bhtm2);
                        // $bhtm3_oanen = array_sum($bhtm3);
                        // $ps = json_decode($value3["ps"], true);
                        // $pelepah_s = array_sum($ps);

                        $totalPokok += $value3["sample"];
                        $totalPanen += $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen +=  $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];
                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];

                        $totalpelepah_s += $value3["ps"];
                    }
                    // $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    if ($totalPokok != 0) {
                        $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    } else {
                        $akp = 0;
                    }

                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 2);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 2);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                    } else {
                        $perPl = 0;
                    }

                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);


                    $regMTancakAFD[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $regMTancakAFD[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $regMTancakAFD[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $regMTancakAFD[$key][$key1][$key2]['akp_rl'] = $akp;

                    $regMTancakAFD[$key][$key1][$key2]['p'] = $totalP_panen;
                    $regMTancakAFD[$key][$key1][$key2]['k'] = $totalK_panen;
                    $regMTancakAFD[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    // $regMTancakAFD[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $regMTancakAFD[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $regMTancakAFD[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;


                    // $regMTancakAFD[$key][$key1][$key2]['jjgperBuah'] =$sumPerBH;
                    // data untuk pelepah sengklek

                    $regMTancakAFD[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    // total skor akhir
                    $regMTancakAFD[$key][$key1][$key2]['skor_bh'] = skor_brd_ma($brdPerjjg);
                    $regMTancakAFD[$key][$key1][$key2]['skor_brd'] = skor_buah_Ma($sumPerBH);
                    $regMTancakAFD[$key][$key1][$key2]['skor_ps'] = skor_palepah_ma($perPl);
                    $regMTancakAFD[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                } else {
                    $regMTancakAFD[$key][$key1][$key2]['pokok_sample'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['ha_sample'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['akp_rl'] = 0;

                    $regMTancakAFD[$key][$key1][$key2]['p'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['k'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['tgl'] = 0;

                    // $regMTancakAFD[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $regMTancakAFD[$key][$key1][$key2]['brd/jjg'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['skor_brd'] = 0;
                    // data untuk buah tinggal
                    $regMTancakAFD[$key][$key1][$key2]['bhts_s'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm1'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm2'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['bhtm3'] = 0;

                    $regMTancakAFD[$key][$key1][$key2]['skor_bh'] = 0;
                    // $regMTancakAFD[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 2);
                    // data untuk pelepah sengklek
                    $regMTancakAFD[$key][$key1][$key2]['skor_ps'] = 0;
                    $regMTancakAFD[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhir
                    $regMTancakAFD[$key][$key1][$key2]['skor_akhir'] = 0;
                }
            }
        }
        // dd($regMTancakAFD['February']['RDE']);

        $regMTancakEST = array();
        foreach ($regMTancakAFD as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $pokok_sample = 0;
                $jum_ha = 0;
                $pokok_panen = 0;
                $p_panen = 0;
                $k_panen = 0;
                $tgl_panen = 0;
                $totalPKGL = 0;
                $brdPerjjg = 0;
                $bmts = 0;
                $bhtm1 = 0;
                $bhtm2 = 0;
                $bhtm3 = 0;
                $totalSM123 = 0;
                $palepah_pokok = 0;
                $perPl = 0;
                $sumPerBH = 0;
                $ttlSkorMA = 0;
                foreach ($value1 as $key2 => $value2) {
                    // dd($value2);
                    $pokok_sample += $value2['pokok_sample'];
                    $jum_ha += $value2['ha_sample'];
                    $pokok_panen += $value2['jumlah_panen'];
                    $p_panen += $value2['p'];
                    $k_panen += $value2['k'];
                    $tgl_panen += $value2['tgl'];

                    $bmts += $value2['bhts_s'];
                    $bhtm1 += $value2['bhtm1'];
                    $bhtm2 += $value2['bhtm2'];
                    $bhtm3 += $value2['bhtm3'];

                    $palepah_pokok += $value2['palepah_pokok'];
                }

                $skor_bTinggal = $p_panen + $k_panen + $tgl_panen;

                if ($skor_bTinggal != 0) {
                    $brdPerjjg = round($skor_bTinggal / $pokok_panen, 2);
                } else {
                    $brdPerjjg = 0;
                }

                $sumBH = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
                if ($sumBH != 0) {
                    $sumPerBH = round($sumBH / ($pokok_panen + $sumBH) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                if ($pokok_sample != 0) {
                    $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
                } else {
                    $perPl = 0;
                }

                $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                $regMTancakEST[$key][$key1]['pokok_sample'] = $pokok_sample;
                $regMTancakEST[$key][$key1]['ha_sample'] = $jum_ha;
                $regMTancakEST[$key][$key1]['pokok_panen'] = $pokok_panen;
                $regMTancakEST[$key][$key1]['p_panen'] = $p_panen;
                $regMTancakEST[$key][$key1]['k_panen'] = $k_panen;
                $regMTancakEST[$key][$key1]['tgl_panen'] = $tgl_panen;
                $regMTancakEST[$key][$key1]['brdPerjjg'] = $brdPerjjg;
                $regMTancakEST[$key][$key1]['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);

                $regMTancakEST[$key][$key1]['bmts'] = $bmts;
                $regMTancakEST[$key][$key1]['bhtm1'] = $bhtm1;
                $regMTancakEST[$key][$key1]['bhtm2'] = $bhtm2;
                $regMTancakEST[$key][$key1]['bhtm3'] = $bhtm3;
                $regMTancakEST[$key][$key1]['skor_bh'] = skor_buah_Ma($sumPerBH);

                $regMTancakEST[$key][$key1]['palepah_pokok'] = $palepah_pokok;
                $regMTancakEST[$key][$key1]['perPl'] = $perPl;
                $regMTancakEST[$key][$key1]['skor_perPl'] = skor_palepah_ma($perPl);
                $regMTancakEST[$key][$key1]['total_skor'] = $ttlSkorMA;
            } else {
                $regMTancakEST[$key][$key1]['pokok_sample'] = 0;
                $regMTancakEST[$key][$key1]['ha_sample'] = 0;
                $regMTancakEST[$key][$key1]['pokok_panen'] = 0;
                $regMTancakEST[$key][$key1]['p_panen'] = 0;
                $regMTancakEST[$key][$key1]['k_panen'] = 0;
                $regMTancakEST[$key][$key1]['tgl_panen'] = 0;
                $regMTancakEST[$key][$key1]['brdPerjjg'] = 0;
                $regMTancakEST[$key][$key1]['skor_brdPerjjg'] = 0;
                $regMTancakEST[$key][$key1]['bmts'] = 0;
                $regMTancakEST[$key][$key1]['bhtm1'] = 0;
                $regMTancakEST[$key][$key1]['bhtm2'] = 0;
                $regMTancakEST[$key][$key1]['bhtm3'] = 0;
                $regMTancakEST[$key][$key1]['skor_bh'] = 0;
                $regMTancakEST[$key][$key1]['palepah_pokok'] = 0;
                $regMTancakEST[$key][$key1]['perPl'] = 0;
                $regMTancakEST[$key][$key1]['skor_perPl'] = 0;
                $regMTancakEST[$key][$key1]['total_skor'] = 0;;
            }
        }
        // dd($regMTancakEST['February']['RDE']);

        $RegMTancakBln = array();
        foreach ($regMTancakEST as $key => $value)    if (!empty($value)) {
            $pokok_sample = 0;
            $jum_ha = 0;
            $pokok_panen = 0;
            $p_panen = 0;
            $k_panen = 0;
            $tgl_panen = 0;
            $totalPKGL = 0;
            $brdPerjjg = 0;
            $bmts = 0;
            $bhtm1 = 0;
            $bhtm2 = 0;
            $bhtm3 = 0;
            $totalSM123 = 0;
            $palepah_pokok = 0;
            $perPl = 0;
            $sumPerBH = 0;
            $ttlSkorMA = 0;
            foreach ($value as $key1 => $value1) {
                // dd($value3);
                $pokok_sample += $value1['pokok_sample'];
                $jum_ha += $value1['ha_sample'];
                $pokok_panen += $value1['pokok_panen'];
                $p_panen += $value1['p_panen'];
                $k_panen += $value1['k_panen'];
                $tgl_panen += $value1['tgl_panen'];

                $bmts += $value1['bmts'];
                $bhtm1 += $value1['bhtm1'];
                $bhtm2 += $value1['bhtm2'];
                $bhtm3 += $value1['bhtm3'];

                $palepah_pokok += $value1['palepah_pokok'];
            }
            $skor_bTinggal = $p_panen + $k_panen + $tgl_panen;

            if ($skor_bTinggal != 0) {
                $brdPerjjg = round($skor_bTinggal / $pokok_panen, 2);
            } else {
                $brdPerjjg = 0;
            }

            $sumBH = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
            if ($sumBH != 0) {
                $sumPerBH = round($sumBH / ($pokok_panen + $sumBH) * 100, 2);
            } else {
                $sumPerBH = 0;
            }

            if ($pokok_sample != 0) {
                $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
            } else {
                $perPl = 0;
            }

            $nonZeroValues = array_filter([$p_panen, $k_panen, $tgl_panen, $bmts, $bhtm1, $bhtm2, $bhtm3]);

            if (!empty($nonZeroValues)) {
                $RegMTancakBln[$key]['skor_bh'] = $skor_bh = skor_buah_Ma($sumPerBH);
                $RegMTancakBln[$key]['skor_brd'] = $skor_brd = skor_brd_ma($brdPerjjg);
                $RegMTancakBln[$key]['skor_ps'] = $skor_ps = skor_palepah_ma($perPl);
            } else {
                $RegMTancakBln[$key]['skor_bh'] = $skor_bh = 0;
                $RegMTancakBln[$key]['skor_brd'] = $skor_brd = 0;
                $RegMTancakBln[$key]['skor_ps'] = $skor_ps = 0;
            }

            $ttlSkorMA = $skor_bh + $skor_brd + $skor_ps;
            // $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);
            $RegMTancakBln[$key]['pokok_sample'] = $pokok_sample;
            $RegMTancakBln[$key]['ha_sample'] = $jum_ha;
            $RegMTancakBln[$key]['pokok_panen'] = $pokok_panen;
            $RegMTancakBln[$key]['p_panen'] = $p_panen;
            $RegMTancakBln[$key]['k_panen'] = $k_panen;
            $RegMTancakBln[$key]['tgl_panen'] = $tgl_panen;
            $RegMTancakBln[$key]['brdPerjjg'] = $brdPerjjg;
            // $RegMTancakBln[$key]['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);

            $RegMTancakBln[$key]['bmts'] = $bmts;
            $RegMTancakBln[$key]['bhtm1'] = $bhtm1;
            $RegMTancakBln[$key]['bhtm2'] = $bhtm2;
            $RegMTancakBln[$key]['bhtm3'] = $bhtm3;
            // $RegMTancakBln[$key]['skor_bh'] = skor_buah_Ma($sumPerBH);

            $RegMTancakBln[$key]['palepah_pokok'] = $palepah_pokok;
            $RegMTancakBln[$key]['perPl'] = $perPl;
            // $RegMTancakBln[$key]['skor_perPl'] = skor_palepah_ma($perPl);
            $RegMTancakBln[$key]['total_skor'] = $ttlSkorMA;
        } else {
            $RegMTancakBln[$key]['pokok_sample'] = 0;
            $RegMTancakBln[$key]['ha_sample'] = 0;
            $RegMTancakBln[$key]['pokok_panen'] = 0;
            $RegMTancakBln[$key]['p_panen'] = 0;
            $RegMTancakBln[$key]['k_panen'] = 0;
            $RegMTancakBln[$key]['tgl_panen'] = 0;
            $RegMTancakBln[$key]['brdPerjjg'] = 0;
            $RegMTancakBln[$key]['skor_brdPerjjg'] = 0;
            $RegMTancakBln[$key]['bmts'] = 0;
            $RegMTancakBln[$key]['bhtm1'] = 0;
            $RegMTancakBln[$key]['bhtm2'] = 0;
            $RegMTancakBln[$key]['bhtm3'] = 0;
            $RegMTancakBln[$key]['skor_bh'] = 0;
            $RegMTancakBln[$key]['palepah_pokok'] = 0;
            $RegMTancakBln[$key]['perPl'] = 0;
            $RegMTancakBln[$key]['skor_perPl'] = 0;
            $RegMTancakBln[$key]['total_skor'] = 0;;
        }
        // dd($RegMTancakBln['February']);
        //menghitung MT ancak peregion PErthaun all estate
        $RegMTanckTHn = array();
        $pokok_sample = 0;
        $jum_ha = 0;
        $pokok_panen = 0;
        $p_panen = 0;
        $k_panen = 0;
        $tgl_panen = 0;
        $totalPKGL = 0;
        $brdPerjjg = 0;
        $bmts = 0;
        $bhtm1 = 0;
        $bhtm2 = 0;
        $bhtm3 = 0;
        $totalSM123 = 0;
        $palepah_pokok = 0;
        $perPl = 0;
        $sumPerBH = 0;
        $ttlSkorMA = 0;
        foreach ($WilMtAncakThn as $key => $value) {
            $pokok_sample += $value['pokok_sample'];
            $jum_ha += $value['ha_sample'];
            $pokok_panen += $value['pokok_panen'];
            $p_panen += $value['p_panen'];
            $k_panen += $value['k_panen'];
            $tgl_panen += $value['tgl_panen'];

            $bmts += $value['bmts'];
            $bhtm1 += $value['bhtm1'];
            $bhtm2 += $value['bhtm2'];
            $bhtm3 += $value['bhtm3'];

            $palepah_pokok += $value['palepah_pokok'];
        }

        $skor_bTinggal = $p_panen + $k_panen + $tgl_panen;

        if ($skor_bTinggal != 0) {
            $brdPerjjg = round($skor_bTinggal / $pokok_panen, 2);
        } else {
            $brdPerjjg = 0;
        }

        $sumBH = $bmts +  $bhtm1 +  $bhtm2 +  $bhtm3;
        if ($sumBH != 0) {
            $sumPerBH = round($sumBH / ($pokok_panen + $sumBH) * 100, 2);
        } else {
            $sumPerBH = 0;
        }

        if ($pokok_sample != 0) {
            $perPl = round(($palepah_pokok / $pokok_sample) * 100, 2);
        } else {
            $perPl = 0;
        }

        $nonZeroValuesCak = array_filter([
            $pokok_sample,
            $pokok_panen,
            $p_panen,
            $k_panen,
            $tgl_panen,
            $bmts,
            $bhtm1,
            $bhtm2,
            $bhtm3,
            $palepah_pokok
        ]);

        if (!empty($nonZeroValuesCak)) {
            $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);
        } else {
            $ttlSkorMA = 0;
        }





        $RegMTanckTHn['I']['pokok_sample'] = $pokok_sample;
        $RegMTanckTHn['I']['pokok_sample'] = $pokok_sample;
        $RegMTanckTHn['I']['ha_sample'] = $jum_ha;
        $RegMTanckTHn['I']['pokok_panen'] = $pokok_panen;
        $RegMTanckTHn['I']['p_panen'] = $p_panen;
        $RegMTanckTHn['I']['k_panen'] = $k_panen;
        $RegMTanckTHn['I']['tgl_panen'] = $tgl_panen;
        $RegMTanckTHn['I']['brdPerjjg'] = $brdPerjjg;
        $RegMTanckTHn['I']['skor_brdPerjjg'] = skor_brd_ma($brdPerjjg);

        $RegMTanckTHn['I']['bmts'] = $bmts;
        $RegMTanckTHn['I']['bhtm1'] = $bhtm1;
        $RegMTanckTHn['I']['bhtm2'] = $bhtm2;
        $RegMTanckTHn['I']['bhtm3'] = $bhtm3;
        $RegMTanckTHn['I']['skor_bh'] = skor_buah_Ma($sumPerBH);

        $RegMTanckTHn['I']['palepah_pokok'] = $palepah_pokok;
        $RegMTanckTHn['I']['perPl'] = $perPl;
        $RegMTanckTHn['I']['skor_perPl'] = skor_palepah_ma($perPl);
        $RegMTanckTHn['I']['total_skor'] = $ttlSkorMA;

        // dd($RegMTanckTHn);

        //end perhitungan MT ancak

        $dataTransportBulan = array();
        foreach ($queryMTtrans as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataTransportBulan)) {
                        $dataTransportBulan[$month] = array();
                    }
                    if (!array_key_exists($key, $dataTransportBulan[$month])) {
                        $dataTransportBulan[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataTransportBulan[$month][$key])) {
                        $dataTransportBulan[$month][$key][$key2] = array();
                    }
                    $dataTransportBulan[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $defPerbulanWil = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defPerbulanWil[$month][$est['est']][$afd['nama']] = 0;
                    }
                }
            }
        }

        // dd($dataTransportBulan);

        // dd($dataPerBulan)
        //menimpa nilai default di atas dengan dataperbulan mutu transport yang ada isinya sehingga yang kosong menjadi 0

        foreach ($defPerbulanWil as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataTransportBulan as $dataKey => $dataValue) {
                    // dd($dataKey, $key);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $defPerbulanWil[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        // dd($defPerbulanWil);
        //membuat data mutu transpot berdasarakan wilayah 1,2,3
        $mtTransWil = array();
        foreach ($queryEste2 as $key => $value) {
            foreach ($defPerbulanWil as $key2 => $value2) {
                // dd($value2);
                foreach ($value2 as $key3 => $value3) {
                    if ($value['est'] == $key3) {
                        $mtTransWil[$value['wil']][$key2][$key3] = $value3;
                    }
                }
            }
        }
        // dd($mtTransWil);
        // dd($mutuTransAFD['SBE']);
        //perhitungan untuk mutu transport
        //hitungan berdsarkan bulan > afd
        $mtTransAFDblan = array();
        foreach ($mtTransWil as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key4 => $value4) {

                            // if (!in_array($value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            // }
                            if ($RegData == '2' || $RegData == 2 && isset($mutuTransAFD[$key2][$key1][$key3])) {
                                $dataBLok = $mutuTransAFD[$key2][$key1][$key3]['tph_sample'];
                            } else {
                                $dataBLok = count($listBlokPerAfd);
                            }
                            $sum_bt += $value4['bt'];
                            $sum_rst += $value4['rst'];
                        }

                        $brdPertph = round($sum_bt / $dataBLok, 2);
                        $buahPerTPH = round($sum_rst / $dataBLok, 2);

                        $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        $mtTransAFDblan[$key][$key1][$key2][$key3]['tph_sample'] = $dataBLok;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_brd'] = $sum_bt;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_brd/TPH'] = $brdPertph;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_buah'] = $sum_rst;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_buahPerTPH'] = $buahPerTPH;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['totalSkor'] = $totalSkor;
                    } else {
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['tph_sample'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_brd'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_brd/TPH'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_buah'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['total_buahPerTPH'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['skor_brdPertph'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['skor_buahPerTPH'] = 0;
                        $mtTransAFDblan[$key][$key1][$key2][$key3]['totalSkor'] = 0;

                        if (isset($mutuTransAFD[$key2][$key1][$key3])) {
                            $mtTransAFDblan[$key][$key1][$key2][$key3]['tph_sample'] = $mutuTransAFD[$key2][$key1][$key3]['tph_sample'];
                        }
                    }
                }
            }
        }

        // dd($mtTransAFDblan);
        // if($RegData == '2' || $RegData == 2){
        //     foreach ($mtTransAFDblan as $key1 => $value1) {
        //         foreach ($value1 as $key2 => $value2) {
        //             foreach ($value2 as $key3 => $value3) {
        //                 foreach ($value3 as $key4 => $value) {

        //                     if (isset($tphSampleReg2[$key3][$key2][$key4])) {

        //                         $mtTransAFDblan[$key1][$key2][$key3][$key4]['tph_sample'] = $tphSampleReg2[$key3][$key2][$key4]['tph_sample'];
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        // dd($mtTransAFDblan);
        // dd($mtTransAFDblan['1']['April']);
        //perhitungan mutu transport bulan per > estate
        $mtTransESTblan = array();
        foreach ($mtTransAFDblan as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    foreach ($value2 as $key3 => $value3) {
                        // dd($value3);
                        $sum_bt += $value3['total_brd'];
                        $sum_rst += $value3['total_buah'];
                        $dataBLok += $value3['tph_sample'];
                    }

                    if ($dataBLok != 0) {
                        $brdPertph = round($sum_bt / $dataBLok, 2);
                    } else {
                        $brdPertph = 0;
                    }

                    if ($dataBLok != 0) {
                        $buahPerTPH = round($sum_rst / $dataBLok, 2);
                    } else {
                        $buahPerTPH = 0;
                    }



                    $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


                    $mtTransESTblan[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $mtTransESTblan[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $mtTransESTblan[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $mtTransESTblan[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $mtTransESTblan[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                    $mtTransESTblan[$key][$key1][$key2]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                    $mtTransESTblan[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                    $mtTransESTblan[$key][$key1][$key2]['totalSkor'] = $totalSkor;
                } else {

                    $mtTransESTblan[$key][$key1][$key2]['tph_sample'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['total_brd'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['total_buah'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $mtTransESTblan[$key][$key1][$key2]['totalSkor'] = 0;
                }
            }
        }
        // dd($mtTransESTblan);
        // menghitung mututransprt unutk data perbulan dari semua estate
        $mtTranstAllbln = array();
        foreach ($mtTransESTblan as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLok = 0;
                $sum_bt = 0;
                $sum_rst = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;

                foreach ($value1 as $Key2 => $value2) {
                    $sum_bt += $value2['total_brd'];
                    $sum_rst += $value2['total_buah'];
                    $dataBLok += $value2['tph_sample'];
                }

                if ($dataBLok != 0) {
                    $brdPertph = round($sum_bt / $dataBLok, 2);
                } else {
                    $brdPertph = 0;
                }

                if ($dataBLok != 0) {
                    $buahPerTPH = round($sum_rst / $dataBLok, 2);
                } else {
                    $buahPerTPH = 0;
                }
                $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                if (!empty($nonZeroValues)) {
                    $mtTranstAllbln[$key][$key1]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                    $mtTranstAllbln[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                } else {
                    $mtTranstAllbln[$key][$key1]['skor_brdPertph'] = $skor_brd = 0;
                    $mtTranstAllbln[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                $totalSkor = $skor_brd + $skor_buah;
                // $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


                $mtTranstAllbln[$key][$key1]['tph_sample'] = $dataBLok;
                $mtTranstAllbln[$key][$key1]['total_brd'] = $sum_bt;
                $mtTranstAllbln[$key][$key1]['total_brd/TPH'] = $brdPertph;
                $mtTranstAllbln[$key][$key1]['total_buah'] = $sum_rst;
                $mtTranstAllbln[$key][$key1]['total_buahPerTPH'] = $buahPerTPH;
                // $mtTranstAllbln[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                // $mtTranstAllbln[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                $mtTranstAllbln[$key][$key1]['totalSkor'] = $totalSkor;
            } else {
                $mtTranstAllbln[$key][$key1]['tph_sample'] = 0;
                $mtTranstAllbln[$key][$key1]['total_brd'] = 0;
                $mtTranstAllbln[$key][$key1]['total_brd/TPH'] = 0;
                $mtTranstAllbln[$key][$key1]['total_buah'] = 0;
                $mtTranstAllbln[$key][$key1]['total_buahPerTPH'] = 0;
                $mtTranstAllbln[$key][$key1]['skor_brdPertph'] = 0;
                $mtTranstAllbln[$key][$key1]['skor_buahPerTPH'] = 0;
                $mtTranstAllbln[$key][$key1]['totalSkor'] = 0;
            }
        }

        // dd($mtTranstAllbln);
        //perhitungan mutu transprt pertahun
        $mtTransTahun = array();
        foreach ($mtTranstAllbln as $key => $value) if (!empty($value)) {
            $dataBLok = 0;
            $sum_bt = 0;
            $sum_rst = 0;
            $brdPertph = 0;
            $buahPerTPH = 0;
            foreach ($value as $key1 => $value2) {
                // dd($value2);
                $sum_bt += $value2['total_brd'];
                $sum_rst += $value2['total_buah'];
                $dataBLok += $value2['tph_sample'];
            }
            if ($dataBLok != 0) {
                $brdPertph = round($sum_bt / $dataBLok, 2);
            } else {
                $brdPertph = 0;
            }

            if ($dataBLok != 0) {
                $buahPerTPH = round($sum_rst / $dataBLok, 2);
            } else {
                $buahPerTPH = 0;
            }

            $nonZeroValues = array_filter([$sum_bt, $sum_rst, $dataBLok]);

            if (!empty($nonZeroValues)) {
                $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);
            } else {

                $totalSkor = 0;
            }




            $mtTransTahun[$key]['tph_sample'] = $dataBLok;
            $mtTransTahun[$key]['total_brd'] = $sum_bt;
            $mtTransTahun[$key]['total_brd/TPH'] = $brdPertph;
            $mtTransTahun[$key]['total_buah'] = $sum_rst;
            $mtTransTahun[$key]['total_buahPerTPH'] = $buahPerTPH;
            $mtTransTahun[$key]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
            $mtTransTahun[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
            $mtTransTahun[$key]['totalSkor'] = $totalSkor;
        }

        // dd($mtTransTahun);
        //menghitung untuk regional 1
        $RegTransAFD = array();
        foreach ($defPerbulanWil as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key4 => $value3) {
                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        // }
                        $dataBLok = count($listBlokPerAfd);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }
                    $brdPertph = round($sum_bt / $dataBLok, 2);
                    $buahPerTPH = round($sum_rst / $dataBLok, 2);


                    $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $RegTransAFD[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $RegTransAFD[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $RegTransAFD[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $RegTransAFD[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $RegTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                    $RegTransAFD[$key][$key1][$key2]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                    $RegTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                    $RegTransAFD[$key][$key1][$key2]['totalSkor'] = $totalSkor;
                } else {
                    $RegTransAFD[$key][$key1][$key2]['tph_sample'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['total_brd'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['total_buah'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $RegTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
                }
            }
        }

        // dd($RegTransAFD);
        $RegTransEst = array();
        foreach ($RegTransAFD as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                $sum_bt = 0;
                $sum_rst = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                $dataBLok = 0;
                foreach ($value1 as $key2 => $value2) {
                    $sum_bt += $value2['total_brd'];
                    $sum_rst += $value2['total_buah'];
                    $dataBLok += $value2['tph_sample'];
                }
                if ($dataBLok != 0) {
                    $brdPertph = round($sum_bt / $dataBLok, 2);
                } else {
                    $brdPertph = 0;
                }

                if ($dataBLok != 0) {
                    $buahPerTPH = round($sum_rst / $dataBLok, 2);
                } else {
                    $buahPerTPH = 0;
                }

                $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


                $RegTransEst[$key][$key1]['tph_sample'] = $dataBLok;
                $RegTransEst[$key][$key1]['total_brd'] = $sum_bt;
                $RegTransEst[$key][$key1]['total_brd/TPH'] = $brdPertph;
                $RegTransEst[$key][$key1]['total_buah'] = $sum_rst;
                $RegTransEst[$key][$key1]['total_buahPerTPH'] = $buahPerTPH;
                $RegTransEst[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                $RegTransEst[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                $RegTransEst[$key][$key1]['totalSkor'] = $totalSkor;
            } else {
                $RegTransEst[$key][$key1]['tph_sample'] = 0;
                $RegTransEst[$key][$key1]['total_brd'] = 0;
                $RegTransEst[$key][$key1]['total_brd/TPH'] = 0;
                $RegTransEst[$key][$key1]['total_buah'] = 0;
                $RegTransEst[$key][$key1]['total_buahPerTPH'] = 0;
                $RegTransEst[$key][$key1]['skor_brdPertph'] = 0;
                $RegTransEst[$key][$key1]['skor_buahPerTPH'] = 0;
                $RegTransEst[$key][$key1]['totalSkor'] = 0;
            }
        }
        // dd($RegTransEst);
        $RegMTtransBln = array();
        foreach ($RegTransEst as $key => $value) if (!empty($value)) {
            $sum_bt = 0;
            $sum_rst = 0;
            $brdPertph = 0;
            $buahPerTPH = 0;
            $totalSkor = 0;
            $dataBLok = 0;
            foreach ($value as $key1 => $value1) {
                // dd($value3);
                $sum_bt += $value1['total_brd'];
                $sum_rst += $value1['total_buah'];
                $dataBLok += $value1['tph_sample'];
            }

            if ($dataBLok != 0) {
                $brdPertph = round($sum_bt / $dataBLok, 2);
            } else {
                $brdPertph = 0;
            }

            if ($dataBLok != 0) {
                $buahPerTPH = round($sum_rst / $dataBLok, 2);
            } else {
                $buahPerTPH = 0;
            }
            $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

            if (!empty($nonZeroValues)) {
                $RegMTtransBln[$key]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                $RegMTtransBln[$key]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
            } else {
                $RegMTtransBln[$key]['skor_brdPertph'] = $skor_brd = 0;
                $RegMTtransBln[$key]['skor_buahPerTPH'] = $skor_buah = 0;
            }

            $totalSkor = $skor_brd + $skor_buah;
            // $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

            $RegMTtransBln[$key]['tph_sample'] = $dataBLok;
            $RegMTtransBln[$key]['total_brd'] = $sum_bt;
            $RegMTtransBln[$key]['total_brd/TPH'] = $brdPertph;
            $RegMTtransBln[$key]['total_buah'] = $sum_rst;
            $RegMTtransBln[$key]['total_buahPerTPH'] = $buahPerTPH;
            // $RegMTtransBln[$key]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
            // $RegMTtransBln[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
            $RegMTtransBln[$key]['totalSkor'] = $totalSkor;
        } else {
            $RegMTtransBln[$key]['tph_sample'] = 0;
            $RegMTtransBln[$key]['total_brd'] = 0;
            $RegMTtransBln[$key]['total_brd/TPH'] = 0;
            $RegMTtransBln[$key]['total_buah'] = 0;
            $RegMTtransBln[$key]['total_buahPerTPH'] = 0;
            $RegMTtransBln[$key]['skor_brdPertph'] = 0;
            $RegMTtransBln[$key]['skor_buahPerTPH'] = 0;
            $RegMTtransBln[$key]['totalSkor'] = 0;
        }
        // dd($RegMTtransBln);

        // dd($mtTransTahun);
        //perhitungan mt trans Reg 1 
        $RegMTtransTHn = array();
        $dataBLok = 0;
        $sum_bt = 0;
        $sum_rst = 0;
        $brdPertph = 0;
        $buahPerTPH = 0;
        foreach ($mtTransTahun as $key => $value) {
            $sum_bt += $value['total_brd'];
            $sum_rst += $value['total_buah'];
            $dataBLok += $value['tph_sample'];
        }
        if ($dataBLok != 0) {
            $brdPertph = round($sum_bt / $dataBLok, 2);
        } else {
            $brdPertph = 0;
        }

        if ($dataBLok != 0) {
            $buahPerTPH = round($sum_rst / $dataBLok, 2);
        } else {
            $buahPerTPH = 0;
        }

        $nonZeroValuesTrans = array_filter([$sum_bt, $sum_rst, $dataBLok]);

        if (!empty($nonZeroValuesTrans)) {
            $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);
        } else {
            $totalSkor = 0;
        }



        $RegMTtransTHn['I']['tph_sample'] = $dataBLok;
        $RegMTtransTHn['I']['total_brd'] = $sum_bt;
        $RegMTtransTHn['I']['total_brd/TPH'] = $brdPertph;
        $RegMTtransTHn['I']['total_buah'] = $sum_rst;
        $RegMTtransTHn['I']['total_buahPerTPH'] = $buahPerTPH;
        $RegMTtransTHn['I']['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
        $RegMTtransTHn['I']['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
        $RegMTtransTHn['I']['totalSkor'] = $totalSkor;



        // dd($RegMTtransTHn);

        //end perhitungna mt trans

        $dataMtBuahWil = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataMtBuahWil)) {
                        $dataMtBuahWil[$month] = array();
                    }
                    if (!array_key_exists($key, $dataMtBuahWil[$month])) {
                        $dataMtBuahWil[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataMtBuahWil[$month][$key])) {
                        $dataMtBuahWil[$month][$key][$key2] = array();
                    }
                    $dataMtBuahWil[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $defperbulanMTbh = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defperbulanMTbh[$month][$est['est']][$afd['nama']] = 0;
                    }
                }
            }
        }


        foreach ($defperbulanMTbh as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataMtBuahWil as $dataKey => $dataValue) {
                    // dd($dataKey, $key);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $defperbulanMTbh[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($defperbulanMTbh);

        // dd($dataPerBulan)
        //menimpa nilai default di atas dengan dataperbulan mutu buah yang ada isinya sehingga yang kosong menjadi 0
        // foreach ($dataMtBuahWil as $key2 => $value2) {
        //     foreach ($value2 as $key3 => $value3) {
        //         foreach ($value3 as $key4 => $value4) {
        //             // foreach ($defperbulanMTbh[$key2][$key3][$key4] as $key => $value) {
        //             $defperbulanMTbh[$key2][$key3][$key4] = $value4;
        //         }
        //     }
        // }



        // dd($defperbulanMTbh);
        //membuat data mutu transpot berdasarakan wilayah 1,2,3
        $mtBuahwil = array();
        foreach ($queryEste2 as $key => $value) {
            foreach ($defperbulanMTbh as $key2 => $value2) {
                // dd($value2);
                foreach ($value2 as $key3 => $value3) {
                    if ($value['est'] == $key3) {
                        $mtBuahwil[$value['wil']][$key2][$key3] = $value3;
                    }
                }
            }
        }
        // dd($mtBuahwil);
        //menghitung mutu buah perbulan > afdeling
        $mtBuahAFDbln = array();
        foreach ($mtBuahwil as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        if (!empty($value3)) {
                            $dataBLok = 0;
                            $sum_bmt = 0;
                            $sum_bmk = 0;
                            $sum_over = 0;
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
                            $no_Vcut = 0;
                            $combination_counts = array();
                            $jml_mth = 0;
                            $jml_mtg = 0;
                            $dataBLok = 0;

                            $listBlokPerAfd = [];
                            foreach ($value3 as $key4 => $value4) {
                                $combination = $value4['blok'] . ' ' . $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['tph_baris'];
                                if (!isset($combination_counts[$combination])) {
                                    $combination_counts[$combination] = 0;
                                }
                                $combination_counts[$combination]++;
                                $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'] . ' ' . $value4['tph_baris'];
                                $dtBlok = count($listBlokPerAfd);
                                $sum_bmt += $value4['bmt'];
                                $sum_bmk += $value4['bmk'];
                                $sum_over += $value4['overripe'];
                                $sum_kosongjjg += $value4['empty_bunch'];
                                $sum_vcut += $value4['vcut'];
                                $sum_kr += $value4['alas_br'];


                                $sum_Samplejjg += $value4['jumlah_jjg'];
                                $sum_abnor += $value4['abnormal'];
                            }

                            $jml_mth = ($sum_bmt + $sum_bmk);
                            $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                            $dataBLok += $dtBlok;

                            if ($sum_kr != 0) {
                                $total_kr = round($sum_kr / $dataBLok, 2);
                            } else {
                                $total_kr = 0;
                            }
                            $no_Vcut = $sum_Samplejjg - $sum_vcut;

                            $per_kr = round($total_kr * 100, 2);

                            $denom1 = ($sum_Samplejjg - $sum_abnor) != 0 ? ($sum_Samplejjg - $sum_abnor) : 1; // set denominator to 1 if it is zero
                            $denom2 = $sum_Samplejjg != 0 ? $sum_Samplejjg : 1; // set denominator to 1 if it is zero

                            $PerMth = round(($jml_mth / $denom1) * 100, 2);
                            $PerMsk = round(($jml_mtg / $denom1) * 100, 2);
                            $PerOver = round(($sum_over / $denom1) * 100, 2);
                            $Perkosongjjg = round(($sum_kosongjjg / $denom1) * 100, 2);
                            $PerVcut = round(($no_Vcut / $denom2) * 100, 2);
                            $PerAbr = round(($sum_abnor / $denom2) * 100, 2);

                            // $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                            // $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                            // $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                            // $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                            // $PerVcut = round(($no_Vcut / $sum_Samplejjg) * 100, 2);
                            // $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);

                            $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);


                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['tph_baris_blok'] = $dataBLok;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['sampleJJG_total'] = $sum_Samplejjg;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_mentah'] = $jml_mth;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perMentah'] = $PerMth;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_masak'] = $jml_mtg;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perMasak'] = $PerMsk;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_over'] = $sum_over;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perOver'] = $PerOver;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_abnormal'] = $sum_abnor;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_jjgKosong'] = $sum_kosongjjg;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perKosongjjg'] = $Perkosongjjg;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_vcut'] = $sum_vcut;

                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['jum_kr'] = $sum_kr;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_kr'] = $total_kr;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['persen_kr'] = $per_kr;

                            // skoring
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_over'] = skor_buah_over_mb($PerOver);
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_jjgKosong'] =  skor_jangkos_mb($Perkosongjjg);
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_vcut'] = skor_vcut_mb($PerVcut);

                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_kr'] = skor_abr_mb($per_kr);
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['TOTAL_SKOR'] = $totalSkor;
                        } else {
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['tph_baris_blok'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['sampleJJG_total'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_mentah'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perMentah'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_masak'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perMasak'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_over'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perOver'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_abnormal'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_jjgKosong'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_perKosongjjg'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_vcut'] = 0;

                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['jum_kr'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['total_kr'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['persen_kr'] = 0;

                            // skoring
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_mentah'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_masak'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_over'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_jjgKosong'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_vcut'] = 0;

                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['skor_kr'] = 0;
                            $mtBuahAFDbln[$key][$key1][$key2][$key3]['TOTAL_SKOR'] = 0;
                        }
                    }
                }
            }
        }

        // dd($mtBuahAFDbln[1]);
        //hitung mutu buah perbulan > est 
        $mtBuahESTbln = array();
        foreach ($mtBuahAFDbln as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $dataBLok = 0;
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
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

                    $no_Vcut = 0;
                    foreach ($value2 as $key3 => $value3) {
                        $dataBLok += $value3['tph_baris_blok'];
                        $sum_bmt += $value3['total_mentah'];
                        $sum_bmk += $value3['total_masak'];
                        $sum_over += $value3['total_over'];
                        $sum_kosongjjg += $value3['total_jjgKosong'];
                        $sum_vcut += $value3['total_vcut'];
                        $sum_kr += $value3['jum_kr'];

                        $sum_Samplejjg += $value3['sampleJJG_total'];
                        $sum_abnor += $value3['total_abnormal'];
                    }

                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }

                    $no_Vcut = $sum_Samplejjg - $sum_vcut;
                    if ($sum_bmt != 0) {
                        $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMth = 0;
                    }
                    if ($sum_bmk != 0) {
                        $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMsk = 0;
                    }

                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerOver = 0;
                    }

                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $Perkosongjjg = 0;
                    }

                    if ($sum_Samplejjg != 0) {
                        $PerVcut = round(($no_Vcut / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_Samplejjg != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerAbr = 0;
                    }

                    $per_kr = round($total_kr * 100, 2);


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_jangkos_mb($Perkosongjjg) + skor_vcut_mb($PerVcut)  + skor_abr_mb($per_kr);
                    //

                    $mtBuahESTbln[$key][$key1][$key2]['tph_baris_blok'] = $dataBLok;
                    $mtBuahESTbln[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $mtBuahESTbln[$key][$key1][$key2]['total_mentah'] = $sum_bmt;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $mtBuahESTbln[$key][$key1][$key2]['total_masak'] = $sum_bmk;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $mtBuahESTbln[$key][$key1][$key2]['total_over'] = $sum_over;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $mtBuahESTbln[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $mtBuahESTbln[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $mtBuahESTbln[$key][$key1][$key2]['total_vcut'] = $sum_vcut;

                    $mtBuahESTbln[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $mtBuahESTbln[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $mtBuahESTbln[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $mtBuahESTbln[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $mtBuahESTbln[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $mtBuahESTbln[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $mtBuahESTbln[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $mtBuahESTbln[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $mtBuahESTbln[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $mtBuahESTbln[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;
                } else {
                    $mtBuahESTbln[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_mentah'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perMentah'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_masak'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perMasak'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_over'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perOver'] = 0;

                    $mtBuahESTbln[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_vcut'] = 0;

                    $mtBuahESTbln[$key][$key1][$key2]['jum_kr'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['total_kr'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $mtBuahESTbln[$key][$key1][$key2]['skor_mentah'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_masak'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_over'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_vcut'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_abnormal'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['skor_kr'] = 0;
                    $mtBuahESTbln[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                }
            }
        }

        // dd($mtBuahESTbln)[1];

        //perhitungan mutu buah untuk perbulan semua estate
        $mtBuahAllEst = array();
        foreach ($mtBuahESTbln as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLok = 0;
                $sum_bmt = 0;
                $sum_bmk = 0;
                $sum_over = 0;
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
                $perbagi = 0;
                $no_Vcut = 0;
                foreach ($value1 as $key2 => $value2) {
                    // dd($value2);
                    $dataBLok += $value2['tph_baris_blok'];
                    $sum_bmt += $value2['total_mentah'];
                    $sum_bmk += $value2['total_masak'];
                    $sum_over += $value2['total_over'];
                    $sum_kosongjjg += $value2['total_jjgKosong'];
                    $sum_vcut += $value2['total_vcut'];
                    $sum_kr += $value2['jum_kr'];

                    $sum_Samplejjg += $value2['sampleJJG_total'];
                    $sum_abnor += $value2['total_abnormal'];
                }
                if ($sum_kr != 0) {
                    $total_kr = round($sum_kr / $dataBLok, 2);
                } else {
                    $total_kr = 0;
                }


                $no_Vcut = $sum_Samplejjg - $sum_vcut;


                if ($sum_bmt != 0) {
                    $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMth = 0;
                }
                if ($sum_bmk != 0) {
                    $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }

                if ($sum_over != 0) {
                    $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerOver = 0;
                }

                if ($sum_kosongjjg != 0) {
                    $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }



                if ($sum_vcut != 0) {
                    $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerVcut = 0;
                }


                if ($sum_Samplejjg != 0) {
                    $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerAbr = 0;
                }

                $per_kr = $dataBLok != 0 ? round(($sum_kr / $dataBLok) * 100, 2) : 0;



                $nonZeroValues = array_filter([$sum_Samplejjg, $sum_bmt, $sum_bmk, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

                if (!empty($nonZeroValues)) {
                    $mtBuahAllEst[$key][$key1]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                    $mtBuahAllEst[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                    $mtBuahAllEst[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                    $mtBuahAllEst[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                    $mtBuahAllEst[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                    $mtBuahAllEst[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
                } else {
                    $mtBuahAllEst[$key][$key1]['skor_mentah'] = $skor_mentah = 0;
                    $mtBuahAllEst[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    $mtBuahAllEst[$key][$key1]['skor_over'] = $skor_over = 0;
                    $mtBuahAllEst[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    $mtBuahAllEst[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    $mtBuahAllEst[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
                // $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_jangkos_mb($Perkosongjjg) + skor_vcut_mb($PerVcut)  + skor_abr_mb($per_kr);


                $mtBuahAllEst[$key][$key1]['tph_baris_blok'] = $dataBLok;
                $mtBuahAllEst[$key][$key1]['sampleJJG_total'] = $sum_Samplejjg;
                $mtBuahAllEst[$key][$key1]['total_mentah'] = $sum_bmt;
                $mtBuahAllEst[$key][$key1]['total_perMentah'] = $PerMth;
                $mtBuahAllEst[$key][$key1]['total_masak'] = $sum_bmk;
                $mtBuahAllEst[$key][$key1]['total_perMasak'] = $PerMsk;
                $mtBuahAllEst[$key][$key1]['total_over'] = $sum_over;
                $mtBuahAllEst[$key][$key1]['total_perOver'] = $PerOver;
                $mtBuahAllEst[$key][$key1]['total_abnormal'] = $sum_abnor;
                $mtBuahAllEst[$key][$key1]['per_abnormal'] = $PerAbr;
                $mtBuahAllEst[$key][$key1]['total_jjgKosong'] = $sum_kosongjjg;
                $mtBuahAllEst[$key][$key1]['total_perKosongjjg'] = $Perkosongjjg;
                $mtBuahAllEst[$key][$key1]['total_vcut'] = $sum_vcut;

                $mtBuahAllEst[$key][$key1]['jum_kr'] = $sum_kr;
                $mtBuahAllEst[$key][$key1]['total_kr'] = $total_kr;
                $mtBuahAllEst[$key][$key1]['persen_kr'] = $per_kr;

                // skoring
                // $mtBuahAllEst[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                // $mtBuahAllEst[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                // $mtBuahAllEst[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);
                // $mtBuahAllEst[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                // $mtBuahAllEst[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);

                // $mtBuahAllEst[$key][$key1]['skor_kr'] = skor_abr_mb($per_kr);
                $mtBuahAllEst[$key][$key1]['TOTAL_SKOR'] = $totalSkor;
            } else {
                $mtBuahAllEst[$key][$key1]['tph_baris_blok'] = 0;
                $mtBuahAllEst[$key][$key1]['sampleJJG_total'] = 0;
                $mtBuahAllEst[$key][$key1]['total_mentah'] = 0;
                $mtBuahAllEst[$key][$key1]['total_perMentah'] = 0;
                $mtBuahAllEst[$key][$key1]['total_masak'] = 0;
                $mtBuahAllEst[$key][$key1]['total_perMasak'] = 0;
                $mtBuahAllEst[$key][$key1]['total_over'] = 0;
                $mtBuahAllEst[$key][$key1]['total_perOver'] = 0;

                $mtBuahAllEst[$key][$key1]['total_jjgKosong'] = 0;
                $mtBuahAllEst[$key][$key1]['total_perKosongjjg'] = 0;
                $mtBuahAllEst[$key][$key1]['total_vcut'] = 0;

                $mtBuahAllEst[$key][$key1]['jum_kr'] = 0;
                $mtBuahAllEst[$key][$key1]['total_kr'] = 0;
                $mtBuahAllEst[$key][$key1]['persen_kr'] = 0;

                // skoring
                $mtBuahAllEst[$key][$key1]['skor_mentah'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_masak'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_over'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_jjgKosong'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_vcut'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_abnormal'] = 0;
                $mtBuahAllEst[$key][$key1]['skor_kr'] = 0;
                $mtBuahAllEst[$key][$key1]['TOTAL_SKOR'] = 0;
            }
        }

        // dd($mtBuahAllEst);
        //perhitungan mutu buah unutuk wilayah pertahun
        $mtBuahTahunall = array();
        foreach ($mtBuahAllEst as $key => $value) if (!empty($value)) {
            $dataBLok = 0;
            $sum_bmt = 0;
            $sum_bmk = 0;
            $sum_over = 0;
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
            $perbagi = 0;
            $no_Vcut = 0;
            foreach ($value as $key1 => $value1) {
                $dataBLok += $value1['tph_baris_blok'];
                $sum_bmt += $value1['total_mentah'];
                $sum_bmk += $value1['total_masak'];
                $sum_over += $value1['total_over'];
                $sum_kosongjjg += $value1['total_jjgKosong'];
                $sum_vcut += $value1['total_vcut'];
                $sum_kr += $value1['jum_kr'];

                $sum_Samplejjg += $value1['sampleJJG_total'];
                $sum_abnor += $value1['total_abnormal'];
            }

            if ($sum_kr != 0) {
                $total_kr = round($sum_kr / $dataBLok, 2);
            } else {
                $total_kr = 0;
            }


            $no_Vcut = $sum_Samplejjg - $sum_vcut;
            if ($sum_bmt != 0) {
                $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerMth = 0;
            }
            if ($sum_bmk != 0) {
                $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerMsk = 0;
            }

            if ($sum_over != 0) {
                $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerOver = 0;
            }

            if ($sum_kosongjjg != 0) {
                $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $Perkosongjjg = 0;
            }

            if ($sum_vcut != 0) {
                $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
            } else {
                $PerVcut = 0;
            }

            if ($sum_Samplejjg != 0) {
                $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
            } else {
                $PerAbr = 0;
            }

            $per_kr = round($total_kr * 100, 2);

            $nonZeroValues = array_filter([$sum_Samplejjg, $sum_bmt, $sum_bmk, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

            if (!empty($nonZeroValues)) {
                $mtBuahTahunall[$key]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                $mtBuahTahunall[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                $mtBuahTahunall[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                $mtBuahTahunall[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                $mtBuahTahunall[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                $mtBuahTahunall[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
            } else {
                $mtBuahTahunall[$key]['skor_mentah'] = $skor_mentah = 0;
                $mtBuahTahunall[$key]['skor_masak'] = $skor_masak = 0;
                $mtBuahTahunall[$key]['skor_over'] = $skor_over = 0;
                $mtBuahTahunall[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                $mtBuahTahunall[$key]['skor_vcut'] = $skor_vcut =  0;
                $mtBuahTahunall[$key]['skor_kr'] = $skor_kr = 0;
            }


            $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;




            // $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_jangkos_mb($Perkosongjjg) + skor_vcut_mb($PerVcut)  + skor_abr_mb($per_kr);

            $mtBuahTahunall[$key]['tph_blok'] = $dataBLok;
            $mtBuahTahunall[$key]['sampleJJG_total'] = $sum_Samplejjg;
            $mtBuahTahunall[$key]['total_mentah'] = $sum_bmt;
            $mtBuahTahunall[$key]['total_perMentah'] = $PerMth;
            $mtBuahTahunall[$key]['total_masak'] = $sum_bmk;
            $mtBuahTahunall[$key]['total_perMasak'] = $PerMsk;
            $mtBuahTahunall[$key]['total_over'] = $sum_over;
            $mtBuahTahunall[$key]['total_perOver'] = $PerOver;
            $mtBuahTahunall[$key]['total_abnormal'] = $sum_abnor;
            $mtBuahTahunall[$key]['total_jjgKosong'] = $sum_kosongjjg;
            $mtBuahTahunall[$key]['total_perKosongjjg'] = $Perkosongjjg;
            $mtBuahTahunall[$key]['total_vcut'] = $sum_vcut;

            $mtBuahTahunall[$key]['jum_kr'] = $sum_kr;
            $mtBuahTahunall[$key]['total_kr'] = $total_kr;
            $mtBuahTahunall[$key]['persen_kr'] = $per_kr;

            // skoring
            // $mtBuahTahunall[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
            // $mtBuahTahunall[$key]['skor_masak'] = skor_buah_masak_mb($PerMsk);
            // $mtBuahTahunall[$key]['skor_over'] = skor_buah_over_mb($PerOver);
            // $mtBuahTahunall[$key]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
            // $mtBuahTahunall[$key]['skor_vcut'] = skor_vcut_mb($PerVcut);
            // $mtBuahTahunall[$key]['skor_kr'] = skor_abr_mb($per_kr);
            $mtBuahTahunall[$key]['TOTAL_SKOR'] = $totalSkor;
        }
        // dd($defperbulanMTbh);
        //mutu buah regional perbulan]
        $RegMtBhAfdBln = array();
        foreach ($defperbulanMTbh as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)  if (!empty($value2)) {
                    $dataBLok = 0;
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
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
                    $no_Vcut = 0;
                    $jml_mth = 0;
                    $jml_mtg = 0;
                    $combination_counts = array();
                    foreach ($value2 as $key3 => $value3) {
                        $combination = $value3['blok'] . ' ' . $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $combination_counts[$combination]++;

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
                    // dd($sum_vcut);
                    $dataBLok = count($combination_counts);
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }

                    $no_Vcut = $sum_Samplejjg - $sum_vcut;
                    $per_kr = round($total_kr * 100, 2);
                    $denom1 = ($sum_Samplejjg - $sum_abnor) != 0 ? ($sum_Samplejjg - $sum_abnor) : 1; // set denominator to 1 if it is zero
                    $denom2 = $sum_Samplejjg != 0 ? $sum_Samplejjg : 1; // set denominator to 1 if it is zero

                    $PerMth = round(($jml_mth / $denom1) * 100, 2);
                    $PerMsk = round(($jml_mtg / $denom1) * 100, 2);
                    $PerOver = round(($sum_over / $denom1) * 100, 2);
                    $Perkosongjjg = round(($sum_kosongjjg / $denom1) * 100, 2);
                    $PerVcut = round(($sum_vcut / $denom2) * 100, 2);
                    $PerAbr = round(($sum_abnor / $denom2) * 100, 2);

                    // $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    // $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    // $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    // $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    // $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                    // $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);

                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $RegMtBhAfdBln[$key][$key1][$key2]['tph_baris_blok'] = $dataBLok;
                    $RegMtBhAfdBln[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_over'] = $sum_over;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_vcut'] = $sum_vcut;

                    $RegMtBhAfdBln[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $RegMtBhAfdBln[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $RegMtBhAfdBln[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;
                } else {
                    $RegMtBhAfdBln[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_mentah'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perMentah'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_masak'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perMasak'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_over'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perOver'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_abnormal'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_vcut'] = 0;

                    $RegMtBhAfdBln[$key][$key1][$key2]['jum_kr'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['total_kr'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_mentah'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_masak'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_over'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_vcut'] = 0;

                    $RegMtBhAfdBln[$key][$key1][$key2]['skor_kr'] = 0;
                    $RegMtBhAfdBln[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                }
            }
        }

        // dd($RegMtBhAfdBln);

        $RegMTBHESTbln = array();
        foreach ($RegMtBhAfdBln as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLok = 0;
                $sum_bmt = 0;
                $sum_bmk = 0;
                $sum_over = 0;
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
                $perbagi = 0;
                $no_Vcut = 0;
                foreach ($value1 as $key2 => $value2) {
                    $dataBLok += $value2['tph_baris_blok'];
                    $sum_bmt += $value2['total_mentah'];
                    $sum_bmk += $value2['total_masak'];
                    $sum_over += $value2['total_over'];
                    $sum_kosongjjg += $value2['total_jjgKosong'];
                    $sum_vcut += $value2['total_vcut'];
                    $sum_kr += $value2['jum_kr'];

                    $sum_Samplejjg += $value2['sampleJJG_total'];
                    $sum_abnor += $value2['total_abnormal'];
                }
                if ($sum_kr != 0) {
                    $total_kr = round($sum_kr / $dataBLok, 2);
                } else {
                    $total_kr = 0;
                }

                $no_Vcut = $sum_Samplejjg - $sum_vcut;
                if ($sum_bmt != 0) {
                    $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMth = 0;
                }
                if ($sum_bmk != 0) {
                    $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }

                if ($sum_over != 0) {
                    $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerOver = 0;
                }

                if ($sum_kosongjjg != 0) {
                    $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }

                if ($sum_vcut != 0) {
                    $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerVcut = 0;
                }

                if ($sum_abnor != 0) {
                    $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerAbr = 0;
                }

                $per_kr = round($total_kr * 100, 2);


                $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                $RegMTBHESTbln[$key][$key1]['tph_baris_blok'] = $dataBLok;
                $RegMTBHESTbln[$key][$key1]['sampleJJG_total'] = $sum_Samplejjg;
                $RegMTBHESTbln[$key][$key1]['total_mentah'] = $sum_bmt;
                $RegMTBHESTbln[$key][$key1]['total_perMentah'] = $PerMth;
                $RegMTBHESTbln[$key][$key1]['total_masak'] = $sum_bmk;
                $RegMTBHESTbln[$key][$key1]['total_perMasak'] = $PerMsk;
                $RegMTBHESTbln[$key][$key1]['total_over'] = $sum_over;
                $RegMTBHESTbln[$key][$key1]['total_perOver'] = $PerOver;
                $RegMTBHESTbln[$key][$key1]['total_abnormal'] = $sum_abnor;
                $RegMTBHESTbln[$key][$key1]['total_jjgKosong'] = $sum_kosongjjg;
                $RegMTBHESTbln[$key][$key1]['total_perKosongjjg'] = $Perkosongjjg;
                $RegMTBHESTbln[$key][$key1]['total_vcut'] = $sum_vcut;

                $RegMTBHESTbln[$key][$key1]['jum_kr'] = $sum_kr;
                $RegMTBHESTbln[$key][$key1]['total_kr'] = $total_kr;
                $RegMTBHESTbln[$key][$key1]['persen_kr'] = $per_kr;

                // skoring
                $RegMTBHESTbln[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                $RegMTBHESTbln[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                $RegMTBHESTbln[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);
                $RegMTBHESTbln[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                $RegMTBHESTbln[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);

                $RegMTBHESTbln[$key][$key1]['skor_kr'] = skor_abr_mb($per_kr);
                $RegMTBHESTbln[$key][$key1]['TOTAL_SKOR'] = $totalSkor;
            } else {
                $RegMTBHESTbln[$key][$key1]['tph_baris_blok'] = 0;
                $RegMTBHESTbln[$key][$key1]['sampleJJG_total'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_mentah'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_perMentah'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_masak'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_perMasak'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_over'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_perOver'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_abnormal'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_jjgKosong'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_perKosongjjg'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_vcut'] = 0;

                $RegMTBHESTbln[$key][$key1]['jum_kr'] = 0;
                $RegMTBHESTbln[$key][$key1]['total_kr'] = 0;
                $RegMTBHESTbln[$key][$key1]['persen_kr'] = 0;

                // skoring
                $RegMTBHESTbln[$key][$key1]['skor_mentah'] = 0;
                $RegMTBHESTbln[$key][$key1]['skor_masak'] = 0;
                $RegMTBHESTbln[$key][$key1]['skor_over'] = 0;
                $RegMTBHESTbln[$key][$key1]['skor_jjgKosong'] = 0;
                $RegMTBHESTbln[$key][$key1]['skor_vcut'] = 0;

                $RegMTBHESTbln[$key][$key1]['skor_kr'] = 0;
                $RegMTBHESTbln[$key][$key1]['TOTAL_SKOR'] = 0;
            }
        }
        // dd($RegMTBHESTbln);
        $RegMTbuahBln = array();
        foreach ($RegMTBHESTbln as $key => $value) if (!empty($value)) {
            $dataBLok = 0;
            $sum_bmt = 0;
            $sum_bmk = 0;
            $sum_over = 0;
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
            $perbagi = 0;
            $no_Vcut = 0;
            foreach ($value as $key1 => $value2) {
                $dataBLok += $value2['tph_baris_blok'];
                $sum_bmt += $value2['total_mentah'];
                $sum_bmk += $value2['total_masak'];
                $sum_over += $value2['total_over'];
                $sum_kosongjjg += $value2['total_jjgKosong'];
                $sum_vcut += $value2['total_vcut'];
                $sum_kr += $value2['jum_kr'];

                $sum_Samplejjg += $value2['sampleJJG_total'];
                $sum_abnor += $value2['total_abnormal'];
            }
            if ($sum_kr != 0) {
                $total_kr = round($sum_kr / $dataBLok, 2);
            } else {
                $total_kr = 0;
            }

            $no_Vcut = $sum_Samplejjg - $sum_vcut;
            if ($sum_bmt != 0) {
                $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerMth = 0;
            }
            if ($sum_bmk != 0) {
                $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerMsk = 0;
            }

            if ($sum_over != 0) {
                $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $PerOver = 0;
            }

            if ($sum_kosongjjg != 0) {
                $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
            } else {
                $Perkosongjjg = 0;
            }

            if ($sum_vcut != 0) {
                $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
            } else {
                $PerVcut = 0;
            }

            if ($sum_abnor != 0) {
                $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
            } else {
                $PerAbr = 0;
            }

            $per_kr = round($total_kr * 100, 2);

            $nonZeroValues = array_filter([$sum_Samplejjg, $sum_bmt, $sum_bmk, $sum_over, $sum_abnor, $sum_kosongjjg, $sum_vcut]);

            if (!empty($nonZeroValues)) {
                $RegMTbuahBln[$key]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMth);
                $RegMTbuahBln[$key]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMsk);
                $RegMTbuahBln[$key]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOver);
                $RegMTbuahBln[$key]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($Perkosongjjg);
                $RegMTbuahBln[$key]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcut);
                $RegMTbuahBln[$key]['skor_kr'] = $skor_kr =  skor_abr_mb($per_kr);
            } else {
                $RegMTbuahBln[$key]['skor_mentah'] = $skor_mentah = 0;
                $RegMTbuahBln[$key]['skor_masak'] = $skor_masak = 0;
                $RegMTbuahBln[$key]['skor_over'] = $skor_over = 0;
                $RegMTbuahBln[$key]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                $RegMTbuahBln[$key]['skor_vcut'] = $skor_vcut =  0;
                $RegMTbuahBln[$key]['skor_kr'] = $skor_kr = 0;
            }

            $totalSkor = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;
            // $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

            $RegMTbuahBln[$key]['tph_baris_blok'] = $dataBLok;
            $RegMTbuahBln[$key]['sampleJJG_total'] = $sum_Samplejjg;
            $RegMTbuahBln[$key]['total_mentah'] = $sum_bmt;
            $RegMTbuahBln[$key]['total_perMentah'] = $PerMth;
            $RegMTbuahBln[$key]['total_masak'] = $sum_bmk;
            $RegMTbuahBln[$key]['total_perMasak'] = $PerMsk;
            $RegMTbuahBln[$key]['total_over'] = $sum_over;
            $RegMTbuahBln[$key]['total_perOver'] = $PerOver;
            $RegMTbuahBln[$key]['total_abnormal'] = $sum_abnor;
            $RegMTbuahBln[$key]['total_jjgKosong'] = $sum_kosongjjg;
            $RegMTbuahBln[$key]['total_perKosongjjg'] = $Perkosongjjg;
            $RegMTbuahBln[$key]['total_vcut'] = $sum_vcut;

            $RegMTbuahBln[$key]['jum_kr'] = $sum_kr;
            $RegMTbuahBln[$key]['total_kr'] = $total_kr;
            $RegMTbuahBln[$key]['persen_kr'] = $per_kr;

            // skoring
            // $RegMTbuahBln[$key]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
            // $RegMTbuahBln[$key]['skor_masak'] = skor_buah_masak_mb($PerMsk);
            // $RegMTbuahBln[$key]['skor_over'] = skor_buah_over_mb($PerOver);
            // $RegMTbuahBln[$key]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
            // $RegMTbuahBln[$key]['skor_vcut'] = skor_vcut_mb($PerVcut);

            // $RegMTbuahBln[$key]['skor_kr'] = skor_abr_mb($per_kr);
            $RegMTbuahBln[$key]['TOTAL_SKOR'] = $totalSkor;
        } else {
            $RegMTbuahBln[$key]['tph_baris_blok'] = 0;
            $RegMTbuahBln[$key]['sampleJJG_total'] = 0;
            $RegMTbuahBln[$key]['total_mentah'] = 0;
            $RegMTbuahBln[$key]['total_perMentah'] = 0;
            $RegMTbuahBln[$key]['total_masak'] = 0;
            $RegMTbuahBln[$key]['total_perMasak'] = 0;
            $RegMTbuahBln[$key]['total_over'] = 0;
            $RegMTbuahBln[$key]['total_perOver'] = 0;
            $RegMTbuahBln[$key]['total_abnormal'] = 0;
            $RegMTbuahBln[$key]['total_jjgKosong'] = 0;
            $RegMTbuahBln[$key]['total_perKosongjjg'] = 0;
            $RegMTbuahBln[$key]['total_vcut'] = 0;

            $RegMTbuahBln[$key]['jum_kr'] = 0;
            $RegMTbuahBln[$key]['total_kr'] = 0;
            $RegMTbuahBln[$key]['persen_kr'] = 0;

            // skoring
            $RegMTbuahBln[$key]['skor_mentah'] = 0;
            $RegMTbuahBln[$key]['skor_masak'] = 0;
            $RegMTbuahBln[$key]['skor_over'] = 0;
            $RegMTbuahBln[$key]['skor_jjgKosong'] = 0;
            $RegMTbuahBln[$key]['skor_vcut'] = 0;

            $RegMTbuahBln[$key]['skor_kr'] = 0;
            $RegMTbuahBln[$key]['TOTAL_SKOR'] = 0;
        }
        // dd($RegMTbuahBln);

        // foreach ($mtBuahAllEst as)
        //mutu buah regionan pertahun
        $RegMTbuahTHn = array();
        $dataBLok = 0;
        $sum_bmt = 0;
        $sum_bmk = 0;
        $sum_over = 0;
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
        $perbagi = 0;
        $no_Vcut = 0;
        foreach ($mtBuahTahunall as $key => $value) {
            // dd($value);
            $dataBLok += $value['tph_blok'];
            $sum_bmt += $value['total_mentah'];
            $sum_bmk += $value['total_masak'];
            $sum_over += $value['total_over'];
            $sum_kosongjjg += $value['total_jjgKosong'];
            $sum_vcut += $value['total_vcut'];
            $sum_kr += $value['jum_kr'];

            $sum_Samplejjg += $value['sampleJJG_total'];
            $sum_abnor += $value['total_abnormal'];
        }

        if ($sum_kr != 0) {
            $total_kr = round($sum_kr / $dataBLok, 2);
        } else {
            $total_kr = 0;
        }

        $no_Vcut = $sum_Samplejjg - $sum_vcut;

        if ($sum_bmt != 0) {
            $PerMth = round(($sum_bmt / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
        } else {
            $PerMth = 0;
        }
        if ($sum_bmk != 0) {
            $PerMsk = round(($sum_bmk / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
        } else {
            $PerMsk = 0;
        }

        if ($sum_over != 0) {
            $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
        } else {
            $PerOver = 0;
        }

        if ($sum_kosongjjg != 0) {
            $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
        } else {
            $Perkosongjjg = 0;
        }

        if ($sum_vcut != 0) {
            $PerVcut = round(($no_Vcut / $sum_Samplejjg) * 100, 2);

            // $PerVcut = 2.91;
        } else {
            $PerVcut = 0;
        }

        if ($sum_abnor != 0) {
            $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
        } else {
            $PerAbr = 0;
        }

        $per_kr = round($total_kr * 100, 2);

        $nonZeroValuesBuah = array_filter([
            $dataBLok,
            $sum_bmt,
            $sum_bmk,
            $sum_over,
            $sum_kosongjjg,
            $sum_vcut,
            $sum_kr,
            $sum_Samplejjg,
            $sum_abnor
        ]);

        if (!empty($nonZeroValuesBuah)) {
            $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
        } else {
            $totalSkor = 0;
        }



        $RegMTbuahTHn['I']['tph_blok'] = $dataBLok;
        $RegMTbuahTHn['I']['sampleJJG_total'] = $sum_Samplejjg;
        $RegMTbuahTHn['I']['total_mentah'] = $sum_bmt;
        $RegMTbuahTHn['I']['total_perMentah'] = $PerMth;
        $RegMTbuahTHn['I']['total_masak'] = $sum_bmk;
        $RegMTbuahTHn['I']['total_perMasak'] = $PerMsk;
        $RegMTbuahTHn['I']['total_over'] = $sum_over;
        $RegMTbuahTHn['I']['total_perOver'] = $PerOver;
        $RegMTbuahTHn['I']['total_abnormal'] = $sum_abnor;
        $RegMTbuahTHn['I']['total_jjgKosong'] = $sum_kosongjjg;
        $RegMTbuahTHn['I']['total_perKosongjjg'] = $Perkosongjjg;
        $RegMTbuahTHn['I']['total_Novcut'] = $no_Vcut;
        $RegMTbuahTHn['I']['per_vcut'] = $PerVcut;

        $RegMTbuahTHn['I']['jum_kr'] = $sum_kr;
        $RegMTbuahTHn['I']['total_kr'] = $total_kr;
        $RegMTbuahTHn['I']['persen_kr'] = $per_kr;

        // skoring
        $RegMTbuahTHn['I']['skor_mentah'] = skor_buah_mentah_mb($PerMth);
        $RegMTbuahTHn['I']['skor_masak'] = skor_buah_masak_mb($PerMsk);
        $RegMTbuahTHn['I']['skor_over'] = skor_buah_over_mb($PerOver);
        $RegMTbuahTHn['I']['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
        $RegMTbuahTHn['I']['skor_vcut'] = skor_vcut_mb($PerVcut);

        $RegMTbuahTHn['I']['skor_kr'] = skor_abr_mb($per_kr);
        $RegMTbuahTHn['I']['TOTAL_SKOR'] = $totalSkor;

        // dd($RegMTbuahTHn);
        // dd($mtBuahAllEst, $mtTranstAllbln, $bulanAllEST);
        // dd($bulanAllEST);
        // dd($WilMtAncakThn, $mtTransTahun, $mtBuahTahunall);

        $datamtBuahAFD = array();
        foreach ($queryMTbuah as $key => $value) {
            $datamtBuahAFD[$key] = array();
            foreach ($value as $key2 => $value2) {
                $datamtBuahAFD[$key][$key2] = array();
                foreach ($bulan as $month) {
                    $datamtBuahAFD[$key][$key2][$month] = array();
                    foreach ($value2 as $key3 => $value3) {
                        $date_month = date('F', strtotime($value3['datetime']));
                        if ($date_month == $month) {
                            array_push($datamtBuahAFD[$key][$key2][$month], $value3);
                        }
                    }
                }
            }
        }
        // dd($datamtBuahAFD);
        //membuat nilai untuk mutu ancakan berdasarkan afdeling perbulan
        $datamtAncakAFD = array();
        foreach ($querytahun as $key => $value) {
            $datamtAncakAFD[$key] = array();
            foreach ($value as $key2 => $value2) {
                $datamtAncakAFD[$key][$key2] = array();
                foreach ($bulan as $month) {
                    $datamtAncakAFD[$key][$key2][$month] = array();
                    foreach ($value2 as $key3 => $value3) {
                        $date_month = date('F', strtotime($value3['datetime']));
                        if ($date_month == $month) {
                            array_push($datamtAncakAFD[$key][$key2][$month], $value3);
                        }
                    }
                }
            }
        }
        // dd($datamtAncakAFD);
        //membuat nilai untuk mutu transport berdasarkan afdeling perbulan
        $datamtTransAFD = array();
        foreach ($queryMTtrans as $key => $value) {
            $datamtTransAFD[$key] = array();
            foreach ($value as $key2 => $value2) {
                $datamtTransAFD[$key][$key2] = array();
                foreach ($bulan as $month) {
                    $datamtTransAFD[$key][$key2][$month] = array();
                    foreach ($value2 as $key3 => $value3) {
                        $date_month = date('F', strtotime($value3['datetime']));
                        if ($date_month == $month) {
                            array_push($datamtTransAFD[$key][$key2][$month], $value3);
                        }
                    }
                }
            }
        }
        // dd($datamtTransAFD);
        //membuat nilai mutu buah afdeling perbulan untuk nilai default
        $defBuahAFDtab = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defBuahAFDtab[$est['est']][$afd['nama']][$month] = 0;
                    }
                }
            }
        }
        //membuat nilai mutu ancak afdeling perbulan untuk nilai default
        $defaultTabAFD = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultTabAFD[$est['est']][$afd['nama']][$month] = 0;
                    }
                }
            }
        }
        //membuat nilai mutu trans afdeling perbulan untuk nilai default
        $defTransAFDtab = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defTransAFDtab[$est['est']][$afd['nama']][$month] = 0;
                    }
                }
            }
        }
        //meninmpa nilai defaul dengan isi data yang di atas tadi untuk mutu buah
        foreach ($datamtBuahAFD as $estKey => $estValue) {
            foreach ($estValue as $afdKey => $afdValue) {
                foreach ($afdValue as $monthKey => $monthValue) {
                    if (!empty($monthValue)) {
                        $defBuahAFDtab[$estKey][$afdKey][$monthKey] = $monthValue;
                    } else {
                        $defBuahAFDtab[$estKey][$afdKey][$monthKey] = 0;
                    }
                }
            }
        }
        // dd($datamtBuahAFD, $defBuahAFDtab);
        //meninmpa nilai defaul dengan isi data yang di atas tadi untuk mutu ancak
        foreach ($datamtAncakAFD as $estKey => $estValue) {
            foreach ($estValue as $afdKey => $afdValue) {
                foreach ($afdValue as $monthKey => $monthValue) {
                    if (!empty($monthValue)) {
                        $defaultTabAFD[$estKey][$afdKey][$monthKey] = $monthValue;
                    } else {
                        $defaultTabAFD[$estKey][$afdKey][$monthKey] = 0;
                    }
                }
            }
        }

        //meninmpa nilai defaul dengan isi data yang di atas tadi untuk mutu trans
        foreach ($datamtTransAFD as $estKey => $estValue) {
            foreach ($estValue as $afdKey => $afdValue) {
                foreach ($afdValue as $monthKey => $monthValue) {
                    if (!empty($monthValue)) {
                        $defTransAFDtab[$estKey][$afdKey][$monthKey] = $monthValue;
                    } else {
                        $defTransAFDtab[$estKey][$afdKey][$monthKey] = 0;
                    }
                }
            }
        }
        // dd($defTransAFDtab);
        //perhitungan mutu buah untuk afd perbulan untuk tabel paling akhir mencari tahun
        $MtBuahtabAFDbln = array();
        foreach ($defBuahAFDtab as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)  if (is_array($value2)) {
                    $sum_bmt = 0;
                    $sum_bmk = 0;
                    $sum_over = 0;
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
                    $no_Vcut = 0;
                    $jml_mth = 0;
                    $jml_mtg = 0;
                    $combination_counts = array();
                    $dataBLok = 0;
                    $listBlokPerAfd = [];
                    foreach ($value2 as $ke3 => $value3) {
                        $combination = $value3['blok'] . ' ' . $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['tph_baris'];
                        if (!isset($combination_counts[$combination])) {
                            $combination_counts[$combination] = 0;
                        }
                        $combination_counts[$combination]++;

                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                        $dtBlok = count($listBlokPerAfd);

                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];


                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }
                    $jml_mth = ($sum_bmt + $sum_bmk);
                    $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                    // dd($sum_vcut);
                    $dataBLok += $dtBlok;
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dataBLok, 2);
                    } else {
                        $total_kr = 0;
                    }

                    $no_Vcut = $sum_Samplejjg - $sum_vcut;

                    if ($jml_mth != 0) {
                        $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMth = 0;
                    }
                    if ($jml_mtg != 0) {
                        $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMsk = 0;
                    }

                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerOver = 0;
                    }

                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $Perkosongjjg = 0;
                    }

                    if ($sum_vcut != 0) {
                        $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_abnor != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerAbr = 0;
                    }

                    $per_kr = round($total_kr * 100, 2);


                    $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                    $MtBuahtabAFDbln[$key][$key1][$key2]['tph_baris_blok'] = $dataBLok;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_over'] = $sum_over;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_vcut'] = $sum_vcut;

                    $MtBuahtabAFDbln[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_vcut'] =  skor_vcut_mb($PerVcut);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $MtBuahtabAFDbln[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['check_arr'] = 'ada';
                } else {
                    $MtBuahtabAFDbln[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_mentah'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perMentah'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_masak'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perMasak'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_over'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perOver'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_abnormal'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_vcut'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['jum_kr'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['total_kr'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_mentah'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_masak'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_over'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_vcut'] = 0;

                    $MtBuahtabAFDbln[$key][$key1][$key2]['skor_kr'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                    $MtBuahtabAFDbln[$key][$key1][$key2]['check_arr'] = 'kosong';
                }
            }
        }
        // dd($MtBuahtabAFDbln['RDE']);
        $AfdThnMtBuah = array();
        foreach ($MtBuahtabAFDbln as $key => $value) {
            foreach ($value as $key1 => $value1)  if (!empty($value1)) {
                $tph_blok = 0;
                $jjgMth = 0;
                $sampleJJG = 0;
                $jjgAbn = 0;
                $PerMth = 0;
                $PerMsk = 0;
                $PerOver = 0;
                $Perkosongjjg = 0;
                $PerVcut = 0;
                $PerAbr = 0;
                $per_kr = 0;
                $jjgMsk = 0;
                $jjgOver = 0;
                $jjgKosng = 0;
                $vcut = 0;
                $jum_kr = 0;
                $total_kr = 0;
                $totalSkor = 0;
                $no_Vcut = 0;
                $checkArray = array();

                foreach ($value1 as $key2 => $value2) {
                    // dd($value3);
                    $tph_blok += $value2['tph_baris_blok'];
                    $sampleJJG += $value2['sampleJJG_total'];
                    $jjgMth += $value2['total_mentah'];
                    $jjgMsk += $value2['total_masak'];
                    $jjgOver += $value2['total_over'];
                    $jjgKosng += $value2['total_jjgKosong'];
                    $vcut += $value2['total_vcut'];
                    $jum_kr += $value2['jum_kr'];

                    $jjgAbn += $value2['total_abnormal'];
                    $checkArray[] = $value2['check_arr'];
                }

                $no_Vcut = $sampleJJG - $vcut;



                if ($jum_kr != 0) {
                    $total_kr = round($jum_kr / $tph_blok, 2);
                } else {
                    $total_kr = 0;
                }

                if ($jjgMth != 0) {
                    $PerMth = round(($jjgMth / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMth = 0;
                }
                if ($jjgMsk != 0) {
                    $PerMsk = round(($jjgMsk / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }

                if ($jjgOver != 0) {
                    $PerOver = round(($jjgOver / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerOver = 0;
                }

                if ($jjgKosng != 0) {
                    $Perkosongjjg = round(($jjgKosng / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }

                if ($vcut != 0) {
                    $PerVcut = round(($vcut / $sampleJJG) * 100, 2);
                } else {
                    $PerVcut = 0;
                }

                if ($jjgAbn != 0) {
                    $PerAbr = round(($jjgAbn / $sampleJJG) * 100, 2);
                } else {
                    $PerAbr = 0;
                }

                $per_kr = round($total_kr * 100, 2);
                if (in_array('ada', $checkArray)) {
                    $result = 'ada';
                } else {
                    $result = 'kosong';
                }

                $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                $AfdThnMtBuah[$key][$key1]['blok'] = $tph_blok;
                $AfdThnMtBuah[$key][$key1]['sample_jjg'] = $sampleJJG;

                $AfdThnMtBuah[$key][$key1]['jjg_mentah'] = $jjgMth;
                $AfdThnMtBuah[$key][$key1]['mentahPerjjg'] = $PerMth;

                $AfdThnMtBuah[$key][$key1]['jjg_msk'] = $jjgMsk;
                $AfdThnMtBuah[$key][$key1]['mskPerjjg'] = $PerMsk;

                $AfdThnMtBuah[$key][$key1]['jjg_over'] = $jjgOver;
                $AfdThnMtBuah[$key][$key1]['overPerjjg'] = $PerOver;

                $AfdThnMtBuah[$key][$key1]['jjg_kosong'] = $jjgKosng;
                $AfdThnMtBuah[$key][$key1]['kosongPerjjg'] = $Perkosongjjg;

                $AfdThnMtBuah[$key][$key1]['v_cut'] = $sum_vcut;
                $AfdThnMtBuah[$key][$key1]['vcutPerjjg'] = $PerVcut;

                $AfdThnMtBuah[$key][$key1]['jjg_abr'] = $jjgAbn;
                $AfdThnMtBuah[$key][$key1]['krPer'] = $per_kr;

                $AfdThnMtBuah[$key][$key1]['jum_kr'] = $jum_kr;
                $AfdThnMtBuah[$key][$key1]['abrPerjjg'] = $PerAbr;

                $AfdThnMtBuah[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                $AfdThnMtBuah[$key][$key1]['skor_msak'] =   skor_buah_masak_mb($PerMsk);
                $AfdThnMtBuah[$key][$key1]['skor_over'] =  skor_buah_over_mb($PerOver);
                $AfdThnMtBuah[$key][$key1]['skor_kosong'] = skor_jangkos_mb($Perkosongjjg);
                $AfdThnMtBuah[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);
                $AfdThnMtBuah[$key][$key1]['skor_karung'] = skor_abr_mb($per_kr);

                $AfdThnMtBuah[$key][$key1]['totalSkor'] = $totalSkor;
                $AfdThnMtBuah[$key][$key1]['check_arr'] = $result;
            }
        }


        // dd($AfdThnMtBuah['RDE']);
        //perhitungan mutu trans untuk afd perbulan untuk tabel paling akhir mencari tahun
        $MttranstabAFDbln = array();
        foreach ($defTransAFDtab as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2)  if (is_array($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;
                    $dataBLok = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key3 => $value3) {
                        // dd($value4);
                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        // }
                        $dataBLok = count($listBlokPerAfd);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }

                    $brdPertph = round($sum_bt / $dataBLok, 2);
                    $buahPerTPH = round($sum_rst / $dataBLok, 2);

                    $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $MttranstabAFDbln[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                    $MttranstabAFDbln[$key][$key1][$key2]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                    $MttranstabAFDbln[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                    $MttranstabAFDbln[$key][$key1][$key2]['totalSkor'] = $totalSkor;
                    $MttranstabAFDbln[$key][$key1][$key2]['check_arr'] = 'ada';
                } else {
                    $MttranstabAFDbln[$key][$key1][$key2]['tph_sample'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_brd'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_buah'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['totalSkor'] = 0;
                    $MttranstabAFDbln[$key][$key1][$key2]['check_arr'] = 'kosong';
                }
            }
        }
        // dd($MttranstabAFDbln);
        //perhitungan untuk tahun di afd perbulan unutk mutu trans
        $AfdThnMtTrans = array();
        foreach ($MttranstabAFDbln as $key => $value) {
            foreach ($value as $key1 => $value1)  if (!empty($value1)) {
                $total_sample = 0;
                $total_brd = 0;
                $total_buah = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                $checkArray = array();
                foreach ($value1 as $key2 => $value2) {
                    // dd($value3);
                    $total_sample += $value2['tph_sample'];
                    $total_brd += $value2['total_brd'];
                    $total_buah += $value2['total_buah'];
                    $checkArray[] = $value2['check_arr'];
                }

                if ($total_sample != 0) {
                    $brdPertph = round($total_brd / $total_sample, 2);
                } else {
                    $brdPertph = 0;
                }

                if ($total_sample != 0) {
                    $buahPerTPH = round($total_buah / $total_sample, 2);
                } else {
                    $buahPerTPH = 0;
                }

                if (in_array('ada', $checkArray)) {
                    $result = 'ada';
                } else {
                    $result = 'kosong';
                }
                $totalSkor = skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                $AfdThnMtTrans[$key][$key1]['total_sampleEST'] = $total_sample;
                $AfdThnMtTrans[$key][$key1]['total_brdEST'] = $total_brd;
                $AfdThnMtTrans[$key][$key1]['total_brdPertphEST'] = $brdPertph;
                $AfdThnMtTrans[$key][$key1]['total_buahEST'] = $total_buah;
                $AfdThnMtTrans[$key][$key1]['total_buahPertphEST'] = $buahPerTPH;
                $AfdThnMtTrans[$key][$key1]['skor_brd'] = skor_brd_tinggal($brdPertph);
                $AfdThnMtTrans[$key][$key1]['skor_buah'] = skor_buah_tinggal($buahPerTPH);
                $AfdThnMtTrans[$key][$key1]['total_skor'] = $totalSkor;
                $AfdThnMtTrans[$key][$key1]['check_arr'] = $result;
            }
        }

        // dd($AfdThnMtTrans);
        //perhitungan mutu ancak untuk afd perbulan unutk tabel paling akhir untuk mencari tahun
        $MtancaktabAFDbln = array();
        foreach ($defaultTabAFD as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $listBlokPerAfd = array();
                    $blok = 0;
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
                    $jum_ha = 0;
                    $pelepah_s = 0;
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
                    foreach ($value2 as $key3 => $value3) {
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);


                        $totalPokok += $value3["sample"];
                        $totalPanen += $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen +=  $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];

                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];
                        $totalpelepah_s += $value3["ps"];
                    }
                    $blok = $jum_ha;
                    // $akp = ($janjang_panen / $pokok_panen) %
                    $akp = $totalPokok !== 0 ? round(($totalPanen / $totalPokok) * 100, 1) : 0;

                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 2);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 2);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                    } else {
                        $perPl = 0;
                    }

                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                    $MtancaktabAFDbln[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $MtancaktabAFDbln[$key][$key1][$key2]['ha_sample'] = $blok;
                    $MtancaktabAFDbln[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['akp_rl'] =  $akp;

                    $MtancaktabAFDbln[$key][$key1][$key2]['p'] = $totalP_panen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['k'] = $totalK_panen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    // $MtancaktabAFDbln[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $MtancaktabAFDbln[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;


                    // $MtancaktabAFDbln[$key][$key1][$key2]['jjgperBuah'] = $sumPerBH;
                    // data untuk pelepah sengklek

                    $MtancaktabAFDbln[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    // total skor akhir
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_bh'] =  skor_brd_ma($brdPerjjg);
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_brd'] = skor_buah_Ma($sumPerBH);
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_ps'] = skor_palepah_ma($perPl);
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;
                    $MtancaktabAFDbln[$key][$key1][$key2]['check_arr'] = 'ada';
                } else {
                    $MtancaktabAFDbln[$key][$key1][$key2]['pokok_sample'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['ha_sample'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['akp_rl'] =  0;

                    $MtancaktabAFDbln[$key][$key1][$key2]['p'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['k'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['tgl'] = 0;

                    // $MtancaktabAFDbln[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $MtancaktabAFDbln[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhts_s'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm1'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm2'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['bhtm3'] = 0;


                    // $MtancaktabAFDbln[$key][$key1][$key2]['jjgperBuah'] = $sumPerBH, 2);
                    // data untuk pelepah sengklek

                    $MtancaktabAFDbln[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhir
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_bh'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_brd'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_ps'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['skor_akhir'] = 0;
                    $MtancaktabAFDbln[$key][$key1][$key2]['check_arr'] = 'kosong';
                }
            }
        }
        // dd($MtancaktabAFDbln);
        //mutu ancak hitung perthaun untuk tab afd tahunan
        $AfdThnMtAncak = array();
        foreach ($MtancaktabAFDbln as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $total_brd = 0;
                $total_buah = 0;
                $total_skor = 0;
                $sum_p = 0;
                $sum_k = 0;
                $sum_gl = 0;
                $sum_panen = 0;
                $total_BrdperJJG = 0;
                $sum_pokok = 0;
                $sum_Restan = 0;
                $sum_s = 0;
                $sum_m1 = 0;
                $sum_m2 = 0;
                $sum_m3 = 0;
                $sumPerBH = 0;
                $sum_pelepah = 0;
                $perPl = 0;
                $checkArray = array();
                foreach ($value1 as $key2 => $value2) {
                    $sum_panen += $value2['jumlah_panen'];
                    $sum_pokok += $value2['pokok_sample'];
                    //brondolamn
                    $sum_p += $value2['p'];
                    $sum_k += $value2['k'];
                    $sum_gl += $value2['tgl'];
                    //buah tianggal
                    $sum_s += $value2['bhts_s'];
                    $sum_m1 += $value2['bhtm1'];
                    $sum_m2 += $value2['bhtm2'];
                    $sum_m3 += $value2['bhtm3'];
                    //pelepah
                    $sum_pelepah += $value2['palepah_pokok'];
                    $checkArray[] = $value2['check_arr'];
                }

                $skor_bTinggal = $sum_p + $sum_k + $sum_gl;

                if ($sum_panen != 0) {
                    $brdPerjjg = round($skor_bTinggal / $sum_panen, 2);
                } else {
                    $brdPerjjg = 0;
                }


                $sumBH = $sum_s +  $sum_m1 +  $sum_m2 +  $sum_m3;
                if ($sumBH != 0) {
                    $sumPerBH = round($sumBH / ($sum_panen + $sumBH) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                if ($sum_pelepah != 0) {
                    $perPl = round(($sum_pelepah / $sum_pokok) * 100, 2);
                } else {
                    $perPl = 0;
                }

                $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                if (in_array('ada', $checkArray)) {
                    $result = 'ada';
                } else {
                    $result = 'kosong';
                }



                $AfdThnMtAncak[$key][$key1]['total_p.k.gl'] = $total_brd;
                $AfdThnMtAncak[$key][$key1]['total_jumPanen'] = $sum_panen;
                $AfdThnMtAncak[$key][$key1]['total_jumPokok'] = $sum_pokok;
                $AfdThnMtAncak[$key][$key1]['total_brd/jjg'] = $total_BrdperJJG;
                $AfdThnMtAncak[$key][$key1]['skor_brd'] =  skor_brd_ma($brdPerjjg);
                $AfdThnMtAncak[$key][$key1]['s'] = $sum_s;
                $AfdThnMtAncak[$key][$key1]['m1'] = $sum_m1;
                $AfdThnMtAncak[$key][$key1]['m2'] = $sum_m2;
                $AfdThnMtAncak[$key][$key1]['m3'] = $sum_m3;
                $AfdThnMtAncak[$key][$key1]['total_bh'] = $total_buah;
                $AfdThnMtAncak[$key][$key1]['total_bh/jjg'] = $sumPerBH;
                $AfdThnMtAncak[$key][$key1]['skor_bh'] = skor_buah_Ma($sumPerBH);
                $AfdThnMtAncak[$key][$key1]['pokok_palepah'] = $sum_pelepah;
                $AfdThnMtAncak[$key][$key1]['perPalepah'] = $perPl;
                $AfdThnMtAncak[$key][$key1]['skor_perPl'] = skor_palepah_ma($perPl);
                //total skor akhir
                $AfdThnMtAncak[$key][$key1]['skor_final'] = $ttlSkorMA;
                $AfdThnMtAncak[$key][$key1]['check_arr'] = $result;
            }
        }

        // dd($AfdThnMtAncak['BDE'],$AfdThnMtTrans['BDE'],$AfdThnMtBuah['BDE']);

        // dd($AfdThnMtTrans['BKE']['OD'], $AfdThnMtBuah['BKE']['OD'], $AfdThnMtAncak['BKE']['OD']);

        $RekapTahunAFD = array();
        foreach ($AfdThnMtAncak  as $key => $value) {
            foreach ($value  as $key1 => $value1) {
                foreach ($AfdThnMtTrans  as $key2 => $value2) {
                    foreach ($value2  as $key3 => $value3) {
                        foreach ($AfdThnMtBuah  as $key4 => $value4) if ($key == $key2 && $key2 == $key4) {
                            foreach ($value4  as $key5 => $value5) if ($key1 == $key3 && $key3 == $key5) {
                                if ($value1['check_arr'] == 'kosong' && $value3['check_arr'] == 'kosong' && $value5['check_arr'] == 'kosong') {
                                    $RekapTahunAFD[$key][$key1]['tahun_skorwil'] = 0;
                                } else {
                                    $RekapTahunAFD[$key][$key1]['tahun_skorwil'] = $value1['skor_final'] + $value3['total_skor'] + $value5['totalSkor'];
                                }
                            }
                        }
                    }
                }
            }
        }
        // dd($RekapTahunAFD);
        // foreach ($RekapTahunAFD as $key => $value) {
        //     array_multisort(array_column($RekapTahunAFD[$key], 'tahun_skorwil'), SORT_DESC, $RekapTahunAFD[$key]);
        // }
        // dd($bulanAllEST['2']['February'], $mtBuahAllEst['2']['February'], $mtTranstAllbln['2']['February']);
        // dd( $mtTranstAllbln['1']['April']);

        // dd($bulanAllEST[6]['June'],  $mtTranstAllbln[6]['June'], $mtBuahAllEst[6]['June']);
        // dd($mtBuahAllEst);
        // dd($bulanAllEST);
        //pentotalan skor mt ancak mt transprt mt buah
        $RekapBulanwil = array();
        foreach ($mtBuahAllEst as $key => $value) {
            foreach ($value as $key2  => $value2) {
                foreach ($mtTranstAllbln as $key3 => $value3) {
                    foreach ($value3 as $key4 => $value4) {
                        foreach ($bulanAllEST as $key5 => $value5) if ($key == $key3 && $key3 == $key5) {
                            foreach ($value5 as $key6 => $value6)
                                if ($key2 == $key4 && $key4 == $key6) {
                                    $RekapBulanwil[$key][$key2]['skor_bulanTotal'] = $value4['totalSkor'] + $value2['TOTAL_SKOR'] + $value6['total_skor'];
                                }
                        }
                    }
                }
            }
        }
        // dd($RekapBulanwil);
        $RekapTahunwil = array();
        foreach ($WilMtAncakThn as $key => $value) {
            foreach ($mtTransTahun as $key2 => $value2) {
                foreach ($mtBuahTahunall as $key3 => $value3) {
                    if ($key == $key2 && $key2 == $key3) {
                        $RekapTahunwil[$key]['tahun_skorwil'] = $value['total_skor'] + $value2['totalSkor'] + $value3['TOTAL_SKOR'];
                    }
                }
            }
        }
        // dd($WilMtAncakThn,$mtTransTahun,$mtBuahTahunall);
        // dd($RegMTbuahBln['April'], $RegMTtransBln['April'], $RegMTancakBln['April']);
        //rekap bulan regional 1
        $RekapBulanReg = array();
        foreach ($RegMTbuahBln as $key => $value) {
            foreach ($RegMTtransBln as $key1 => $value2) {
                foreach ($RegMTancakBln as $key2 => $value3) {
                    if ($key == $key1 && $key1 == $key2)
                        $RekapBulanReg[$key]['skor_bulanTotal'] = $value['TOTAL_SKOR'] + $value2['totalSkor'] + $value3['total_skor'];
                }
            }
        }
        // dd($RekapBulanReg);
        //rekap tahun regional 1
        $RekapTahunReg = array();
        foreach ($RegMTanckTHn as $key => $value) {
            foreach ($RegMTtransTHn as $key2 => $value2) {
                foreach ($RegMTbuahTHn as $key3 => $value3) {
                    if ($key == $key2 && $key2 == $key3) {
                        $RekapTahunReg[$key]['tahun_skorwil'] = $value['total_skor'] + $value2['totalSkor'] + $value3['TOTAL_SKOR'];
                    }
                }
            }
        }
        // dd($bulananBh, $dataTahunEst);
        // dd($RegMTanckTHn,
        // $RegMTtransTHn,
        // $RegMTbuahTHn);

        // rekap untuk table perfadling table tarakhir
        $RekapBulanAFD = array();


        // dd($mutuTransAFD['BKE']['April']['OD'], $bulananBh['BKE']['April']['OD'], $dataTahunEst['BKE']['April']['OD']);
        foreach ($mutuTransAFD as $mtKey => $mtValue) {
            foreach ($bulananBh as $bbKey => $bbValue) {
                foreach ($dataTahunEst as $dteKey => $dteValue) {
                    if ($mtKey == $bbKey && $bbKey == $dteKey) {
                        foreach ($mtValue as $mtKey1 => $mtValue1) {
                            foreach ($bbValue as $bbKey1 => $bbValue1) {
                                foreach ($dteValue as $dteKey1 => $dteValue1) {
                                    if ($mtKey1 == $bbKey1 && $bbKey1 == $dteKey1) {
                                        foreach ($mtValue1 as $mtKey2 => $mtValue2) {
                                            foreach ($bbValue1 as $bbKey2 => $bbValue2) {
                                                foreach ($dteValue1 as $dteKey2 => $dteValue2) {
                                                    if ($mtKey2 == $bbKey2 && $bbKey2 == $dteKey2) {
                                                        // if ($mtValue2['check_data'] == 'reg2') {
                                                        //     # code...
                                                        // }
                                                        $RekapBulanAFD[$mtKey][$mtKey1][$mtKey2]['bulan_afd'] = intval($mtValue2['totalSkor'] + $bbValue2['TOTAL_SKOR'] + $dteValue2['skor_akhir']);
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

        // dd($RekapBulanAFD);
        // dd($RekapBulanAFD);
        // //end
        //bagian chart untuk pertahun 

        $chartBTTth = array();
        foreach ($Final_end as $key => $value) {
            // dd($value);
            $chartBTTth[$key] = $value['total_brd/panen'];
        }
        // dd($chartBTTth);
        $chartBHth = array();
        foreach ($Final_end as $key => $value) {
            // dd($value);
            $chartBHth[$key] = $value['total_buah/jjg'];
        }
        // chart untuk perwilayah
        $chartbrdWilTH = array();
        foreach ($WilMtAncakThn as $key => $value) {
            // dd($value);
            $chartbrdWilTH[$key] = $value['brdPerjjg'];
        }
        $chartBhwilTH = array();
        foreach ($WilMtAncakThn as $key => $value) {
            // dd($value);
            $chartBhwilTH[$key] = $value['bhPerjjg'];
        }
        // dd($chartBHth);
        //end
        // dd($RankingFinal);

        $queryEsta = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['CWS1', 'CWS2', 'CWS3'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $RegData)
            // ->where('wil.regional', '3')
            ->pluck('est');

        // Convert the result into an array with numeric keys
        $queryEsta = array_values(json_decode($queryEsta, true));

        function PlasmaID($array)
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

        $queryEsta = PlasmaID($queryEsta);


        //hitungan plasma untuk perwilayah tabel
        //mutu buah


        $PlasmaMtBH = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (isset($PlasmaMtBH[$key][$month][$key2])) {
                        // If the month already exists in the array, append the new value to the existing array
                        $PlasmaMtBH[$key][$month][$key2][] = $value3;
                    } else {
                        // If the month does not exist in the array, create a new array with the new value
                        $PlasmaMtBH[$key][$month][$key2] = array($value3);
                    }
                }
            }
        }
        // dd($PlasmaMtBH);
        $PlasmaDefaultBH = array();
        foreach ($bulan as $month) {
            foreach ($queryEstePla as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $PlasmaDefaultBH[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }
        // dd($PlasmaDefaultBH);
        foreach ($PlasmaDefaultBH as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($PlasmaMtBH as $dataKey => $dataValue) {
                    // dd($dataKey, $key);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $PlasmaDefaultBH[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($PlasmaDefaultBH);
        $PlasmaBuah = array();
        foreach ($PlasmaDefaultBH as $key => $value) if (!empty($value)) {
            $jum_haWil  = 0;
            $sum_SamplejjgWil = 0;
            $sum_bmtWil = 0;
            $sum_bmkWil = 0;
            $sum_overWil = 0;
            $sum_abnorWil = 0;
            $sum_kosongjjgWil = 0;
            $sum_vcutWil = 0;
            $sum_krWil = 0;
            $no_VcutWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
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
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
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
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {

                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'], $listBlokPerAfd)) {
                        $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] . ' ' . $value3['tph_baris'];
                        // }
                        $dtBlok = count($listBlokPerAfd);
                        $sum_bmt += $value3['bmt'];
                        $sum_bmk += $value3['bmk'];
                        $sum_over += $value3['overripe'];
                        $sum_kosongjjg += $value3['empty_bunch'];
                        $sum_vcut += $value3['vcut'];
                        $sum_kr += $value3['alas_br'];
                        $sum_Samplejjg += $value3['jumlah_jjg'];
                        $sum_abnor += $value3['abnormal'];
                    }

                    $jml_mth = ($sum_bmt + $sum_bmk);
                    $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                    // $dataBLok = count($combination_counts);
                    if ($sum_kr != 0) {
                        $total_kr = round($sum_kr / $dtBlok, 2);
                    } else {
                        $total_kr = 0;
                    }


                    $per_kr = round($total_kr * 100, 2);
                    if ($jml_mth != 0) {
                        $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMth = 0;
                    }
                    if ($jml_mtg != 0) {
                        $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerMsk = 0;
                    }
                    if ($sum_over != 0) {
                        $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $PerOver = 0;
                    }
                    if ($sum_kosongjjg != 0) {
                        $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                    } else {
                        $Perkosongjjg = 0;
                    }
                    if ($sum_vcut != 0) {
                        $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerVcut = 0;
                    }

                    if ($sum_abnor != 0) {
                        $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                    } else {
                        $PerAbr = 0;
                    }


                    $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);
                    $PlasmaBuah[$key][$key1][$key2]['tph_baris_blok'] = $dtBlok;
                    $PlasmaBuah[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                    $PlasmaBuah[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                    $PlasmaBuah[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                    $PlasmaBuah[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                    $PlasmaBuah[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                    $PlasmaBuah[$key][$key1][$key2]['total_over'] = $sum_over;
                    $PlasmaBuah[$key][$key1][$key2]['total_perOver'] = $PerOver;
                    $PlasmaBuah[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                    $PlasmaBuah[$key][$key1][$key2]['perAbnormal'] = $PerAbr;
                    $PlasmaBuah[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                    $PlasmaBuah[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                    $PlasmaBuah[$key][$key1][$key2]['total_vcut'] = $sum_vcut;
                    $PlasmaBuah[$key][$key1][$key2]['perVcut'] = $PerVcut;

                    $PlasmaBuah[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                    $PlasmaBuah[$key][$key1][$key2]['total_kr'] = $total_kr;
                    $PlasmaBuah[$key][$key1][$key2]['persen_kr'] = $per_kr;

                    // skoring
                    $PlasmaBuah[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                    $PlasmaBuah[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                    $PlasmaBuah[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                    $PlasmaBuah[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                    $PlasmaBuah[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);

                    $PlasmaBuah[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                    $PlasmaBuah[$key][$key1][$key2]['skorBulanPlasma'] = $totalSkor;

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
                    $PlasmaBuah[$key][$key1][$key2]['tph_baris_blok'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['sampleJJG_total'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_mentah'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_perMentah'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_masak'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_perMasak'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_over'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_perOver'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_abnormal'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['perAbnormal'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_jjgKosong'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_vcut'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['perVcut'] = 0;

                    $PlasmaBuah[$key][$key1][$key2]['jum_kr'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['total_kr'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['persen_kr'] = 0;

                    // skoring
                    $PlasmaBuah[$key][$key1][$key2]['skor_mentah'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skor_masak'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skor_over'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skor_vcut'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skor_abnormal'] = 0;;
                    $PlasmaBuah[$key][$key1][$key2]['skor_kr'] = 0;
                    $PlasmaBuah[$key][$key1][$key2]['skorBulanPlasma'] = 0;
                }
                $no_VcutEst = $sum_SamplejjgEst - $sum_vcutEst;


                if ($sum_krEst != 0) {
                    $total_krEst = round($sum_krEst / $jum_haEst, 2);
                } else {
                    $total_krEst = 0;
                }


                if ($sum_bmtEst != 0) {
                    $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
                } else {
                    $PerMthEst = 0;
                }

                if ($sum_bmkEst != 0) {
                    $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
                } else {
                    $PerMskEst = 0;
                }

                if ($sum_overEst != 0) {
                    $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
                } else {
                    $PerOverEst = 0;
                }
                if ($sum_kosongjjgEst != 0) {
                    $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
                } else {
                    $PerkosongjjgEst = 0;
                }
                if ($sum_vcutEst != 0) {
                    $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 2);
                } else {
                    $PerVcutest = 0;
                }
                if ($sum_abnorEst != 0) {
                    $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 2);
                } else {
                    $PerAbrest = 0;
                }
                // $per_kr = round($sum_kr * 100);
                $per_krEst = round($total_krEst * 100, 2);

                $nonZeroValues = array_filter([$sum_SamplejjgEst, $sum_bmtEst, $sum_bmkEst, $sum_overEst, $sum_abnorEst, $sum_kosongjjgEst, $sum_vcutEst]);

                if (!empty($nonZeroValues)) {
                    $PlasmaBuah[$key][$key1]['skor_mentah'] = $skor_mentah =  skor_buah_mentah_mb($PerMthEst);
                    $PlasmaBuah[$key][$key1]['skor_masak'] = $skor_masak =  skor_buah_masak_mb($PerMskEst);
                    $PlasmaBuah[$key][$key1]['skor_over'] = $skor_over =  skor_buah_over_mb($PerOverEst);
                    $PlasmaBuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong =  skor_jangkos_mb($PerkosongjjgEst);
                    $PlasmaBuah[$key][$key1]['skor_vcut'] = $skor_vcut =  skor_vcut_mb($PerVcutest);
                    $PlasmaBuah[$key][$key1]['skor_kr'] = $skor_kr =  skor_abr_mb($per_krEst);
                } else {
                    $PlasmaBuah[$key][$key1]['skor_mentah'] = $skor_mentah = 0;
                    $PlasmaBuah[$key][$key1]['skor_masak'] = $skor_masak = 0;
                    $PlasmaBuah[$key][$key1]['skor_over'] = $skor_over = 0;
                    $PlasmaBuah[$key][$key1]['skor_jjgKosong'] = $skor_jjgKosong = 0;
                    $PlasmaBuah[$key][$key1]['skor_vcut'] = $skor_vcut =  0;
                    $PlasmaBuah[$key][$key1]['skor_kr'] = $skor_kr = 0;
                }

                $totalSkorEst = $skor_mentah + $skor_masak + $skor_over + $skor_jjgKosong + $skor_vcut + $skor_kr;

                // $totalSkorEst =   skor_buah_mentah_mb($PerMthEst) + skor_buah_masak_mb($PerMskEst) + skor_buah_over_mb($PerOverEst) + skor_jangkos_mb($PerkosongjjgEst) + skor_vcut_mb($PerVcutest) + skor_abr_mb($per_krEst);
                $PlasmaBuah[$key][$key1]['tph_baris_blok'] = $jum_haEst;
                $PlasmaBuah[$key][$key1]['sampleJJG_total'] = $sum_SamplejjgEst;
                $PlasmaBuah[$key][$key1]['total_mentah'] = $sum_bmtEst;
                $PlasmaBuah[$key][$key1]['total_perMentah'] = $PerMthEst;
                $PlasmaBuah[$key][$key1]['total_masak'] = $sum_bmkEst;
                $PlasmaBuah[$key][$key1]['total_perMasak'] = $PerMskEst;
                $PlasmaBuah[$key][$key1]['total_over'] = $sum_overEst;
                $PlasmaBuah[$key][$key1]['total_perOver'] = $PerOverEst;
                $PlasmaBuah[$key][$key1]['total_abnormal'] = $sum_abnorEst;
                $PlasmaBuah[$key][$key1]['total_perabnormal'] = $PerAbrest;
                $PlasmaBuah[$key][$key1]['total_jjgKosong'] = $sum_kosongjjgEst;
                $PlasmaBuah[$key][$key1]['total_perKosongjjg'] = $PerkosongjjgEst;
                $PlasmaBuah[$key][$key1]['total_vcut'] = $sum_vcutEst;
                $PlasmaBuah[$key][$key1]['perVcut'] = $PerVcutest;
                $PlasmaBuah[$key][$key1]['jum_kr'] = $sum_krEst;
                $PlasmaBuah[$key][$key1]['kr_blok'] = $total_krEst;

                $PlasmaBuah[$key][$key1]['persen_kr'] = $per_krEst;

                // skoring
                // $PlasmaBuah[$key][$key1]['skor_mentah'] =  skor_buah_mentah_mb($PerMthEst);
                // $PlasmaBuah[$key][$key1]['skor_masak'] = skor_buah_masak_mb($PerMskEst);
                // $PlasmaBuah[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOverEst);;
                // $PlasmaBuah[$key][$key1]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgEst);
                // $PlasmaBuah[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcutest);
                // $PlasmaBuah[$key][$key1]['skor_kr'] = skor_abr_mb($per_krEst);
                $PlasmaBuah[$key][$key1]['SkorbulanWil'] = $totalSkorEst;

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
                $PlasmaBuah[$key][$key1]['tph_baris_blok'] = 0;
                $PlasmaBuah[$key][$key1]['sampleJJG_total'] = 0;
                $PlasmaBuah[$key][$key1]['total_mentah'] = 0;
                $PlasmaBuah[$key][$key1]['total_perMentah'] = 0;
                $PlasmaBuah[$key][$key1]['total_masak'] = 0;
                $PlasmaBuah[$key][$key1]['total_perMasak'] = 0;
                $PlasmaBuah[$key][$key1]['total_over'] = 0;
                $PlasmaBuah[$key][$key1]['total_perOver'] = 0;
                $PlasmaBuah[$key][$key1]['total_abnormal'] = 0;
                $PlasmaBuah[$key][$key1]['total_perabnormal'] = 0;
                $PlasmaBuah[$key][$key1]['total_jjgKosong'] = 0;
                $PlasmaBuah[$key][$key1]['total_perKosongjjg'] = 0;
                $PlasmaBuah[$key][$key1]['total_vcut'] = 0;
                $PlasmaBuah[$key][$key1]['perVcut'] = 0;
                $PlasmaBuah[$key][$key1]['jum_kr'] = 0;
                $PlasmaBuah[$key][$key1]['kr_blok'] = 0;
                $PlasmaBuah[$key][$key1]['persen_kr'] = 0;

                // skoring
                $PlasmaBuah[$key][$key1]['skor_mentah'] = 0;
                $PlasmaBuah[$key][$key1]['skor_masak'] = 0;
                $PlasmaBuah[$key][$key1]['skor_over'] = 0;
                $PlasmaBuah[$key][$key1]['skor_jjgKosong'] = 0;
                $PlasmaBuah[$key][$key1]['skor_vcut'] = 0;
                $PlasmaBuah[$key][$key1]['skor_abnormal'] = 0;;
                $PlasmaBuah[$key][$key1]['skor_kr'] = 0;
                $PlasmaBuah[$key][$key1]['SkorbulanWil'] = 0;
            }
            if ($sum_krWil != 0) {
                $total_krWil = round($sum_krWil / $jum_haWil, 2);
            } else {
                $total_krWil = 0;
            }


            if ($sum_bmtWil != 0) {
                $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
            } else {
                $PerMthWil = 0;
            }

            if ($sum_bmkWil != 0) {
                $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
            } else {
                $PerMskWil = 0;
            }

            if ($sum_overWil != 0) {
                $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
            } else {
                $PerOverWil = 0;
            }
            if ($sum_kosongjjgWil != 0) {
                $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
            } else {
                $PerkosongjjgWil = 0;
            }
            if ($sum_vcutWil != 0) {
                $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 2);
            } else {
                $PerVcutWil = 0;
            }
            if ($sum_abnorWil != 0) {
                $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 2);
            } else {
                $PerAbrWil = 0;
            }
            // $per_kr = round($sum_kr * 100);
            $per_krWil = round($total_krWil * 100, 2);

            $nonZeroValues = array_filter([
                $sum_PKGL,
                $sum_SamplejjgWil,
                $sum_bmtWil,
                $sum_bmkWil,
                $sum_overWil,
                $sum_abnorWil,
                $sum_kosongjjgWil,
                $sum_vcutWil,
                $sum_krWil
            ]);

            if (!empty($nonZeroValues)) {
                $totalSkorWil =   skor_buah_mentah_mb($PerMthWil) + skor_buah_masak_mb($PerMskWil) + skor_buah_over_mb($PerOverWil) + skor_jangkos_mb($PerkosongjjgWil) + skor_vcut_mb($PerVcutWil) + skor_abr_mb($per_krWil);
            } else {
                $totalSkorWil = 0;
            }



            $PlasmaBuah[$key]['tph_baris_blok'] = $jum_haWil;
            $PlasmaBuah[$key]['sampleJJG_total'] = $sum_SamplejjgWil;
            $PlasmaBuah[$key]['total_mentah'] = $sum_bmtWil;
            $PlasmaBuah[$key]['total_perMentah'] = $PerMthWil;
            $PlasmaBuah[$key]['total_masak'] = $sum_bmkWil;
            $PlasmaBuah[$key]['total_perMasak'] = $PerMskWil;
            $PlasmaBuah[$key]['total_over'] = $sum_overWil;
            $PlasmaBuah[$key]['total_perOver'] = $PerOverWil;
            $PlasmaBuah[$key]['total_abnormal'] = $sum_abnorWil;
            $PlasmaBuah[$key]['total_perabnormal'] = $PerAbrWil;
            $PlasmaBuah[$key]['total_jjgKosong'] = $sum_kosongjjgWil;
            $PlasmaBuah[$key]['total_perKosongjjg'] = $PerkosongjjgWil;
            $PlasmaBuah[$key]['total_vcut'] = $sum_vcutWil;
            $PlasmaBuah[$key]['perVcut'] = $PerVcutWil;
            $PlasmaBuah[$key]['jum_kr'] = $sum_krWil;
            $PlasmaBuah[$key]['kr_blok'] = $total_krWil;

            $PlasmaBuah[$key]['persen_kr'] = $per_krWil;

            // skoring
            $PlasmaBuah[$key]['skor_mentah'] =  skor_buah_mentah_mb($PerMthWil);
            $PlasmaBuah[$key]['skor_masak'] = skor_buah_masak_mb($PerMskWil);
            $PlasmaBuah[$key]['skor_over'] = skor_buah_over_mb($PerOverWil);;
            $PlasmaBuah[$key]['skor_jjgKosong'] = skor_jangkos_mb($PerkosongjjgWil);
            $PlasmaBuah[$key]['skor_vcut'] = skor_vcut_mb($PerVcutWil);
            $PlasmaBuah[$key]['skor_kr'] = skor_abr_mb($per_krWil);
            $PlasmaBuah[$key]['PlasmaSkorTahun'] = $totalSkorWil;
        }
        // dd($PlasmaBuah);
        //mutubuah end cari data 



        //mutu ancak
        $PlasmaMtAncak = array();
        foreach ($querytahun as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (isset($PlasmaMtAncak[$key][$month][$key2])) {
                        // If the month already exists in the array, append the new value to the existing array
                        $PlasmaMtAncak[$key][$month][$key2][] = $value3;
                    } else {
                        // If the month does not exist in the array, create a new array with the new value
                        $PlasmaMtAncak[$key][$month][$key2] = array($value3);
                    }
                }
            }
        }

        // dd($PlasmaMtAncak);
        $PlasmaDefaultAncak = array();
        foreach ($bulan as $month) {
            foreach ($queryEstePla as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $PlasmaDefaultAncak[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }

        // dd($PlasmaDefaultAncak);
        foreach ($PlasmaDefaultAncak as $key => $estValue) {
            // dd($key);
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($PlasmaMtAncak as $dataKey => $dataValue) {
                    // dd($dataValue);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $PlasmaDefaultAncak[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($PlasmaDefaultAncak);
        $PlasmaAncak = array();
        foreach ($PlasmaDefaultAncak as $key => $value) if (!empty($value1)) {
            $pokok_panenWil = 0;
            $jum_haWil =  0;
            $janjang_panenWil =  0;
            $akpWil =  0;
            $p_panenWil =  0;
            $k_panenWil =  0;
            $brtgl_panenWil = 0;
            $brdPerjjgWil =  0;
            $bhtsWil = 0;
            $bhtm1Wil = 0;
            $bhtm2Wil = 0;
            $bhtm3Wil = 0;
            $pelepah_sWil = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
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
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                    } else {
                        $perPl = 0;
                    }



                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                    $PlasmaAncak[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $PlasmaAncak[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $PlasmaAncak[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $PlasmaAncak[$key][$key1][$key2]['akp_rl'] = $akp;

                    $PlasmaAncak[$key][$key1][$key2]['p'] = $totalP_panen;
                    $PlasmaAncak[$key][$key1][$key2]['k'] = $totalK_panen;
                    $PlasmaAncak[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;

                    // $PlasmaAncak[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $PlasmaAncak[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $PlasmaAncak[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;
                    $PlasmaAncak[$key][$key1][$key2]['buah/jjg'] = $sumPerBH;

                    // $PlasmaAncak[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 2);
                    // data untuk pelepah sengklek

                    $PlasmaAncak[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    // total skor akhir
                    $PlasmaAncak[$key][$key1][$key2]['skor_bh'] = skor_brd_ma($brdPerjjg);
                    $PlasmaAncak[$key][$key1][$key2]['skor_brd'] = skor_buah_Ma($sumPerBH);
                    $PlasmaAncak[$key][$key1][$key2]['skor_ps'] = skor_palepah_ma($perPl);
                    $PlasmaAncak[$key][$key1][$key2]['skorBulanPlasma'] = $ttlSkorMA;

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
                    $PlasmaAncak[$key][$key1][$key2]['pokok_sample'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['ha_sample'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['akp_rl'] =  0;

                    $PlasmaAncak[$key][$key1][$key2]['p'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['k'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['tgl'] = 0;

                    // $PlasmaAncak[$key][$key1]['total_brd'] = $skor_bTinggal;
                    $PlasmaAncak[$key][$key1][$key2]['brd/jjg'] = 0;

                    // data untuk buah tinggal
                    $PlasmaAncak[$key][$key1][$key2]['bhts_s'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm1'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm2'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['bhtm3'] = 0;

                    // $PlasmaAncak[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 2);
                    // data untuk pelepah sengklek

                    $PlasmaAncak[$key][$key1][$key2]['palepah_pokok'] = 0;
                    // total skor akhi0;

                    $PlasmaAncak[$key][$key1][$key2]['skor_bh'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['skor_brd'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['skor_ps'] = 0;
                    $PlasmaAncak[$key][$key1][$key2]['skorBulanPlasma'] = 0;
                }
                $sumBHEst = $bhtsEST +  $bhtm1EST +  $bhtm2EST +  $bhtm3EST;
                $totalPKT = $p_panenEst + $k_panenEst + $brtgl_panenEst;
                // dd($sumBHEst);
                if ($pokok_panenEst != 0) {
                    $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 2);
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
                    $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 2);
                } else {
                    $perPlEst = 0;
                }

                $nonZeroValues = array_filter([$p_panenEst, $k_panenEst, $brtgl_panenEst, $bhtsEST, $bhtm1EST, $bhtm2EST, $bhtm3EST]);

                if (!empty($nonZeroValues)) {
                    $PlasmaAncak[$key][$key1]['skor_bh'] = $skor_bh = skor_buah_Ma($brdPerjjgEst);
                    $PlasmaAncak[$key][$key1]['skor_brd'] = $skor_brd = skor_brd_ma($sumPerBHEst);
                    $PlasmaAncak[$key][$key1]['skor_ps'] = $skor_ps = skor_palepah_ma($perPlEst);
                } else {
                    $PlasmaAncak[$key][$key1]['skor_bh'] = $skor_bh = 0;
                    $PlasmaAncak[$key][$key1]['skor_brd'] = $skor_brd = 0;
                    $PlasmaAncak[$key][$key1]['skor_ps'] = $skor_ps = 0;
                }

                $totalSkorEst = $skor_bh + $skor_brd + $skor_ps;

                // $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
                //PENAMPILAN UNTUK PERESTATE
                $PlasmaAncak[$key][$key1]['pokok_sample'] = $pokok_panenEst;
                $PlasmaAncak[$key][$key1]['ha_sample'] =  $jum_haEst;
                $PlasmaAncak[$key][$key1]['jumlah_panen'] = $janjang_panenEst;
                $PlasmaAncak[$key][$key1]['akp_rl'] =  $akpEst;

                $PlasmaAncak[$key][$key1]['p'] = $p_panenEst;
                $PlasmaAncak[$key][$key1]['k'] = $k_panenEst;
                $PlasmaAncak[$key][$key1]['tgl'] = $brtgl_panenEst;

                // $PlasmaAncak[$key][$key1]['total_brd'] = $skor_bTinggal;
                $PlasmaAncak[$key][$key1]['brd/jjgest'] = $brdPerjjgEst;
                $PlasmaAncak[$key][$key1]['buah/jjg'] = $sumPerBHEst;

                // data untuk buah tinggal
                $PlasmaAncak[$key][$key1]['bhts_s'] = $bhtsEST;
                $PlasmaAncak[$key][$key1]['bhtm1'] = $bhtm1EST;
                $PlasmaAncak[$key][$key1]['bhtm2'] = $bhtm2EST;
                $PlasmaAncak[$key][$key1]['bhtm3'] = $bhtm3EST;
                $PlasmaAncak[$key][$key1]['palepah_pokok'] = $pelepah_sEST;
                $PlasmaAncak[$key][$key1]['palepah_per'] = $perPlEst;
                // total skor akhir
                // $PlasmaAncak[$key][$key1]['skor_bh'] =  skor_brd_ma($brdPerjjgEst);
                // $PlasmaAncak[$key][$key1]['skor_brd'] = skor_buah_Ma($sumPerBHEst);
                // $PlasmaAncak[$key][$key1]['skor_ps'] = skor_palepah_ma($perPlEst);
                $PlasmaAncak[$key][$key1]['SkorbulanWil'] = $totalSkorEst;

                $pokok_panenWil += $pokok_panenEst;

                $jum_haWil += $jum_haEst;
                $janjang_panenWil += $janjang_panenEst;

                $p_panenWil += $p_panenEst;
                $k_panenWil += $k_panenEst;
                $brtgl_panenWil += $brtgl_panenEst;

                // bagian buah tinggal
                $bhtsWil  += $bhtsEST;
                $bhtm1Wil += $bhtm1EST;
                $bhtm2Wil  += $bhtm2EST;
                $bhtm3Wil  += $bhtm3EST;
                // data untuk pelepah sengklek
                $pelepah_sWil += $pelepah_sEST;
            } else {
                $PlasmaAncak[$key][$key1]['pokok_sample'] = 0;
                $PlasmaAncak[$key][$key1]['ha_sample'] =  0;
                $PlasmaAncak[$key][$key1]['jumlah_panen'] = 0;
                $PlasmaAncak[$key][$key1]['akp_rl'] =  0;

                $PlasmaAncak[$key][$key1]['p'] = 0;
                $PlasmaAncak[$key][$key1]['k'] = 0;
                $PlasmaAncak[$key][$key1]['tgl'] = 0;

                // $PlasmaAncak[$key][$key1]['total_brd'] = $skor_bTinggal;
                $PlasmaAncak[$key][$key1]['brd/jjgest'] = 0;
                $PlasmaAncak[$key][$key1]['buah/jjg'] = 0;
                // data untuk buah tinggal
                $PlasmaAncak[$key][$key1]['bhts_s'] = 0;
                $PlasmaAncak[$key][$key1]['bhtm1'] = 0;
                $PlasmaAncak[$key][$key1]['bhtm2'] = 0;
                $PlasmaAncak[$key][$key1]['bhtm3'] = 0;
                $PlasmaAncak[$key][$key1]['palepah_pokok'] = 0;
                // total skor akhir
                $PlasmaAncak[$key][$key1]['skor_bh'] =  0;
                $PlasmaAncak[$key][$key1]['skor_brd'] = 0;
                $PlasmaAncak[$key][$key1]['skor_ps'] = 0;
                $PlasmaAncak[$key][$key1]['SkorbulanWil'] = 0;
            }
            $sumBHWil = $bhtsWil +  $bhtm1Wil +  $bhtm2Wil +  $bhtm3Wil;
            $totalPKT = $p_panenWil + $k_panenWil + $brtgl_panenWil;
            // dd($sumBHWil);
            if ($pokok_panenWil != 0) {
                $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 2);
            } else {
                $akpWil = 0;
            }

            if ($pokok_panenWil != 0) {
                $brdPerjjgWil = round($totalPKT / $janjang_panenWil, 1);
            } else {
                $brdPerjjgWil = 0;
            }



            // dd($sumBHWil);
            if ($sumBHWil != 0) {
                $sumPerBHWil = round($sumBHWil / ($janjang_panenWil + $sumBHWil) * 100, 1);
            } else {
                $sumPerBHWil = 0;
            }

            if ($pokok_panenWil != 0) {
                $perPlWil = round(($pelepah_sEST / $pokok_panenWil) * 100, 2);
            } else {
                $perPlWil = 0;
            }

            $nonZeroValues = array_filter([
                $bhtsWil,
                $bhtm1Wil,
                $bhtm2Wil,
                $bhtm3Wil,
                $p_panenWil,
                $k_panenWil,
                $brtgl_panenWil,
            ]);

            if (!empty($nonZeroValues)) {
                $totalSkorWil =  skor_brd_ma($brdPerjjgWil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPlWil);
            } else {
                $totalSkorWil =  0;
            }


            //PENAMPILAN UNTUK PERESTATE
            $PlasmaAncak[$key]['pokok_sample'] = $pokok_panenWil;
            $PlasmaAncak[$key]['ha_sample'] =  $jum_haWil;
            $PlasmaAncak[$key]['jumlah_panen'] = $janjang_panenWil;
            $PlasmaAncak[$key]['akp_rl'] =  $akpWil;

            $PlasmaAncak[$key]['p'] = $p_panenWil;
            $PlasmaAncak[$key]['k'] = $k_panenWil;
            $PlasmaAncak[$key]['tgl'] = $brtgl_panenWil;

            // $PlasmaAncak[$key]['total_brd'] = $skor_bTinggal;
            $PlasmaAncak[$key]['brd/jjgest'] = $brdPerjjgWil;
            $PlasmaAncak[$key]['buah/jjg'] = $sumPerBHWil;

            // data untuk buah tinggal
            $PlasmaAncak[$key]['bhts_s'] = $bhtsEST;
            $PlasmaAncak[$key]['bhtm1'] = $bhtm1EST;
            $PlasmaAncak[$key]['bhtm2'] = $bhtm2EST;
            $PlasmaAncak[$key]['bhtm3'] = $bhtm3EST;
            $PlasmaAncak[$key]['palepah_pokok'] = $pelepah_sEST;
            $PlasmaAncak[$key]['palepah_per'] = $perPlWil;
            // total skor akhir
            $PlasmaAncak[$key]['skor_bh'] =  skor_brd_ma($brdPerjjgWil);
            $PlasmaAncak[$key]['skor_brd'] = skor_buah_Ma($sumPerBHWil);
            $PlasmaAncak[$key]['skor_ps'] = skor_palepah_ma($perPlWil);
            $PlasmaAncak[$key]['PlasmaSkorTahun'] = $totalSkorWil;
        }

        // dd($PlasmaAncak);
        //mutuancak end

        ///mutu transport
        $PlasmaMtTrans = array();
        foreach ($queryMTtrans as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (isset($PlasmaMtTrans[$key][$month][$key2])) {
                        // If the month already exists in the array, append the new value to the existing array
                        $PlasmaMtTrans[$key][$month][$key2][] = $value3;
                    } else {
                        // If the month does not exist in the array, create a new array with the new value
                        $PlasmaMtTrans[$key][$month][$key2] = array($value3);
                    }
                }
            }
        }

        // dd($PlasmaMtTrans);
        // dd($PlasmaMtAncak);
        $PlasmaDefaultTrans = array();
        foreach ($bulan as $month) {
            foreach ($queryEstePla as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $PlasmaDefaultTrans[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }


        // dd($PlasmaDefaultTrans);
        // dd($PlasmaDefaultAncak);
        foreach ($PlasmaDefaultTrans as $key => $estValue) {
            // dd($key);
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($PlasmaMtTrans as $dataKey => $dataValue) {
                    // dd($dataValue);
                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            // dd($dataEstKey, $monthKey);
                            if ($dataEstKey == $monthKey) {
                                $PlasmaDefaultTrans[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        // dd($PlasmaDefaultTrans);
        $PlasmaTrans = array();
        $testingCoba = array();
        foreach ($PlasmaDefaultTrans as $key => $value) if (!empty($value)) {
            $dataBLokWil = 0;
            $sum_btWil = 0;
            $sum_rstWil = 0;
            $plasmaWIl = 0;
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $dataBLokEst = 0;
                $sum_btEst = 0;
                $sum_rstEst = 0;
                $TOTAL = 0;
                // $brd = 0;
                // $buah = 0;
                foreach ($value1 as $key2 => $value2) if (!empty($value2)) {
                    $sum_bt = 0;
                    $sum_rst = 0;
                    $brdPertph = 0;
                    $buahPerTPH = 0;
                    $totalSkor = 0;

                    $listBlokPerAfd = array();
                    $listBlokTransport = array();

                    $inc = 0;
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                        $dataBLok = 0;
                        if ($RegData == '2') {
                            if ($value3['blok'] != '0') {
                                $countTransport = 0;
                                $getDate = Carbon::createFromFormat('Y-m-d H:i:s', $value3['datetime']);
                                $day = $getDate->format('d');
                                $month = $getDate->format('m');
                                $year = $getDate->format('Y');

                                $ancak = DB::connection('mysql2')->table('mutu_ancak_new')
                                    ->select(
                                        "mutu_ancak_new.*"
                                    )
                                    ->where('estate', $key)
                                    ->where('afdeling', $key2)
                                    ->where('blok', $value3['blok'])
                                    ->whereDay('datetime', $day)
                                    ->whereMonth('datetime', $month)
                                    ->whereYear('datetime', $year)
                                    ->orderBy('datetime', 'DESC')
                                    ->first();

                                if ($ancak) {
                                    $status_panen = $ancak->status_panen;
                                    if (strlen($ancak->status_panen) == 3) {
                                        $arrStatus = explode(',', $ancak->status_panen);
                                        $status_panen = $arrStatus[0];
                                    }

                                    if ($status_panen <= 3) {
                                        $countTransport = round($ancak->luas_blok * 1.3, 2);
                                    } else {
                                        $countTransport = DB::connection('mysql2')->table('mutu_ancak_new')->where('estate', $ancak->estate)->where('afdeling', $ancak->afdeling)->where('blok', $ancak->blok)->whereDay('datetime', $day)->whereMonth('datetime', $month)->whereYear('datetime', $year)->count();
                                    }
                                    $listBlokTransport[] = $ancak->blok;
                                    $testingCoba[$key][$key1][$key2][$ancak->blok] = $countTransport;
                                } else {
                                    $countTransport = DB::connection('mysql2')->table('mutu_transport')->where('estate', $value3['estate'])->whereDay('datetime', $day)->whereMonth('datetime', $month)->whereYear('datetime', $year)->where('afdeling', $value3['afdeling'])->where('blok', $value3['blok'])->count();
                                    $listBlokTransport[] = $value3['blok'];

                                    $testingCoba[$key][$key1][$key2][$value3['blok']] = $countTransport;
                                }

                                $dataBLok += $countTransport;

                                // $ancakTanpaMT = DB::connection('mysql2')->table('mutu_ancak_new')
                                // ->select(
                                //     "mutu_ancak_new.*",
                                //     "mutu_ancak_new.blok as nama_blok"
                                // )
                                // ->where('estate', $key)
                                // ->where('afdeling', $key2)
                                // ->whereDay('datetime',$day)
                                // ->whereMonth('datetime', $month)
                                // ->whereYear('datetime', $year)
                                // ->whereNotIn('blok',$listBlokTransport)
                                // ->groupBy('nama_blok')
                                // ->orderBy('datetime', 'DESC')
                                // ->get();

                                //     foreach ($ancakTanpaMT as $key5 => $value5) {
                                //         $status_panen = $value5->status_panen;
                                //         if( strlen($value5->status_panen) == 3){
                                //             $arrStatus = explode(',', $value5->status_panen);
                                //             $status_panen = $arrStatus[0];
                                //         }

                                //         if((int)$status_panen <= 3){
                                //            $dataBLok += round($value5->luas_blok * 1.3,2);
                                //            $testingCoba[$key][$key1][$key2][$value5->blok] = round($value5->luas_blok *1.3 , 2);
                                //         }else{
                                //            $dataBLok += DB::connection('mysql2')->table('mutu_ancak_new')->where('estate',$key)->where('afdeling',$key2)->whereDay('datetime',$day)->whereMonth('datetime',$month)->whereYear('datetime',$year)->where('blok', $value5->blok)->count();
                                //         $testingCoba[$key][$key1][$key2][$value5->blok] = 2;
                                //         }
                                //     }

                                // $dataBLok += $countTransport;
                                $inc++;
                            }
                        } else {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                            $dataBLok = count($listBlokPerAfd);
                        }

                        // dd($dataBLok);
                        $sum_bt += $value3['bt'];
                        $sum_rst += $value3['rst'];
                    }
                    $brdPertph = ($dataBLok !== 0) ? round($sum_bt / $dataBLok, 2) : 0;
                    $buahPerTPH = ($dataBLok !== 0) ? round($sum_rst / $dataBLok, 2) : 0;

                    $nonZeroValues = array_filter([$sum_bt, $sum_rst]);

                    if (!empty($nonZeroValues)) {
                        $PlasmaTrans[$key][$key1][$key2]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertph);
                        $PlasmaTrans[$key][$key1][$key2]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPH);
                    } else {
                        $PlasmaTrans[$key][$key1][$key2]['skor_brdPertph'] = $skor_brd = 0;
                        $PlasmaTrans[$key][$key1][$key2]['skor_buahPerTPH'] = $skor_buah = 0;
                    }

                    $totalSkor = $skor_brd + $skor_buah;
                    // $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                    $PlasmaTrans[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                    $PlasmaTrans[$key][$key1][$key2]['total_brd'] = $sum_bt;
                    $PlasmaTrans[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                    $PlasmaTrans[$key][$key1][$key2]['total_buah'] = $sum_rst;
                    $PlasmaTrans[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                    // $PlasmaTrans[$key][$key1][$key2]['skor_brdPertph'] = skor_brd_tinggal($brdPertph);
                    // $PlasmaTrans[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                    $PlasmaTrans[$key][$key1][$key2]['skorBulanPlasma'] = $totalSkor;

                    $dataBLokEst += $dataBLok;
                    $sum_btEst += $sum_bt;
                    $sum_rstEst += $sum_rst;
                } else {
                    $PlasmaTrans[$key][$key1][$key2]['tph_sample'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['total_brd'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['total_brd/TPH'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['total_buah'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['skor_brdPertph'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                    $PlasmaTrans[$key][$key1][$key2]['skorBulanPlasma'] = 0;
                }
                //PERHITUNGAN PERESTATE


                $brdPertphEst = ($dataBLokEst !== 0) ? round($sum_btEst / $dataBLokEst, 2) : 0;
                $buahPerTPHEst = ($dataBLokEst !== 0) ? round($sum_rstEst / $dataBLokEst, 2) : 0;
                $nonZeroValues = array_filter([$sum_btEst, $sum_rstEst]);

                if (!empty($nonZeroValues)) {
                    $PlasmaTrans[$key][$key1]['skor_brdPertph'] = $skor_brd =  skor_brd_tinggal($brdPertphEst);
                    $PlasmaTrans[$key][$key1]['skor_buahPerTPH'] = $skor_buah =  skor_buah_tinggal($buahPerTPHEst);
                } else {
                    $PlasmaTrans[$key][$key1]['skor_brdPertph'] = $skor_brd = 0;
                    $PlasmaTrans[$key][$key1]['skor_buahPerTPH'] = $skor_buah = 0;
                }

                $TOTAL = $skor_brd + $skor_buah;
                // $TOTAL = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
                // $brd = skor_brd_tinggal($brdPertphEst);
                // $buah = skor_buah_tinggal($buahPerTPHEst);
                // $totalSkorEst = 
                $PlasmaTrans[$key][$key1]['tph_sample'] = $dataBLokEst;
                $PlasmaTrans[$key][$key1]['total_brd'] = $sum_btEst;
                $PlasmaTrans[$key][$key1]['total_brd/TPH'] = $brdPertphEst;
                $PlasmaTrans[$key][$key1]['total_buah'] = $sum_rstEst;
                $PlasmaTrans[$key][$key1]['total_buahPerTPH'] = $buahPerTPHEst;
                // $PlasmaTrans[$key][$key1]['skor_brdPertph'] = skor_brd_tinggal($brdPertphEst);
                // $PlasmaTrans[$key][$key1]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHEst);
                $PlasmaTrans[$key][$key1]['SkorbulanWil'] = $TOTAL;


                $dataBLokWil += $dataBLokEst;
                $sum_btWil += $sum_btEst;
                $sum_rstWil += $sum_rstEst;
            } else {
                $PlasmaTrans[$key][$key1]['tph_sample'] = 0;
                $PlasmaTrans[$key][$key1]['total_brd'] = 0;
                $PlasmaTrans[$key][$key1]['total_brd/TPH'] = 0;
                $PlasmaTrans[$key][$key1]['total_buah'] = 0;
                $PlasmaTrans[$key][$key1]['total_buahPerTPH'] = 0;
                $PlasmaTrans[$key][$key1]['skor_brdPertph'] = 0;
                $PlasmaTrans[$key][$key1]['skor_buahPerTPH'] = 0;
                $PlasmaTrans[$key][$key1]['SkorbulanWil'] = 0;
            }
            if ($dataBLokWil != 0) {
                $brdPertphWil = round($sum_btWil / $dataBLokWil, 2);
            } else {
                $brdPertphWil = 0;
            }
            if ($dataBLokWil != 0) {
                $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 2);
            } else {
                $buahPerTPHWil = 0;
            }

            $nonZeroValues = array_filter([$dataBLokWil, $sum_btWil, $sum_rstWil]);

            if (!empty($nonZeroValues)) {
                $plasmaWIl = skor_brd_tinggal($brdPertphWil) + skor_buah_tinggal($buahPerTPHWil);
            } else {
                $plasmaWIl = 0;
            }




            $PlasmaTrans[$key]['tph_sample'] = $dataBLokWil;
            $PlasmaTrans[$key]['total_brd'] = $sum_btWil;
            $PlasmaTrans[$key]['total_brd/TPH'] = $brdPertphWil;
            $PlasmaTrans[$key]['total_buah'] = $sum_rstWil;
            $PlasmaTrans[$key]['total_buahPerTPH'] = $buahPerTPHWil;
            $PlasmaTrans[$key]['skor_brdPertph'] = skor_brd_tinggal($brdPertphWil);
            $PlasmaTrans[$key]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);

            $PlasmaTrans[$key]['PlasmaSkorTahun'] = $plasmaWIl;
        } else {
            $PlasmaTrans[$key]['tph_sample'] = 0;
            $PlasmaTrans[$key]['total_brd'] = 0;
            $PlasmaTrans[$key]['total_brd/TPH'] = 0;
            $PlasmaTrans[$key]['total_buah'] = 0;
            $PlasmaTrans[$key]['total_buahPerTPH'] = 0;
            $PlasmaTrans[$key]['skor_brdPertph'] = 0;
            $PlasmaTrans[$key]['skor_buahPerTPH'] = 0;
            $PlasmaTrans[$key]['PlasmaSkorTahun'] = 0;
        }

        // dd($testingCoba);
        // dd($PlasmaTrans['Plasma1']['April'], $PlasmaAncak['Plasma1']['April'], $PlasmaBuah['Plasma1']['April']);

        $RekapBulanPlasma = array();

        foreach ($PlasmaTrans as $tranKey => $trans) if (is_array($trans)) {
            $NamaGM = '';
            $afd = 'GM';
            foreach ($queryAsisten as $ast => $asistan) if (is_array($asistan) && $asistan['est'] == $tranKey  && $asistan['afd'] == $afd) {
                $NamaGM = $asistan['nama'];
            }
            foreach ($trans as $tranKey1 => $trans1) if (is_array($trans1)) {
                $NamaEM = '';
                $afd = 'EM';
                foreach ($queryAsisten as $ast => $asistan) if (is_array($asistan) && $asistan['est'] == $tranKey  && $asistan['afd'] == $afd) {
                    $NamaEM = $asistan['nama'];
                }
                foreach ($trans1 as $tranKey2 => $trans2) if (is_array($trans2)) {
                    $NamaWil = '';
                    foreach ($queryAsisten as $ast => $asistan) if (is_array($asistan) && $asistan['est'] == $tranKey && $asistan['afd'] == $tranKey2) {

                        // dd($ast);
                        // dd($asistan);
                        $NamaWil = $asistan['nama'];
                    }

                    foreach ($PlasmaAncak as $ancakKey => $ancak) if (is_array($ancak)) {
                        foreach ($ancak as $ancakKey1 => $ancak1) if (is_array($ancak1)) {
                            foreach ($ancak1 as $ancakKey2 => $ancak2) if (is_array($ancak2)) {
                                foreach ($PlasmaBuah as $buahKey => $buah) if (is_array($buah) && $tranKey == $ancakKey && $ancakKey == $buahKey) {
                                    $tahun = 0;
                                    foreach ($buah as $buahKey1 => $buah1) if (is_array($buah1) && $tranKey1 == $ancakKey1 && $ancakKey1 == $buahKey1) {
                                        $wilSkor = 0;
                                        foreach ($buah1 as $buahKey2 => $buah2) if (is_array($buah2) && $tranKey2 == $ancakKey2 && $ancakKey2 == $buahKey2) {
                                            $RekapBulanPlasma[$tranKey][$tranKey1][$tranKey2]['bulan'] = $trans2['skorBulanPlasma'] + $ancak2['skorBulanPlasma'] + $buah2['skorBulanPlasma'];
                                        }
                                        $wilSkor = $trans1['SkorbulanWil'] + $ancak1['SkorbulanWil'] + $buah1['SkorbulanWil'];
                                    }


                                    $tahun = $trans['PlasmaSkorTahun'] + $ancak['PlasmaSkorTahun'] + $buah['PlasmaSkorTahun'];
                                }
                            }
                        }
                    }
                    $RekapBulanPlasma[$tranKey][$tranKey1][$tranKey2]['namaEM'] = $NamaWil;
                }
                $RekapBulanPlasma[$tranKey][$tranKey1]['namaEM'] = $NamaEM;
                $RekapBulanPlasma[$tranKey][$tranKey1]['Bulan'] = $wilSkor;
            }
            $RekapBulanPlasma[$tranKey]['namaGM'] = $NamaGM;
            $RekapBulanPlasma[$tranKey]['Tahun'] = $tahun;
        }
        //end plasma
        // dd($PlasmaBuah);

        $estateEST = DB::connection('mysql2')->table('estate')
            ->select('estate.est', 'estate.nama')
            ->whereNotIn('estate.est', ['CWS1', 'CWS2', 'CWS3'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $RegData)
            // ->where('wil.regional', '3')
            ->get();
        $estateEST = json_decode($estateEST, true);

        // dd($RekapBulanAFD);

        //untuk perhitungan ptmua
        $ptmuaAncak = DB::connection('mysql2')->table('mutu_ancak_new')
            ->select(
                "mutu_ancak_new.*",
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun')
            )
            // ->whereBetween('mutu_ancak_new.datetime', ['2023-04-06', '2023-04-12'])
            // ->where('datetime', 'like', '%' . $date . '%')
            ->whereYear('datetime', $year)
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
            // ->where('datetime', 'like', '%' . $date . '%')
            ->whereYear('datetime', $year)
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
            // ->where('datetime', 'like', '%' . $date . '%')
            ->whereYear('datetime', $year)
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
                    $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                } else {
                    $perPl = 0;
                }



                $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                $mtAncakMua[$key][$key1]['pokok_sample'] = $totalPokok;
                $mtAncakMua[$key][$key1]['ha_sample'] = $jum_ha;
                $mtAncakMua[$key][$key1]['jumlah_panen'] = $totalPanen;
                $mtAncakMua[$key][$key1]['akp_rl'] = $akp;

                $mtAncakMua[$key][$key1]['p'] = $totalP_panen;
                $mtAncakMua[$key][$key1]['k'] = $totalK_panen;
                $mtAncakMua[$key][$key1]['tgl'] = $totalPTgl_panen;

                // $mtAncakMua[$key][$key1]['total_brd'] = $skor_bTinggal;
                $mtAncakMua[$key][$key1]['brd/jjg'] = $brdPerjjg;

                // data untuk buah tinggal
                $mtAncakMua[$key][$key1]['bhts_s'] = $totalbhts_panen;
                $mtAncakMua[$key][$key1]['bhtm1'] = $totalbhtm1_panen;
                $mtAncakMua[$key][$key1]['bhtm2'] = $totalbhtm2_panen;
                $mtAncakMua[$key][$key1]['bhtm3'] = $totalbhtm3_oanen;
                $mtAncakMua[$key][$key1]['buah/jjg'] = $sumPerBH;

                // $mtAncakMua[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 2);
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

                // $mtAncakMua[$key][$key1]['jjgperBuah'] = number_format($sumPerBH, 2);
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
                $akpEst = round(($janjang_panenEst / $pokok_panenEst) * 100, 2);
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
                $perPlEst = round(($pelepah_sEST / $pokok_panenEst) * 100, 2);
            } else {
                $perPlEst = 0;
            }

            $totalSkorEst =  skor_brd_ma($brdPerjjgEst) + skor_buah_Ma($sumPerBHEst) + skor_palepah_ma($perPlEst);
            //PENAMPILAN UNTUK PERESTATE
            $mtAncakMua[$key]['pokok_sample'] = $pokok_panenEst;
            $mtAncakMua[$key]['ha_sample'] =  $jum_haEst;
            $mtAncakMua[$key]['jumlah_panen'] = $janjang_panenEst;
            $mtAncakMua[$key]['akp_rl'] =  $akpEst;

            $mtAncakMua[$key]['p'] = $p_panenEst;
            $mtAncakMua[$key]['k'] = $k_panenEst;
            $mtAncakMua[$key]['tgl'] = $brtgl_panenEst;

            // $mtAncakMua[$key]['total_brd'] = $skor_bTinggal;
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

        if ($janjang_panenWil != 0) {
            $akpWil = round(($janjang_panenWil / $pokok_panenWil) * 100, 2);
        } else {
            $akpWil = 0;
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


        $totalWil = skor_brd_ma($brdPerwil) + skor_buah_Ma($sumPerBHWil) + skor_palepah_ma($perPiWil);

        $mtAncakMua['pokok_sample'] = $pokok_panenWil;
        $mtAncakMua['ha_sample'] =  $jum_haWil;
        $mtAncakMua['jumlah_panen'] = $janjang_panenWil;
        $mtAncakMua['akp_rl'] =  $akpWil;

        $mtAncakMua['p'] = $p_panenWil;
        $mtAncakMua['k'] = $k_panenWil;
        $mtAncakMua['tgl'] = $brtgl_panenWil;

        // $mtAncakMua['total_brd'] = $skor_bTinggal;
        $mtAncakMua['brd/jjgwil'] = $brdPerwil;
        $mtAncakMua['buah/jjgwil'] = $sumPerBHWil;
        $mtAncakMua['bhts_s'] = $bhts_panenWil;
        $mtAncakMua['bhtm1'] = $bhtm1_panenWil;
        $mtAncakMua['bhtm2'] = $bhtm2_panenWil;
        $mtAncakMua['bhtm3'] = $bhtm3_oanenWil;
        // $mtAncakMua['jjgperBuah'] = number_format($sumPerBH, 2);
        // data untuk pelepah sengklek
        $mtAncakMua['palepah_pokok'] = $pelepah_swil;

        $mtAncakMua['palepah_per'] = $perPiWil;
        // total skor akhir
        $mtAncakMua['skor_bh'] = skor_brd_ma($brdPerwil);
        $mtAncakMua['skor_brd'] = skor_buah_Ma($sumPerBHWil);
        $mtAncakMua['skor_ps'] = skor_palepah_ma($perPiWil);
        $mtAncakMua['skor_akhir'] = $totalWil;
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
                    $total_kr = round($sum_kr / $dataBLok, 2);
                } else {
                    $total_kr = 0;
                }


                $per_kr = round($total_kr * 100, 2);
                if ($jml_mth != 0) {
                    $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMth = 0;
                }
                if ($jml_mtg != 0) {
                    $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }
                if ($sum_over != 0) {
                    $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $PerOver = 0;
                }
                if ($sum_kosongjjg != 0) {
                    $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }
                if ($sum_vcut != 0) {
                    $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerVcut = 0;
                }

                if ($sum_abnor != 0) {
                    $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);
                } else {
                    $PerAbr = 0;
                }


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
                $total_krEst = round($sum_krEst / $jum_haEst, 2);
            } else {
                $total_krEst = 0;
            }
            // if ($sum_kr != 0) {
            //     $total_kr = round($sum_kr / $dataBLok, 2);
            // } else {
            //     $total_kr = 0;
            // }

            if ($sum_bmtEst != 0) {
                $PerMthEst = round(($sum_bmtEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
            } else {
                $PerMthEst = 0;
            }

            if ($sum_bmkEst != 0) {
                $PerMskEst = round(($sum_bmkEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
            } else {
                $PerMskEst = 0;
            }

            if ($sum_overEst != 0) {
                $PerOverEst = round(($sum_overEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
            } else {
                $PerOverEst = 0;
            }
            if ($sum_kosongjjgEst != 0) {
                $PerkosongjjgEst = round(($sum_kosongjjgEst / ($sum_SamplejjgEst - $sum_abnorEst)) * 100, 2);
            } else {
                $PerkosongjjgEst = 0;
            }
            if ($sum_vcutEst != 0) {
                $PerVcutest = round(($sum_vcutEst / $sum_SamplejjgEst) * 100, 2);
            } else {
                $PerVcutest = 0;
            }
            if ($sum_abnorEst != 0) {
                $PerAbrest = round(($sum_abnorEst / $sum_SamplejjgEst) * 100, 2);
            } else {
                $PerAbrest = 0;
            }
            // $per_kr = round($sum_kr * 100);
            $per_krEst = round($total_krEst * 100, 2);

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
            $total_krWil = round($sum_krWil / $jum_haWil, 2);
        } else {
            $total_krWil = 0;
        }

        if ($sum_bmtWil != 0) {
            $PerMthWil = round(($sum_bmtWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
        } else {
            $PerMthWil = 0;
        }


        if ($sum_bmkWil != 0) {
            $PerMskWil = round(($sum_bmkWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
        } else {
            $PerMskWil = 0;
        }
        if ($sum_overWil != 0) {
            $PerOverWil = round(($sum_overWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
        } else {
            $PerOverWil = 0;
        }
        if ($sum_kosongjjgWil != 0) {
            $PerkosongjjgWil = round(($sum_kosongjjgWil / ($sum_SamplejjgWil - $sum_abnorWil)) * 100, 2);
        } else {
            $PerkosongjjgWil = 0;
        }
        if ($sum_vcutWil != 0) {
            $PerVcutWil = round(($sum_vcutWil / $sum_SamplejjgWil) * 100, 2);
        } else {
            $PerVcutWil = 0;
        }
        if ($sum_abnorWil != 0) {
            $PerAbrWil = round(($sum_abnorWil / $sum_SamplejjgWil) * 100, 2);
        } else {
            $PerAbrWil = 0;
        }
        $per_krWil = round($total_krWil * 100, 2);
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
                    $brdPertph = round($sum_bt / $dataBLok, 2);
                } else {
                    $brdPertph = 0;
                }
                if ($dataBLok != 0) {
                    $buahPerTPH = round($sum_rst / $dataBLok, 2);
                } else {
                    $buahPerTPH = 0;
                }



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
                    $brdPertphEst = round($sum_btEst / $dataBLokEst, 2);
                } else {
                    $brdPertphEst = 0;
                }
                if ($dataBLokEst != 0) {
                    $buahPerTPHEst = round($sum_rstEst / $dataBLokEst, 2);
                } else {
                    $buahPerTPHEst = 0;
                }

                $totalSkorEst = skor_brd_tinggal($brdPertphEst) + skor_buah_tinggal($buahPerTPHEst);
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
                $brdPertphWil = round($sum_btWil / $dataBLokWil, 2);
            } else {
                $brdPertphWil = 0;
            }
            if ($dataBLokWil != 0) {
                $buahPerTPHWil = round($sum_rstWil / $dataBLokWil, 2);
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
        $mtTransMua['tph_sample'] = $dataBLokWil;
        $mtTransMua['total_brd'] = $sum_btWil;
        $mtTransMua['total_brd/TPH'] = $brdPertphWil;
        $mtTransMua['total_buah'] = $sum_rstWil;
        $mtTransMua['total_buahPerTPH'] = $buahPerTPHWil;
        $mtTransMua['skor_brdPertph'] =   skor_brd_tinggal($brdPertphWil);
        $mtTransMua['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPHWil);
        $mtTransMua['totalSkor'] = $totalSkorWil;
        // dd($mtBuahMua, $mtAncakMua, $mtTransMua);
        $mtBuahMuaTotalSkor = $mtBuahMua['TOTAL_SKOR'];
        $mtAncakMuaSkorAkhir = $mtAncakMua['skor_akhir'];
        $mtTransMuaTotalSkor = $mtTransMua['totalSkor'];


        $ptMuachartBuah = $mtAncakMua['buah/jjgwil'];
        $ptMuachartBRD = $mtAncakMua['brd/jjgwil'];
        $arrChartbhMua = [
            'pt_muabuah' => $ptMuachartBuah
        ];
        $arrChartbhBRD = [
            'pt_muabrd' => $ptMuachartBRD
        ];

        // dd($chartBTTth);
        $keysToRemove = ["SRE", "LDE", "SKE"];
        $filteredBTT = [];

        foreach ($chartBTTth as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $filteredBTT[$key] = $value;
            }
        }
        $filteredBRD = [];

        foreach ($chartBHth as $key => $value) {
            if (!in_array($key, $keysToRemove)) {
                $filteredBRD[$key] = $value;
            }
        }
        // dd($arrChartbhMua, $filteredBuah);
        $filteredBTT["pt_muabuah"] = $arrChartbhMua["pt_muabuah"];
        $filteredBRD["pt_muabrd"] = $arrChartbhBRD["pt_muabrd"];

        // Check if $Reg is not equal to 1 or '1'
        if ($RegData != 1 && $RegData != '1') {
            unset($filteredBTT['pt_muabuah']);
            unset($filteredBRD['pt_muabrd']);
        }
        // dd($filteredBTT);

        $arrView = array();

        $arrView['RekapBulan'] =  $RekapBulan;
        $arrView['RekapTahun'] =  $RekapTahun;

        $arrView['asisten'] =  $queryAsisten;


        $arrView['chart_brdTAHUN'] = $filteredBTT;
        $arrView['chart_buahTAHUN'] = $filteredBRD;

        $arrView['chartbrdWilTH'] = $chartbrdWilTH;
        $arrView['chartBhwilTH'] = $chartBhwilTH;
        $arrView['list_estate'] = $queryEsta;
        $arrView['estateEST'] = $estateEST;

        // dd($RekapTahunwil);
        $arrView['FinalTahun'] = $FinalTahun;
        $arrView['Final_end'] = $Final_end;
        $arrView['RekapBulanwil'] = $RekapBulanwil;
        $arrView['RekapTahunwil'] = $RekapTahunwil;
        $arrView['RekapBulanReg'] = $RekapBulanReg;
        $arrView['RekapTahunReg'] = $RekapTahunReg;
        $arrView['RekapBulanAFD'] = $RekapBulanAFD;
        $arrView['RekapTahunAFD'] = $RekapTahunAFD;
        $arrView['RekapBulanPlasma'] = $RekapBulanPlasma;

        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }


    public function graphfilter(Request $request)
    {
        $estData = $request->input('est');
        $yearGraph = $request->input('yearGraph');
        $reg = $request->input('reg');
        $wilayahGrafik = $request->input('wilayahGrafik');
        // dd($estData, $yearGraph,$reg);

        // dd($wilayahGrafik);
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->where('estate.emp', '!=', 1)
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $reg)
            ->get();
        $queryEste = json_decode($queryEste, true);
        $querySidak = DB::connection('mysql2')->table('mutu_transport')
            ->select("mutu_transport.*")
            // ->where('datetime', 'like', '%' . $getDate . '%')
            // ->where('datetime', 'like', '%' . '2023-01' . '%')
            ->get();
        $DataEstate = $querySidak->groupBy(['estate', 'afdeling']);
        // dd($DataEstate);
        $DataEstate = json_decode($DataEstate, true);

        //menghitung buat table tampilkan pertahun

        //bagian querry
        //mutu ancak
        // $querytahun = DB::connection('mysql2')->table('mutu_ancak_new')
        //     ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
        //     ->whereYear('datetime', $yearGraph)
        //     // ->where('estate', 'KNE')
        //     ->orderBy('datetime', 'DESC')
        //     ->orderBy(DB::raw('SECOND(datetime)'), 'DESC')
        //     ->get();

        // $querytahun = $querytahun->groupBy(['estate', 'afdeling']);
        // $querytahun = json_decode($querytahun, true);


        $querytahun = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_ancak_new')
                ->select("mutu_ancak_new.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $reg)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($querytahun[$key1][$key2])) {
                        $querytahun[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $querytahun[$key1][$key2] = array_merge($querytahun[$key1][$key2], $value2);
                    }
                }
            }
        }

        $queryMTbuah = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_buah')
                ->select("mutu_buah.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_buah.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $reg)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($queryMTbuah[$key1][$key2])) {
                        $queryMTbuah[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $queryMTbuah[$key1][$key2] = array_merge($queryMTbuah[$key1][$key2], $value2);
                    }
                }
            }
        }


        $queryMTtrans = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('Y-m', mktime(0, 0, 0, $month, 1));

            $data = DB::connection('mysql2')->table('mutu_transport')
                ->select("mutu_transport.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun'))
                ->join('estate', 'estate.est', '=', 'mutu_transport.estate')
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('datetime', 'like', '%' . $monthName . '%')
                ->where('wil.regional', $reg)
                ->orderBy('estate', 'asc')
                ->orderBy('afdeling', 'asc')
                ->orderBy('blok', 'asc')
                ->orderBy('datetime', 'asc')
                ->get();

            $data = $data->groupBy(['estate', 'afdeling']);
            $data = json_decode($data, true);

            foreach ($data as $key1 => $value) {
                foreach ($value as $key2 => $value2) {
                    if (!isset($queryMTtrans[$key1][$key2])) {
                        $queryMTtrans[$key1][$key2] = [];
                    }

                    if (!empty($value2)) {
                        $queryMTtrans[$key1][$key2] = array_merge($queryMTtrans[$key1][$key2], $value2);
                    }
                }
            }
        }
        // dd($queryMTancak);

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
        // $queryEste = DB::connection('mysql2')->table('estate')->whereIn('wil', [1, 2, 3])->get();
        // $queryEste = json_decode($queryEste, true);

        // dd($queryEste);
        //end query
        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->whereNotIn('estate.est', ['CWS1', 'CWS2', 'CWS3'])
            ->where('estate.emp', '!=', 1)
            ->where('wil.regional', $reg)
            ->get();


        $queryEste = json_decode($queryEste, true);

        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        //mutu ancak membuat nilai berdasrakan bulan
        $dataPerBulan = array();
        foreach ($querytahun as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataPerBulan)) {
                        $dataPerBulan[$month] = array();
                    }
                    if (!array_key_exists($key, $dataPerBulan[$month])) {
                        $dataPerBulan[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataPerBulan[$month][$key])) {
                        $dataPerBulan[$month][$key][$key2] = array();
                    }
                    $dataPerBulan[$month][$key][$key2][$key3] = $value3;
                }
            }
        }
        //mutu buah  membuat nilai berdasrakan bulan
        $dataPerBulanMTbh = array();
        foreach ($queryMTbuah as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataPerBulanMTbh)) {
                        $dataPerBulanMTbh[$month] = array();
                    }
                    if (!array_key_exists($key, $dataPerBulanMTbh[$month])) {
                        $dataPerBulanMTbh[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataPerBulanMTbh[$month][$key])) {
                        $dataPerBulanMTbh[$month][$key][$key2] = array();
                    }
                    $dataPerBulanMTbh[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        // dd($dataPerBulanMTbh);
        //mutu transport memnuat nilai perbulan
        $dataBulananMTtrans = array();
        foreach ($queryMTtrans as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataBulananMTtrans)) {
                        $dataBulananMTtrans[$month] = array();
                    }
                    if (!array_key_exists($key, $dataBulananMTtrans[$month])) {
                        $dataBulananMTtrans[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataBulananMTtrans[$month][$key])) {
                        $dataBulananMTtrans[$month][$key][$key2] = array();
                    }
                    $dataBulananMTtrans[$month][$key][$key2][$key3] = $value3;
                }
            }
        }
        // dd($dataBulananMTtrans);

        //membuat nilai default 0 ke masing masing est-afdeling untuk di timpa nanti
        //membuat array estate -> bulan -> afdeling
        // mutu ancak
        $defaultNew = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultNew[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }


        // dd($defaultTabAFD);
        //mutu buah
        $defaultMTbh = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultMTbh[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }
        //mutu transport
        $defaultTrans = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultTrans[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }


        //membuat nilai default untuk table terakhir tahunan EST > AFD

        // dd($defaultMTbh);
        //end  nilai defalt
        //bagian menimpa nilai dengan menggunakan defaultNEw
        //menimpa nilai default dengan value mutu ancak yang ada isinya sehingga yang tidak ada value menjadi 0
        foreach ($defaultNew as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataPerBulan as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultNew[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }



        // dd($defaultTabAFD);
        // menimpa nilai defaultnew dengan value mutu buah yang ada isi nya
        // dd($defaultMTbh, $dataPerBulanMTbh);
        foreach ($defaultMTbh as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataPerBulanMTbh as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultMTbh[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }
        // dd($defaultMTbh);
        //menimpa nilai default mutu transport dengan yang memiliki value
        foreach ($defaultTrans as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataBulananMTtrans as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultTrans[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        // buat perhitungan regional 2... group berdasrakan blok
        // $newArrayANcak = [];
        // foreach ($defaultNew as $key1 => $value1) {
        //     $newArrayANcak[$key1] = [];
        //     foreach ($value1 as $key2 => $value2) {
        //         $newArrayANcak[$key1][$key2] = [];
        //         foreach ($value2 as $key3 => $value3) {
        //             if (is_array($value3)) {
        //                 foreach ($value3 as $item) {
        //                     $nestedKey = $item['blok'];
        //                     if (!isset($newArrayANcak[$key1][$key2][$key3][$nestedKey])) {
        //                         $newArrayANcak[$key1][$key2][$key3][$nestedKey] = [];
        //                     }
        //                     $newArrayANcak[$key1][$key2][$key3][$nestedKey][] = $item;
        //                 }
        //             } else {
        //                 $newArrayANcak[$key1][$key2][$key3] = $value3;
        //             }
        //         }
        //     }
        // }

        // $newArrayTrans = [];
        // foreach ($defaultTrans as $key1 => $value1) {
        //     $newArrayTrans[$key1] = [];
        //     foreach ($value1 as $key2 => $value2) {
        //         $newArrayTrans[$key1][$key2] = [];
        //         foreach ($value2 as $key3 => $value3) {
        //             if (is_array($value3)) {
        //                 foreach ($value3 as $item) {
        //                     $nestedKey = $item['blok'];
        //                     if (!isset($newArrayTrans[$key1][$key2][$key3][$nestedKey])) {
        //                         $newArrayTrans[$key1][$key2][$key3][$nestedKey] = [];
        //                     }
        //                     $newArrayTrans[$key1][$key2][$key3][$nestedKey][] = $item;
        //                 }
        //             } else {
        //                 $newArrayTrans[$key1][$key2][$key3] = $value3;
        //             }
        //         }
        //     }
        // }

        // $mutuTrans = array_replace_recursive($newArrayTrans, $newArrayANcak);

        $newArrayANcak = [];
        foreach ($defaultNew as $key1 => $value1) {
            $newArrayANcak[$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $newArrayANcak[$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $item) {
                            // Change the key "status_panen" to "status_panenMA"
                            $item['status_panenMA'] = $item['status_panen'];
                            unset($item['status_panen']);

                            $item['luas_blokMa'] = $item['luas_blok'];
                            unset($item['luas_blok']);

                            $nestedDate = date('Y-m-d', strtotime($item['datetime'])); // Format datetime as Y-m-d
                            $nestedBlok = $item['blok']; // Group by "blok"

                            if (!isset($newArrayANcak[$key1][$key2][$key3][$nestedDate])) {
                                $newArrayANcak[$key1][$key2][$key3][$nestedDate] = [];
                            }
                            if (!isset($newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok])) {
                                $newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok] = [];
                            }
                            $newArrayANcak[$key1][$key2][$key3][$nestedDate][$nestedBlok][] = $item;
                        }
                    } else {
                        $newArrayANcak[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }



        // dd($newArrayANcak['MRE']['June']);

        $newArrayTrans = [];
        foreach ($defaultTrans as $key1 => $value1) {
            $newArrayTrans[$key1] = [];
            foreach ($value1 as $key2 => $value2) {
                $newArrayTrans[$key1][$key2] = [];
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $item) {
                            // Change the key "status_panen" to "status_panenMA"
                            $item['status_panenTran'] = $item['status_panen'];
                            unset($item['status_panen']);

                            $item['luas_blokTrans'] = $item['luas_blok'];
                            unset($item['luas_blok']);

                            $nestedDate = date('Y-m-d', strtotime($item['datetime'])); // Format datetime as Y-m-d
                            $nestedBlok = $item['blok']; // Group by "blok"

                            if (!isset($newArrayTrans[$key1][$key2][$key3][$nestedDate])) {
                                $newArrayTrans[$key1][$key2][$key3][$nestedDate] = [];
                            }
                            if (!isset($newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok])) {
                                $newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok] = [];
                            }
                            $newArrayTrans[$key1][$key2][$key3][$nestedDate][$nestedBlok][] = $item;
                        }
                    } else {
                        $newArrayTrans[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }

        // dd($newArrayTrans['MRE']['June']['OC'], $newArrayANcak['MRE']['June']['OC']);
        $mutuTrans = array_replace_recursive($newArrayTrans, $newArrayANcak);


        if ($reg == 2) {
            $newTransv2 = array();
            foreach ($mutuTrans as $key => $value) {
                foreach ($value as $key1 => $value1) if (!empty($value)) {
                    $reg_blok = 0;
                    if (is_array($value1)) {
                        foreach ($value1 as $key2 => $value2) {
                            $wil_blok = 0;
                            if (is_array($value2)) {

                                foreach ($value2 as $key3 => $value3) {

                                    if (is_array($value3)) {

                                        $est_blok = 0; // Moved outside the innermost loop
                                        $largestLuasBlokMa = 0;
                                        foreach ($value3 as $key4 => $value4) {
                                            if (is_array($value4)) {
                                                $tot_blok = count($value4);
                                                foreach ($value4 as $key5 => $value5) {
                                                    $status_panen = $value5['status_panenMA'] ?? 'kosong';
                                                    $luas_blok = $value5['luas_blokMa'] ?? 0;

                                                    // if ($luas_blok > $largestLuasBlokMa) {
                                                    //     $largestLuasBlokMa = $luas_blok; // Update the largest luas_blokMa value
                                                    // }

                                                    if ($status_panen <= 3 && $status_panen != 'kosong') {
                                                        $new_blok = round($luas_blok * 1.3, 2);
                                                    } else {
                                                        $new_blok = $tot_blok;
                                                    }
                                                    $newTransv2[$key][$key1][$key2][$key3][$key4]['luas_blok'] = $luas_blok;
                                                    $newTransv2[$key][$key1][$key2][$key3][$key4]['status_panen'] = $status_panen;
                                                    $newTransv2[$key][$key1][$key2][$key3][$key4]['tph_sampleNew'] = $new_blok;
                                                }
                                                $est_blok += $new_blok;
                                            }
                                        }
                                        $newTransv2[$key][$key1][$key2][$key3]['tph_sampleEst'] = $est_blok;
                                        $wil_blok += $est_blok;
                                    }
                                }
                            }
                            $newTransv2[$key][$key1][$key2]['tph_sampleWil'] = $wil_blok;
                            $reg_blok += $wil_blok;
                        }
                    }
                    $newTransv2[$key][$key1]['tph_sampleReg'] = $reg_blok;
                } else {
                    $newTransv2[$key][$key1]['tph_sampleReg'] = 0;
                }
            }
        }

        // dd($newTransv2['MRE']['June']);


        // dd($newTransv2['MRE']['June'],$mutuTrans['MRE']['June']);
        // dd($newTransTPh['MRE']['June']['OC'],$mutuTrans['MRE']['June']['OC']);
        // $tph_est = array();

        // foreach ($newTransTPh as $key => $value) {
        //     foreach ($value as $key1 => $value2) {
        //             $sum_tphest = 0;
        //         foreach ($value2 as $key2 => $value3) {
        //         //    dd($value3);
        //                 $sum_tphest += $value3['tph_sampNEW'];
        //         }# code...
        //         $tph_est[$key][$key1]['tph_est'] = $sum_tphest;
        //     }# code...
        // }



        // dd($newTransTPh,$tph_est);

        // dd($newArrayTrans['MRE']['June'],$newArrayANcak['MRE']['June'],$newTransTPh['MRE']['June'],$mutuTrans['MRE']['June']);



        // endperhitungan

        // dd($defaultMTbh);
        $bulananBh = array();
        foreach ($defaultMTbh as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $tph_blok = 0;
                $jjgMth = 0;
                $sampleJJG = 0;
                $jjgAbn = 0;
                $PerMth = 0;
                $PerMsk = 0;
                $PerOver = 0;
                $Perkosongjjg = 0;
                $PerVcut = 0;
                $PerAbr = 0;
                $per_kr = 0;
                $jjgMsk = 0;
                $jjgOver = 0;
                $jjgKosng = 0;
                $vcut = 0;
                $jum_kr = 0;
                $total_kr = 0;
                $totalSkor = 0;
                $no_Vcut = 0;
                foreach ($value1 as $key2 => $value2)
                    if (is_array($value2)) {
                        $sum_bmt = 0;
                        $sum_bmk = 0;
                        $sum_over = 0;
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
                        $no_Vcut = 0;
                        $jml_mth = 0;
                        $jml_mtg = 0;
                        $combination_counts = array();
                        foreach ($value2 as $key3 => $value3) {
                            $combination = $value3['blok'] . ' ' . $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['tph_baris'];
                            if (!isset($combination_counts[$combination])) {
                                $combination_counts[$combination] = 0;
                            }
                            $combination_counts[$combination]++;
                            $sum_bmt += $value3['bmt'];
                            $sum_bmk += $value3['bmk'];
                            $sum_over += $value3['overripe'];
                            $sum_kosongjjg += $value3['empty_bunch'];
                            $sum_vcut += $value3['vcut'];
                            $sum_kr += $value3['alas_br'];


                            $sum_Samplejjg += $value3['jumlah_jjg'];
                            $sum_abnor += $value3['abnormal'];
                            // dd($sum_bmk);
                        }

                        $jml_mth = ($sum_bmt + $sum_bmk);
                        $jml_mtg = $sum_Samplejjg - ($jml_mth + $sum_over + $sum_kosongjjg + $sum_abnor);
                        // dd($sum_vcut);
                        $dataBLok = count($combination_counts);
                        if ($sum_kr != 0) {
                            $total_kr = round($sum_kr / $dataBLok, 2);
                        } else {
                            $total_kr = 0;
                        }

                        $per_kr = round($total_kr * 100, 2);
                        $denom1 = ($sum_Samplejjg - $sum_abnor) != 0 ? ($sum_Samplejjg - $sum_abnor) : 1;
                        $denom2 = $sum_Samplejjg != 0 ? $sum_Samplejjg : 1;

                        $PerMth = $denom1 != 0 ? round(($jml_mth / $denom1) * 100, 2) : 0;
                        $PerMsk = $denom1 != 0 ? round(($jml_mtg / $denom1) * 100, 2) : 0;
                        $PerOver = $denom1 != 0 ? round(($sum_over / $denom1) * 100, 2) : 0;
                        $Perkosongjjg = $denom1 != 0 ? round(($sum_kosongjjg / $denom1) * 100, 2) : 0;
                        $PerVcut = $denom2 != 0 ? round(($sum_vcut / $denom2) * 100, 2) : 0;
                        $PerAbr = $denom2 != 0 ? round(($sum_abnor / $denom2) * 100, 2) : 0;

                        // $PerMth = round(($jml_mth / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        // $PerMsk = round(($jml_mtg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        // $PerOver = round(($sum_over / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        // $Perkosongjjg = round(($sum_kosongjjg / ($sum_Samplejjg - $sum_abnor)) * 100, 2);
                        // $PerVcut = round(($sum_vcut / $sum_Samplejjg) * 100, 2);
                        // $PerAbr = round(($sum_abnor / $sum_Samplejjg) * 100, 2);

                        $totalSkor =  skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);

                        $bulananBh[$key][$key1][$key2]['tph_baris_blok'] = $dataBLok;
                        $bulananBh[$key][$key1][$key2]['sampleJJG_total'] = $sum_Samplejjg;
                        $bulananBh[$key][$key1][$key2]['total_mentah'] = $jml_mth;
                        $bulananBh[$key][$key1][$key2]['total_perMentah'] = $PerMth;
                        $bulananBh[$key][$key1][$key2]['total_masak'] = $jml_mtg;
                        $bulananBh[$key][$key1][$key2]['total_perMasak'] = $PerMsk;
                        $bulananBh[$key][$key1][$key2]['total_over'] = $sum_over;
                        $bulananBh[$key][$key1][$key2]['total_perOver'] = $PerOver;
                        $bulananBh[$key][$key1][$key2]['total_abnormal'] = $sum_abnor;
                        $bulananBh[$key][$key1][$key2]['total_jjgKosong'] = $sum_kosongjjg;
                        $bulananBh[$key][$key1][$key2]['total_perKosongjjg'] = $Perkosongjjg;
                        $bulananBh[$key][$key1][$key2]['total_vcut'] = $sum_vcut;

                        $bulananBh[$key][$key1][$key2]['jum_kr'] = $sum_kr;
                        $bulananBh[$key][$key1][$key2]['total_kr'] = $total_kr;
                        $bulananBh[$key][$key1][$key2]['persen_kr'] = $per_kr;

                        // skoring
                        $bulananBh[$key][$key1][$key2]['skor_mentah'] = skor_buah_mentah_mb($PerMth);
                        $bulananBh[$key][$key1][$key2]['skor_masak'] = skor_buah_masak_mb($PerMsk);
                        $bulananBh[$key][$key1][$key2]['skor_over'] = skor_buah_over_mb($PerOver);
                        $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = skor_jangkos_mb($Perkosongjjg);
                        $bulananBh[$key][$key1][$key2]['skor_vcut'] = skor_vcut_mb($PerVcut);
                        $bulananBh[$key][$key1][$key2]['skor_kr'] = skor_abr_mb($per_kr);
                        $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = $totalSkor;

                        //perhitungan estate
                        $tph_blok += $dataBLok;
                        $sampleJJG += $sum_Samplejjg;
                        $jjgMth += $jml_mth;

                        $jjgOver += $sum_over;
                        $jjgKosng += $sum_kosongjjg;
                        $vcut += $sum_vcut;
                        $jum_kr +=  $sum_kr;

                        $jjgAbn +=  $sum_abnor;

                        $jjgMsk +=  $jml_mtg;
                    } else {

                        $bulananBh[$key][$key1][$key2]['tph_baris_blok'] = 0;
                        $bulananBh[$key][$key1][$key2]['sampleJJG_total'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_mentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perMentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_masak'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perMasak'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_over'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perOver'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_abnormal'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_jjgKosong'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_perKosongjjg'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_vcut'] = 0;
                        $bulananBh[$key][$key1][$key2]['jum_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['total_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['persen_kr'] = 0;

                        // skoring
                        $bulananBh[$key][$key1][$key2]['skor_mentah'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_masak'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_over'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_jjgKosong'] = 0;
                        $bulananBh[$key][$key1][$key2]['skor_vcut'] = 0;

                        $bulananBh[$key][$key1][$key2]['skor_kr'] = 0;
                        $bulananBh[$key][$key1][$key2]['TOTAL_SKOR'] = 0;
                    }

                // dd($jjgMsk);
                if ($jum_kr != 0) {
                    $total_kr = round($jum_kr / $tph_blok, 2);
                } else {
                    $total_kr = 0;
                }

                if ($sampleJJG != 0) {
                    $PerMth = round(($jjgMth / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMth = 0;
                }
                if ($sampleJJG != 0) {
                    $PerMsk = round(($jjgMsk / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerMsk = 0;
                }

                if ($sampleJJG != 0 && $jjgAbn != 0) {
                    $PerOver = round(($jjgOver / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $PerOver = 0;
                }

                if ($sampleJJG != 0 && $jjgAbn != 0) {
                    $Perkosongjjg = round(($jjgKosng / ($sampleJJG - $jjgAbn)) * 100, 2);
                } else {
                    $Perkosongjjg = 0;
                }

                if ($sampleJJG != 0) {
                    $PerVcut = round(($vcut / $sampleJJG) * 100, 2);
                } else {
                    $PerVcut = 0;
                }

                if ($sampleJJG != 0) {
                    $PerAbr = round(($jjgAbn / $sampleJJG) * 100, 2);
                } else {
                    $PerAbr = 0;
                }

                $per_kr = round($total_kr * 100, 2);


                $totalSkor = skor_buah_mentah_mb($PerMth) + skor_buah_masak_mb($PerMsk) + skor_buah_over_mb($PerOver) + skor_vcut_mb($PerVcut) + skor_jangkos_mb($Perkosongjjg) + skor_abr_mb($per_kr);


                $nonZeroValues = array_filter([
                    $tph_blok,
                    $sampleJJG,
                    $jjgMth,
                    $jjgOver,
                    $jjgKosng,
                    $vcut,
                    $jum_kr,
                    $jjgAbn,
                    $jjgMsk
                ]);

                if (!empty($nonZeroValues) && !in_array(0, $nonZeroValues)) {
                    $bulananBh[$key][$key1]['totalSkor'] = $totalSkor;
                } else {
                    $bulananBh[$key][$key1]['totalSkor'] = 0;
                }


                $bulananBh[$key][$key1]['blok'] = $tph_blok;
                $bulananBh[$key][$key1]['sample_jjg'] = $sampleJJG;
                $bulananBh[$key][$key1]['jjg_mentah'] = $jjgMth;
                $bulananBh[$key][$key1]['mentahPerjjg'] = $PerMth;

                $bulananBh[$key][$key1]['jjg_msk'] = $jjgMsk;
                $bulananBh[$key][$key1]['mskPerjjg'] = $PerMsk;

                $bulananBh[$key][$key1]['jjg_over'] = $jjgOver;
                $bulananBh[$key][$key1]['overPerjjg'] = $PerOver;

                $bulananBh[$key][$key1]['jjg_kosong'] = $jjgKosng;
                $bulananBh[$key][$key1]['kosongPerjjg'] = $Perkosongjjg;

                $bulananBh[$key][$key1]['v_cut'] = $vcut;
                $bulananBh[$key][$key1]['vcutPerjjg'] = $PerVcut;

                $bulananBh[$key][$key1]['jjg_abr'] = $jjgAbn;
                $bulananBh[$key][$key1]['krPer'] = $per_kr;

                $bulananBh[$key][$key1]['jum_kr'] = $jum_kr;
                $bulananBh[$key][$key1]['abrPerjjg'] = $PerAbr;

                $bulananBh[$key][$key1]['skor_mentah'] = skor_buah_mentah_mb($PerMth);;
                $bulananBh[$key][$key1]['skor_msak'] = skor_buah_masak_mb($PerMsk);;
                $bulananBh[$key][$key1]['skor_over'] = skor_buah_over_mb($PerOver);;
                $bulananBh[$key][$key1]['skor_kosong'] = skor_jangkos_mb($Perkosongjjg);;
                $bulananBh[$key][$key1]['skor_vcut'] = skor_vcut_mb($PerVcut);;
                $bulananBh[$key][$key1]['skor_karung'] = skor_abr_mb($per_kr);;
                // $bulananBh[$key][$key1]['totalSkor'] = $totalSkor;
            }
        }
        // dd($bulananBh);
        $mutuTransAFD = array();
        foreach ($defaultTrans as $key => $value) {
            foreach ($value as $key1 => $value2) if (!empty($value2)) {
                $total_sample = 0;
                $total_brd = 0;
                $total_buah = 0;
                $brdPertph = 0;
                $buahPerTPH = 0;
                $totalSkor = 0;
                foreach ($value2 as $key2 => $value3)
                    if (is_array($value3)) {
                        $sum_bt = 0;
                        $sum_rst = 0;
                        $brdPertph = 0;
                        $buahPerTPH = 0;
                        $totalSkor = 0;
                        $dataBLok = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key3 => $value4) {
                            // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'] , $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value4['estate'] . ' ' . $value4['afdeling'] . ' ' . $value4['blok'];
                            // }
                            $dataBLok = count($listBlokPerAfd);
                            $sum_bt += $value4['bt'];
                            $sum_rst += $value4['rst'];
                        }
                        if ($dataBLok != 0) {
                            $brdPertph = round($sum_bt / $dataBLok, 2);
                        } else {
                            $brdPertph = 0;
                        }
                        if ($dataBLok != 0) {
                            $buahPerTPH = round($sum_rst / $dataBLok, 2);
                        } else {
                            $buahPerTPH = 0;
                        }

                        $totalSkor =  skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);

                        $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = $dataBLok;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd'] = $sum_bt;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = $brdPertph;
                        $mutuTransAFD[$key][$key1][$key2]['total_buah'] = $sum_rst;
                        $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = $buahPerTPH;
                        $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] =  skor_brd_tinggal($brdPertph);
                        $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = skor_buah_tinggal($buahPerTPH);
                        $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = $totalSkor;

                        //perhitungan untuk est
                        // dd($value3);

                        if ($reg == 2) {
                            foreach ($newTransv2 as $keyx => $value) if ($keyx ==  $key) {
                                foreach ($value as $keyx1 => $value1) if ($keyx1 ==  $key1) {
                                    $total_sample = $value1['tph_sampleReg'];
                                }
                            }
                        } else {
                            $total_sample += $dataBLok;
                        }
                        $total_brd += $sum_bt;
                        $total_buah += $sum_rst;
                    } else {
                        $mutuTransAFD[$key][$key1][$key2]['tph_sample'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_brd/TPH'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_buah'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['total_buahPerTPH'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['skor_brdPertph'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['skor_buahPerTPH'] = 0;
                        $mutuTransAFD[$key][$key1][$key2]['totalSkor'] = 0;
                    }



                if ($total_sample != 0) {
                    $brdPertph = round($total_brd / $total_sample, 2);
                } else {
                    $brdPertph = 0;
                }

                if ($total_sample != 0) {
                    $buahPerTPH = round($total_buah / $total_sample, 2);
                } else {
                    $buahPerTPH = 0;
                }

                $nonZeroValues = array_filter([$total_sample, $total_brd, $total_buah]);

                if (!empty($nonZeroValues)) {
                    $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);
                } else {
                    $totalSkor =  0;
                }

                // $totalSkor =   skor_brd_tinggal($brdPertph) + skor_buah_tinggal($buahPerTPH);


                $mutuTransAFD[$key][$key1]['total_sampleEST'] = $total_sample;
                $mutuTransAFD[$key][$key1]['total_brdEST'] = $total_brd;
                $mutuTransAFD[$key][$key1]['total_brdPertphEST'] = $brdPertph;
                $mutuTransAFD[$key][$key1]['total_buahEST'] = $total_buah;
                $mutuTransAFD[$key][$key1]['total_buahPertphEST'] = $buahPerTPH;
                $mutuTransAFD[$key][$key1]['skor_brd'] =   skor_brd_tinggal($brdPertph);;
                $mutuTransAFD[$key][$key1]['skor_buah'] = skor_buah_tinggal($buahPerTPH);;
                $mutuTransAFD[$key][$key1]['total_skor'] = $totalSkor;
            } else {
                $mutuTransAFD[$key][$key1]['total_sampleEST'] = 0;
                $mutuTransAFD[$key][$key1]['total_brdEST'] = 0;
                $mutuTransAFD[$key][$key1]['total_brdPertphEST'] = 0;
                $mutuTransAFD[$key][$key1]['total_buahEST'] = 0;
                $mutuTransAFD[$key][$key1]['total_buahPertphEST'] = 0;
                $mutuTransAFD[$key][$key1]['skor_brd'] = 0;
                $mutuTransAFD[$key][$key1]['skor_buah'] = 0;
                $mutuTransAFD[$key][$key1]['total_skor'] = 0;
            }
        }
        // dd($mutuTransAFD);
        // dd($mutuTransAFD,$newTransv2);

        //mt ancak 
        $GraphMTancak = array();
        foreach ($defaultNew as $key => $value) {
            foreach ($value as $key1 => $value1) if (!empty($value1)) {
                $total_brd = 0;
                $total_buah = 0;
                $total_skor = 0;
                $sum_p = 0;
                $sum_k = 0;
                $sum_gl = 0;
                $sum_panen = 0;
                $total_BrdperJJG = 0;
                $sum_pokok = 0;
                $sum_Restan = 0;
                $sum_s = 0;
                $sum_m1 = 0;
                $sum_m2 = 0;
                $sum_m3 = 0;
                $sumPerBH = 0;

                $sum_pelepah = 0;
                $perPl = 0;
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
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'];
                        }
                        $jum_ha = count($listBlokPerAfd);


                        $totalPokok += $value3["sample"];
                        $totalPanen += $value3["jjg"];
                        $totalP_panen += $value3["brtp"];
                        $totalK_panen +=  $value3["brtk"];
                        $totalPTgl_panen += $value3["brtgl"];

                        $totalbhts_panen += $value3["bhts"];
                        $totalbhtm1_panen += $value3["bhtm1"];
                        $totalbhtm2_panen += $value3["bhtm2"];
                        $totalbhtm3_oanen += $value3["bhtm3"];

                        $totalpelepah_s += $value3["ps"];
                    }
                    $akp = round(($totalPanen / $totalPokok) * 100, 1);
                    $skor_bTinggal = $totalP_panen + $totalK_panen + $totalPTgl_panen;

                    if ($totalPanen != 0) {
                        $brdPerjjg = round($skor_bTinggal / $totalPanen, 2);
                    } else {
                        $brdPerjjg = 0;
                    }

                    $sumBH = $totalbhts_panen +  $totalbhtm1_panen +  $totalbhtm2_panen +  $totalbhtm3_oanen;
                    if ($sumBH != 0) {
                        $sumPerBH = round($sumBH / ($totalPanen + $sumBH) * 100, 2);
                    } else {
                        $sumPerBH = 0;
                    }

                    if ($totalpelepah_s != 0) {
                        $perPl = round(($totalpelepah_s / $totalPokok) * 100, 2);
                    } else {
                        $perPl = 0;
                    }

                    $ttlSkorMA = skor_brd_ma($brdPerjjg) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);

                    $GraphMTancak[$key][$key1][$key2]['pokok_sample'] = $totalPokok;
                    $GraphMTancak[$key][$key1][$key2]['ha_sample'] = $jum_ha;
                    $GraphMTancak[$key][$key1][$key2]['jumlah_panen'] = $totalPanen;
                    $GraphMTancak[$key][$key1][$key2]['akp_rl'] =  $akp;
                    $GraphMTancak[$key][$key1][$key2]['p'] = $totalP_panen;
                    $GraphMTancak[$key][$key1][$key2]['k'] = $totalK_panen;
                    $GraphMTancak[$key][$key1][$key2]['tgl'] = $totalPTgl_panen;
                    // $GraphMTancak[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $GraphMTancak[$key][$key1][$key2]['brd/jjg'] = $brdPerjjg;

                    // data untuk buah tinggal
                    $GraphMTancak[$key][$key1][$key2]['bhts_s'] = $totalbhts_panen;
                    $GraphMTancak[$key][$key1][$key2]['bhtm1'] = $totalbhtm1_panen;
                    $GraphMTancak[$key][$key1][$key2]['bhtm2'] = $totalbhtm2_panen;
                    $GraphMTancak[$key][$key1][$key2]['bhtm3'] = $totalbhtm3_oanen;


                    $GraphMTancak[$key][$key1][$key2]['jjgperBuah'] = $sumPerBH;
                    // data untuk pelepah sengklek

                    $GraphMTancak[$key][$key1][$key2]['palepah_pokok'] = $totalpelepah_s;
                    $GraphMTancak[$key][$key1][$key2]['palepahPerPk'] = $perPl;
                    // total skor akhir
                    $GraphMTancak[$key][$key1][$key2]['skor_bh'] = skor_buah_Ma($sumPerBH);
                    $GraphMTancak[$key][$key1][$key2]['skor_brd'] = skor_brd_ma($brdPerjjg);
                    $GraphMTancak[$key][$key1][$key2]['skor_ps'] = skor_palepah_ma($perPl);
                    $GraphMTancak[$key][$key1][$key2]['skor_akhir'] = $ttlSkorMA;

                    $sum_panen += $totalPanen;
                    $sum_pokok += $totalPokok;
                    //brondolamn
                    $sum_p += $totalP_panen;
                    $sum_k += $totalK_panen;
                    $sum_gl += $totalPTgl_panen;
                    //buah tianggal
                    $sum_s += $totalbhts_panen;
                    $sum_m1 += $totalbhtm1_panen;
                    $sum_m2 += $totalbhtm2_panen;
                    $sum_m3 += $totalbhtm3_oanen;
                    //pelepah
                    $sum_pelepah += $totalpelepah_s;
                } else {
                    $GraphMTancak[$key][$key1][$key2]['pokok_sample'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['ha_sample'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['jumlah_panen'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['akp_rl'] = 0;

                    $GraphMTancak[$key][$key1][$key2]['p'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['k'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['tgl'] = 0;

                    // $GraphMTancak[$key][$key1][$key2]['total_brd'] = $skor_bTinggal;
                    $GraphMTancak[$key][$key1][$key2]['brd/jjg'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['skor_brd'] = 0;
                    // data untuk buah tinggal
                    $GraphMTancak[$key][$key1][$key2]['bhts_s'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['bhtm1'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['bhtm2'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['bhtm3'] = 0;

                    $GraphMTancak[$key][$key1][$key2]['skor_bh'] = 0;
                    // $GraphMTancak[$key][$key1][$key2]['jjgperBuah'] = number_format($sumPerBH, 2);
                    // data untuk pelepah sengklek
                    $GraphMTancak[$key][$key1][$key2]['skor_ps'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['palepah_pokok'] = 0;
                    $GraphMTancak[$key][$key1][$key2]['palepahPerPk'] = 0;
                    // total skor akhir
                    $GraphMTancak[$key][$key1][$key2]['skor_akhir'] = 0;
                }
                $total_brd = $sum_p + $sum_k + $sum_gl;
                $total_buah = $sum_s + $sum_m1 + $sum_m2 + $sum_m3;
                // $persenPalepah = $sum_palepah/$sum_pokok 

                if ($sum_panen != 0) {
                    $total_BrdperJJG = round($total_brd / $sum_panen, 2);
                } else {
                    $total_BrdperJJG = 0;
                }

                if ($sum_panen != 0) {
                    $sumPerBH = round($total_buah / ($sum_panen + $total_buah) * 100, 2);
                } else {
                    $sumPerBH = 0;
                }

                if ($sum_pelepah != 0) {
                    $perPl = round(($sum_pelepah / $sum_pokok) * 100, 2);
                } else {
                    $perPl = 0;
                }



                $total_skor = skor_brd_ma($total_BrdperJJG) + skor_buah_Ma($sumPerBH) + skor_palepah_ma($perPl);


                $nonZeroValues = array_filter([$total_brd, $total_buah]);

                if (!empty($nonZeroValues) && !in_array(0, $nonZeroValues)) {
                    $GraphMTancak[$key][$key1]['skor_finals'] = $total_skor;
                } else {
                    $GraphMTancak[$key][$key1]['skor_finals'] = 0;
                }


                $GraphMTancak[$key][$key1]['total_p.k.gl'] = $total_brd;
                $GraphMTancak[$key][$key1]['total_jumPanen'] = $sum_panen;
                $GraphMTancak[$key][$key1]['total_jumPokok'] = $sum_pokok;
                $GraphMTancak[$key][$key1]['total_brd/jjg'] = $total_BrdperJJG;
                $GraphMTancak[$key][$key1]['skor_brd'] = skor_brd_ma($total_BrdperJJG);
                //buah tinggal
                $GraphMTancak[$key][$key1]['s'] = $sum_s;
                $GraphMTancak[$key][$key1]['m1'] = $sum_m1;
                $GraphMTancak[$key][$key1]['m2'] = $sum_m2;
                $GraphMTancak[$key][$key1]['m3'] = $sum_m3;
                $GraphMTancak[$key][$key1]['total_bh'] = $total_buah;
                $GraphMTancak[$key][$key1]['total_bh/jjg'] = $sumPerBH;
                $GraphMTancak[$key][$key1]['skor_bh'] = skor_buah_Ma($sumPerBH);
                $GraphMTancak[$key][$key1]['pokok_palepah'] = $sum_pelepah;
                $GraphMTancak[$key][$key1]['perPalepah'] = $perPl;
                $GraphMTancak[$key][$key1]['skor_perPl'] = skor_palepah_ma($perPl);
                //total skor akhir
                // $GraphMTancak[$key][$key1]['skor_finals'] = $total_skor;
            }
        }
        // dd($mutuTransAFD['RDE']['February'], $GraphMTancak['RDE']['February'], $bulananBh['RDE']['February']);
        //hitung untuk per estate
        // dd($bulananBh['RDE']['February']);
        // dd($mutuTransAFD);
        // TOTALAN SKOR
        $RekapBulan = array();
        foreach ($mutuTransAFD as $key => $value) {
            foreach ($value as $key2  => $value2) {
                foreach ($GraphMTancak as $key3 => $value3) {
                    foreach ($value3 as $key4 => $value4) {
                        foreach ($bulananBh as $key5 => $value5) {
                            foreach ($value5 as $key6 => $value6)
                                if ($key == $key3 && $key3 == $key5 && $key2 == $key4 && $key4 == $key6) {
                                    $RekapBulan[$key][$key2]['bulan_skor'] = $value2['total_skor'] + $value4['skor_finals'] + $value6['totalSkor'];
                                }
                        }
                    }
                }
            }
        }
        // dd($RekapBulan);
        $RekapBulan_wil = array();
        foreach ($queryEste as $key => $value) {
            foreach ($RekapBulan as $key1 => $value2) if ($value['est'] == $key1 && $wilayahGrafik == $value['wil']) {
                // dd($key ,$key1);
                $RekapBulan_wil[$key1] = $value2;
            }
        }

        $RekapEst_wil = [];
        // $estData = "PLE";
        if ($estData !== 'CWS1' && isset($RekapBulan_wil)) {
            foreach ($RekapBulan_wil as $month => $data) {

                foreach ($data as $months => $data2) {

                    $RekapEst_wil[$month][$months] = isset($data2['bulan_skor']) ? $data2['bulan_skor'] : 0;
                }
            }
        } else {
            $RekapEst_wil = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }
        // dd($RekapEst_wil);
        $RekapEst = [];
        // $estData = "PLE";
        if ($estData !== 'CWS1' && isset($RekapBulan[$estData])) {
            foreach ($RekapBulan[$estData] as $month => $data) {
                $RekapEst[$estData][$month] = isset($data['bulan_skor']) ? $data['bulan_skor'] : 0;
            }
        } else {
            $RekapEst[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }

        // dd($RekapEst);
        $RekapSkor = array();
        foreach ($RekapEst as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $RekapSkor[] = $value1;
            }
        }
        $rekapWilayah = array();
        foreach ($RekapBulan as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $rekapWilayah[] = $value1;
            }
        }

        // dd($RekapBulan);
        //mutuancak totalBRD
        $ancakBRD = [];

        if ($estData !== 'CWS1' && isset($GraphMTancak[$estData])) {
            foreach ($GraphMTancak[$estData] as $month => $data) {
                $ancakBRD[$estData][$month] = isset($data['total_brd/jjg']) ? $data['total_brd/jjg'] : 0;
            }
        } else {
            $ancakBRD[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }

        // dd($ancakBRD);
        $chartBTT = array();
        foreach ($ancakBRD as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBTT[] = $value1;
            }
        }
        // dd($chartBTT);
        //mutuancak totalnuah
        $ancakBuah = [];

        if ($estData !== 'CWS1' && isset($GraphMTancak[$estData])) {
            foreach ($GraphMTancak[$estData] as $month => $data) {
                $ancakBuah[$estData][$month] = isset($data['total_bh/jjg']) ? $data['total_bh/jjg'] : 0;
            }
        } else {
            $ancakBuah[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }

        // dd($ancakBuah);

        $chartBuah = array();
        foreach ($ancakBuah as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuah[] = $value1;
            }
        }

        $mtbuahMentah = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahMentah[$estData][$month] = isset($data['mentahPerjjg']) ? $data['mentahPerjjg'] : 0;
            }
        } else {
            $mtbuahMentah[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }

        // dd($mtbuahMentah);

        $chartBuahMentah = array();
        foreach ($mtbuahMentah as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahMentah[] = $value1;
            }
        }

        $mtbuahMasak = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahMasak[$estData][$month] = isset($data['mskPerjjg']) ? $data['mskPerjjg'] : 0;
            }
        } else {
            $mtbuahMasak[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }

        // dd($mtbuahMasak);

        $chartBuahMasak = array();
        foreach ($mtbuahMasak as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahMasak[] = $value1;
            }
        }

        $mtbuahOver = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahOver[$estData][$month] = isset($data['overPerjjg']) ? $data['overPerjjg'] : 0;
            }
        } else {
            $mtbuahOver[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartBuahOver = array();
        foreach ($mtbuahOver as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahOver[] = $value1;
            }
        }

        $mtbuahKsng = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahKsng[$estData][$month] = isset($data['kosongPerjjg']) ? $data['kosongPerjjg'] : 0;
            }
        } else {
            $mtbuahKsng[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartBuahKsng = array();
        foreach ($mtbuahKsng as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahKsng[] = $value1;
            }
        }

        $mtbuahVcut = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahVcut[$estData][$month] = isset($data['vcutPerjjg']) ? $data['vcutPerjjg'] : 0;
            }
        } else {
            $mtbuahVcut[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartBuahVcut = array();
        foreach ($mtbuahVcut as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahVcut[] = $value1;
            }
        }

        $mtbuahAbr = [];

        if ($estData !== 'CWS1' && isset($bulananBh[$estData])) {
            foreach ($bulananBh[$estData] as $month => $data) {
                $mtbuahAbr[$estData][$month] = isset($data['abrPerjjg']) ? $data['abrPerjjg'] : 0;
            }
        } else {
            $mtbuahAbr[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartBuahAbr = array();
        foreach ($mtbuahAbr as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartBuahAbr[] = $value1;
            }
        }
        $mtTransportbrd = [];

        if ($estData !== 'CWS1' && isset($mutuTransAFD[$estData])) {
            foreach ($mutuTransAFD[$estData] as $month => $data) {
                $mtTransportbrd[$estData][$month] = isset($data['total_brdPertphEST']) ? $data['total_brdPertphEST'] : 0;
            }
        } else {
            $mtTransportbrd[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartTransBrd = array();
        foreach ($mtTransportbrd as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartTransBrd[] = $value1;
            }
        }

        $mtTransportbuah = [];


        if ($estData !== 'CWS1' && isset($mutuTransAFD[$estData])) {
            foreach ($mutuTransAFD[$estData] as $month => $data) {
                $mtTransportbuah[$estData][$month] = isset($data['total_buahPertphEST']) ? $data['total_buahPertphEST'] : 0;
            }
        } else {
            $mtTransportbuah[$estData] = [
                "January" => 0,
                "February" => 0,
                "March" => 0,
                "April" => 0,
                "May" => 0,
                "June" => 0,
                "July" => 0,
                "August" => 0,
                "September" => 0,
                "October" => 0,
                "November" => 0,
                "December" => 0
            ];
        }



        $chartTransbuah = array();
        foreach ($mtTransportbuah as $key => $value) {
            foreach ($value as $key1 => $value1) {

                $chartTransbuah[] = $value1;
            }
        }


        // dd($RekapSkor, $chartBuah);

        $arrView = array();

        // dd($queryEste);

        $arrView['GraphBtt'] =  $chartBTT;
        $arrView['GraphBuah'] =  $chartBuah;
        $arrView['GraphSkorTotal'] =  $RekapSkor;
        $arrView['list_est'] =  $queryEste;
        $arrView['mtbuah_mth'] =  $chartBuahMentah;
        $arrView['mtbuah_masak'] =  $chartBuahMasak;
        $arrView['mtbuah_over'] =  $chartBuahOver;
        $arrView['mtbuah_ksng'] =  $chartBuahKsng;
        $arrView['mtbuah_vcut'] =  $chartBuahVcut;
        $arrView['mtbuah_abr'] =  $chartBuahAbr;
        $arrView['mttransbrd'] =  $chartTransBrd;
        $arrView['mttransbb'] =  $chartTransbuah;
        $arrView['rekap_wil'] =  $RekapEst_wil;

        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }
}
