<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require '../app/helpers.php';

class emplacementsController extends Controller
{
    public function dashboard_perum(Request $request)
    {


        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        // dd($bulan);
        $shortMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'AVE', 'STATUS'];

        $arrHeader = ['No', 'Estate', 'Kode', 'Estate Manager'];
        $arrHeader =  array_merge($arrHeader, $shortMonth);
        // array_push($arrHeader, date('Y'));

        $arrHeaderSc = ['WILAYAH', 'Group Manager'];
        $arrHeaderSc = array_merge($arrHeaderSc, $shortMonth);
        // array_push($arrHeaderSc, date('Y'));

        $arrHeaderReg = ['Region', 'Region Head'];
        $arrHeaderReg = array_merge($arrHeaderReg, $shortMonth);

        $arrHeaderTrd = ['No', 'Estate', 'Afdeling', 'Nama Asisten'];
        $arrHeaderTrd =  array_merge($arrHeaderTrd, $shortMonth);
        // array_push($arrHeaderTrd, date('Y'));

        $queryAsisten = DB::connection('mysql2')->table('asisten_qc')
            ->select('asisten_qc.*')
            ->get();

        $queryAsisten = json_decode($queryAsisten, true);

        // dd($mutu_buahs, $sidak_buah);
        // $arrView['list_bulan'] =  $bulan;
        return view('dashboard_perum', [
            'arrHeader' => $arrHeader,
            'arrHeaderSc' => $arrHeaderSc,
            'arrHeaderTrd' => $arrHeaderTrd,
            'arrHeaderReg' => $arrHeaderReg,
            'list_bulan' => $bulan,
            'shortMonth' => $shortMonth,
        ]);
    }

    public function getAFD(Request $request)
    {
        $regional = $request->input('reg');
        $bulan = $request->input('tahun');

        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE', 'CWS1', 'CWS2', 'CWS3'])
            ->get();
        $queryEste = json_decode($queryEste, true);


        $estates = array_column($queryEste, 'est');
        // dd($estates);
        $emplacement = DB::connection('mysql2')->table('perumahan')
            ->select(
                "perumahan.*",
                DB::raw('DATE_FORMAT(perumahan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(perumahan.datetime, "%Y") as tahun'),
            )
            ->where('perumahan.datetime', 'like', '%' . $bulan . '%')
            ->whereIn('est', $estates)
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $emplacement = json_decode(json_encode($emplacement), true); // Convert the collection to an array
        $emplacement = collect($emplacement)->groupBy(['est', 'afd'])->toArray();

        $lingkungan = DB::connection('mysql2')->table('lingkungan')
            ->select(
                "lingkungan.*",
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%Y") as tahun'),
            )
            ->where('lingkungan.datetime', 'like', '%' . $bulan . '%')
            ->whereIn('est', $estates)
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $lingkungan = json_decode(json_encode($lingkungan), true); // Convert the collection to an array
        $lingkungan = collect($lingkungan)->groupBy(['est', 'afd'])->toArray();


        $landscape = DB::connection('mysql2')->table('landscape')
            ->select(
                "landscape.*",
                DB::raw('DATE_FORMAT(landscape.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(landscape.datetime, "%Y") as tahun'),
            )
            ->where('landscape.datetime', 'like', '%' . $bulan . '%')
            ->whereIn('est', $estates)
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $landscape = json_decode(json_encode($landscape), true); // Convert the collection to an array
        $landscape = collect($landscape)->groupBy(['est', 'afd'])->toArray();

        // dd($emplacement);


        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);
        // dd($emplacement);
        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        // untuk perumahan 
        $dataPerBulan = array();
        foreach ($emplacement as $key => $value) {
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


        foreach ($defaultNew as $key => $value) {
            // dd($key);
            foreach ($queryEste as $key2 => $value2) if ($key == $value2['est']) {
                $emplashmenOri[$value2['nama']] = $value;
            }
        }

        // untuk lingkungan 
        $perbulan_lingkungan = array();
        foreach ($lingkungan as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $perbulan_lingkungan)) {
                        $perbulan_lingkungan[$month] = array();
                    }
                    if (!array_key_exists($key, $perbulan_lingkungan[$month])) {
                        $perbulan_lingkungan[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $perbulan_lingkungan[$month][$key])) {
                        $perbulan_lingkungan[$month][$key][$key2] = array();
                    }
                    $perbulan_lingkungan[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $def_lingkungan = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $def_lingkungan[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }

        foreach ($def_lingkungan as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($perbulan_lingkungan as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $def_lingkungan[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        // dd($def_lingkungan);
        foreach ($def_lingkungan as $key => $value) {
            // dd($key);
            foreach ($queryEste as $key2 => $value2) if ($key == $value2['est']) {
                $lingkunganOri[$value2['nama']] = $value;
            }
        }

        // dd($lingkunganOri);

        // untuk landscape 
        $perbulan_landscape = array();
        foreach ($landscape as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $perbulan_landscape)) {
                        $perbulan_landscape[$month] = array();
                    }
                    if (!array_key_exists($key, $perbulan_landscape[$month])) {
                        $perbulan_landscape[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $perbulan_landscape[$month][$key])) {
                        $perbulan_landscape[$month][$key][$key2] = array();
                    }
                    $perbulan_landscape[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $def_lanscape = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $def_lanscape[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }

        foreach ($def_lanscape as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($perbulan_landscape as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $def_lanscape[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }


        foreach ($def_lanscape as $key => $value) {
            foreach ($queryEste as $key2 => $value2) if ($key == $value2['est']) {
                $landscapeOri[$value2['nama']] = $value;
            }
        }

        $hitungRmh = array();
        foreach ($emplashmenOri as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungRmh[$key][$key1] = [];
                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            // Initialize the "nilai_total" for each index (OA, OB, OC, OD) to 0
                            $hitungRmh[$key][$key1][$key2] = [];
                            foreach ($value3 as $key3 => $value4) {
                                if (is_array($value4)) {
                                    // Check if the "nilai" key exists, otherwise set it to 0
                                    $sumNilai = isset($value4['nilai']) ? array_sum(array_map('intval', explode('$', $value4['nilai']))) : 0;
                                    // Store the sum in the "nilai_total" key of the corresponding index
                                    $date = $value4['datetime'];
                                    // Get the year and month from the datetime
                                    $yearMonth = date('Y-m-d', strtotime($date));


                                    $hitungRmh[$key][$key1][$key2][$key3]['nilai_total'] = $sumNilai;
                                    $hitungRmh[$key][$key1][$key2][$key3]['date'] = $yearMonth;
                                    $hitungRmh[$key][$key1][$key2][$key3]['est'] = $value4['est'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['afd'] = $value4['afd'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['petugas'] = $value4['petugas'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['pendamping'] = $value4['pendamping'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['penghuni'] = $value4['penghuni'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['tipe_rumah'] = $value4['tipe_rumah'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['foto_temuan'] = $value4['foto_temuan'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['komentar_temuan'] = $value4['komentar_temuan'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['nilai'] = $value4['nilai'];
                                    $hitungRmh[$key][$key1][$key2][$key3]['komentar'] = $value4['komentar'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // dd($emplashmenOri, $hitungRmh);

        foreach ($emplashmenOri as $location => $months) {
            foreach ($months as $month => $values) {
                if (isset($hitungRmh[$location][$month])) {
                    // Merge the values from hitungRmh if the month exists in both arrays
                    foreach ($values as $key => $value) {
                        if (is_array($value) && isset($hitungRmh[$location][$month][$key])) {
                            $emplashmenOri[$location][$month][$key] = $hitungRmh[$location][$month][$key];
                        }
                    }
                } else {
                    // If the month does not exist in hitungRmh, set default values as 0
                    $emplashmenOri[$location][$month] = array_fill_keys(array_keys($values), 0);
                }
            }
        }

        // Resulting merged array with values from hitungRmh
        $mergedArray_rmh = $emplashmenOri;

        // Now, the "nilai" values will be updated with their respective sums in the $emplacement array.




        $FinalArr_rumah = array();
        foreach ($mergedArray_rmh as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_rumah[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    } else {
                        // For the innermost arrays that are not arrays themselves, just copy them as is
                        $FinalArr_rumah[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }

        // dd($modifiedMergedArray);

        // untuk landscape 
        $hitungLandscape = array();
        foreach ($landscapeOri as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungLandscape[$key][$key1] = [];
                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            // Initialize the "nilai_total" for each index (OA, OB, OC, OD) to 0
                            $hitungLandscape[$key][$key1][$key2] = [];
                            foreach ($value3 as $key3 => $value4) {
                                if (is_array($value4)) {
                                    // Check if the "nilai" key exists, otherwise set it to 0
                                    $sumNilai = isset($value4['nilai']) ? array_sum(array_map('intval', explode('$', $value4['nilai']))) : 0;
                                    // Store the sum in the "nilai_total" key of the corresponding index
                                    $date = $value4['datetime'];
                                    // Get the year and month from the datetime
                                    $yearMonth = date('Y-m-d', strtotime($date));


                                    $hitungLandscape[$key][$key1][$key2][$key3]['nilai_total_LP'] = $sumNilai;
                                    $hitungLandscape[$key][$key1][$key2][$key3]['date'] = $yearMonth;
                                    $hitungLandscape[$key][$key1][$key2][$key3]['est_LP'] = $value4['est'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['afd_LP'] = $value4['afd'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['petugas_LP'] = $value4['petugas'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['pendamping_LP'] = $value4['pendamping'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['penghuni_LP'] = $value4['penghuni'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['tipe_rumah_LP'] = $value4['tipe_rumah'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['foto_temuan_LP'] = $value4['foto_temuan'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['komentar_temuan_LP'] = $value4['komentar_temuan'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['nilai_LP'] = $value4['nilai'];
                                    $hitungLandscape[$key][$key1][$key2][$key3]['komentar_LP'] = $value4['komentar'];
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($landscapeOri as $location => $months) {
            foreach ($months as $month => $values) {
                if (isset($hitungLandscape[$location][$month])) {
                    // Merge the values from hitungLandscape if the month exists in both arrays
                    foreach ($values as $key => $value) {
                        if (is_array($value) && isset($hitungLandscape[$location][$month][$key])) {
                            $landscapeOri[$location][$month][$key] = $hitungLandscape[$location][$month][$key];
                        }
                    }
                } else {
                    // If the month does not exist in hitungRmh, set default values as 0
                    $landscapeOri[$location][$month] = array_fill_keys(array_keys($values), 0);
                }
            }
        }

        // Resulting merged array with values from hitungRmh
        $mergedArray_lp = $landscapeOri;

        $FinalArr_LP = array();
        foreach ($mergedArray_lp as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_LP[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    } else {
                        // For the innermost arrays that are not arrays themselves, just copy them as is
                        $FinalArr_LP[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }

        // dd($FinalArr_LP);

        // hitungan lingkungan 

        $hitungLingkungan = array();
        foreach ($lingkunganOri as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungLingkungan[$key][$key1] = [];
                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            // Initialize the "nilai_total" for each index (OA, OB, OC, OD) to 0
                            $hitungLingkungan[$key][$key1][$key2] = [];
                            foreach ($value3 as $key3 => $value4) {
                                if (is_array($value4)) {
                                    // Check if the "nilai" key exists, otherwise set it to 0
                                    $sumNilai = isset($value4['nilai']) ? array_sum(array_map('intval', explode('$', $value4['nilai']))) : 0;
                                    // Store the sum in the "nilai_total" key of the corresponding index
                                    $date = $value4['datetime'];
                                    // Get the year and month from the datetime
                                    $yearMonth = date('Y-m-d', strtotime($date));


                                    $hitungLingkungan[$key][$key1][$key2][$key3]['nilai_total_Lngkl'] = $sumNilai;
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['date'] = $yearMonth;
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['est_Lngkl'] = $value4['est'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['afd_Lngkl'] = $value4['afd'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['petugas_Lngkl'] = $value4['petugas'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['pendamping_Lngkl'] = $value4['pendamping'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['penghuni_Lngkl'] = $value4['penghuni'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['tipe_rumah_Lngkl'] = $value4['tipe_rumah'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['foto_temuan_Lngkl'] = $value4['foto_temuan'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['komentar_temuan_Lngkl'] = $value4['komentar_temuan'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['nilai_Lngkl'] = $value4['nilai'];
                                    $hitungLingkungan[$key][$key1][$key2][$key3]['komentar_Lngkl'] = $value4['komentar'];
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($lingkunganOri as $location => $months) {
            foreach ($months as $month => $values) {
                if (isset($hitungLingkungan[$location][$month])) {
                    // Merge the values from hitungLingkungan if the month exists in both arrays
                    foreach ($values as $key => $value) {
                        if (is_array($value) && isset($hitungLingkungan[$location][$month][$key])) {
                            $lingkunganOri[$location][$month][$key] = $hitungLingkungan[$location][$month][$key];
                        }
                    }
                } else {
                    // If the month does not exist in hitungRmh, set default values as 0
                    $lingkunganOri[$location][$month] = array_fill_keys(array_keys($values), 0);
                }
            }
        }

        // Resulting merged array with values from hitungRmh
        $mrg_lingkn = $lingkunganOri;

        $FinalArr_Lingkn = array();
        foreach ($mrg_lingkn as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_Lingkn[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    } else {
                        // For the innermost arrays that are not arrays themselves, just copy them as is
                        $FinalArr_Lingkn[$key1][$key2][$key3] = $value3;
                    }
                }
            }
        }



        function mergeArrays($arr1, $arr2, $arr3)
        {
            foreach ($arr1 as $key => $value) {
                if (isset($arr2[$key])) {
                    $arr1[$key] = array_merge_recursive($value, $arr2[$key]);
                }
                if (isset($arr3[$key])) {
                    $arr1[$key] = mergeArraysRecursive($arr1[$key] ?? [], $arr3[$key]);
                }
            }

            return $arr1;
        }

        function mergeArraysRecursive($arr1, $arr2)
        {
            foreach ($arr2 as $key => $value) {
                if (is_array($value)) {
                    $arr1[$key] = mergeArraysRecursive($arr1[$key] ?? [], $value);
                } else {
                    $arr1[$key] = $value ?? 0;
                }
            }

            return $arr1;
        }

        $result = mergeArrays($FinalArr_rumah, $FinalArr_LP, $FinalArr_Lingkn);



        foreach ($result as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        $containsNonZeroValue = false;
                        foreach ($value2 as $innerValue) {
                            if (!is_array($innerValue) && $innerValue === 0) {
                                $containsNonZeroValue = true;
                                break;
                            }
                        }

                        if ($containsNonZeroValue) {
                            $filteredValue = array_filter($value2, function ($val) {
                                return !(is_numeric($val) && $val === 0);
                            });

                            $result[$key][$key1][$key2] = $filteredValue;
                            if (empty($filteredValue)) {
                                unset($result[$key][$key1][$key2]);
                            }
                        }
                    }
                }
            }
        }


        $queryAsisten =  DB::connection('mysql2')->Table('asisten_qc')->get();

        $queryAsisten = json_decode($queryAsisten, true);


        $final_result = array();

        foreach ($result as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $inc = 1;
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            $EM = 'GM';
                            $nama = '-';
                            foreach ($queryAsisten as $value4) {
                                if ($value4['est'] == ($value3['est'] ?? $value3['est_LP'] ??  $value3['est_Lngkl'] ?? null) && $value4['afd'] == $key2) {
                                    $nama = $value4['nama'];
                                    break;
                                }
                            }
                            $total_skor = ($value3['nilai_total'] ?? 0) + ($value3['nilai_total_LP'] ?? 0) + ($value3['nilai_total_Lngkl'] ?? 0);
                            $final_result[$key][$key1][$key2][$key3]['skor_total'] = $total_skor;
                            $final_result[$key][$key1][$key2][$key3]['visit'] = $inc++;
                            $final_result[$key][$key1][$key2][$key3]['est'] = $value3['est'] ?? $value3['est_LP'] ??  $value3['est_Lngkl'] ?? null;
                            $final_result[$key][$key1][$key2][$key3]['afd'] = $key2;
                            $final_result[$key][$key1][$key2][$key3]['asisten'] = $nama;
                            $final_result[$key][$key1][$key2][$key3]['date'] = $key3;
                        }
                    } else {
                        // If it's not an array, it means it's a direct value (e.g., February, March, etc.)
                        // Set default value of 0 for missing months
                        $final_result[$key][$key1][$key2] = $value2;
                    }
                }
            }
        }



        $resultArray = [];

        // Loop through the original array
        foreach ($final_result as $estate => $months) {
            foreach ($months as $month => $afdelings) {
                foreach ($afdelings as $afdeling => $data) {
                    $resultArray[$estate][$afdeling][$month] = $data;
                }
            }
        }


        foreach ($resultArray as $estate => $months) {
            foreach ($months as $afdeling => $data) {
                foreach ($data as $month => $visits) {
                    if (is_array($visits)) {
                        $visitsWithoutDates = []; // Array to store visit data without individual dates
                        foreach ($visits as $date => $visitData) {
                            if (is_array($visitData)) { // Check if $visitData is an array (not an individual date)
                                $visitKey = 'visit' . count($visitsWithoutDates) + 1;
                                $visitsWithoutDates[$visitKey] = $visitData;
                            }
                        }
                        // dd($visitData);

                        // Add default visit data for months with no visits (empty $visitsWithoutDates)
                        if (empty($visitsWithoutDates)) {
                            $visitsWithoutDates['visit1'] = [
                                'skor_total' => 0,
                                'visit' => 1,
                                'est' => $estate,
                                'afd' => $afdeling,

                                'date' => '-'
                            ];
                        }

                        // After the loop, set the resultArray to the updated visits without dates
                        $resultArray[$estate][$afdeling][$month] = $visitsWithoutDates;
                    } else {
                        $nama = '-';
                        foreach ($queryAsisten as $value4) {
                            if ($value4['est'] == $estate && $value4['afd'] == $afdeling) {
                                $nama = $value4['nama'];
                                break;
                            }
                        }
                        $resultArray[$estate][$afdeling][$month] = [
                            'visit1' => [
                                'skor_total' => 0,
                                'visit' => 1,
                                'est' => $estate,
                                'afd' => $afdeling,
                                'asisten' => $nama,
                                'date' => '-'
                            ]
                        ];
                    }
                }
            }
        }

        // dd($resultArray);
        $get_cell = array();
        foreach ($resultArray as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $visit = count($value2);
                    $get_cell[$key2][$key][$key1]['visit'] = $visit;
                }
            }
        }



        // dd($get_cell, $get_max);

        $arrView = array();
        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;
        $arrView['afd_rekap'] =  $resultArray;


        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();

        // return view('dashboard_perum', [
        //     'afd_rekap' => $resultArray
        // ]);
    }

    public function estAFD(Request $request)
    {
        $regional = $request->input('reg');
        $bulan = $request->input('tahun');

        // Perform any required processing here



        // dd($emplashment, $queryEste);
        // Return a JSON response
        $arrView = array();

        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;


        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }
}
