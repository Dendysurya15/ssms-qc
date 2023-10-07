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

class pdfgenerateController extends Controller
{

    public function cetakPDFFI($id, $est, $tgl)
    {

        if ($est == 'Pla') {
            $est = 'Plasma1';
        }
        $date = Carbon::parse($tgl)->format('F Y');
        $queryMTFI = DB::connection('mysql2')->table('mutu_transport')
            ->select("mutu_transport.*")
            ->where('estate', $est)
            ->where('datetime', 'like', '%' . $tgl . '%')
            ->orderBy('estate', 'asc')
            ->orderBy('afdeling', 'asc')
            ->orderBy('blok', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();
        $dataMTFI1 = $queryMTFI->groupBy(function ($item) {
            return $item->estate . ' ' . $item->afdeling . ' ' . $item->blok;
        });
        $dataMTFI1 = json_decode($dataMTFI1, true);

        // dd($dataMTFI1);

        $queryMBFI = DB::connection('mysql2')->table('mutu_buah')
            ->select("mutu_buah.*")
            ->where('estate', $est)
            ->where('datetime', 'like', '%' . $tgl . '%')
            ->orderBy('estate', 'asc')
            ->orderBy('afdeling', 'asc')
            ->orderBy('blok', 'asc')
            ->orderBy('datetime', 'asc')
            ->get();
        $queryMBFI = $queryMBFI->groupBy(function ($item) {
            return $item->estate . ' ' . $item->afdeling . ' ' . $item->blok;
        });
        $queryMBFI = json_decode($queryMBFI, true);


        $queryNew = DB::connection('mysql2')->table('follow_up_ma')
            ->select("follow_up_ma.*")
            ->where('waktu_temuan', 'like', '%' . $tgl . '%')
            ->where('estate', '=', $est)
            ->where('estate', 'not like', '%Plasma%')
            ->orderBy('estate', 'asc')
            ->orderBy('afdeling', 'asc')
            ->orderBy('blok', 'asc')
            ->orderBy('waktu_temuan', 'asc')
            ->get();

        $queryNew = $queryNew->groupBy(function ($item) {
            return $item->estate . ' ' . $item->afdeling . ' ' . $item->blok;
        });
        $queryNew = json_decode($queryNew, true);
        // dd( $queryNew);

        $dates = [];

        foreach ($dataMTFI1 as $subarray) {
            foreach ($subarray as $item) {
                if (isset($item['datetime'])) {
                    $date = date('Y-m-d', strtotime($item['datetime']));
                    $dates[$date] = true;
                }
            }
        }

        $dateListTrans = array_keys($dates);

        $datesbh = [];

        foreach ($queryMBFI as $subarray) {
            foreach ($subarray as $item) {
                if (isset($item['datetime'])) {
                    $date = date('Y-m-d', strtotime($item['datetime']));
                    $datesbh[$date] = true;
                }
            }
        }

        $dateListBuah = array_keys($datesbh);

        $datesAncak = [];

        foreach ($queryNew as $subarray) {
            foreach ($subarray as $item) {
                if (isset($item['waktu_temuan'])) {
                    $date = date('Y-m-d', strtotime($item['waktu_temuan']));
                    $datesAncak[$date] = true;
                }
            }
        }

        $dateListAncak = array_keys($datesAncak);

        // dd($dateListTrans);

        $all_mutu = [];

        foreach ($dataMTFI1 as $key => $items) {
            if (!array_key_exists($key, $all_mutu)) {
                $all_mutu[$key] = [
                    'mutu_transport' => [],
                    'mutu_ancak' => [],
                    'mutu_buah' => [],
                ];
            }

            $visit_count = 1; // Initialize visit count to 1

            foreach ($items as &$item) {
                $date = substr($item['datetime'], 0, 10);

                // Get visit count from datelisttrans array
                $visit = array_search($date, $dateListTrans);
                if ($visit !== false) {
                    $visit_count = $visit + 1; // Update visit count
                }

                $item['visit'] = $visit_count;

                // Check if foto_temuan or foto_fu is not empty and process them
                if (!empty($item['foto_temuan']) || !empty($item['foto_fu'])) {

                    // Remove brackets and explode the strings into arrays
                    $foto_temuan = explode(',', str_replace(['[', ']'], '', $item['foto_temuan']));
                    $komentar = explode(',', str_replace(['[', ']'], '', $item['komentar']));

                    // Loop through each foto_temuan and komentar
                    for ($i = 0; $i < count($foto_temuan); $i++) {
                        // Copy the item
                        $new_item = $item;

                        // Replace foto_temuan and komentar with their respective values
                        $new_item['foto_temuan'] = trim($foto_temuan[$i]); // trim is used to remove any unwanted spaces
                        $new_item['komentar'] = trim($komentar[$i]); // trim is used to remove any unwanted spaces

                        // Add the new item to the all_mutu array
                        $all_mutu[$key]['mutu_transport'][] = $new_item;
                    }
                }
            }
        }


        // dd($all_mutu,$dateListTrans);


        // dd($all_mutu);
        foreach ($queryNew as $key => $items) {
            if (!array_key_exists($key, $all_mutu)) {
                $all_mutu[$key] = [
                    'mutu_transport' => [],
                    'mutu_ancak' => [],
                    'mutu_buah' => [],
                ];
            }
            $visit_count = 1; // Initialize visit count to 1

            foreach ($items as $item) {
                $date = substr($item['waktu_temuan'], 0, 10);
                $visit = array_search($date, $dateListAncak);
                if ($visit !== false) {
                    $visit_count = $visit + 1; // Update visit count
                }

                $item['visit'] = $visit_count;
                if (!empty($item['foto_temuan1']) || !empty($item['foto_fu1']) || !empty($item['komentar'])) {
                    $all_mutu[$key]['mutu_ancak'][] = $item;
                }
            }
        }

        // dd($all_mutu);
        foreach ($queryMBFI as $key => $items) {
            if (!array_key_exists($key, $all_mutu)) {
                $all_mutu[$key] = [
                    'mutu_transport' => [],
                    'mutu_ancak' => [],
                    'mutu_buah' => [],
                ];
            }

            $visit_count = 1; // Initialize visit count to 1

            foreach ($items as $item) {
                $date = substr($item['datetime'], 0, 10);
                $visit = array_search($date, $dateListBuah);
                if ($visit !== false) {
                    $visit_count = $visit + 1; // Update visit count
                }

                $item['visit'] = $visit_count;

                // Check if foto_temuan or foto_fu is not empty and process them
                if (!empty($item['foto_temuan']) || !empty($item['foto_fu'])) {
                    // Remove brackets and explode the strings into arrays
                    $foto_temuan = explode(';', str_replace(' ', '', $item['foto_temuan']));
                    $komentar = explode(';', $item['komentar']);

                    // Loop through each foto_temuan and komentar
                    for ($i = 0; $i < count($foto_temuan); $i++) {
                        // Copy the item
                        $new_item = $item;

                        // Replace foto_temuan and komentar with their respective values
                        $new_item['foto_temuan'] = trim($foto_temuan[$i]);
                        $new_item['komentar'] = trim($komentar[$i]);

                        // Add the new item to the all_mutu array
                        $all_mutu[$key]['mutu_buah'][] = $new_item;
                    }
                }
            }
        }


        $all_mutu = array_filter($all_mutu, function ($item) {
            return !empty($item['mutu_transport']) || !empty($item['mutu_ancak']) || !empty($item['mutu_buah']);
        });



        // function getGroupLetter($key)
        // {
        //     return substr($key, 4, 2);
        // }
        // uksort($all_mutu, function ($a, $b) {
        //     $groupLetterA = getGroupLetter($a);
        //     $groupLetterB = getGroupLetter($b);

        //     if ($groupLetterA === $groupLetterB) {
        //         return strcmp($a, $b); // If the group letters are the same, compare the full keys
        //     }

        //     return strcmp($groupLetterA, $groupLetterB); // Compare the group letters
        // });

        // dd($all_mutu);

        ////



        // print_r($all_mutu);


        $pdf = pdf::loadview('cetakFI', [
            'id' => $id,
            'date' => $tgl,
            'est' => $est,
            // 'dataResult' => $resultData,
            'newResult' => $all_mutu,
        ]);

        $customPaper = array(360, 360, 360, 360);
        $pdf->set_paper('A2', 'potraits');

        $filename = 'FI - Visit' . $id . ' - ' . $est . '.pdf';
        return $pdf->stream($filename);
    }


    public function updateBA(Request $request)
    {



        // mutu ancak 
        $est = $request->input('est');
        $afd = $request->input('afd');
        $date = $request->input('date');

        $estate = $request->input('estate');
        $afdeling = $request->input('afdeling');
        $id = $request->input('id');
        $blok = $request->input('blokCak');
        $status_panen = $request->input('StatusPnen');
        $sph = $request->input('sph');
        $br1 = $request->input('br1');
        $br2 = $request->input('br2');
        $sample = $request->input('sampCak');
        $pk_kuning = $request->input('pkKuning');
        $piringan_semak = $request->input('prSmk');
        $underpruning = $request->input('undrPR');
        $overpruning = $request->input('overPR');
        $janjang = $request->input('jjgCak');
        $brtp = $request->input('brtp');
        $brtk = $request->input('brtk');
        $brtgl = $request->input('brtgl');
        $bhts = $request->input('bhts');
        $bhtm1 = $request->input('bhtm1');
        $bhtm2 = $request->input('bhtm2');
        $bhtm3 = $request->input('bhtm3');
        $ps = $request->input('ps');
        $sp = $request->input('sp');
        $pk_panen = $request->input('pk_panenCAk');
        // dd($id, $estate, $afdeling,$blok,$status_panen);


        // $kmnBH = $request->input('kmnBH');
        // mutu transport
        // dd($ids,$jjgBH);

        $id_trans = $request->input('id_trans');
        $afd_trans = $request->input('afd_trans');
        $blok_trans = $request->input('blok_trans');
        $trans_panen = $request->input('Status_trPanen');
        $tphbrTrans = $request->input('tphbrTrans');
        $bt_trans = $request->input('bt_trans');
        $komentar_trans = $request->input('komentar_trans');
        $petugasTrans = $request->input('petugasTrans');
        $rstTrans = $request->input('rstTrans');
        $estTrans = $request->input('estTrans');

        // dd($id_trans, $afd_trans, $blok_trans, $bt_trans, $komentar_trans);

        DB::connection('mysql2')->table('mutu_ancak_new')->where('id', $id)->update([
            'blok' => $blok,
            'status_panen' => $status_panen,
            'sph' => $sph,
            'br1' => $br1,
            'br2' => $br2,
            'sample' => $sample,
            'pokok_kuning' => $pk_kuning,
            'piringan_semak' => $piringan_semak,
            'underpruning' => $underpruning,
            'overpruning' => $overpruning,
            'jjg' => $janjang,
            'brtp' => $brtp,
            'brtk' => $brtk,
            'brtgl' => $brtgl,
            'bhts' => $bhts,
            'bhtm1' => $bhtm1,
            'bhtm2' => $bhtm2,
            'bhtm3' => $bhtm3,
            'ps' => $ps,
            'sp' => $sp,
            'pokok_panen' => $pk_panen,
        ]);


        $ids = $request->input('editId_buah');
        $blok_bh = $request->input('blok_bh');
        $status_bhpanen = $request->input('StatusBhpnen');
        $bmt = $request->input('bmt');
        $bmk = $request->input('bmk');
        $pemanen_bh = $request->input('pemanen_bh');

        $estBH = $request->input('estBH');
        $afdBH = $request->input('afdBH');
        $tphBH = $request->input('tphBH');
        $petugasBHs = $request->input('petugasBH');
        $emptyBHS = $request->input('emptyBH');
        $jjgBH = $request->input('jjgBH');
        $overBH = $request->input('overBH');
        $abrBH = $request->input('abrBH');
        $vcutBHs = $request->input('vcutBH');
        $alsBR = $request->input('alsBR');

        //  dd($ids,$blok_bh,$status_bhpanen,$bmt);

        DB::connection('mysql2')->table('mutu_buah')->where('id', $ids)->update([
            'blok' => $blok_bh,
            'status_panen' => $status_bhpanen,
            'bmt' => $bmt,
            'bmk' => $bmk,
            'ancak_pemanen' => $pemanen_bh,
            'estate' => $estBH,
            'afdeling' => $afdBH,
            'tph_baris' => $tphBH,
            'petugas' => $petugasBHs,
            'empty_bunch' => $emptyBHS,
            'jumlah_jjg' => $jjgBH,
            'overripe' => $overBH,
            'abnormal' => $abrBH,
            'vcut' => $vcutBHs,
            'alas_br' => $alsBR,
            // 'komentar' => $kmnBH,
        ]);
        DB::connection('mysql2')->table('mutu_transport')->where('id', $id_trans)->update([
            'afdeling' => $afd_trans,
            'blok' => $blok_trans,
            'status_panen' => $trans_panen,
            'bt' => $bt_trans,
            'komentar' => $komentar_trans,
            'petugas' => $petugasTrans,
            'rst' => $rstTrans,
            'estate' => $estTrans,
            'tph_baris' => $tphbrTrans,
        ]);
    }
    public function deleteBA(Request $request)
    {

        $idBuah = $request->input('delete_idBuah');
        // $ancak = $request->input('id');
        $ancaks = $request->input('delete_id');
        $id_trans = $request->input('id_trans');

        // dd($id_trans);


        $ancakFA = DB::connection('mysql2')->table('mutu_ancak_new')
            // ->where('id', $request->input('id'))
            ->where('id', $request->input('delete_id'))
            ->get();
        $ancakFA = $ancakFA->groupBy(['estate', 'afdeling']);
        $ancakFA = json_decode($ancakFA, true);

        $followup = DB::connection('mysql2')->table('follow_up_ma')
            // ->where('id', $request->input('id'))
            ->get();
        $followup = $followup->groupBy(['estate', 'afdeling']);
        $followup = json_decode($followup, true);
        // dd($ancakFA ,$followup['GDE']['OD']);
        $getID = []; // initialize it as array for follow_up_ma
        $getAncakID = []; // initialize it as array for mutu_ancak_new
        foreach ($ancakFA as $key => $value) {
            if (!isset($followup[$key])) {
                continue;
            }
            foreach ($value as $key1 => $value1) {
                if (!isset($followup[$key][$key1])) {
                    continue;
                }
                foreach ($value1 as $key2 => $value2) {
                    // Convert the datetime strings to date format
                    $dateAncak = (new DateTime($value2['datetime']))->format('Y-m-d');

                    foreach ($followup[$key][$key1] as $val3) {
                        // Convert the datetime strings to date format
                        $dateFollowUp = (new DateTime($val3['waktu_temuan']))->format('Y-m-d');

                        // Compare the dates and other values
                        if (
                            $value2['br1'] == $val3['br1']
                            && $value2['br2'] == $val3['br2']
                            && $value2['estate'] == $val3['estate']
                            && $value2['afdeling'] == $val3['afdeling']
                            && $value2['jalur_masuk'] == $val3['jalur_masuk']
                            && $dateFollowUp == $dateAncak
                        ) {
                            $getID[] = $val3['id']; // store the id in the array for follow_up_ma
                            $getAncakID[] = $value2['id']; // store the id in the array for mutu_ancak_new
                        }
                    }
                }
            }
        }


        // dd($getID);


        // Now you have IDs for both tables, so you can delete rows from both tables

        DB::connection('mysql2')->table('follow_up_ma')->whereIn('id', $getID)->delete();



        DB::connection('mysql2')->table('mutu_ancak_new')->where('id', $request->input('delete_id'))->delete();


        //mutu buah
        DB::connection('mysql2')->table('mutu_buah')
            ->where('id', $idBuah)
            ->delete();
        DB::connection('mysql2')->table('mutu_transport')
            ->where('id', $id_trans)
            ->delete();

        return response()->json(['status' => 'success']);
    }

    public function deleteTrans($id)
    {
        DB::connection('mysql2')->table('mutu_transport')
            ->where('id', $id)
            ->delete();
        return response()->json(['status' => 'success']);
    }


    public function pdfBA(Request $request)
    {
        $est = $request->input('estBA');
        $afd = $request->input('afdBA');
        $date = $request->input('tglPDF');
        $reg = $request->input('regPDF');

        $mutuAncak = DB::connection('mysql2')
            ->table('mutu_ancak_new')
            ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_ancak_new.estate', $est)
            ->where('mutu_ancak_new.afdeling', $afd)
            ->orderBy('estate', 'desc')
            ->orderBy('afdeling', 'desc')
            ->orderBy('blok', 'desc')
            ->orderBy('datetime', 'desc')
            ->get();

        $mutuAncak = $mutuAncak->groupBy('blok')->toArray();

        // dd($mutuAncak);

        if ($reg == 1) {
            $mutuAncak = array_combine(
                array_map(function ($key) {
                    $parts = explode('-SSMS', $key);
                    return $parts[0];
                }, array_keys($mutuAncak)),
                array_map(function ($value) {
                    return json_decode(json_encode($value), true);
                }, array_values($mutuAncak))
            );
        } else {
        }


        $mutuAncak = json_decode(json_encode($mutuAncak), true);
        // dd($mutuAncak);

        // dd($mutuAncak);
        $mutuBuahQuery = DB::connection('mysql2')->table('mutu_buah')
            ->select("mutu_buah.*", DB::raw('DATE_FORMAT(mutu_buah.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_buah.datetime, "%Y") as tahun'))
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_buah.estate', $est)
            ->where('mutu_buah.afdeling', $afd)

            ->get();
        $mutuBuahQuery = $mutuBuahQuery->groupBy(['blok']);
        $mutuBuahQuery = json_decode($mutuBuahQuery, true);


        // $mutuTransport = json_decode($mutuTransport, true);
        $mutuTransport = DB::connection('mysql2')
            ->table('mutu_transport')
            ->select("mutu_transport.*", DB::raw('DATE_FORMAT(mutu_transport.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_transport.datetime, "%Y") as tahun'))
            ->where('datetime', 'like', '%' . $date . '%')
            ->where('mutu_transport.estate', $est)
            ->where('mutu_transport.afdeling', $afd)
            ->orderBy('estate', 'desc')
            ->orderBy('afdeling', 'desc')
            ->orderBy('blok', 'desc')
            ->orderBy('datetime', 'desc')
            ->get();

        $mutuTransport = $mutuTransport->groupBy('blok')->toArray();



        if ($reg == 1) {
            $mutuTransport = array_combine(
                array_map(function ($key) {
                    $parts = explode('-SSMS', $key);
                    return $parts[0];
                }, array_keys($mutuTransport)),
                array_map(function ($value) {
                    return json_decode(json_encode($value), true);
                }, array_values($mutuTransport))
            );
        } else {

            foreach ($mutuTransport as $key => $value) {
                $mutuTransport[$key] = array_map(function ($item) {
                    return json_decode(json_encode($item), true);
                }, $value);
            }

            $mutuTransport = json_decode(json_encode($mutuTransport), true);
        }

        $mutuTransport = json_decode(json_encode($mutuTransport), true);

        $filteredAncakPemanen = [];

        foreach ($mutuAncak as $key => $blockData) {
            $hah = []; // Initialize an empty array for each block

            foreach ($blockData as $key1 => $data) {
                // Check if any of the specified fields have a non-zero value
                if (
                    $data["brtp"] != 0 ||
                    $data["brtk"] != 0 ||
                    $data["brtgl"] != 0 ||
                    $data["bhts"] != 0 ||
                    $data["bhtm1"] != 0 ||
                    $data["bhtm2"] != 0 ||
                    $data["bhtm3"] != 0
                ) {
                    // Add "ancak_pemanen" value to the $hah array
                    $hah[] = $data["ancak_pemanen"];
                }
            }

            // If $hah is empty, all specified fields are 0; get the first "ancak_pemanen"
            if (empty($hah)) {
                $hah[] = $blockData[0]["ancak_pemanen"];
            }

            // Concatenate the "ancak_pemanen" values into a single index
            $filteredAncakPemanen[$key]['new_ancak'] = implode('-', $hah);
        }

        // Debugging: Use dd to dump the original array and the filtered result
        // dd($mutuAncak, $filteredAncakPemanen);



        $ancak = array();
        $sum = 0; // Initialize sum variable
        $count = 0; // Initialize count variable
        foreach ($mutuAncak as $key => $value) {
            $jumPokok = 0;
            $sph = 0;
            $jml_jjg_panen = 0;

            $jml_brtk = 0;
            $jml_brtgl = 0;
            $jml_bhts = 0;
            $jml_bhtm1 = 0;
            $jml_bhtm2 = 0;
            $jml_bhtm3 = 0;
            $jml_ps = 0;
            $listBlok = array();
            $pk_kuning = 0;
            $pr_smak = 0;
            $unprun  = 0;
            $sp = 0;
            $over_prun = 0;
            $pokok_panen = 0;
            $firstEntry = $value[0];
            $jml_brtp = 0;
            foreach ($value as $key1 => $value2) {


                $jumPokok += $value2['sample'];
                if (!in_array($value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'], $listBlok)) {
                    if ($value2['sph'] != 0) {
                        $listBlok[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                        $sph += $value2['sph'];
                    }
                }
                $jml_blok = count($listBlok);

                $jml_jjg_panen += $value2['jjg'];
                $jml_brtp += $value2['brtp'];
                $jml_brtk += $value2['brtk'];
                $jml_brtgl += $value2['brtgl'];
                $jml_bhts += $value2['bhts'];
                $jml_bhtm1 += $value2['bhtm1'];
                $jml_bhtm2 += $value2['bhtm2'];
                $jml_bhtm3 += $value2['bhtm3'];
                $jml_ps += $value2['ps'];


                // untuk bagian food stacking
                $pk_kuning += $value2['pokok_kuning'];
                $pr_smak += $value2['piringan_semak'];
                $unprun += $value2['underpruning'];
                $over_prun += $value2['overpruning'];
                $sp += $value2['sp'];
                $pokok_panen += $value2['pokok_panen'];
            }
            $jml_sph = $jml_blok == 0 ? $sph : ($sph / $jml_blok);
            $tot_brd = ($jml_brtp + $jml_brtk + $jml_brtgl);
            $tot_jjg = ($jml_bhts + $jml_bhtm1 + $jml_bhtm2 + $jml_bhtm3);
            // $luas_ha = round(($jumPokok / $jml_sph), 2);
            $luas_ha = ($jml_sph != 0) ? round(($jumPokok / $jml_sph), 2) : 0;

            if ($firstEntry['luas_blok'] != 0) {
                $first = $firstEntry['luas_blok'];
            } else {
                $first = '-';
            }


            $ancak[$key]['luas_blok'] = $first;
            $ancak[$key]['persenSamp'] = ($first != '-') ? round(($luas_ha / $first) * 100, 2) : '-';

            if ($reg === '2' || $reg === 2) {
                $status_panen = explode(",", $value2['status_panen']);
                $ancak[$key]['status_panen'] = $status_panen[0];
            } else {
                $ancak[$key]['status_panen'] = $value2['status_panen'];
            }
            $ancak[$key]['sph'] = $sph;
            $ancak[$key]['pokok_sample'] = $jumPokok;
            $ancak[$key]['pokok_panen'] = $pokok_panen;
            $ancak[$key]['luas_ha'] = $luas_ha;
            $ancak[$key]['jml_jjg_panen'] = $jml_jjg_panen;
            if ($reg === '2' || $reg === 2) {
                $ancak[$key]['akp_real'] = round((($jml_jjg_panen + $tot_jjg) / $jumPokok * 100), 2);
            } else {
                $ancak[$key]['akp_real'] = count_percent($jml_jjg_panen, $jumPokok);
            }

            $ancak[$key]['p_ma'] = $jml_brtp;
            $ancak[$key]['k_ma'] = $jml_brtk;
            $ancak[$key]['gl_ma'] = $jml_brtgl;
            $ancak[$key]['total_brd_ma'] = $tot_brd;
            if ($jml_jjg_panen != 0) {
                $ancak[$key]['btr_jjg_ma'] = round(($tot_brd / $jml_jjg_panen), 2);
            } else {
                $ancak[$key]['btr_jjg_ma'] = 0;
            }

            $ancak[$key]['bhts_ma'] = $jml_bhts;
            $ancak[$key]['bhtm1_ma'] = $jml_bhtm1;
            $ancak[$key]['bhtm2_ma'] = $jml_bhtm2;
            $ancak[$key]['bhtm3_ma'] = $jml_bhtm3;
            $ancak[$key]['tot_jjg_ma'] = $tot_jjg;
            if ($tot_jjg != 0) {
                $ancak[$key]['jjg_tgl_ma'] = round(($tot_jjg / ($jml_jjg_panen + $tot_jjg)) * 100, 2);
            } else {
                $ancak[$key]['jjg_tgl_ma'] = 0;
            }

            $ancak[$key]['ps_ma'] = $jml_ps;

            $ancak[$key]['PerPSMA'] = count_percent($jml_ps, $jumPokok);
            $ancak[$key]['front'] = $sp;
            $ancak[$key]['pk_kuning'] = $pk_kuning;
            $ancak[$key]['und'] = $unprun;
            $ancak[$key]['overprn'] = $over_prun;
            foreach ($filteredAncakPemanen as $keyx => $valuex) if ($keyx == $key) {
                foreach ($valuex as $valuex1) {
                    $newAncak = $valuex['new_ancak'];
                } # code...
            }


            $ancak[$key]['ancak_panen'] = $value2['ancak_pemanen'];
            $ancak[$key]['ancak_panenReg2'] = $newAncak;
            $ancak[$key]['prsmk'] = $pr_smak;
            $ancak[$key]['frontstack'] = ($jumPokok != 0) ? round(($sp / $jumPokok) * 100, 2) : 0;
            $ancak[$key]['under'] = ($jumPokok != 0) ? round(($unprun / $jumPokok) * 100, 2) : 0;
            $ancak[$key]['overprun'] = ($jumPokok != 0) ? round(($over_prun / $jumPokok) * 100, 2) : 0;
            $ancak[$key]['piringansmk'] = ($jumPokok != 0) ? round(($pr_smak / $jumPokok) * 100, 2) : 0;


            if ($first != '-') {
                $sum += $first; // Add luas_blok to the sum
                $count++;
            }
        }

        // dd($ancak,$mutuAncak);
        $average = $count != 0 ? $sum / $count : 0;


        $avg = [];
        foreach ($ancak as $key) {
            $avg['average'] = $average;
        }
        // dd($ancak, $avg);
        $sph_values = [];
        foreach ($ancak as $key => $data) {
            $sph_values[] = $data['sph'];
        }

        // Calculate the sum of sph values
        $sum = array_sum($sph_values);
        if (count($sph_values) > 0) {
            $average = round($sum / count($sph_values), 0);
        } else {
            $average = 0; // or any default value you prefer
        }


        $transport = array();

        foreach ($mutuTransport as $key => $value) {
            $sum_bt = 0;
            $sum_Restan = 0;
            $tph_sample = 0;
            $listBlokPerAfd = array();
            foreach ($value as $key2 => $value2) {
                // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                // }
                $sum_Restan += $value2['rst'];
                $tph_sample = count($listBlokPerAfd);
                $sum_bt += $value2['bt'];
            }
            $transport[$key]['reg'] = 'reg1/reg3';
            $transport[$key]['bt_total'] = $sum_bt;
            $transport[$key]['restan_total'] = $sum_Restan;
            $transport[$key]['tph_sample'] = $tph_sample;
            $transport[$key]['skor'] = ($tph_sample != 0) ? round($sum_bt / $tph_sample, 2) : 0;
            $transport[$key]['skor_restan'] = ($tph_sample != 0) ? round($sum_Restan / $tph_sample, 2) : 0;
        }


        if ($reg === '2' || $reg === 2) {

            // $ancak_status = $ancak[''];
            foreach ($mutuTransport as $key => $value) {
                $sum_bt = 0;
                $sum_Restan = 0;
                $tph_sample = 0;
                $listBlokPerAfd = array();
                foreach ($value as $key2 => $value2) {
                    // if (!in_array($value3['estate'] . ' ' . $value3['afdeling'] . ' ' . $value3['blok'], $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['estate'] . ' ' . $value2['afdeling'] . ' ' . $value2['blok'];
                    // }
                    $sum_Restan += $value2['rst'];
                    $tph_sample = count($listBlokPerAfd);
                    $sum_bt += $value2['bt'];
                    // dd($value2);
                }

                $panenKey = 0;

                if (isset($ancak[$key]['status_panen'])) {
                    $transport[$key]['status_panen'] = $ancak[$key]['status_panen'];
                    $panenKey = $ancak[$key]['status_panen'];
                    $transport[$key]['status_panentrans'] = $value2['status_panen'];
                    $transport[$key]['status_panenAncak'] = $ancak[$key]['status_panen'];
                }
                $LuasKey = 0;
                if (isset($ancak[$key]['luas_blok'])) {
                    $transport[$key]['luas_blok'] = $ancak[$key]['luas_blok'];
                    $LuasKey = $ancak[$key]['luas_blok'];
                }

                if (isset($panenKey) && $panenKey <= 3 && isset($ancak[$key]['luas_blok'])) {
                    $transport[$key]['tph_sample'] = round($LuasKey * 1.3, 2);
                } else {
                    $transport[$key]['tph_sample'] = $tph_sample;
                }


                $transport[$key]['reg'] = $reg;
                $transport[$key]['status_panen'] = $value2['status_panen'];
                $transport[$key]['tph_sampleTrans'] = $tph_sample;
                $transport[$key]['estate'] = $value2['estate'];
                $transport[$key]['afdeling'] = $value2['afdeling'];
                $transport[$key]['bt_total'] = $sum_bt;
                $transport[$key]['restan_total'] = $sum_Restan;
                $transport[$key]['skor'] = ($tph_sample != 0) ? round($sum_bt / $tph_sample, 2) : 0;
                $transport[$key]['skor_restan'] = ($tph_sample != 0) ? round($sum_Restan / $tph_sample, 2) : 0;
            }
            // Add this code after your existing foreach loop


            $transReg2 = array();
            $tph_sample = 0;
            foreach ($transport as $key => $value) {
                # code...
                $tph_sample += $value['tph_sample'];
                // dd($value);
            }
            if (isset($value['estate'])) {
                if (!isset($transReg2[$value['estate']])) {
                    $transReg2[$value['estate']] = [];
                }

                if (!isset($transReg2[$value['estate']][$value['afdeling']])) {
                    $transReg2[$value['estate']][$value['afdeling']] = [];
                }

                $transReg2[$value['estate']][$value['afdeling']]['tph_sample'] = $tph_sample;
            }


            foreach ($ancak as $key => $value) {
                if (!array_key_exists($key, $transport)) {
                    $transport[$key]['status_panen'] = $value['status_panen'];
                    $transport[$key]['luas_blok'] = $value['luas_blok'];

                    if ($value['status_panen'] <= 3) {
                        $transport[$key]['tph_sample'] = round($value['luas_blok'] * 1.3, 2);
                    } else {
                        $transport[$key]['tph_sample'] = $value['status_panen'];
                    }
                    $transport[$key]['bt_total'] = 0;
                    $transport[$key]['restan_total'] = 0;
                    $transport[$key]['skor'] = 0;
                    $transport[$key]['skor_restan'] = 0;
                }
            }
        }
        $newVariable = array();

        foreach ($transport as $key => $value) {
            if (isset($value['status_panentrans']) && isset($value['status_panenAncak'])) {
                $newVariable[$key] = $value;
                break;  // stop the loop after the first match
            }
        }

        // dd($transport, $ancak);

        // dd($newVariable,$transport);
        $mutuBuah = array();
        foreach ($mutuBuahQuery as $key => $value) {
            $listBlokPerAfd = array();
            $janjang = 0;
            $Jjg_Mth = 0;
            $Jjg_Mth2 = 0;
            $Jjg_Over = 0;
            $Jjg_Empty = 0;
            $Jjg_Abr = 0;
            $Jjg_Vcut = 0;
            $Jjg_Als = 0;
            $dtBlok = count($value);
            $count_alas_br_1 = 0;
            $count_alas_br_0 = 0;
            $vcutStack = 0;
            foreach ($value as $key2 => $value2) {

                if (!in_array($value2['blok'], $listBlokPerAfd)) {
                    $listBlokPerAfd[] = $value2['blok'];
                }
                $janjang += $value2['jumlah_jjg'];
                $Jjg_Mth += $value2['bmt'];
                $Jjg_Mth2 += $value2['bmk'];
                $Jjg_Over += $value2['overripe'];
                $Jjg_Empty += $value2['empty_bunch'];
                $Jjg_Abr += $value2['abnormal'];
                $Jjg_Vcut += $value2['vcut'];
                $Jjg_Als += $value2['alas_br'];

                if ($value2['alas_br'] == 1) {
                    $count_alas_br_1++;
                } elseif ($value2['alas_br'] == 0) {
                    $count_alas_br_0++;
                }
            }
            //untuk food stacking
            $vcutStack = $janjang - $Jjg_Vcut;

            $jml_mth = ($Jjg_Mth + $Jjg_Mth2);
            $jml_mtg = $janjang - ($jml_mth + $Jjg_Over + $Jjg_Empty + $Jjg_Abr);

            $mutuBuah[$key]['blok_mb'] = $dtBlok;
            $mutuBuah[$key]['status_panen'] = $value2['status_panen'];
            $mutuBuah[$key]['alas_mb'] = $Jjg_Als;
            $mutuBuah[$key]['bmt'] = $Jjg_Mth;
            $mutuBuah[$key]['bmk'] = $Jjg_Mth2;
            $mutuBuah[$key]['jml_janjang'] = $janjang;
            $mutuBuah[$key]['jml_mentah'] = $jml_mth;
            $mutuBuah[$key]['jml_masak'] = $jml_mtg;
            $mutuBuah[$key]['jml_over'] = $Jjg_Over;
            $mutuBuah[$key]['jml_empty'] = $Jjg_Empty;
            $mutuBuah[$key]['jml_abnormal'] = $Jjg_Abr;
            $mutuBuah[$key]['jml_vcut'] = $Jjg_Vcut;
            $mutuBuah[$key]['jml_krg_brd'] = $dtBlok == 0 ? $Jjg_Als : round($Jjg_Als / $dtBlok, 2);
            $denom = ($janjang - $Jjg_Abr) != 0 ? ($janjang - $Jjg_Abr) : 1;

            $mutuBuah[$key]['PersenBuahMentah'] = $denom != 0 ? round(($jml_mth / $denom) * 100, 2) : 0;
            $mutuBuah[$key]['PersenBuahMasak'] = $denom != 0 ? round(($jml_mtg / $denom) * 100, 2) : 0;
            $mutuBuah[$key]['PersenBuahOver'] = $denom != 0 ? round(($Jjg_Over / $denom) * 100, 2) : 0;
            $mutuBuah[$key]['PersenPerJanjang'] = $denom != 0 ? round(($Jjg_Empty / $denom) * 100, 2) : 0;
            $mutuBuah[$key]['PersenVcut'] = count_percent($Jjg_Vcut, $janjang);
            $mutuBuah[$key]['PersenAbr'] = count_percent($Jjg_Abr, $janjang);
            $mutuBuah[$key]['PersenKrgBrd'] = count_percent($count_alas_br_1, $dtBlok);
            $mutuBuah[$key]['count_alas_br_1'] = $count_alas_br_1;
            $mutuBuah[$key]['count_alas_br_0'] = $count_alas_br_0;
            $mutuBuah[$key]['vst'] = $vcutStack;
            $mutuBuah[$key]['vcutStack'] = $janjang != 0 ? round(($vcutStack / $janjang) * 100, 2) : 0;
        }

        // dd($ancak, $mutuBuah);

        $BuahStack = array();

        // Merge the keys from both arrays and create a unique set of keys
        $keys = array_unique(array_merge(array_keys($mutuBuah), array_keys($ancak)));

        foreach ($keys as $key) {
            $currentBuahStack = array();
            $currentBuahStack['vst'] = isset($mutuBuah[$key]['vst']) ? $mutuBuah[$key]['vst'] : 0;
            $currentBuahStack['jml_janjang'] = isset($mutuBuah[$key]['jml_janjang']) ? $mutuBuah[$key]['jml_janjang'] : 0;
            $currentBuahStack['bmt'] = isset($mutuBuah[$key]['bmt']) ? $mutuBuah[$key]['bmt'] : 0;
            $currentBuahStack['bmk'] = isset($mutuBuah[$key]['bmk']) ? $mutuBuah[$key]['bmk'] : 0;
            $currentBuahStack['jml_vcut'] = isset($mutuBuah[$key]['jml_vcut']) ? $mutuBuah[$key]['jml_vcut'] : 0;
            //ancak 
            $currentBuahStack['front'] = isset($ancak[$key]['front']) ? $ancak[$key]['front'] : 0;
            $currentBuahStack['pk_kuning'] = isset($ancak[$key]['pk_kuning']) ? $ancak[$key]['pk_kuning'] : 0;
            $currentBuahStack['pokok_panen'] = isset($ancak[$key]['pokok_panen']) ? $ancak[$key]['pokok_panen'] : 0;
            $currentBuahStack['und'] = isset($ancak[$key]['und']) ? $ancak[$key]['und'] : 0;
            $currentBuahStack['overprn'] = isset($ancak[$key]['overprn']) ? $ancak[$key]['overprn'] : 0;
            $currentBuahStack['prsmk'] = isset($ancak[$key]['prsmk']) ? $ancak[$key]['prsmk'] : 0;
            $currentBuahStack['pokok_sample'] = isset($ancak[$key]['pokok_sample']) ? $ancak[$key]['pokok_sample'] : 0;

            // Append the current iteration values to the main $BuahStack array with the $key
            $BuahStack[$key] = $currentBuahStack;
        }

        // dd($BuahStack);
        $CalculateStack = array();
        $vcut = 0;
        $jjg = 0;
        $front = 0;
        $und  = 0;
        $overprn = 0;
        $prsmk = 0;
        $pkok_sam = 0;
        $pkok_kuning = 0;
        $bmt = 0;
        $bmk = 0;
        $vcutt = 0;
        $pkok_panen = 0;
        foreach ($BuahStack as $key => $value) {
            $vcut += $value['vst'];
            $vcutt += $value['jml_vcut'];
            $bmt += $value['bmt'];
            $bmk += $value['bmk'];
            $jjg += $value['jml_janjang'];
            $front += $value['front'];
            $und  += $value['und'];
            $overprn += $value['overprn'];
            $prsmk += $value['prsmk'];
            $pkok_sam += $value['pokok_sample'];
            $pkok_panen += $value['pokok_panen'];
            $pkok_kuning += $value['pk_kuning'];
        }
        $vcutStacks  = $jjg - $vcutt;
        $CalculateStack['frontstack'] = $pkok_panen != 0 ? round(($front / $pkok_panen) * 100, 2) : 0;
        $CalculateStack['pokok_kuning'] = $pkok_sam != 0 ? round(($pkok_kuning / $pkok_sam) * 100, 2) : 0;
        $CalculateStack['piringansmk'] = $pkok_sam != 0 ? round(($prsmk / $pkok_sam) * 100, 2) : 0;
        $CalculateStack['under'] = $pkok_sam != 0 ? round(($und / $pkok_sam) * 100, 2) : 0;
        $CalculateStack['overprun'] = $pkok_sam != 0 ? round(($overprn / $pkok_sam) * 100, 2) : 0;
        $CalculateStack['mentah_tpBrd'] = $jjg != 0 ? round(($bmt / $jjg) * 100, 2) : 0;
        $CalculateStack['mentah_krngBRD'] = $jjg != 0 ? round(($bmk / $jjg) * 100, 2) : 0;
        $CalculateStack['vcutStack'] = $jjg != 0 ? round(($vcutStacks / $jjg) * 100, 2) : 0;


        $CalculateStack['vst'] = $vcut;
        $CalculateStack['vcutStacks'] = $vcutStacks;
        $CalculateStack['TidakVcut'] = $vcutt;
        $CalculateStack['jjg_buah'] = $jjg;
        $CalculateStack['bmk'] = $bmk;
        $CalculateStack['bmt'] = $bmt;
        $CalculateStack['pokok_sample'] = $jjg;
        // dd($transReg2,$transport);
        // dd($ancak);
        // Session::put('transReg2', $transReg2);
        // dd($transport);
        $arrView = array();
        $arrView['hitung'] =  $CalculateStack;

        $arrView['mutuAncak'] =  $ancak;
        $arrView['avg'] =  $avg;
        $arrView['sph_avg'] = $average;
        $arrView['mutuBuah'] =  $mutuBuah;
        $arrView['mutuTransport'] =  $transport;
        $arrView['est'] =  $est;
        $arrView['afd'] =  $afd;
        $arrView['reg'] =  $reg;
        $arrView['tanggal'] =  $date;
        $arrView['ancak_trans'] =  $newVariable;

        $pdf = PDF::loadView('pdfBA', ['data' => $arrView]);

        $customPaper = array(360, 360, 360, 360);
        $pdf->set_paper('A2', 'landscape');
        // $pdf->set_paper('A2', 'potrait');

        $filename = 'BA Inpeksi Quality control-' . $arrView['tanggal']  . $arrView['est'] . $arrView['afd'] . '.pdf';

        return $pdf->stream($filename);
        // return $pdf->download($filename);

        // return view('pdfBA', [$arrView ]);

    }
}