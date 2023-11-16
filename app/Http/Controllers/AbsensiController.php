<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;



class AbsensiController extends Controller
{
    //

    public function index(Request $request)
    {

        // use Carbon\Carbon;

        $lok = $request->session()->get('lok');

        // dd($lok);
        $userabsen = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('departemen', 'QC')
            ->where('lokasi_kerja', $lok)
            ->where('email', 'like', '%mandor%')
            ->get();

        // You can remove the unnecessary conversion to JSON
        // $userabsen = $userabsen->groupBy('nama_lengkap');
        $userabsen = json_decode($userabsen, true);



        $user_Data = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('departemen', 'QC')
            ->where('lokasi_kerja', $lok)
            ->where('email', 'like', '%mandor%')
            ->get();

        // You can remove the unnecessary conversion to JSON
        // $user_Data = $user_Data->groupBy('nama_lengkap');
        $user_Data = json_decode($user_Data, true);

        // dd($user_Data);

        $firstDayOfMonth = Carbon::now()->firstOfMonth();
        $JumlahBulan = $firstDayOfMonth->daysInMonth;
        $bulanNow = Carbon::now()->format('M');
        // dd($bulanNow);

        $header_month = $bulanNow . '-' . $JumlahBulan;

        // dd($header_month);

        return view('Absensi.index', ['header_month' => $header_month, 'dates' => $JumlahBulan, 'useroption' => $user_Data]);
    }

    public function data(Request $request)
    {
        $regional = $request->input('regional');
        $bulan = $request->input('dateMonth');

        $regs = DB::connection('mysql2')->table('reg')
            ->where('reg.id', $regional)
            ->pluck('nama');
        // dd($regs);


        // Split the "2023-09" string into year and month
        list($year, $month) = explode('-', $bulan);

        // Create a Carbon instance for the first day of the specified month
        $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $JumlahBulan = $firstDayOfMonth->daysInMonth;

        $carbonDate = Carbon::parse($bulan);

        // Get the full name of the month
        $monthName = $carbonDate->format('M');

        $header_month = $monthName . '-' . $JumlahBulan;

        // dd($monthName);


        $endDate = $firstDayOfMonth->copy()->endOfMonth();

        $datesnew = [];

        while ($firstDayOfMonth->lte($endDate)) {
            $datesnew[] = $firstDayOfMonth->toDateString();
            $firstDayOfMonth->addDay();
        }
        // dd($datesnew);

        // Now $dates contains all dates in October

        $result = [];
        for ($i = 1; $i <= $JumlahBulan; $i++) {
            $result[$i - 1] = $i;
        }

        // dd($result);
        $user_Data = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('departemen', 'QC')
            ->where('lokasi_kerja', $regs)
            ->where('email', 'like', '%mandor%')
            ->get();

        // You can remove the unnecessary conversion to JSON
        $user_Data = $user_Data->groupBy('user_id');
        $user_Data = json_decode($user_Data, true);


        // dd($datesnew);

        $user_default = [];

        foreach ($user_Data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $user_default[$key] = [
                    'id' => $value2['user_id'],
                    'nama' => $value2['nama_lengkap'],
                    'payroll' => '-',
                ];

                // Loop through $datesnew and add each date with a default value of "-"
                foreach ($datesnew as $date) {
                    // Check if the day of the week for the date is Sunday (day number 7)
                    if (date('N', strtotime($date)) == 7) {
                        $user_default[$key][$date] = "minggu";
                    } else {
                        $user_default[$key][$date] = "";
                    }
                }
                $user_default[$key]['total'] = 0;
            }
        }


        // dd($user_default);
        $user_absensi = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*', 'list_pekerjaan.nama as pekerjaan', DB::raw('DATE(waktu_absensi) as tanggal'),)
            ->where('waktu_absensi', 'like', '%' . $bulan . '%')
            ->join('list_pekerjaan', 'list_pekerjaan.id', '=', 'absensi_qc.id_pekerjaan')
            ->get();

        // You can remove the unnecessary conversion to JSON
        // $user_absensi = $user_absensi->groupBy('id_user', 'tanggal');
        $user_absensi = $user_absensi->groupBy(['id_user', 'tanggal']);
        $user_absensi = json_decode($user_absensi, true);


        // dd($user_absensi, $bulan);

        $tanggal_values = []; // Create an array to store unique "tanggal" values
        // dd($user_absensi, $tanggal_values);
        foreach ($user_absensi as $key => $value) {
            foreach ($value as $tanggal => $entries) {
                $dayOfWeek = date('N', strtotime($tanggal));
                if (!in_array($tanggal, $tanggal_values)) {
                    $tanggal_values[$tanggal] = ($dayOfWeek == 7) ? 'minggu' : 'MK';
                }
            }
        }

        // dd($tanggal_values, $user_default);
        $getkerja = DB::connection('mysql2')->table('list_pekerjaan')
            ->get();
        $getkerja = json_decode($getkerja, true);
        // dd($getkerja);
        // dd($tanggal_values);


        // dd($tanggal_values);

        // dd($user_absensi);

        $newabsensi = [];
        foreach ($user_absensi as $key => $value) {
            foreach ($value as $key2 => $value2) {
                // dd($value2);
                foreach ($value2 as $key3 => $value3) {
                    $date = $value3['waktu_absensi'];
                    $formattedTime = date('H:i:s', strtotime($date));


                    $specificValues = ['Sakit', 'Cuti', 'Izin'];

                    $pekerjaan = explode('$', $value3['id_pekerjaan']);


                    // dd($date);
                    // dd($pekerjaan);
                    $getkerjax = $pekerjaan[0];

                    $kerja = '-';
                    foreach ($getkerja as $keyx => $valuex) {
                        # code...
                        // dd($value);

                        if ($valuex['id'] == $getkerjax) {
                            # code...
                            $kerja = $valuex['nama'];
                        }
                    }

                    $ket = '-';

                    if (!in_array($kerja, $specificValues)) {
                        $ket = 'KJ' . '$' . $formattedTime;
                    } elseif ($kerja == 'Sakit' || $kerja == 'Ijin') {
                        $ket = 'I';
                    } elseif ($kerja == 'Cuti') {
                        $ket = 'CT';
                    }

                    // dd($value);
                }

                $newabsensi[$key][$key2] = $ket;


                // $user_absensi[$key][$formattedDate] = $ket;
            }
        }

        // dd($newabsensi);
        // dd($newabsensi);
        $result = [];

        foreach ($user_default as $key => $user) {
            $updatedUser = $user;
            foreach ($tanggal_values as $date => $value) {
                if (array_key_exists($date, $updatedUser)) {
                    $updatedUser[$date] = $value;
                }
            }
            $result[$key] = $updatedUser;
        }

        // The $result array contains the updated user arrays.



        // dd($result);



        $get_data = [];

        foreach ($result as $key => $value) {
            if (array_key_exists($key, $newabsensi)) {
                $get_data[$key] = array_replace($value, $newabsensi[$key]);
            } else {
                // Check if the key exists in "tanggal_values," and if so, use its value
                if (array_key_exists($key, $tanggal_values)) {
                    $value[$key] = $tanggal_values[$key];
                }
                $get_data[$key] = $value;
            }
        }

        // dd($tanggal_values);

        $user_ci = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*', 'list_pekerjaan.nama as pekerjaan', DB::raw('DATE(waktu_absensi) as tanggal'))
            ->join('list_pekerjaan', 'list_pekerjaan.id', '=', 'absensi_qc.id_pekerjaan')
            ->where('range_date', '!=', 0)
            ->get();


        // You can remove the unnecessary conversion to JSON
        // $user_ci = $user_ci->groupBy('id_user', 'tanggal');
        $user_ci = $user_ci->groupBy(['id_user', 'tanggal']);
        $user_ci = json_decode($user_ci, true);
        // dd($user_default, $tanggal_values, $get_data);
        // dd($user_ci);

        // nambah cuti 


        $get_cuti = array();
        foreach ($user_ci as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $range = $value2[0]['range_date'];
                $ket = $value2[0]['pekerjaan'];

                $pekerjaan = explode('$', $range);

                $start = $pekerjaan[0];
                $end = $pekerjaan[1];
                $get_cuti[$key][$key2]['start'] = $start;
                $get_cuti[$key][$key2]['end'] = $end;

                // dd($value2);
                // Generate dates from start to end
                $currentDate = strtotime($start);
                while ($currentDate <= strtotime($end)) {
                    $date = date("Y-m-d", $currentDate);

                    if (date('N', strtotime($date)) == 7) {
                        $get_cuti[$key][$date] = "minggu";
                    } else {
                        if ($ket == 'Cuti') {
                            $get_cuti[$key][$date] = 'CT';
                        } else {
                            $get_cuti[$key][$date] = 'I';
                        }
                    }

                    $currentDate = strtotime("+1 day", $currentDate);
                }
            }
        }
        // dd($user_ci, $get_cuti);

        $final_data = array();
        foreach ($get_data as $key => $value) {
            if (array_key_exists($key, $get_cuti)) {
                // Check if the key exists in both get_data and get_cuti
                $dataKeys = array_keys($value);
                $cutiKeys = array_keys($get_cuti[$key]);

                // Merge data when keys match
                $final_data[$key] = array_replace($value, $get_cuti[$key]);

                // Ensure there are no extra keys in get_cuti
                $extraKeys = array_diff($cutiKeys, $dataKeys);
                foreach ($extraKeys as $extraKey) {
                    unset($final_data[$key][$extraKey]);
                }
            } else {
                // If the key doesn't exist in get_cuti, keep the original data
                $final_data[$key] = $value;
            }
        }
        $updatedArray = [];



        // Your existing code
        foreach ($final_data as $key => $value) {
            // Initialize the count for each entry
            $count = 0;

            // Check if the value is an array
            if (is_array($value)) {
                foreach ($value as $innerKey => $innerValue) {
                    if (is_string($innerValue) && strpos($innerValue, 'KJ$') !== false) {
                        $count++;
                    }
                }
            }

            // Create a new array with "total" key and count value
            $final_data[$key]['total'] = strval($count);
        }

        $absensiArray = [];

        foreach ($final_data as $subArray) {
            $updatedSubArray = [];

            foreach ($subArray as $key => $value) {
                // Check if the value contains "KJ$"
                if (is_string($value) && strpos($value, 'KJ$') !== false) {
                    // Split the value by '$'
                    $parts = explode('KJ$', $value);

                    // Assign the second part to the value
                    $updatedSubArray[$key] = $parts[1];
                } else {
                    // Keep the original value if it doesn't contain "KJ$"
                    $updatedSubArray[$key] = $value;
                }
            }

            // Add the updated sub-array to the new array
            $absensiArray[] = $updatedSubArray;
        }


        // dd($absensiArray);
        $apiEndpoint = "https://api-harilibur.vercel.app/api";

        // Make the API call using Laravel's HTTP client
        $response = Http::get($apiEndpoint);

        // Check if the API call was successful
        if ($response->successful()) {
            // Decode the JSON response
            $apiData = $response->json();

            // Check if the decoding was successful and the expected data structure is present
            if (isset($apiData[0]['holiday_date'], $apiData[0]['is_national_holiday'], $apiData[0]['holiday_name'])) {
                // Filter dates where is_national_holiday is true
                $nationalHolidays = array_filter($apiData, function ($holiday) {
                    return $holiday['is_national_holiday'] == true;
                });

                // Extract the holiday dates and names from the filtered data
                $holidays = array_column($nationalHolidays, 'holiday_date');
                $holidayNames = array_column($nationalHolidays, 'holiday_name');

                // Now $holidays contains the list of national holidays
                // Assume $absensiArray is your array of dates
                foreach ($absensiArray as $key => $absensi) {
                    foreach ($absensi as $date => $status) {
                        if (in_array($date, $holidays)) {
                            // Find the index of the date in $holidays and get the corresponding holiday name
                            $index = array_search($date, $holidays);
                            $absensiArray[$key][$date] = $holidayNames[$index];
                        }
                    }
                }
            } else {
                return response()->json(['error' => 'Failed to decode API response or missing expected data structure.'], 500);
            }
        } else {
            return response()->json(['error' => 'Failed to fetch data from the API.'], 500);
        }


        // dd($absensiArray);

        $arrView = array();
        $arrView['header_month'] = $header_month; // Ensure it's a string
        $arrView['dates'] = $result;
        $arrView['JumlahBulan'] = $JumlahBulan;
        $arrView['data_absensi'] = $absensiArray;

        return response()->json($arrView); // Laravel's response to JSON
    }

    public function getMaps(Request $request)
    {
        // $userid = $request->input('userid');
        $date = $request->input('date');

        $lok = $request->session()->get('lok');
        // dd($lok);

        $userlist = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('lokasi_kerja', $lok)
            ->where('email', 'like', '%mandor%')
            ->pluck('user_id');

        // dd($userlist);

        $data = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*')
            ->whereIn('id_user', $userlist)
            ->where('waktu_absensi', 'like', '%' . $date . '%')
            ->get();
        $id_kerja = DB::connection('mysql2')->table('list_pekerjaan')
            ->select('list_pekerjaan.*')
            ->get();


        // dd($data, $date);

        $geoJSON = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($data as $item) {
            // dd($item);
            $pekerjaan = '-';
            foreach ($id_kerja as $keyx => $valuex) {
                // dd($valuex, $item);
                $getidkerja = $item->id_pekerjaan;

                // dd($getidkerja);
                if ($valuex->id == $item->id_pekerjaan) {
                    $pekerjaan = $valuex->nama;
                }
            }
            // dd($id_kerja);

            $feature = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$item->lat, $item->lon],
                ],
                'properties' => [
                    'id' => $item->id,
                    'nama_user' => $item->nama_user,
                    'waktu_absensi' => $item->waktu_absensi,
                    'jenis_kerja' => $pekerjaan
                ],
                'images' => [
                    'img' => $item->foto,
                ]
            ];
            $geoJSON['features'][] = $feature;
        }

        // dd($geoJSON);

        $estplot = DB::connection('mysql2')->table('estate_plot')
            ->select('estate_plot.*', 'wil.*', 'reg.*', 'estate.*')
            ->join('estate', 'estate.est', '=', 'estate_plot.est')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->join('reg', 'reg.id', '=', 'wil.regional')
            ->where('reg.nama', '=', $lok)
            ->get();
        $estplot = $estplot->groupBy(['nama']);
        $estplot = json_decode($estplot, true);



        $lating = [];

        foreach ($estplot as $key => $value) {
            #dd

            foreach ($value as $key2 => $value2) {
                // dd($value2);


                $lating[$key][$key2]['lating'] = $value2['lat'] . '$' . $value2['lon'];
            }
        }
        // dd($lating);



        // dd($estplot);
        $arrView['datauser'] = $geoJSON; // Ensure it's a string
        $arrView['GeoPlot'] = $lating; // Ensure it's a string

        return response()->json($arrView); // Laravel's response to JSON

    }

    public function exportPDF(Request $request)
    {
        // Retrieve parameters from the request
        $regional = $request->input('regional');
        $bulan = $request->input('date');
        $token = $request->input('_token');



        $regs = DB::connection('mysql2')->table('reg')
            ->where('reg.id', $regional)
            ->pluck('nama');
        // dd($regs);


        // Split the "2023-09" string into year and month
        list($year, $month) = explode('-', $bulan);

        // Create a Carbon instance for the first day of the specified month
        $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $JumlahBulan = $firstDayOfMonth->daysInMonth;

        $carbonDate = Carbon::parse($bulan);

        // Get the full name of the month
        $monthName = $carbonDate->format('M');

        $header_month = $monthName . '-' . $JumlahBulan;

        // dd($monthName);


        $endDate = $firstDayOfMonth->copy()->endOfMonth();

        $datesnew = [];

        while ($firstDayOfMonth->lte($endDate)) {
            $datesnew[] = $firstDayOfMonth->toDateString();
            $firstDayOfMonth->addDay();
        }
        // dd($datesnew);

        // Now $dates contains all dates in October

        $result = [];
        for ($i = 1; $i <= $JumlahBulan; $i++) {
            $result[$i - 1] = $i;
        }

        // dd($result);
        $user_Data = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('departemen', 'QC')
            ->where('lokasi_kerja', $regs)
            ->where('email', 'like', '%mandor%')
            ->get();

        // You can remove the unnecessary conversion to JSON
        $user_Data = $user_Data->groupBy('user_id');
        $user_Data = json_decode($user_Data, true);


        // dd($datesnew);

        $user_default = [];

        foreach ($user_Data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $user_default[$key] = [
                    'id' => $value2['user_id'],
                    'nama' => $value2['nama_lengkap'],
                    'payroll' => '-',
                ];

                // Loop through $datesnew and add each date with a default value of "-"
                foreach ($datesnew as $date) {
                    // Check if the day of the week for the date is Sunday (day number 7)
                    if (date('N', strtotime($date)) == 7) {
                        $user_default[$key][$date] = "minggu";
                    } else {
                        $user_default[$key][$date] = "";
                    }
                }
                $user_default[$key]['total'] = 0;
            }
        }


        // dd($user_default);
        $user_absensi = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*', 'list_pekerjaan.nama as pekerjaan', DB::raw('DATE(waktu_absensi) as tanggal'),)
            ->where('waktu_absensi', 'like', '%' . $bulan . '%')
            ->join('list_pekerjaan', 'list_pekerjaan.id', '=', 'absensi_qc.id_pekerjaan')
            ->get();

        // You can remove the unnecessary conversion to JSON
        // $user_absensi = $user_absensi->groupBy('id_user', 'tanggal');
        $user_absensi = $user_absensi->groupBy(['id_user', 'tanggal']);
        $user_absensi = json_decode($user_absensi, true);


        // dd($user_absensi, $bulan);

        $tanggal_values = []; // Create an array to store unique "tanggal" values
        // dd($user_absensi, $tanggal_values);
        foreach ($user_absensi as $key => $value) {
            foreach ($value as $tanggal => $entries) {
                $dayOfWeek = date('N', strtotime($tanggal));
                if (!in_array($tanggal, $tanggal_values)) {
                    $tanggal_values[$tanggal] = ($dayOfWeek == 7) ? 'minggu' : 'MK';
                }
            }
        }


        $getkerja = DB::connection('mysql2')->table('list_pekerjaan')
            ->get();
        $getkerja = json_decode($getkerja, true);
        // dd($getkerja);
        // dd($tanggal_values);


        // dd($tanggal_values);

        // dd($user_absensi);

        $newabsensi = [];
        foreach ($user_absensi as $key => $value) {
            foreach ($value as $key2 => $value2) {
                // dd($value2);
                foreach ($value2 as $key3 => $value3) {
                    $date = $value3['waktu_absensi'];
                    $formattedTime = date('H:i:s', strtotime($date));


                    $specificValues = ['Lainnya', 'Sakit', 'Cuti', 'Izin'];

                    $pekerjaan = explode('$', $value3['id_pekerjaan']);


                    // dd($date);
                    // dd($pekerjaan);
                    $getkerjax = $pekerjaan[0];

                    $kerja = '-';
                    foreach ($getkerja as $keyx => $valuex) {
                        # code...
                        // dd($value);

                        if ($valuex['id'] == $getkerjax) {
                            # code...
                            $kerja = $valuex['nama'];
                        }
                    }

                    $ket = '-';

                    if (!in_array($kerja, $specificValues)) {
                        $ket = 'KJ' . '$' . $formattedTime;
                    } elseif ($kerja == 'Sakit' || $kerja == 'Ijin') {
                        $ket = 'I';
                    } elseif ($kerja == 'Cuti') {
                        $ket = 'CT';
                    }

                    // dd($value);
                }

                $newabsensi[$key][$key2] = $ket;


                // $user_absensi[$key][$formattedDate] = $ket;
            }
        }

        // dd($newabsensi);
        // dd($newabsensi);
        $result = [];

        foreach ($user_default as $key => $user) {
            $updatedUser = $user;
            foreach ($tanggal_values as $date => $value) {
                if (array_key_exists($date, $updatedUser)) {
                    $updatedUser[$date] = $value;
                }
            }
            $result[$key] = $updatedUser;
        }

        // The $result array contains the updated user arrays.



        // dd($result);



        $get_data = [];

        foreach ($result as $key => $value) {
            if (array_key_exists($key, $newabsensi)) {
                $get_data[$key] = array_replace($value, $newabsensi[$key]);
            } else {
                // Check if the key exists in "tanggal_values," and if so, use its value
                if (array_key_exists($key, $tanggal_values)) {
                    $value[$key] = $tanggal_values[$key];
                }
                $get_data[$key] = $value;
            }
        }

        // dd($tanggal_values);

        $user_ci = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*', 'list_pekerjaan.nama as pekerjaan', DB::raw('DATE(waktu_absensi) as tanggal'))
            ->join('list_pekerjaan', 'list_pekerjaan.id', '=', 'absensi_qc.id_pekerjaan')
            ->where('range_date', '!=', 0)
            ->get();


        // You can remove the unnecessary conversion to JSON
        // $user_ci = $user_ci->groupBy('id_user', 'tanggal');
        $user_ci = $user_ci->groupBy(['id_user', 'tanggal']);
        $user_ci = json_decode($user_ci, true);
        // dd($user_default, $tanggal_values, $get_data);


        // nambah cuti 


        $get_cuti = array();
        foreach ($user_ci as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $range = $value2[0]['range_date'];
                $ket = $value2[0]['pekerjaan'];

                $pekerjaan = explode('$', $range);

                $start = $pekerjaan[0];
                $end = $pekerjaan[1];
                $get_cuti[$key][$key2]['start'] = $start;
                $get_cuti[$key][$key2]['end'] = $end;

                // dd($value2);
                // Generate dates from start to end
                $currentDate = strtotime($start);
                while ($currentDate <= strtotime($end)) {
                    $date = date("Y-m-d", $currentDate);

                    if (date('N', strtotime($date)) == 7) {
                        $get_cuti[$key][$date] = "minggu";
                    } else {
                        if ($ket == 'Cuti') {
                            $get_cuti[$key][$date] = 'CT';
                        } else {
                            $get_cuti[$key][$date] = 'I';
                        }
                    }

                    $currentDate = strtotime("+1 day", $currentDate);
                }
            }
        }
        // dd($user_ci, $get_cuti);

        $final_data = array();
        foreach ($get_data as $key => $value) {
            if (array_key_exists($key, $get_cuti)) {
                // Check if the key exists in both get_data and get_cuti
                $dataKeys = array_keys($value);
                $cutiKeys = array_keys($get_cuti[$key]);

                // Merge data when keys match
                $final_data[$key] = array_replace($value, $get_cuti[$key]);

                // Ensure there are no extra keys in get_cuti
                $extraKeys = array_diff($cutiKeys, $dataKeys);
                foreach ($extraKeys as $extraKey) {
                    unset($final_data[$key][$extraKey]);
                }
            } else {
                // If the key doesn't exist in get_cuti, keep the original data
                $final_data[$key] = $value;
            }
        }
        $updatedArray = [];



        // Your existing code
        foreach ($final_data as $key => $value) {
            // Initialize the count for each entry
            $count = 0;

            // Check if the value is an array
            if (is_array($value)) {
                foreach ($value as $innerKey => $innerValue) {
                    if (is_string($innerValue) && strpos($innerValue, 'KJ$') !== false) {
                        $count++;
                    }
                }
            }

            // Create a new array with "total" key and count value
            $final_data[$key]['total'] = strval($count);
        }

        $updatedArray = [];

        foreach ($final_data as $subArray) {
            $updatedSubArray = [];

            foreach ($subArray as $key => $value) {
                // Check if the value contains "KJ$"
                if (is_string($value) && strpos($value, 'KJ$') !== false) {
                    // Split the value by '$'
                    $parts = explode('KJ$', $value);

                    // Assign the second part to the value
                    $updatedSubArray[$key] = $parts[1];
                } else {
                    // Keep the original value if it doesn't contain "KJ$"
                    $updatedSubArray[$key] = $value;
                }
            }

            // Add the updated sub-array to the new array
            $updatedArray[] = $updatedSubArray;
        }

        // dd($updatedArray);

        $newArray = [];

        foreach ($updatedArray as $item) {
            $newItem = []; // Create a new item for the modified array

            // Iterate through the keys of the original item
            foreach ($item as $key => $value) {
                // Check if the key is in the format of "yyyy-mm-dd"
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $key)) {
                    // Extract the day from the date and create a new key "date{day}"
                    $day = substr($key, -2);
                    $newKey = "date{$day}";

                    // Add the new key-value pair to the new item
                    $newItem[$newKey] = $value;
                } else {
                    // Keep non-date keys as they are
                    $newItem[$key] = $value;
                }
            }

            // Add the new item to the modified array
            $newArray[] = $newItem;
        }

        // Now, $newArray contains the modified data with date keys renamed
        // dd($newArray);


        $arrView = array();
        $arrView['Dataabsensi'] = $newArray;
        $arrView['bulan'] = $bulan;
        $arrView['Reg'] = $regs[0];
        $arrView['header_month'] = $header_month;
        $arrView['JumlahBulan'] = $JumlahBulan;

        // dd($header_month, $JumlahBulan);

        $pdf = PDF::loadView('Absensi.pdf', ['data' => $arrView]);
        $pdf->setPaper('A2', 'landscape');

        $filename = 'PDF Absensi' . ' ' . $arrView['bulan'] . '.pdf';

        return $pdf->stream($filename);
    }

    public function getimgBukti(Request $request)
    {
        // $userid = $request->input('userid');
        $date = $request->input('date');
        $lok = $request->session()->get('lok');
        $user_Data = DB::table('pengguna')
            ->select('pengguna.*')
            ->where('departemen', 'QC')
            ->where('lokasi_kerja', $lok)
            ->where('email', 'like', '%mandor%')
            ->pluck('user_id');


        $user_Data = json_decode($user_Data, true);



        $datauser = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*')
            ->whereIN('id_user', $user_Data)
            ->where('waktu_absensi', 'LIKE', '%' . $date . '%')
            ->get();
        $datauser = $datauser->groupBy(['id_user']);
        $datauser = json_decode($datauser, true);

        // dd($datauser);
        $getkerja = DB::connection('mysql2')->table('list_pekerjaan')
            ->get();
        $getkerja = json_decode($getkerja, true);

        $imgdata = [];
        foreach ($datauser as $key => $value) {
            # code...
            foreach ($value as $key1 => $value1) {
                # code...
                // dd($value1);
                $kerja = '-';
                foreach ($getkerja as $keyx => $valuex) {
                    # code...
                    // dd($value);

                    if ($valuex['id'] == $value1['id_pekerjaan']) {
                        # code...
                        $kerja = $valuex['nama'];
                    }
                }
                $date = $value1['waktu_absensi'];
                $formattedTime = date('H:i:s', strtotime($date));

                $imgdata[$key]['nama'] = $value1['nama_user'];
                $imgdata[$key]['foto'] = $value1['foto'];
                $imgdata[$key]['jam'] = $formattedTime;
                $imgdata[$key]['pekerjaan'] = $kerja;
            }
        }

        // dd($imgdata);
        // dd($datauser, $date);
        $arrView = array();
        $arrView['data'] = $imgdata; // Ensure it's a string
        return response()->json($arrView); // Laravel's response to JSON
    }
}
