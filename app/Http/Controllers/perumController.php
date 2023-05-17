<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Barryvdh\DomPDF\Facade\Pdf;


use function PHPUnit\Framework\isEmpty;

require '../app/helpers.php';
class perumController extends Controller
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

        $arrView = array();

        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;


        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }

    public function estAFD(Request $request)
    {
        $regional = $request->input('reg');
        $bulan = $request->input('tahun');

        // Perform any required processing here

        // Return a JSON response
        $arrView = array();

        $arrView['reg'] =  $regional;
        $arrView['bulan'] =  $bulan;

        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }
}
