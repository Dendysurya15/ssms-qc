<?php

use App\Http\Controllers\unitController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SidaktphController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TesExportController;
use App\Http\Controllers\inspectController;
use App\Http\Controllers\mutubuahController;
use App\Http\Controllers\emplacementsController;
use App\Http\Controllers\userNewController;
use App\Http\Controllers\UserQCController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [loginController::class, 'authenticate'])->name('login');
Route::post('logout', [loginController::class, 'logout'])->name('logout');
// Route::middleware(['auth'])->group(function () {
Route::get('/index', [unitController::class, 'index']);
Route::get('/dashboard_gudang', [unitController::class, 'dashboard_gudang'])->name('dashboard_gudang');
Route::get('/dashboardtph', [SidaktphController::class, 'index'])->name('dashboardtph');
Route::get('/listAsisten', [SidaktphController::class, 'listAsisten'])->name('listAsisten');
Route::post('/tambahAsisten', [SidaktphController::class, 'tambahAsisten'])->name('tambahAsisten');
Route::post('/perbaruiAsisten', [SidaktphController::class, 'perbaruiAsisten'])->name('perbaruiAsisten');
Route::post('/hapusAsisten', [SidaktphController::class, 'hapusAsisten'])->name('hapusAsisten');
Route::post('/getData', [SidaktphController::class, 'getData'])->name('getData');
Route::post('/dashboardtph', [SidaktphController::class, 'chart'])->name('chart');
Route::post('/downloadPDF', [SidaktphController::class, 'downloadPDF'])->name('downloadPDF');

Route::post('/getBtTph', [SidaktphController::class, 'getBtTph'])->name('getBtTph');
Route::post('/getKrTph', [SidaktphController::class, 'getKrTph'])->name('getKrTph');
Route::post('/getBHtgl', [SidaktphController::class, 'getBHtgl'])->name('getBHtgl');
Route::get('/exportPDF', [SidaktphController::class, 'exportPDF'])->name('exportPDF');

Route::post('/changeRegionEst', [SidaktphController::class, 'changeRegionEst'])->name('changeRegionEst');

Route::post('/changeDataTph', [SidaktphController::class, 'changeDataTph'])->name('changeDataTph');

Route::get('/getBtTphMonth', [SidaktphController::class, 'getBtTphMonth'])->name('getBtTphMonth');

Route::post('/getBtTphYear', [SidaktphController::class, 'getBtTphYear'])->name('getBtTphYear');

Route::post('/graphFilterYear', [SidaktphController::class, 'graphFilterYear'])->name('graphFilterYear');
// Route::get('/404', [SidaktphController::class, 'notfound'])->name('404');
Route::post('/getDataByYear', [unitController::class, 'getDataByYear'])->name('getDataByYear');

Route::get('/tambah', [unitController::class, 'tambah']);
Route::post('/store', [unitController::class, 'store']);
Route::get('/edit/{id}', [unitController::class, 'edit']);
Route::post('/update', [unitController::class, 'update']);
Route::get('/hapus/{id}', [unitController::class, 'hapus']);
Route::get('detailInspeksi/{id}', [unitController::class, 'detailInspeksi'])->name('detailInspeksi');
Route::get('detailSidakTph/{est}/{afd}/{start}/{last}', [SidaktphController::class, 'detailSidakTph'])->name('detailSidakTph');
Route::post('getDetailTPH', [SidaktphController::class, 'getDetailTPH'])->name('getDetailTPH');
Route::get('getPlotLine', [SidaktphController::class, 'getPlotLine'])->name('getPlotLine');
Route::get('/qc', [unitController::class, 'load_qc_gudang'])->name('qc');
Route::get('/hapusRecord/{id}', [unitController::class, 'hapusRecord'])->name('hapusRecord');
Route::get('/cetakpdf/{id}', [unitController::class, 'cetakpdf']);
// });

Route::get('/dashboard_inspeksi', [inspectController::class, 'dashboard_inspeksi'])->name('dashboard_inspeksi');
Route::get('/cetakPDFFI/{id}/{est}/{tgl}', [inspectController::class, 'cetakPDFFI'])->name('cetakPDFFI');
Route::post('/getFindData', [inspectController::class, 'getFindData'])->name('getFindData');
Route::post('/changeDataInspeksi', [inspectController::class, 'changeDataInspeksi'])->name('changeDataInspeksi');
Route::post('/plotEstate', [inspectController::class, 'plotEstate'])->name('plotEstate');
Route::get('/plotBlok', [inspectController::class, 'plotBlok'])->name('plotBlok');
// Route::post('/filter', [inspectController::class, 'filter']);


Route::get('/filter', [inspectController::class, 'filter'])->name('filter');
Route::get('/graphfilter', [inspectController::class, 'graphfilter'])->name('graphfilter');
Route::get('/filterTahun', [inspectController::class, 'filterTahun'])->name('filterTahun');
Route::get('/scorebymap', [inspectController::class, 'scorebymap'])->name('scorebymap');
Route::get('detailInpeksi/{est}/{afd}/{date}', [inspectController::class, 'detailInpeksi'])->name('detailInpeksi');
Route::get('dataDetail/{est}/{afd}/{date}/{reg}', [inspectController::class, 'dataDetail'])->name('dataDetail');
Route::get('filterDataDetail', [inspectController::class, 'filterDataDetail'])->name('filterDataDetail');


Route::post('/updateBA', [inspectController::class, 'updateBA'])->name('updateBA');

Route::post('/deleteBA', [inspectController::class, 'deleteBA'])->name('deleteBA');


Route::delete('/deleteTrans/{id}', [inspectController::class, 'deleteTrans'])->name('deleteTrans');
Route::post('/pdfBA', [inspectController::class, 'pdfBA'])->name('pdfBA');

Route::post('/fetchEstatesByRegion', [inspectController::class, 'fetchEstatesByRegion'])->name('fetchEstatesByRegion');
Route::get('/listktu', [unitController::class, 'listktu'])->name('listktu');
Route::post('/tambahKTU', [unitController::class, 'tambahKTU'])->name('tambahKTU');
Route::post('/updateKTU', [unitController::class, 'updateKTU'])->name('updateKTU');
Route::post('/hapusKTU', [unitController::class, 'hapusKTU'])->name('hapusKTU');

Route::post('/hapusDetailSidak', [SidaktphController::class, 'hapusDetailSidak'])->name('hapusDetailSidak');
// Route::get('BaSidakTPH/{est}/{start}/{last}', [SidaktphController::class, 'BasidakTph'])->name('BasidakTph');
Route::get('BaSidakTPH/{est}/{afd}/{tanggal}/{regional}', [SidaktphController::class, 'BasidakTph'])->name('BasidakTph');

Route::get('/dashboard_mutubuah', [mutubuahController::class, 'dashboard_mutubuah'])->name('dashboard_mutubuah');
Route::get('/getWeek', [MutubuahController::class, 'getWeek'])->name('getWeek');
Route::get('/getYear', [MutubuahController::class, 'getYear'])->name('getYear');
Route::get('/getYearData', [MutubuahController::class, 'getYearData'])->name('getYearData');
Route::get('/findingIsueTahun', [MutubuahController::class, 'findingIsueTahun'])->name('findingIsueTahun');
Route::get('/getWeekData', [MutubuahController::class, 'getWeekData'])->name('getWeekData');
Route::get('/getahun_sbi', [MutubuahController::class, 'getahun_sbi'])->name('getahun_sbi');

Route::get('filtersidaktphrekap', [SidaktphController::class, 'filtersidaktphrekap'])->name('filtersidaktphrekap');
Route::post('/deleteBAsidakTPH', [SidaktphController::class, 'deleteBAsidakTPH'])->name('deleteBAsidakTPH');
Route::post('/updateBASidakTPH', [SidaktphController::class, 'updateBASidakTPH'])->name('updateBASidakTPH');
Route::get('pdfBAsidak', [SidaktphController::class, 'pdfBAsidak'])->name('pdfBAsidak');



Route::get('/cetakmutubuah_id/{est}/{tahun}/{reg}', [MutubuahController::class, 'cetakmutubuahsidak'])->name('cetakmutubuahsidak');
Route::get('/chartsbi_oke', [MutubuahController::class, 'chartsbi_oke'])->name('chartsbi_oke');

Route::get('detailtmutubuah/{est}/{afd}/{bulan}', [MutubuahController::class, 'detailtmutubuah'])->name('detailtmutubuah');
Route::get('filterdetialMutubuah', [MutubuahController::class, 'filterdetialMutubuah'])->name('filterdetialMutubuah');
Route::post('/updateBA_mutubuah', [MutubuahController::class, 'updateBA_mutubuah'])->name('updateBA_mutubuah');
Route::post('/deleteBA_mutubuah', [MutubuahController::class, 'deleteBA_mutubuah'])->name('deleteBA_mutubuah');
Route::post('/pdfBA_sidakbuah', [MutubuahController::class, 'pdfBA_sidakbuah'])->name('pdfBA_sidakbuah');
Route::post('/findIssueSmb', [MutubuahController::class, 'findIssueSmb'])->name('findIssueSmb');
Route::get('/cetakFiSmb/{est}/{tgl}', [MutubuahController::class, 'cetakFiSmb'])->name('cetakFiSmb');


Route::get('/getMapsdetail', [inspectController::class, 'getMapsdetail'])->name('getMapsdetail');


Route::get('/dashboard_perum', [emplacementsController::class, 'dashboard_perum'])->name('dashboard_perum');
Route::get('/getAFD', [emplacementsController::class, 'getAFD'])->name('getAFD');
Route::get('/estAFD', [emplacementsController::class, 'estAFD'])->name('estAFD');
Route::get('/User/user', [userNewController::class, 'showUser'])->name('user.show');
Route::post('/getuser', [userNewController::class, 'getuser'])->name('getuser');
Route::post('/update_user', [userNewController::class, 'update_user'])->name('update_user');
Route::get('/listAsisten2', [userNewController::class, 'listAsisten2'])->name('listAsisten2');
Route::post('/updateAsisten', [userNewController::class, 'updateAsisten'])->name('updateAsisten');
Route::post('/deleteAsisten', [userNewController::class, 'deleteAsisten'])->name('deleteAsisten');
Route::post('/storeAsisten', [userNewController::class, 'storeAsisten'])->name('storeAsisten');


Route::get('/getWeekInpeksi', [inspectController::class, 'getWeekInpeksi'])->name('getWeekInpeksi');
Route::post('/pdfBA_excel', [inspectController::class, 'pdfBA_excel'])->name('pdfBA_excel');

Route::get('/user_qc/{lokasi_kerja}', [UserQCController::class, 'index'])->name('user_qc');
Route::get('/create', [UserQCController::class, 'create'])->name('create');
Route::post('/store/{lokasi_kerja}', [UserQCController::class, 'store'])->name('store');
Route::get('/edit/{id}', [UserQCController::class, 'edit'])->name('edit');
Route::post('/update/{id}/{lokasi_kerja}', [UserQCController::class, 'update'])->name('update');
Route::post('/delete/{id}', [UserQCController::class, 'destroy'])->name('delete');
Route::get('/getDataRekap', [MutubuahController::class, 'getDataRekap'])->name('getDataRekap');
Route::post('/WeeklyReport', [MutubuahController::class, 'weeklypdf'])->name('WeeklyReport');

Route::get('/getDataDay', [inspectController::class, 'getDataDay'])->name('getDataDay');


Route::get('getMapsTph', [SidaktphController::class, 'getMapsTph'])->name('getMapsTph');


Route::get('/getMapsData', [MutubuahController::class, 'getMapsData'])->name('getMapsData');

Route::get('detailEmplashmend/{est}/{afd}/{date}', [emplacementsController::class, 'detailEmplashmend'])->name('detailEmplashmend');
