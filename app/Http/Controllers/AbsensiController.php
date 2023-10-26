<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    //

    public function index()
    {

        // use Carbon\Carbon;

        $firstDayOfMonth = Carbon::now()->firstOfMonth();
        $JumlahBulan = $firstDayOfMonth->daysInMonth;
        $bulanNow = Carbon::now()->format('M');
        // dd($bulanNow);

        $header_month = $bulanNow . '-' . $JumlahBulan;

        // dd($header_month);

        return view('Absensi.index', ['header_month' => $header_month, 'dates' => $JumlahBulan]);
    }

    public function data(Request $request)
    {
        $regional = $request->input('regional');
        $bulan = $request->input('dateMonth');

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
            ->where('lokasi_kerja', 'Regional I')
            ->where('email', 'like', '%mandor%')
            ->get();

        // You can remove the unnecessary conversion to JSON
        $user_Data = $user_Data->groupBy('user_id');
        $user_Data = json_decode($user_Data, true);


        // dd($user_Data);

        $user_default = [];

        foreach ($user_Data as $key => $value) {
            foreach ($value as $key2 => $value2) {
                # code...
                $user_default[$key] = [
                    'id' => $value2['user_id'],
                    'nama' => $value2['nama_lengkap'],
                    'total' => '-'
                ];

                // Loop through $datenew and add each date with a default value "-"
                foreach ($datesnew as $date) {
                    $user_default[$key][$date] = "";
                }
            }
        }

        $user_absensi = DB::connection('mysql2')->table('absensi_qc')
            ->select('absensi_qc.*', 'list_pekerjaan.nama as pekerjaan', DB::raw('DATE(waktu_absensi) as tanggal'),)
            ->where('waktu_absensi', 'like', '%' . $bulan . '%')
            ->join('list_pekerjaan', 'list_pekerjaan.id', '=', 'absensi_qc.id_pekerjaan')
            ->get();

        // You can remove the unnecessary conversion to JSON
        // $user_absensi = $user_absensi->groupBy('id_user', 'tanggal');
        $user_absensi = $user_absensi->groupBy(['id_user', 'tanggal']);
        $user_absensi = json_decode($user_absensi, true);

        $tanggal_values = []; // Create an array to store unique "tanggal" values

        foreach ($user_absensi as $key => $value) {
            foreach ($value as $tanggal => $entries) {
                // Check if the "tanggal" value is not already in the $tanggal_values array
                if (!in_array($tanggal, $tanggal_values)) {
                    $tanggal_values[$tanggal] = 'MK';
                }
            }
        }



        // dd($tanggal_values);
        foreach ($user_absensi as $key => $value) {
            foreach ($value as $key2 => $value2) {
                // dd($value2);

                foreach ($value2 as $key3 => $value3) {
                    $date = $value3['waktu_absensi'];
                    $formattedDate = date('Y-m-d', strtotime($date));

                    $specificValues = ['Lainnya', 'Sakit', 'Cuti', 'Izin'];

                    if (!in_array($value3['pekerjaan'], $specificValues)) {
                        $ket = 'KJ';
                    } elseif ($value3['pekerjaan'] == 'Sakit' || $value3['pekerjaan'] == 'Ijin') {
                        $ket = 'I';
                    } elseif ($value3['pekerjaan'] == 'Cuti') {
                        $ket = 'CT';
                    }

                    // dd($value);

                    # code...
                    $user_absensi[$key][$key2] = [
                        'id' => $value3['id_user'],
                        'nama' => $value3['nama_user'],
                        'total' => '-',
                        'date' => $date,
                        'kerja' => $ket,
                        'keterangan' => $value3['pekerjaan'],
                    ];
                }

                // $user_absensi[$key][$formattedDate] = $ket;
            }
        }


        foreach ($user_default as $key => &$value) {
            foreach ($value as $key2 => $value2) {
                foreach ($tanggal_values as $key3 => $value3) {
                    if ($key2 == $key3) {
                        // Replace the key2 with key3 value
                        $value[$key3] = $value2;
                        unset($value[$key2]);
                    }
                }
            }
        }

        // Unset the reference to prevent side effects
        unset($value);
        // Now, $user_default contains the updated values
        dd($user_default);


        $get_data = [];

        foreach ($user_default as $key => $value) {
            if (array_key_exists($key, $user_absensi)) {
                $get_data[$key] = array_replace($value, $user_absensi[$key]);
            } else {
                // Check if the key exists in "tanggal_values," and if so, use its value
                if (array_key_exists($key, $tanggal_values)) {
                    $value[$key] = $tanggal_values[$key];
                }
                $get_data[$key] = $value;
            }
        }

        dd($user_default, $tanggal_values, $get_data);


        $arrView = array();
        $arrView['header_month'] = $header_month; // Ensure it's a string
        $arrView['dates'] = $result;
        $arrView['JumlahBulan'] = $JumlahBulan;

        return response()->json($arrView); // Laravel's response to JSON
    }
}
