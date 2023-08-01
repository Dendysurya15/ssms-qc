<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // dd($regional, $bulan);

        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE', 'CWS1', 'CWS2', 'CWS3', 'SRS', 'TC', 'SR', 'SLM', 'SGM', 'SKM', 'SYM', 'NBM'])
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
            // ->whereNotIn('perumahan.est', 'REG%')
            ->whereNotIn('perumahan.afd', ['est', 'es'])
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
            // ->whereNotIn('lingkungan.est', 'REG%')
            ->whereNotIn('lingkungan.afd', ['est', 'es'])
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
            ->whereNotIn('landscape.afd', ['est', 'es'])
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
        // dd($emplashmenOri);
        // untuk li dd($defaultNew);ngkungan 
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

        // dd($hitungRmh);

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


        // dd($mergedArray_rmh, $hitungRmh);

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

        // dd($mergedArray_rmh, $FinalArr_rumah);

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

        // dd($FinalArr_rumah, $FinalArr_LP, $FinalArr_Lingkn);

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
        // dd($resultArray);
        // dd($final_result, $resultArray);
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

        $max_visit = array();
        foreach ($get_cell as $month => $monthData) {
            $max_visitEst = 0;
            foreach ($monthData as $location => $locationData) {
                $maxVisit = 0;
                foreach ($locationData as $visitData) {
                    // Check if the current visit value is greater than the current maximum visit
                    if (isset($visitData['visit']) && $visitData['visit'] > $maxVisit) {
                        // Update the maximum visit for this location
                        $maxVisit = $visitData['visit'];
                    }
                }
                // Add the maximum visit for this location to the $max_visit array
                $max_visit[$month][$location]['max_visit'] = $maxVisit;

                // Calculate the overall maximum visit for the entire array
                if ($maxVisit > $max_visitEst) {
                    $max_visitEst = $maxVisit;
                }
            }
            // Add the overall maximum visit for this month to the $max_visit array
            $max_visit[$month]['max_visitEst'] = $max_visitEst;
        }

        $header_cell = array();
        foreach ($max_visit as $key => $value) {
            $header_cell[$key] = $value['max_visitEst'];
        }

        function createEmptyVisit($visitNumber)
        {
            return [
                "skor_total" => 0,
                "visit" => $visitNumber,
                "est" => "Kenambui", // Replace with the appropriate value
                "afd" => "OB", // Replace with the appropriate value
                "asisten" => "-",
                "date" => "-",
            ];
        }


        foreach ($resultArray as $key1 => &$level1) {
            foreach ($level1 as $key2 => &$level2) {
                foreach ($level2 as $month => &$visits) {
                    if (isset($header_cell[$month])) {
                        $requiredVisits = $header_cell[$month];
                        $currentVisits = count($visits);

                        // Add empty visits if required
                        for ($i = $currentVisits + 1; $i <= $requiredVisits; $i++) {
                            $visits["visit" . $i] = createEmptyVisit($i);
                        }
                    }
                }
            }
        }



        $sum_header = [
            "head" => array_sum($header_cell) + 2,
        ];


        // dd($header_cell, $sum_header);
        $arrView = array();
        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;
        $arrView['afd_rekap'] =  $resultArray;
        $arrView['header_cell'] =  $header_cell;
        $arrView['header_head'] =  $sum_header;


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

        $queryEste = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();
        $queryEste = json_decode($queryEste, true);

        // dd($queryEste);
        $estates = array_column($queryEste, 'est');

        $emplacement = DB::connection('mysql2')->table('perumahan')
            ->select(
                "perumahan.*",
                DB::raw('DATE_FORMAT(perumahan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(perumahan.datetime, "%Y") as tahun'),
            )
            ->where('perumahan.datetime', 'like', '%' . $bulan . '%')
            ->whereIn('est', $estates)
            ->where('perumahan.afd', 'EST')
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $emplacement = json_decode(json_encode($emplacement), true); // Convert the collection to an array
        $emplacement = collect($emplacement)->groupBy(['est', 'afd'])->toArray();

        // dd($emplacement);

        $lingkungan = DB::connection('mysql2')->table('lingkungan')
            ->select(
                "lingkungan.*",
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%Y") as tahun'),
            )
            ->where('lingkungan.datetime', 'like', '%' . $bulan . '%')
            ->whereIn('est', $estates)
            ->where('lingkungan.afd', 'EST')
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
            ->where('landscape.afd', 'EST')
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $landscape = json_decode(json_encode($landscape), true); // Convert the collection to an array
        $landscape = collect($landscape)->groupBy(['est', 'afd'])->toArray();


        // check date yang sama  helper
        function countEntriesWithSameDate($data, $est)
        {
            $count = 0;
            $targetDate = null;
            $sameDateIds = [];

            foreach ($data[$est]['EST'] as $entry) {
                // Extract the year-month-date from the "datetime" value
                $dateTime = explode(" ", $entry['datetime']);
                $date = $dateTime[0]; // "2023-01-10"

                // If it's the first entry, set the target date
                if ($targetDate === null) {
                    $targetDate = $date;
                }

                // Compare the date with the target date
                if ($date === $targetDate) {
                    $count++;
                    $sameDateIds[] = $entry['id']; // Add the ID to the list
                } else {
                    // If the dates are not the same, break the loop (since entries are sorted by date)
                    break;
                }
            }

            return [
                'count' => $count,
                'ids' => $sameDateIds,
            ];
        }

        // ngisi nilai defaull jika bulan kosong 
        function addMissingMonths(&$array1, $array2)
        {
            $monthsOrder = [
                "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
            ];

            foreach ($array1 as $location => &$locationData) {
                $existingMonths = array_keys($locationData);
                $missingMonths = array_diff($monthsOrder, $existingMonths);

                if (!empty($missingMonths)) {
                    foreach ($missingMonths as $month) {
                        $locationData[$month] = $array2[$location][$month];
                    }
                }

                // Sort the months in the desired order
                uksort($locationData, function ($a, $b) use ($monthsOrder) {
                    return array_search($a, $monthsOrder) - array_search($b, $monthsOrder);
                });
            }
        }
        // Sample data (replace this with your actual array)


        // Call the function for each "est" key and get the count
        $samedateCounts = [];

        foreach ($landscape as $estKey => $estValue) {
            $count = countEntriesWithSameDate($landscape, $estKey);
            $samedateCounts[$estKey] = $count;
        }


        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        // nilai default perbulan 
        $defaultNew = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {


                $defaultNew[$est['nama']][$month] = 0;
            }
        }


        // perhitungan perumahan 

        $dataPerBulan = [];
        foreach ($bulan as $month) {
            foreach ($emplacement as $estKey => $estValue) {
                foreach ($estValue['EST'] as $entry) {
                    // Get the month and year from the "datetime" field
                    $entryMonth = date('F', strtotime($entry['datetime']));
                    $year = date('Y', strtotime($entry['datetime']));

                    if ($entryMonth === $month) {
                        // Group the data by "nama" value
                        if (!isset($dataPerBulan[$month])) {
                            $dataPerBulan[$month] = [];
                        }

                        // Find the corresponding "nama" value from the "queryEste" array
                        $nama = '';
                        foreach ($queryEste as $namaData) {
                            if ($namaData['est'] === $estKey) {
                                $nama = $namaData['nama'];
                                break;
                            }
                        }

                        // Handle keys that should not have the "-EST" suffix
                        $exceptKeys = ['REG-I', 'TC', 'SRS', 'SR', 'SLM', 'SGM', 'SKM', 'SYM', 'NBM'];
                        $keyToAdd = in_array($estKey, $exceptKeys) ? $estKey : $estKey . '-EST';

                        // Group the data by "nama" value and "est" key
                        if (!isset($dataPerBulan[$month][$nama][$keyToAdd])) {
                            $dataPerBulan[$month][$nama][$keyToAdd] = [];
                        }

                        // Add the data to the result array
                        $dataPerBulan[$month][$nama][$keyToAdd][] = $entry;
                    }
                }
            }

            // Check if the month has no data and set its value to 0
            if (!isset($dataPerBulan[$month])) {
                $dataPerBulan[$month] = 0;
            }
        }

        function groupArrayByDatetime($data)
        {
            $groupedArray = [];

            foreach ($data as $bulan => $locations) {
                if (is_array($locations)) {
                    foreach ($locations as $location => $estAfdData) {
                        foreach ($estAfdData as $estAfd => $items) {
                            $groupedItems = [];

                            foreach ($items as $item) {
                                $key = $item['est'] . '-' . $item['afd'] . '-' . $item['petugas'] . '-' . $item['pendamping'] . '-' . substr($item['datetime'], 0, 10);

                                if (!isset($groupedItems[$key])) {
                                    $groupedItems[$key] = $item;
                                } else {
                                    $existingItem = $groupedItems[$key];
                                    $index = 1;

                                    // Find the next available index
                                    while (isset($existingItem['nilai' . $index])) {
                                        $index++;
                                    }

                                    // Set the new index for the current item
                                    foreach ($item as $field => $value) {
                                        $existingItem[$field . $index] = $value;
                                    }

                                    $groupedItems[$key] = $existingItem;
                                }
                            }

                            $groupedArray[$bulan][$location][$estAfd] = array_values($groupedItems);
                        }
                    }
                } else {
                    $groupedArray[$bulan] = 0;
                }
            }

            return $groupedArray;
        }

        $groupedArray = groupArrayByDatetime($dataPerBulan);

        // dd($groupedArray, $dataPerBulan);
        // $hitungRmh = array();

        foreach ($groupedArray as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key2 => $value3) {
                            if (is_array($value3)) {
                                foreach ($value3 as $key3 => $value4) {
                                    $avg_nilai = 0;
                                    if (is_array($value4)) {

                                        $totalNilai = 0;
                                        $nilaiKeys = [];

                                        foreach ($value4 as $innerKey => $innerValue) {
                                            if (strpos($innerKey, 'nilai') === 0) {
                                                $nilaiKeys[] = $innerKey;
                                                $nilaiValues = array_map('intval', explode('$', $innerValue));
                                                $totalNilai += array_sum($nilaiValues);
                                            }
                                        }

                                        $dividen = 0;
                                        foreach ($value4 as $key4 => $item) {
                                            if (strpos($key4, 'nilai') === 0) {
                                                $dividen++;
                                            }
                                        }

                                        if ($dividen != 1) {
                                            $avg_nilai = round($totalNilai / $dividen, 2);
                                        } else {
                                            $avg_nilai = $totalNilai;
                                        }

                                        $date = $value4['datetime'];
                                        $yearMonth = date('Y-m-d', strtotime($date));

                                        $hitungRmh[$key][$key1][$key2][$key3]['nilai_total'] = $avg_nilai;
                                        $hitungRmh[$key][$key1][$key2][$key3]['total_nilai'] = $totalNilai;
                                        $hitungRmh[$key][$key1][$key2][$key3]['dividen'] = $dividen;

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
        }





        // dd($hitungRmh);

        $dataPerBulan = array_fill_keys($bulan, 0);

        // Populate $dataPerBulan with actual data
        foreach ($bulan as $month) {
            if (isset($hitungRmh[$month])) {
                $dataPerBulan[$month] = $hitungRmh[$month];
            }
        }



        $resultArray = [];
        foreach ($dataPerBulan as $estate => $months) if (is_array($months)) {
            foreach ($months as $month => $afdelings) if (is_array($afdelings)) {
                foreach ($afdelings as $afdeling => $data) if (is_array($data)) {
                    $resultArray[$month][$estate][$afdeling] = $data;
                }
            }
        }





        $FinalArr_rumah = array();
        foreach ($resultArray as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_rumah[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    }
                }
            }
        }

        addMissingMonths($FinalArr_rumah, $defaultNew);
        // dd($FinalArr_rumah);

        // perhitungan landscape 

        $dataPerBulan_Ls = [];
        foreach ($bulan as $month) {
            foreach ($landscape as $estKey => $estValue) {
                foreach ($estValue['EST'] as $entry) {
                    // Get the month and year from the "datetime" field
                    $entryMonth = date('F', strtotime($entry['datetime']));
                    $year = date('Y', strtotime($entry['datetime']));

                    if ($entryMonth === $month) {
                        // Group the data by "nama" value
                        if (!isset($dataPerBulan_Ls[$month])) {
                            $dataPerBulan_Ls[$month] = [];
                        }

                        // Find the corresponding "nama" value from the "queryEste" array
                        $nama = '';
                        foreach ($queryEste as $namaData) {
                            if ($namaData['est'] === $estKey) {
                                $nama = $namaData['nama'];
                                break;
                            }
                        }

                        // Handle keys that should not have the "-EST" suffix
                        $exceptKeys = ['REG-I', 'TC', 'SRS', 'SR', 'SLM', 'SGM', 'SKM', 'SYM', 'NBM'];
                        $keyToAdd = in_array($estKey, $exceptKeys) ? $estKey : $estKey . '-EST';

                        // Group the data by "nama" value and "est" key
                        if (!isset($dataPerBulan_Ls[$month][$nama][$keyToAdd])) {
                            $dataPerBulan_Ls[$month][$nama][$keyToAdd] = [];
                        }

                        // Add the data to the result array
                        $dataPerBulan_Ls[$month][$nama][$keyToAdd][] = $entry;
                    }
                }
            }

            // Check if the month has no data and set its value to 0
            if (!isset($dataPerBulan_Ls[$month])) {
                $dataPerBulan_Ls[$month] = 0;
            }
        }

        $groupedArray_LS = groupArrayByDatetime($dataPerBulan_Ls);

        // dd($groupedArray_LS, $dataPerBulan_Ls);

        $hitungLandscape = array();

        foreach ($groupedArray_LS as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key2 => $value3) {
                            if (is_array($value3)) {
                                foreach ($value3 as $key3 => $value4) {
                                    $avg_nilai = 0;
                                    if (is_array($value4)) {

                                        $totalNilai = 0;
                                        $nilaiKeys = [];

                                        foreach ($value4 as $innerKey => $innerValue) {
                                            if (strpos($innerKey, 'nilai') === 0) {
                                                $nilaiKeys[] = $innerKey;
                                                $nilaiValues = array_map('intval', explode('$', $innerValue));
                                                $totalNilai += array_sum($nilaiValues);
                                            }
                                        }

                                        $dividen = 0;
                                        foreach ($value4 as $key4 => $item) {
                                            if (strpos($key4, 'nilai') === 0) {
                                                $dividen++;
                                            }
                                        }

                                        if ($dividen != 1) {
                                            $avg_nilai = round($totalNilai / $dividen, 2);
                                        } else {
                                            $avg_nilai = $totalNilai;
                                        }

                                        $date = $value4['datetime'];
                                        $yearMonth = date('Y-m-d', strtotime($date));

                                        $hitungLandscape[$key][$key1][$key2][$key3]['nilai_total_LP'] = $avg_nilai;
                                        $hitungLandscape[$key][$key1][$key2][$key3]['total_nilai'] = $totalNilai;
                                        $hitungLandscape[$key][$key1][$key2][$key3]['dividen'] = $dividen;

                                        $hitungLandscape[$key][$key1][$key2][$key3]['date'] = $yearMonth;
                                        $hitungLandscape[$key][$key1][$key2][$key3]['est_LP'] = $value4['est'];
                                        $hitungLandscape[$key][$key1][$key2][$key3]['afd_LP'] = $value4['afd'];
                                        $hitungLandscape[$key][$key1][$key2][$key3]['petugas_LP'] = $value4['petugas'];
                                        $hitungLandscape[$key][$key1][$key2][$key3]['pendamping_LP'] = $value4['pendamping'];
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
        }


        // dd($hitungLandscape);
        $dataPerBulan_Ls = array_fill_keys($bulan, 0);
        foreach ($bulan as $month) {
            if (isset($hitungLandscape[$month])) {
                $dataPerBulan_Ls[$month] = $hitungLandscape[$month];
            }
        }


        $resultArray_Ls = [];
        foreach ($dataPerBulan_Ls as $estate => $months) if (is_array($months)) {
            foreach ($months as $month => $afdelings) if (is_array($afdelings)) {
                foreach ($afdelings as $afdeling => $data) if (is_array($data)) {
                    $resultArray_Ls[$month][$estate][$afdeling] = $data;
                }
            }
        }






        $FinalArr_LS = array();
        foreach ($resultArray_Ls as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_LS[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    }
                }
            }
        }

        addMissingMonths($FinalArr_LS, $defaultNew);

        // dd($FinalArr_LS);
        //perhitungan lingkungan

        $dataPerBulan_LK = [];
        foreach ($bulan as $month) {
            foreach ($lingkungan as $estKey => $estValue) {
                foreach ($estValue['EST'] as $entry) {
                    // Get the month and year from the "datetime" field
                    $entryMonth = date('F', strtotime($entry['datetime']));
                    $year = date('Y', strtotime($entry['datetime']));

                    if ($entryMonth === $month) {
                        // Group the data by "nama" value
                        if (!isset($dataPerBulan_LK[$month])) {
                            $dataPerBulan_LK[$month] = [];
                        }

                        // Find the corresponding "nama" value from the "queryEste" array
                        $nama = '';
                        foreach ($queryEste as $namaData) {
                            if ($namaData['est'] === $estKey) {
                                $nama = $namaData['nama'];
                                break;
                            }
                        }

                        // Handle keys that should not have the "-EST" suffix
                        $exceptKeys = ['REG-I', 'TC', 'SRS', 'SR', 'SLM', 'SGM', 'SKM', 'SYM', 'NBM'];
                        $keyToAdd = in_array($estKey, $exceptKeys) ? $estKey : $estKey . '-EST';

                        // Group the data by "nama" value and "est" key
                        if (!isset($dataPerBulan_LK[$month][$nama][$keyToAdd])) {
                            $dataPerBulan_LK[$month][$nama][$keyToAdd] = [];
                        }

                        // Add the data to the result array
                        $dataPerBulan_LK[$month][$nama][$keyToAdd][] = $entry;
                    }
                }
            }

            // Check if the month has no data and set its value to 0
            if (!isset($dataPerBulan_LK[$month])) {
                $dataPerBulan_LK[$month] = 0;
            }
        }

        $groupedArray_LK = groupArrayByDatetime($dataPerBulan_LK);

        $hitungLingkungan = array();


        foreach ($groupedArray_LK as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key2 => $value3) {
                            if (is_array($value3)) {
                                foreach ($value3 as $key3 => $value4) {
                                    $avg_nilai = 0;
                                    if (is_array($value4)) {

                                        $totalNilai = 0;
                                        $nilaiKeys = [];

                                        foreach ($value4 as $innerKey => $innerValue) {
                                            if (strpos($innerKey, 'nilai') === 0) {
                                                $nilaiKeys[] = $innerKey;
                                                $nilaiValues = array_map('intval', explode('$', $innerValue));
                                                $totalNilai += array_sum($nilaiValues);
                                            }
                                        }

                                        $dividen = 0;
                                        foreach ($value4 as $key4 => $item) {
                                            if (strpos($key4, 'nilai') === 0) {
                                                $dividen++;
                                            }
                                        }

                                        if ($dividen != 1) {
                                            $avg_nilai = round($totalNilai / $dividen, 2);
                                        } else {
                                            $avg_nilai = $totalNilai;
                                        }

                                        $date = $value4['datetime'];
                                        $yearMonth = date('Y-m-d', strtotime($date));

                                        $hitungLingkungan[$key][$key1][$key2][$key3]['nilai_total_Lngkl'] = $avg_nilai;
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['total_nilai'] = $totalNilai;
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['dividen'] = $dividen;

                                        $hitungLingkungan[$key][$key1][$key2][$key3]['date'] = $yearMonth;
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['est_Lngkl'] = $value4['est'];
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['afd_Lngkl'] = $value4['afd'];
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['petugas_Lngkl'] = $value4['petugas'];
                                        $hitungLingkungan[$key][$key1][$key2][$key3]['pendamping_Lngkl'] = $value4['pendamping'];
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
        }

        $dataPerBulan_LK = array_fill_keys($bulan, 0);
        foreach ($bulan as $month) {
            if (isset($hitungLingkungan[$month])) {
                $dataPerBulan_LK[$month] = $hitungLingkungan[$month];
            }
        }


        $resultArray_LK = [];
        foreach ($dataPerBulan_LK as $estate => $months) if (is_array($months)) {
            foreach ($months as $month => $afdelings) if (is_array($afdelings)) {
                foreach ($afdelings as $afdeling => $data) if (is_array($data)) {
                    $resultArray_LK[$month][$estate][$afdeling] = $data;
                }
            }
        }






        $FinalArr_LK = array();
        foreach ($resultArray_LK as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                foreach ($value2 as $key3 => $value3) {
                    if (is_array($value3)) {
                        foreach ($value3 as $value4) {
                            $FinalArr_LK[$key1][$key2][$key3][$value4['date']] = $value4;
                        }
                    }
                }
            }
        }

        addMissingMonths($FinalArr_LK, $defaultNew);

        // dd($FinalArr_LK);

        // function mergeArray_est($arr1, $arr2, $arr3)
        // {
        //     foreach ($arr1 as $key => $value) {
        //         if (isset($arr2[$key])) {
        //             $arr1[$key] = array_merge_recursive($value, $arr2[$key]);
        //         }
        //         if (isset($arr3[$key])) {
        //             $arr1[$key] = mergeArraysRecursive_est($arr1[$key] ?? [], $arr3[$key]);
        //         }
        //     }

        //     return $arr1;
        // }


        // function mergeArraysRecursive_est($arr1, $arr2)
        // {
        //     foreach ($arr2 as $key => $value) {
        //         if (is_array($value)) {
        //             $arr1[$key] = mergeArraysRecursive_est($arr1[$key] ?? [], $value);
        //         } else {
        //             $arr1[$key] = $value ?? 0;
        //         }
        //     }

        //     return $arr1;
        // }
        // $result = mergeArray_est($FinalArr_rumah, $FinalArr_LK, $FinalArr_LS);

        function mergeArray_est($arr1, $arr2, $arr3)
        {
            $allKeys = array_unique(array_merge(array_keys($arr1), array_keys($arr2), array_keys($arr3)));
            $result = [];

            foreach ($allKeys as $key) {
                $result[$key] = mergeArraysRecursive_est($arr1[$key] ?? [], $arr2[$key] ?? [], $arr3[$key] ?? []);
            }

            return $result;
        }

        function mergeArraysRecursive_est(...$arrays)
        {
            $result = [];
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    foreach ($array as $key => $value) {
                        if (is_array($value)) {
                            $result[$key] = mergeArraysRecursive_est($result[$key] ?? [], $value);
                        } else {
                            $result[$key] = $value ?? 0;
                        }
                    }
                }
            }

            return $result;
        }


        $result = mergeArray_est($FinalArr_rumah, $FinalArr_LK, $FinalArr_LS);

        // dd($result);

        //  delete index with 0 value 
        foreach ($result as $key => $months) {
            foreach ($months as $month => $value) {
                if (is_array($value)) {
                    // Check if the value is an array
                    $filteredValues = array_filter($value, function ($item) {
                        return $item !== 0; // Filter out entries with value 0
                    });

                    // Reassign the filtered array to the original array
                    $result[$key][$month] = $filteredValues;
                }
            }
        }

        // dd($result);
        $queryAsisten =  DB::connection('mysql2')->Table('asisten_qc')->get();

        $queryAsisten = json_decode($queryAsisten, true);
        // Now $result will have the entries with value 0 removed from nested arrays
        $final_result = array();

        foreach ($result as $key => $value) {
            foreach ($value as $key1 => $value1) if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) if (is_array($value2)) {
                    $inc = 1;
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            // dd($value3);
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

                        // dd($value1);
                    }
                }
            } else {

                $final_result[$key][$key1] = $value1;
            }
        }

        // dd($final_result);


        $avarage = array();

        foreach ($final_result as $estate => $months) {
            $countMth = 0; // Initialize the month count with zero
            $est_avg = 0;
            $avg_tod = 0;
            foreach ($months as $month => $value) {
                if (is_array($value)) {
                    // If the month has data, increase the count
                    $countMth++;
                    $total_avg = 0;
                    foreach ($value as $key2 => $value2) {
                        $count = count($value2);
                        $total_val = 0;
                        $avg = 0;
                        foreach ($value2 as $key3 => $value3) {
                            // dd($value3);
                            $total_val += $value3['skor_total'];
                        }
                        $avg = round($total_val / $count, 1);

                        $avarage[$estate][$month][$key2]['test_tod'] = $count;
                        $avarage[$estate][$month][$key2]['total_nilai'] = $total_val;
                        $avarage[$estate][$month][$key2]['est'] = $value3['est'];
                        $avarage[$estate][$month][$key2]['afd'] = $value3['afd'];
                        $avarage[$estate][$month][$key2]['rata_rata'] = $avg;

                        $total_avg += $avg;
                    }

                    $avarage[$estate][$month]['tot_avg'] = $total_avg;

                    $est_avg += $total_avg;
                }
            }

            $avg_tod = round($est_avg / $countMth, 1);
            $avarage[$estate]['tot_avg'] = $est_avg;
            $avarage[$estate]['bulan'] = $countMth;
            $avarage[$estate]['est'] = $value3['est'];
            $avarage[$estate]['afd'] = $value3['afd'];
            $avarage[$estate]['avg'] = $avg_tod;
        }
        // "est" => "BKE"
        // "afd" => "BKE-EST"
        // "avg" => 28.5
        // dd($avarage);
        $extractedData = [];
        foreach ($avarage as $estate => $data) {
            $extractedData[$estate]['est'] = $data['est'];
            $extractedData[$estate]['afd'] = $data['afd'];
            $extractedData[$estate]['avg'] = $data['avg'];
        }


        // Now $avarage array contains the counts of months with data for each estate

        // dd($avarage, $extractedData);
        $resultArray = [];

        // Loop through the original array
        foreach ($final_result as $estate => $months) {
            // Initialize the data for the estate and afdeling
            $resultArray[$estate] = [];

            foreach ($months as $month => $afdelings) if (is_array($afdelings)) {
                // Initialize the data for the afdeling
                foreach ($afdelings as $afdeling => $data) {
                    $resultArray[$estate][$afdeling][$month] = $data;
                }

                // Handle the default months with value 0
                $monthsList = [
                    "January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];

                foreach ($monthsList as $defaultMonth) {
                    if (!isset($resultArray[$estate][$afdeling][$defaultMonth])) {
                        $resultArray[$estate][$afdeling][$defaultMonth] = 0;
                    }
                }
            }
        }



        // dd($resultArray);


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
                                'date' => '-',
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

        // dd($get_cell);

        $max_visit = array();
        foreach ($get_cell as $month => $monthData) {
            $max_visitEst = 0;
            foreach ($monthData as $location => $locationData) {
                $maxVisit = 0;
                foreach ($locationData as $visitData) {
                    // Check if the current visit value is greater than the current maximum visit
                    if (isset($visitData['visit']) && $visitData['visit'] > $maxVisit) {
                        // Update the maximum visit for this location
                        $maxVisit = $visitData['visit'];
                    }
                }
                // Add the maximum visit for this location to the $max_visit array
                $max_visit[$month][$location]['max_visit'] = $maxVisit;

                // Calculate the overall maximum visit for the entire array
                if ($maxVisit > $max_visitEst) {
                    $max_visitEst = $maxVisit;
                }
            }
            // Add the overall maximum visit for this month to the $max_visit array
            $max_visit[$month]['max_visitEst'] = $max_visitEst;
        }

        $header_cell = array();
        foreach ($max_visit as $key => $value) {
            $header_cell[$key] = $value['max_visitEst'];
        }

        // dd($header_cell);

        function createEmptyVisit2($visitNumber)
        {
            return [
                "skor_total" => 0,
                "visit" => $visitNumber,
                "est" => "Kenambui", // Replace with the appropriate value
                "afd" => "OB", // Replace with the appropriate value
                "asisten" => "-",
                "date" => "-",
            ];
        }


        foreach ($resultArray as $key1 => &$level1) {
            foreach ($level1 as $key2 => &$level2) {
                foreach ($level2 as $month => &$visits) {
                    if (isset($header_cell[$month])) {
                        $requiredVisits = $header_cell[$month];
                        $currentVisits = count($visits);

                        // Add empty visits if required
                        for ($i = $currentVisits + 1; $i <= $requiredVisits; $i++) {
                            $visits["visit" . $i] = createEmptyVisit2($i);
                        }
                    }
                }
            }
        }

        // dd($resultArray);
        $sum_header = [
            "head" => array_sum($header_cell) + 2,
        ];
        // dd($header_cell, $sum_header);

        // dd($resultArray);
        // Return a JSON response
        $arrView = array();

        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;
        $arrView['afd_rekap'] =  $resultArray;
        $arrView['header_cell'] =  $header_cell;
        $arrView['header_head'] =  $sum_header;
        $arrView['rata_rata'] =  $extractedData;


        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }

    public function detailEmplashmend($est, $afd, $date)
    {


        $emplacement = DB::connection('mysql2')->table('perumahan')
            ->select(
                "perumahan.*",
                DB::raw('DATE_FORMAT(perumahan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(perumahan.datetime, "%Y") as tahun'),
            )
            ->where('perumahan.datetime', 'like', '%' . $date . '%')
            ->where('perumahan.est', $est)
            ->where('perumahan.afd', 'EST')
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $emplacement = json_decode(json_encode($emplacement), true); // Convert the collection to an array
        $emplacement = collect($emplacement)->groupBy(['est', 'afd'])->toArray();

        // dd($emplacement);
        $lingkungan = DB::connection('mysql2')->table('lingkungan')
            ->select(
                "lingkungan.*",
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(lingkungan.datetime, "%Y") as tahun'),
            )
            ->where('lingkungan.datetime', 'like', '%' . $date . '%')
            ->where('lingkungan.est', $est)
            ->where('lingkungan.afd', 'EST')
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
            ->where('landscape.datetime', 'like', '%' . $date . '%')
            ->where('landscape.est', $est)
            ->where('landscape.afd', 'EST')
            ->orderBy('est', 'asc')
            ->orderBy('afd', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();

        $landscape = json_decode(json_encode($landscape), true); // Convert the collection to an array
        $landscape = collect($landscape)->groupBy(['est', 'afd'])->toArray();



        // dd($emplacement, $landscape, $lingkungan);

        $hitungRmh = array();
        foreach ($emplacement as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungRmh[$key][$key1] = [];

                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            $sumNilai = isset($value3['nilai']) ? array_sum(array_map('intval', explode('$', $value3['nilai']))) : 0;
                            // Store the sum in the "nilai_total" key of the corresponding index
                            $date = $value3['datetime'];
                            // Get the year and month from the datetime
                            $yearMonth = date('Y-m-d', strtotime($date));

                            $foto_temuan = explode('$', $value3['foto_temuan']);
                            $kom_temuan = explode('$', $value3['komentar_temuan']);
                            $nilai = explode('$', $value3['nilai']);
                            $komentar = explode('$', $value3['komentar']);

                            unset($value3['foto_temuan']);
                            unset($value3['komentar_temuan']);
                            unset($value3['nilai']);
                            unset($value3['komentar']);

                            $hitungRmh[$key][$key1][$key2] = array_merge($value3, [
                                'nilai_total_Lngkl' => $sumNilai,
                                'date' => $yearMonth,
                                'est_afd' => $value3['est'] . '_' . $value3['afd'],
                            ]);

                            foreach ($foto_temuan as $i => $foto) {
                                // Create new keys for each exploded value
                                $hitungRmh[$key][$key1][$key2]['foto_temuan_rmh' . ($i + 1)] = $foto;
                            }

                            foreach ($kom_temuan as $i => $komn) {
                                // Create new keys for each exploded value
                                $hitungRmh[$key][$key1][$key2]['komentar_temuan_rmh' . ($i + 1)] = $komn;
                            }

                            foreach ($nilai as $i => $nilai) {
                                // Create new keys for each exploded value
                                $hitungRmh[$key][$key1][$key2]['nilai_rmh' . ($i + 1)] = $nilai;
                            }

                            foreach ($komentar as $i => $komens) {
                                // Create new keys for each exploded value
                                $hitungRmh[$key][$key1][$key2]['komentar_rmh' . ($i + 1)] = $komens;
                            }
                        }
                    }
                }
            }
        }

        // dd($emplashmenOri, $hitungRmh);


        $hitungLandscape = array();
        foreach ($landscape as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungLandscape[$key][$key1] = [];

                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            $sumNilai = isset($value3['nilai']) ? array_sum(array_map('intval', explode('$', $value3['nilai']))) : 0;
                            // Store the sum in the "nilai_total" key of the corresponding index
                            $date = $value3['datetime'];
                            // Get the year and month from the datetime
                            $yearMonth = date('Y-m-d', strtotime($date));

                            $foto_temuan = explode('$', $value3['foto_temuan']);
                            $kom_temuan = explode('$', $value3['komentar_temuan']);
                            $nilai = explode('$', $value3['nilai']);
                            $komentar = explode('$', $value3['komentar']);

                            unset($value3['foto_temuan']);
                            unset($value3['komentar_temuan']);
                            unset($value3['nilai']);
                            unset($value3['komentar']);

                            $hitungLandscape[$key][$key1][$key2] = array_merge($value3, [
                                'nilai_total_Lngkl' => $sumNilai,
                                'date' => $yearMonth,
                                'est_afd' => $value3['est'] . '_' . $value3['afd'],
                            ]);

                            foreach ($foto_temuan as $i => $foto) {
                                // Create new keys for each exploded value
                                $hitungLandscape[$key][$key1][$key2]['foto_temuan_ls' . ($i + 1)] = $foto;
                            }

                            foreach ($kom_temuan as $i => $komn) {
                                // Create new keys for each exploded value
                                $hitungLandscape[$key][$key1][$key2]['komentar_temuan_ls' . ($i + 1)] = $komn;
                            }

                            foreach ($nilai as $i => $nilai) {
                                // Create new keys for each exploded value
                                $hitungLandscape[$key][$key1][$key2]['nilai_ls' . ($i + 1)] = $nilai;
                            }

                            foreach ($komentar as $i => $komens) {
                                // Create new keys for each exploded value
                                $hitungLandscape[$key][$key1][$key2]['komentar_ls' . ($i + 1)] = $komens;
                            }
                        }
                    }
                }
            }
        }



        $hitungLingkungan = array();

        foreach ($lingkungan as $key => $value) {
            foreach ($value as $key1 => $value2) {
                // Initialize the "nilai_total" for each month to 0
                $hitungLingkungan[$key][$key1] = [];

                if (is_array($value2)) {
                    foreach ($value2 as $key2 => $value3) {
                        if (is_array($value3)) {
                            $sumNilai = isset($value3['nilai']) ? array_sum(array_map('intval', explode('$', $value3['nilai']))) : 0;
                            // Store the sum in the "nilai_total" key of the corresponding index
                            $date = $value3['datetime'];
                            // Get the year and month from the datetime
                            $yearMonth = date('Y-m-d', strtotime($date));

                            $foto_temuan = explode('$', $value3['foto_temuan']);
                            $kom_temuan = explode('$', $value3['komentar_temuan']);
                            $nilai = explode('$', $value3['nilai']);
                            $komentar = explode('$', $value3['komentar']);

                            unset($value3['foto_temuan']);
                            unset($value3['komentar_temuan']);
                            unset($value3['nilai']);
                            unset($value3['komentar']);

                            $hitungLingkungan[$key][$key1][$key2] = array_merge($value3, [
                                'nilai_total_Lngkl' => $sumNilai,
                                'date' => $yearMonth,
                                'est_afd' => $value3['est'] . '_' . $value3['afd'],
                            ]);

                            foreach ($foto_temuan as $i => $foto) {
                                // Create new keys for each exploded value
                                $hitungLingkungan[$key][$key1][$key2]['foto_temuan_ll' . ($i + 1)] = $foto;
                            }

                            foreach ($kom_temuan as $i => $komn) {
                                // Create new keys for each exploded value
                                $hitungLingkungan[$key][$key1][$key2]['komentar_temuan_ll' . ($i + 1)] = $komn;
                            }

                            foreach ($nilai as $i => $nilai) {
                                // Create new keys for each exploded value
                                $hitungLingkungan[$key][$key1][$key2]['nilai_ll' . ($i + 1)] = $nilai;
                            }

                            foreach ($komentar as $i => $komens) {
                                // Create new keys for each exploded value
                                $hitungLingkungan[$key][$key1][$key2]['komentar_ll' . ($i + 1)] = $komens;
                            }
                        }
                    }
                }
            }
        }

        // dd($hitungRmh, $hitungLandscape, $hitungLingkungan);

        $new_Lscp = array();

        foreach ($hitungLandscape as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $index = 0; // Initialize index here
                foreach ($value1 as $key2 => $value2) {
                    $index++; // Increment the index inside the loop

                    // Group data based on "rmhX" keys
                    $number = 1;
                    $groupedData = array();
                    $numberOfFotoTemuanLsKeys = 0; // Initialize the counter for foto_temuan_ls keys
                    foreach ($value2 as $key3 => $value3) {
                        if (strpos($key3, 'foto_temuan_ls') === 0) {
                            $numberOfFotoTemuanLsKeys++;
                        }
                    }

                    // dd($numberOfFotoTemuanLsKeys);
                    for ($i = 1; $i <= $numberOfFotoTemuanLsKeys; $i++) {
                        $number++;
                        $groupedData[] = array(
                            "foto_temuan_ls" => $value2["foto_temuan_ls" . $i],
                            "komentar_temuan_ls" => $value2["komentar_temuan_ls" . $i],
                            "komentar_ls" => $value2["komentar_ls" . $i],
                            "title" => $value2["est"] . "-" . $value2["afd"],

                        );
                    }

                    // Now, use the incremented index to create new arrays with desired keys and values
                    $new_Lscp[$key][$key1][$key2][$index] = $groupedData;
                }
            }
        }

        // dd($new_Lscp);
        $new_Rmh = array();

        foreach ($hitungRmh as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $index = 0; // Initialize index here
                foreach ($value1 as $key2 => $value2) {
                    $index++; // Increment the index inside the loop

                    // Group data based on "rmhX" keys
                    $number = 1;
                    $groupedData = array();
                    $numberOfFotoTemuanLsKeys = 0; // Initialize the counter for foto_temuan_ls keys
                    foreach ($value2 as $key3 => $value3) {
                        if (strpos($key3, 'foto_temuan_rmh') === 0) {
                            $numberOfFotoTemuanLsKeys++;
                        }
                    }

                    for ($i = 1; $i <= $numberOfFotoTemuanLsKeys; $i++) {
                        $number++;
                        $groupedData[] = array(
                            "foto_temuan_rmh" => $value2["foto_temuan_rmh" . $i],
                            "komentar_temuan_rmh" => $value2["komentar_temuan_rmh" . $i],
                            "komentar_rmh" => $value2["komentar_rmh" . $i],
                            "title" => $value2["est"] . "-" . $value2["afd"],

                        );
                    }

                    // Now, use the incremented index to create new arrays with desired keys and values
                    $new_Rmh[$key][$key1][$key2][$index] = $groupedData;
                }
            }
        }
        $new_lkngan = array();

        foreach ($hitungLingkungan as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $index = 0; // Initialize index here
                foreach ($value1 as $key2 => $value2) {
                    $index++; // Increment the index inside the loop

                    // Group data based on "rmhX" keys
                    $number = 1;
                    $groupedData = array();
                    $numberOfFotoTemuanLsKeys = 0; // Initialize the counter for foto_temuan_ls keys
                    foreach ($value2 as $key3 => $value3) {
                        if (strpos($key3, 'foto_temuan_ll') === 0) {
                            $numberOfFotoTemuanLsKeys++;
                        }
                    }
                    // dd($numberOfFotoTemuanLsKeys);
                    for ($i = 1; $i <= $numberOfFotoTemuanLsKeys; $i++) {
                        $number++;
                        $groupedData[] = array(
                            "foto_temuan_ll" => $value2["foto_temuan_ll" . $i],
                            "komentar_temuan_ll" => $value2["komentar_temuan_ll" . $i],
                            "komentar_ll" => $value2["komentar_ll" . $i],
                            "title" => $value2["est"] . "-" . $value2["afd"],

                        );
                    }

                    // Now, use the incremented index to create new arrays with desired keys and values
                    $new_lkngan[$key][$key1][$key2][$index] = $groupedData;
                }
            }
        }


        // Output the result

        // dd($new_lkngan, $hitungLingkungan);
        $new_Rmh_result = array();

        foreach ($new_Rmh as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $merged_values = array();
                foreach ($value1 as $key2 => $value2) {
                    // Merge all the nested arrays into a single array
                    $merged_values = array_merge($merged_values, $value2);
                }
                // Add the merged array to the result
                $new_Rmh_result[$key][$key1] = $merged_values;
            }
            // Unset the unnecessary level of the array
            $new_Rmh_result[$key][$key1] = $new_Rmh_result[$key][$key1];
        }

        $new_Lscp_result = array();

        foreach ($new_Lscp as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $merged_values = array();
                foreach ($value1 as $key2 => $value2) {
                    // Merge all the nested arrays into a single array
                    $merged_values = array_merge($merged_values, $value2);
                }
                // Add the merged array to the result
                $new_Lscp_result[$key][$key1] = $merged_values;
            }
            // Unset the unnecessary level of the array
            $new_Lscp_result[$key][$key1] = $new_Lscp_result[$key][$key1];
        }
        $new_lkngan_result = array();

        foreach ($new_lkngan as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $merged_values = array();
                foreach ($value1 as $key2 => $value2) {
                    // Merge all the nested arrays into a single array
                    $merged_values = array_merge($merged_values, $value2);
                }
                // Add the merged array to the result
                $new_lkngan_result[$key][$key1] = $merged_values;
            }
            // Unset the unnecessary level of the array
            $new_lkngan_result[$key][$key1] = $new_lkngan_result[$key][$key1];
        }

        // Output the result

        // Loop through the outermost array and merge the nested arrays for each key dynamically
        foreach ($new_Rmh_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray = array_merge(...$value2);
                $new_Rmh_result[$key1][$key2] = $mergedArray;
            }
        }
        foreach ($new_Lscp_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray = array_merge(...$value2);
                $new_Lscp_result[$key1][$key2] = $mergedArray;
            }
        }
        foreach ($new_lkngan_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray = array_merge(...$value2);
                $new_lkngan_result[$key1][$key2] = $mergedArray;
            }
        }

        // pagination perumahan 
        // dd($new_Rmh_result);
        $mergedArray = [];
        foreach ($new_Rmh_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray = array_merge($mergedArray, $value2);
            }
        }
        $mergedArray_lscp = [];
        foreach ($new_Lscp_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray_lscp = array_merge($mergedArray_lscp, $value2);
            }
        }
        $mergedArray_lkngn = [];
        foreach ($new_lkngan_result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $mergedArray_lkngn = array_merge($mergedArray_lkngn, $value2);
            }
        }

        // Convert the flat array into a Laravel collection
        $collection = new Collection($mergedArray);
        $collection_ls = new Collection($mergedArray_lscp);
        $collection_lkngn = new Collection($mergedArray_lkngn);

        // Paginate the collection with 8 items per page
        $perPage = 8;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($collection), $perPage);
        $paginatedItems->setPath(request()->url());


        $currentPageItems_ls = $collection_ls->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems_lscp = new LengthAwarePaginator($currentPageItems_ls, count($collection_ls), $perPage);
        $paginatedItems_lscp->setPath(request()->url());

        $currentPageItems_lkngn = $collection_lkngn->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems_lkngan = new LengthAwarePaginator($currentPageItems_lkngn, count($collection_lkngn), $perPage);
        $paginatedItems_lkngan->setPath(request()->url());

        // dd($paginatedItems_lscp);

        // Send the paginated collection to the view
        $arrView = array();
        $arrView['est'] =  $est;
        $arrView['afd'] =  $afd;
        $arrView['Landscape'] =  $paginatedItems_lscp;
        $arrView['lingkungan'] =  $paginatedItems_lkngan;
        $arrView['tanggal'] =  $date;
        $arrView['li'] =  $hitungRmh;
        $arrView['Perumahan'] = $paginatedItems;

        return view('datailEmplashmend', $arrView);
    }
}
