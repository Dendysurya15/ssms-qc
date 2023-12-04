<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Arr;
use Nette\Utils\DateTime;
use PhpParser\Node\Stmt\Foreach_;
use Termwind\Components\Dd;
use Symfony\Component\VarDumper\VarDumper;

require "../app/helpers.php";

class SidaktphController extends Controller
{
    //
    public $search;
    public function index(Request $request)
    {
        $queryEst = DB::connection('mysql2')->table('estate')->whereIn('wil', [1, 2, 3])->where('estate.est', '!=', 'CWS1')->where('estate.est', '!=', 'PLASMA')->pluck('est');

        $queryEste = DB::connection('mysql2')->table('estate')->whereIn('wil', [1, 2, 3])->where('estate.est', '!=', 'CWS1')->where('estate.est', '!=', 'PLASMA')->get();
        $queryEste = $queryEste->groupBy(function ($item) {
            return $item->wil;
        });

        $queryEste = json_decode($queryEste, true);

        // dd($queryEste);
        $queryAfd = DB::connection('mysql2')->Table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'

            ) //buat mengambil data di estate db dan willayah db

            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->where('estate.est', '!=', 'CWS1')->where('estate.est', '!=', 'PLASMA')
            ->get();

        $queryAfd = json_decode($queryAfd, true);
        // dd($queryAfd);



        $querySidak = DB::connection('mysql2')->table('sidak_tph')
            ->whereBetween('sidak_tph.datetime', ['2023-01-22', '2023-01-29'])
            ->get();
        $querySidak = json_decode($querySidak, true);

        $dataAfdEst = array();
        // menyimpan array nested dari  wil -> est -> afd
        foreach ($queryEste as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($queryAfd as $key3 => $value3) {
                    if ($value2['est'] == $value3['est']) {
                        foreach ($querySidak as $key4 => $value4) {
                            if (($value2['est'] == $value4['est']) && ($value3['nama'] == $value4['afd'])) {
                                if (!isset($dataAfdEst[$value2['est']][$value3['nama']])) {
                                    $dataAfdEst[$value2['est']][$value3['nama']] = array();
                                }
                                $dataAfdEst[$value2['est']][$value3['nama']][] = $value4;
                            }
                        }
                    }
                }
            }
        }

        foreach ($dataAfdEst as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (empty($value2)) {
                    unset($dataAfdEst[$key][$key2]);
                }
            }
            if (empty($dataAfdEst[$key])) {
                unset($dataAfdEst[$key]);
            }
        }

        // dd($dataSkorAkhirPerWil);
        $queryWill = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [1])
            ->pluck('nama');
        $queryTph = DB::connection('mysql2')
            ->table('sidak_tph')
            ->orderBy('datetime', 'desc')
            ->groupBy(DB::raw('YEAR(datetime)'))
            ->pluck('datetime')->toArray();

        $optYear = array();
        foreach ($queryTph as $datetime) {
            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $datetime);
            array_push($optYear, $carbon->format('Y'));
        }
        $listMonth = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'];

        $queryEstate = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', '1')
            ->get();
        $queryEstate = json_decode($queryEstate, true);

        $dataSkorPlas = array();
        foreach ($queryEstate as $value1) {
            $querySidaks = DB::connection('mysql2')->table('sidak_tph')
                ->select("sidak_tph.*")
                ->where('est', $value1['est'])
                ->where('datetime', 'like', '%' . '2023-04' . '%')
                ->whereIn('est', ['Plasma1', 'Plasma2', 'Plasma3'])
                ->orderBy('afd', 'asc')
                ->get();
            $DataEstate = $querySidaks->groupBy(['est', 'afd']);
            // dd($DataEstate);
            $DataEstate = json_decode($DataEstate, true);

            foreach ($DataEstate as $key => $value) {
                $luas_ha_est = 0;
                $jml_blok_est = 0;
                $sum_bt_tph_est = 0;
                $sum_bt_jln_est = 0;
                $sum_bt_bin_est = 0;
                $sum_krg_est = 0;
                $sumBuah_est = 0;
                $sumRst_est = 0;
                foreach ($value as $key2 => $value2) {
                    $luas_ha = 0;
                    $jml_blok = 0;
                    $sum_bt_tph = 0;
                    $sum_bt_jln = 0;
                    $sum_bt_bin = 0;
                    $sum_krg = 0;
                    $sumBuah = 0;
                    $sumRst = 0;
                    $listBlokPerAfd = array();
                    foreach ($value2 as $key3 => $value3) {
                        if (!in_array($value3['est'] . ' ' . $value3['afd'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $value3['est'] . ' ' . $value3['afd'] . ' ' . $value3['blok'];
                            $luas_ha += $value3['luas'];
                        }
                        $jml_blok = count($listBlokPerAfd);
                        $sum_bt_tph += $value3['bt_tph'];
                        $sum_bt_jln += $value3['bt_jalan'];
                        $sum_bt_bin += $value3['bt_bin'];
                        $sum_krg += $value3['jum_karung'];
                        $sumBuah += $value3['buah_tinggal'];
                        $sumRst += $value3['restan_unreported'];
                    }
                    $luas_ha_est += $luas_ha;
                    $jml_blok_est += $jml_blok;
                    $sum_bt_tph_est += $sum_bt_tph;
                    $sum_bt_jln_est += $sum_bt_jln;
                    $sum_bt_bin_est += $sum_bt_bin;
                    $sum_krg_est += $sum_krg;
                    $sumBuah_est += $sumBuah;
                    $sumRst_est += $sumRst;

                    $tot_bt = ($sum_bt_tph + $sum_bt_jln + $sum_bt_bin);
                    $dataSkorPlas[$value1['wil']][$key][$key2]['jml_blok'] = $jml_blok;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['luas_ha'] = $luas_ha;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['bt_tph'] = $sum_bt_tph;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['bt_jln'] = $sum_bt_jln;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['bt_bin'] = $sum_bt_bin;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['tot_bt'] = $tot_bt;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['divBt'] = round($tot_bt / $jml_blok, 2);
                    $dataSkorPlas[$value1['wil']][$key][$key2]['skorBt'] = skor_bt_tph(round($tot_bt / $jml_blok, 2));
                    $dataSkorPlas[$value1['wil']][$key][$key2]['sum_krg'] = $sum_krg;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['divKrg'] = round($sum_krg / $jml_blok, 2);
                    $dataSkorPlas[$value1['wil']][$key][$key2]['skorKrg'] = skor_krg_tph(round($sum_krg / $jml_blok, 2));
                    $dataSkorPlas[$value1['wil']][$key][$key2]['sumBuah'] = $sumBuah;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['divBuah'] = round($sumBuah / $jml_blok, 2);
                    $dataSkorPlas[$value1['wil']][$key][$key2]['skorBuah'] = skor_buah_tph(round($sumBuah / $jml_blok, 2));
                    $dataSkorPlas[$value1['wil']][$key][$key2]['sumRst'] = $sumRst;
                    $dataSkorPlas[$value1['wil']][$key][$key2]['divRst'] = round($sumRst / $jml_blok, 2);
                    $dataSkorPlas[$value1['wil']][$key][$key2]['skorRst'] = skor_rst_tph(round($sumRst / $jml_blok, 2));
                    $dataSkorPlas[$value1['wil']][$key][$key2]['allSkor'] = skor_bt_tph(round($tot_bt / $jml_blok, 2)) + skor_krg_tph(round($sum_krg / $jml_blok, 2)) + skor_buah_tph(round($sumBuah / $jml_blok, 2)) + skor_rst_tph(round($sumRst / $jml_blok, 2));
                }
                $tot_bt_est = ($sum_bt_tph_est + $sum_bt_jln_est + $sum_bt_bin_est);
                $dataSkorPlas[$value1['wil']][$key]['jml_blok_est'] = $jml_blok_est;
                $dataSkorPlas[$value1['wil']][$key]['luas_ha_est'] = $luas_ha_est;
                $dataSkorPlas[$value1['wil']][$key]['bt_tph_est'] = $sum_bt_tph_est;
                $dataSkorPlas[$value1['wil']][$key]['bt_jln_est'] = $sum_bt_jln_est;
                $dataSkorPlas[$value1['wil']][$key]['bt_bin_est'] = $sum_bt_bin_est;
                $dataSkorPlas[$value1['wil']][$key]['tot_bt_est'] = $tot_bt_est;
                $dataSkorPlas[$value1['wil']][$key]['divBt_est'] = round($tot_bt_est / $jml_blok_est, 2);
                $dataSkorPlas[$value1['wil']][$key]['skorBt_est'] = skor_bt_tph(round($tot_bt_est / $jml_blok_est, 2));
                $dataSkorPlas[$value1['wil']][$key]['sum_krg_est'] = $sum_krg_est;
                $dataSkorPlas[$value1['wil']][$key]['divKrg_est'] = round($sum_krg_est / $jml_blok_est, 2);
                $dataSkorPlas[$value1['wil']][$key]['skorKrg_est'] = skor_krg_tph(round($sum_krg_est / $jml_blok_est, 2));
                $dataSkorPlas[$value1['wil']][$key]['sumBuah_est'] = $sumBuah_est;
                $dataSkorPlas[$value1['wil']][$key]['divBuah_est'] = round($sumBuah_est / $jml_blok_est, 2);
                $dataSkorPlas[$value1['wil']][$key]['skorBuah_est'] = skor_buah_tph(round($sumBuah_est / $jml_blok_est, 2));
                $dataSkorPlas[$value1['wil']][$key]['sumRst_est'] = $sumRst_est;
                $dataSkorPlas[$value1['wil']][$key]['divRst_est'] = round($sumRst_est / $jml_blok_est, 2);
                $dataSkorPlas[$value1['wil']][$key]['skorRst_est'] = skor_rst_tph(round($sumRst_est / $jml_blok_est, 2));
                $dataSkorPlas[$value1['wil']][$key]['allSkor_est'] = skor_bt_tph(round($tot_bt_est / $jml_blok_est, 2)) + skor_krg_tph(round($sum_krg_est / $jml_blok_est, 2)) + skor_buah_tph(round($sumBuah_est / $jml_blok_est, 2)) + skor_rst_tph(round($sumRst_est / $jml_blok_est, 2));
            }
        }
        // dd($dataSkorPlas);
        $optionREg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->whereNotIn('reg.id', [5])
            // ->where('wil.regional', 1)
            ->get();


        $optionREg = json_decode($optionREg, true);
        return view('dashboardtph', [
            'list_estate' => $queryEst,
            'list_wilayah' => $queryWill,
            'optYear' => $optYear,
            'list_month' => $listMonth,
            'option_reg' => $optionREg
        ]);
    }



    public function changeDataTph(Request $request)
    {
        $tanggal = $request->get('date');
        $regional = $request->get('regional');
        $ancakFA = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select("sidak_tph.*", DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal')) // Change the format to "%Y-%m-%d"
            ->where('sidak_tph.datetime', 'like', '%' . $tanggal . '%')
            ->orderBy('status', 'asc')
            ->get();

        $ancakFA = $ancakFA->groupBy(['est', 'afd', 'status', 'tanggal', 'blok']);
        $ancakFA = json_decode($ancakFA, true);

        $dateString = $tanggal;
        $dateParts = date_parse($dateString);
        $year = $dateParts['year'];
        $month = $dateParts['month'];

        $year = $year; // Replace with the desired year
        $month = $month;   // Replace with the desired month (September in this example)

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


        $result = [];

        // Iterate through the original array
        foreach ($ancakFA as $mainKey => $mainValue) {
            $result[$mainKey] = [];

            foreach ($mainValue as $subKey => $subValue) {
                $result[$mainKey][$subKey] = [];

                foreach ($subValue as $dateKey => $dateValue) {
                    // Remove 'H+' prefix if it exists
                    $numericIndex = is_numeric($dateKey) ? $dateKey : (strpos($dateKey, 'H+') === 0 ? substr($dateKey, 2) : $dateKey);

                    if (!isset($result[$mainKey][$subKey][$numericIndex])) {
                        $result[$mainKey][$subKey][$numericIndex] = [];
                    }

                    foreach ($dateValue as $statusKey => $statusValue) {
                        // Handle 'H+' prefix in status
                        $statusIndex = is_numeric($statusKey) ? $statusKey : (strpos($statusKey, 'H+') === 0 ? substr($statusKey, 2) : $statusKey);

                        if (!isset($result[$mainKey][$subKey][$numericIndex][$statusIndex])) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex] = [];
                        }

                        foreach ($statusValue as $blokKey => $blokValue) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex][$blokKey] = $blokValue;
                        }
                    }
                }
            }
        }

        // result by statis week 
        $newResult = [];

        foreach ($result as $key => $value) {
            $newResult[$key] = [];

            foreach ($value as $estKey => $est) {
                $newResult[$key][$estKey] = [];

                foreach ($est as $statusKey => $status) {
                    $newResult[$key][$estKey][$statusKey] = [];

                    foreach ($weeks as $weekKey => $week) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $newResult[$key][$estKey][$statusKey]["week" . ($weekKey + 1)] = $newStatus;
                        }
                    }
                }
            }
        }

        // result by week status 
        $WeekStatus = [];

        foreach ($result as $key => $value) {
            $WeekStatus[$key] = [];

            foreach ($value as $estKey => $est) {
                $WeekStatus[$key][$estKey] = [];

                foreach ($weeks as $weekKey => $week) {
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



        $qrafd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $qrafd = json_decode($qrafd, true);
        $queryEstereg = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['PLASMA', 'SRE', 'LDE', 'SKE', 'CWS1', 'SRS'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regional)
            ->get();
        $queryEstereg = json_decode($queryEstereg, true);

        // dd($queryEstereg);
        $defaultNew = array();

        foreach ($queryEstereg as $est) {
            foreach ($qrafd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']] = 0;
                }
            }
        }



        foreach ($defaultNew as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($newResult as $dataKey => $dataValue) {

                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {

                            if ($dataEstKey == $monthKey) {
                                $defaultNew[$key][$monthKey] = $dataEstValue;
                            }
                        }
                    }
                }
            }
        }

        $defaultWeek = array();

        foreach ($queryEstereg as $est) {
            foreach ($qrafd as $afd) {
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




        $newDefaultWeek = [];

        foreach ($defaultWeek as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        foreach ($value1 as $subKey => $subValue) {
                            if (is_array($subValue)) {
                                // Check if both key 0 and key 1 exist
                                $hasKeyZero = isset($subValue[0]);
                                $hasKeyOne = isset($subValue[1]);

                                // Merge key 0 into key 1
                                if ($hasKeyZero && $hasKeyOne) {
                                    $subValue[1] = array_merge_recursive((array)$subValue[1], (array)$subValue[0]);
                                    unset($subValue[0]);
                                } elseif ($hasKeyZero && !$hasKeyOne) {
                                    // Create key 1 and merge key 0 into it
                                    $subValue[1] = $subValue[0];
                                    unset($subValue[0]);
                                }

                                // Check if keys 1 through 7 don't exist, add them with a default value of 0
                                for ($i = 1; $i <= 7; $i++) {
                                    if (!isset($subValue[$i])) {
                                        $subValue[$i] = 0;
                                    }
                                }

                                // Ensure key 8 exists, and if not, create it with a default value of an empty array
                                if (!isset($subValue[8])) {
                                    $subValue[8] = 0;
                                }

                                // Check if keys higher than 8 exist, merge them into index 8
                                for ($i = 9; $i <= 100; $i++) {
                                    if (isset($subValue[$i])) {
                                        $subValue[8] = array_merge_recursive((array)$subValue[8], (array)$subValue[$i]);
                                        unset($subValue[$i]);
                                    }
                                }
                            }
                            $newDefaultWeek[$key][$key1][$subKey] = $subValue;
                        }
                    } else {
                        // Check if $value1 is equal to 0 and add "week1" to "week5" keys
                        if ($value1 === 0) {
                            $newDefaultWeek[$key][$key1] = [];
                            for ($i = 1; $i <= 5; $i++) {
                                $weekKey = "week" . $i;
                                $newDefaultWeek[$key][$key1][$weekKey] = [];
                                for ($j = 1; $j <= 8; $j++) {
                                    $newDefaultWeek[$key][$key1][$weekKey][$j] = 0;
                                }
                            }
                        } else {
                            $newDefaultWeek[$key][$key1] = $value1;
                        }
                    }
                }
            } else {
                $newDefaultWeek[$key] = $value;
            }
        }
        // dd($newDefaultWeek['Plasma1']['WIL-III']);
        // dd($newDefaultWeek);

        function removeZeroFromDatetime2(&$array)
        {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => &$value2) {
                        if (is_array($value2)) {
                            foreach ($value2 as $key2 => &$value3) {
                                if (is_array($value3)) {
                                    foreach ($value3 as $key3 => &$value4) if (is_array($value4)) {
                                        foreach ($value4 as $key4 => $value5) {
                                            if ($key4 === 0 && $value5 === 0) {
                                                unset($value4[$key4]); // Unset the key 0 => 0 within the current nested array
                                            }
                                            removeZeroFromDatetime2($value4);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        removeZeroFromDatetime2($newDefaultWeek);

        function filterEmptyWeeks(&$array)
        {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    filterEmptyWeeks($value); // Recursively check nested arrays
                    if (empty($value) && $key !== 'week') {
                        unset($array[$key]);
                    }
                }
            }
        }

        // dd($defaultWeek);
        // Call the function on your array
        filterEmptyWeeks($defaultWeek);


        // dd($defaultWeek);
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
        // dd($newDefaultWeek['BKE']['OA']);

        // dd($newDefaultWeek['Plasma1']['WIL-III']);
        $newSidak = array();
        $asisten_qc = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();
        $asisten_qc = json_decode($asisten_qc, true);
        // dd($newDefaultWeek);
        $devest = 0;
        foreach ($newDefaultWeek as $key => $value) {
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

            foreach ($value as $key1 => $value2)  if (is_array($value2)) {

                $tot_afdscore = 0;
                $totskor_brd1 = 0;
                $totskor_janjang1 = 0;
                $total_skoreest = 0;
                foreach ($value2 as $key2 => $value3) {


                    $total_brondolan = 0;
                    $total_janjang = 0;
                    $tod_brd = 0;
                    $tod_jjg = 0;
                    $totskor_brd = 0;
                    $totskor_janjang = 0;
                    $tot_brdxm = 0;
                    $tod_janjangxm = 0;

                    foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                        $tph1 = 0;
                        $jalan1 = 0;
                        $bin1 = 0;
                        $karung1 = 0;
                        $buah1 = 0;
                        $restan1 = 0;

                        foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                            $tph = 0;
                            $jalan = 0;
                            $bin = 0;
                            $karung = 0;
                            $buah = 0;
                            $restan = 0;
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

                            $tph1 += $tph;
                            $jalan1 += $jalan;
                            $bin1 += $bin;
                            $karung1 += $karung;
                            $buah1 += $buah;
                            $restan1 += $restan;
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

                        $totskor_brd += $total_brondolan;
                        $totskor_janjang += $total_janjang;
                        $tot_brdxm += $tod_brd;
                        $tod_janjangxm += $tod_jjg;
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
                    }


                    $total_estkors = $totskor_brd + $totskor_janjang;
                    if ($total_estkors != 0) {
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

                    $totskor_brd1 += $totskor_brd;
                    $totskor_janjang1 += $totskor_janjang;
                    $total_skoreest += $total_skoreafd;
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


                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }

                $deviden = count($value2);

                $new_dvd = $dividen_x;
                $new_dvdest = $devidenEst_x;
                if ($new_dvd != 0) {
                    $tot_afdscore = round($total_skoreest / $new_dvd, 1);
                } else {
                    $tot_afdscore = 0;  # code...
                }

                // $newSidak[$key][$key1]['deviden'] = $deviden;
                $newSidak[$key][$key1]['total_score'] = $tot_afdscore;
                $newSidak[$key][$key1]['total_brd'] = $totskor_brd1;
                $newSidak[$key][$key1]['total_janjang'] = $totskor_janjang1;
                $newSidak[$key][$key1]['new_deviden'] = $new_dvd;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
                $newSidak[$key][$key1]['total_skor'] = $total_skoreest;
                $newSidak[$key][$key1]['est'] = $key;
                $newSidak[$key][$key1]['afd'] = $key1;
                $newSidak[$key][$key1]['devidenest'] = $devest;

                $tot_estAFd += $tot_afdscore;
                $new_dvdAfd += $new_dvd;
                $new_dvdAfdest += $new_dvdest;
            } else {
                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $newSidak[$key][$key1]['deviden'] = 0;
                $newSidak[$key][$key1]['total_score'] = 0;
                $newSidak[$key][$key1]['total_brd'] = 0;
                $newSidak[$key][$key1]['total_janjang'] = 0;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
            }

            $dividen_afd = count($value);
            if ($new_dvdAfdest != 0) {
                $total_skoreest = round($tot_estAFd / $new_dvdAfdest, 1);
            } else {
                $total_skoreest = 0;
            }

            // dd($value);

            $namaGM = '-';
            foreach ($asisten_qc as $asisten) {
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
        }

        // dd($def)

        // dd($newSidak);
        $week1 = []; // Initialize the new array
        foreach ($newSidak as $key => $value) {
            $estateValues = []; // Initialize an array to accumulate values for estate
            $est_brd = 0;
            $total_weeks = 0;
            $deviden = 0;
            $devnew = 0;
            $skor_akhir = 0;
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue) && isset($subValue['week1'])) {
                    $week1Data = $subValue['week1']; // Access "week1" data
                    foreach ($weeks as $keywk => $value) if ($keywk == 1) {
                        $start = $value['start'];
                        $end = $value['end'];
                    }
                    // week for afdeling 
                    $week1Flat = [
                        'est' => $key,
                        'afd' => $subKey,
                        'total_score' => round($week1Data['all_score'], 1),
                        'kategori' => 'Test',

                    ];

                    // Extract tphx values for keys 1 to 8 and flatten them
                    for ($i = 1; $i <= 8; $i++) {
                        $tphxKey = $i;
                        $tphxValue = $week1Data[$tphxKey]['tphx'];
                        $jalan = $week1Data[$tphxKey]['jalan'];
                        $bin = $week1Data[$tphxKey]['bin'];
                        $karung = $week1Data[$tphxKey]['karung'];
                        $buah = $week1Data[$tphxKey]['buah'];
                        $restan = $week1Data[$tphxKey]['restan'];
                        $skor_brd = $week1Data[$tphxKey]['skor_brd'];
                        $skor_janjang = $week1Data[$tphxKey]['skor_janjang'];
                        $tot_brd = $week1Data[$tphxKey]['tot_brd'];
                        $tod_jjg = $week1Data[$tphxKey]['tod_jjg'];

                        $week1Flat["tph$i"] = $tphxValue;
                        $week1Flat["jalan$i"] = $jalan;
                        $week1Flat["bin$i"] = $bin;
                        $week1Flat["karung$i"] = $karung;
                        $week1Flat["buah$i"] = $buah;
                        $week1Flat["restan$i"] = $restan;
                        $week1Flat["skor_brd$i"] = $skor_brd;
                        $week1Flat["skor_janjang$i"] = round($skor_janjang, 1);
                        $week1Flat["tot_brd$i"] = $tot_brd;
                        $week1Flat["tod_jjg$i"] = $tod_jjg;
                        if (!isset($estateValues["tph$i"])) {
                            $estateValues["tph$i"] = 0;
                            $estateValues["jalan$i"] = 0;
                            $estateValues["bin$i"] = 0;
                            $estateValues["karung$i"] = 0;
                            $estateValues["buah$i"] = 0;
                            $estateValues["restan$i"] = 0;
                            $estateValues["skor_brd$i"] = 0;
                            $estateValues["skor_janjang$i"] = 0;
                            $estateValues["tot_brd$i"] = 0;
                            $estateValues["tod_jjg$i"] = 0;
                        }


                        [$panen_brd, $panen_jjg] = calculatePanen($i);

                        $estateValues["tph$i"] += $tphxValue;
                        $estateValues["jalan$i"] += $jalan;
                        $estateValues["bin$i"] += $bin;
                        $estateValues["karung$i"] += $karung;
                        $estateValues["buah$i"] += $buah;
                        $estateValues["restan$i"] += $restan;
                        $estateValues["skor_brd$i"] += round(($tot_brd * $panen_brd) / 100, 1);
                        $estateValues["skor_janjang$i"] += round(($tod_jjg * $panen_jjg) / 100, 1);
                        $estateValues["tot_brd$i"] += $tot_brd;
                        $estateValues["tod_jjg$i"] += $tod_jjg;
                    }
                    $total_weeks += round($week1Data['all_score'], 1);
                    $deviden += $subValue['new_deviden'];
                    $devnew = $subValue['devidenest'];
                    // Add the flattened array to the result
                    $week1[] = $week1Flat;
                }
            }
            if ($devnew != 0) {
                // $skor_akhir = round($total_weeks / $deviden, 1);
                $skor_akhir = round($total_weeks / $devnew, 1);
            } else {
                $skor_akhir = 0;
            }

            // week for estate 
            $weekestate = [
                'est' => $key,
                'afd' => 'EST',
                'kategori' => 'Test',
                'total_score' => $skor_akhir,
                'start' => $start,
                'end' => $end,
                'reg' => $regional,
            ];
            $skor_brd = 0;
            $skor_janjang = 0;
            for ($i = 1; $i <= 8; $i++) {
                // Calculate the values for estate using $estateValues

                $skor_brd += round($estateValues["skor_brd$i"], 1);
                $skor_janjang += round($estateValues["skor_janjang$i"], 1);
                $weekestate["tph$i"] = $estateValues["tph$i"];
                $weekestate["jalan$i"] = $estateValues["jalan$i"];
                $weekestate["bin$i"] = $estateValues["bin$i"];
                $weekestate["karung$i"] = $estateValues["karung$i"];
                $weekestate["buah$i"] = $estateValues["buah$i"];
                $weekestate["restan$i"] = $estateValues["restan$i"];
                $weekestate["skor_brd$i"] = $estateValues["skor_brd$i"];
                $weekestate["skor_janjang$i"] = round($estateValues["skor_janjang$i"], 1);
                $weekestate["tot_brd$i"] = $estateValues["tot_brd$i"];
                $weekestate["tod_jjg$i"] = $estateValues["tod_jjg$i"];
                // $weekestate["total_score"] = round($skor_brd + $skor_janjang, 1);
            }


            $week1[] = $weekestate;
        }

        // dd($week1);

        $week2 = []; // Initialize the new array
        foreach ($newSidak as $key => $value) {
            $estateValues = []; // Initialize an array to accumulate values for estate
            $est_brd = 0;
            $total_weeks = 0;
            $deviden = 0;
            $skor_akhir = 0;
            $devnew = 0;
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue) && isset($subValue['week2'])) {
                    $week1Data = $subValue['week2']; // Access "week1" data
                    foreach ($weeks as $keywk => $value) if ($keywk == 2) {
                        $start = $value['start'];
                        $end = $value['end'];
                    }
                    // week for afdeling 
                    $week1Flat = [
                        'est' => $key,
                        'afd' => $subKey,
                        'total_score' => round($week1Data['all_score'], 1),
                        'kategori' => 'Test',

                    ];

                    // Extract tphx values for keys 1 to 8 and flatten them
                    for ($i = 1; $i <= 8; $i++) {
                        $tphxKey = $i;
                        $tphxValue = $week1Data[$tphxKey]['tphx'];
                        $jalan = $week1Data[$tphxKey]['jalan'];
                        $bin = $week1Data[$tphxKey]['bin'];
                        $karung = $week1Data[$tphxKey]['karung'];
                        $buah = $week1Data[$tphxKey]['buah'];
                        $restan = $week1Data[$tphxKey]['restan'];
                        $skor_brd = $week1Data[$tphxKey]['skor_brd'];
                        $skor_janjang = $week1Data[$tphxKey]['skor_janjang'];
                        $tot_brd = $week1Data[$tphxKey]['tot_brd'];
                        $tod_jjg = $week1Data[$tphxKey]['tod_jjg'];

                        $week1Flat["tph$i"] = $tphxValue;
                        $week1Flat["jalan$i"] = $jalan;
                        $week1Flat["bin$i"] = $bin;
                        $week1Flat["karung$i"] = $karung;
                        $week1Flat["buah$i"] = $buah;
                        $week1Flat["restan$i"] = $restan;
                        $week1Flat["skor_brd$i"] = $skor_brd;
                        $week1Flat["skor_janjang$i"] = round($skor_janjang, 1);
                        $week1Flat["tot_brd$i"] = $tot_brd;
                        $week1Flat["tod_jjg$i"] = $tod_jjg;
                        if (!isset($estateValues["tph$i"])) {
                            $estateValues["tph$i"] = 0;
                            $estateValues["jalan$i"] = 0;
                            $estateValues["bin$i"] = 0;
                            $estateValues["karung$i"] = 0;
                            $estateValues["buah$i"] = 0;
                            $estateValues["restan$i"] = 0;
                            $estateValues["skor_brd$i"] = 0;
                            $estateValues["skor_janjang$i"] = 0;
                            $estateValues["tot_brd$i"] = 0;
                            $estateValues["tod_jjg$i"] = 0;
                        }


                        [$panen_brd, $panen_jjg] = calculatePanen($i);

                        $estateValues["tph$i"] += $tphxValue;
                        $estateValues["jalan$i"] += $jalan;
                        $estateValues["bin$i"] += $bin;
                        $estateValues["karung$i"] += $karung;
                        $estateValues["buah$i"] += $buah;
                        $estateValues["restan$i"] += $restan;
                        $estateValues["skor_brd$i"] += round(($tot_brd * $panen_brd) / 100, 1);
                        $estateValues["skor_janjang$i"] += round(($tod_jjg * $panen_jjg) / 100, 1);
                        $estateValues["tot_brd$i"] += $tot_brd;
                        $estateValues["tod_jjg$i"] += $tod_jjg;
                    }
                    $total_weeks += round($week1Data['all_score'], 1);
                    $deviden += $subValue['new_deviden'];
                    $devnew = $subValue['devidenest'];
                    // Add the flattened array to the result
                    $week2[] = $week1Flat;
                }
            }
            if ($devnew != 0) {
                // $skor_akhir = round($total_weeks / $deviden, 1);
                $skor_akhir = round($total_weeks / $devnew, 1);
            } else {
                $skor_akhir = 0;
            }

            // week for estate 
            $weekestate = [
                'est' => $key,
                'afd' => 'EST',
                'kategori' => 'Test',
                'total_score' => $skor_akhir,
                'start' => $start,
                'end' => $end,
                'reg' => $regional,
            ];
            $skor_brd = 0;
            $skor_janjang = 0;
            for ($i = 1; $i <= 8; $i++) {
                // Calculate the values for estate using $estateValues

                $skor_brd += round($estateValues["skor_brd$i"], 1);
                $skor_janjang += round($estateValues["skor_janjang$i"], 1);
                $weekestate["tph$i"] = $estateValues["tph$i"];
                $weekestate["jalan$i"] = $estateValues["jalan$i"];
                $weekestate["bin$i"] = $estateValues["bin$i"];
                $weekestate["karung$i"] = $estateValues["karung$i"];
                $weekestate["buah$i"] = $estateValues["buah$i"];
                $weekestate["restan$i"] = $estateValues["restan$i"];
                $weekestate["skor_brd$i"] = $estateValues["skor_brd$i"];
                $weekestate["skor_janjang$i"] = round($estateValues["skor_janjang$i"], 1);
                $weekestate["tot_brd$i"] = $estateValues["tot_brd$i"];
                $weekestate["tod_jjg$i"] = $estateValues["tod_jjg$i"];
                // $weekestate["total_score"] = round($skor_brd + $skor_janjang, 1);
            }


            $week2[] = $weekestate;
        }


        $week3 = []; // Initialize the new array
        foreach ($newSidak as $key => $value) {
            $estateValues = []; // Initialize an array to accumulate values for estate
            $est_brd = 0;
            $total_weeks = 0;
            $deviden = 0;
            $skor_akhir = 0;
            $devnew = 0;
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue) && isset($subValue['week3'])) {
                    $week1Data = $subValue['week3']; // Access "week1" data
                    foreach ($weeks as $keywk => $value) if ($keywk == 3) {
                        $start = $value['start'];
                        $end = $value['end'];
                    }
                    // week for afdeling 
                    $week1Flat = [
                        'est' => $key,
                        'afd' => $subKey,
                        'total_score' => round($week1Data['all_score'], 1),
                        'kategori' => 'Test',

                    ];

                    // Extract tphx values for keys 1 to 8 and flatten them
                    for ($i = 1; $i <= 8; $i++) {
                        $tphxKey = $i;
                        $tphxValue = $week1Data[$tphxKey]['tphx'];
                        $jalan = $week1Data[$tphxKey]['jalan'];
                        $bin = $week1Data[$tphxKey]['bin'];
                        $karung = $week1Data[$tphxKey]['karung'];
                        $buah = $week1Data[$tphxKey]['buah'];
                        $restan = $week1Data[$tphxKey]['restan'];
                        $skor_brd = $week1Data[$tphxKey]['skor_brd'];
                        $skor_janjang = $week1Data[$tphxKey]['skor_janjang'];
                        $tot_brd = $week1Data[$tphxKey]['tot_brd'];
                        $tod_jjg = $week1Data[$tphxKey]['tod_jjg'];

                        $week1Flat["tph$i"] = $tphxValue;
                        $week1Flat["jalan$i"] = $jalan;
                        $week1Flat["bin$i"] = $bin;
                        $week1Flat["karung$i"] = $karung;
                        $week1Flat["buah$i"] = $buah;
                        $week1Flat["restan$i"] = $restan;
                        $week1Flat["skor_brd$i"] = $skor_brd;
                        $week1Flat["skor_janjang$i"] = round($skor_janjang, 1);
                        $week1Flat["tot_brd$i"] = $tot_brd;
                        $week1Flat["tod_jjg$i"] = $tod_jjg;
                        if (!isset($estateValues["tph$i"])) {
                            $estateValues["tph$i"] = 0;
                            $estateValues["jalan$i"] = 0;
                            $estateValues["bin$i"] = 0;
                            $estateValues["karung$i"] = 0;
                            $estateValues["buah$i"] = 0;
                            $estateValues["restan$i"] = 0;
                            $estateValues["skor_brd$i"] = 0;
                            $estateValues["skor_janjang$i"] = 0;
                            $estateValues["tot_brd$i"] = 0;
                            $estateValues["tod_jjg$i"] = 0;
                        }


                        [$panen_brd, $panen_jjg] = calculatePanen($i);

                        $estateValues["tph$i"] += $tphxValue;
                        $estateValues["jalan$i"] += $jalan;
                        $estateValues["bin$i"] += $bin;
                        $estateValues["karung$i"] += $karung;
                        $estateValues["buah$i"] += $buah;
                        $estateValues["restan$i"] += $restan;
                        $estateValues["skor_brd$i"] += round(($tot_brd * $panen_brd) / 100, 1);
                        $estateValues["skor_janjang$i"] += round(($tod_jjg * $panen_jjg) / 100, 1);
                        $estateValues["tot_brd$i"] += $tot_brd;
                        $estateValues["tod_jjg$i"] += $tod_jjg;
                    }
                    $total_weeks += round($week1Data['all_score'], 1);
                    $deviden += $subValue['new_deviden'];
                    $devnew = $subValue['devidenest'];
                    // Add the flattened array to the result
                    $week3[] = $week1Flat;
                }
            }
            if ($devnew != 0) {
                // $skor_akhir = round($total_weeks / $deviden, 1);
                $skor_akhir = round($total_weeks / $devnew, 1);
            } else {
                $skor_akhir = 0;
            }
            // week for estate 
            $weekestate = [
                'est' => $key,
                'afd' => 'EST',
                'kategori' => 'Test',
                'total_score' => $skor_akhir,
                'start' => $start,
                'end' => $end,
                'reg' => $regional,
            ];
            $skor_brd = 0;
            $skor_janjang = 0;
            for ($i = 1; $i <= 8; $i++) {
                // Calculate the values for estate using $estateValues

                $skor_brd += round($estateValues["skor_brd$i"], 1);
                $skor_janjang += round($estateValues["skor_janjang$i"], 1);
                $weekestate["tph$i"] = $estateValues["tph$i"];
                $weekestate["jalan$i"] = $estateValues["jalan$i"];
                $weekestate["bin$i"] = $estateValues["bin$i"];
                $weekestate["karung$i"] = $estateValues["karung$i"];
                $weekestate["buah$i"] = $estateValues["buah$i"];
                $weekestate["restan$i"] = $estateValues["restan$i"];
                $weekestate["skor_brd$i"] = $estateValues["skor_brd$i"];
                $weekestate["skor_janjang$i"] = round($estateValues["skor_janjang$i"], 1);
                $weekestate["tot_brd$i"] = $estateValues["tot_brd$i"];
                $weekestate["tod_jjg$i"] = $estateValues["tod_jjg$i"];
                // $weekestate["total_score"] = round($skor_brd + $skor_janjang, 1);
            }


            $week3[] = $weekestate;
        }

        $week4 = []; // Initialize the new array
        foreach ($newSidak as $key => $value) {
            $estateValues = []; // Initialize an array to accumulate values for estate
            $est_brd = 0;
            $total_weeks = 0;
            $deviden = 0;
            $devnew = 0;
            $skor_akhir = 0;
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue) && isset($subValue['week4'])) {
                    $week1Data = $subValue['week4']; // Access "week1" data
                    foreach ($weeks as $keywk => $value) if ($keywk == 4) {
                        $start = $value['start'];
                        $end = $value['end'];
                    }
                    // week for afdeling 
                    $week1Flat = [
                        'est' => $key,
                        'afd' => $subKey,
                        'total_score' => round($week1Data['all_score'], 1),
                        'kategori' => 'Test',

                    ];

                    // Extract tphx values for keys 1 to 8 and flatten them
                    for ($i = 1; $i <= 8; $i++) {
                        $tphxKey = $i;
                        $tphxValue = $week1Data[$tphxKey]['tphx'];
                        $jalan = $week1Data[$tphxKey]['jalan'];
                        $bin = $week1Data[$tphxKey]['bin'];
                        $karung = $week1Data[$tphxKey]['karung'];
                        $buah = $week1Data[$tphxKey]['buah'];
                        $restan = $week1Data[$tphxKey]['restan'];
                        $skor_brd = $week1Data[$tphxKey]['skor_brd'];
                        $skor_janjang = $week1Data[$tphxKey]['skor_janjang'];
                        $tot_brd = $week1Data[$tphxKey]['tot_brd'];
                        $tod_jjg = $week1Data[$tphxKey]['tod_jjg'];

                        $week1Flat["tph$i"] = $tphxValue;
                        $week1Flat["jalan$i"] = $jalan;
                        $week1Flat["bin$i"] = $bin;
                        $week1Flat["karung$i"] = $karung;
                        $week1Flat["buah$i"] = $buah;
                        $week1Flat["restan$i"] = $restan;
                        $week1Flat["skor_brd$i"] = $skor_brd;
                        $week1Flat["skor_janjang$i"] = round($skor_janjang, 1);
                        $week1Flat["tot_brd$i"] = $tot_brd;
                        $week1Flat["tod_jjg$i"] = $tod_jjg;
                        if (!isset($estateValues["tph$i"])) {
                            $estateValues["tph$i"] = 0;
                            $estateValues["jalan$i"] = 0;
                            $estateValues["bin$i"] = 0;
                            $estateValues["karung$i"] = 0;
                            $estateValues["buah$i"] = 0;
                            $estateValues["restan$i"] = 0;
                            $estateValues["skor_brd$i"] = 0;
                            $estateValues["skor_janjang$i"] = 0;
                            $estateValues["tot_brd$i"] = 0;
                            $estateValues["tod_jjg$i"] = 0;
                        }


                        [$panen_brd, $panen_jjg] = calculatePanen($i);

                        $estateValues["tph$i"] += $tphxValue;
                        $estateValues["jalan$i"] += $jalan;
                        $estateValues["bin$i"] += $bin;
                        $estateValues["karung$i"] += $karung;
                        $estateValues["buah$i"] += $buah;
                        $estateValues["restan$i"] += $restan;
                        $estateValues["skor_brd$i"] += round(($tot_brd * $panen_brd) / 100, 1);
                        $estateValues["skor_janjang$i"] += round(($tod_jjg * $panen_jjg) / 100, 1);
                        $estateValues["tot_brd$i"] += $tot_brd;
                        $estateValues["tod_jjg$i"] += $tod_jjg;
                    }
                    $total_weeks += round($week1Data['all_score'], 1);
                    $deviden += $subValue['new_deviden'];
                    $devnew = $subValue['devidenest'];
                    // Add the flattened array to the result
                    $week4[] = $week1Flat;
                }
            }
            if ($devnew != 0) {
                // $skor_akhir = round($total_weeks / $deviden, 1);
                $skor_akhir = round($total_weeks / $devnew, 1);
            } else {
                $skor_akhir = 0;
            }
            // dd

            // week for estate 
            $weekestate = [
                'est' => $key,
                'afd' => 'EST',
                'kategori' => 'Test',
                'total_score' => $skor_akhir,
                'start' => $start,
                'end' => $end,
                'reg' => $regional,
            ];
            $skor_brd = 0;
            $skor_janjang = 0;
            for ($i = 1; $i <= 8; $i++) {
                // Calculate the values for estate using $estateValues

                $skor_brd += round($estateValues["skor_brd$i"], 1);
                $skor_janjang += round($estateValues["skor_janjang$i"], 1);
                $weekestate["tph$i"] = $estateValues["tph$i"];
                $weekestate["jalan$i"] = $estateValues["jalan$i"];
                $weekestate["bin$i"] = $estateValues["bin$i"];
                $weekestate["karung$i"] = $estateValues["karung$i"];
                $weekestate["buah$i"] = $estateValues["buah$i"];
                $weekestate["restan$i"] = $estateValues["restan$i"];
                $weekestate["skor_brd$i"] = $estateValues["skor_brd$i"];
                $weekestate["skor_janjang$i"] = round($estateValues["skor_janjang$i"], 1);
                $weekestate["tot_brd$i"] = $estateValues["tot_brd$i"];
                $weekestate["tod_jjg$i"] = $estateValues["tod_jjg$i"];
                // $weekestate["total_score"] = round($skor_brd + $skor_janjang, 1);
            }


            $week4[] = $weekestate;
        }



        $week5 = []; // Initialize the new array
        foreach ($newSidak as $key => $value) {
            $estateValues = []; // Initialize an array to accumulate values for estate
            $est_brd = 0;
            $total_weeks = 0;
            $deviden = 0;
            $skor_akhir = 0;
            $devnew = 0;
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue) && isset($subValue['week5'])) {
                    $week1Data = $subValue['week5']; // Access "week1" data
                    foreach ($weeks as $keywk => $value) if ($keywk == 5) {
                        $start = $value['start'];
                        $end = $value['end'];
                    }
                    // week for afdeling 
                    $week1Flat = [
                        'est' => $key,
                        'afd' => $subKey,
                        'total_score' => round($week1Data['all_score'], 1),
                        'kategori' => 'Test',

                    ];

                    // dd($subValue);

                    // Extract tphx values for keys 1 to 8 and flatten them
                    for ($i = 1; $i <= 8; $i++) {
                        $tphxKey = $i;
                        $tphxValue = $week1Data[$tphxKey]['tphx'];
                        $jalan = $week1Data[$tphxKey]['jalan'];
                        $bin = $week1Data[$tphxKey]['bin'];
                        $karung = $week1Data[$tphxKey]['karung'];
                        $buah = $week1Data[$tphxKey]['buah'];
                        $restan = $week1Data[$tphxKey]['restan'];
                        $skor_brd = $week1Data[$tphxKey]['skor_brd'];
                        $skor_janjang = $week1Data[$tphxKey]['skor_janjang'];
                        $tot_brd = $week1Data[$tphxKey]['tot_brd'];
                        $tod_jjg = $week1Data[$tphxKey]['tod_jjg'];

                        $week1Flat["tph$i"] = $tphxValue;
                        $week1Flat["jalan$i"] = $jalan;
                        $week1Flat["bin$i"] = $bin;
                        $week1Flat["karung$i"] = $karung;
                        $week1Flat["buah$i"] = $buah;
                        $week1Flat["restan$i"] = $restan;
                        $week1Flat["skor_brd$i"] = $skor_brd;
                        $week1Flat["skor_janjang$i"] = round($skor_janjang, 1);
                        $week1Flat["tot_brd$i"] = $tot_brd;
                        $week1Flat["tod_jjg$i"] = $tod_jjg;
                        if (!isset($estateValues["tph$i"])) {
                            $estateValues["tph$i"] = 0;
                            $estateValues["jalan$i"] = 0;
                            $estateValues["bin$i"] = 0;
                            $estateValues["karung$i"] = 0;
                            $estateValues["buah$i"] = 0;
                            $estateValues["restan$i"] = 0;
                            $estateValues["skor_brd$i"] = 0;
                            $estateValues["skor_janjang$i"] = 0;
                            $estateValues["tot_brd$i"] = 0;
                            $estateValues["tod_jjg$i"] = 0;
                        }


                        [$panen_brd, $panen_jjg] = calculatePanen($i);

                        $estateValues["tph$i"] += $tphxValue;
                        $estateValues["jalan$i"] += $jalan;
                        $estateValues["bin$i"] += $bin;
                        $estateValues["karung$i"] += $karung;
                        $estateValues["buah$i"] += $buah;
                        $estateValues["restan$i"] += $restan;
                        $estateValues["skor_brd$i"] += round(($tot_brd * $panen_brd) / 100, 1);
                        $estateValues["skor_janjang$i"] += round(($tod_jjg * $panen_jjg) / 100, 1);
                        $estateValues["tot_brd$i"] += $tot_brd;
                        $estateValues["tod_jjg$i"] += $tod_jjg;
                    }
                    $total_weeks += round($week1Data['all_score'], 1);
                    $deviden += $subValue['new_deviden'];
                    $devnew = $subValue['devidenest'];
                    // Add the flattened array to the result
                    $week5[] = $week1Flat;
                }
            }
            if ($devnew != 0) {
                // $skor_akhir = round($total_weeks / $deviden, 1);
                $skor_akhir = round($total_weeks / $devnew, 1);
            } else {
                $skor_akhir = 0;
            }
            // dd($total_weeks);
            // week for estate 
            $weekestate = [
                'est' => $key,
                'afd' => 'EST',
                'kategori' => 'Test',
                'total_score' => $skor_akhir,
                'start' => $start,
                'end' => $end,
                'reg' => $regional,
            ];
            $skor_brd = 0;
            $skor_janjang = 0;
            for ($i = 1; $i <= 8; $i++) {
                // Calculate the values for estate using $estateValues

                $skor_brd += round($estateValues["skor_brd$i"], 1);
                $skor_janjang += round($estateValues["skor_janjang$i"], 1);
                $weekestate["tph$i"] = $estateValues["tph$i"];
                $weekestate["jalan$i"] = $estateValues["jalan$i"];
                $weekestate["bin$i"] = $estateValues["bin$i"];
                $weekestate["karung$i"] = $estateValues["karung$i"];
                $weekestate["buah$i"] = $estateValues["buah$i"];
                $weekestate["restan$i"] = $estateValues["restan$i"];
                $weekestate["skor_brd$i"] = $estateValues["skor_brd$i"];
                $weekestate["skor_janjang$i"] = round($estateValues["skor_janjang$i"], 1);
                $weekestate["tot_brd$i"] = $estateValues["tot_brd$i"];
                $weekestate["tod_jjg$i"] = $estateValues["tod_jjg$i"];
                // $weekestate["total_score"] = round($skor_brd + $skor_janjang, 1);
            }


            $week5[] = $weekestate;
        }


        // dd($week5);
        $arrView = array();
        $arrView['week1'] = $week1;
        $arrView['week2'] = $week2;
        $arrView['week3'] = $week3;
        $arrView['week4'] = $week4;
        $arrView['week5'] = $week5;



        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
        // return view('dataSidakTph', [
        //     'dataSkor' => $dataSkor,
        //     'dataSkorPlasma' => $dataSkorPlas,
        //     'tanggal' => $tanggal,
        //     'regional' => $regional,
        // ]);
    }

    public function listAsisten(Request $request)
    {
        $query = DB::connection('mysql2')->table('asisten_qc')
            ->select('asisten_qc.*')
            // ->whereIn('estate.wil', [1, 2, 3])
            // ->join('estate', 'estate.est', '=', 'asisten_qc.est')
            ->get();

        $queryEst = DB::connection('mysql2')->table('estate')->whereIn('wil', [1, 2, 3])->get();
        $queryAfd = DB::connection('mysql2')->table('afdeling')->select('nama')->groupBy('nama')->get();

        return view('listAsisten', ['asisten' => $query, 'estate' => $queryEst, 'afdeling' => $queryAfd]);
    }

    public function tambahAsisten(Request $request)
    {
        $query = DB::connection('mysql2')->table('asisten_qc')
            ->where('est', $request->input('est'))
            ->where('afd', $request->input('afd'))
            ->first();

        if (empty($query)) {
            DB::connection('mysql2')->table('asisten_qc')->insert([
                'nama' => $request->input('nama'),
                'est' => $request->input('est'),
                'afd' => $request->input('afd')
            ]);

            return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil ditambahkan!');
        } else {
            return redirect()->route('listAsisten')->with('error', 'Gagal ditambahkan, asisten dengan Estate dan Afdeling tersebut sudah ada!');
        }
    }

    public function perbaruiAsisten(Request $request)
    {

        $est = $request->input('est');
        $afd = $request->input('afd');
        $nama = $request->input('nama');
        $id = $request->input('id');

        $query = DB::connection('mysql2')->table('asisten_qc')
            ->where('id', $id)
            ->first();


        // dd($est, $query->est);

        if ($query->nama != $nama && $query->est == $est && $query->afd == $afd) {
            DB::connection('mysql2')->table('asisten_qc')->where('id', $request->input('id'))
                ->update([
                    'nama' => $request->input('nama'),
                    'est' => $request->input('est'),
                    'afd' => $request->input('afd')
                ]);

            return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil diperbarui!');
        } else if ($est != $query->est) {
            $queryWill2 = DB::connection('mysql2')->table('asisten_qc')
                ->where('est', $est)
                ->where('afd', $afd)
                ->first();

            if (empty($queryWill2)) {
                DB::connection('mysql2')->table('asisten_qc')->where('id', $request->input('id'))
                    ->update([
                        'nama' => $request->input('nama'),
                        'est' => $request->input('est'),
                        'afd' => $request->input('afd')
                    ]);

                return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil diperbarui!');
            } else {
                return redirect()->route('listAsisten')->with('error', 'Gagal diperbarui, asisten dengan Estate dan Afdeling tersebut sudah ada!');
            }
        } else if ($afd != $query->afd) {
            $queryWill2 = DB::connection('mysql2')->table('asisten_qc')
                ->where('est', $est)
                ->where('afd', $afd)
                ->first();

            if (empty($queryWill2)) {
                DB::connection('mysql2')->table('asisten_qc')->where('id', $request->input('id'))
                    ->update([
                        'nama' => $request->input('nama'),
                        'est' => $request->input('est'),
                        'afd' => $request->input('afd')
                    ]);

                return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil diperbarui!');
            } else {
                return redirect()->route('listAsisten')->with('error', 'Gagal diperbarui, asisten dengan Estate dan Afdeling tersebut sudah ada!');
            }
        } else {
            return redirect()->route('listAsisten')->with('error', 'Gagal diperbarui, asisten dengan Estate dan Afdeling tersebut sudah ada!');
        }

        // $query = DB::connection('mysql2')->table('asisten_qc')
        //     ->where('est', $request->input('est'))
        //     ->where('afd', $request->input('afd'))
        //     ->first();

        // // dd($query);
        // if (empty($query)) {
        //     DB::connection('mysql2')->table('asisten_qc')->where('id', $request->input('id'))
        //         ->update([
        //             'nama' => $request->input('nama'),
        //             'est' => $request->input('est'),
        //             'afd' => $request->input('afd')
        //         ]);

        //     return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil diperbarui!');
        // } else {
        //     return redirect()->route('listAsisten')->with('error', 'Gagal diperbarui, asisten dengan Estate dan Afdeling tersebut sudah ada!');
        // }
    }

    public function hapusAsisten(Request $request)
    {
        DB::connection('mysql2')->table('asisten_qc')->where('id', $request->input('id'))->delete();
        return redirect()->route('listAsisten')->with('status', 'Data asisten berhasil dihapus!');
    }

    public function downloadPDF(Request $request)
    {
        $url = $request->get('url');
        $arrView = array();
        $file_headers = @get_headers($url);
        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $arrView['status'] = '404';
            $arrView['url'] = $url;
        } else {
            $arrView['status'] = '200';
            $arrView['url'] = $url;
        }
        echo json_encode($arrView);
        exit();
    }

    public function getBtTph(Request $request)
    {


        $regSidak = $request->get('reg');
        $queryWill = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->get();
        $queryReg = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('regional');
        $queryReg2 = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('id')
            ->toArray();

        // dapatkan data estate dari table estate dengan wilayah 1 , 2 , 3
        $queryEst = DB::connection('mysql2')
            ->table('estate')
            // ->whereNotIn('estate.est', ['PLASMA'])
            ->whereIn('wil', $queryReg2)
            ->get();
        // dd($queryEst);
        $queryEste = DB::connection('mysql2')
            ->table('estate')
            ->whereNotIn('estate.est', ['PLASMA', 'SRE', 'LDE', 'SKE'])
            ->whereIn('wil', $queryReg2)
            ->get();
        $queryEste = $queryEste->groupBy(function ($item) {
            return $item->wil;
        });

        $queryEste = json_decode($queryEste, true);

        // dd($queryEst);
        $queryAfd = DB::connection('mysql2')
            ->Table('afdeling')
            ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();

        $queryAfd = json_decode($queryAfd, true);

        //array untuk tampung nilai bt tph per estate dari table bt_jalan & bt_tph dll
        $arrBtTPHperEst = []; //table dari brondolan di buat jadi array agar bisa di parse ke json
        $arrKRest = []; //table dari jum_jkarung di buat jadi array agar bisa di parse ke json
        $arrBHest = []; //table dari Buah di buat jadi array agar bisa di parse ke json
        $arrRSest = []; //table array untuk buah restant tidak di laporkan

        ///array untuk table nya

        $dataSkorAwal = [];

        $list_all_will = [];

        //memberi nilai 0 default kesemua estate
        foreach ($queryEst as $key => $value) {
            $arrBtTPHperEst[$value->est] = 0; //est mengambil value dari table estate
            $arrKRest[$value->est] = 0;
            $arrBHest[$value->est] = 0;
            $arrRSest[$value->est] = 0;
        }
        // dd($queryEst);
        foreach ($queryWill as $key => $value) {
            $arrBtTPHperWil[$value->nama] = 0; //est mengambil value dari table estate
            $arrKRestWil[$value->nama] = 0;
            $arrBHestWil[$value->nama] = 0;
            $arrRestWill[$value->nama] = 0;
        }

        $firstWeek = $request->get('start');
        $lastWeek = $request->get('finish');

        // dd($firstWeek, $lastWeek);
        $query = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->whereBetween('sidak_tph.datetime', [$firstWeek, $lastWeek])
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        //     ->get();
        $queryAFD = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->whereBetween('sidak_tph.datetime', [$firstWeek, $lastWeek])
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        // dd($query);
        $queryAsisten = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();

        $dataAfdEst = [];

        $querySidak = DB::connection('mysql2')
            ->table('sidak_tph')
            ->whereBetween('sidak_tph.datetime', [$firstWeek, $lastWeek])
            // ->whereBetween('sidak_tph.datetime', ['2023-01-23', '202-12-25'])
            ->get();
        $querySidak = json_decode($querySidak, true);

        $allBlok = $query->groupBy(function ($item) {
            return $item->blok;
        });

        if (!empty($query && $queryAFD && $querySidak)) {
            $queryGroup = $queryAFD->groupBy(function ($item) {
                return $item->est;
            });
            // dd($queryGroup);
            $queryWi = DB::connection('mysql2')
                ->table('estate')
                ->whereIn('wil', $queryReg2)
                ->get();

            $queryWill = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });

            $queryWill2 = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });

            //untuk table!!
            // store wil -> est -> afd
            // menyimpan array nested dari  wil -> est -> afd
            foreach ($queryEste as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($queryAfd as $key3 => $value3) {
                        $est = $value2['est'];
                        $afd = $value3['nama'];
                        if ($value2['est'] == $value3['est']) {
                            foreach ($querySidak as $key4 => $value4) {
                                if ($est == $value4['est'] && $afd == $value4['afd']) {
                                    $dataAfdEst[$est][$afd][] = $value4;
                                } else {
                                    $dataAfdEst[$est][$afd]['null'] = 0;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        unset($dataAfdEst[$key][$key2]['null']);
                        if (empty($dataAfdEst[$key][$key2])) {
                            $dataAfdEst[$key][$key2] = 0;
                        }
                    }
                }
            }

            $listBlokPerAfd = [];
            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            // dd($key3);
                            foreach ($allBlok as $key4 => $value4) {
                                if ($value3['blok'] == $key4) {
                                    $listBlokPerAfd[$key][$key2][$key3] = $value4;
                                }
                            }
                        }
                    }
                }

                // //menghitung data skor untuk brd/blok
                foreach ($dataAfdEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value2 as $key3 => $value3) {
                                if (is_array($value3)) {
                                    $blok = $value3['est'] . ' ' . $value3['afd'] . ' ' . $value3['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value3['bt_tph'];
                                    $sum_bt_jalan += $value3['bt_jalan'];
                                    $sum_bt_bin += $value3['bt_bin'];

                                    $sum_jum_karung += $value3['jum_karung'];
                                    $sum_buah_tinggal += $value3['buah_tinggal'];
                                    $sum_restan_unreported += $value3['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            $skor_brd = round($sum_all / $jum_blok, 1);
                            // dd($skor_brd);
                            $skor_kr = round($sum_all_karung / $jum_blok, 1);
                            $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                            $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $dataSkorAwal[$key][$key2]['karung_tes'] = $sum_all_karung;
                            $dataSkorAwal[$key][$key2]['tph_test'] = $sum_all;
                            $dataSkorAwal[$key][$key2]['buah_test'] = $sum_all_bt_tgl;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = $sum_all_restan_unreported;

                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = $jum_blok;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = skorBRDsidak($skor_brd);
                            // $dataSkorAwal[$key][$key2]['brd'] = skorBRDsidak($skor_brd);
                            $dataSkorAwal[$key][$key2]['kr_blok'] = skorKRsidak($skor_kr);
                            $dataSkorAwal[$key][$key2]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $dataSkorAwal[$key][$key2]['restan_blok'] = skorRSsidak($skor_restan);
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = $skoreTotal;
                        } else {
                            $dataSkorAwal[$key][$key2]['karung_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['tph_test'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_test'] = 0;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['kr_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['restan_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = 0;
                        }
                    }
                }

                // dd($dataSkorAwal);

                foreach ($dataSkorAwal as $key => $value) {
                    $jum_blok = 0;
                    $jum_all_blok = 0;
                    $sum_all_tph = 0;
                    $sum_tph = 0;
                    $sum_all_karung = 0;
                    $sum_karung = 0;
                    $sum_all_buah = 0;
                    $sum_buah = 0;
                    $sum_all_restant = 0;
                    $sum_restant = 0;
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok += $value2['jumlah_blok'];
                            $sum_karung += $value2['karung_tes'];
                            $sum_restant += $value2['restant_tes'];
                            $sum_tph += $value2['tph_test'];
                            $sum_buah += $value2['buah_test'];
                        }
                    }
                    $sum_all_tph = $sum_tph;
                    $jum_all_blok = $jum_blok;
                    $sum_all_karung = $sum_karung;
                    $sum_all_buah = $sum_buah;
                    $sum_all_restant = $sum_restant;

                    if ($jum_all_blok != 0) {
                        $skor_tph = round($sum_all_tph / $jum_all_blok, 2);
                        $skor_karung = round($sum_all_karung / $jum_all_blok, 2);
                        $skor_buah = round($sum_all_buah / $jum_all_blok, 2);
                        $skor_restan = round($sum_all_restant / $jum_all_blok, 2);
                    } else {
                        $skor_tph = 0;
                        $skor_karung = 0;
                        $skor_buah = 0;
                        $skor_restan = 0;
                    }


                    $skoreTotal = skorBRDsidak($skor_tph) + skorKRsidak($skor_karung) + skorBHsidak($skor_buah) + skorRSsidak($skor_restan);

                    if (
                        $jum_blok == 0 &&
                        $sum_karung == 0 &&
                        $sum_restant == 0 &&
                        $sum_tph == 0 &&
                        $sum_buah == 0
                    ) {
                        $dataSkorAwaltest[$key]['skor_akhir'] = 0;
                    } else {
                        $dataSkorAwaltest[$key]['skor_akhir'] = $skoreTotal;
                    }
                    $dataSkorAwaltest[$key]['total_estate_brondol'] = $sum_all_tph;
                    $dataSkorAwaltest[$key]['total_estate_karung'] = $sum_all_karung;
                    $dataSkorAwaltest[$key]['total_estate_buah_tinggal'] = $sum_all_buah;
                    $dataSkorAwaltest[$key]['total_estate_restan_tinggal'] = $sum_all_restant;
                    $dataSkorAwaltest[$key]['tph'] = skorBRDsidak($skor_tph);
                    $dataSkorAwaltest[$key]['karung'] = skorKRsidak($skor_karung);
                    $dataSkorAwaltest[$key]['buah_tinggal'] = skorBHsidak($skor_buah);
                    $dataSkorAwaltest[$key]['restant'] = skorRSsidak($skor_restan);
                    $dataSkorAwaltest[$key]['total_blokokok'] = $jum_all_blok;
                }
                // dd($dataSkorAwaltest);

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwal as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWil[$key][$key3] = $value3;
                            }
                        }
                    }
                }

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwaltest as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWilEst[$key][$key3] = $value3;
                            }
                        }
                    }
                }
                // dd($dataSkorAkhirPerWil);
                //menshort nilai masing masing
                $sortList = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $sortList[$key][$key2 . '_' . $key3] = $value3['skore_akhir'];
                            $inc++;
                        }
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortList as &$value) {
                    arsort($value);
                }
                // dd($sortList);
                $sortListEstate = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $sortListEstate[$key][$key2] = $value2['skor_akhir'];
                        $inc++;
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortListEstate as &$value) {
                    arsort($value);
                }

                // dd($sortListEstate);
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                        }
                    }
                }

                // dd($dataSkorAkhirPerWilEst);
                //menambahkan nilai rank ketia semua total skor sudah di uritkan
                $test = [];
                $listRank = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    // create an array to store the skore_akhir values
                    $skore_akhir_values = [];
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $skore_akhir_values[] = $value3['skore_akhir'];
                        }
                    }
                    // sort the skore_akhir values in descending order
                    rsort($skore_akhir_values);
                    // assign ranks to each skore_akhir value
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $rank = array_search($value3['skore_akhir'], $skore_akhir_values) + 1;
                            $dataSkorAkhirPerWil[$key][$key2][$key3]['rank'] = $rank;
                            $test[$key][] = $value3['skore_akhir'];
                        }
                    }
                }

                // dd($dataSkorAkhirPerWil);
                // perbaiki rank saya berdasarkan skore_akhir di mana jika $value3['skore_akhir'] terkecil merupakan rank 1 dan seterusnya
                $list_all_will = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $list_all_will[$key][$inc]['est_afd'] = $key2 . '_' . $key3;
                            $list_all_will[$key][$inc]['est'] = $key2;
                            $list_all_will[$key][$inc]['afd'] = $key3;
                            $list_all_will[$key][$inc]['skor'] = $value3['skore_akhir'];
                            foreach ($queryAsisten as $key4 => $value4) {
                                if ($value4->est == $key2 && $value4->afd == $key3) {
                                    $list_all_will[$key][$inc]['nama'] = $value4->nama;
                                }
                            }
                            if (empty($list_all_will[$key][$inc]['nama'])) {
                                $list_all_will[$key][$inc]['nama'] = '-';
                            }
                            $inc++;
                        }
                    }
                }

                // dd($dataSkorAkhirPerWilEst);
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    foreach ($value as $subKey => $subValue) {
                        if (strpos($subKey, 'Plasma') !== false) {
                            unset($dataSkorAkhirPerWilEst[$key][$subKey]);
                        }
                    }
                }
                // dd($dataSkorAkhirPerWilEst);
                $skor_gm_wil = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $sum_est_brondol = 0;
                    $sum_est_karung = 0;
                    $sum_est_buah_tinggal = 0;
                    $sum_est_restan_tinggal = 0;
                    $sum_blok = 0;
                    foreach ($value as $key2 => $value2) {
                        $sum_est_brondol += $value2['total_estate_brondol'];
                        $sum_est_karung += $value2['total_estate_karung'];
                        $sum_est_buah_tinggal += $value2['total_estate_buah_tinggal'];
                        $sum_est_restan_tinggal += $value2['total_estate_restan_tinggal'];

                        // dd($value2['total_blokokok']);

                        // if ($value2['total_blokokok'] != 0) {
                        $sum_blok += $value2['total_blokokok'];
                        // } else {
                        //     $sum_blok = 1;
                        // }
                    }

                    if ($sum_blok != 0) {
                        $skor_total_brondol = round($sum_est_brondol / $sum_blok, 2);
                    } else {
                        $skor_total_brondol = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_karung = round($sum_est_karung / $sum_blok, 2);
                    } else {
                        $skor_total_karung = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_buah_tinggal = round($sum_est_buah_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_buah_tinggal = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_restan_tinggal = round($sum_est_restan_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_restan_tinggal = 0;
                    }
                    if (
                        $sum_est_brondol == 0 &&
                        $sum_est_karung  == 0 &&
                        $sum_est_buah_tinggal  == 0 &&
                        $sum_est_restan_tinggal  == 0 &&
                        $sum_blok  == 0
                    ) {
                        $skor_gm_wil[$key]['skor'] = 0;
                    } else {
                        $skor_gm_wil[$key]['skor'] = skorBRDsidak($skor_total_brondol) + skorKRsidak($skor_total_karung) + skorBHsidak($skor_total_buah_tinggal) + skorRSsidak($skor_total_restan_tinggal);
                    }

                    $skor_gm_wil[$key]['total_brondolan'] = $sum_est_brondol;
                    $skor_gm_wil[$key]['total_karung'] = $sum_est_karung;
                    $skor_gm_wil[$key]['total_buah_tinggal'] = $sum_est_buah_tinggal;
                    $skor_gm_wil[$key]['total_restan'] = $sum_est_restan_tinggal;
                    $skor_gm_wil[$key]['blok'] = $sum_blok;
                }
                // dd($skor_gm_wil);
                $GmSkorWil = [];

                $queryAsisten1 = DB::connection('mysql2')
                    ->Table('asisten_qc')
                    ->get();
                $queryAsisten1 = json_decode($queryAsisten1, true);

                foreach ($skor_gm_wil as $key => $value) {
                    // determine estWil value based on key
                    if ($key == 1) {
                        $estWil = 'WIL-I';
                    } elseif ($key == 2) {
                        $estWil = 'WIL-II';
                    } elseif ($key == 3) {
                        $estWil = 'WIL-III';
                    } elseif ($key == 4) {
                        $estWil = 'WIL-IV';
                    } elseif ($key == 5) {
                        $estWil = 'WIL-V';
                    } elseif ($key == 6) {
                        $estWil = 'WIL-VI';
                    } elseif ($key == 7) {
                        $estWil = 'WIL-VII';
                    } elseif ($key == 8) {
                        $estWil = 'WIL-VIII';
                    } elseif ($key == 10) {
                        $estWil = 'WIL-IX';
                    } elseif ($key == 11) {
                        $estWil = 'WIL-X';
                    }

                    // get nama value from queryAsisten1
                    $namaGM = '-';
                    foreach ($queryAsisten1 as $asisten) {
                        if ($asisten['est'] == $estWil && $asisten['afd'] == 'GM') {
                            $namaGM = $asisten['nama'];
                            break; // stop searching once we find the matching asisten
                        }
                    }

                    // add the current skor_gm_wil value and namaGM value to GmSkorWil array
                    $GmSkorWil[] = [
                        'total_brondolan' => $value['total_brondolan'],
                        'total_karung' => $value['total_karung'],
                        'total_buah_tinggal' => $value['total_buah_tinggal'],
                        'total_restan' => $value['total_restan'],
                        'blok' => $value['blok'],
                        'skor' => $value['skor'],
                        'est' => $estWil,
                        'afd' => 'GM',
                        'namaGM' => $namaGM,
                    ];
                }

                $GmSkorWil = array_values($GmSkorWil);

                $sum_wil_blok = 0;
                $sum_wil_brondolan = 0;
                $sum_wil_karung = 0;
                $sum_wil_buah_tinggal = 0;
                $sum_wil_restan = 0;

                foreach ($skor_gm_wil as $key => $value) {
                    $sum_wil_blok += $value['blok'];
                    $sum_wil_brondolan += $value['total_brondolan'];
                    $sum_wil_karung += $value['total_karung'];
                    $sum_wil_buah_tinggal += $value['total_buah_tinggal'];
                    $sum_wil_restan += $value['total_restan'];
                }

                $skor_total_wil_brondol = $sum_wil_blok == 0 ? $sum_wil_brondolan : round($sum_wil_brondolan / $sum_wil_blok, 2);
                $skor_total_wil_karung = $sum_wil_blok == 0 ? $sum_wil_karung : round($sum_wil_karung / $sum_wil_blok, 2);
                $skor_total_wil_buah_tinggal = $sum_wil_blok == 0 ? $sum_wil_buah_tinggal : round($sum_wil_buah_tinggal / $sum_wil_blok, 2);
                $skor_total_wil_restan = $sum_wil_blok == 0 ? $sum_wil_restan : round($sum_wil_restan / $sum_wil_blok, 2);

                $skor_rh = [];
                foreach ($queryReg as $key => $value) {
                    if ($value == 1) {
                        $est = 'REG-I';
                    } elseif ($value == 2) {
                        $est = 'REG-II';
                    } elseif ($value == 3) {
                        $est = 'REG-III';
                    } else {
                        $est = 'REG-IV';
                    }
                    foreach ($queryAsisten as $key2 => $value2) {
                        if ($value2->est == $est && $value2->afd == 'RH') {
                            $skor_rh[$value]['nama'] = $value2->nama;
                        }
                    }
                    if (empty($skor_rh[$value]['nama'])) {
                        $skor_rh[$value]['nama'] = '-';
                    }
                    if (
                        $sum_wil_blok == 0 &&
                        $sum_wil_brondolan == 0 &&
                        $sum_wil_karung == 0 &&
                        $sum_wil_buah_tinggal == 0 &&
                        $sum_wil_restan == 0
                    ) {
                        $skor_rh[$value]['skor'] = 0;
                    } else {
                        $skor_rh[$value]['skor'] = skorBRDsidak($skor_total_wil_brondol) + skorKRsidak($skor_total_wil_karung) + skorBHsidak($skor_total_wil_buah_tinggal) + skorRSsidak($skor_total_wil_restan);
                    }
                }

                // dd($skor_rh);

                foreach ($list_all_will as $key => $value) {
                    array_multisort(array_column($list_all_will[$key], 'skor'), SORT_DESC, $list_all_will[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_will[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_will[$key], 'est_afd'), SORT_ASC, $list_all_will[$key]);
                }


                $list_all_est = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $list_all_est[$key][$inc]['est'] = $key2;
                        $list_all_est[$key][$inc]['skor'] = $value2['skor_akhir'];
                        $list_all_est[$key][$inc]['EM'] = 'EM';
                        foreach ($queryAsisten as $key4 => $value4) {
                            if ($value4->est == $key2 && $value4->afd == 'EM') {
                                $list_all_est[$key][$inc]['nama'] = $value4->nama;
                            }
                        }
                        if (empty($list_all_est[$key][$inc]['nama'])) {
                            $list_all_est[$key][$inc]['nama'] = '-';
                        }
                        $inc++;
                    }
                }

                foreach ($list_all_est as $key => $value) {
                    array_multisort(array_column($list_all_est[$key], 'skor'), SORT_DESC, $list_all_est[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_est[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_est[$key], 'est'), SORT_ASC, $list_all_est[$key]);
                }

                // dd($list_all_est);
                // dd($list_all_est);

                //untuk chart!!!
                foreach ($queryGroup as $key => $value) {
                    $sum_bt_tph = 0;
                    $sum_bt_jalan = 0;
                    $sum_bt_bin = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    $jum_blok = 0;
                    $tot_brd = 0;

                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                            $jum_blok++;
                        }
                        $sum_bt_tph += $val->bt_tph;
                        $sum_bt_jalan += $val->bt_jalan;
                        $sum_bt_bin += $val->bt_bin;
                    }
                    $tot_brd = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                    $skor_brd = round($tot_brd / $jum_blok, 2);
                    $arrBtTPHperEst[$key] = $skor_brd;
                    // $arrBtTPHperEst[$key] = [
                    //     'skor_brd' => $skor_brd,
                    //     'jum_blok' => $jum_blok,
                    //     'tot_brd' => $tot_brd,
                    // ];
                }
                // dd($arrBtTPHperEst);
                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroup as $key => $value) {
                    $sum_jum_karung = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_jum_karung += $val->jum_karung;
                    }
                    $skor_brd = round($sum_jum_karung / $jum_blok, 2);
                    $arrKRest[$key] = $skor_brd;
                }
                //looping buah tinggal
                foreach ($queryGroup as $key => $value) {
                    $sum_buah_tinggal = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_buah_tinggal += $val->buah_tinggal;
                    }
                    $skor_brd = round($sum_buah_tinggal / $jum_blok, 2);
                    $arrBHest[$key] = $skor_brd;
                }
                //looping buah restrant tidak di  laporkan
                foreach ($queryGroup as $key => $value) {
                    $sum_restan_unreported = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_restan_unreported += $val->restan_unreported;
                    }
                    $skor_brd = round($sum_restan_unreported / $jum_blok, 2);
                    $arrRSest[$key] = $skor_brd;
                }

                //query untuk wilayah menambhakna data
                //jadikan dulu query dalam group memakai data querry untuk wilayah
                $queryGroupWil = $query->groupBy(function ($item) {
                    return $item->wil;
                });

                // dd($queryGroupWil);
                foreach ($queryGroupWil as $key => $value) {
                    $sum_bt_tph = 0;
                    foreach ($value as $key2 => $val) {
                        $sum_bt_tph += $val->bt_tph;
                    }
                    // if ($key == 1 || $key == 2 || $key == 3) {
                    if ($skor_gm_wil[$key]['blok'] != 0) {
                        $arrBtTPHperWil[$key] = round($sum_bt_tph / $skor_gm_wil[$key]['blok'], 2);
                    } else {
                        $arrBtTPHperWil[$key] = 0;
                    }
                    // }
                }

                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroupWil as $key => $value) {
                    $sum_jum_karung = 0;
                    foreach ($value as $key2 => $vale) {
                        $sum_jum_karung += $vale->jum_karung;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrKRestWil[$key] = round($sum_jum_karung / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrKRestWil[$key] = 0;
                        }
                    }
                }
                //looping buah tinggal
                foreach ($queryGroupWil as $key => $value) {
                    $sum_buah_tinggal = 0;
                    foreach ($value as $key2 => $val2) {
                        $sum_buah_tinggal += $val2->buah_tinggal;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrBHestWil[$key] = round($sum_buah_tinggal / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrBHestWil[$key] = 0;
                        }
                    }
                }
                foreach ($queryGroupWil as $key => $value) {
                    $sum_restan_unreported = 0;
                    foreach ($value as $key2 => $val3) {
                        $sum_restan_unreported += $val3->restan_unreported;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrRestWill[$key] = round($sum_restan_unreported / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrRestWill[$key] = 0;
                        }
                    }
                }
            }
            // dd($arrBtTPHperWil, $arrKRestWil, $arrBHestWil, $arrRestWill);
            // dd($queryGroup);

            //bagian plasma cuy
            $QueryPlasmaSIdak = DB::connection('mysql2')
                ->table('sidak_tph')
                ->select('sidak_tph.*', DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'))
                ->whereBetween('sidak_tph.datetime', [$firstWeek, $lastWeek])
                ->get();
            $QueryPlasmaSIdak = $QueryPlasmaSIdak->groupBy(['est', 'afd']);
            $QueryPlasmaSIdak = json_decode($QueryPlasmaSIdak, true);
            // dd($QueryPlasmaSIdak['Plasma1']);
            $getPlasma = 'Plasma' . $regSidak;
            $queryEstePla = DB::connection('mysql2')
                ->table('estate')
                ->select('estate.*')
                ->whereIn('estate.est', [$getPlasma])
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('wil.regional', $regSidak)
                ->get();
            $queryEstePla = json_decode($queryEstePla, true);

            $queryAsisten = DB::connection('mysql2')
                ->Table('asisten_qc')
                ->get();
            $queryAsisten = json_decode($queryAsisten, true);

            $PlasmaAfd = DB::connection('mysql2')
                ->table('afdeling')
                ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
                ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
                ->get();
            $PlasmaAfd = json_decode($PlasmaAfd, true);

            $SidakTPHPlA = [];
            foreach ($QueryPlasmaSIdak as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        $SidakTPHPlA[$key][$key2][$key3] = $value3;
                    }
                }
            }
            // dd($SidakTPHPlA);
            $defPLASidak = [];
            foreach ($queryEstePla as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defPLASidak[$est['est']][$afd['nama']]['null'] = 0;
                    }
                }
            }

            foreach ($defPLASidak as $key => $estValue) {
                foreach ($estValue as $monthKey => $monthValue) {
                    $mergedValues = [];
                    foreach ($SidakTPHPlA as $dataKey => $dataValue) {
                        if ($dataKey == $key && isset($dataValue[$monthKey])) {
                            $mergedValues = array_merge($mergedValues, $dataValue[$monthKey]);
                        }
                    }
                    $defPLASidak[$key][$monthKey] = $mergedValues;
                }
            }

            $arrPlasma = [];
            foreach ($defPLASidak as $key => $value) {
                if (!empty($value)) {
                    $jum_blokPla = 0;
                    $sum_tphPla = 0;
                    $sum_karungPla = 0;
                    $sum_buahPla = 0;
                    $sum_restantPla = 0;

                    $skor_tphPla = 0;
                    $skor_karungPla = 0;
                    $skor_buahPla = 0;
                    $skor_restanPla = 0;
                    $skoreTotalPla = 0;
                    foreach ($value as $key1 => $value1) {
                        if (!empty($value1)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value1 as $key2 => $value2) {
                                if (is_array($value2)) {
                                    $blok = $value2['est'] . ' ' . $value2['afd'] . ' ' . $value2['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value2['bt_tph'];
                                    $sum_bt_jalan += $value2['bt_jalan'];
                                    $sum_bt_bin += $value2['bt_bin'];

                                    $sum_jum_karung += $value2['jum_karung'];
                                    $sum_buah_tinggal += $value2['buah_tinggal'];
                                    $sum_restan_unreported += $value2['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            if ($jum_blok == 0) {
                                $skor_brd = 0;
                                $skor_kr = 0;
                                $skor_buahtgl = 0;
                                $skor_restan = 0;
                            } else {
                                $skor_brd = round($sum_all / $jum_blok, 1);
                                $skor_kr = round($sum_all_karung / $jum_blok, 1);
                                $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                                $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);
                            }

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $arrPlasma[$key][$key1]['karung_tes'] = $sum_all_karung;
                            $arrPlasma[$key][$key1]['tph_test'] = $sum_all;
                            $arrPlasma[$key][$key1]['buah_test'] = $sum_all_bt_tgl;
                            $arrPlasma[$key][$key1]['restant_tes'] = $sum_all_restan_unreported;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = $jum_blok;

                            $arrPlasma[$key][$key1]['brd_blok'] = skorBRDsidak($skor_brd);
                            $arrPlasma[$key][$key1]['kr_blok'] = skorKRsidak($skor_kr);
                            $arrPlasma[$key][$key1]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $arrPlasma[$key][$key1]['restan_blok'] = skorRSsidak($skor_restan);
                            $arrPlasma[$key][$key1]['skorWil'] = $skoreTotal;

                            $jum_blokPla += $jum_blok;
                            $sum_karungPla += $sum_all_karung;
                            $sum_restantPla += $sum_all_restan_unreported;
                            $sum_tphPla += $sum_all;
                            $sum_buahPla += $sum_all_bt_tgl;
                        } else {
                            $arrPlasma[$key][$key1]['karung_tes'] = 0;
                            $arrPlasma[$key][$key1]['tph_test'] = 0;
                            $arrPlasma[$key][$key1]['buah_test'] = 0;
                            $arrPlasma[$key][$key1]['restant_tes'] = 0;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = 0;

                            $arrPlasma[$key][$key1]['brd_blok'] = 0;
                            $arrPlasma[$key][$key1]['kr_blok'] = 0;
                            $arrPlasma[$key][$key1]['buah_blok'] = 0;
                            $arrPlasma[$key][$key1]['restan_blok'] = 0;
                            $arrPlasma[$key][$key1]['skorWil'] = 0;
                        }
                    }

                    if ($jum_blokPla != 0) {
                        $skor_tphPla = round($sum_tphPla / $jum_blokPla, 2);
                        $skor_karungPla = round($sum_karungPla / $jum_blokPla, 2);
                        $skor_buahPla = round($sum_buahPla / $jum_blokPla, 2);
                        $skor_restanPla = round($sum_restantPla / $jum_blokPla, 2);
                    } else {
                        $skor_tphPla = 0;
                        $skor_karungPla = 0;
                        $skor_buahPla = 0;
                        $skor_restanPla = 0;
                    }

                    $skoreTotalPla = skorBRDsidak($skor_tphPla) + skorKRsidak($skor_karungPla) + skorBHsidak($skor_buahPla) + skorRSsidak($skor_restanPla);
                    if (
                        $jum_blokPla == 0 &&
                        $sum_karungPla == 0 &&
                        $sum_restantPla == 0 &&
                        $sum_tphPla == 0 &&
                        $sum_buahPla == 0
                    ) {
                        $arrPlasma[$key]['SkorPlasma'] = 0;
                    } else {
                        $arrPlasma[$key]['SkorPlasma'] = $skoreTotalPla;
                    }

                    $arrPlasma[$key]['karung_tes'] = $sum_karungPla;
                    $arrPlasma[$key]['tph_test'] = $sum_tphPla;
                    $arrPlasma[$key]['buah_test'] = $sum_buahPla;
                    $arrPlasma[$key]['restant_tes'] = $sum_restantPla;

                    $arrPlasma[$key]['jumlah_blok'] = $jum_blokPla;

                    $arrPlasma[$key]['brd_blok'] = skorBRDsidak($skor_tphPla);
                    $arrPlasma[$key]['kr_blok'] = skorKRsidak($skor_karungPla);
                    $arrPlasma[$key]['buah_blok'] = skorBHsidak($skor_buahPla);
                    $arrPlasma[$key]['restan_blok'] = skorRSsidak($skor_restanPla);
                }
            }
            // dd($arrPlasma);
            foreach ($arrPlasma as $key1 => $estates) {
                if (is_array($estates)) {
                    // $sortedData = array();
                    $sortedDataEst = [];
                    foreach ($estates as $estateName => $data) {
                        // dd($data);
                        if (is_array($data)) {
                            $sortedDataEst[] = [
                                'key1' => $key1,
                                'estateName' => $estateName,
                                'data' => $data,
                            ];
                        }
                    }
                    usort($sortedDataEst, function ($a, $b) {
                        return $b['data']['skorWil'] - $a['data']['skorWil'];
                    });
                    $rank = 1;
                    foreach ($sortedDataEst as $sortedest) {
                        $arrPlasma[$key1][$sortedest['estateName']]['rank'] = $rank;
                        $rank++;
                    }
                    unset($sortedDataEst);
                }
            }
            // dd($arrPlasma);

            $PlasmaWIl = [];
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) {
                            // dd($value1);
                            $inc = 0;
                            $est = $key;
                            $skor = $value1['skorWil'];
                            // dd($skor);
                            $EM = $key1;

                            $rank = $value1['rank'];
                            // $rank = $value1['rank'];
                            $nama = '-';
                            foreach ($queryAsisten as $key4 => $value4) {
                                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                                    $nama = $value4['nama'];
                                    break;
                                }
                            }
                            $PlasmaWIl[] = [
                                'est' => $est,
                                'afd' => $EM,
                                'nama' => $nama,
                                'skor' => $skor,
                                'rank' => $rank,
                            ];
                            $inc++;
                        }
                    }
                }
            }

            $PlasmaWIl = array_values($PlasmaWIl);

            $PlasmaEM = [];
            $NamaEm = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $EM = 'EM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                            $NamaEm = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($EM)) {
                $PlasmaEM[] = [
                    'est' => $est,
                    'afd' => $EM,
                    'namaEM' => $NamaEm,
                    'Skor' => $skor,
                ];
            }

            $PlasmaEM = array_values($PlasmaEM);

            $plasmaGM = [];
            $namaGM = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $GM = 'GM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $GM) {
                            $namaGM = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($GM)) {
                $plasmaGM[] = [
                    'est' => $est,
                    'afd' => $GM,
                    'namaGM' => $namaGM,
                    'Skor' => $skor,
                ];
            }

            $plasmaGM = array_values($plasmaGM);
            //masukan semua yang sudah selese di olah di atas ke dalam vaiabel terserah kemudian masukan kedalam aray
            //karena chart hanya bisa menerima inputan json
            $queryWilChart = DB::connection('mysql2')
                ->table('wil')
                ->whereIn('regional', [$regSidak])
                ->pluck('nama');

            //masukan semua yang sudah selese di olah di atas ke dalam vaiabel terserah kemudian masukan kedalam aray
            //karena chart hanya bisa menerima inputan json
            $queryWilChart = DB::connection('mysql2')
                ->table('wil')
                ->whereIn('regional', [$regSidak])
                ->pluck('nama');
            $list_all_will = changeKTE4ToKTE($list_all_will);
            // dd($result);


            $list_all_est = BpthKTE($list_all_est);
            // dd($result);


            $queryWill = list_wil($queryWill);
            $arrView = [];

            // $arrView['list_estate'] = $queryEst;
            $arrView['list_wilayah'] = $queryWill;
            $arrView['list_wilayah2'] = $queryWilChart;
            // $arrView['restant'] = $dataSkorAwalRestant;

            $arrView['list_all_wil'] = $list_all_will;
            $arrView['list_all_est'] = $list_all_est;
            $arrView['list_skor_gm'] = $skor_gm_wil;
            $arrView['list_skor_rh'] = $skor_rh;
            $arrView['PlasmaWIl'] = $PlasmaWIl;
            $arrView['PlasmaEM'] = $PlasmaEM;
            $arrView['plasmaGM'] = $plasmaGM;
            $arrView['list_skor_gmNew'] = $GmSkorWil;
            // $arrView['karung'] = $dataSkorAwalKr;
            // $arrView['buah'] = $dataSkorAwalBuah;
            // // dd($queryEst);
            // dd($arrBtTPHperEst);
            $keysToRemove = ['SRE', 'LDE', 'SKE', 'CWS1', 'CWS2', 'CWS3'];

            // Loop through the array and remove the elements with the specified keys
            foreach ($keysToRemove as $key) {
                unset($arrBtTPHperEst[$key]);
                unset($arrKRest[$key]);
                unset($arrBHest[$key]);
                unset($arrRSest[$key]);
            }
            $arrays = [
                &$arrBtTPHperEst,
                &$arrKRest,
                &$arrBHest,
                &$arrRSest,

            ];
            $insertAfterv2 = $regSidak == '1' ? "UPE" : ($regSidak == '2' ? "SCE" : "GDE");
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
            foreach ($arrays as &$array) {
                $plasmaKeys = preg_grep('/^Plasma/', array_keys($array));
                foreach ($plasmaKeys as $plasmaKey) {
                    moveElement($array, $plasmaKey, $insertAfterv2);
                }
            }
            $keys = array_keys($arrRSest);
            $keys = updateKeyRecursive2($keys);
            // masukan ke array data penjumlahan dari estate
            // dd($arrBtTPHperEst, $arrKRest, $arrBHest, $arrRSest, $keys);
            $arrView['estate_new'] = $keys;
            $arrView['val_bt_tph'] = $arrBtTPHperEst; //data jsen brondolan tinggal di tph
            $arrView['val_kr_tph'] = $arrKRest; //data jsen karung yang berisi buah
            $arrView['val_bh_tph'] = $arrBHest; //data jsen buah yang tinggal
            $arrView['val_rs_tph'] = $arrRSest; //data jsen restan yang tidak dilaporkan
            //masukan ke array data penjumlahan dari wilayah
            $arrView['val_kr_tph_wil'] = $arrKRestWil; //data jsen karung yang berisi buah
            $arrView['val_bt_tph_wil'] = $arrBtTPHperWil; //data jsen brondolan tinggal di tph
            $arrView['val_bh_tph_wil'] = $arrBHestWil; //data jsen buah yang tinggal
            $arrView['val_rs_tph_wil'] = $arrRestWill; //data jsen restan yang tidak dilaporkan
            // dd($arrBtTPHperEst);
            echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
            exit();
        }
    }

    public function getBtTphMonth(Request $request)
    {
        $regSidak = $request->get('reg');
        $monthSidak = $request->get('month');
        $queryWill = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->get();
        $queryReg = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('regional');
        $queryReg2 = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('id')
            ->toArray();

        // dapatkan data estate dari table estate dengan wilayah 1 , 2 , 3
        $queryEst = DB::connection('mysql2')
            ->table('estate')
            // ->whereNotIn('estate.est', ['PLASMA'])
            ->whereIn('wil', $queryReg2)
            ->get();
        // dd($queryEst);
        $queryEste = DB::connection('mysql2')
            ->table('estate')
            ->whereNotIn('estate.est', ['PLASMA', 'SRE', 'LDE', 'SKE', 'CWS1'])
            ->whereIn('wil', $queryReg2)
            ->get();
        $queryEste = $queryEste->groupBy(function ($item) {
            return $item->wil;
        });

        $queryEste = json_decode($queryEste, true);

        // dd($queryEst);
        $queryAfd = DB::connection('mysql2')
            ->Table('afdeling')
            ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE', 'CWS1'])
            ->get();

        $queryAfd = json_decode($queryAfd, true);

        //array untuk tampung nilai bt tph per estate dari table bt_jalan & bt_tph dll
        $arrBtTPHperEst = []; //table dari brondolan di buat jadi array agar bisa di parse ke json
        $arrKRest = []; //table dari jum_jkarung di buat jadi array agar bisa di parse ke json
        $arrBHest = []; //table dari Buah di buat jadi array agar bisa di parse ke json
        $arrRSest = []; //table array untuk buah restant tidak di laporkan

        ///array untuk table nya

        $dataSkorAwal = [];

        $list_all_will = [];

        //memberi nilai 0 default kesemua estate
        foreach ($queryEst as $key => $value) {
            $arrBtTPHperEst[$value->est] = 0; //est mengambil value dari table estate
            $arrKRest[$value->est] = 0;
            $arrBHest[$value->est] = 0;
            $arrRSest[$value->est] = 0;
        }
        // dd($queryEst);
        foreach ($queryWill as $key => $value) {
            $arrBtTPHperWil[$value->nama] = 0; //est mengambil value dari table estate
            $arrKRestWil[$value->nama] = 0;
            $arrBHestWil[$value->nama] = 0;
            $arrRestWill[$value->nama] = 0;
        }

        // dd($firstWeek, $lastWeek);
        $query = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.datetime', 'like', '%' . $monthSidak . '%')
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        //     ->get();
        $queryAFD = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.datetime', 'like', '%' . $monthSidak . '%')
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        // dd($query);
        $queryAsisten = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();

        $dataAfdEst = [];

        $querySidak = DB::connection('mysql2')
            ->table('sidak_tph')
            ->where('sidak_tph.datetime', 'like', '%' . $monthSidak . '%')
            ->orderBy('est', 'desc')
            ->orderBy('afd', 'desc')
            ->orderBy('blok', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();
        $querySidak = json_decode($querySidak, true);





        // new hitungan 




        $ancakFA = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select("sidak_tph.*", DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal')) // Change the format to "%Y-%m-%d"
            ->where('sidak_tph.datetime', 'like', '%' . $monthSidak . '%')
            ->orderBy('status', 'asc')
            ->get();

        $ancakFA = $ancakFA->groupBy(['est', 'afd', 'status', 'tanggal', 'blok']);
        $ancakFA = json_decode($ancakFA, true);

        $dateString = $monthSidak;
        $dateParts = date_parse($dateString);
        $year = $dateParts['year'];
        $month = $dateParts['month'];

        $year = $year; // Replace with the desired year
        $month = $month;   // Replace with the desired month (September in this example)


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



        $result = [];

        // Iterate through the original array
        foreach ($ancakFA as $mainKey => $mainValue) {
            $result[$mainKey] = [];

            foreach ($mainValue as $subKey => $subValue) {
                $result[$mainKey][$subKey] = [];

                foreach ($subValue as $dateKey => $dateValue) {
                    // Remove 'H+' prefix if it exists
                    $numericIndex = is_numeric($dateKey) ? $dateKey : (strpos($dateKey, 'H+') === 0 ? substr($dateKey, 2) : $dateKey);

                    if (!isset($result[$mainKey][$subKey][$numericIndex])) {
                        $result[$mainKey][$subKey][$numericIndex] = [];
                    }

                    foreach ($dateValue as $statusKey => $statusValue) {
                        // Handle 'H+' prefix in status
                        $statusIndex = is_numeric($statusKey) ? $statusKey : (strpos($statusKey, 'H+') === 0 ? substr($statusKey, 2) : $statusKey);

                        if (!isset($result[$mainKey][$subKey][$numericIndex][$statusIndex])) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex] = [];
                        }

                        foreach ($statusValue as $blokKey => $blokValue) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex][$blokKey] = $blokValue;
                        }
                    }
                }
            }
        }

        // result by statis week 
        $newResult = [];

        foreach ($result as $key => $value) {
            $newResult[$key] = [];

            foreach ($value as $estKey => $est) {
                $newResult[$key][$estKey] = [];

                foreach ($est as $statusKey => $status) {
                    $newResult[$key][$estKey][$statusKey] = [];

                    foreach ($weeks as $weekKey => $week) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $newResult[$key][$estKey][$statusKey]["week" . ($weekKey + 1)] = $newStatus;
                        }
                    }
                }
            }
        }

        // result by week status 
        $WeekStatus = [];

        foreach ($result as $key => $value) {
            $WeekStatus[$key] = [];

            foreach ($value as $estKey => $est) {
                $WeekStatus[$key][$estKey] = [];

                foreach ($weeks as $weekKey => $week) {
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



        $qrafd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $qrafd = json_decode($qrafd, true);
        $queryEstereg = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereNotIn('estate.est', ['PLASMA', 'SRE', 'LDE', 'SKE', 'CWS1', 'SRS'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->where('wil.regional', $regSidak)
            ->get();
        $queryEstereg = json_decode($queryEstereg, true);

        // dd($queryEstereg);
        $defaultNew = array();

        foreach ($queryEstereg as $est) {
            foreach ($qrafd as $afd) {
                if ($est['est'] == $afd['est']) {
                    $defaultNew[$est['est']][$afd['nama']] = 0;
                }
            }
        }



        foreach ($defaultNew as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($newResult as $dataKey => $dataValue) {

                    if ($dataKey == $key) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {

                            if ($dataEstKey == $monthKey) {
                                $defaultNew[$key][$monthKey] = $dataEstValue;
                            }
                        }
                    }
                }
            }
        }

        $defaultWeek = array();

        foreach ($queryEstereg as $est) {
            foreach ($qrafd as $afd) {
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




        $newDefaultWeek = [];

        foreach ($defaultWeek as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        foreach ($value1 as $subKey => $subValue) {
                            if (is_array($subValue)) {
                                // Check if both key 0 and key 1 exist
                                $hasKeyZero = isset($subValue[0]);
                                $hasKeyOne = isset($subValue[1]);

                                // Merge key 0 into key 1
                                if ($hasKeyZero && $hasKeyOne) {
                                    $subValue[1] = array_merge_recursive((array)$subValue[1], (array)$subValue[0]);
                                    unset($subValue[0]);
                                } elseif ($hasKeyZero && !$hasKeyOne) {
                                    // Create key 1 and merge key 0 into it
                                    $subValue[1] = $subValue[0];
                                    unset($subValue[0]);
                                }

                                // Check if keys 1 through 7 don't exist, add them with a default value of 0
                                for ($i = 1; $i <= 7; $i++) {
                                    if (!isset($subValue[$i])) {
                                        $subValue[$i] = 0;
                                    }
                                }

                                // Ensure key 8 exists, and if not, create it with a default value of an empty array
                                if (!isset($subValue[8])) {
                                    $subValue[8] = 0;
                                }

                                // Check if keys higher than 8 exist, merge them into index 8
                                for ($i = 9; $i <= 100; $i++) {
                                    if (isset($subValue[$i])) {
                                        $subValue[8] = array_merge_recursive((array)$subValue[8], (array)$subValue[$i]);
                                        unset($subValue[$i]);
                                    }
                                }
                            }
                            $newDefaultWeek[$key][$key1][$subKey] = $subValue;
                        }
                    } else {
                        // Check if $value1 is equal to 0 and add "week1" to "week5" keys
                        if ($value1 === 0) {
                            $newDefaultWeek[$key][$key1] = [];
                            for ($i = 1; $i <= 5; $i++) {
                                $weekKey = "week" . $i;
                                $newDefaultWeek[$key][$key1][$weekKey] = [];
                                for ($j = 1; $j <= 8; $j++) {
                                    $newDefaultWeek[$key][$key1][$weekKey][$j] = 0;
                                }
                            }
                        } else {
                            $newDefaultWeek[$key][$key1] = $value1;
                        }
                    }
                }
            } else {
                $newDefaultWeek[$key] = $value;
            }
        }
        // dd($newDefaultWeek['Plasma1']['WIL-III']);
        // dd($newDefaultWeek);

        function removeZeroFromDatetime(&$array)
        {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => &$value2) {
                        if (is_array($value2)) {
                            foreach ($value2 as $key2 => &$value3) {
                                if (is_array($value3)) {
                                    foreach ($value3 as $key3 => &$value4) if (is_array($value4)) {
                                        foreach ($value4 as $key4 => $value5) {
                                            if ($key4 === 0 && $value5 === 0) {
                                                unset($value4[$key4]); // Unset the key 0 => 0 within the current nested array
                                            }
                                            removeZeroFromDatetime($value4);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        removeZeroFromDatetime($newDefaultWeek);

        function filterEmptyWeeks2(&$array)
        {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    filterEmptyWeeks2($value); // Recursively check nested arrays
                    if (empty($value) && $key !== 'week') {
                        unset($array[$key]);
                    }
                }
            }
        }

        // dd($defaultWeek);
        // Call the function on your array
        filterEmptyWeeks2($defaultWeek);


        // dd($defaultWeek);
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
        $asisten_qc = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();
        $asisten_qc = json_decode($asisten_qc, true);
        foreach ($newDefaultWeek as $key => $value) {
            $dividen_afd = 0;
            $total_skoreest = 0;
            $tot_estAFd = 0;
            $new_dvdAfd = 0;
            $new_dvdAfdest = 0;
            $total_estkors = 0;
            $total_skoreafd = 0;
            foreach ($value as $key1 => $value2)  if (is_array($value2)) {
                $deviden = 0;
                $tot_afdscore = 0;
                $totskor_brd1 = 0;
                $totskor_janjang1 = 0;
                $total_skoreest = 0;
                foreach ($value2 as $key2 => $value3) {
                    $total_brondolan = 0;
                    $total_janjang = 0;
                    $tod_brd = 0;
                    $tod_jjg = 0;
                    $totskor_brd = 0;
                    $totskor_janjang = 0;
                    $tot_brdxm = 0;
                    $tod_janjangxm = 0;
                    foreach ($value3 as $key3 => $value4) if (is_array($value4)) {
                        $tph1 = 0;
                        $jalan1 = 0;
                        $bin1 = 0;
                        $karung1 = 0;
                        $buah1 = 0;
                        $restan1 = 0;
                        foreach ($value4 as $key4 => $value5) if (is_array($value5)) {
                            $tph = 0;
                            $jalan = 0;
                            $bin = 0;
                            $karung = 0;
                            $buah = 0;
                            $restan = 0;
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

                            $tph1 += $tph;
                            $jalan1 += $jalan;
                            $bin1 += $bin;
                            $karung1 += $karung;
                            $buah1 += $buah;
                            $restan1 += $restan;
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

                        $totskor_brd += $total_brondolan;
                        $totskor_janjang += $total_janjang;
                        $tot_brdxm += $tod_brd;
                        $tod_janjangxm += $tod_jjg;
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
                    }


                    $total_estkors = $totskor_brd + $totskor_janjang;
                    if ($total_estkors != 0) {
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

                    $totskor_brd1 += $totskor_brd;
                    $totskor_janjang1 += $totskor_janjang;
                    $total_skoreest += $total_skoreafd;
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


                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }

                $deviden = count($value2);
                $new_dvd = $dividen_x;
                $new_dvdest = $devidenEst_x;
                if ($new_dvd != 0) {
                    $tot_afdscore = round($total_skoreest / $new_dvd, 1);
                } else {
                    $tot_afdscore = 0;  # code...
                }

                // $newSidak[$key][$key1]['deviden'] = $deviden;
                $newSidak[$key][$key1]['total_score'] = $tot_afdscore;
                $newSidak[$key][$key1]['total_brd'] = $totskor_brd1;
                $newSidak[$key][$key1]['total_janjang'] = $totskor_janjang1;
                $newSidak[$key][$key1]['new_deviden'] = $new_dvd;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
                $newSidak[$key][$key1]['total_skor'] = $total_skoreest;

                $tot_estAFd += $tot_afdscore;
                $new_dvdAfd += $new_dvd;
                $new_dvdAfdest += $new_dvdest;
            } else {
                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {

                    // dd($asisten);
                    if ($asisten['est'] == $key && $asisten['afd'] == $key1) {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $newSidak[$key][$key1]['deviden'] = 0;
                $newSidak[$key][$key1]['total_score'] = 0;
                $newSidak[$key][$key1]['total_brd'] = 0;
                $newSidak[$key][$key1]['total_janjang'] = 0;
                $newSidak[$key][$key1]['asisten'] = $namaGM;
            }

            $dividen_afd = count($value);
            if ($new_dvdAfdest != 0) {
                $total_skoreest = round($tot_estAFd / $new_dvdAfdest, 1);
            } else {
                $total_skoreest = 0;
            }

            // dd($value);

            $namaGM = '-';
            foreach ($asisten_qc as $asisten) {
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
        }


        // dd($newSidak);
        $mtancakWIltab1 = array();
        foreach ($queryEstereg as $key => $value) {
            foreach ($newSidak as $key2 => $value2) {
                if ($value['est'] == $key2) {
                    $mtancakWIltab1[$value['wil']][$key2] = array_merge($mtancakWIltab1[$value['wil']][$key2] ?? [], $value2);
                }
            }
        }

        $plasmaArray = [];

        foreach ($mtancakWIltab1 as $key => $innerArray) {
            foreach ($innerArray as $innerKey => $innerValue) {
                if (strpos($innerKey, "Plasma") !== false) {
                    // Move the inner array with "Plasma" key to the "Plasma" key
                    $plasmaArray["Plasma"] = $innerValue;
                    unset($mtancakWIltab1[$key][$innerKey]); // Remove the "Plasma" key
                }
            }
        }

        // Add the "Plasma" array to the original array
        if (!empty($plasmaArray)) {
            $mtancakWIltab1["Plasma"] = $plasmaArray;
        }
        // final untuk penampilan afdeling 

        // dd($mtancakWIltab1);
        $resultafd1 = array();

        $keysToIterate = [1, 4, 7, 10]; // Define the keys you want to iterate through

        foreach ($keysToIterate as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) { // Check if $value1 is an array
                            $est = $key;
                            $afd = $key1;

                            $total_score = $value1['total_score'];
                            $asisten = $value1['asisten'];

                            // Create a new array for each iteration
                            $resultafd1[] = array(
                                'est' => $est,
                                'afd' => $afd,
                                'skor' => $total_score,
                                'asisten' => $asisten,
                                'ranking' => null, // Placeholder for ranking
                            );
                        }
                    }
                }
            }
        }

        // Create a copy of the array to preserve the original order
        $sortedArray = $resultafd1;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedArray, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $rank = 1;
        $prevScore = null;

        foreach ($sortedArray as &$item) {
            if ($prevScore === null || $prevScore != $item['skor']) {
                $item['ranking'] = $rank;
            }
            $prevScore = $item['skor'];
            $rank++;
        }

        // Merge the ranked data back into the original order
        foreach ($resultafd1 as &$item) {
            foreach ($sortedArray as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor']) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }
        // dd($resultafd1);



        $resultafd2 = array();

        $keys2 = [2, 5, 8, 11]; // Define the keys you want to iterate through

        foreach ($keys2 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) { // Check if $value1 is an array
                            $est = $key;
                            $afd = $key1;

                            $total_score = $value1['total_score'];
                            $asisten = $value1['asisten'];

                            // Create a new array for each iteration
                            $resultafd2[] = array(
                                'est' => $est,
                                'afd' => $afd,
                                'skor' => $total_score,
                                'asisten' => $asisten,
                                'ranking' => null, // Placeholder for ranking
                            );
                        }
                    }
                }
            }
        }
        $sortedArray1 = $resultafd2;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedArray1, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        $rank2 = 1;
        $prevScore2 = null;

        foreach ($sortedArray1 as &$item) {
            if ($prevScore2 === null || $prevScore2 != $item['skor']) {
                $item['ranking'] = $rank2;
            }
            $prevScore2 = $item['skor'];
            $rank2++;
        }

        // Merge the ranked data back into the original order
        foreach ($resultafd2 as &$item) {
            foreach ($sortedArray1 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor']) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }

        // dd($resultafd2);

        $resultafd3 = array();

        $keys3 = [3, 6]; // Define the keys you want to iterate through

        foreach ($keys3 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) { // Check if $value1 is an array
                            $est = $key;
                            $afd = $key1;

                            $total_score = $value1['total_score'];
                            $asisten = $value1['asisten'];

                            // Create a new array for each iteration
                            $resultafd3[] = array(
                                'est' => $est,
                                'afd' => $afd,
                                'skor' => $total_score,
                                'asisten' => $asisten,
                                'ranking' => null, // Placeholder for ranking
                            );
                        }
                    }
                }
            }
        }
        $sortedArray2 = $resultafd3;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedArray2, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $rank3 = 1;
        $prevScore3 = null;

        foreach ($sortedArray2 as &$item) {
            if ($prevScore3 === null || $prevScore3 != $item['skor']) {
                $item['ranking'] = $rank3;
            }
            $prevScore3 = $item['skor'];
            $rank3++;
        }

        // Merge the ranked data back into the original order
        foreach ($resultafd3 as &$item) {
            foreach ($sortedArray2 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor']) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }

        $resultafd4 = array();

        $keys4 = ['Plasma']; // Define the keys you want to iterate through

        foreach ($keys4 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) { // Check if $value1 is an array
                            $est = $key;
                            $afd = $key1;

                            $total_score = $value1['total_score'];
                            $asisten = $value1['asisten'];

                            // Create a new array for each iteration
                            $resultafd4[] = array(
                                'est' => $est,
                                'afd' => $afd,
                                'skor' => $total_score,
                                'asisten' => $asisten,
                                'ranking' => null, // Placeholder for ranking
                            );
                        }
                    }
                }
            }
        }

        $sortedArray4 = $resultafd4;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedArray4, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $rank4 = 1;
        $prevScore4 = null;

        foreach ($sortedArray4 as &$item) {
            if ($prevScore4 === null || $prevScore4 != $item['skor']) {
                $item['ranking'] = $rank4;
            }
            $prevScore4 = $item['skor'];
            $rank4++;
        }

        // Merge the ranked data back into the original order
        foreach ($resultafd4 as &$item) {
            foreach ($sortedArray4 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor']) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }
        // dd($resultafd1, $resultafd2, $resultafd3, $resultafd4, $mtancakWIltab1);

        // table per estate 

        $resultest1 = array();

        $keyEst = [1, 4, 7, 10]; // Define the keys you want to iterate through

        foreach ($keyEst as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                $estateScore = 0;
                $diveden = 0;
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    // dd($value);
                    if (is_array($value)) { // Check if $value1 is an array
                        $est = $key;
                        $afd = $key1;

                        $total_score = $value['score_estate'];
                        $asisten = $value['asisten'];
                        $estateScore += $value['score_estate'];
                        $diveden += $value['deviden'];

                        if ($diveden != 0) {
                            $totalEst = round($estateScore / $diveden, 2);
                        } else {
                            $totalEst = 0;
                        }

                        // Create a new array for each iteration
                        $resultest1[] = array(
                            'est' => $est,
                            'afd' => 'EM',
                            'skor' => $total_score,
                            'asisten' => $asisten,
                            'ranking' => null,
                        );
                    }
                }
                if ($keyToIterate == 1) {
                    $newKey = 'WIL-I';
                } elseif ($keyToIterate == 4) {
                    $newKey = 'WIL-IV';
                } elseif ($keyToIterate == 7) {
                    $newKey = 'WIL-VII';
                } elseif ($keyToIterate == 10) {
                    $newKey = 'WIL-IX';
                } else {
                    $newKey = 'WIL-';
                }

                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {
                    if ($asisten['est'] == $newKey && $asisten['afd'] == 'GM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $resultest1[] = array(
                    'afd' => 'GM',
                    'est' => $newKey,
                    'skor' => $totalEst,
                    'asisten' => $namaGM,
                    'ranking' => '-',
                    'est_score' => $estateScore,
                    'dividen' => $diveden
                );
            }
        }


        // Create a copy of the array to preserve the original order
        $sortedest1 = $resultest1;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedest1, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $est1 = 1;
        $prevEst1 = null;

        foreach ($sortedest1 as &$item) {
            // Check if 'est_score' and 'dividen' are present in the array
            if (!isset($item['est_score']) && !isset($item['dividen'])) {
                if ($prevEst1 === null || $prevEst1 != $item['skor']) {
                    $item['ranking'] = $est1;
                }
                $prevEst1 = $item['skor'];
                $est1++;
            }
        }

        // Merge the ranked data back into the original order
        foreach ($resultest1 as &$item) {
            foreach ($sortedest1 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor'] && !isset($item['est_score']) && !isset($item['dividen'])) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }


        // dd($resultest1);
        $resultest2 = array();
        $keyEst2 = [2, 5, 8, 11];

        foreach ($keyEst2 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                $estateScore = 0;
                $diveden = 0;
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    // dd($value);
                    if (is_array($value)) { // Check if $value1 is an array
                        $est = $key;
                        $afd = $key1;

                        $total_score = $value['score_estate'];
                        $asisten = $value['asisten'];
                        $estateScore += $value['score_estate'];
                        $diveden += $value['deviden'];

                        if ($diveden != 0) {
                            $totalEst = round($estateScore / $diveden, 2);
                        } else {
                            $totalEst = 0;
                        }

                        // Create a new array for each iteration
                        $resultest2[] = array(
                            'est' => $est,
                            'afd' => 'EM',
                            'skor' => $total_score,
                            'asisten' => $asisten,
                            'ranking' => null,
                        );
                    }
                }
                if ($keyToIterate == 2) {
                    $newKey = 'WIL-II';
                } elseif ($keyToIterate == 5) {
                    $newKey = 'WIL-V';
                } elseif ($keyToIterate == 8) {
                    $newKey = 'WIL-VIII';
                } elseif ($keyToIterate == 11) {
                    $newKey = 'WIL-XI';
                } else {
                    $newKey = 'WIL-';
                }

                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {
                    if ($asisten['est'] == $newKey && $asisten['afd'] == 'GM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $resultest2[] = array(
                    'afd' => 'GM',
                    'est' =>  $newKey, // Concatenate $keyEst here
                    'skor' => $totalEst,
                    'asisten' => $namaGM,
                    'ranking' => '-',
                    'est_score' => $estateScore,
                    'dividen' => $diveden
                );
            }
        }

        $sortedest2 = $resultest2;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedest2, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $est2 = 1;
        $prevEst2 = null;

        foreach ($sortedest2 as &$item) {
            // Check if 'est_score' and 'dividen' are present in the array
            if (!isset($item['est_score']) && !isset($item['dividen'])) {
                if ($prevEst2 === null || $prevEst2 != $item['skor']) {
                    $item['ranking'] = $est2;
                }
                $prevEst2 = $item['skor'];
                $est2++;
            }
        }

        // Merge the ranked data back into the original order
        foreach ($resultest2 as &$item) {
            foreach ($sortedest2 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor'] && !isset($item['est_score']) && !isset($item['dividen'])) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }


        // dd($resultest2);

        $resultest3 = array();

        $keyEst3 = [3, 6]; // Define the keys you want to iterate through

        foreach ($keyEst3 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                $estateScore = 0;
                $diveden = 0;
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    // dd($value);
                    if (is_array($value)) { // Check if $value1 is an array
                        $est = $key;
                        $afd = $key1;

                        $total_score = $value['score_estate'];
                        $asisten = $value['asisten'];
                        $estateScore += $value['score_estate'];
                        $diveden += $value['deviden'];

                        if ($diveden != 0) {
                            $totalEst = round($estateScore / $diveden, 2);
                        } else {
                            $totalEst = 0;
                        }

                        // Create a new array for each iteration
                        $resultest3[] = array(
                            'est' => $est,
                            'afd' => 'EM',
                            'skor' => $total_score,
                            'asisten' => $asisten,
                            'ranking' => null,
                        );
                    }
                }
                if ($keyToIterate == 3) {
                    $newKey = 'WIL-III';
                } elseif ($keyToIterate == 6) {
                    $newKey = 'WIL-VI';
                } else {
                    $newKey = 'WIL-';
                }

                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {
                    if ($asisten['est'] == $newKey && $asisten['afd'] == 'GM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $resultest3[] = array(
                    'afd' => 'GM',
                    'est' =>  $newKey, // Concatenate $keyEst here
                    'skor' => $totalEst,
                    'asisten' => $namaGM,
                    'ranking' => '-',
                    'est_score' => $estateScore,
                    'dividen' => $diveden
                );
            }
        }
        // Create a copy of the array to preserve the original order
        $sortedest3 = $resultest3;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedest3, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $est33 = 1;
        $prevEst3 = null;

        foreach ($sortedest3 as &$item) {
            // Check if 'est_score' and 'dividen' are present in the array
            if (!isset($item['est_score']) && !isset($item['dividen'])) {
                if ($prevEst3 === null || $prevEst3 != $item['skor']) {
                    $item['ranking'] = $est33;
                }
                $prevEst2 = $item['skor'];
                $est33++;
            }
        }
        // Merge the ranked data back into the original order
        foreach ($resultest3 as &$item) {
            foreach ($sortedest3 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor'] && !isset($item['est_score']) && !isset($item['dividen'])) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }



        $resultest4 = array();

        $keyEst4 = ['Plasma']; // Define the keys you want to iterate through

        foreach ($keyEst4 as $keyToIterate) {
            if (isset($mtancakWIltab1[$keyToIterate])) {
                $estateScore = 0;
                $diveden = 0;
                foreach ($mtancakWIltab1[$keyToIterate] as $key => $value) {
                    // dd($value);
                    if (is_array($value)) { // Check if $value1 is an array
                        $est = $key;
                        $afd = $key1;

                        $total_score = $value['score_estate'];
                        $asisten = $value['asisten'];
                        $estateScore += $value['score_estate'];
                        $diveden += $value['deviden'];

                        if ($diveden != 0) {
                            $totalEst = round($estateScore / $diveden, 2);
                        } else {
                            $totalEst = 0;
                        }

                        // Create a new array for each iteration
                        $resultest4[] = array(
                            'est' => $est,
                            'afd' => 'EM',
                            'skor' => $total_score,
                            'asisten' => $asisten,
                            'ranking' => null,
                        );
                    }
                }


                $namaGM = '-';
                foreach ($asisten_qc as $asisten) {
                    if ($asisten['est'] == $value['estate'] && $asisten['afd'] == 'GM') {
                        $namaGM = $asisten['nama'];
                        break;
                    }
                }
                $resultest4[] = array(
                    'afd' => 'GM',
                    'est' => 'WIL-' . $keyToIterate, // Concatenate $keyEst here
                    'skor' => $totalEst,
                    'asisten' => $namaGM,
                    'ranking' => '-',
                    'est_score' => $estateScore,
                    'dividen' => $diveden
                );
            }
        }
        // Create a copy of the array to preserve the original order
        $sortedest4 = $resultest4;

        // Sort the copy based on 'skor' (total_score) in descending order
        usort($sortedest4, function ($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Calculate rankings based on the sorted array
        $est4 = 1;
        $prevEst4 = null;
        foreach ($sortedest4 as &$item) {
            // Check if 'est_score' and 'dividen' are present in the array
            if (!isset($item['est_score']) && !isset($item['dividen'])) {
                if ($prevEst4 === null || $prevEst4 != $item['skor']) {
                    $item['ranking'] = $est4;
                }
                $prevEst2 = $item['skor'];
                $est4++;
            }
        }

        // Merge the ranked data back into the original order
        foreach ($resultest4 as &$item) {
            foreach ($sortedest4 as $sortedItem) {
                if ($sortedItem['skor'] == $item['skor'] && !isset($item['est_score']) && !isset($item['dividen'])) {
                    $item['ranking'] = $sortedItem['ranking'];
                    break;
                }
            }
        }
        // dd($resultest4, $mtancakWIltab1);
        $rhEstate = array();
        $total_rh = 0;
        $reg_finalskor = 0;
        $reg_devskor = 0;
        foreach ($mtancakWIltab1 as $key => $value) {
            $estateScore = 0;
            $diveden = 0;
            foreach ($value as $key1 => $value1) {
                $estateScore += $value1['score_estate'];

                // dd($value1);
                $diveden += $value1['deviden'];

                if ($diveden != 0) {
                    $totalEst = round($estateScore / $diveden, 2);
                } else {
                    $totalEst = 0;
                }
            }
            if ($diveden != 0) {
                $reg_est = $estateScore / $diveden;
                $div_reg = 1;
            } else {
                $reg_est = 0;
                $div_reg = 0;
            }

            $total_rh += $totalEst;


            $reg_finalskor += $reg_est;
            $reg_devskor += $div_reg;
        }

        // Create a new array for each iteration
        $rhEstate[] = array(
            'est' => 'REG-1',
            'jab' => 'RH',
            'nama' => '-',
            'total' => $reg_finalskor,
            'skor' => ($reg_devskor != 0) ? round($reg_finalskor / $reg_devskor, 1) : 0
        );

        // dd($rhEstate, $mtancakWIltab1);
        // dd($rhEstate, $mtancakWIltab1);
        // end new hitungan 

        $allBlok = $query->groupBy(function ($item) {
            return $item->blok;
        });

        if (!empty($query && $queryAFD && $querySidak)) {
            $queryGroup = $queryAFD->groupBy(function ($item) {
                return $item->est;
            });
            // dd($queryGroup);
            $queryWi = DB::connection('mysql2')
                ->table('estate')
                ->whereIn('wil', $queryReg2)
                ->get();

            $queryWill = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });

            $queryWill2 = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });
            // dd($querySidak);
            //untuk table!!
            // store wil -> est -> afd
            // menyimpan array nested dari  wil -> est -> afd
            foreach ($queryEste as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($queryAfd as $key3 => $value3) {
                        $est = $value2['est'];
                        $afd = $value3['nama'];
                        if ($value2['est'] == $value3['est']) {
                            foreach ($querySidak as $key4 => $value4) {
                                if ($est == $value4['est'] && $afd == $value4['afd']) {
                                    $dataAfdEst[$est][$afd][] = $value4;
                                } else {
                                    $dataAfdEst[$est][$afd]['null'] = 0;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        unset($dataAfdEst[$key][$key2]['null']);
                        if (empty($dataAfdEst[$key][$key2])) {
                            $dataAfdEst[$key][$key2] = 0;
                        }
                    }
                }
            }

            $listBlokPerAfd = [];
            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            // dd($key3);
                            foreach ($allBlok as $key4 => $value4) {
                                if ($value3['blok'] == $key4) {
                                    $listBlokPerAfd[$key][$key2][$key3] = $value4;
                                }
                            }
                        }
                    }
                }

                // dd($listBlokPerAfd);
                // //menghitung data skor untuk brd/blok
                foreach ($dataAfdEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value2 as $key3 => $value3) {
                                if (is_array($value3)) {
                                    $blok = $value3['est'] . ' ' . $value3['afd'] . ' ' . $value3['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value3['bt_tph'];
                                    $sum_bt_jalan += $value3['bt_jalan'];
                                    $sum_bt_bin += $value3['bt_bin'];

                                    $sum_jum_karung += $value3['jum_karung'];
                                    $sum_buah_tinggal += $value3['buah_tinggal'];
                                    $sum_restan_unreported += $value3['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            $skor_brd = round($sum_all / $jum_blok, 1);
                            // dd($skor_brd);
                            $skor_kr = round($sum_all_karung / $jum_blok, 1);
                            $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                            $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $dataSkorAwal[$key][$key2]['karung_tes'] = $sum_all_karung;
                            $dataSkorAwal[$key][$key2]['jum_blok'] = $jum_blok;
                            $dataSkorAwal[$key][$key2]['tph_test'] = $sum_all;
                            $dataSkorAwal[$key][$key2]['buah_test'] = $sum_all_bt_tgl;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = $sum_all_restan_unreported;

                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = $jum_blok;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = skorBRDsidak($skor_brd);
                            // $dataSkorAwal[$key][$key2]['brd'] = skorBRDsidak($skor_brd);
                            $dataSkorAwal[$key][$key2]['kr_blok'] = skorKRsidak($skor_kr);
                            $dataSkorAwal[$key][$key2]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $dataSkorAwal[$key][$key2]['restan_blok'] = skorRSsidak($skor_restan);
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = $skoreTotal;
                        } else {
                            $dataSkorAwal[$key][$key2]['karung_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['jum_blok'] = -0;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['tph_test'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_test'] = 0;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['kr_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['restan_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = 0;
                        }
                    }
                }
                // dd($newSidak, $dataSkorAwal);
                // dd($dataSkorAwal);

                foreach ($dataSkorAwal as $key => $value) {
                    $jum_blok = 0;
                    $jum_all_blok = 0;
                    $sum_all_tph = 0;
                    $sum_tph = 0;
                    $sum_all_karung = 0;
                    $sum_karung = 0;
                    $sum_all_buah = 0;
                    $sum_buah = 0;
                    $sum_all_restant = 0;
                    $sum_restant = 0;
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok += $value2['jumlah_blok'];
                            $sum_karung += $value2['karung_tes'];
                            $sum_restant += $value2['restant_tes'];
                            $sum_tph += $value2['tph_test'];
                            $sum_buah += $value2['buah_test'];
                        }
                    }
                    $sum_all_tph = $sum_tph;
                    $jum_all_blok = $jum_blok;
                    $sum_all_karung = $sum_karung;
                    $sum_all_buah = $sum_buah;
                    $sum_all_restant = $sum_restant;

                    if ($jum_all_blok != 0) {
                        $skor_tph = round($sum_all_tph / $jum_all_blok, 2);
                        $skor_karung = round($sum_all_karung / $jum_all_blok, 2);
                        $skor_buah = round($sum_all_buah / $jum_all_blok, 2);
                        $skor_restan = round($sum_all_restant / $jum_all_blok, 2);
                    } else {
                        $skor_tph = 0;
                        $skor_karung = 0;
                        $skor_buah = 0;
                        $skor_restan = 0;
                    }


                    $skoreTotal = skorBRDsidak($skor_tph) + skorKRsidak($skor_karung) + skorBHsidak($skor_buah) + skorRSsidak($skor_restan);

                    if (
                        $jum_blok == 0 &&
                        $sum_karung == 0 &&
                        $sum_restant == 0 &&
                        $sum_tph == 0 &&
                        $sum_buah == 0
                    ) {
                        $dataSkorAwaltest[$key]['skor_akhir'] = 0;
                    } else {
                        $dataSkorAwaltest[$key]['skor_akhir'] = $skoreTotal;
                    }
                    $dataSkorAwaltest[$key]['total_estate_brondol'] = $sum_all_tph;
                    $dataSkorAwaltest[$key]['total_estate_karung'] = $sum_all_karung;
                    $dataSkorAwaltest[$key]['total_estate_buah_tinggal'] = $sum_all_buah;
                    $dataSkorAwaltest[$key]['total_estate_restan_tinggal'] = $sum_all_restant;
                    $dataSkorAwaltest[$key]['tph'] = skorBRDsidak($skor_tph);
                    $dataSkorAwaltest[$key]['karung'] = skorKRsidak($skor_karung);
                    $dataSkorAwaltest[$key]['buah_tinggal'] = skorBHsidak($skor_buah);
                    $dataSkorAwaltest[$key]['restant'] = skorRSsidak($skor_restan);
                    $dataSkorAwaltest[$key]['total_blokokok'] = $jum_all_blok;
                }
                // dd($dataSkorAwaltest);

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwal as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWil[$key][$key3] = $value3;
                            }
                        }
                    }
                }

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwaltest as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWilEst[$key][$key3] = $value3;
                            }
                        }
                    }
                }
                // dd($dataSkorAkhirPerWil);
                //menshort nilai masing masing
                $sortList = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $sortList[$key][$key2 . '_' . $key3] = $value3['skore_akhir'];
                            $inc++;
                        }
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortList as &$value) {
                    arsort($value);
                }
                // dd($sortList);
                $sortListEstate = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $sortListEstate[$key][$key2] = $value2['skor_akhir'];
                        $inc++;
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortListEstate as &$value) {
                    arsort($value);
                }

                // dd($sortListEstate);
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                        }
                    }
                }

                // dd($dataSkorAkhirPerWilEst);
                //menambahkan nilai rank ketia semua total skor sudah di uritkan
                $test = [];
                $listRank = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    // create an array to store the skore_akhir values
                    $skore_akhir_values = [];
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $skore_akhir_values[] = $value3['skore_akhir'];
                        }
                    }
                    // sort the skore_akhir values in descending order
                    rsort($skore_akhir_values);
                    // assign ranks to each skore_akhir value
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $rank = array_search($value3['skore_akhir'], $skore_akhir_values) + 1;
                            $dataSkorAkhirPerWil[$key][$key2][$key3]['rank'] = $rank;
                            $test[$key][] = $value3['skore_akhir'];
                        }
                    }
                }

                // dd($dataSkorAkhirPerWil);
                // perbaiki rank saya berdasarkan skore_akhir di mana jika $value3['skore_akhir'] terkecil merupakan rank 1 dan seterusnya
                $list_all_will = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $list_all_will[$key][$inc]['est_afd'] = $key2 . '_' . $key3;
                            $list_all_will[$key][$inc]['est'] = $key2;
                            $list_all_will[$key][$inc]['afd'] = $key3;
                            $list_all_will[$key][$inc]['skor'] = $value3['skore_akhir'];
                            foreach ($queryAsisten as $key4 => $value4) {
                                if ($value4->est == $key2 && $value4->afd == $key3) {
                                    $list_all_will[$key][$inc]['nama'] = $value4->nama;
                                }
                            }
                            if (empty($list_all_will[$key][$inc]['nama'])) {
                                $list_all_will[$key][$inc]['nama'] = '-';
                            }
                            $inc++;
                        }
                    }
                }

                // dd($dataSkorAkhirPerWilEst);
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    foreach ($value as $subKey => $subValue) {
                        if (strpos($subKey, 'Plasma') !== false) {
                            unset($dataSkorAkhirPerWilEst[$key][$subKey]);
                        }
                    }
                }
                // dd($dataSkorAkhirPerWilEst);
                $skor_gm_wil = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $sum_est_brondol = 0;
                    $sum_est_karung = 0;
                    $sum_est_buah_tinggal = 0;
                    $sum_est_restan_tinggal = 0;
                    $sum_blok = 0;
                    foreach ($value as $key2 => $value2) {
                        $sum_est_brondol += $value2['total_estate_brondol'];
                        $sum_est_karung += $value2['total_estate_karung'];
                        $sum_est_buah_tinggal += $value2['total_estate_buah_tinggal'];
                        $sum_est_restan_tinggal += $value2['total_estate_restan_tinggal'];

                        // dd($value2['total_blokokok']);

                        // if ($value2['total_blokokok'] != 0) {
                        $sum_blok += $value2['total_blokokok'];
                        // } else {
                        //     $sum_blok = 1;
                        // }
                    }

                    if ($sum_blok != 0) {
                        $skor_total_brondol = round($sum_est_brondol / $sum_blok, 2);
                    } else {
                        $skor_total_brondol = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_karung = round($sum_est_karung / $sum_blok, 2);
                    } else {
                        $skor_total_karung = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_buah_tinggal = round($sum_est_buah_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_buah_tinggal = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_restan_tinggal = round($sum_est_restan_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_restan_tinggal = 0;
                    }
                    if (
                        $sum_est_brondol == 0 &&
                        $sum_est_karung  == 0 &&
                        $sum_est_buah_tinggal  == 0 &&
                        $sum_est_restan_tinggal  == 0 &&
                        $sum_blok  == 0
                    ) {
                        $skor_gm_wil[$key]['skor'] = 0;
                    } else {
                        $skor_gm_wil[$key]['skor'] = skorBRDsidak($skor_total_brondol) + skorKRsidak($skor_total_karung) + skorBHsidak($skor_total_buah_tinggal) + skorRSsidak($skor_total_restan_tinggal);
                    }

                    $skor_gm_wil[$key]['total_brondolan'] = $sum_est_brondol;
                    $skor_gm_wil[$key]['total_karung'] = $sum_est_karung;
                    $skor_gm_wil[$key]['total_buah_tinggal'] = $sum_est_buah_tinggal;
                    $skor_gm_wil[$key]['total_restan'] = $sum_est_restan_tinggal;
                    $skor_gm_wil[$key]['blok'] = $sum_blok;
                }
                // dd($skor_gm_wil);
                $GmSkorWil = [];

                $queryAsisten1 = DB::connection('mysql2')
                    ->Table('asisten_qc')
                    ->get();
                $queryAsisten1 = json_decode($queryAsisten1, true);

                foreach ($skor_gm_wil as $key => $value) {
                    // determine estWil value based on key
                    if ($key == 1) {
                        $estWil = 'WIL-I';
                    } elseif ($key == 2) {
                        $estWil = 'WIL-II';
                    } elseif ($key == 3) {
                        $estWil = 'WIL-III';
                    } elseif ($key == 4) {
                        $estWil = 'WIL-IV';
                    } elseif ($key == 5) {
                        $estWil = 'WIL-V';
                    } elseif ($key == 6) {
                        $estWil = 'WIL-VI';
                    } elseif ($key == 7) {
                        $estWil = 'WIL-VII';
                    } elseif ($key == 8) {
                        $estWil = 'WIL-VIII';
                    } elseif ($key == 10) {
                        $estWil = 'WIL-IX';
                    } elseif ($key == 11) {
                        $estWil = 'WIL-X';
                    }

                    // get nama value from queryAsisten1
                    $namaGM = '-';
                    foreach ($queryAsisten1 as $asisten) {
                        if ($asisten['est'] == $estWil && $asisten['afd'] == 'GM') {
                            $namaGM = $asisten['nama'];
                            break; // stop searching once we find the matching asisten
                        }
                    }

                    // add the current skor_gm_wil value and namaGM value to GmSkorWil array
                    $GmSkorWil[] = [
                        'total_brondolan' => $value['total_brondolan'],
                        'total_karung' => $value['total_karung'],
                        'total_buah_tinggal' => $value['total_buah_tinggal'],
                        'total_restan' => $value['total_restan'],
                        'blok' => $value['blok'],
                        'skor' => $value['skor'],
                        'est' => $estWil,
                        'afd' => 'GM',
                        'namaGM' => $namaGM,
                    ];
                }

                $GmSkorWil = array_values($GmSkorWil);

                $sum_wil_blok = 0;
                $sum_wil_brondolan = 0;
                $sum_wil_karung = 0;
                $sum_wil_buah_tinggal = 0;
                $sum_wil_restan = 0;

                foreach ($skor_gm_wil as $key => $value) {
                    $sum_wil_blok += $value['blok'];
                    $sum_wil_brondolan += $value['total_brondolan'];
                    $sum_wil_karung += $value['total_karung'];
                    $sum_wil_buah_tinggal += $value['total_buah_tinggal'];
                    $sum_wil_restan += $value['total_restan'];
                }

                $skor_total_wil_brondol = $sum_wil_blok == 0 ? $sum_wil_brondolan : round($sum_wil_brondolan / $sum_wil_blok, 2);
                $skor_total_wil_karung = $sum_wil_blok == 0 ? $sum_wil_karung : round($sum_wil_karung / $sum_wil_blok, 2);
                $skor_total_wil_buah_tinggal = $sum_wil_blok == 0 ? $sum_wil_buah_tinggal : round($sum_wil_buah_tinggal / $sum_wil_blok, 2);
                $skor_total_wil_restan = $sum_wil_blok == 0 ? $sum_wil_restan : round($sum_wil_restan / $sum_wil_blok, 2);

                $skor_rh = [];
                foreach ($queryReg as $key => $value) {
                    if ($value == 1) {
                        $est = 'REG-I';
                    } elseif ($value == 2) {
                        $est = 'REG-II';
                    } elseif ($value == 3) {
                        $est = 'REG-III';
                    } else {
                        $est = 'REG-IV';
                    }
                    foreach ($queryAsisten as $key2 => $value2) {
                        if ($value2->est == $est && $value2->afd == 'RH') {
                            $skor_rh[$value]['nama'] = $value2->nama;
                        }
                    }
                    if (empty($skor_rh[$value]['nama'])) {
                        $skor_rh[$value]['nama'] = '-';
                    }
                    if (
                        $sum_wil_blok == 0 &&
                        $sum_wil_brondolan == 0 &&
                        $sum_wil_karung == 0 &&
                        $sum_wil_buah_tinggal == 0 &&
                        $sum_wil_restan == 0
                    ) {
                        $skor_rh[$value]['skor'] = 0;
                    } else {
                        $skor_rh[$value]['skor'] = skorBRDsidak($skor_total_wil_brondol) + skorKRsidak($skor_total_wil_karung) + skorBHsidak($skor_total_wil_buah_tinggal) + skorRSsidak($skor_total_wil_restan);
                    }
                }

                // dd($skor_rh);

                foreach ($list_all_will as $key => $value) {
                    array_multisort(array_column($list_all_will[$key], 'skor'), SORT_DESC, $list_all_will[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_will[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_will[$key], 'est_afd'), SORT_ASC, $list_all_will[$key]);
                }


                $list_all_est = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $list_all_est[$key][$inc]['est'] = $key2;
                        $list_all_est[$key][$inc]['skor'] = $value2['skor_akhir'];
                        $list_all_est[$key][$inc]['EM'] = 'EM';
                        foreach ($queryAsisten as $key4 => $value4) {
                            if ($value4->est == $key2 && $value4->afd == 'EM') {
                                $list_all_est[$key][$inc]['nama'] = $value4->nama;
                            }
                        }
                        if (empty($list_all_est[$key][$inc]['nama'])) {
                            $list_all_est[$key][$inc]['nama'] = '-';
                        }
                        $inc++;
                    }
                }

                foreach ($list_all_est as $key => $value) {
                    array_multisort(array_column($list_all_est[$key], 'skor'), SORT_DESC, $list_all_est[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_est[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_est[$key], 'est'), SORT_ASC, $list_all_est[$key]);
                }

                // dd($list_all_est);
                // dd($list_all_est);

                //untuk chart!!!
                foreach ($queryGroup as $key => $value) {
                    $sum_bt_tph = 0;
                    $sum_bt_jalan = 0;
                    $sum_bt_bin = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    $jum_blok = 0;
                    $tot_brd = 0;

                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                            $jum_blok++;
                        }
                        $sum_bt_tph += $val->bt_tph;
                        $sum_bt_jalan += $val->bt_jalan;
                        $sum_bt_bin += $val->bt_bin;
                    }
                    $tot_brd = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                    $skor_brd = round($tot_brd / $jum_blok, 2);
                    $arrBtTPHperEst[$key] = $skor_brd;
                    // $arrBtTPHperEst[$key] = [
                    //     'skor_brd' => $skor_brd,
                    //     'jum_blok' => $jum_blok,
                    //     'tot_brd' => $tot_brd,
                    // ];
                }

                // dd($arrBtTPHperEst);
                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroup as $key => $value) {
                    $sum_jum_karung = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_jum_karung += $val->jum_karung;
                    }
                    $skor_brd = round($sum_jum_karung / $jum_blok, 2);
                    $arrKRest[$key] = $skor_brd;
                    // $arrKRest[$key] = [
                    //     'skor_kr' => $skor_brd,
                    //     'jum_blok' => $jum_blok,
                    //     'tot_brd' => $tot_brd,
                    // ];
                }
                // dd($arrKRest);
                //looping buah tinggal
                foreach ($queryGroup as $key => $value) {
                    $sum_buah_tinggal = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_buah_tinggal += $val->buah_tinggal;
                    }
                    $skor_brd = round($sum_buah_tinggal / $jum_blok, 2);
                    $arrBHest[$key] = $skor_brd;
                    // $arrBHest[$key] = [
                    //     'skor_bh' => $skor_brd,
                    //     'jum_blok' => $jum_blok,
                    //     'tot_bh' => $sum_buah_tinggal,
                    // ];
                }
                // dd($arrBHest);
                //looping buah restrant tidak di  laporkan
                foreach ($queryGroup as $key => $value) {
                    $sum_restan_unreported = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_restan_unreported += $val->restan_unreported;
                    }
                    $skor_brd = round($sum_restan_unreported / $jum_blok, 2);
                    $arrRSest[$key] = $skor_brd;
                }

                //query untuk wilayah menambhakna data
                //jadikan dulu query dalam group memakai data querry untuk wilayah
                $queryGroupWil = $query->groupBy(function ($item) {
                    return $item->wil;
                });

                // dd($queryGroupWil);
                foreach ($queryGroupWil as $key => $value) {
                    $sum_bt_tph = 0;
                    foreach ($value as $key2 => $val) {
                        $sum_bt_tph += $val->bt_tph;
                    }
                    // if ($key == 1 || $key == 2 || $key == 3) {
                    if ($skor_gm_wil[$key]['blok'] != 0) {
                        $arrBtTPHperWil[$key] = round($sum_bt_tph / $skor_gm_wil[$key]['blok'], 2);
                    } else {
                        $arrBtTPHperWil[$key] = 0;
                    }
                    // }
                }

                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroupWil as $key => $value) {
                    $sum_jum_karung = 0;
                    foreach ($value as $key2 => $vale) {
                        $sum_jum_karung += $vale->jum_karung;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrKRestWil[$key] = round($sum_jum_karung / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrKRestWil[$key] = 0;
                        }
                    }
                }
                //looping buah tinggal
                foreach ($queryGroupWil as $key => $value) {
                    $sum_buah_tinggal = 0;
                    foreach ($value as $key2 => $val2) {
                        $sum_buah_tinggal += $val2->buah_tinggal;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrBHestWil[$key] = round($sum_buah_tinggal / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrBHestWil[$key] = 0;
                        }
                    }
                }
                foreach ($queryGroupWil as $key => $value) {
                    $sum_restan_unreported = 0;
                    foreach ($value as $key2 => $val3) {
                        $sum_restan_unreported += $val3->restan_unreported;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrRestWill[$key] = round($sum_restan_unreported / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrRestWill[$key] = 0;
                        }
                    }
                }
            }
            // dd($arrBtTPHperWil, $arrKRestWil, $arrBHestWil, $arrRestWill);
            // dd($queryGroup);

            //bagian plasma cuy
            $QueryPlasmaSIdak = DB::connection('mysql2')
                ->table('sidak_tph')
                ->select('sidak_tph.*', DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'))
                ->where('sidak_tph.datetime', 'like', '%' . $monthSidak . '%')
                ->get();
            $QueryPlasmaSIdak = $QueryPlasmaSIdak->groupBy(['est', 'afd']);
            $QueryPlasmaSIdak = json_decode($QueryPlasmaSIdak, true);
            // dd($QueryPlasmaSIdak['Plasma1']);
            $getPlasma = 'Plasma' . $regSidak;
            $queryEstePla = DB::connection('mysql2')
                ->table('estate')
                ->select('estate.*')
                ->whereIn('estate.est', [$getPlasma])
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('wil.regional', $regSidak)
                ->get();
            $queryEstePla = json_decode($queryEstePla, true);

            $queryAsisten = DB::connection('mysql2')
                ->Table('asisten_qc')
                ->get();
            $queryAsisten = json_decode($queryAsisten, true);

            $PlasmaAfd = DB::connection('mysql2')
                ->table('afdeling')
                ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
                ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
                ->get();
            $PlasmaAfd = json_decode($PlasmaAfd, true);

            $SidakTPHPlA = [];
            foreach ($QueryPlasmaSIdak as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        $SidakTPHPlA[$key][$key2][$key3] = $value3;
                    }
                }
            }
            // dd($SidakTPHPlA);
            $defPLASidak = [];
            foreach ($queryEstePla as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defPLASidak[$est['est']][$afd['nama']]['null'] = 0;
                    }
                }
            }

            foreach ($defPLASidak as $key => $estValue) {
                foreach ($estValue as $monthKey => $monthValue) {
                    $mergedValues = [];
                    foreach ($SidakTPHPlA as $dataKey => $dataValue) {
                        if ($dataKey == $key && isset($dataValue[$monthKey])) {
                            $mergedValues = array_merge($mergedValues, $dataValue[$monthKey]);
                        }
                    }
                    $defPLASidak[$key][$monthKey] = $mergedValues;
                }
            }

            $arrPlasma = [];
            foreach ($defPLASidak as $key => $value) {
                if (!empty($value)) {
                    $jum_blokPla = 0;
                    $sum_tphPla = 0;
                    $sum_karungPla = 0;
                    $sum_buahPla = 0;
                    $sum_restantPla = 0;

                    $skor_tphPla = 0;
                    $skor_karungPla = 0;
                    $skor_buahPla = 0;
                    $skor_restanPla = 0;
                    $skoreTotalPla = 0;
                    foreach ($value as $key1 => $value1) {
                        if (!empty($value1)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value1 as $key2 => $value2) {
                                if (is_array($value2)) {
                                    $blok = $value2['est'] . ' ' . $value2['afd'] . ' ' . $value2['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value2['bt_tph'];
                                    $sum_bt_jalan += $value2['bt_jalan'];
                                    $sum_bt_bin += $value2['bt_bin'];

                                    $sum_jum_karung += $value2['jum_karung'];
                                    $sum_buah_tinggal += $value2['buah_tinggal'];
                                    $sum_restan_unreported += $value2['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            if ($jum_blok == 0) {
                                $skor_brd = 0;
                                $skor_kr = 0;
                                $skor_buahtgl = 0;
                                $skor_restan = 0;
                            } else {
                                $skor_brd = round($sum_all / $jum_blok, 1);
                                $skor_kr = round($sum_all_karung / $jum_blok, 1);
                                $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                                $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);
                            }

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $arrPlasma[$key][$key1]['karung_tes'] = $sum_all_karung;
                            $arrPlasma[$key][$key1]['tph_test'] = $sum_all;
                            $arrPlasma[$key][$key1]['buah_test'] = $sum_all_bt_tgl;
                            $arrPlasma[$key][$key1]['restant_tes'] = $sum_all_restan_unreported;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = $jum_blok;

                            $arrPlasma[$key][$key1]['brd_blok'] = skorBRDsidak($skor_brd);
                            $arrPlasma[$key][$key1]['kr_blok'] = skorKRsidak($skor_kr);
                            $arrPlasma[$key][$key1]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $arrPlasma[$key][$key1]['restan_blok'] = skorRSsidak($skor_restan);
                            $arrPlasma[$key][$key1]['skorWil'] = $skoreTotal;

                            $jum_blokPla += $jum_blok;
                            $sum_karungPla += $sum_all_karung;
                            $sum_restantPla += $sum_all_restan_unreported;
                            $sum_tphPla += $sum_all;
                            $sum_buahPla += $sum_all_bt_tgl;
                        } else {
                            $arrPlasma[$key][$key1]['karung_tes'] = 0;
                            $arrPlasma[$key][$key1]['tph_test'] = 0;
                            $arrPlasma[$key][$key1]['buah_test'] = 0;
                            $arrPlasma[$key][$key1]['restant_tes'] = 0;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = 0;

                            $arrPlasma[$key][$key1]['brd_blok'] = 0;
                            $arrPlasma[$key][$key1]['kr_blok'] = 0;
                            $arrPlasma[$key][$key1]['buah_blok'] = 0;
                            $arrPlasma[$key][$key1]['restan_blok'] = 0;
                            $arrPlasma[$key][$key1]['skorWil'] = 0;
                        }
                    }

                    if ($jum_blokPla != 0) {
                        $skor_tphPla = round($sum_tphPla / $jum_blokPla, 2);
                        $skor_karungPla = round($sum_karungPla / $jum_blokPla, 2);
                        $skor_buahPla = round($sum_buahPla / $jum_blokPla, 2);
                        $skor_restanPla = round($sum_restantPla / $jum_blokPla, 2);
                    } else {
                        $skor_tphPla = 0;
                        $skor_karungPla = 0;
                        $skor_buahPla = 0;
                        $skor_restanPla = 0;
                    }

                    $skoreTotalPla = skorBRDsidak($skor_tphPla) + skorKRsidak($skor_karungPla) + skorBHsidak($skor_buahPla) + skorRSsidak($skor_restanPla);
                    if (
                        $jum_blokPla == 0 &&
                        $sum_karungPla == 0 &&
                        $sum_restantPla == 0 &&
                        $sum_tphPla == 0 &&
                        $sum_buahPla == 0
                    ) {
                        $arrPlasma[$key]['SkorPlasma'] = 0;
                    } else {
                        $arrPlasma[$key]['SkorPlasma'] = $skoreTotalPla;
                    }

                    $arrPlasma[$key]['karung_tes'] = $sum_karungPla;
                    $arrPlasma[$key]['tph_test'] = $sum_tphPla;
                    $arrPlasma[$key]['buah_test'] = $sum_buahPla;
                    $arrPlasma[$key]['restant_tes'] = $sum_restantPla;

                    $arrPlasma[$key]['jumlah_blok'] = $jum_blokPla;

                    $arrPlasma[$key]['brd_blok'] = skorBRDsidak($skor_tphPla);
                    $arrPlasma[$key]['kr_blok'] = skorKRsidak($skor_karungPla);
                    $arrPlasma[$key]['buah_blok'] = skorBHsidak($skor_buahPla);
                    $arrPlasma[$key]['restan_blok'] = skorRSsidak($skor_restanPla);
                }
            }
            // dd($arrPlasma);
            foreach ($arrPlasma as $key1 => $estates) {
                if (is_array($estates)) {
                    // $sortedData = array();
                    $sortedDataEst = [];
                    foreach ($estates as $estateName => $data) {
                        // dd($data);
                        if (is_array($data)) {
                            $sortedDataEst[] = [
                                'key1' => $key1,
                                'estateName' => $estateName,
                                'data' => $data,
                            ];
                        }
                    }
                    usort($sortedDataEst, function ($a, $b) {
                        return $b['data']['skorWil'] - $a['data']['skorWil'];
                    });
                    $rank = 1;
                    foreach ($sortedDataEst as $sortedest) {
                        $arrPlasma[$key1][$sortedest['estateName']]['rank'] = $rank;
                        $rank++;
                    }
                    unset($sortedDataEst);
                }
            }
            // dd($arrPlasma);

            $PlasmaWIl = [];
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) {
                            // dd($value1);
                            $inc = 0;
                            $est = $key;
                            $skor = $value1['skorWil'];
                            // dd($skor);
                            $EM = $key1;

                            $rank = $value1['rank'];
                            // $rank = $value1['rank'];
                            $nama = '-';
                            foreach ($queryAsisten as $key4 => $value4) {
                                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                                    $nama = $value4['nama'];
                                    break;
                                }
                            }
                            $PlasmaWIl[] = [
                                'est' => $est,
                                'afd' => $EM,
                                'nama' => $nama,
                                'skor' => $skor,
                                'rank' => $rank,
                            ];
                            $inc++;
                        }
                    }
                }
            }

            $PlasmaWIl = array_values($PlasmaWIl);

            $PlasmaEM = [];
            $NamaEm = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $EM = 'EM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                            $NamaEm = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($EM)) {
                $PlasmaEM[] = [
                    'est' => $est,
                    'afd' => $EM,
                    'namaEM' => $NamaEm,
                    'Skor' => $skor,
                ];
            }

            $PlasmaEM = array_values($PlasmaEM);

            $plasmaGM = [];
            $namaGM = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $GM = 'GM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $GM) {
                            $namaGM = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($GM)) {
                $plasmaGM[] = [
                    'est' => $est,
                    'afd' => $GM,
                    'namaGM' => $namaGM,
                    'Skor' => $skor,
                ];
            }

            $plasmaGM = array_values($plasmaGM);
            //masukan semua yang sudah selese di olah di atas ke dalam vaiabel terserah kemudian masukan kedalam aray
            //karena chart hanya bisa menerima inputan json
            $queryWilChart = DB::connection('mysql2')
                ->table('wil')
                ->whereIn('regional', [$regSidak])
                ->pluck('nama');

            $arrView = [];

            // $list_all_will = changeKTE4ToKTE($list_all_will);
            // dd($PlasmaEM);


            // $list_all_est = BpthKTE($list_all_est);
            // // dd($result);
            // dd($list_all_will);


            $queryWill = list_wil($queryWill);

            $arrView['list_wilayah'] = $queryWill;
            $arrView['list_wilayah2'] = $queryWilChart;
            // $arrView['restant'] = $dataSkorAwalRestant;
            // dd($skor_gm_wil);
            $arrView['list_all_wil'] = $list_all_will;
            $arrView['list_all_est'] = $list_all_est;
            $arrView['list_skor_gm'] = $skor_gm_wil;
            $arrView['list_skor_rh'] = $skor_rh;
            $arrView['PlasmaWIl'] = $PlasmaWIl;
            $arrView['PlasmaEM'] = $PlasmaEM;
            $arrView['plasmaGM'] = $plasmaGM;
            $arrView['list_skor_gmNew'] = $GmSkorWil;
            // $arrView['karung'] = $dataSkorAwalKr;
            // $arrView['buah'] = $dataSkorAwalBuah;
            // // dd($queryEst);
            $keysToRemove = ['SRE', 'LDE', 'SKE', 'CWS1', 'CWS2', 'CWS3'];

            // Loop through the array and remove the elements with the specified keys
            foreach ($keysToRemove as $key) {
                unset($arrBtTPHperEst[$key]);
                unset($arrKRest[$key]);
                unset($arrBHest[$key]);
                unset($arrRSest[$key]);
            }
            $arrays = [
                &$arrBtTPHperEst,
                &$arrKRest,
                &$arrBHest,
                &$arrRSest,

            ];
            $insertAfterv2 = $regSidak == '1' ? "UPE" : ($regSidak == '2' ? "SCE" : "GDE");
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
            foreach ($arrays as &$array) {
                $plasmaKeys = preg_grep('/^Plasma/', array_keys($array));
                foreach ($plasmaKeys as $plasmaKey) {
                    moveElement($array, $plasmaKey, $insertAfterv2);
                }
            }
            $keys = array_keys($arrRSest);

            $keys = updateKeyRecursive2($keys);
            // dd($keys);
            // masukan ke array data penjumlahan dari estate
            // dd($arrBtTPHperEst, $arrKRest, $arrBHest, $arrRSest, $keys);
            $arrView['estate_new'] = $keys;
            $arrView['val_bt_tph'] = $arrBtTPHperEst; //data jsen brondolan tinggal di tph
            $arrView['val_kr_tph'] = $arrKRest; //data jsen karung yang berisi buah
            $arrView['val_bh_tph'] = $arrBHest; //data jsen buah yang tinggal
            $arrView['val_rs_tph'] = $arrRSest; //data jsen restan yang tidak dilaporkan
            //masukan ke array data penjumlahan dari wilayah
            $arrView['val_kr_tph_wil'] = $arrKRestWil; //data jsen karung yang berisi buah
            $arrView['val_bt_tph_wil'] = $arrBtTPHperWil; //data jsen brondolan tinggal di tph
            $arrView['val_bh_tph_wil'] = $arrBHestWil; //data jsen buah yang tinggal
            $arrView['val_rs_tph_wil'] = $arrRestWill; //data jsen restan yang tidak dilaporkan
            // dd($resultest1);

            // new tph result 
            // untuk afdeling table 1 sampe 4 
            $arrView['afdeling1'] = $resultafd1;
            $arrView['afdeling2'] = $resultafd2;
            $arrView['afdeling3'] = $resultafd3;
            $arrView['afdeling4'] = $resultafd4;
            //  untuk estate table 1 sampe 4 
            $arrView['estate1'] = $resultest1;
            $arrView['estate2'] = $resultest2;
            $arrView['estate3'] = $resultest3;
            $arrView['estate4'] = $resultest4;
            $arrView['hasilRh'] = $rhEstate;


            echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
            exit();
        }
    }

    public function getBtTphYear(Request $request)
    {
        $regSidak = $request->get('reg');
        $yearSidak = $request->get('year');
        $queryWill = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->get();
        $queryReg = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('regional');
        $queryReg2 = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regSidak])
            ->pluck('id')
            ->toArray();

        // dapatkan data estate dari table estate dengan wilayah 1 , 2 , 3
        $queryEst = DB::connection('mysql2')
            ->table('estate')
            // ->whereNotIn('estate.est', ['PLASMA'])
            ->whereIn('wil', $queryReg2)
            ->get();
        // dd($queryEst);
        $queryEste = DB::connection('mysql2')
            ->table('estate')
            ->whereNotIn('estate.est', ['PLASMA', 'SRE', 'LDE', 'SKE'])
            ->whereIn('wil', $queryReg2)
            ->get();
        $queryEste = $queryEste->groupBy(function ($item) {
            return $item->wil;
        });

        $queryEste = json_decode($queryEste, true);

        // dd($queryEst);
        $queryAfd = DB::connection('mysql2')
            ->Table('afdeling')
            ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->whereNotIn('estate.est', ['SRE', 'LDE', 'SKE'])
            ->get();

        $queryAfd = json_decode($queryAfd, true);

        //array untuk tampung nilai bt tph per estate dari table bt_jalan & bt_tph dll
        $arrBtTPHperEst = []; //table dari brondolan di buat jadi array agar bisa di parse ke json
        $arrKRest = []; //table dari jum_jkarung di buat jadi array agar bisa di parse ke json
        $arrBHest = []; //table dari Buah di buat jadi array agar bisa di parse ke json
        $arrRSest = []; //table array untuk buah restant tidak di laporkan

        ///array untuk table nya

        $dataSkorAwal = [];

        $list_all_will = [];

        //memberi nilai 0 default kesemua estate
        foreach ($queryEst as $key => $value) {
            $arrBtTPHperEst[$value->est] = 0; //est mengambil value dari table estate
            $arrKRest[$value->est] = 0;
            $arrBHest[$value->est] = 0;
            $arrRSest[$value->est] = 0;
        }
        // dd($queryEst);
        foreach ($queryWill as $key => $value) {
            $arrBtTPHperWil[$value->nama] = 0; //est mengambil value dari table estate
            $arrKRestWil[$value->nama] = 0;
            $arrBHestWil[$value->nama] = 0;
            $arrRestWill[$value->nama] = 0;
        }

        // dd($firstWeek, $lastWeek);
        $query = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.datetime', 'like', '%' . $yearSidak . '%')
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        //     ->get();
        $queryAFD = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->whereIn('estate.wil', $queryReg2)
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.datetime', 'like', '%' . $yearSidak . '%')
            // ->where('sidak_tph.datetime', 'LIKE', '%' . $request->$firstDate . '%')
            ->get();
        // dd($query);
        $queryAsisten = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();

        $dataAfdEst = [];

        $querySidak = DB::connection('mysql2')
            ->table('sidak_tph')
            ->where('sidak_tph.datetime', 'like', '%' . $yearSidak . '%')
            // ->whereBetween('sidak_tph.datetime', ['2023-01-23', '202-12-25'])
            ->get();
        $querySidak = json_decode($querySidak, true);

        $allBlok = $query->groupBy(function ($item) {
            return $item->blok;
        });

        if (!empty($query && $queryAFD && $querySidak)) {
            $queryGroup = $queryAFD->groupBy(function ($item) {
                return $item->est;
            });
            // dd($queryGroup);
            $queryWi = DB::connection('mysql2')
                ->table('estate')
                ->whereIn('wil', $queryReg2)
                ->get();

            $queryWill = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });

            $queryWill2 = $queryWi->groupBy(function ($item) {
                return $item->nama;
            });

            //untuk table!!
            // store wil -> est -> afd
            // menyimpan array nested dari  wil -> est -> afd
            foreach ($queryEste as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($queryAfd as $key3 => $value3) {
                        $est = $value2['est'];
                        $afd = $value3['nama'];
                        if ($value2['est'] == $value3['est']) {
                            foreach ($querySidak as $key4 => $value4) {
                                if ($est == $value4['est'] && $afd == $value4['afd']) {
                                    $dataAfdEst[$est][$afd][] = $value4;
                                } else {
                                    $dataAfdEst[$est][$afd]['null'] = 0;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        unset($dataAfdEst[$key][$key2]['null']);
                        if (empty($dataAfdEst[$key][$key2])) {
                            $dataAfdEst[$key][$key2] = 0;
                        }
                    }
                }
            }

            $listBlokPerAfd = [];
            foreach ($dataAfdEst as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            // dd($key3);
                            foreach ($allBlok as $key4 => $value4) {
                                if ($value3['blok'] == $key4) {
                                    $listBlokPerAfd[$key][$key2][$key3] = $value4;
                                }
                            }
                        }
                    }
                }

                // //menghitung data skor untuk brd/blok
                foreach ($dataAfdEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value2 as $key3 => $value3) {
                                if (is_array($value3)) {
                                    $blok = $value3['est'] . ' ' . $value3['afd'] . ' ' . $value3['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value3['bt_tph'];
                                    $sum_bt_jalan += $value3['bt_jalan'];
                                    $sum_bt_bin += $value3['bt_bin'];

                                    $sum_jum_karung += $value3['jum_karung'];
                                    $sum_buah_tinggal += $value3['buah_tinggal'];
                                    $sum_restan_unreported += $value3['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            $skor_brd = round($sum_all / $jum_blok, 1);
                            // dd($skor_brd);
                            $skor_kr = round($sum_all_karung / $jum_blok, 1);
                            $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                            $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $dataSkorAwal[$key][$key2]['karung_tes'] = $sum_all_karung;
                            $dataSkorAwal[$key][$key2]['tph_test'] = $sum_all;
                            $dataSkorAwal[$key][$key2]['buah_test'] = $sum_all_bt_tgl;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = $sum_all_restan_unreported;

                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = $jum_blok;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = skorBRDsidak($skor_brd);
                            $dataSkorAwal[$key][$key2]['kr_blok'] = skorKRsidak($skor_kr);
                            $dataSkorAwal[$key][$key2]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $dataSkorAwal[$key][$key2]['restan_blok'] = skorRSsidak($skor_restan);
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = $skoreTotal;
                        } else {
                            $dataSkorAwal[$key][$key2]['karung_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['restant_tes'] = 0;
                            $dataSkorAwal[$key][$key2]['jumlah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['tph_test'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_test'] = 0;

                            $dataSkorAwal[$key][$key2]['brd_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['kr_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['buah_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['restan_blok'] = 0;
                            $dataSkorAwal[$key][$key2]['skore_akhir'] = 0;
                        }
                    }
                }

                // dd($dataSkorAwal);

                foreach ($dataSkorAwal as $key => $value) {
                    $jum_blok = 0;
                    $jum_all_blok = 0;
                    $sum_all_tph = 0;
                    $sum_tph = 0;
                    $sum_all_karung = 0;
                    $sum_karung = 0;
                    $sum_all_buah = 0;
                    $sum_buah = 0;
                    $sum_all_restant = 0;
                    $sum_restant = 0;
                    foreach ($value as $key2 => $value2) {
                        if (is_array($value2)) {
                            $jum_blok += $value2['jumlah_blok'];
                            $sum_karung += $value2['karung_tes'];
                            $sum_restant += $value2['restant_tes'];
                            $sum_tph += $value2['tph_test'];
                            $sum_buah += $value2['buah_test'];
                        }
                    }
                    $sum_all_tph = $sum_tph;
                    $jum_all_blok = $jum_blok;
                    $sum_all_karung = $sum_karung;
                    $sum_all_buah = $sum_buah;
                    $sum_all_restant = $sum_restant;

                    if ($jum_all_blok != 0) {
                        $skor_tph = round($sum_all_tph / $jum_all_blok, 2);
                        $skor_karung = round($sum_all_karung / $jum_all_blok, 2);
                        $skor_buah = round($sum_all_buah / $jum_all_blok, 2);
                        $skor_restan = round($sum_all_restant / $jum_all_blok, 2);
                    } else {
                        $skor_tph = 0;
                        $skor_karung = 0;
                        $skor_buah = 0;
                        $skor_restan = 0;
                    }

                    $skoreTotal = skorBRDsidak($skor_tph) + skorKRsidak($skor_karung) + skorBHsidak($skor_buah) + skorRSsidak($skor_restan);

                    $dataSkorAwaltest[$key]['total_estate_brondol'] = $sum_all_tph;
                    $dataSkorAwaltest[$key]['total_estate_karung'] = $sum_all_karung;
                    $dataSkorAwaltest[$key]['total_estate_buah_tinggal'] = $sum_all_buah;
                    $dataSkorAwaltest[$key]['total_estate_restan_tinggal'] = $sum_all_restant;
                    $dataSkorAwaltest[$key]['tph'] = skorBRDsidak($skor_tph);
                    $dataSkorAwaltest[$key]['karung'] = skorKRsidak($skor_karung);
                    $dataSkorAwaltest[$key]['buah_tinggal'] = skorBHsidak($skor_buah);
                    $dataSkorAwaltest[$key]['restant'] = skorRSsidak($skor_restan);
                    $dataSkorAwaltest[$key]['total_blokokok'] = $jum_all_blok;
                    $dataSkorAwaltest[$key]['skor_akhir'] = $skoreTotal;
                }
                // dd($dataSkorAwaltest);

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwal as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWil[$key][$key3] = $value3;
                            }
                        }
                    }
                }

                foreach ($queryEste as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($dataSkorAwaltest as $key3 => $value3) {
                            if ($value2['est'] == $key3) {
                                $dataSkorAkhirPerWilEst[$key][$key3] = $value3;
                            }
                        }
                    }
                }
                // dd($dataSkorAkhirPerWilEst['3']);
                //menshort nilai masing masing
                $sortList = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $sortList[$key][$key2 . '_' . $key3] = $value3['skore_akhir'];
                            $inc++;
                        }
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortList as &$value) {
                    arsort($value);
                }
                // dd($sortList);
                $sortListEstate = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $sortListEstate[$key][$key2] = $value2['skor_akhir'];
                        $inc++;
                    }
                }

                //short list untuk mengurutkan valuenya
                foreach ($sortListEstate as &$value) {
                    arsort($value);
                }

                // dd($sortListEstate);
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                        }
                    }
                }

                //menambahkan nilai rank ketia semua total skor sudah di uritkan
                $test = [];
                $listRank = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    // create an array to store the skore_akhir values
                    $skore_akhir_values = [];
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $skore_akhir_values[] = $value3['skore_akhir'];
                        }
                    }
                    // sort the skore_akhir values in descending order
                    rsort($skore_akhir_values);
                    // assign ranks to each skore_akhir value
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $rank = array_search($value3['skore_akhir'], $skore_akhir_values) + 1;
                            $dataSkorAkhirPerWil[$key][$key2][$key3]['rank'] = $rank;
                            $test[$key][] = $value3['skore_akhir'];
                        }
                    }
                }

                // perbaiki rank saya berdasarkan skore_akhir di mana jika $value3['skore_akhir'] terkecil merupakan rank 1 dan seterusnya
                $list_all_will = [];
                foreach ($dataSkorAkhirPerWil as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $list_all_will[$key][$inc]['est_afd'] = $key2 . '_' . $key3;
                            $list_all_will[$key][$inc]['est'] = $key2;
                            $list_all_will[$key][$inc]['afd'] = $key3;
                            $list_all_will[$key][$inc]['skor'] = $value3['skore_akhir'];
                            foreach ($queryAsisten as $key4 => $value4) {
                                if ($value4->est == $key2 && $value4->afd == $key3) {
                                    $list_all_will[$key][$inc]['nama'] = $value4->nama;
                                }
                            }
                            if (empty($list_all_will[$key][$inc]['nama'])) {
                                $list_all_will[$key][$inc]['nama'] = '-';
                            }
                            $inc++;
                        }
                    }
                }

                $skor_gm_wil = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $sum_est_brondol = 0;
                    $sum_est_karung = 0;
                    $sum_est_buah_tinggal = 0;
                    $sum_est_restan_tinggal = 0;
                    $sum_blok = 0;
                    foreach ($value as $key2 => $value2) {
                        $sum_est_brondol += $value2['total_estate_brondol'];
                        $sum_est_karung += $value2['total_estate_karung'];
                        $sum_est_buah_tinggal += $value2['total_estate_buah_tinggal'];
                        $sum_est_restan_tinggal += $value2['total_estate_restan_tinggal'];

                        // dd($value2['total_blokokok']);

                        // if ($value2['total_blokokok'] != 0) {
                        $sum_blok += $value2['total_blokokok'];
                        // } else {
                        //     $sum_blok = 1;
                        // }
                    }

                    if ($sum_blok != 0) {
                        $skor_total_brondol = round($sum_est_brondol / $sum_blok, 2);
                    } else {
                        $skor_total_brondol = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_karung = round($sum_est_karung / $sum_blok, 2);
                    } else {
                        $skor_total_karung = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_buah_tinggal = round($sum_est_buah_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_buah_tinggal = 0;
                    }

                    if ($sum_blok != 0) {
                        $skor_total_restan_tinggal = round($sum_est_restan_tinggal / $sum_blok, 2);
                    } else {
                        $skor_total_restan_tinggal = 0;
                    }

                    $skor_gm_wil[$key]['total_brondolan'] = $sum_est_brondol;
                    $skor_gm_wil[$key]['total_karung'] = $sum_est_karung;
                    $skor_gm_wil[$key]['total_buah_tinggal'] = $sum_est_buah_tinggal;
                    $skor_gm_wil[$key]['total_restan'] = $sum_est_restan_tinggal;
                    $skor_gm_wil[$key]['blok'] = $sum_blok;
                    $skor_gm_wil[$key]['skor'] = skorBRDsidak($skor_total_brondol) + skorKRsidak($skor_total_karung) + skorBHsidak($skor_total_buah_tinggal) + skorRSsidak($skor_total_restan_tinggal);
                }

                $GmSkorWil = [];

                $queryAsisten1 = DB::connection('mysql2')
                    ->Table('asisten_qc')
                    ->get();
                $queryAsisten1 = json_decode($queryAsisten1, true);

                foreach ($skor_gm_wil as $key => $value) {
                    // determine estWil value based on key
                    if ($key == 1) {
                        $estWil = 'WIL-I';
                    } elseif ($key == 2) {
                        $estWil = 'WIL-II';
                    } elseif ($key == 3) {
                        $estWil = 'WIL-III';
                    } elseif ($key == 4) {
                        $estWil = 'WIL-IV';
                    } elseif ($key == 5) {
                        $estWil = 'WIL-V';
                    } elseif ($key == 6) {
                        $estWil = 'WIL-VI';
                    } elseif ($key == 7) {
                        $estWil = 'WIL-VII';
                    } elseif ($key == 8) {
                        $estWil = 'WIL-VIII';
                    } elseif ($key == 10) {
                        $estWil = 'WIL-IX';
                    } elseif ($key == 11) {
                        $estWil = 'WIL-X';
                    }
                    // get nama value from queryAsisten1
                    $namaGM = '-';
                    foreach ($queryAsisten1 as $asisten) {
                        if ($asisten['est'] == $estWil && $asisten['afd'] == 'GM') {
                            $namaGM = $asisten['nama'];
                            break; // stop searching once we find the matching asisten
                        }
                    }

                    // add the current skor_gm_wil value and namaGM value to GmSkorWil array
                    $GmSkorWil[] = [
                        'total_brondolan' => $value['total_brondolan'],
                        'total_karung' => $value['total_karung'],
                        'total_buah_tinggal' => $value['total_buah_tinggal'],
                        'total_restan' => $value['total_restan'],
                        'blok' => $value['blok'],
                        'skor' => $value['skor'],
                        'est' => $estWil,
                        'afd' => 'GM',
                        'namaGM' => $namaGM,
                    ];
                }

                $GmSkorWil = array_values($GmSkorWil);

                $sum_wil_blok = 0;
                $sum_wil_brondolan = 0;
                $sum_wil_karung = 0;
                $sum_wil_buah_tinggal = 0;
                $sum_wil_restan = 0;

                foreach ($skor_gm_wil as $key => $value) {
                    $sum_wil_blok += $value['blok'];
                    $sum_wil_brondolan += $value['total_brondolan'];
                    $sum_wil_karung += $value['total_karung'];
                    $sum_wil_buah_tinggal += $value['total_buah_tinggal'];
                    $sum_wil_restan += $value['total_restan'];
                }

                $skor_total_wil_brondol = $sum_wil_blok == 0 ? $sum_wil_brondolan : round($sum_wil_brondolan / $sum_wil_blok, 2);
                $skor_total_wil_karung = $sum_wil_blok == 0 ? $sum_wil_karung : round($sum_wil_karung / $sum_wil_blok, 2);
                $skor_total_wil_buah_tinggal = $sum_wil_blok == 0 ? $sum_wil_buah_tinggal : round($sum_wil_buah_tinggal / $sum_wil_blok, 2);
                $skor_total_wil_restan = $sum_wil_blok == 0 ? $sum_wil_restan : round($sum_wil_restan / $sum_wil_blok, 2);

                $skor_rh = [];
                foreach ($queryReg as $key => $value) {
                    if ($value == 1) {
                        $est = 'REG-I';
                    } elseif ($value == 2) {
                        $est = 'REG-II';
                    } else {
                        $est = 'REG-III';
                    }
                    foreach ($queryAsisten as $key2 => $value2) {
                        if ($value2->est == $est && $value2->afd == 'RH') {
                            $skor_rh[$value]['nama'] = $value2->nama;
                        }
                    }
                    if (empty($skor_rh[$value]['nama'])) {
                        $skor_rh[$value]['nama'] = '-';
                    }
                    $skor_rh[$value]['skor'] = skorBRDsidak($skor_total_wil_brondol) + skorKRsidak($skor_total_wil_karung) + skorBHsidak($skor_total_wil_buah_tinggal) + skorRSsidak($skor_total_wil_restan);
                }

                foreach ($list_all_will as $key => $value) {
                    array_multisort(array_column($list_all_will[$key], 'skor'), SORT_DESC, $list_all_will[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_will[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_will[$key], 'est_afd'), SORT_ASC, $list_all_will[$key]);
                }
                // $list_all_will = array();
                // foreach ($dataSkorAkhirPerWil as $key => $value) {
                //     $inc = 0;
                //     foreach ($value as $key2 => $value2) {
                //         foreach ($value2 as $key3 => $value3) {
                //             $list_all_will[$key][$inc]['est'] = $key2;
                //             $list_all_will[$key][$inc]['afd'] = $key3;
                //             $list_all_will[$key][$inc]['skor'] = $value3['skore_akhir'];
                //             $list_all_will[$key][$inc]['nama'] = '-';
                //             $list_all_will[$key][$inc]['rank'] = '-';
                //             $inc++;
                //         }
                //     }
                // }

                // foreach ($list_all_will as $key1 => $value1) {
                //     $filtered_subarray = array_filter($value1, function ($element) {
                //         return $element['skor'] != '-';
                //     });
                //     $rank = 1;
                //     foreach ($filtered_subarray as $key2 => $value2) {
                //         $filtered_subarray[$key2]['rank'] = $rank;
                //         $rank++;
                //     }
                //     $list_all_will[$key1] = $filtered_subarray;
                // }

                $list_all_est = [];
                foreach ($dataSkorAkhirPerWilEst as $key => $value) {
                    $inc = 0;
                    foreach ($value as $key2 => $value2) {
                        $list_all_est[$key][$inc]['est'] = $key2;
                        $list_all_est[$key][$inc]['skor'] = $value2['skor_akhir'];
                        $list_all_est[$key][$inc]['EM'] = 'EM';
                        foreach ($queryAsisten as $key4 => $value4) {
                            if ($value4->est == $key2 && $value4->afd == 'EM') {
                                $list_all_est[$key][$inc]['nama'] = $value4->nama;
                            }
                        }
                        if (empty($list_all_est[$key][$inc]['nama'])) {
                            $list_all_est[$key][$inc]['nama'] = '-';
                        }
                        $inc++;
                    }
                }

                foreach ($list_all_est as $key => $value) {
                    array_multisort(array_column($list_all_est[$key], 'skor'), SORT_DESC, $list_all_est[$key]);
                    $rank = 1;
                    foreach ($value as $key1 => $value1) {
                        foreach ($value1 as $key2 => $value2) {
                            $list_all_est[$key][$key1]['rank'] = $rank;
                        }
                        $rank++;
                    }
                    array_multisort(array_column($list_all_est[$key], 'est'), SORT_ASC, $list_all_est[$key]);
                }

                // dd($list_all_est);
                // dd($list_all_est);

                //untuk chart!!!
                foreach ($queryGroup as $key => $value) {
                    $sum_bt_tph = 0;
                    $sum_bt_jalan = 0;
                    $sum_bt_bin = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    $jum_blok = 0;
                    $tot_brd = 0;

                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                            $jum_blok++;
                        }
                        $sum_bt_tph += $val->bt_tph;
                        $sum_bt_jalan += $val->bt_jalan;
                        $sum_bt_bin += $val->bt_bin;
                    }
                    $tot_brd = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                    $skor_brd = round($tot_brd / $jum_blok, 2);
                    $arrBtTPHperEst[$key] = $skor_brd;
                    // $arrBtTPHperEst[$key] = [
                    //     'skor_brd' => $skor_brd,
                    //     'jum_blok' => $jum_blok,
                    //     'tot_brd' => $tot_brd,
                    // ];
                }
                // dd($arrBtTPHperEst);
                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroup as $key => $value) {
                    $sum_jum_karung = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_jum_karung += $val->jum_karung;
                    }
                    $skor_brd = round($sum_jum_karung / $jum_blok, 2);
                    $arrKRest[$key] = $skor_brd;
                }
                //looping buah tinggal
                foreach ($queryGroup as $key => $value) {
                    $sum_buah_tinggal = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_buah_tinggal += $val->buah_tinggal;
                    }
                    $skor_brd = round($sum_buah_tinggal / $jum_blok, 2);
                    $arrBHest[$key] = $skor_brd;
                }
                //looping buah restrant tidak di  laporkan
                foreach ($queryGroup as $key => $value) {
                    $sum_restan_unreported = 0;
                    $skor_brd = 0;
                    $listBlokPerAfd = [];
                    foreach ($value as $val) {
                        if (!in_array($val->est . ' ' . $val->afd . ' ' . $val->blok, $listBlokPerAfd)) {
                            $listBlokPerAfd[] = $val->est . ' ' . $val->afd . ' ' . $val->blok;
                        }
                        $jum_blok = count($listBlokPerAfd);
                        $sum_restan_unreported += $val->restan_unreported;
                    }
                    $skor_brd = round($sum_restan_unreported / $jum_blok, 2);
                    $arrRSest[$key] = $skor_brd;
                }

                //query untuk wilayah menambhakna data
                //jadikan dulu query dalam group memakai data querry untuk wilayah
                $queryGroupWil = $query->groupBy(function ($item) {
                    return $item->wil;
                });

                // dd($queryGroupWil);
                foreach ($queryGroupWil as $key => $value) {
                    $sum_bt_tph = 0;
                    foreach ($value as $key2 => $val) {
                        $sum_bt_tph += $val->bt_tph;
                    }
                    // if ($key == 1 || $key == 2 || $key == 3) {
                    if ($skor_gm_wil[$key]['blok'] != 0) {
                        $arrBtTPHperWil[$key] = round($sum_bt_tph / $skor_gm_wil[$key]['blok'], 2);
                    } else {
                        $arrBtTPHperWil[$key] = 0;
                    }
                    // }
                }

                //sebelum di masukan ke aray harus looping karung berisi brondolan untuk mengambil datanya 2 lapis
                foreach ($queryGroupWil as $key => $value) {
                    $sum_jum_karung = 0;
                    foreach ($value as $key2 => $vale) {
                        $sum_jum_karung += $vale->jum_karung;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrKRestWil[$key] = round($sum_jum_karung / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrKRestWil[$key] = 0;
                        }
                    }
                }
                //looping buah tinggal
                foreach ($queryGroupWil as $key => $value) {
                    $sum_buah_tinggal = 0;
                    foreach ($value as $key2 => $val2) {
                        $sum_buah_tinggal += $val2->buah_tinggal;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrBHestWil[$key] = round($sum_buah_tinggal / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrBHestWil[$key] = 0;
                        }
                    }
                }
                foreach ($queryGroupWil as $key => $value) {
                    $sum_restan_unreported = 0;
                    foreach ($value as $key2 => $val3) {
                        $sum_restan_unreported += $val3->restan_unreported;
                    }
                    if ($key == 1 || $key == 2 || $key == 3) {
                        if ($skor_gm_wil[$key]['blok'] != 0) {
                            $arrRestWill[$key] = round($sum_restan_unreported / $skor_gm_wil[$key]['blok'], 2);
                        } else {
                            $arrRestWill[$key] = 0;
                        }
                    }
                }
            }
            // dd($arrBtTPHperWil, $arrKRestWil, $arrBHestWil, $arrRestWill);
            // dd($queryGroup);

            //bagian plasma cuy
            $QueryPlasmaSIdak = DB::connection('mysql2')
                ->table('sidak_tph')
                ->select('sidak_tph.*', DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'))
                ->where('sidak_tph.datetime', 'like', '%' . $yearSidak . '%')
                ->get();
            $QueryPlasmaSIdak = $QueryPlasmaSIdak->groupBy(['est', 'afd']);
            $QueryPlasmaSIdak = json_decode($QueryPlasmaSIdak, true);
            // dd($QueryPlasmaSIdak['Plasma1']);
            $getPlasma = 'Plasma' . $regSidak;
            $queryEstePla = DB::connection('mysql2')
                ->table('estate')
                ->select('estate.*')
                ->whereIn('estate.est', [$getPlasma])
                ->join('wil', 'wil.id', '=', 'estate.wil')
                ->where('wil.regional', $regSidak)
                ->get();
            $queryEstePla = json_decode($queryEstePla, true);

            $queryAsisten = DB::connection('mysql2')
                ->Table('asisten_qc')
                ->get();
            $queryAsisten = json_decode($queryAsisten, true);

            $PlasmaAfd = DB::connection('mysql2')
                ->table('afdeling')
                ->select('afdeling.id', 'afdeling.nama', 'estate.est') //buat mengambil data di estate db dan willayah db
                ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
                ->get();
            $PlasmaAfd = json_decode($PlasmaAfd, true);

            $SidakTPHPlA = [];
            foreach ($QueryPlasmaSIdak as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) {
                        $SidakTPHPlA[$key][$key2][$key3] = $value3;
                    }
                }
            }
            // dd($SidakTPHPlA);
            $defPLASidak = [];
            foreach ($queryEstePla as $est) {
                // dd($est);
                foreach ($queryAfd as $afd) {
                    // dd($afd);
                    if ($est['est'] == $afd['est']) {
                        $defPLASidak[$est['est']][$afd['nama']]['null'] = 0;
                    }
                }
            }

            foreach ($defPLASidak as $key => $estValue) {
                foreach ($estValue as $monthKey => $monthValue) {
                    $mergedValues = [];
                    foreach ($SidakTPHPlA as $dataKey => $dataValue) {
                        if ($dataKey == $key && isset($dataValue[$monthKey])) {
                            $mergedValues = array_merge($mergedValues, $dataValue[$monthKey]);
                        }
                    }
                    $defPLASidak[$key][$monthKey] = $mergedValues;
                }
            }

            $arrPlasma = [];
            foreach ($defPLASidak as $key => $value) {
                if (!empty($value)) {
                    $jum_blokPla = 0;
                    $sum_tphPla = 0;
                    $sum_karungPla = 0;
                    $sum_buahPla = 0;
                    $sum_restantPla = 0;

                    $skor_tphPla = 0;
                    $skor_karungPla = 0;
                    $skor_buahPla = 0;
                    $skor_restanPla = 0;
                    $skoreTotalPla = 0;
                    foreach ($value as $key1 => $value1) {
                        if (!empty($value1)) {
                            $jum_blok = 0;
                            $sum_bt_tph = 0;
                            $sum_bt_jalan = 0;
                            $sum_bt_bin = 0;
                            $sum_all = 0;
                            $sum_all_karung = 0;
                            $sum_jum_karung = 0;
                            $sum_buah_tinggal = 0;
                            $sum_all_bt_tgl = 0;
                            $sum_restan_unreported = 0;
                            $sum_all_restan_unreported = 0;
                            $listBlokPerAfd = [];
                            foreach ($value1 as $key2 => $value2) {
                                if (is_array($value2)) {
                                    $blok = $value2['est'] . ' ' . $value2['afd'] . ' ' . $value2['blok'];

                                    if (!in_array($blok, $listBlokPerAfd)) {
                                        array_push($listBlokPerAfd, $blok);
                                    }

                                    // $jum_blok = count(float)($listBlokPerAfd);
                                    $jum_blok = count($listBlokPerAfd);

                                    $sum_bt_tph += $value2['bt_tph'];
                                    $sum_bt_jalan += $value2['bt_jalan'];
                                    $sum_bt_bin += $value2['bt_bin'];

                                    $sum_jum_karung += $value2['jum_karung'];
                                    $sum_buah_tinggal += $value2['buah_tinggal'];
                                    $sum_restan_unreported += $value2['restan_unreported'];
                                }
                            }
                            //change value 3to float type
                            $sum_all = $sum_bt_tph + $sum_bt_jalan + $sum_bt_bin;
                            $sum_all_karung = $sum_jum_karung;
                            $sum_all_bt_tgl = $sum_buah_tinggal;
                            $sum_all_restan_unreported = $sum_restan_unreported;
                            if ($jum_blok == 0) {
                                $skor_brd = 0;
                                $skor_kr = 0;
                                $skor_buahtgl = 0;
                                $skor_restan = 0;
                            } else {
                                $skor_brd = round($sum_all / $jum_blok, 1);
                                $skor_kr = round($sum_all_karung / $jum_blok, 1);
                                $skor_buahtgl = round($sum_all_bt_tgl / $jum_blok, 1);
                                $skor_restan = round($sum_all_restan_unreported / $jum_blok, 1);
                            }

                            $skoreTotal = skorBRDsidak($skor_brd) + skorKRsidak($skor_kr) + skorBHsidak($skor_buahtgl) + skorRSsidak($skor_restan);

                            $arrPlasma[$key][$key1]['karung_tes'] = $sum_all_karung;
                            $arrPlasma[$key][$key1]['tph_test'] = $sum_all;
                            $arrPlasma[$key][$key1]['buah_test'] = $sum_all_bt_tgl;
                            $arrPlasma[$key][$key1]['restant_tes'] = $sum_all_restan_unreported;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = $jum_blok;

                            $arrPlasma[$key][$key1]['brd_blok'] = skorBRDsidak($skor_brd);
                            $arrPlasma[$key][$key1]['kr_blok'] = skorKRsidak($skor_kr);
                            $arrPlasma[$key][$key1]['buah_blok'] = skorBHsidak($skor_buahtgl);
                            $arrPlasma[$key][$key1]['restan_blok'] = skorRSsidak($skor_restan);
                            $arrPlasma[$key][$key1]['skorWil'] = $skoreTotal;

                            $jum_blokPla += $jum_blok;
                            $sum_karungPla += $sum_all_karung;
                            $sum_restantPla += $sum_all_restan_unreported;
                            $sum_tphPla += $sum_all;
                            $sum_buahPla += $sum_all_bt_tgl;
                        } else {
                            $arrPlasma[$key][$key1]['karung_tes'] = 0;
                            $arrPlasma[$key][$key1]['tph_test'] = 0;
                            $arrPlasma[$key][$key1]['buah_test'] = 0;
                            $arrPlasma[$key][$key1]['restant_tes'] = 0;

                            $arrPlasma[$key][$key1]['jumlah_blok'] = 0;

                            $arrPlasma[$key][$key1]['brd_blok'] = 0;
                            $arrPlasma[$key][$key1]['kr_blok'] = 0;
                            $arrPlasma[$key][$key1]['buah_blok'] = 0;
                            $arrPlasma[$key][$key1]['restan_blok'] = 0;
                            $arrPlasma[$key][$key1]['skorWil'] = 0;
                        }
                    }

                    if ($jum_blokPla != 0) {
                        $skor_tphPla = round($sum_tphPla / $jum_blokPla, 2);
                        $skor_karungPla = round($sum_karungPla / $jum_blokPla, 2);
                        $skor_buahPla = round($sum_buahPla / $jum_blokPla, 2);
                        $skor_restanPla = round($sum_restantPla / $jum_blokPla, 2);
                    } else {
                        $skor_tphPla = 0;
                        $skor_karungPla = 0;
                        $skor_buahPla = 0;
                        $skor_restanPla = 0;
                    }

                    $skoreTotalPla = skorBRDsidak($skor_tphPla) + skorKRsidak($skor_karungPla) + skorBHsidak($skor_buahPla) + skorRSsidak($skor_restanPla);
                    if (
                        $jum_blokPla == 0 &&
                        $sum_karungPla == 0 &&
                        $sum_restantPla == 0 &&
                        $sum_tphPla == 0 &&
                        $sum_buahPla == 0
                    ) {
                        $arrPlasma[$key]['SkorPlasma'] = 0;
                    } else {
                        $arrPlasma[$key]['SkorPlasma'] = $skoreTotalPla;
                    }

                    $arrPlasma[$key]['karung_tes'] = $sum_karungPla;
                    $arrPlasma[$key]['tph_test'] = $sum_tphPla;
                    $arrPlasma[$key]['buah_test'] = $sum_buahPla;
                    $arrPlasma[$key]['restant_tes'] = $sum_restantPla;

                    $arrPlasma[$key]['jumlah_blok'] = $jum_blokPla;

                    $arrPlasma[$key]['brd_blok'] = skorBRDsidak($skor_tphPla);
                    $arrPlasma[$key]['kr_blok'] = skorKRsidak($skor_karungPla);
                    $arrPlasma[$key]['buah_blok'] = skorBHsidak($skor_buahPla);
                    $arrPlasma[$key]['restan_blok'] = skorRSsidak($skor_restanPla);
                }
            }
            // dd($arrPlasma);
            foreach ($arrPlasma as $key1 => $estates) {
                if (is_array($estates)) {
                    // $sortedData = array();
                    $sortedDataEst = [];
                    foreach ($estates as $estateName => $data) {
                        // dd($data);
                        if (is_array($data)) {
                            $sortedDataEst[] = [
                                'key1' => $key1,
                                'estateName' => $estateName,
                                'data' => $data,
                            ];
                        }
                    }
                    usort($sortedDataEst, function ($a, $b) {
                        return $b['data']['skorWil'] - $a['data']['skorWil'];
                    });
                    $rank = 1;
                    foreach ($sortedDataEst as $sortedest) {
                        $arrPlasma[$key1][$sortedest['estateName']]['rank'] = $rank;
                        $rank++;
                    }
                    unset($sortedDataEst);
                }
            }
            // dd($arrPlasma);

            $PlasmaWIl = [];
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => $value1) {
                        if (is_array($value1)) {
                            // dd($value1);
                            $inc = 0;
                            $est = $key;
                            $skor = $value1['skorWil'];
                            // dd($skor);
                            $EM = $key1;

                            $rank = $value1['rank'];
                            // $rank = $value1['rank'];
                            $nama = '-';
                            foreach ($queryAsisten as $key4 => $value4) {
                                if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                                    $nama = $value4['nama'];
                                    break;
                                }
                            }
                            $PlasmaWIl[] = [
                                'est' => $est,
                                'afd' => $EM,
                                'nama' => $nama,
                                'skor' => $skor,
                                'rank' => $rank,
                            ];
                            $inc++;
                        }
                    }
                }
            }

            $PlasmaWIl = array_values($PlasmaWIl);

            $PlasmaEM = [];
            $NamaEm = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $EM = 'EM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $EM) {
                            $NamaEm = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($EM)) {
                $PlasmaEM[] = [
                    'est' => $est,
                    'afd' => $EM,
                    'namaEM' => $NamaEm,
                    'Skor' => $skor,
                ];
            }

            $PlasmaEM = array_values($PlasmaEM);

            $plasmaGM = [];
            $namaGM = '-';
            foreach ($arrPlasma as $key => $value) {
                if (is_array($value)) {
                    $inc = 0;
                    $est = $key;
                    $skor = $value['SkorPlasma'];
                    $GM = 'GM';
                    // dd($value);
                    foreach ($queryAsisten as $key4 => $value4) {
                        if (is_array($value4) && $value4['est'] == $est && $value4['afd'] == $GM) {
                            $namaGM = $value4['nama'];
                        }
                    }
                    $inc++;
                }
            }

            if (isset($GM)) {
                $plasmaGM[] = [
                    'est' => $est,
                    'afd' => $GM,
                    'namaGM' => $namaGM,
                    'Skor' => $skor,
                ];
            }

            $plasmaGM = array_values($plasmaGM);
            //masukan semua yang sudah selese di olah di atas ke dalam vaiabel terserah kemudian masukan kedalam aray
            //karena chart hanya bisa menerima inputan json
            $queryWilChart = DB::connection('mysql2')
                ->table('wil')
                ->whereIn('regional', [$regSidak])
                ->pluck('nama');

            $arrView = [];
            $list_all_will = changeKTE4ToKTE($list_all_will);
            // dd($result);


            $list_all_est = BpthKTE($list_all_est);
            // dd($result);


            $queryWill = list_wil($queryWill);

            // dd($list_all_will);
            $arrView['list_estate'] = $queryEst;
            $arrView['list_wilayah'] = $queryWill;
            $arrView['list_wilayah2'] = $queryWilChart;
            // $arrView['restant'] = $dataSkorAwalRestant;

            $arrView['list_all_wil'] = $list_all_will;
            $arrView['list_all_est'] = $list_all_est;
            $arrView['list_skor_gm'] = $skor_gm_wil;
            $arrView['list_skor_rh'] = $skor_rh;
            $arrView['PlasmaWIl'] = $PlasmaWIl;
            $arrView['PlasmaEM'] = $PlasmaEM;
            $arrView['plasmaGM'] = $plasmaGM;
            $arrView['list_skor_gmNew'] = $GmSkorWil;
            // $arrView['karung'] = $dataSkorAwalKr;
            // $arrView['buah'] = $dataSkorAwalBuah;
            // // dd($queryEst);
            $keysToRemove = ['SRE', 'LDE', 'SKE'];

            // Loop through the array and remove the elements with the specified keys
            foreach ($keysToRemove as $key) {
                unset($arrBtTPHperEst[$key]);
                unset($arrKRest[$key]);
                unset($arrBHest[$key]);
                unset($arrRSest[$key]);
            }

            // dd($arrBtTPHperEst);
            // masukan ke array data penjumlahan dari estate
            $arrView['val_bt_tph'] = $arrBtTPHperEst; //data jsen brondolan tinggal di tph
            $arrView['val_kr_tph'] = $arrKRest; //data jsen karung yang berisi buah
            $arrView['val_bh_tph'] = $arrBHest; //data jsen buah yang tinggal
            $arrView['val_rs_tph'] = $arrRSest; //data jsen restan yang tidak dilaporkan
            //masukan ke array data penjumlahan dari wilayah
            $arrView['val_kr_tph_wil'] = $arrKRestWil; //data jsen karung yang berisi buah
            $arrView['val_bt_tph_wil'] = $arrBtTPHperWil; //data jsen brondolan tinggal di tph
            $arrView['val_bh_tph_wil'] = $arrBHestWil; //data jsen buah yang tinggal
            $arrView['val_rs_tph_wil'] = $arrRestWill; //data jsen restan yang tidak dilaporkan
            // dd($arrBtTPHperEst);
            echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
            exit();
        }
    }
    public function graphFilterYear(Request $request)
    {
        $regData = $request->get('reg');
        $estData = $request->get('est');
        $yearGraph = $request->get('yearGraph');

        $queryReg = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$regData])
            ->pluck('id')
            ->toArray();

        $querySidak = DB::connection('mysql2')->table('sidak_tph')
            ->select("sidak_tph.*")
            ->whereNotIn('est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->get();
        $DataEstate = $querySidak->groupBy(['est', 'afd']);
        $DataEstate = json_decode($DataEstate, true);

        //menghitung buat table tampilkan pertahun
        $queryTph = DB::connection('mysql2')->table('sidak_tph')
            ->select(
                "sidak_tph.*",
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'),
                DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun')
            )
            ->whereNotIn('est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->whereYear('datetime', $yearGraph)
            ->get();
        $queryTph = $queryTph->groupBy(['est', 'afd']);
        $queryTph = json_decode($queryTph, true);

        //afdeling
        $queryAfd = DB::connection('mysql2')->table('afdeling')
            ->select(
                'afdeling.id',
                'afdeling.nama',
                'estate.est'
            ) //buat mengambil data di estate db dan willayah db
            ->whereNotIn('estate.est', ['Plasma1', 'Plasma2', 'Plasma3'])
            ->join('estate', 'estate.id', '=', 'afdeling.estate') //kemudian di join untuk mengambil est perwilayah
            ->get();
        $queryAfd = json_decode($queryAfd, true);
        //estate
        $queryEste = DB::connection('mysql2')->table('estate')->whereIn('wil', $queryReg)->get();
        $queryEste = json_decode($queryEste, true);

        $bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $dataBulananTph = array();
        foreach ($queryTph as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    $month = date('F', strtotime($value3['datetime']));
                    if (!array_key_exists($month, $dataBulananTph)) {
                        $dataBulananTph[$month] = array();
                    }
                    if (!array_key_exists($key, $dataBulananTph[$month])) {
                        $dataBulananTph[$month][$key] = array();
                    }
                    if (!array_key_exists($key2, $dataBulananTph[$month][$key])) {
                        $dataBulananTph[$month][$key][$key2] = array();
                    }
                    $dataBulananTph[$month][$key][$key2][$key3] = $value3;
                }
            }
        }

        $defaultTph = array();
        foreach ($bulan as $month) {
            foreach ($queryEste as $est) {
                foreach ($queryAfd as $afd) {
                    if ($est['est'] == $afd['est']) {
                        $defaultTph[$est['est']][$month][$afd['nama']] = 0;
                    }
                }
            }
        }

        //menimpa nilai default mutu transport dengan yang memiliki value
        foreach ($defaultTph as $key => $estValue) {
            foreach ($estValue as $monthKey => $monthValue) {
                foreach ($dataBulananTph as $dataKey => $dataValue) {
                    if ($dataKey == $monthKey) {
                        foreach ($dataValue as $dataEstKey => $dataEstValue) {
                            if ($dataEstKey == $key) {
                                $defaultTph[$key][$monthKey] = array_merge($monthValue, $dataEstValue);
                            }
                        }
                    }
                }
            }
        }

        $sidakTphEst = array();
        foreach ($defaultTph as $key => $value) {
            foreach ($value as $key1 => $value2) if (!empty($value2)) {
                $luas_ha_est = 0;
                $jml_blok_est = 0;
                $sum_bt_tph_est = 0;
                $sum_bt_jln_est = 0;
                $sum_bt_bin_est = 0;
                $sum_krg_est = 0;
                $sumBuah_est = 0;
                $sumRst_est = 0;
                foreach ($value2 as $key2 => $value3) {
                    if (is_array($value3)) {
                        $luas_ha = 0;
                        $jml_blok = 0;
                        $sum_bt_tph = 0;
                        $sum_bt_jln = 0;
                        $sum_bt_bin = 0;
                        $sum_krg = 0;
                        $sumBuah = 0;
                        $sumRst = 0;
                        $listBlokPerAfd = array();
                        foreach ($value3 as $key3 => $value4) {
                            if (!in_array($value4['est'] . ' ' . $value4['afd'] . ' ' . $value4['blok'], $listBlokPerAfd)) {
                                $listBlokPerAfd[] = $value4['est'] . ' ' . $value4['afd'] . ' ' . $value4['blok'];
                                $luas_ha += $value4['luas'];
                            }
                            $jml_blok = count($listBlokPerAfd);
                            $sum_bt_tph += $value4['bt_tph'];
                            $sum_bt_jln += $value4['bt_jalan'];
                            $sum_bt_bin += $value4['bt_bin'];
                            $sum_krg += $value4['jum_karung'];
                            $sumBuah += $value4['buah_tinggal'];
                            $sumRst += $value4['restan_unreported'];
                        }
                        $luas_ha_est += $luas_ha;
                        $jml_blok_est += $jml_blok;
                        $sum_bt_tph_est += $sum_bt_tph;
                        $sum_bt_jln_est += $sum_bt_jln;
                        $sum_bt_bin_est += $sum_bt_bin;
                        $sum_krg_est += $sum_krg;
                        $sumBuah_est += $sumBuah;
                        $sumRst_est += $sumRst;

                        $tot_bt = ($sum_bt_tph + $sum_bt_jln + $sum_bt_bin);
                        $sidakTphEst[$key][$key1][$key2]['jml_blok'] = $jml_blok;
                        $sidakTphEst[$key][$key1][$key2]['luas_ha'] = $luas_ha;
                        $sidakTphEst[$key][$key1][$key2]['bt_tph'] = $sum_bt_tph;
                        $sidakTphEst[$key][$key1][$key2]['bt_jln'] = $sum_bt_jln;
                        $sidakTphEst[$key][$key1][$key2]['bt_bin'] = $sum_bt_bin;
                        $sidakTphEst[$key][$key1][$key2]['tot_bt'] = $tot_bt;
                        $sidakTphEst[$key][$key1][$key2]['divBt'] = round($tot_bt / $jml_blok, 2);
                        $sidakTphEst[$key][$key1][$key2]['skorBt'] = skor_bt_tph(round($tot_bt / $jml_blok, 2));
                        $sidakTphEst[$key][$key1][$key2]['sum_krg'] = $sum_krg;
                        $sidakTphEst[$key][$key1][$key2]['divKrg'] = round($sum_krg / $jml_blok, 2);
                        $sidakTphEst[$key][$key1][$key2]['skorKrg'] = skor_krg_tph(round($sum_krg / $jml_blok, 2));
                        $sidakTphEst[$key][$key1][$key2]['sumBuah'] = $sumBuah;
                        $sidakTphEst[$key][$key1][$key2]['divBuah'] = round($sumBuah / $jml_blok, 2);
                        $sidakTphEst[$key][$key1][$key2]['skorBuah'] = skor_buah_tph(round($sumBuah / $jml_blok, 2));
                        $sidakTphEst[$key][$key1][$key2]['sumRst'] = $sumRst;
                        $sidakTphEst[$key][$key1][$key2]['divRst'] = round($sumRst / $jml_blok, 2);
                        $sidakTphEst[$key][$key1][$key2]['skorRst'] = skor_rst_tph(round($sumRst / $jml_blok, 2));
                        $sidakTphEst[$key][$key1][$key2]['allSkor'] = skor_bt_tph(round($tot_bt / $jml_blok, 2)) + skor_krg_tph(round($sum_krg / $jml_blok, 2)) + skor_buah_tph(round($sumBuah / $jml_blok, 2)) + skor_rst_tph(round($sumRst / $jml_blok, 2));
                    } else {
                        $sidakTphEst[$key][$key1][$key2]['jml_blok'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['luas_ha'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['bt_tph'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['bt_jln'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['bt_bin'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['tot_bt'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['divBt'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['skorBt'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['sum_krg'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['divKrg'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['skorKrg'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['sumBuah'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['divBuah'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['skorBuah'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['sumRst'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['divRst'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['skorRst'] = 0;
                        $sidakTphEst[$key][$key1][$key2]['allSkor'] = 0;
                    }
                }
                $tot_bt_est = ($sum_bt_tph_est + $sum_bt_jln_est + $sum_bt_bin_est);
                $divBt_est = $jml_blok_est == 0 ? $tot_bt_est : round($tot_bt_est / $jml_blok_est, 2);
                $divKrg_est = $jml_blok_est == 0 ? $sum_krg_est : round($sum_krg_est / $jml_blok_est, 2);
                $divBuah_est = $jml_blok_est == 0 ? $sumBuah_est : round($sumBuah_est / $jml_blok_est, 2);
                $divRst_est = $jml_blok_est == 0 ? $sumRst_est : round($sumRst_est / $jml_blok_est, 2);
                $sidakTphEst[$key][$key1]['jml_blok_est'] = $jml_blok_est;
                $sidakTphEst[$key][$key1]['luas_ha_est'] = $luas_ha_est;
                $sidakTphEst[$key][$key1]['bt_tph_est'] = $sum_bt_tph_est;
                $sidakTphEst[$key][$key1]['bt_jln_est'] = $sum_bt_jln_est;
                $sidakTphEst[$key][$key1]['bt_bin_est'] = $sum_bt_bin_est;
                $sidakTphEst[$key][$key1]['tot_bt_est'] = $tot_bt_est;
                $sidakTphEst[$key][$key1]['divBt_est'] = $divBt_est;
                $sidakTphEst[$key][$key1]['skorBt_est'] = skor_bt_tph($divBt_est);
                $sidakTphEst[$key][$key1]['sum_krg_est'] = $sum_krg_est;
                $sidakTphEst[$key][$key1]['divKrg_est'] = $divKrg_est;
                $sidakTphEst[$key][$key1]['skorKrg_est'] = skor_krg_tph($divKrg_est);
                $sidakTphEst[$key][$key1]['sumBuah_est'] = $sumBuah_est;
                $sidakTphEst[$key][$key1]['divBuah_est'] = $divBuah_est;
                $sidakTphEst[$key][$key1]['skorBuah_est'] = skor_buah_tph($divBuah_est);
                $sidakTphEst[$key][$key1]['sumRst_est'] = $sumRst_est;
                $sidakTphEst[$key][$key1]['divRst_est'] = $divRst_est;
                $sidakTphEst[$key][$key1]['skorRst_est'] = skor_rst_tph($divRst_est);
                $sidakTphEst[$key][$key1]['allSkor_est'] = skor_bt_tph($divBt_est) + skor_krg_tph($divKrg_est) + skor_buah_tph($divBuah_est) + skor_rst_tph($divRst_est);
            } else {
                $sidakTphEst[$key][$key1]['jml_blok_est'] = 0;
                $sidakTphEst[$key][$key1]['luas_ha_est'] = 0;
                $sidakTphEst[$key][$key1]['bt_tph_est'] = 0;
                $sidakTphEst[$key][$key1]['bt_jln_est'] = 0;
                $sidakTphEst[$key][$key1]['bt_bin_est'] = 0;
                $sidakTphEst[$key][$key1]['tot_bt_est'] = 0;
                $sidakTphEst[$key][$key1]['divBt_est'] = 0;
                $sidakTphEst[$key][$key1]['skorBt_est'] = 0;
                $sidakTphEst[$key][$key1]['sum_krg_est'] = 0;
                $sidakTphEst[$key][$key1]['divKrg_est'] = 0;
                $sidakTphEst[$key][$key1]['skorKrg_est'] = 0;
                $sidakTphEst[$key][$key1]['sumBuah_est'] = 0;
                $sidakTphEst[$key][$key1]['divBuah_est'] = 0;
                $sidakTphEst[$key][$key1]['skorBuah_est'] = 0;
                $sidakTphEst[$key][$key1]['sumRst_est'] = 0;
                $sidakTphEst[$key][$key1]['divRst_est'] = 0;
                $sidakTphEst[$key][$key1]['skorRst_est'] = 0;
                $sidakTphEst[$key][$key1]['allSkor_est'] = 0;
            }
        }

        $brdGraphMonth = array();
        $krgGraphMonth = array();
        $buahGraphMonth = array();
        $rstGraphMonth = array();
        foreach ($sidakTphEst as $key => $value) {
            foreach ($value as $key2  => $value2) {
                $brdGraphMonth[$key][$key2]['brdGraph'] = $value2['divBt_est'];
                $krgGraphMonth[$key][$key2]['krgGraph'] = $value2['divKrg_est'];
                $buahGraphMonth[$key][$key2]['buahGraph'] = $value2['divBuah_est'];
                $rstGraphMonth[$key][$key2]['rstGraph'] = $value2['divRst_est'];
            }
        }

        $rekapBrdGraph = [];
        if ($estData !== 'CWS1' && isset($brdGraphMonth[$estData])) {
            foreach ($brdGraphMonth[$estData] as $month => $data) {
                $rekapBrdGraph[$estData][$month] = isset($data['brdGraph']) ? $data['brdGraph'] : 0;
            }
        } else {
            $rekapBrdGraph[$estData] = [
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

        $rekapKrgGraph = [];
        if ($estData !== 'CWS1' && isset($krgGraphMonth[$estData])) {
            foreach ($krgGraphMonth[$estData] as $month => $data) {
                $rekapKrgGraph[$estData][$month] = isset($data['krgGraph']) ? $data['krgGraph'] : 0;
            }
        } else {
            $rekapKrgGraph[$estData] = [
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

        $rekapBuahGraph = [];
        if ($estData !== 'CWS1' && isset($buahGraphMonth[$estData])) {
            foreach ($buahGraphMonth[$estData] as $month => $data) {
                $rekapBuahGraph[$estData][$month] = isset($data['buahGraph']) ? $data['buahGraph'] : 0;
            }
        } else {
            $rekapBuahGraph[$estData] = [
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

        $rekapRstGraph = [];
        if ($estData !== 'CWS1' && isset($rstGraphMonth[$estData])) {
            foreach ($rstGraphMonth[$estData] as $month => $data) {
                $rekapRstGraph[$estData][$month] = isset($data['rstGraph']) ? $data['rstGraph'] : 0;
            }
        } else {
            $rekapRstGraph[$estData] = [
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

        $scBrdGraph = array();
        foreach ($rekapBrdGraph as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $scBrdGraph[] = $value1;
            }
        }
        $scKrgGraph = array();
        foreach ($rekapKrgGraph as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $scKrgGraph[] = $value1;
            }
        }
        $scBuahGraph = array();
        foreach ($rekapBuahGraph as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $scBuahGraph[] = $value1;
            }
        }
        $scRstGraph = array();
        foreach ($rekapRstGraph as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $scRstGraph[] = $value1;
            }
        }
        $keysToRemove = ['SRE', 'LDE', 'SKE'];

        // Loop through the array and remove the elements with the specified keys
        // foreach ($keysToRemove as $key) {
        //     unset($arrBtTPHperEst[$key]);
        //     unset($arrKRest[$key]);
        //     unset($arrBHest[$key]);
        //     unset($arrRSest[$key]);
        // }

        // dd($scBrdGraph, $scKrgGraph, $scBuahGraph, $scRstGraph);
        $arrView = array();
        $arrView['brdGraph'] =  $scBrdGraph;
        $arrView['krgGraph'] =  $scKrgGraph;
        $arrView['buahGraph'] =  $scBuahGraph;
        $arrView['rstGraph'] =  $scRstGraph;
        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }

    public function notfound()
    {

        return view('404');
    }

    public function detailSidakTph($est, $afd, $start, $last)
    {
        $query = DB::connection('mysql2')->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            ->where('sidak_tph.afd', $afd)
            ->whereBetween('sidak_tph.datetime', [$start, $last])
            ->get();

        $query = $query->groupBy(function ($item) {
            return $item->blok;
        });

        // dd($query);
        $datas = array();
        $img = array();
        foreach ($query as $key => $value) {
            $inc = 0;
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
                if (!empty($value2->foto_temuan)) {
                    $img[$key][$inc]['foto'] = $value2->foto_temuan;
                    $img[$key][$inc]['title'] = $value2->est . ' ' .  $value2->afd . ' - ' . $value2->blok;
                    $inc++;
                }
            }
        }

        $imgNew = array();
        foreach ($img as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $imgNew[] = $value2;
            }
        }
        // dd($img);

        $queryBlok = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            ->where('sidak_tph.afd', $afd)
            ->whereBetween('sidak_tph.datetime', [$start, $last])
            ->groupBy('sidak_tph.blok')
            ->orderBy('sidak_tph.blok', 'asc')
            ->get()->toArray();

        return view('detailSidakTPH', ['est' => $est, 'afd' => $afd, 'start' => $start, 'last' => $last, 'data' => $datas, 'img' => $imgNew, 'blok' => $queryBlok]);
    }


    public function getPlotLine(Request $request)
    {
        $afd = $request->get('afd');
        $est = $request->get('est');
        $start = $request->get('start');
        $last = $request->get('last');

        $query = DB::connection('mysql2')->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            ->where('sidak_tph.afd', $afd)
            ->whereBetween('sidak_tph.datetime', [$start, $last])
            ->get();

        $query = $query->groupBy(function ($item) {
            return $item->blok;
        });

        $datas = array();
        $img = array();
        foreach ($query as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
                if (!empty($value2->foto_temuan)) {
                    $img[] = $value2->foto_temuan;
                }
            }
        }

        $plotTitik = array();
        $plotMarker = array();
        $inc = 0;

        foreach ($datas as $key => $value) {
            if (!empty($value->lat)) {
                $plotTitik[] = '[' . $value->lon . ',' . $value->lat . ']';
                $plotMarker[$inc]['latln'] = '[' . $value->lat . ',' . $value->lon . ']';
                $plotMarker[$inc]['notph'] = $value->no_tph;
                $plotMarker[$inc]['blok'] = $value->blok;
                $plotMarker[$inc]['brondol_tinggal'] = $value->bt_tph + $value->bt_jalan + $value->bt_bin;
                $plotMarker[$inc]['jum_karung'] = $value->jum_karung;
                $plotMarker[$inc]['buah_tinggal'] = $value->buah_tinggal;
                $plotMarker[$inc]['restan_unreported'] = $value->restan_unreported;
                $plotMarker[$inc]['datetime'] = $value->datetime;

                $fotoTemuan = explode('; ', $value->foto_temuan);
                $komentar = explode('; ', $value->komentar);

                // If the number of items is the same for both arrays
                if (count($fotoTemuan) == count($komentar)) {
                    for ($i = 0; $i < count($fotoTemuan); $i++) {
                        $plotMarker[$inc]['foto_temuan' . ($i + 1)] = $fotoTemuan[$i];
                        $plotMarker[$inc]['komentar' . ($i + 1)] = $komentar[$i];
                        $plotMarker[$inc]['jam'] = Carbon::parse($value->datetime)->format('H:i');
                    }
                } else {
                    // Handle the case where the number of items is different
                    // This assumes that the number of items in `foto_temuan` and `komentar` will always match
                    // If they don't match, you'll need to handle it accordingly
                    // For example, you can ignore the extra items or take specific action
                    // In this code, it simply uses the first item of each array and ignores the rest

                    $plotMarker[$inc]['foto_temuan'] = $fotoTemuan[0];
                    $plotMarker[$inc]['komentar'] = $komentar[0];
                    $plotMarker[$inc]['jam'] = Carbon::parse($value->datetime)->format('H:i');
                }

                $inc++;
            }
        }

        // dd($datas);

        $list_blok = array();
        foreach ($datas as $key => $value) {
            $list_blok[$est][] = $value->blok;
        }

        $blokPerEstate = array();
        $estateQuery = DB::connection('mysql2')->Table('estate')
            ->join('afdeling', 'afdeling.estate', 'estate.id')
            ->where('est', $est)->get();

        $listIdAfd = array();
        // dd($estateQuery);

        foreach ($estateQuery as $key => $value) {

            $blokPerEstate[$est][$value->nama] =  DB::connection('mysql2')->Table('blok')
                // ->join('blok', 'blok.afdeling', 'afdeling.id')
                // ->where('afdeling.estate', $value->id)->get();
                ->where('afdeling', $value->id)->pluck('nama', 'id');
            $listIdAfd[] = $value->id;
        }

        // dd($blokPerEstate);


        $result_list_blok = array();
        foreach ($list_blok as $key => $value) {
            foreach ($value as $key2 => $data) {
                if (strlen($data) == 5) {
                    $result_list_blok[$key][$data] = substr($data, 0, -2);
                } else if (strlen($data) == 6) {
                    $sliced = substr_replace($data, '', 1, 1);
                    $result_list_blok[$key][$data] = substr($sliced, 0, -2);
                } else if (strlen($data) == 3) {
                    $result_list_blok[$key][$data] = $data;
                } else if (strpos($data, 'CBI') !== false) {
                    $result_list_blok[$key][$data] = substr($data, 0, -4);
                } else if (strpos($data, 'CB') !== false) {
                    $sliced = substr_replace($data, '', 1, 1);
                    $result_list_blok[$key][$data] = substr($sliced, 0, -3);
                }
            }
        }

        $result_list_all_blok = array();
        foreach ($blokPerEstate as $key2 => $value) {
            foreach ($value as $key3 => $afd) {
                foreach ($afd as $key4 => $data) {
                    if (strlen($data) == 4) {
                        $result_list_all_blok[$key2][] = substr_replace($data, '', 1, 1);
                    }
                }
            }
        }

        // //bandingkan list blok query dan list all blok dan get hanya blok yang cocok
        $result_blok = array();
        if (array_key_exists($est, $result_list_all_blok)) {
            $query = array_unique($result_list_all_blok[$est]);
            $result_blok[$est] = array_intersect($result_list_blok[$est], $query);
        }
        // dd($result_list_blok, $result_blok, $listIdAfd);


        //get lat lang dan key $result_blok atau semua list_blok

        $blokLatLn = array();

        foreach ($result_list_blok as $key => $value) {
            $inc = 0;
            foreach ($value as $key2 => $data) {
                $newData = substr_replace($data, '0', 1, 0);
                $query = '';
                $query = DB::connection('mysql2')->table('blok')
                    ->select('blok.*')
                    // ->where('blok.nama', $newData)
                    // ->orWhere('blok.nama', $data)
                    ->whereIn('blok.afdeling', $listIdAfd)
                    ->get();

                // dd($newData, $data);

                $latln = '';
                foreach ($query as $key3 => $val) {
                    if ($val->nama == $newData || $val->nama == $data) {
                        $latln .= '[' . $val->lon . ',' . $val->lat . '],';
                    }
                }

                $estate = DB::connection('mysql2')->table('estate')
                    ->select('estate.*')
                    ->where('estate.est', $est)
                    ->first();

                $nama_estate = $estate->nama;

                $blokLatLn[$inc]['blok'] = $key2;
                $blokLatLn[$inc]['estate'] = $nama_estate;
                $blokLatLn[$inc]['latln'] = rtrim($latln, ',');
                $inc++;
            }
        }

        // dd($plotMarker);
        $plot['plot'] = $plotTitik;
        $plot['marker'] = $plotMarker;
        $plot['blok'] = $blokLatLn;
        // dd($plot);
        echo json_encode($plot);
    }

    public function hapusDetailSidak(Request $request)
    {
        $ids = $request->input('ids');
        $start = $request->input('start');
        $last = $request->input('last');
        $est = $request->input('est');
        $afd = $request->input('afd');

        if (is_array($ids)) {
            // Delete each item with the corresponding id
            foreach ($ids as $id) {
                DB::connection('mysql2')->table('sidak_tph')
                    ->where('id', $id)
                    ->delete();
            }
        } else {
            // If only one id is present, delete the item with that id
            DB::connection('mysql2')->table('sidak_tph')
                ->where('id', $ids)
                ->delete();
        }

        session()->flash('status', 'Data Sidak berhasil dihapus!');
        return redirect()->route('detailSidakTph', ['est' => $est, 'afd' => $afd, 'start' => $start, 'last' => $last]);
    }


    public function BasidakTph($est, $start, $last, $regional)
    {


        $query = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil')
            ->join('estate', 'estate.est', '=', 'sidak_tph.est')
            ->where('sidak_tph.est', $est)
            ->where(function ($query) use ($start, $last) {
                $query->where('sidak_tph.datetime', '>=', $start)
                    ->where('sidak_tph.datetime', '<=', $last . ' 23:59:59');
            })
            ->get();


        $query = $query->groupBy(['afd']);

        $query = json_decode($query, true);
        // dd($query);
        // Extract unique dates from the array
        $unique_dates = [];
        foreach ($query as $key => $subArray) {
            foreach ($subArray as $item) {
                $date = explode(" ", $item['datetime'])[0]; // Extract only the date part
                if (!in_array($date, $unique_dates)) {
                    $unique_dates[] = $date;
                }
            }
        }

        // Sort unique dates
        sort($unique_dates);

        // dd($unique_dates);

        // dd($unique_dates, $start, $last, $est);
        // Generate the HTML select element with options
        $query2 = DB::connection('mysql2')->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            // ->where('sidak_tph.afd', $afd)
            // ->where('sidak_tph.datetime', 'like', '%' . $tanggal . '%')
            ->whereBetween('sidak_tph.datetime', [$start, $last])
            ->get();

        $query2 = $query2->groupBy(function ($item) {
            return $item->blok;
        });

        // dd($query2);
        $datas = array();
        $img = array();
        foreach ($query2 as $key => $value) {
            $inc = 0;
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
                if (!empty($value2->foto_temuan)) {
                    $img[$key][$inc]['foto'] = $value2->foto_temuan;
                    $img[$key][$inc]['title'] = $value2->est . ' ' .  $value2->afd . ' - ' . $value2->blok;
                    $inc++;
                }
            }
        }

        $imgNew = array();
        foreach ($img as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $imgNew[] = $value2;
            }
        }
        // dd($img);

        $queryBlok = DB::connection('mysql2')
            ->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            // ->where('sidak_tph.afd', $afd)
            // ->where('sidak_tph.datetime', 'like', '%' . $tanggal . '%')
            ->whereBetween('sidak_tph.datetime', [$start, $last])
            ->groupBy('sidak_tph.blok')
            ->orderBy('sidak_tph.blok', 'asc')
            ->get()->toArray();

        $arrView = array();
        $afd = '-';

        $arrView['est'] =  $est;
        $arrView['tanggal'] =  $start;
        $arrView['regional'] =  $regional;
        $arrView['afd'] =  $afd;
        $arrView['filter'] =  $unique_dates;

        // $formattedStartDate = $startDate->format('d-m-Y');
        // $formattedEndDate = $endDate->format('d-m-Y');
        json_encode($arrView);

        // return view('BaSidakTPH', $arrView);

        return view('BaSidakTPH', ['est' => $est, 'afd' => $afd, 'data' => $datas, 'img' => $imgNew, 'blok' => $queryBlok], $arrView);
    }


    public function filtersidaktphrekap(Request $request)
    {
        $dates = $request->input('tanggal');
        // $Reg = $request->input('est');
        $estate = $request->input('estate');
        $afd = $request->input('afd');


        // dd($estate, $afd);

        $perPage = 10;

        $sidak_tph = DB::connection('mysql2')->table('sidak_tph')
            ->select("sidak_tph.*", DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'))
            ->where('datetime', 'like', '%' . $dates . '%')
            ->where('sidak_tph.est', $estate)
            // ->where('sidak_tph.afd', $afd)

            ->orderBy('blok', 'asc')
            ->paginate($perPage, ['*'], 'page');


        $sidak_tph2 = DB::connection('mysql2')->table('sidak_tph')
            ->select("sidak_tph.*", DB::raw('DATE_FORMAT(sidak_tph.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y") as tahun'))
            ->where('datetime', 'like', '%' . $dates . '%')
            ->where('sidak_tph.est', $estate)
            ->get();
        $sidak_tph2 = json_decode($sidak_tph2, true);

        $arrView = array();
        $arrView['sidak_tph'] =  $sidak_tph;
        $arrView['sidak_tph2'] =  $sidak_tph2;
        $arrView['tanggal'] =  $dates;

        // dd($sidak_tph);
        echo json_encode($arrView);
        exit();
    }


    public function updateBASidakTPH(Request $request)
    {

        $est = $request->input('Estate');
        $afd = $request->input('Afdeling');
        $qc = $request->input('QC');

        // dd($date, $afd, $est);
        // mutu ancak 
        $notph = $request->input('no_tph');
        $id = $request->input('id');
        $bttph = $request->input('bttph');
        $btjalan = $request->input('btjalan');
        $btbin = $request->input('btbin');
        $jumkrng = $request->input('jumkrng');
        $buahtgl = $request->input('buahtgl');
        $restanunr = $request->input('restanunr');
        $tphsemak = $request->input('tphsemak');


        // dd($id, $qc);



        // dd($id_trans, $afd_trans, $blok_trans, $bt_trans, $komentar_trans);

        DB::connection('mysql2')->table('sidak_tph')->where('id', $id)->update([
            'no_tph' => $notph,
            'est' => $est,
            'afd' => $afd,
            'qc' => $qc,

            'bt_tph' => $bttph,
            'bt_jalan' => $btjalan,
            'bt_bin' => $btbin,
            'jum_karung' => $jumkrng,
            'buah_tinggal' => $buahtgl,
            'restan_unreported' => $restanunr,
            'tph_semak' => $tphsemak,

        ]);
    }
    public function deleteBAsidakTPH(Request $request)
    {

        $idBuah = $request->input('id');

        // dd($idBuah);
        DB::connection('mysql2')->table('sidak_tph')
            ->where('id', $idBuah)
            ->delete();

        return response()->json(['status' => 'success']);
    }

    public function pdfBAsidak(Request $request)
    {
        $est = $request->input('est');

        // $afd = $request->input('afdling');
        // $awal = $request->input('inputDates');

        $tanggal = $request->get('inputDates');
        // $regional = $request->get('regional');
        $ancakFA = DB::connection('mysql2')
            ->table('sidak_tph')
            ->select("sidak_tph.*", DB::raw('DATE_FORMAT(sidak_tph.datetime, "%Y-%m-%d") as tanggal')) // Change the format to "%Y-%m-%d"
            ->where('sidak_tph.est', $est)
            ->where('sidak_tph.datetime', 'like', '%' . $tanggal . '%')
            ->orderBy('afd', 'asc')
            ->orderBy('status', 'asc')
            ->get();

        $ancakFA = $ancakFA->groupBy(['est', 'afd', 'status', 'tanggal', 'blok']);
        $ancakFA = json_decode($ancakFA, true);

        $dateString = $tanggal;
        $dateParts = date_parse($dateString);
        $year = $dateParts['year'];
        $month = $dateParts['month'];

        $year = $year; // Replace with the desired year
        $month = $month;   // Replace with the desired month (September in this example)

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


        // dd($weeks);
        $result = [];

        // Iterate through the original array
        foreach ($ancakFA as $mainKey => $mainValue) {
            $result[$mainKey] = [];

            foreach ($mainValue as $subKey => $subValue) {
                $result[$mainKey][$subKey] = [];

                foreach ($subValue as $dateKey => $dateValue) {
                    // Remove 'H+' prefix if it exists
                    $numericIndex = is_numeric($dateKey) ? $dateKey : (strpos($dateKey, 'H+') === 0 ? substr($dateKey, 2) : $dateKey);

                    if (!isset($result[$mainKey][$subKey][$numericIndex])) {
                        $result[$mainKey][$subKey][$numericIndex] = [];
                    }

                    foreach ($dateValue as $statusKey => $statusValue) {
                        // Handle 'H+' prefix in status
                        $statusIndex = is_numeric($statusKey) ? $statusKey : (strpos($statusKey, 'H+') === 0 ? substr($statusKey, 2) : $statusKey);

                        if (!isset($result[$mainKey][$subKey][$numericIndex][$statusIndex])) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex] = [];
                        }

                        foreach ($statusValue as $blokKey => $blokValue) {
                            $result[$mainKey][$subKey][$numericIndex][$statusIndex][$blokKey] = $blokValue;
                        }
                    }
                }
            }
        }

        // result by statis week 
        $newResult = [];

        foreach ($result as $key => $value) {
            $newResult[$key] = [];

            foreach ($value as $estKey => $est) {
                $newResult[$key][$estKey] = [];

                foreach ($est as $statusKey => $status) {
                    $newResult[$key][$estKey][$statusKey] = [];

                    foreach ($weeks as $weekKey => $week) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $newResult[$key][$estKey][$statusKey]["week" . ($weekKey + 1)] = $newStatus;
                        }
                    }
                }
            }
        }

        // dd($newResult);

        // result by week status 
        $WeekStatus = [];

        foreach ($result as $key => $value) {
            $WeekStatus[$key] = [];

            foreach ($value as $estKey => $est) {
                $WeekStatus[$key][$estKey] = [];

                foreach ($weeks as $weekKey => $week) {
                    $WeekStatus[$key][$estKey]["week" . ($weekKey + 1)] = []; // Note: Use "week" . ($weekKey + 1) instead of "week" . ($weekKey + 0)

                    foreach ($est as $statusKey => $status) {
                        $newStatus = [];

                        foreach ($status as $date => $data) {
                            if (strtotime($date) >= strtotime($week["start"]) && strtotime($date) <= strtotime($week["end"])) {
                                $newStatus[$date] = $data;
                            }
                        }

                        if (!empty($newStatus)) {
                            $WeekStatus[$key][$estKey]["week" . ($weekKey + 1)][$statusKey] = $newStatus;
                        }
                    }

                    // Remove the week if it's empty
                    if (empty($WeekStatus[$key][$estKey]["week" . ($weekKey + 1)])) {
                        unset($WeekStatus[$key][$estKey]["week" . ($weekKey + 1)]);
                    }
                }
            }
        }

        // dd($WeekStatus);



        // dd($WeekStatus);

        $newDefaultWeek = [];

        foreach ($WeekStatus as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    if (is_array($value1)) {
                        foreach ($value1 as $subKey => $subValue) {
                            if (is_array($subValue)) {
                                // Check if both key 0 and key 1 exist
                                $hasKeyZero = isset($subValue[0]);
                                $hasKeyOne = isset($subValue[1]);

                                // Merge key 0 into key 1
                                if ($hasKeyZero && $hasKeyOne) {
                                    $subValue[1] = array_merge_recursive((array)$subValue[1], (array)$subValue[0]);
                                    unset($subValue[0]);
                                } elseif ($hasKeyZero && !$hasKeyOne) {
                                    // Create key 1 and merge key 0 into it
                                    $subValue[1] = $subValue[0];
                                    unset($subValue[0]);
                                }

                                // Check if keys 1 through 7 don't exist, add them with a default value of 0
                                for ($i = 1; $i <= 7; $i++) {
                                    if (!isset($subValue[$i])) {
                                        $subValue[$i] = 0;
                                    }
                                }

                                // Ensure key 8 exists, and if not, create it with a default value of an empty array
                                if (!isset($subValue[8])) {
                                    $subValue[8] = 0;
                                }

                                // Check if keys higher than 8 exist, merge them into index 8
                                for ($i = 9; $i <= 100; $i++) {
                                    if (isset($subValue[$i])) {
                                        $subValue[8] = array_merge_recursive((array)$subValue[8], (array)$subValue[$i]);
                                        unset($subValue[$i]);
                                    }
                                }
                            }
                            $newDefaultWeek[$key][$key1][$subKey] = $subValue;
                        }
                    }
                }
            }
        }
        // dd($newDefaultWeek['Plasma1']['WIL-III']);
        // dd($newDefaultWeek);

        function removeZeroFromDatetime3(&$array)
        {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    foreach ($value as $key1 => &$value2) {
                        if (is_array($value2)) {
                            foreach ($value2 as $key2 => &$value3) {
                                if (is_array($value3)) {
                                    foreach ($value3 as $key3 => &$value4) if (is_array($value4)) {
                                        foreach ($value4 as $key4 => $value5) {
                                            if ($key4 === 0 && $value5 === 0) {
                                                unset($value4[$key4]); // Unset the key 0 => 0 within the current nested array
                                            }
                                            removeZeroFromDatetime3($value4);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        removeZeroFromDatetime3($newDefaultWeek);
        // dd($newDefaultWeek);
        $calculation = [];

        foreach ($newDefaultWeek as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if (is_array($value1)) {
                    $totalKeys = [];

                    foreach ($value1 as $key2 => $value2) {
                        if (is_array($value2)) {
                            foreach ($value2 as $key3 => $value3) {
                                if (is_array($value3)) {
                                    foreach ($value3 as $key4 => $value4) {
                                        if (is_array($value4)) {
                                            $totalKeys = array_merge($totalKeys, array_keys($value4));
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Extract the first 4 characters from each key and remove '0'
                    $shortenedKeys = array_map(function ($key) {
                        $shortenedKey = substr($key, 0, 4);
                        return str_replace('0', '', $shortenedKey);
                    }, $totalKeys);

                    // Concatenate the modified keys with a comma separator
                    $calculation[$key][$key1] = implode('-', $shortenedKeys);
                }
            }
        }


        // dd($calculation);
        $hitung = [];
        // dd($newDefaultWeek);

        foreach ($newDefaultWeek as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    foreach ($value2 as $key3 => $value3) if (is_array($value3)) {
                        $brd2 = 0;
                        $janjang2 = 0;
                        $luas2 = 0;
                        foreach ($value3 as $key4 => $value4) if (is_array($value4)) {
                            $brd1 = 0;
                            $janjang1 = 0;
                            $luas1 = 0;
                            foreach ($value4 as $key5 => $value5) {
                                $bt_tph  = 0;
                                $bt_jalan = 0;
                                $bt_bin  = 0;
                                $jum_karung = 0;
                                $buah_tinggal = 0;
                                $restan_unreported = 0;
                                $brd = 0;
                                $janjang = 0;
                                $total_brondolan = 0;
                                $total_janjang = 0;
                                // dd($key3);
                                foreach ($value5 as $key6 => $value6) {
                                    $bt_tph += $value6['bt_tph'];
                                    $bt_jalan += $value6['bt_jalan'];
                                    $bt_bin += $value6['bt_bin'];
                                    $jum_karung += $value6['jum_karung'];
                                    $buah_tinggal += $value6['buah_tinggal'];
                                    $restan_unreported += $value6['restan_unreported'];
                                    $luas = $value6['luas'];
                                }

                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['brondolan_tph'] = $bt_tph;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['brondolan_jalan'] = $bt_jalan;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['brondolan_bin'] = $bt_bin;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['brondolan_karung'] = $jum_karung;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['luas'] = $luas;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['janjnang_tinggal'] = $buah_tinggal;
                                $hitung[$key][$key1][$key2][$key3][$key4][$key5]['janjang_unreported'] = $restan_unreported;


                                $brd = $bt_bin + $bt_jalan + $bt_tph + $jum_karung;
                                $janjang = $restan_unreported + $buah_tinggal;
                                $brd1 += $brd;
                                $janjang1 += $janjang;
                                $luas1 += $luas;
                            }

                            $hitung[$key][$key1][$key2][$key3][$key4]['tot_janjnag'] = $janjang1;
                            $hitung[$key][$key1][$key2][$key3][$key4]['tod_brd'] = $brd1;
                            $hitung[$key][$key1][$key2][$key3][$key4]['tod_luas'] = $luas1;


                            $janjang2 += $janjang1;
                            $brd2 += $brd1;
                            $luas2 += $luas1;
                        }

                        $status_panen = $key3;
                        [$panen_brd, $panen_jjg] = calculatePanen($status_panen);
                        $total_brondolan =  round(($brd2) * $panen_brd / 100, 1);
                        $total_janjang =  round(($janjang2) * $panen_jjg / 100, 1);
                        $hitung[$key][$key1][$key2][$key3]['tot_janjnag'] = $janjang2;
                        $hitung[$key][$key1][$key2][$key3]['tod_brd'] = $brd2;
                        $hitung[$key][$key1][$key2][$key3]['tod_luas'] = $luas2;
                        $hitung[$key][$key1][$key2][$key3]['skor_brd'] = $total_brondolan;
                        $hitung[$key][$key1][$key2][$key3]['skor_jjg'] = $total_janjang;
                        $hitung[$key][$key1][$key2][$key3]['avg'] = 1;
                    } else {
                        $hitung[$key][$key1][$key2][$key3]['tot_janjnag'] = 0;
                        $hitung[$key][$key1][$key2][$key3]['tod_brd'] = 0;
                        $hitung[$key][$key1][$key2][$key3]['tod_luas'] = 0;
                        $hitung[$key][$key1][$key2][$key3]['skor_brd'] = 0;
                        $hitung[$key][$key1][$key2][$key3]['skor_jjg'] = 0;
                        $hitung[$key][$key1][$key2][$key3]['avg'] = 0;
                    }
                }
            }
        }

        $final = [];
        foreach ($hitung as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $tot_luas = 0;
                    $tot_janjnag = 0;
                    $tod_brd = 0;
                    $avg = 0;
                    foreach ($value2 as $key3 => $value3) {
                        foreach ($calculation as $keyx => $value4) if ($key == $keyx) {
                            foreach ($value4 as $keyx1 => $value5) if ($key1 == $keyx1) {
                                $blok = $value5;
                            } # code...
                        }
                        $weekestate = [
                            'est' => $key,
                            'afd' => $key1,
                            'status' => $key3,
                            'janjang' => $value3['tot_janjnag'],
                            'brd' => $value3['tod_brd'],
                            'luas' => $value3['tod_luas'],
                            'skor_brd' => $value3['skor_brd'],
                            'skor_luas' => $value3['skor_jjg'],
                        ];

                        $final[$key][$key1][$key3] = $weekestate;

                        $tot_luas += $value3['tod_luas'];
                        $tot_janjnag += $value3['skor_jjg'];
                        $tod_brd += $value3['skor_brd'];
                        $avg += $value3['avg'];
                    } # code...
                    $final[$key][$key1]['blok'] = $blok;
                    $final[$key][$key1]['luas'] = $tot_luas;
                    $final[$key][$key1]['total_skor'] = $tot_janjnag + $tod_brd;
                    $final[$key][$key1]['skor_akhir'] = 100 - ($tot_janjnag + $tod_brd);
                } # code...
            }  # code...
        }

        // Now $keysCollection contains the keys as you described, including the date values.

        // dd($final);




        // dd($newDefaultWeek['Plasma1']['WIL-III']);
        $newSidak = array();
        $asisten_qc = DB::connection('mysql2')
            ->Table('asisten_qc')
            ->get();
        $asisten_qc = json_decode($asisten_qc, true);

        // dd($pdf, $total_pdf);

        $arrView = array();
        $arrView['hitung'] =  $final;
        $arrView['total_hitung'] =  '-';

        $arrView['est'] =  $request->input('est');
        $arrView['afd'] =  '-';
        $arrView['awal'] =  $tanggal;
        // $arrView['akhir'] =  $formattedEndDate;

        $pdf = PDF::loadView('Pdfsidaktphba', ['data' => $arrView]);

        $customPaper = array(360, 360, 360, 360);
        $pdf->set_paper('A2', 'landscape');
        // $pdf->set_paper('A2', 'potrait');

        $filename = 'BA Sidak TPH -' . $arrView['awal'] . '-' . $arrView['est']  . '.pdf';
        // $filename = 'BA Sidak TPH -' . '.pdf';

        return $pdf->stream($filename);
    }


    public function changeRegionEst(Request $request)
    {
        $reg = $request->get('region');

        // Split the string into an array of numbers
        $queryReg2 = DB::connection('mysql2')
            ->table('wil')
            ->whereIn('regional', [$reg])
            ->pluck('id')
            ->toArray();

        $EstMapVal = DB::connection('mysql2')->table('estate')
            ->whereNotIn('estate.est', ['CWS1', 'CWS2', 'CWS3', 'SRE', 'LDE', 'SKE'])
            ->whereIn('wil', $queryReg2)->pluck('est')->toArray();

        // Return the estates as JSON data
        return response()->json([
            'estates' => $EstMapVal
        ]);
    }


    public function getMapsTph(Request $request)
    {
        $afd = $request->get('afd');
        $est = $request->get('est');
        $date = $request->get('date');
        $afd2 = $request->get('afd');

        // dd($afd, $est, $date);

        $query = DB::connection('mysql2')->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            // ->where('sidak_tph.afd', $afd)
            ->where('datetime', 'like', '%' . $date . '%')
            // ->where('sidak_tph.datetime', $date)
            ->get();

        $query = $query->groupBy(function ($item) {
            return $item->blok;
        });

        // dd($query);

        $datas = array();
        $img = array();
        foreach ($query as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
                if (!empty($value2->foto_temuan)) {
                    $img[] = $value2->foto_temuan;
                }
            }
        }

        $plotTitik = array();
        $plotMarker = array();
        $inc = 0;
        // dd($datas);
        foreach ($datas as $key => $value) {
            if (!empty($value->lat)) {
                $plotTitik[] = '[' . $value->lon . ',' . $value->lat . ']';
                $plotMarker[$inc]['latln'] = '[' . $value->lat . ',' . $value->lon . ']';
                $plotMarker[$inc]['notph'] = $value->no_tph;
                $plotMarker[$inc]['blok'] = $value->blok;
                $plotMarker[$inc]['afd'] = $value->afd;
                $plotMarker[$inc]['brondol_tinggal'] = $value->bt_tph + $value->bt_jalan + $value->bt_bin;
                $plotMarker[$inc]['jum_karung'] = $value->jum_karung;
                $plotMarker[$inc]['buah_tinggal'] = $value->buah_tinggal;
                $plotMarker[$inc]['restan_unreported'] = $value->restan_unreported;
                $plotMarker[$inc]['datetime'] = $value->datetime;

                $fotoTemuan = explode('; ', $value->foto_temuan);
                $komentar = explode('; ', $value->komentar);

                // If the number of items is the same for both arrays
                if (count($fotoTemuan) == count($komentar)) {
                    for ($i = 0; $i < count($fotoTemuan); $i++) {
                        $plotMarker[$inc]['foto_temuan' . ($i + 1)] = $fotoTemuan[$i];
                        $plotMarker[$inc]['komentar' . ($i + 1)] = $komentar[$i];
                        $plotMarker[$inc]['jam'] = Carbon::parse($value->datetime)->format('H:i');
                    }
                } else {


                    $plotMarker[$inc]['foto_temuan'] = $fotoTemuan[0];
                    $plotMarker[$inc]['komentar'] = $komentar[0];
                    $plotMarker[$inc]['jam'] = Carbon::parse($value->datetime)->format('H:i');
                }

                $inc++;
            }
        }

        // dd($plotMarker);

        $list_blok = array();
        foreach ($datas as $key => $value) {
            $list_blok[$est][] = $value->blok;
        }

        $blokPerEstate = array();
        $estateQuery = DB::connection('mysql2')->Table('estate')
            ->join('afdeling', 'afdeling.estate', 'estate.id')
            ->where('est', $est)->get();

        $listIdAfd = array();
        // dd($estateQuery);

        foreach ($estateQuery as $key => $value) {

            $blokPerEstate[$est][$value->nama] =  DB::connection('mysql2')->Table('blok')
                // ->join('blok', 'blok.afdeling', 'afdeling.id')
                // ->where('afdeling.estate', $value->id)->get();
                ->where('afdeling', $value->id)->pluck('nama', 'id');
            $listIdAfd[] = $value->id;
        }

        // dd($blokPerEstate);





        $query2 = DB::connection('mysql2')->Table('sidak_tph')
            ->select('sidak_tph.*', 'estate.wil') //buat mengambil data di estate db dan willayah db
            ->join('estate', 'estate.est', '=', 'sidak_tph.est') //kemudian di join untuk mengambil est perwilayah
            ->where('sidak_tph.est', $est)
            // ->where('sidak_tph.afd', $afd2)
            ->where('datetime', 'like', '%' . $date . '%')
            ->get();

        $query2 = $query2->groupBy(function ($item) {
            return $item->blok;
        });


        $datas = array();
        $img = array();
        foreach ($query2 as $key => $value) {
            $inc = 0;
            foreach ($value as $key2 => $value2) {
                $datas[] = $value2;
                if (!empty($value2->foto_temuan)) {
                    $img[$key][$inc]['foto'] = $value2->foto_temuan;
                    $img[$key][$inc]['title'] = $value2->est . ' ' .  $value2->afd . ' - ' . $value2->blok;
                    $inc++;
                }
            }
        }

        $imgNew = array();
        foreach ($img as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $imgNew[] = $value2;
            }
        }
        // dd($imgNew);

        function isPointInPolygon($point, $polygon)
        {
            $splPoint = explode(',', $point);
            $x = $splPoint[0];
            $y = $splPoint[1];

            $vertices = array_map(function ($vertex) {
                return explode(',', $vertex);
            }, explode('$', $polygon));

            $numVertices = count($vertices);
            $isInside = false;

            for ($i = 0, $j = $numVertices - 1; $i < $numVertices; $j = $i++) {
                $xi = $vertices[$i][0];
                $yi = $vertices[$i][1];
                $xj = $vertices[$j][0];
                $yj = $vertices[$j][1];

                $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

                if ($intersect) {
                    $isInside = !$isInside;
                }
            }

            return $isInside;
        }

        $estateQuery = DB::connection('mysql2')->table('estate')
            ->select('*')
            ->join('afdeling', 'afdeling.estate', '=', 'estate.id')
            ->where('estate.est', $est)
            ->get();
        $estateQuery = json_decode($estateQuery, true);

        $listIdAfd = array();
        foreach ($estateQuery as $key => $value) {
            $listIdAfd[] = $value['id'];
        }

        $blokEstate = DB::connection('mysql2')->table('blok')
            ->select(DB::raw('DISTINCT nama, MIN(id) as id, afdeling'))
            ->whereIn('afdeling', $listIdAfd)
            ->groupBy('nama', 'afdeling')
            ->get();
        $blokEstate = json_decode($blokEstate, true);

        $blokEstateFix = array();
        foreach ($blokEstate as $key => $value) {
            $blokEstateFix[$value['afdeling']][] = $value['nama'];
        }

        // dd($blokEstateFix);
        $qrAfd = DB::connection('mysql2')->table('afdeling')
            ->select('*')
            ->get();
        $qrAfd = json_decode($qrAfd, true);

        $blokEstNewFix = array();
        foreach ($blokEstateFix as $key => $value) {
            foreach ($qrAfd as $key1 => $value1) {
                if ($value1['id'] == $key) {
                    $afdelingNama = $value1['nama'];
                }
            }
            $blokEstNewFix[$afdelingNama] = $value;
        }

        $queryBlok = DB::connection('mysql2')->table('blok')
            ->select('*')
            ->whereIn('afdeling', $listIdAfd)
            ->get();
        $queryBlok = json_decode($queryBlok, true);

        $blokLatLnEw = array();
        $inc = 0;
        foreach ($blokEstNewFix as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $latln = '';
                $latln2 = '';
                foreach ($queryBlok as $key3 => $value4) {
                    if ($value4['nama'] == $value1) {
                        $latln .= $value4['lat'] . ',' . $value4['lon'] . '$';
                        $latln2 .= '[' . $value4['lon'] . ',' . $value4['lat'] . '],';
                    }
                }

                $blokLatLnEw[$inc]['afd'] = $key;
                $blokLatLnEw[$inc]['blok'] = $value1;
                $blokLatLnEw[$inc]['latln'] = rtrim($latln, '$');
                $blokLatLnEw[$inc]['latinnew'] = rtrim($latln2, ',');
                $inc++;
            }
        }
        // dd($blokLatLnEw);
        $dtQuery = DB::connection('mysql2')->table('sidak_tph')
            ->select('*', DB::raw("DATE_FORMAT(datetime, '%H:%i:%s') AS time"))
            ->where('est', $est)
            ->where('datetime', 'LiKE', '%' . $date . '%')
            ->orderBy('time', 'asc')
            ->get();
        $dtQuery = json_decode($dtQuery, true);


        // dd($dtQuery);

        $pkLatLn = array();
        $incr = 0;
        foreach ($dtQuery as $key => $value) {
            $pkLatLn[$incr]['id'] = $value['id'];
            $pkLatLn[$incr]['latln'] = $value['lat'] . ',' . $value['lon'];
            $incr++;
        }

        // dd($pkLatLn);

        // Define an associative array to track unique combinations
        $uniqueCombinations = [];

        foreach ($blokLatLnEw as $value) {
            foreach ($pkLatLn as $marker) {
                if (isPointInPolygon($marker['latln'], $value['latln'])) {
                    // Create a unique key based on nama, estate, and latin
                    $key = $value['blok'] . '_' . $est . '_' . $value['latln'];

                    // $latln .= '[' . $val->lon . ',' . $val->lat . '],';

                    // Check if the combination already exists
                    if (!isset($uniqueCombinations[$key])) {
                        $uniqueCombinations[$key] = true; // Mark the combination as encountered
                        $messageResponse[] = [
                            'blok' => $value['blok'],
                            'estate' => $est,
                            'latln' => $value['latinnew']
                        ];
                    }
                }
            }
        }

        // dd($messageResponse);
        // dd($blokLatLnEw);
        $newArr = DB::connection('mysql2')->table('sidak_tph')
            ->select('*', DB::raw("DATE_FORMAT(datetime, '%H:%i:%s') AS time"))
            ->where('est', $est)
            ->where('datetime', 'LiKE', '%' . $date . '%')
            ->orderBy('time', 'asc')
            ->get();
        $newArr = $newArr->groupBy(['qc']);
        $newArr = json_decode($newArr, true);

        $pkLatLnnew = array();
        $incr = 0;
        foreach ($newArr as $key => $value) {
            $latln2 = '';
            foreach ($value as $value2) {
                # code...
                // dd($value2);
                $latln2 .= '[' . $value2['lon'] . ',' . $value2['lat'] . '],';
                $pkLatLnnew[$key]['qc'] = $value2['qc'];
                $pkLatLnnew[$key]['latln'] = $latln2;
            }
        }

        // dd($newArr, $pkLatLnnew);



        // dd($blokLatLn, $messageResponse);
        $plot['plot'] = $plotTitik;
        $plot['marker'] = $plotMarker;
        $plot['blok'] = $messageResponse;
        $plot['img'] = $imgNew;
        $plot['plotarrow'] = $pkLatLnnew;
        // dd($plot);
        echo json_encode($plot);
    }


    public function updatesidakTPhnew(Request $request)
    {

        // mutu buah 
        $ids = $request->input('id');
        $blok_bh = $request->input('blok_bh');
        $brdtgl = $request->input('brdtgl');

        // dd($brdtgl, $ids);
        $brdjln = $request->input('brdjln');
        $brdbin = $request->input('brdbin');
        $qc = $request->input('qc');
        $jumkrng = $request->input('jumkrng');
        $buahtgl = $request->input('buahtgl');
        $restan = $request->input('restan');


        DB::connection('mysql2')->table('sidak_tph')->where('id', $ids)->update([
            'blok' => $blok_bh,
            'bt_tph' => $brdtgl,
            'bt_jalan' => $brdjln,
            'bt_bin' => $brdbin,
            'qc' => $qc,
            'jum_karung' => $jumkrng,
            'buah_tinggal' => $buahtgl,
            'restan_unreported' => $restan,
        ]);
    }
    public function deletedetailtph(Request $request)
    {
        $ancaks = $request->input('delete_id');

        if (is_array($ancaks)) {
            // Delete multiple rows
            DB::connection('mysql2')->table('sidak_tph')->whereIn('id', $ancaks)->delete();
        } else {
            // Delete a single row
            DB::connection('mysql2')->table('sidak_tph')->where('id', $ancaks)->delete();
        }

        return response()->json(['status' => 'success']);
    }
}
