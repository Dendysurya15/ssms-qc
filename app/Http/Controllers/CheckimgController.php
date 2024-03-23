<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckimgController extends Controller
{
    //
    public function dashboard()
    {

        return view('check.index', []);
    }

    public function getIMGgudang(Request $request)
    {
        $regional = $request->input('reg');
        $bulan = $request->input('tahun');

        $emplacement = DB::connection('mysql2')->table('qc_gudang')
            ->select(
                "qc_gudang.*",
                DB::raw('DATE_FORMAT(qc_gudang.tanggal, "%M") as bulan'),
                DB::raw('DATE_FORMAT(qc_gudang.tanggal, "%Y") as tahun'),
            )
            ->where('qc_gudang.tanggal', 'like', '%' . $bulan . '%')
            ->orderBy('tanggal', 'asc')
            ->get();

        $emplacement = json_decode(json_encode($emplacement), true); // Convert the collection to an array

        $databaseImageNames = [];
        foreach ($emplacement as $item) {
            foreach ($item as $key => $value) {
                if (strpos($value, 'Gudang_QC') !== false && strpos($key, 'foto_') === 0) {
                    $photoFilenames = explode(';', $value);
                    foreach ($photoFilenames as $filename) {
                        $databaseImageNames[] = $filename;
                    }
                }
            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://srs-ssms.com/qc_inspeksi/missGudang.php',
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            // Handle cURL error
            echo 'cURL Error: ' . curl_error($curl);
        } else {
            $folderImageNames = json_decode($response);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error
                echo 'JSON Decoding Error: ' . json_last_error_msg();
            } else {
                // Proceed with your logic
            }
        }

        curl_close($curl);
        // dd($folderImageNames);

        $missingImages = array_diff($databaseImageNames, $folderImageNames);

        // dd($missingImages);
    }
}
