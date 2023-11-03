<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class testingmapsController extends Controller
{
    //
    public function index()
    {
        $quryReg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->get();


        $quryReg = json_decode($quryReg, true);

        $queryEstate = DB::connection('mysql2')->table('estate')
            ->select('estate.*', 'reg.*', 'reg.id as regid')
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->join('reg', 'reg.id', '=', 'wil.regional')
            ->where('estate.emp', '=', 0)
            ->where('estate.est', '!=', 'PLASMA')
            ->get();



        $queryEstate = json_decode($queryEstate, true);

        $queryafd = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*', 'estate.id', 'estate.est', 'afdeling.id as afdid')
            ->join('estate', 'estate.id', 'afdeling.estate')
            ->get();
        // dd($queryafd, $queryEstate);



        $queryafd = json_decode($queryafd, true);


        return view('testingaja', ['reg' => $quryReg, 'estate' => $queryEstate, 'afd' => $queryafd]);
    }
}
