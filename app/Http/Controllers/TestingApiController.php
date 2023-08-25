<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TestingApiController extends Controller
{

    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $est = $request->input('est');

        if ($est === null) {

            $emplacement = DB::connection('mysql2')->table('mutu_ancak_new')
                ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
                ->where('datetime', 'like', '%' . $tanggal . '%')
                ->get();
        } else {

            $emplacement = DB::connection('mysql2')->table('mutu_ancak_new')
                ->select("mutu_ancak_new.*", DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
                ->where('estate', $est)
                ->where('datetime', 'like', '%' . $tanggal . '%')
                ->get();
        }





        return response()->json($emplacement);
    }
}
