@include('layout/header')
<style>
    .tabContainer {}

    .blur {
        filter: blur(4px);
        opacity: 0.5;
    }

    .big-table {
        width: 100% !important;
        position: absolute !important;
        left: 0 !important;
        z-index: 10;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .tabContainer .col-sm-3:not(.blur) {
        cursor: pointer;
    }

    .mode-active {
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    .mode-options {
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .my-table {
        width: 100%;
        border-collapse: collapse;
    }

    .my-table th,
    .my-table td {
        text-align: center;
        padding: 8px;
        border: 1px solid black;
        white-space: nowrap;
    }

    .my-table thead th {
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .my-table thead.horizontal-freeze th {
        position: sticky;
        left: 0;
        z-index: 101;
    }



    .title-row {
        font-size: 1.5em;
        text-align: center;
        margin-bottom: 10px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: scroll;
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    .loading-text {
        animation: fadeInOut 2s ease-in-out infinite;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Add these dependencies in your HTML head -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>




<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Halaman Utama</a>
                        <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Data</a>
                        <a class="nav-item nav-link" id="nav-sbi-tab" data-toggle="tab" href="#nav-sbi" role="tab" aria-controls="nav-sbi" aria-selected="false">SBI</a>
                        <a class="nav-item nav-link" id="nav-issue-tab" data-toggle="tab" href="#nav-issue" role="tab" aria-controls="nav-issue" aria-selected="false">Finding Issue</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>REKAPITULASI RANKING NILAI SIDAK PEMERIKSAAN MUTU BUAH
                                </b></h5>
                        </div>
                        <div class="content">
                            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                                <div class="row w-100">
                                    <div class="col-md-2 offset-md-8">
                                        {{csrf_field()}}
                                        <select class="form-control" id="regionalPanen">
                                            @foreach($option_reg as $key => $item)
                                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputbulan" id="inputbulan">
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="btnShow">Show</button>
                            </div>
                            <style>
                                /* Add button styles */
                                button {
                                    background-color: #4CAF50;
                                    border: none;
                                    color: white;
                                    padding: 8px 16px;
                                    text-align: center;
                                    text-decoration: none;
                                    display: inline-block;
                                    font-size: 16px;
                                    margin: 4px 2px;
                                    cursor: pointer;
                                    transition-duration: 0.4s;
                                }

                                /* Add hover effect */
                                button:hover {
                                    background-color: #45a049;
                                }
                            </style>
                            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 ">
                                <button id="sort-est-btn">Sort by Afd</button>
                                <button id="sort-rank-btn">Sort by Rank</button>
                            </div>
                            <div id="tablesContainer">
                                <div class="tabContainer">
                                    <div class="ml-3 mr-3">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tab1">
                                                <div class="table-responsive">
                                                    <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                        <thead>
                                                            <tr bgcolor="yellow">
                                                                <th colspan="5" id="thead1" class="text-center">WILAYAH I </th>
                                                            </tr>
                                                            <tr bgcolor="#2044a4" style="color: white">
                                                                <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                <th colspan="2" class="text-center">Todate</th>
                                                            </tr>
                                                            <tr bgcolor="#1D43A2" style="color: white">
                                                                <th>Score</th>
                                                                <th>Rank</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="week1">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tab2">
                                                <div class="table-responsive">
                                                    <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                        <thead>
                                                            <tr bgcolor="yellow">
                                                                <th colspan="5" id="thead2" class="text-center">WILAYAH II</th>
                                                            </tr>
                                                            <tr bgcolor="#2044a4" style="color: white">
                                                                <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                <th colspan="2" class="text-center">Todate</th>
                                                            </tr>
                                                            <tr bgcolor="#1D43A2" style="color: white">
                                                                <th>Score</th>
                                                                <th>Rank</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="week2">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tab3">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" style="font-size: 13px" id="Reg3">
                                                        <thead>
                                                            <tr bgcolor="yellow">
                                                                <th colspan="5" id="thead3" class="text-center">WILAYAH III</th>
                                                            </tr>
                                                            <tr bgcolor="#2044a4" style="color: white">
                                                                <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                <th colspan="2" class="text-center">Todate</th>
                                                            </tr>
                                                            <tr bgcolor="#1D43A2" style="color: white">
                                                                <th>Score</th>
                                                                <th>Rank</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="week3">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tab4">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" style="font-size: 13px" id="plasmaID">
                                                        <thead>
                                                            <tr bgcolor="yellow">
                                                                <th colspan="5" class="text-center" id="theadx3">PLASMA1</th>
                                                            </tr>
                                                            <tr bgcolor="#2044a4" style="color: white">
                                                                <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                <th colspan="2" class="text-center">Todate</th>
                                                            </tr>
                                                            <tr bgcolor="#1D43A2" style="color: white">
                                                                <th>Score</th>
                                                                <th>Rank</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="plasma1">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead id="theadreg">

                                    </thead>

                                </table>
                            </div>

                            <p class="ml-3 mb-3 mr-3">
                                <button style="width: 100%" class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#showEstate" aria-expanded="false" aria-controls="showEstate">
                                    Grafik Sidak Mutu Buah Berdasarkan Estate
                                </button>
                            </p>
                            <div class="collapse" id="showEstate">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>MATANG (%)</u></b></p>
                                                <div id="matang"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>MENTAH (%)</u></b></p>
                                                <div id="mentah"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>LEWAT MATANG (%)</u></b></p>
                                                <div id="lewatmatang"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>JANGKOS (%)</u></b></p>
                                                <div id="jangkos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>TIDAK STANDAR V-CUT (%)</u></b></p>
                                                <div id="tidakvcut"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>PENGGUNAAN KARUNG BRONDOLAN (%)</u></b></p>
                                                <div id="karungbrondolan"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="ml-3 mb-3 mr-3">
                                <button style="width: 100%" class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#showWilayah" aria-expanded="false" aria-controls="showWilayah">
                                    Grafik Sidak Mutu Buah Berdasarkan Wilayah
                                </button>
                            </p>
                            <div class="collapse" id="showWilayah">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>MATANG (%)</u></b></p>
                                                <div id="matang_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>MENTAH (%)</u></b></p>
                                                <div id="mentah_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>LEWAT MATANG (%)</u></b></p>
                                                <div id="lewatmatang_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>JANGKOS (%)</u></b></p>
                                                <div id="jangkos_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>TIDAK STANDAR V-CUT (%)</u></b></p>
                                                <div id="tidakvcut_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="font-size: 15px; text-align: center;"><b><u>PENGGUNAAN KARUNG BRONDOLAN (%)</u></b></p>
                                                <div id="karungbrondolan_wil"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <p class="ml-3 mb-3 mr-3">
                            <button style="width: 100%" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#showByYear" aria-expanded="false" aria-controls="showByYear">
                                TAMPILKAN PER MINGGU
                            </button>
                        </p>

                        <div class="collapse" id="showByYear">
                            <div class="d-flex justify-content-center mb-2 ml-3 mr-3 border border-dark">
                                <h5><b>REKAPITULASI RANKING NILAI KUALITAS PANEN</b></h5>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabTable" role="tabpanel">

                                    <style>
                                        .download-btn {
                                            background-color: green;
                                            color: white;
                                        }

                                        .download-btn.disabled {
                                            background-color: grey;
                                            pointer-events: none;
                                        }
                                    </style>

                                    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                                        <div class="row w-100">
                                            <div class="col-md-2 offset-md-8">
                                                {{csrf_field()}}
                                                <select class="form-control" id="regionalData">
                                                    @foreach($option_reg as $key => $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                {{ csrf_field() }}
                                                <input type="hidden" id="startWeek" name="start" value="">
                                                <input type="hidden" id="lastWeek" name="last" value="">
                                                <input type="week" name="dateWeek" id="dateWeek" value="{{ date('Y').'-W'.date('W') }}" aria-describedby="dateWeekHelp">

                                            </div>
                                        </div>
                                        <button class="btn btn-primary mb-3 ml-2" id="showTahung">Show</button>

                                        <form action="{{ route('WeeklyReport') }}" method="POST" class="form-inline" style="display: inline;" target="_blank">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="tglPDF" id="tglPDF" value="">
                                            <input type="hidden" name="regPDF" id="regPDF" value="">
                                            <button type="submit" class="download-btn ml-2" id="download-button">
                                                PDF
                                            </button>
                                            <!-- <button type="submit" class="btn btn-secondary" id="pdfButton" disabled>PDF</button> -->
                                        </form>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 ">
                                        <button id="sort-est-btnWek">Sort by Afd</button>
                                        <button id="sort-rank-btnWek">Sort by Rank</button>
                                    </div>

                                    <div id="tablesContainer">
                                        <div class="tabContainer">
                                            <div class="ml-3 mr-3">
                                                <div class="row justify-content-center">
                                                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tabsx1">
                                                        <div class="table-responsive">
                                                            <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                                <thead>
                                                                    <tr bgcolor="yellow">
                                                                        <th colspan="5" id="theads1">WILAYAH I</th>
                                                                    </tr>
                                                                    <tr bgcolor="#2044a4" style="color: white">
                                                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                        <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                        <th colspan="2" class="text-center">Todate</th>
                                                                    </tr>
                                                                    <tr bgcolor="#1D43A2" style="color: white">
                                                                        <th>Score</th>
                                                                        <th>Rank</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="weeks1">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tabsx2">
                                                        <div class="table-responsive">
                                                            <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                                <thead>
                                                                    <tr bgcolor="yellow">
                                                                        <th colspan="5" id="theads2">WILAYAH II</th>
                                                                    </tr>
                                                                    <tr bgcolor="#2044a4" style="color: white">
                                                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                        <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                        <th colspan="2" class="text-center">Todate</th>
                                                                    </tr>
                                                                    <tr bgcolor="#1D43A2" style="color: white">
                                                                        <th>Score</th>
                                                                        <th>Rank</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="weeks2">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tabsx3">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" style="font-size: 13px" id="Reg3">
                                                                <thead>
                                                                    <tr bgcolor="yellow">
                                                                        <th colspan="5" id="theads3">WILAYAH III</th>
                                                                    </tr>
                                                                    <tr bgcolor="#2044a4" style="color: white">
                                                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                        <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                        <th colspan="2" class="text-center">Todate</th>
                                                                    </tr>
                                                                    <tr bgcolor="#1D43A2" style="color: white">
                                                                        <th>Score</th>
                                                                        <th>Rank</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="weeks3">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="Tabsx4">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" style="font-size: 13px" id="plasmaID">
                                                                <thead>
                                                                    <tr bgcolor="#fffc04">
                                                                        <th colspan="5" id="theads4" style="text-align:center">Plasma</th>
                                                                    </tr>
                                                                    <tr bgcolor="#2044a4" style="color: white">
                                                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                                                                        <th rowspan="2" style="vertical-align: middle;">AFD</th>
                                                                        <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                                                                        <th colspan="2" class="text-center">Todate</th>
                                                                    </tr>
                                                                    <tr bgcolor="#2044a4" style="color: white">
                                                                        <th>Score</th>
                                                                        <th>Rank</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="plasmas1">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-12">
                                        <table class="table table-bordered">
                                            <thead id="theadregs">
                                                <tr>
                                                    <th colspan="1">REG-I</th>
                                                    <th colspan="1">RH-1</th>
                                                    <th colspan="1">Akhmad Faisyal</th>
                                                    <th colspan="8"></th>
                                                </tr>
                                            </thead>

                                        </table>
                                    </div>

                                    <p class="ml-3 mb-3 mr-3">
                                        <button style="width: 100%" class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#showEstate1" aria-expanded="false" aria-controls="showEstate1">
                                            Grafik Sidak Mutu Buah Berdasarkan Estate
                                        </button>
                                    </p>

                                    <div class="collapse" id="showEstate1">
                                        <div class="ml-4 mr-4">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>MATANG (%)</u></b></p>
                                                            <div id="matangthun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>MENTAH (%)</u></b></p>
                                                            <div id="mentahtahun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>LEWAT MATANG (%)</u></b></p>
                                                            <div id="lewatmatangtahun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>JANGKOS (%)</u></b></p>
                                                            <div id="jangkostahun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>TIDAK STANDAR V-CUT (%)</u></b></p>
                                                            <div id="tidakvcuttahun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px; text-align: center;"><b><u>PENGGUNAAN KARUNG BRONDOLAN (%)</u></b></p>
                                                            <div id="karungbrondolantahun"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="ml-3 mb-3 mr-3">
                                        <button style="width: 100%" class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#showWilayah1" aria-expanded="false" aria-controls="showWilayah1">
                                            Grafik Sidak Mutu Buah Berdasarkan Wilayah
                                        </button>
                                    </p>
                                    <div class="collapse" id="showWilayah1">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>MATANG (%)</u></b></p>
                                                        <div id="matang_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>MENTAH (%)</u></b></p>
                                                        <div id="mentah_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>LEWAT MATANG (%)</u></b></p>
                                                        <div id="lewatmatang_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>JANGKOS (%)</u></b></p>
                                                        <div id="jangkos_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>TIDAK STANDAR V-CUT (%)</u></b></p>
                                                        <div id="tidakvcut_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p style="font-size: 15px; text-align: center;"><b><u>PENGGUNAAN KARUNG BRONDOLAN (%)</u></b></p>
                                                        <div id="karungbrondolan_wils"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane" id="tabGraphs" role="tabpanel">
                                    <div class="d-flex flex-row-reverse justify-content-between align-items-center mr-3">
                                        <button class="btn btn-primary mb-3" id="showDataIns">Show</button>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                {{csrf_field()}}
                                                <select class="form-control" id="regDataTahun">
                                                    @foreach($option_reg as $key => $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                {{csrf_field()}}
                                                <select class="form-control" id="yearData" name="yearData">
                                                    @foreach($list_tahun as $item)
                                                    <option value="{{$item}}">{{$item}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                                        <div class="table-wrapper">
                                            <table class="my-table">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="4" style="background-color: #883c0c;">No</th>
                                                        <th rowspan="4" style="background-color: #883c0c;">Reg.</th>
                                                        <th rowspan="4" style="background-color: #883c0c;">PT.</th>
                                                        <th rowspan="4" rowspan="2" style="background-color: #883c0c;" class="freeze-reg">Est.</th>
                                                        <th rowspan="4" rowspan="2" style="background-color: #883c0c;" class="freeze-afd">Afd.</th>
                                                        <th rowspan="4" rowspan="2" style="background-color: #883c0c;">Nama Staff</th>
                                                        <th colspan="27" style="background-color: #ffc404;">Mutu Buah</th>
                                                        <th rowspan="4" style="background-color: #a8a4a4;" rowspan="2">AlL Skor.</th>
                                                        <th rowspan="4" style="background-color: #a8a4a4;" rowspan="2">Katagori</th>
                                                    </tr>
                                                    <tr>
                                                        <th rowspan="3" style="background-color: #ffc404; white-space: nowrap;">Total Janjang Sample</th>

                                                        <th colspan="7" style="background-color: #ffc404;">Mentah</th>
                                                        <th colspan="3" rowspan="2" style="background-color: #ffc404;">Matang</th>
                                                        <th colspan="3" rowspan="2" style="background-color: #ffc404;">Lewat Matang (O)</th>
                                                        <th colspan="3" rowspan="2" style="background-color: #ffc404;">Janjang Kosong (E)</th>
                                                        <th colspan="3" rowspan="2" style="background-color: #ffc404;">Tangkai Panjang (TP)</th>
                                                        <th colspan="2" rowspan="2" style="background-color: #ffc404;">Abnormal</th>
                                                        <th colspan="2" rowspan="2" style="background-color: #ffc404;">Rat Damage</th>
                                                        <th colspan="3" rowspan="2" style="background-color: #ffc404;">Penggunaan Karung Brondolan</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="background-color: #ffc404;">Tanpa Brondol</th>
                                                        <th colspan="2" style="background-color: #ffc404;">Kurang Brondol</th>
                                                        <th colspan="3" style="background-color: #ffc404;">Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Total</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Total</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Total</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Total</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Total</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Jjg</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">TPH</th>
                                                        <th style="background-color: #ffc404;">%</th>
                                                        <th style="background-color: #ffc404;">Skor</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="data_tahunTab">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div class="tab-pane" id="tabFinding" role="tabpanel">
                                    <div class="d-flex flex-column flex-md-row justify-content-md-end">
                                        <div class="mb-3 mb-md-0 mr-md-2">
                                            <button class="btn btn-primary" id="showFindingYear">Show</button>
                                        </div>
                                        <div class="mb-3 mb-md-0 mr-md-2">
                                            {{csrf_field()}}
                                            <select class="form-control" id="regFindingYear">
                                                @foreach($option_reg as $key => $item)
                                                <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mr-md-2">
                                            {{csrf_field()}}
                                            <select class="form-control" id="yearFinding" name="yearFinding">
                                                @foreach($list_tahun as $item)
                                                <option value="{{$item}}">{{$item}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                                        <p style="text-align: center;">MAIN ISSUE FOTO TEMUAN SIDAK PEMERIKSAAN MUTU BUAH DI TPH
                                        </p>
                                    </div>
                                    <div class="ml-4 mr-4">
                                        <div class="row text-center">
                                            <table class="table table-bordered" style="font-size: 13px">
                                                <thead bgcolor="gainsboro">
                                                    <tr>
                                                        <thclass="align-middle">ESTATE</thclass=>
                                                            <th colspan="5">Temuan Pemeriksaan Panen</th>
                                                            <th rowspan="3" class="align-middle">Foto Temuan</th>
                                                            <!-- <th rowspan="3" class="align-middle">Visit 2</th>
                                                        <th rowspan="3" class="align-middle">Visit 3</th> -->
                                                    </tr>
                                                    <tr>
                                                        <th colspan="5" class="align-middle">Jumlah</th>

                                                    </tr>

                                                </thead>
                                                <tbody id="bodyFind">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- bagian data -->

                    <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>DATA</b></h5>
                        </div>
                        <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                            <div class="row w-100">
                                <div class="col-md-2 offset-md-8">
                                    {{csrf_field()}}
                                    <select class="form-control" id="regional_data">
                                        @foreach($option_reg as $key => $item)
                                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    {{csrf_field()}}
                                    <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputDateMonth" id="inputDateMonth">
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3" style="float: right" id="btnShoWeekdata">Show</button>
                        </div>

                        <style>
                            .table-wrapper {
                                overflow-x: auto;
                                overflow-y: auto;
                                max-height: 600px;
                            }

                            .my-table {
                                width: 100%;
                                font-size: 0.8rem;
                                border-collapse: collapse;
                            }

                            .my-table th,
                            .my-table td {
                                padding: 5px;
                                text-align: center;
                                border: 1px solid #ccc;
                            }

                            .my-table thead {
                                background-color: #f2f2f2;
                            }



                            .my-table tbody tr:nth-child(even) {
                                background-color: #f8f8f8;
                            }

                            .my-table tbody tr:hover {
                                background-color: #eaeaea;
                            }


                            .center {
                                display: flex;
                                justify-content: center;
                            }

                            .my-table thead th.sticky {
                                position: -webkit-sticky;
                                position: sticky;
                                top: 0;
                                z-index: 10;
                                background-color: inherit;
                            }

                            .my-table thead th.sticky-sub {
                                position: -webkit-sticky;
                                position: sticky;
                                top: 30px;
                                /* Adjust this value based on the height of the first row in the header */
                                z-index: 10;
                                background-color: inherit;
                            }

                            .my-table thead th.sticky-third-row {
                                position: -webkit-sticky;
                                position: sticky;
                                top: 90px;
                                /* Adjust this value based on the total height of the first two rows in the header */
                                z-index: 10;
                                background-color: inherit;
                            }

                            .my-table thead th.sticky-second-row {
                                position: -webkit-sticky;
                                position: sticky;
                                top: 60px;
                                z-index: 10;
                                background-color: inherit;
                            }

                            .my-table tbody td.sticky-cell {
                                position: -webkit-sticky;
                                position: sticky;
                                z-index: 5;
                                background-color: white;
                            }
                        </style>

                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <div class="table-wrapper">
                                <table class="my-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="4" class="sticky" style="background-color: #883c0c;">No</th>

                                            <th rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Est.</th>
                                            <th rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Afd.</th>
                                            <th rowspan="4" class="sticky" rowspan="2" style="background-color: #883c0c;">Nama Staff</th>
                                            <th colspan="27" class="sticky" style="background-color: #ffc404;">Mutu Buah</th>
                                            <th rowspan="4" class="sticky" style="background-color: #a8a4a4;" rowspan="2">AlL Skor.</th>
                                            <th rowspan="4" class="sticky" style="background-color: #a8a4a4;" rowspan="2">Katagori</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="3" class="sticky-sub" style="background-color: #ffc404; white-space: nowrap;">Total Janjang Sample</th>

                                            <th colspan="7" class="sticky-sub" style="background-color: #ffc404;">Mentah</th>
                                            <th colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Matang</th>
                                            <th colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Lewat Matang (O)</th>
                                            <th colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Janjang Kosong (E)</th>
                                            <th colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;"> Tidak Standar Vcut</th>
                                            <th colspan="2" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Abnormal</th>
                                            <th colspan="2" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Rat Damage</th>
                                            <th colspan="3" class="sticky-sub" rowspan="2" style="background-color: #ffc404;">Penggunaan Karung Brondolan</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="sticky-second-row" style="background-color: #ffc404;">Tanpa Brondol</th>
                                            <th colspan="2" class="sticky-second-row" style="background-color: #ffc404;">Kurang Brondol</th>
                                            <th colspan="3" class="sticky-second-row" style="background-color: #ffc404;">Total</th>
                                        </tr>

                                        <tr>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Total</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Jjg</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">TPH</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">%</th>
                                            <th class="sticky-third-row" style="background-color: #ffc404;">Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="data_weekTab2">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class=" tab-pane fade" id="nav-sbi" role="tabpanel" aria-labelledby="nav-sbi-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>REKAPITULASI RANKING NILAI SIDAK PEMERIKSAAN MUTU BUAH
                                </b></h5>
                        </div>
                        <div class="content">
                            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                                <div class="row w-100">
                                    <div class="col-md-2 offset-md-8">
                                        {{csrf_field()}}
                                        <select class="form-control" id="reg_sbiThun">
                                            @foreach($option_reg as $key => $item)
                                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <select class="form-control" id="sbi_tahun" name="sbi_tahun">
                                            @foreach($list_tahun as $item)
                                            <option value="{{$item}}">{{$item}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="show_sbithn">Show</button>
                            </div>
                            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 ">
                                <button id="sort-est-btnSBI">Sort by Afd</button>
                                <button id="sort-rank-btnSBI">Sort by Rank</button>
                            </div>

                            <div id="tablesContainer">
                                <div class="tabContainer">
                                    <div class="ml-3 mr-3">
                                        <div class="row text-center">
                                            <div class="col-12 col-md-6 col-lg-3" id="Tabss1">
                                                <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                    <thead>
                                                        <tr bgcolor="fffc04">
                                                            <th colspan="5" id="theadsx1" style="text-align:center">WILAYAH I</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th rowspan="2">KEBUN</th>
                                                            <th rowspan="2">AFD</th>
                                                            <th rowspan="2">Nama</th>
                                                            <th colspan="2">Todate</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th>Score</th>
                                                            <th>Rank</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tahun1">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" id="Tabss2">
                                                <table class=" table table-bordered" style="font-size: 13px" id="table1">
                                                    <thead>
                                                        <tr bgcolor="fffc04">
                                                            <th colspan="5" id="theadsx2" style="text-align:center">WILAYAH II</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th rowspan="2">KEBUN</th>
                                                            <th rowspan="2">AFD</th>
                                                            <th rowspan="2">Nama</th>
                                                            <th colspan="2">Todate</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th>Score</th>
                                                            <th>Rank</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tahun2">

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" id="Tabss3">
                                                <table class="table table-bordered" style="font-size: 13px" id="Reg3">
                                                    <thead>
                                                        <tr bgcolor="fffc04">
                                                            <th colspan="5" id="theadsx3" style="text-align:center">WILAYAH III</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th rowspan="2">KEBUN</th>
                                                            <th rowspan="2">AFD</th>
                                                            <th rowspan="2">Nama</th>
                                                            <th colspan="2">Todate</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th>Score</th>
                                                            <th>Rank</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tahun3">

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-12 col-md-6 col-lg-3" id="Tabss4">
                                                <table class="table table-bordered" style="font-size: 13px" id="plasmaID">
                                                    <thead>
                                                        <tr bgcolor="fffc04">
                                                            <th colspan="5" id="theadsx4" style="text-align:center">Plasma</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th rowspan="2">KEBUN</th>
                                                            <th rowspan="2">AFD</th>
                                                            <th rowspan="2">Nama</th>
                                                            <th colspan="2">Todate</th>
                                                        </tr>
                                                        <tr bgcolor="2044a4" style="color: white">
                                                            <th>Score</th>
                                                            <th>Rank</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tahun4">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead id="tahunreg">
                                        <tr>
                                            <th colspan="1">REG-I</th>
                                            <th colspan="1">RH-1</th>
                                            <th colspan="1">Akhmad Faisyal</th>
                                            <th colspan="8"></th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                            <style>
                                /* CSS for mobile view */
                                @media (max-width: 767.98px) {
                                    .mobile-view {
                                        display: flex;
                                        flex-wrap: nowrap;
                                        justify-content: flex-end;
                                    }

                                    .mobile-view .form-container {
                                        flex: 1;
                                        max-width: calc(100% - 90px);
                                    }

                                    .mobile-view .form-control {
                                        width: 100%;
                                        box-sizing: border-box;
                                        margin-left: 10px;
                                    }

                                    .mobile-view .btn {
                                        width: 80px;
                                        margin-left: 10px;
                                    }
                                }
                            </style>

                            <div class="d-flex flex-row-reverse mr-2 mobile-view">
                                <button class="btn btn-primary mb-3 ml-3" id="sbiGraphYear">Show</button>
                                <div class="form-container">
                                    {{ csrf_field() }}
                                    <select class="form-control" name="estSidakYear" id="estSidakYear"></select>
                                    <input type="hidden" id="hiddenInput" name="hiddenInput">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>MATANG (%)</u></b></p>
                                            <div id="matang_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>MENTAH (%)</u></b></p>
                                            <div id="mentah_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>LEWAT MATANG (%)</u></b></p>
                                            <div id="lewatmatang_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>JANGKOS (%)</u></b></p>
                                            <div id="jangkos_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>TIDAK STANDAR V-CUT (%)</u></b></p>
                                            <div id="tidakvcut_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <p style="font-size: 15px; text-align: center;"><b><u>PENGGUNAAN KARUNG BRONDOLAN (%)</u></b></p>
                                            <div id="karungbrondolan_tahun"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- <a class="nav-item nav-link" id="nav-sbi-tab" data-toggle="tab" href="#nav-sbi" role="tab" aria-controls="nav-sbi" aria-selected="false">SBI</a> -->
                    </div>

                    <div class=" tab-pane fade" id="nav-issue" role="tabpanel" aria-labelledby="nav-issue-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>QC PANEN</b></h5>
                        </div>

                        <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                            <div class="row w-100">
                                <div class="col-md-2 offset-md-8">
                                    {{csrf_field()}}
                                    <select class="form-control" id="regFind">
                                        @foreach($option_reg as $key => $item)
                                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    {{ csrf_field() }}
                                    <input class="form-control" value="{{ date('Y-m') }}" type="month" name="tgl" id="dateFind">
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3 ml-3" id="showFinding">Show</button>
                        </div>

                        <div class="ml-4 mr-4">
                            <div class="row text-center">
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="font-size: 13px">
                                        <thead bgcolor="gainsboro">
                                            <tr>
                                                <th class="align-middle" style="width: 30%;">ESTATE</th>
                                                <th>Jumlah Temuan Pemeriksaan Panen</th>
                                                <th class="align-middle" style="width: 30%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyIssue">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
@include('layout/footer')
<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<script>
    // untuk buat table data bisa d scroll dengan mouse
    document.addEventListener("DOMContentLoaded", function() {
        const tableWrapper = document.querySelector(".table-wrapper");
        let isMouseDown = false;
        let startX, scrollLeft;

        tableWrapper.addEventListener("mousedown", (e) => {
            isMouseDown = true;
            startX = e.pageX - tableWrapper.offsetLeft;
            scrollLeft = tableWrapper.scrollLeft;
            tableWrapper.style.cursor = "grabbing";
        });

        tableWrapper.addEventListener("mouseleave", () => {
            isMouseDown = false;
            tableWrapper.style.cursor = "default";
        });

        tableWrapper.addEventListener("mouseup", () => {
            isMouseDown = false;
            tableWrapper.style.cursor = "default";
        });

        tableWrapper.addEventListener("mousemove", (e) => {
            if (!isMouseDown) return;
            e.preventDefault();
            const x = e.pageX - tableWrapper.offsetLeft;
            const walk = (x - startX) * 2;
            tableWrapper.scrollLeft = scrollLeft - walk;
        });
    });

    ////untuk mode single and full mode


    var currentMode = 'all';
    var lokasiKerja = "{{ session('lok') }}";
    var isTableHeaderModified = false;
    $(document).ready(function() {

        if (lokasiKerja == 'Regional II' && !isTableHeaderModified) {
            $('#regionalPanen').val('2');
            $('#regionalDataweek').val('2');
            $('#regionalData').val('2');
            $('#regDataIns').val('2');
            $('#regFind').val('2');
            $('#regGrafik').val('2');
            $('#reg_sbiThun').val('2');
            $('#regional_data').val('2');

            const thElement1 = document.getElementById('thead1');
            const thElement2 = document.getElementById('thead2');
            const thElement3 = document.getElementById('thead3');
            const thElement4 = document.getElementById('theadx3');
            const thElement1x = document.getElementById('theadsx1');
            const thElement2x = document.getElementById('theadsx2');
            const thElement3x = document.getElementById('theadsx3');
            const thElement4x = document.getElementById('theadsx4');
            const thElement1xx = document.getElementById('theads1');
            const thElement2xx = document.getElementById('theads2');
            const thElement3xx = document.getElementById('theads3');
            const thElement4xx = document.getElementById('theads4');
            thElement1.textContent = 'WILAYAH IV';
            thElement2.textContent = 'WILAYAH V';
            thElement3.textContent = 'WILAYAH VI';
            thElement4.textContent = 'PLASMA II';
            thElement1x.textContent = 'WILAYAH IV';
            thElement2x.textContent = 'WILAYAH V';
            thElement3x.textContent = 'WILAYAH VI';
            thElement4x.textContent = 'PLASMA II';
            thElement1xx.textContent = 'WILAYAH IV';
            thElement2xx.textContent = 'WILAYAH V';
            thElement3xx.textContent = 'WILAYAH VI';
            thElement4xx.textContent = 'PLASMA II';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement3.classList.add("text-center");
            thElement4.classList.add("text-center");
            thElement1x.classList.add("text-center");
            thElement2x.classList.add("text-center");
            thElement3x.classList.add("text-center");
            thElement4x.classList.add("text-center");
            thElement1xx.classList.add("text-center");
            thElement2xx.classList.add("text-center");
            thElement3xx.classList.add("text-center");
            thElement4xx.classList.add("text-center");


        } else if ((lokasiKerja == 'Regional III' || lokasiKerja == 'Regional 3') && !isTableHeaderModified) {
            $('#regionalPanen').val('3');
            $('#regionalDataweek').val('3');
            $('#regionalData').val('3');
            $('#regDataIns').val('3');
            $('#regFind').val('3');
            $('#regGrafik').val('3');
            $('#reg_sbiThun').val('3');
            $('#regional_data').val('3');

            const thElement1 = document.getElementById('thead1');
            const thElement2 = document.getElementById('thead2');
            const thElement3 = document.getElementById('thead3');
            const thElement4 = document.getElementById('theadx3');
            const thElement1x = document.getElementById('theadsx1');
            const thElement2x = document.getElementById('theadsx2');
            const thElement3x = document.getElementById('theadsx3');
            const thElement4x = document.getElementById('theadsx4');
            const thElement1xx = document.getElementById('theads1');
            const thElement2xx = document.getElementById('theads2');
            const thElement3xx = document.getElementById('theads3');
            const thElement4xx = document.getElementById('theads4');
            thElement1.textContent = 'WILAYAH IV';
            thElement2.textContent = 'WILAYAH V';
            thElement3.textContent = 'WILAYAH VI';
            thElement4.textContent = 'PLASMA II';
            thElement1x.textContent = 'WILAYAH IV';
            thElement2x.textContent = 'WILAYAH V';
            thElement3x.textContent = 'WILAYAH VI';
            thElement4x.textContent = 'PLASMA II';
            thElement1xx.textContent = 'WILAYAH IV';
            thElement2xx.textContent = 'WILAYAH V';
            thElement3xx.textContent = 'WILAYAH VI';
            thElement4xx.textContent = 'PLASMA II';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement3.classList.add("text-center");
            thElement4.classList.add("text-center");
            thElement1x.classList.add("text-center");
            thElement2x.classList.add("text-center");
            thElement3x.classList.add("text-center");
            thElement4x.classList.add("text-center");
            thElement1xx.classList.add("text-center");
            thElement2xx.classList.add("text-center");
            thElement3xx.classList.add("text-center");
            thElement4xx.classList.add("text-center");

        } else if ((lokasiKerja == 'Regional IV' || lokasiKerja == 'Regional 4') && !isTableHeaderModified) {
            $('#regionalPanen').val('4');
            $('#regionalDataweek').val('4');
            $('#regionalData').val('4');
            $('#regDataIns').val('4');
            $('#regFind').val('4');
            $('#regGrafik').val('4');
            $('#reg_sbiThun').val('4');
            $('#regional_data').val('4');


            const nons = document.getElementById("Tab1");
            const nonx = document.getElementById("Tab2");
            const llon = document.getElementById("Tab3");
            const non = document.getElementById("Tab4");
            const tahun1 = document.getElementById("Tabsx1");
            const tahun2 = document.getElementById("Tabsx2");
            const tahun3 = document.getElementById("Tabsx3");
            const tahun4 = document.getElementById("Tabsx4");
            const sbi1 = document.getElementById("Tabss1");
            const sbi2 = document.getElementById("Tabss2");
            const sbi3 = document.getElementById("Tabss3");
            const sbi4 = document.getElementById("Tabss4");


            function resetClassList(element) {
                element.classList.remove("col-md-6", "col-lg-3", "col-lg-4", "col-lg-6");
                element.classList.add("col-md-6");
            }

            llon.style.display = "none";
            non.style.display = "none";
            resetClassList(llon);
            resetClassList(non);
            nons.classList.add("col-lg-6");
            nonx.classList.add("col-lg-6");

            tahun3.style.display = "none";
            tahun4.style.display = "none";
            resetClassList(tahun3);
            resetClassList(tahun4);
            tahun1.classList.add("col-lg-6");
            tahun2.classList.add("col-lg-6");


            sbi3.style.display = "none";
            sbi4.style.display = "none";
            resetClassList(sbi3);
            resetClassList(sbi4);
            sbi1.classList.add("col-lg-6");
            sbi2.classList.add("col-lg-6");

            const thElement1 = document.getElementById('thead1');
            const thElement2 = document.getElementById('thead2');
            const thElement1x = document.getElementById('theads1');
            const thElement2x = document.getElementById('theads2');

            thElement1.textContent = 'WILAYAH Inti';
            thElement2.textContent = 'WILAYAH Plasma';
            thElement1x.textContent = 'WILAYAH Inti';
            thElement2x.textContent = 'WILAYAH Plasma';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement1x.classList.add("text-center");
            thElement2x.classList.add("text-center");
        }

        isTableHeaderModified = true;
        getweek()
        dashboard_tahun()
        dashboardData_tahun()
        dashboardFindingYear()
        getweekData()
        sbi_tahun()
        getFindData()

        $('#data_weekTab2 tbody tr').each(function() {
            var secondCell = $(this).find('td:eq(1)');
            if (secondCell.text().trim() === 'KTE4') {
                secondCell.text('KTE');
            }
        });
    });

    $("#showFinding").click(function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        getFindData()
    });

    const c = document.getElementById('btnShow');
    const o = document.getElementById('regionalPanen');
    const s = document.getElementById("Tab1");
    const m = document.getElementById("Tab2");
    const l = document.getElementById("Tab3");
    const n = document.getElementById("Tab4");
    const thElement1 = document.getElementById('thead1');
    const thElement2 = document.getElementById('thead2');
    const thElement3 = document.getElementById('thead3');
    const thElement4 = document.getElementById('theadx3');

    function resetClassList(element) {
        element.classList.remove("col-md-6", "col-lg-3", "col-lg-4", "col-lg-6");
        element.classList.add("col-md-6");
    }

    c.addEventListener('click', function() {
        const c = o.value;
        if (c === '1') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            n.style.display = "";

            resetClassList(s);
            resetClassList(m);
            resetClassList(l);
            resetClassList(n);

            thElement1.textContent = 'WILAYAH I';
            thElement2.textContent = 'WILAYAH II';
            thElement3.textContent = 'WILAYAH III';
            thElement4.textContent = 'PLASMA I';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement3.classList.add("text-center");
            thElement4.classList.add("text-center");

            s.classList.add("col-lg-3");
            m.classList.add("col-lg-3");
            l.classList.add("col-lg-3");
            n.classList.add("col-lg-3");
        } else if (c === '2') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            n.style.display = "";

            resetClassList(s);
            resetClassList(m);
            resetClassList(l);
            resetClassList(n);


            thElement1.textContent = 'WILAYAH IV';
            thElement2.textContent = 'WILAYAH V';
            thElement3.textContent = 'WILAYAH VI';
            thElement4.textContent = 'PLASMA II';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement3.classList.add("text-center");
            thElement4.classList.add("text-center");


            s.classList.add("col-lg-3");
            m.classList.add("col-lg-3");
            l.classList.add("col-lg-3");
            n.classList.add("col-lg-3");
        } else if (c === '3') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            n.style.display = "none";

            resetClassList(s);
            resetClassList(m);
            resetClassList(l);

            thElement1.textContent = 'WILAYAH VII';
            thElement2.textContent = 'WILAYAH VIII';
            thElement3.textContent = 'PLASMA III';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");
            thElement3.classList.add("text-center");

            s.classList.add("col-lg-4");
            m.classList.add("col-lg-4");
            l.classList.add("col-lg-4");
        } else if (c === '4') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "none";
            n.style.display = "none";

            resetClassList(s);
            resetClassList(m);


            thElement1.textContent = 'WILAYAH Inti';
            thElement2.textContent = 'WILAYAH Plasma';

            thElement1.classList.add("text-center");
            thElement2.classList.add("text-center");


            s.classList.add("col-lg-6");
            m.classList.add("col-lg-6");

        }
    });

    const cs = document.getElementById('show_sbithn');
    const os = document.getElementById('reg_sbiThun');
    const ss = document.getElementById("Tabss1");
    const ms = document.getElementById("Tabss2");
    const ls = document.getElementById("Tabss3");
    const ns = document.getElementById("Tabss4");
    const thElement1s = document.getElementById('theadsx1');
    const thElement2s = document.getElementById('theadsx2');
    const thElement3s = document.getElementById('theadsx3');
    const thElement4s = document.getElementById('theadsx4');


    cs.addEventListener('click', function() {
        const cs = os.value;
        if (cs === '1') {
            ss.style.display = "";
            ms.style.display = "";
            ls.style.display = "";
            ns.style.display = "";

            resetClassList(ss);
            resetClassList(ms);
            resetClassList(ls);
            resetClassList(ns);

            thElement1s.textContent = 'WILAYAH I';
            thElement2s.textContent = 'WILAYAH II';
            thElement3s.textContent = 'WILAYAH III';
            thElement4s.textContent = 'Plasma1';

            thElement1s.classList.add("text-center");
            thElement2s.classList.add("text-center");
            thElement3s.classList.add("text-center");
            thElement4s.classList.add("text-center");

            ss.classList.add("col-lg-3");
            ms.classList.add("col-lg-3");
            ls.classList.add("col-lg-3");
            ns.classList.add("col-lg-3");
        } else if (cs === '2') {
            ss.style.display = "";
            ms.style.display = "";
            ls.style.display = "";
            ns.style.display = "";

            resetClassList(ss);
            resetClassList(ms);
            resetClassList(ls);

            thElement1s.textContent = 'WILAYAH IV';
            thElement2s.textContent = 'WILAYAH V';
            thElement3s.textContent = 'WILAYAH VI';
            thElement4s.textContent = 'Plasma2';

            thElement1s.classList.add("text-center");
            thElement2s.classList.add("text-center");
            thElement3s.classList.add("text-center");
            thElement4s.classList.add("text-center");

            ss.classList.add("col-lg-3");
            ms.classList.add("col-lg-3");
            ls.classList.add("col-lg-3");
            ns.classList.add("col-lg-3");
        } else if (cs === '3') {
            ss.style.display = "";
            ms.style.display = "";
            ls.style.display = "";
            ns.style.display = "none";

            resetClassList(ss);
            resetClassList(ms);
            resetClassList(ls);

            thElement1s.textContent = 'WILAYAH VII';
            thElement2s.textContent = 'WILAYAH VIII';
            thElement3s.textContent = 'PLASMA III';

            thElement1s.classList.add("text-center");
            thElement2s.classList.add("text-center");
            thElement3s.classList.add("text-center");

            ss.classList.add("col-lg-4");
            ms.classList.add("col-lg-4");
            ls.classList.add("col-lg-4");
        } else if (cs === '4') {
            ss.style.display = "";
            ms.style.display = "";
            ls.style.display = "none";
            ns.style.display = "none";

            resetClassList(ss);
            resetClassList(ms);


            thElement1s.textContent = 'WILAYAH Inti';
            thElement2s.textContent = 'WILAYAH Plasma';

            thElement1s.classList.add("text-center");
            thElement2s.classList.add("text-center");


            ss.classList.add("col-lg-6");
            ms.classList.add("col-lg-6");

        }
    });

    const ck = document.getElementById('showTahung');
    const ok = document.getElementById('regionalData');
    const sk = document.getElementById("Tabs1");
    const mk = document.getElementById("Tabs2");
    const lk = document.getElementById("Tabs3");
    const nk = document.getElementById("Tabs4");
    const thElement1k = document.getElementById('theads1');
    const thElement2k = document.getElementById('theads2');
    const thElement3k = document.getElementById('theads3');
    const thElement4k = document.getElementById('theads4');

    function resetClassList(element) {
        element.classList.remove("col-md-6", "col-lg-3", "col-lg-4", "col-lg-6");
        element.classList.add("col-md-6");
    }

    ck.addEventListener('click', function() {
        const ck = ok.value;
        if (ck === '1') {
            sk.style.display = "";
            mk.style.display = "";
            lk.style.display = "";
            nk.style.display = "";

            resetClassList(sk);
            resetClassList(mk);
            resetClassList(lk);
            resetClassList(nk);

            thElement1k.textContent = 'WILAYAH I';
            thElement2k.textContent = 'WILAYAH II';
            thElement3k.textContent = 'WILAYAH III';
            thElement4k.textContent = 'Plasma1';

            thElement1k.classList.add("text-center");
            thElement2k.classList.add("text-center");
            thElement3k.classList.add("text-center");
            thElement4k.classList.add("text-center");

            sk.classList.add("col-lg-3");
            mk.classList.add("col-lg-3");
            lk.classList.add("col-lg-3");
            nk.classList.add("col-lg-3");
        } else if (ck === '2') {
            sk.style.display = "";
            mk.style.display = "";
            lk.style.display = "";
            nk.style.display = "";

            resetClassList(sk);
            resetClassList(mk);
            resetClassList(lk);
            resetClassList(nk);

            thElement1k.textContent = 'WILAYAH IV';
            thElement2k.textContent = 'WILAYAH V';
            thElement3k.textContent = 'WILAYAH VI';
            thElement4k.textContent = 'PLASMA 2';

            thElement1k.classList.add("text-center");
            thElement2k.classList.add("text-center");
            thElement3k.classList.add("text-center");
            thElement4k.classList.add("text-center");


            sk.classList.add("col-lg-3");
            mk.classList.add("col-lg-3");
            lk.classList.add("col-lg-3");
            nk.classList.add("col-lg-3");
        } else if (ck === '3') {
            sk.style.display = "";
            mk.style.display = "";
            lk.style.display = "";
            nk.style.display = "none";

            resetClassList(sk);
            resetClassList(mk);
            resetClassList(lk);

            thElement1k.textContent = 'WILAYAH VII';
            thElement2k.textContent = 'WILAYAH VIII';
            thElement3k.textContent = 'PLASMA III';

            thElement1k.classList.add("text-center");
            thElement2k.classList.add("text-center");
            thElement3k.classList.add("text-center");

            sk.classList.add("col-lg-4");
            mk.classList.add("col-lg-4");
            lk.classList.add("col-lg-4");
        } else if (ck === '4') {
            sk.style.display = "";
            mk.style.display = "";
            lk.style.display = "none";
            nk.style.display = "none";

            resetClassList(sk);
            resetClassList(mk);


            thElement1k.textContent = 'WILAYAH Inti';
            thElement2k.textContent = 'WILAYAH Plasma';

            thElement1k.classList.add("text-center");
            thElement2k.classList.add("text-center");


            sk.classList.add("col-lg-6");
            mk.classList.add("col-lg-6");

        }
    });
    //tampilakn filter perweek
    document.getElementById('btnShow').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        getweek();
    }

    var options = {

        series: [{
            name: '',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }],
        chart: {
            background: '#ffffff',
            height: 350,
            type: 'bar'
        },
        plotOptions: {
            bar: {
                distributed: true
            }
        },

        colors: [
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#00ffff'
        ],

        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            type: '',
            // categories: estate
            categories: ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-']
        }
    };

    var options_wil = {

        series: [{
            name: '',
            data: [0, 0, 0]
        }],
        chart: {
            background: '#ffffff',
            height: 350,
            type: 'bar'
        },
        plotOptions: {
            bar: {
                distributed: true
            }
        },

        colors: [
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#00ffff'
        ],

        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            type: '',
            // categories: estate
            categories: [1, 2, 3]
        }
    };




    var chart = new ApexCharts(document.querySelector("#matang"), options);
    var chartx = new ApexCharts(document.querySelector("#mentah"), options);
    var charts = new ApexCharts(document.querySelector("#lewatmatang"), options);
    var chartc = new ApexCharts(document.querySelector("#jangkos"), options);
    var chartv = new ApexCharts(document.querySelector("#tidakvcut"), options);
    var chartb = new ApexCharts(document.querySelector("#karungbrondolan"), options);

    var chart_wil = new ApexCharts(document.querySelector("#matang_wil"), options_wil);
    var chartx_wil = new ApexCharts(document.querySelector("#mentah_wil"), options_wil);
    var charts_wil = new ApexCharts(document.querySelector("#lewatmatang_wil"), options_wil);
    var chartc_wil = new ApexCharts(document.querySelector("#jangkos_wil"), options_wil);
    var chartv_wil = new ApexCharts(document.querySelector("#tidakvcut_wil"), options_wil);
    var chartb_wil = new ApexCharts(document.querySelector("#karungbrondolan_wil"), options_wil);

    chart.render();
    chartx.render();
    charts.render();
    chartc.render();
    chartv.render();
    chartb.render();

    chart_wil.render();
    chartx_wil.render();
    charts_wil.render();
    chartc_wil.render();
    chartv_wil.render();
    chartb_wil.render();

    function getweek() {
        const week1 = $("#week1");
        const week2 = $("#week2");
        const week3 = $("#week3");
        const plasma1 = $("#plasma1");
        const theadreg = $("#theadreg");

        if (week1.length) week1.empty();
        if (week2.length) week2.empty();
        if (week3.length) week3.empty();
        if (plasma1.length) plasma1.empty();
        if (theadreg.length) theadreg.empty();

        var reg = '';

        var bulan = '';
        var reg = document.getElementById('regionalPanen').value;

        var bulan = document.getElementById('inputbulan').value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('getWeek') }}",
            method: "GET",
            data: {
                reg: reg,
                bulan: bulan,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {

                Swal.close();
                var parseResult = JSON.parse(result)
                var region = Object.entries(parseResult['listregion'])

                var mutu_buah = Object.entries(parseResult['mutu_buah'])
                var mutubuah_est = Object.entries(parseResult['mutubuah_est'])
                var mutuBuah_wil = Object.entries(parseResult['mutuBuah_wil'])
                var regIonal = Object.entries(parseResult['regional'])
                var regionaltab = Object.entries(parseResult['regionaltab'])
                var queryAsisten = Object.entries(parseResult['queryAsisten'])

                var chart_matang = Object.entries(parseResult['chart_matang'])
                var chart_mentah = Object.entries(parseResult['chart_mentah'])
                var chart_lewatmatang = Object.entries(parseResult['chart_lewatmatang'])
                var chart_janjangkosong = Object.entries(parseResult['chart_janjangkosong'])
                var chart_vcut = Object.entries(parseResult['chart_vcut'])
                var chart_karung = Object.entries(parseResult['chart_karung'])

                var chart_matangwil = Object.entries(parseResult['chart_matangwil'])
                var chart_mentahwil = Object.entries(parseResult['chart_mentahwil'])
                var chart_lewatmatangwil = Object.entries(parseResult['chart_lewatmatangwil'])
                var chart_janjangkosongwil = Object.entries(parseResult['chart_janjangkosongwil'])
                var chart_vcutwil = Object.entries(parseResult['chart_vcutwil'])
                var chart_karungwil = Object.entries(parseResult['chart_karungwil'])
                var optionREg = Object.entries(parseResult['optionREg'])
                // console.log(chart_matang);
                // console.log(mutu_buah);
                var matang_Wil = '['
                if (chart_matangwil.length > 0) {
                    chart_matangwil.forEach(element => {
                        matang_Wil += '"' + element[1] + '",'
                    });
                    matang_Wil = matang_Wil.substring(0, matang_Wil.length - 1);
                }
                matang_Wil += ']'
                var mentah_wil = '['
                if (chart_mentahwil.length > 0) {
                    chart_mentahwil.forEach(element => {
                        mentah_wil += '"' + element[1] + '",'
                    });
                    mentah_wil = mentah_wil.substring(0, mentah_wil.length - 1);
                }
                mentah_wil += ']'
                var lewatmatangs_wil = '['
                if (chart_lewatmatangwil.length > 0) {
                    chart_lewatmatangwil.forEach(element => {
                        lewatmatangs_wil += '"' + element[1] + '",'
                    });
                    lewatmatangs_wil = lewatmatangs_wil.substring(0, lewatmatangs_wil.length - 1);
                }
                lewatmatangs_wil += ']'

                var jjgkosongs_wil = '['
                if (chart_janjangkosongwil.length > 0) {
                    chart_janjangkosongwil.forEach(element => {
                        jjgkosongs_wil += '"' + element[1] + '",'
                    });
                    jjgkosongs_wil = jjgkosongs_wil.substring(0, jjgkosongs_wil.length - 1);
                }
                jjgkosongs_wil += ']'

                var vcuts_wil = '['
                if (chart_vcutwil.length > 0) {
                    chart_vcutwil.forEach(element => {
                        vcuts_wil += '"' + element[1] + '",'
                    });
                    vcuts_wil = vcuts_wil.substring(0, vcuts_wil.length - 1);
                }
                vcuts_wil += ']'
                var karungs_wil = '['
                if (chart_karungwil.length > 0) {
                    chart_karungwil.forEach(element => {
                        karungs_wil += '"' + element[1] + '",'
                    });
                    karungs_wil = karungs_wil.substring(0, karungs_wil.length - 1);
                }
                karungs_wil += ']'

                // console.log(matang_Wil);
                let regInpt = reg;

                var wilayah = '['
                region.forEach(element => {
                    wilayah += '"' + element + '",'
                });
                wilayah = wilayah.substring(0, wilayah.length - 1);
                wilayah += ']'

                // console.log(chart_matang);
                var matang = '['
                if (chart_matang.length > 0) {
                    chart_matang.forEach(element => {
                        matang += '"' + element[1] + '",'
                    });
                    matang = matang.substring(0, matang.length - 1);
                }
                matang += ']'

                var mentah = '['
                if (chart_mentah.length > 0) {
                    chart_mentah.forEach(element => {
                        mentah += '"' + element[1] + '",'
                    });
                    mentah = mentah.substring(0, mentah.length - 1);
                }
                mentah += ']'

                var lewatmatangs = '['
                if (chart_lewatmatang.length > 0) {
                    chart_lewatmatang.forEach(element => {
                        lewatmatangs += '"' + element[1] + '",'
                    });
                    lewatmatangs = lewatmatangs.substring(0, lewatmatangs.length - 1);
                }
                lewatmatangs += ']'

                var jjgkosongs = '['
                if (chart_janjangkosong.length > 0) {
                    chart_janjangkosong.forEach(element => {
                        jjgkosongs += '"' + element[1] + '",'
                    });
                    jjgkosongs = jjgkosongs.substring(0, jjgkosongs.length - 1);
                }
                jjgkosongs += ']'

                var vcuts = '['
                if (chart_vcut.length > 0) {
                    chart_vcut.forEach(element => {
                        vcuts += '"' + element[1] + '",'
                    });
                    vcuts = vcuts.substring(0, vcuts.length - 1);
                }
                vcuts += ']'

                var karungs = '['
                if (chart_karung.length > 0) {
                    chart_karung.forEach(element => {
                        karungs += '"' + element[1] + '",'
                    });
                    karungs = karungs.substring(0, karungs.length - 1);
                }
                karungs += ']'

                var estate = JSON.parse(wilayah)
                var matang_chart = JSON.parse(matang)
                var matang_chart_wil = JSON.parse(matang_Wil)
                var mentah_chart = JSON.parse(mentah)
                var mentah_chart_wil = JSON.parse(mentah_wil)

                var lwtmatang_chart = JSON.parse(lewatmatangs)
                var lwtmatang_chart_wil = JSON.parse(lewatmatangs_wil)
                var janjangksng_chart = JSON.parse(jjgkosongs)
                var janjangksng_chart_wil = JSON.parse(jjgkosongs_wil)
                var vcuts_chart = JSON.parse(vcuts)
                var vcuts_chart_wil = JSON.parse(vcuts_wil)
                var karungs_chart = JSON.parse(karungs)
                var karungs_chart_wil = JSON.parse(karungs_wil)



                const formatEst = estate.map((item) => item.split(',')[1]);
                let plasma1Index = formatEst.indexOf("Plasma1");

                if (plasma1Index !== -1) {
                    formatEst.splice(plasma1Index, 1);
                    formatEst.push("Plasma1");
                }

                // console.log(formatEst);

                let colors = '';


                if (regInpt === '1') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#00ffff'
                    ]

                } else if (regInpt === '2') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#00ffff',
                        '#00ffff'
                    ]


                } else if (regInpt === '3') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                    ]
                } else if (regInpt === '4') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',

                    ]
                }

                // console.log(matang_chart);
                chart.updateSeries([{
                    name: 'matang',
                    data: matang_chart,

                }])
                chart.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////////
                chartx.updateSeries([{
                    name: 'mentah',
                    data: mentah_chart,

                }])
                chartx.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////////
                charts.updateSeries([{
                    name: 'lewat matang',
                    data: lwtmatang_chart,

                }])
                charts.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                /////////
                chartc.updateSeries([{
                    name: 'janjang kosong',
                    data: janjangksng_chart,

                }])
                chartc.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ////////
                chartv.updateSeries([{
                    name: 'vcut ',
                    data: vcuts_chart,

                }])
                chartv.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////
                chartb.updateSeries([{
                    name: 'karung',
                    data: karungs_chart,

                }])
                chartb.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })


                if (regInpt === '1') {
                    wil_est = ['I', 'II', 'III']
                    warna = ['#00FF00',
                        '#FF8D1A',
                        '#00ffff'
                    ]
                } else if (regInpt === '2') {
                    wil_est = ['IV', 'V', 'VI']
                    warna = ['#00FF00',
                        '#FF8D1A',
                        '#00ffff'
                    ]
                } else if (regInpt === '3') {
                    wil_est = ['VII', 'VIII']
                    warna = ['#00FF00',
                        '#00ffff'
                    ]
                } else {
                    wil_est = ['IX', 'X']
                    warna = ['#00FF00',
                        '#00ffff'
                    ]
                }
                chart_wil.updateSeries([{
                    name: 'matang',
                    data: matang_chart_wil,

                }])
                chart_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna
                })
                chartx_wil.updateSeries([{
                    name: 'mentah',
                    data: mentah_chart_wil,

                }])
                chartx_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ///////////
                charts_wil.updateSeries([{
                    name: 'lewat matang',
                    data: lwtmatang_chart_wil,

                }])
                charts_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                /////////
                chartc_wil.updateSeries([{
                    name: 'janjang kosong',
                    data: janjangksng_chart_wil,

                }])
                chartc_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ////////
                chartv_wil.updateSeries([{
                    name: 'vcut ',
                    data: vcuts_chart_wil,

                }])
                chartv_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ///////
                chartb_wil.updateSeries([{
                    name: 'karung',
                    data: karungs_chart_wil,

                }])
                chartb_wil.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })

                //endchart


                function createTableCell(text, customClass = null) {
                    const cell = document.createElement('td');
                    cell.innerText = text;
                    if (customClass) {
                        cell.classList.add(customClass);
                    }
                    return cell;
                }

                function setBackgroundColor(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }

                function bgest(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }



                var arrTbody1 = mutu_buah[0][1];

                var tbody1 = document.getElementById('week1');

                Object.entries(arrTbody1).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] !== undefined ? data['nama_asisten'] : '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody1.appendChild(tr);
                    });
                });
                var arrEst1 = mutubuah_est[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('week1');

                Object.entries(arrEst1).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody1.appendChild(tr);

                });
                if (regInpt === '1') {
                    wil1 = 'WIL-I';
                    wil2 = 'WIL-II';
                    wil3 = 'WIL-III';
                    wil4 = 'Plasma1'
                } else if (regInpt === '2') {
                    wil1 = 'WIL-IV';
                    wil2 = 'WIL-V';
                    wil3 = 'WIL-VI';
                    wil4 = 'Plasma2'
                } else if (regInpt === '3') {
                    wil1 = 'WIL-VII';
                    wil2 = 'WIL-VIII';
                    wil3 = 'Plasma3';
                    wil4 = 'Plasma3'
                } else {
                    wil1 = 'WIL-IX';
                    wil2 = 'WIL-X';
                    wil3 = '-';
                    wil4 = '-'
                }
                var arrEst1 = mutuBuah_wil[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('week1');
                const tr = document.createElement('tr');
                // console.log(estateData);
                let item3 = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil1 && asisten[1].afd === 'GM') {
                        item3 = asisten[1].nama;
                    }
                });

                const dataItems = {
                    item1: wil1,
                    item2: 'GM',
                    item3: item3,
                    item4: arrEst1['All_skor'],
                    item5: arrEst1['rankWil'],
                };


                const rowData = Object.values(dataItems);

                rowData.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tr.appendChild(cell);
                });

                tbody1.appendChild(tr);



                var tab2 = mutu_buah[1][1];
                var tbody2 = document.getElementById('week2');
                // console.log(tab2);
                Object.entries(tab2).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] || '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody2.appendChild(tr);
                    });
                });

                var arrEst2 = mutubuah_est[1][1];
                // console.log(arrEst2);
                var tbody2 = document.getElementById('week2');

                Object.entries(arrEst2).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody2.appendChild(tr);

                });

                var arrWil2 = mutuBuah_wil[1][1];
                // console.log(arrWil2);
                var tbody2 = document.getElementById('week2');
                const tx = document.createElement('tr');
                // console.log(estateData);
                let item3s = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil2 && asisten[1].afd === 'GM') {
                        item3s = asisten[1].nama;
                    }
                });
                const dataItemx = {

                    item1: wil2,
                    item2: 'GM',
                    item3: item3s,
                    item4: arrWil2['All_skor'],
                    item5: arrWil2['rankWil'],
                };

                const rowDatax = Object.values(dataItemx);

                rowDatax.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tx.appendChild(cell);
                });

                tbody2.appendChild(tx);

                var tbody3 = document.getElementById('week3');

                if (mutu_buah[2] !== undefined) {
                    var tab3 = mutu_buah[2][1];

                    if (tab3 !== null && tab3 !== undefined) {
                        Object.entries(tab3).forEach(([estateName, estateData]) => {
                            Object.entries(estateData).forEach(([key2, data], index) => {
                                const tr = document.createElement('tr');

                                const dataItems = {
                                    item1: estateName,
                                    item2: key2,
                                    item3: data['nama_asisten'] || '-',
                                    item4: data['All_skor'],
                                    item5: data['rankAFD'],
                                };

                                const rowData = Object.values(dataItems);

                                rowData.forEach((item, cellIndex) => {
                                    const cell = createTableCell(item, "text-center");

                                    if (cellIndex === 3) {
                                        setBackgroundColor(cell, item);
                                    }

                                    tr.appendChild(cell);
                                });

                                tbody3.appendChild(tr);
                            });
                        });
                    } else {
                        console.log("tab3 is null or undefined");
                    }
                } else {
                    console.log("mutu_buah[2] is undefined");
                }

                var tbody3 = document.getElementById('week3');

                if (mutubuah_est[2] !== undefined) {
                    var arrEst3 = mutubuah_est[2][1];

                    if (arrEst3 !== null && arrEst3 !== undefined) {
                        Object.entries(arrEst3).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody3.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst3 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[2] is undefined");
                }

                var tbody3 = document.getElementById('week3');

                if (mutuBuah_wil[2] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[2][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil3 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const tm = document.createElement('tr');

                        const dataitemc = {
                            item1: wil3,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowDatac = Object.values(dataitemc);

                        rowDatac.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tm.appendChild(cell);
                        });

                        tbody3.appendChild(tm);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[2] is undefined");
                }


                var arrTbody1 = mutu_buah[0]?.[1] || [];


                if (regInpt === '1') {
                    if (mutu_buah[3]) {
                        var tab4 = mutu_buah[3][1];
                    }
                } else if (regInpt === '2') {
                    if (mutu_buah[3]) {
                        var tab4 = mutu_buah[3][1];
                    }
                } else if (regInpt === '3') {
                    if (mutu_buah[3]) {
                        var tab4 = mutu_buah[2][1];
                    }
                }


                // var tab4 = mutu_buah[3][1];
                var tbody4 = document.getElementById('plasma1');

                if (tab4 !== null && tab4 !== undefined) {
                    Object.entries(tab4).forEach(([estateName, estateData]) => {
                        Object.entries(estateData).forEach(([key2, data], index) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: key2,
                                item3: data['nama_asisten'] || '-',
                                item4: data['All_skor'],
                                item5: data['rankAFD'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");

                                if (cellIndex === 3) {
                                    setBackgroundColor(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    });
                } else {
                    console.log("tab4 is null or undefined");
                }

                var tbody4 = document.getElementById('plasma1');

                if (mutubuah_est[3] !== undefined) {
                    var arrEst4 = mutubuah_est[3][1];

                    if (arrEst4 !== null && arrEst4 !== undefined) {
                        Object.entries(arrEst4).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst4 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[3] is undefined");
                }


                var tbody4 = document.getElementById('plasma1');
                const tl = document.createElement('tr');

                if (mutuBuah_wil[3] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[3][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil4 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const dataOm = {
                            item1: wil4,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowOm = Object.values(dataOm);

                        rowOm.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tl.appendChild(cell);
                        });

                        tbody4.appendChild(tl);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[3] is undefined");
                }


                var regionals = regIonal;
                // console.log(regionaltab);
                var headregional = document.getElementById('theadreg');
                const trreg = document.createElement('tr');

                const dataReg = {
                    // item1: regIonal[0] && regIonal[0][1] && regIonal[0][1].regional !== undefined ? regIonal[0][1].regional : '-',
                    item1: regionaltab[0][1]['nama'],
                    // item2: regIonal[0] && regIonal[0][1] && regIonal[0][1].jabatan !== undefined ? regIonal[0][1].jabatan : '-',
                    item2: regionaltab[0][1]['jabatan'],
                    // item3: regIonal[0] && regIonal[0][1] && regIonal[0][1].nama_asisten !== undefined ? regIonal[0][1].nama_asisten : '-',
                    item3: regionaltab[0][1]['nama_rh'],
                    item4: regIonal[0] && regIonal[0][1] && regIonal[0][1].all_skorYear !== undefined ? regIonal[0][1].all_skorYear : '-',
                };

                const rowREG = Object.values(dataReg);
                rowREG.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }
                    trreg.appendChild(cell);
                });
                headregional.appendChild(trreg);


            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }







    //tampilkan pertahun filter table utama
    document.getElementById('showTahung').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        dashboard_tahun()
    }


    var char1 = new ApexCharts(document.querySelector("#matangthun"), options);
    var chart2 = new ApexCharts(document.querySelector("#mentahtahun"), options);
    var chart3 = new ApexCharts(document.querySelector("#lewatmatangtahun"), options);
    var chart4 = new ApexCharts(document.querySelector("#jangkostahun"), options);
    var chart5 = new ApexCharts(document.querySelector("#tidakvcuttahun"), options);
    var chart6 = new ApexCharts(document.querySelector("#karungbrondolantahun"), options);

    var chart_wils = new ApexCharts(document.querySelector("#matang_wils"), options_wil);
    var chartx_wils = new ApexCharts(document.querySelector("#mentah_wils"), options_wil);
    var charts_wils = new ApexCharts(document.querySelector("#lewatmatang_wils"), options_wil);
    var chartc_wils = new ApexCharts(document.querySelector("#jangkos_wils"), options_wil);
    var chartv_wils = new ApexCharts(document.querySelector("#tidakvcut_wils"), options_wil);
    var chartb_wils = new ApexCharts(document.querySelector("#karungbrondolan_wils"), options_wil);

    chart_wils.render();
    chartx_wils.render();
    charts_wils.render();
    chartc_wils.render();
    chartv_wils.render();
    chartb_wils.render();

    char1.render();
    chart2.render();
    chart3.render();
    chart4.render();
    chart5.render();
    chart6.render();




    function dashboard_tahun() {
        $('#weeks1').empty()
        $('#weeks2').empty()
        $('#weeks3').empty()
        $('#plasmas1').empty()
        $('#theadregs').empty()
        var week = ''
        $regData = ''
        var _token = $('input[name="_token"]').val();
        var week = document.getElementById('dateWeek').value
        var regData = document.getElementById('regionalData').value


        $.ajax({
            url: "{{ route('getYear') }}",
            method: "GET",
            data: {
                week,
                regData,
                _token: _token
            },
            success: function(result) {

                Swal.close();
                var parseResult = JSON.parse(result)
                var region = Object.entries(parseResult['listregion'])

                var mutu_buah = Object.entries(parseResult['mutu_buah'])
                // console.log(mutu_buah);
                var mutubuah_est = Object.entries(parseResult['mutubuah_est'])
                var mutuBuah_wil = Object.entries(parseResult['mutuBuah_wil'])
                var regIonal = Object.entries(parseResult['regional'])
                var queryAsisten = Object.entries(parseResult['queryAsisten'])

                var chart_matang = Object.entries(parseResult['chart_matang'])
                var chart_mentah = Object.entries(parseResult['chart_mentah'])
                var chart_lewatmatang = Object.entries(parseResult['chart_lewatmatang'])
                var chart_janjangkosong = Object.entries(parseResult['chart_janjangkosong'])
                var chart_vcut = Object.entries(parseResult['chart_vcut'])
                var chart_karung = Object.entries(parseResult['chart_karung'])

                var chart_matangwil = Object.entries(parseResult['chart_matangwil'])
                var chart_mentahwil = Object.entries(parseResult['chart_mentahwil'])
                var chart_lewatmatangwil = Object.entries(parseResult['chart_lewatmatangwil'])
                var chart_janjangkosongwil = Object.entries(parseResult['chart_janjangkosongwil'])
                var chart_vcutwil = Object.entries(parseResult['chart_vcutwil'])
                var chart_karungwil = Object.entries(parseResult['chart_karungwil'])
                var regionaltab = Object.entries(parseResult['regionaltab'])
                // console.log(chart_matang);
                var matang_Wil = '['
                if (chart_matangwil.length > 0) {
                    chart_matangwil.forEach(element => {
                        matang_Wil += '"' + element[1] + '",'
                    });
                    matang_Wil = matang_Wil.substring(0, matang_Wil.length - 1);
                }
                matang_Wil += ']'
                var mentah_wil = '['
                if (chart_mentahwil.length > 0) {
                    chart_mentahwil.forEach(element => {
                        mentah_wil += '"' + element[1] + '",'
                    });
                    mentah_wil = mentah_wil.substring(0, mentah_wil.length - 1);
                }
                mentah_wil += ']'
                var lewatmatangs_wil = '['
                if (chart_lewatmatangwil.length > 0) {
                    chart_lewatmatangwil.forEach(element => {
                        lewatmatangs_wil += '"' + element[1] + '",'
                    });
                    lewatmatangs_wil = lewatmatangs_wil.substring(0, lewatmatangs_wil.length - 1);
                }
                lewatmatangs_wil += ']'

                var jjgkosongs_wil = '['
                if (chart_janjangkosongwil.length > 0) {
                    chart_janjangkosongwil.forEach(element => {
                        jjgkosongs_wil += '"' + element[1] + '",'
                    });
                    jjgkosongs_wil = jjgkosongs_wil.substring(0, jjgkosongs_wil.length - 1);
                }
                jjgkosongs_wil += ']'

                var vcuts_wil = '['
                if (chart_vcutwil.length > 0) {
                    chart_vcutwil.forEach(element => {
                        vcuts_wil += '"' + element[1] + '",'
                    });
                    vcuts_wil = vcuts_wil.substring(0, vcuts_wil.length - 1);
                }
                vcuts_wil += ']'
                var karungs_wil = '['
                if (chart_karungwil.length > 0) {
                    chart_karungwil.forEach(element => {
                        karungs_wil += '"' + element[1] + '",'
                    });
                    karungs_wil = karungs_wil.substring(0, karungs_wil.length - 1);
                }
                karungs_wil += ']'

                // console.log(matang_Wil);
                let regInpt = regData;

                var wilayah = '['
                region.forEach(element => {
                    wilayah += '"' + element + '",'
                });
                wilayah = wilayah.substring(0, wilayah.length - 1);
                wilayah += ']'

                // console.log(chart_matang);
                var matang = '['
                if (chart_matang.length > 0) {
                    chart_matang.forEach(element => {
                        matang += '"' + element[1] + '",'
                    });
                    matang = matang.substring(0, matang.length - 1);
                }
                matang += ']'

                var mentah = '['
                if (chart_mentah.length > 0) {
                    chart_mentah.forEach(element => {
                        mentah += '"' + element[1] + '",'
                    });
                    mentah = mentah.substring(0, mentah.length - 1);
                }
                mentah += ']'

                var lewatmatangs = '['
                if (chart_lewatmatang.length > 0) {
                    chart_lewatmatang.forEach(element => {
                        lewatmatangs += '"' + element[1] + '",'
                    });
                    lewatmatangs = lewatmatangs.substring(0, lewatmatangs.length - 1);
                }
                lewatmatangs += ']'

                var jjgkosongs = '['
                if (chart_janjangkosong.length > 0) {
                    chart_janjangkosong.forEach(element => {
                        jjgkosongs += '"' + element[1] + '",'
                    });
                    jjgkosongs = jjgkosongs.substring(0, jjgkosongs.length - 1);
                }
                jjgkosongs += ']'

                var vcuts = '['
                if (chart_vcut.length > 0) {
                    chart_vcut.forEach(element => {
                        vcuts += '"' + element[1] + '",'
                    });
                    vcuts = vcuts.substring(0, vcuts.length - 1);
                }
                vcuts += ']'

                var karungs = '['
                if (chart_karung.length > 0) {
                    chart_karung.forEach(element => {
                        karungs += '"' + element[1] + '",'
                    });
                    karungs = karungs.substring(0, karungs.length - 1);
                }
                karungs += ']'

                var estate = JSON.parse(wilayah)
                var matang_chart = JSON.parse(matang)
                var matang_chart_wil = JSON.parse(matang_Wil)
                var mentah_chart = JSON.parse(mentah)
                var mentah_chart_wil = JSON.parse(mentah_wil)

                var lwtmatang_chart = JSON.parse(lewatmatangs)
                var lwtmatang_chart_wil = JSON.parse(lewatmatangs_wil)
                var janjangksng_chart = JSON.parse(jjgkosongs)
                var janjangksng_chart_wil = JSON.parse(jjgkosongs_wil)
                var vcuts_chart = JSON.parse(vcuts)
                var vcuts_chart_wil = JSON.parse(vcuts_wil)
                var karungs_chart = JSON.parse(karungs)
                var karungs_chart_wil = JSON.parse(karungs_wil)



                const formatEst = estate.map((item) => item.split(',')[1]);
                let plasma1Index = formatEst.indexOf("Plasma1");

                if (plasma1Index !== -1) {
                    formatEst.splice(plasma1Index, 1);
                    formatEst.push("Plasma1");
                }

                // console.log(formatEst);

                let colors = '';


                if (regInpt === '1') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#FF8D1A',
                        '#00ffff'
                    ]

                } else if (regInpt === '2') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#00ffff',
                        '#00ffff'
                    ]


                } else if (regInpt === '3') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                        '#3063EC',
                    ]
                } else if (regInpt === '4') {
                    colors = ['#00FF00',
                        '#00FF00',
                        '#00FF00',
                        '#3063EC',
                        '#3063EC',

                    ]
                }

                // console.log(matang_chart);
                char1.updateSeries([{
                    name: 'matang',
                    data: matang_chart,

                }])
                char1.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////////
                chart2.updateSeries([{
                    name: 'mentah',
                    data: mentah_chart,

                }])
                chart2.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////////
                chart3.updateSeries([{
                    name: 'lewat matang',
                    data: lwtmatang_chart,

                }])
                chart3.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                /////////
                chart4.updateSeries([{
                    name: 'janjang kosong',
                    data: janjangksng_chart,

                }])
                chart4.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ////////
                chart5.updateSeries([{
                    name: 'vcut ',
                    data: vcuts_chart,

                }])
                chart5.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })
                ///////
                chart6.updateSeries([{
                    name: 'karung',
                    data: karungs_chart,

                }])
                chart6.updateOptions({
                    xaxis: {
                        categories: formatEst
                    },
                    colors: colors // Set the colors directly, no need for an object
                })


                if (regInpt === '1') {
                    wil_est = ['I', 'II', 'III']
                    warna = ['#00FF00',
                        '#FF8D1A',
                        '#00ffff'
                    ]
                } else if (regInpt === '2') {
                    wil_est = ['IV', 'V', 'VI']
                    warna = ['#00FF00',
                        '#FF8D1A',
                        '#00ffff'
                    ]
                } else if (regInpt === '3') {
                    wil_est = ['VII', 'VIII']
                    warna = ['#00FF00',
                        '#00ffff'
                    ]
                } else {
                    wil_est = ['IX', 'X']
                    warna = ['#00FF00',
                        '#00ffff'
                    ]
                }
                chart_wils.updateSeries([{
                    name: 'matang',
                    data: matang_chart_wil,

                }])
                chart_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna
                })
                chartx_wils.updateSeries([{
                    name: 'mentah',
                    data: mentah_chart_wil,

                }])
                chartx_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ///////////
                charts_wils.updateSeries([{
                    name: 'lewat matang',
                    data: lwtmatang_chart_wil,

                }])
                charts_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                /////////
                chartc_wils.updateSeries([{
                    name: 'janjang kosong',
                    data: janjangksng_chart_wil,

                }])
                chartc_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ////////
                chartv_wils.updateSeries([{
                    name: 'vcut ',
                    data: vcuts_chart_wil,

                }])
                chartv_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })
                ///////
                chartb_wils.updateSeries([{
                    name: 'karung',
                    data: karungs_chart_wil,

                }])
                chartb_wils.updateOptions({
                    xaxis: {
                        categories: wil_est
                    },
                    colors: warna // Set the colors directly, no need for an object
                })

                //endchart


                function createTableCell(text, customClass = null) {
                    const cell = document.createElement('td');
                    cell.innerText = text;
                    if (customClass) {
                        cell.classList.add(customClass);
                    }
                    return cell;
                }

                function setBackgroundColor(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }

                function bgest(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }



                var arrTbody1 = mutu_buah[0][1];

                var tbody1 = document.getElementById('weeks1');

                Object.entries(arrTbody1).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] !== undefined ? data['nama_asisten'] : '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody1.appendChild(tr);
                    });
                });
                var arrEst1 = mutubuah_est[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('weeks1');

                Object.entries(arrEst1).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody1.appendChild(tr);

                });
                if (regInpt === '1') {
                    wil1 = 'WIL-I';
                    wil2 = 'WIL-II';
                    wil3 = 'WIL-III';
                    wil4 = 'Plasma1'
                } else if (regInpt === '2') {
                    wil1 = 'WIL-IV';
                    wil2 = 'WIL-V';
                    wil3 = 'WIL-VI';
                    wil4 = 'Plasma2'
                } else if (regInpt === '3') {
                    wil1 = 'WIL-VII';
                    wil2 = 'WIL-VIII';
                    wil3 = 'Plasma3';
                    wil4 = 'Plasma3'
                } else {
                    wil1 = 'WIL-IX';
                    wil2 = 'WIL-X';
                    wil3 = '-';
                    wil4 = '-'
                }
                var arrEst1 = mutuBuah_wil[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('weeks1');
                const tr = document.createElement('tr');
                // console.log(estateData);
                let item3 = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil1 && asisten[1].afd === 'GM') {
                        item3 = asisten[1].nama;
                    }
                });

                const dataItems = {
                    item1: wil1,
                    item2: 'GM',
                    item3: item3,
                    item4: arrEst1['All_skor'],
                    item5: arrEst1['rankWil'],
                };


                const rowData = Object.values(dataItems);

                rowData.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tr.appendChild(cell);
                });

                tbody1.appendChild(tr);



                var tab2 = mutu_buah[1][1];
                var tbody2 = document.getElementById('weeks2');
                // console.log(tab2);
                Object.entries(tab2).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] || '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody2.appendChild(tr);
                    });
                });

                var arrEst2 = mutubuah_est[1][1];
                // console.log(arrEst2);
                var tbody2 = document.getElementById('weeks2');

                Object.entries(arrEst2).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody2.appendChild(tr);

                });

                var arrWil2 = mutuBuah_wil[1][1];
                // console.log(arrWil2);
                var tbody2 = document.getElementById('weeks2');
                const tx = document.createElement('tr');
                // console.log(estateData);
                let item3s = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil2 && asisten[1].afd === 'GM') {
                        item3s = asisten[1].nama;
                    }
                });
                const dataItemx = {

                    item1: wil2,
                    item2: 'GM',
                    item3: item3s,
                    item4: arrWil2['All_skor'],
                    item5: arrWil2['rankWil'],
                };

                const rowDatax = Object.values(dataItemx);

                rowDatax.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tx.appendChild(cell);
                });

                tbody2.appendChild(tx);

                var tbody3 = document.getElementById('weeks3');

                if (mutu_buah[2] !== undefined) {
                    var tab3 = mutu_buah[2][1];

                    if (tab3 !== null && tab3 !== undefined) {
                        Object.entries(tab3).forEach(([estateName, estateData]) => {
                            Object.entries(estateData).forEach(([key2, data], index) => {
                                const tr = document.createElement('tr');

                                const dataItems = {
                                    item1: estateName,
                                    item2: key2,
                                    item3: data['nama_asisten'] || '-',
                                    item4: data['All_skor'],
                                    item5: data['rankAFD'],
                                };

                                const rowData = Object.values(dataItems);

                                rowData.forEach((item, cellIndex) => {
                                    const cell = createTableCell(item, "text-center");

                                    if (cellIndex === 3) {
                                        setBackgroundColor(cell, item);
                                    }

                                    tr.appendChild(cell);
                                });

                                tbody3.appendChild(tr);
                            });
                        });
                    } else {
                        console.log("tab3 is null or undefined");
                    }
                } else {
                    console.log("mutu_buah[2] is undefined");
                }

                var tbody3 = document.getElementById('weeks3');

                if (mutubuah_est[2] !== undefined) {
                    var arrEst3 = mutubuah_est[2][1];

                    if (arrEst3 !== null && arrEst3 !== undefined) {
                        Object.entries(arrEst3).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody3.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst3 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[2] is undefined");
                }

                var tbody3 = document.getElementById('weeks3');

                if (mutuBuah_wil[2] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[2][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil3 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const tm = document.createElement('tr');

                        const dataitemc = {
                            item1: wil3,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowDatac = Object.values(dataitemc);

                        rowDatac.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tm.appendChild(cell);
                        });

                        tbody3.appendChild(tm);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[2] is undefined");
                }


                var arrTbody1 = mutu_buah[0]?.[1] || [];

                if (mutu_buah[3]) {
                    var tab4 = mutu_buah[3][1];
                }
                // var tab4 = mutu_buah[3][1];
                var tbody4 = document.getElementById('plasmas1');

                if (tab4 !== null && tab4 !== undefined) {
                    Object.entries(tab4).forEach(([estateName, estateData]) => {
                        Object.entries(estateData).forEach(([key2, data], index) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: key2,
                                item3: data['nama_asisten'] || '-',
                                item4: data['All_skor'],
                                item5: data['rankAFD'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");

                                if (cellIndex === 3) {
                                    setBackgroundColor(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    });
                } else {
                    console.log("tab4 is null or undefined");
                }

                var tbody4 = document.getElementById('plasmas1');

                if (mutubuah_est[3] !== undefined) {
                    var arrEst4 = mutubuah_est[3][1];

                    if (arrEst4 !== null && arrEst4 !== undefined) {
                        Object.entries(arrEst4).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst4 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[3] is undefined");
                }


                var tbody4 = document.getElementById('plasmas1');
                const tl = document.createElement('tr');

                if (mutuBuah_wil[3] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[3][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil4 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const dataOm = {
                            item1: wil4,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowOm = Object.values(dataOm);

                        rowOm.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tl.appendChild(cell);
                        });

                        tbody4.appendChild(tl);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[3] is undefined");
                }


                var regionals = regIonal;
                // console.log(regionals);
                var headregional = document.getElementById('theadregs');
                const trreg = document.createElement('tr');

                const dataReg = {
                    // item1: regIonal[0] && regIonal[0][1] && regIonal[0][1].regional !== undefined ? regIonal[0][1].regional : '-',
                    item1: regionaltab[0][1]['nama'],
                    // item2: regIonal[0] && regIonal[0][1] && regIonal[0][1].jabatan !== undefined ? regIonal[0][1].jabatan : '-',
                    item2: regionaltab[0][1]['jabatan'],
                    // item3: regIonal[0] && regIonal[0][1] && regIonal[0][1].nama_asisten !== undefined ? regIonal[0][1].nama_asisten : '-',
                    item3: regionaltab[0][1]['nama_rh'],
                    item4: regIonal[0] && regIonal[0][1] && regIonal[0][1].all_skorYear !== undefined ? regIonal[0][1].all_skorYear : '-',
                };
                const rowREG = Object.values(dataReg);
                rowREG.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }
                    trreg.appendChild(cell);
                });
                headregional.appendChild(trreg);



            }
        });
    }


    document.getElementById('showDataIns').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        dashboardData_tahun()
    }

    function dashboardData_tahun() {
        $('#data_tahunTab').empty()


        var reg = ''
        var tahun = ''
        var sbi_est = ''

        var _token = $('input[name="_token"]').val();
        var reg = document.getElementById('regDataTahun').value
        var sbi_est = document.getElementById('sbiGraphYear').value
        var tahun = document.getElementById('yearData').value


        $.ajax({
            url: "{{ route('getYearData') }}",
            method: "GET",
            data: {
                reg,
                tahun,
                _token: _token
            },
            success: function(result) {
                Swal.close();
                var parseResult = JSON.parse(result)
                var data_Sidak = Object.entries(parseResult['data_sidak'])
                // console.log(data_Sidak);


                function createTableCell(text) {
                    const cell = document.createElement('td');
                    cell.innerText = text;
                    return cell;
                }

                function createTableCellWithColor(data, kategori) {
                    let cell = document.createElement('td');
                    cell.innerText = data;

                    // Set background color based on kategori value
                    switch (kategori) {
                        case "POOR":
                            cell.style.backgroundColor = 'red';
                            break;
                        case "GOOD":
                            cell.style.backgroundColor = 'green';
                            break;
                        case "FAIR":
                            cell.style.backgroundColor = 'yellow';
                            break;
                        case "EXCELLENT":
                            cell.style.backgroundColor = 'blue';
                            break;
                    }

                    return cell;
                }
                var arrTbody1 = data_Sidak;
                var tbody1 = document.getElementById('data_tahunTab');
                counter = 1;

                arrTbody1.forEach(element => {
                    let item4 = element[0];
                    let afdelingData = element[1];

                    Object.keys(afdelingData).forEach((key, index) => {
                        tr = document.createElement('tr');
                        let dataItems = {
                            item1: counter++,
                            item2: afdelingData[key].reg,
                            item3: afdelingData[key].pt,
                            item4: item4,
                            item5: afdelingData[key].afd,
                            item6: afdelingData[key].nama_staff,
                            item7: afdelingData[key].Jumlah_janjang,
                            item8: afdelingData[key].tnp_brd,
                            item9: afdelingData[key].persenTNP_brd,
                            item10: afdelingData[key].krg_brd,
                            item11: afdelingData[key].persenKRG_brd,
                            item12: afdelingData[key].total_jjg,
                            item13: afdelingData[key].persen_totalJjg,
                            item14: afdelingData[key].skor_total,
                            item15: afdelingData[key].jjg_matang,
                            item16: afdelingData[key].persen_jjgMtang,
                            item17: afdelingData[key].skor_jjgMatang,
                            item18: afdelingData[key].lewat_matang,
                            item19: afdelingData[key].persen_lwtMtng,
                            item20: afdelingData[key].skor_lewatMTng,
                            item21: afdelingData[key].janjang_kosong,
                            item22: afdelingData[key].persen_kosong,
                            item23: afdelingData[key].skor_kosong,
                            item24: afdelingData[key].vcut,
                            item25: afdelingData[key].vcut_persen,
                            item26: afdelingData[key].vcut_skor,
                            item27: afdelingData[key].abnormal,
                            item28: afdelingData[key].abnormal_persen,
                            item29: afdelingData[key].rat_dmg,
                            item30: afdelingData[key].rd_persen,
                            item31: afdelingData[key].TPH,
                            item32: afdelingData[key].persen_krg,
                            item33: afdelingData[key].skor_kr,
                            item34: afdelingData[key].All_skor,
                            item35: afdelingData[key].kategori,
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((data, cellIndex) => {
                            // Create a new cell with a background color based on the 'kategori' value
                            let cell = (cellIndex === 34) ? createTableCellWithColor(data, data) : createTableCell(data);

                            // Apply lightblue color to the last row except for the cell with the 'kategori' value
                            if (index === Object.keys(afdelingData).length - 1 && cellIndex !== 34) {
                                cell.style.backgroundColor = 'lightblue';
                            }

                            // // Add the freeze-reg and freeze-afd classes to the corresponding cells
                            // if (cellIndex === 3) {
                            //     cell.classList.add('freeze-reg');
                            // } else if (cellIndex === 4) {
                            //     cell.classList.add('freeze-afd');
                            // }

                            tr.appendChild(cell);
                        });


                        tbody1.appendChild(tr);
                    });


                });


            }
        });
    }



    document.getElementById('showFindingYear').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });

        dashboardFindingYear()
    }



    function dashboardFindingYear() {
        $('#bodyFind').empty()


        var reg = ''
        var tahun = ''

        var _token = $('input[name="_token"]').val();
        var reg = document.getElementById('regFindingYear').value
        var tahun = document.getElementById('yearFinding').value


        $.ajax({
            url: "{{ route('findingIsueTahun') }}",
            method: "GET",
            data: {
                reg,
                tahun,
                _token: _token
            },
            success: function(result) {
                Swal.close();
                var parseResult = JSON.parse(result)
                var findingIsue = Object.entries(parseResult['finding_nemo'])


                const Findissu = findingIsue.map(([_, data]) => ({

                    est: data.est,
                    temuan: data.foto_temuan,
                    visit: data.visit,
                }));

                // console.log(Findissu);

                var arrTbody1 = Findissu

                var tbody1 = document.getElementById('bodyFind');
                //         $('#thead1').empty()
                // $('#thead2').empty()
                // $('#thead3').empty()

                arrTbody1.forEach(element => {
                    const {
                        est: item1,
                        temuan: item2,
                        visit: item3
                    } = element;

                    const tr = document.createElement('tr');
                    const itemElement1 = document.createElement('td');
                    const itemElement2 = document.createElement('td');
                    const itemElement3 = document.createElement('td');

                    itemElement1.textContent = item1;
                    itemElement1.colSpan = 3;

                    itemElement2.textContent = item2;
                    itemElement2.colSpan = 5;

                    const downloadButton = document.createElement('a');
                    downloadButton.classList.add('btn');

                    if (item2 != 0) {
                        downloadButton.href = `/cetakmutubuah_id/${item1}/${tahun}/${reg}`;
                        downloadButton.classList.add('btn-primary');
                        // downloadButton.target = '_blank';
                    } else {
                        downloadButton.classList.add('btn-secondary');
                        downloadButton.setAttribute('disabled', '');
                    }

                    const downloadIcon = document.createElement('i');
                    downloadIcon.classList.add('nav-icon', 'fa', 'fa-download');
                    downloadButton.appendChild(downloadIcon);

                    itemElement3.appendChild(downloadButton);
                    itemElement2.colSpan = 3;

                    tr.append(itemElement1, itemElement2, itemElement3);
                    tbody1.appendChild(tr);
                });


            }
        });
    }


    document.getElementById('btnShoWeekdata').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        getweekData();
    }

    function getweekData() {
        // $('#data_weekTab').empty()
        $('#data_weekTab2').empty()
        var reg = '';
        var bulan = '';
        var reg = document.getElementById('regional_data').value;
        var bulan = document.getElementById('inputDateMonth').value;
        var _token = $('input[name="_token"]').val();

        // console.log(dateWeek);

        $.ajax({
            url: "{{ route('getWeekData') }}",
            method: "GET",
            data: {
                reg,
                bulan,
                _token: _token
            },
            success: function(result) {

                Swal.close();
                var parseResult = JSON.parse(result)
                var data_Sidak = Object.entries(parseResult['data_week'])
                var data_Sidakv2 = Object.entries(parseResult['data_weekv2'])
                var regional = Object.entries(parseResult['reg_data'])
                var plasma = Object.entries(parseResult['plasma'])
                // console.log(data_Sidakv2);
                delete data_Sidakv2[0][1].Plasma1;
                // console.log(data_Sidakv2);

                function createTableCell(data, isHTML = false) {
                    let td = document.createElement('td');
                    if (isHTML) {
                        td.innerHTML = data;
                    } else {
                        td.textContent = data;
                    }
                    return td;
                }

                function createTableCellWithColor(data, kategori) {
                    let cell = document.createElement('td');
                    cell.innerText = data;

                    // Set background color based on kategori value
                    switch (kategori) {
                        case "POOR":
                            cell.style.backgroundColor = '#ff0404';
                            break;
                        case "GOOD":
                            cell.style.backgroundColor = '#08fc2c';
                            break;
                        case "FAIR":
                            cell.style.backgroundColor = '#ffa404';
                            break;
                        case "EXCELLENT":
                            cell.style.backgroundColor = '#5074c4';
                            break;
                        case "SATISFACTORY":
                            cell.style.backgroundColor = '#fffc04';
                            break;
                    }

                    return cell;
                }


                var arrbody2 = data_Sidakv2;
                // Extract Wilayah



                counters = 1;


                function setBackgroundColorForEstate(data, bgColor) {
                    let cell = document.createElement('td');
                    cell.textContent = data;
                    cell.style.backgroundColor = bgColor;
                    return cell;
                }

                // console.log(arrbody2)

                var body2 = document.getElementById('data_weekTab2');
                arrbody2.forEach(element => {
                    let afdelingData = element[1];
                    let wilayahData;

                    if (typeof element[0] === 'string' && typeof afdelingData[element[0]] === 'object') {
                        wilayahData = afdelingData[element[0]];
                    }

                    Object.keys(afdelingData).forEach((key) => {
                        let currentData = afdelingData[key];
                        if (typeof currentData === 'object' && currentData !== null && !Array.isArray(currentData)) {
                            Object.keys(currentData).forEach(innerKey => {
                                let innerData = currentData[innerKey];
                                if (typeof innerData === 'object' && innerData !== null && innerData.hasOwnProperty('Jumlah_janjang')) {

                                    tr = document.createElement('tr');
                                    let item1 = counters++

                                    let item4 = innerData.est;
                                    // let item4 = document.createElement('span');
                                    // item4.innerText = innerData.est;

                                    // let item5 = innerData.afd;
                                    let item5 = document.createElement('a');
                                    item5.href = 'detailtmutubuah/' + innerData.est + '/' + innerData.afd + '/' + bulan;
                                    // item5.target = '_blank';

                                    item5.innerText = innerData.afd;
                                    let item6 = innerData.nama_staff !== undefined ? innerData.nama_staff : '-';
                                    let item7 = innerData.Jumlah_janjang;
                                    let item8 = innerData.tnp_brd;
                                    let item9 = innerData.persenTNP_brd;
                                    let item10 = innerData.krg_brd;
                                    let item11 = innerData.persenKRG_brd;
                                    let item12 = innerData.total_jjg;
                                    let item13 = innerData.persen_totalJjg;
                                    let item14 = innerData.skor_total;
                                    let item15 = innerData.jjg_matang;
                                    let item16 = innerData.persen_jjgMtang;
                                    let item17 = innerData.skor_jjgMatang;
                                    let item18 = innerData.lewat_matang;
                                    let item19 = innerData.persen_lwtMtng;
                                    let item20 = innerData.skor_lewatMTng;
                                    let item21 = innerData.janjang_kosong;
                                    let item22 = innerData.persen_kosong;
                                    let item23 = innerData.skor_kosong;
                                    let item24 = innerData.vcut;
                                    let item25 = innerData.vcut_persen;
                                    let item26 = innerData.vcut_skor;
                                    let item27 = innerData.abnormal;
                                    let item28 = innerData.abnormal_persen;
                                    let item29 = innerData.rat_dmg;
                                    let item30 = innerData.rd_persen;
                                    let item31 = innerData.TPH;
                                    let item32 = innerData.persen_krg;
                                    let item33 = innerData.skor_kr;
                                    let item34 = innerData.All_skor;
                                    let item35 = innerData.kategori;

                                    let rowData = [
                                        item1, item4, item5, item6, item7, item8, item9, item10,
                                        item11, item12, item13, item14, item15, item16, item17, item18, item19, item20,
                                        item21, item22, item23, item24, item25, item26, item27, item28, item29, item30,
                                        item31, item32, item33, item34, item35
                                    ];


                                    rowData.forEach((data, cellIndex) => {
                                        let cell;

                                        // Create a colored cell for item35 using createTableCellWithColor function
                                        if (cellIndex === 32) {
                                            cell = createTableCellWithColor(data, data);
                                        } else {
                                            cell = document.createElement('td');
                                        }

                                        cell.classList.add('text-center');

                                        // Add the sticky-cell class and set different left values for cells in the 4th and 5th columns
                                        if (cellIndex === 1) {
                                            cell.classList.add('sticky-cell');
                                            cell.style.left = '0';
                                        } else if (cellIndex === 2) {
                                            cell.classList.add('sticky-cell');
                                            cell.style.left = '30px'; // You can adjust this value based on the width of the 4th column
                                        }

                                        if (cellIndex === 2) {
                                            cell.appendChild(data);
                                        } else if (cellIndex !== 32) {
                                            cell.innerText = data;
                                        }

                                        // ...

                                        // Apply background color to the entire row except for cell 34, cell 4 and cell 5
                                        if (cellIndex !== 32) {
                                            cell.style.backgroundColor = innerData['background_color'];
                                        }

                                        tr.appendChild(cell);
                                        // ...

                                    });

                                    body2.appendChild(tr);
                                }
                            });
                        }
                    });

                    if (wilayahData) {
                        tr = document.createElement('tr');
                        let item1 = counters++

                        let item4 = wilayahData.est;
                        let item5 = ' '

                        let item6 = wilayahData.nama_asisten !== undefined ? wilayahData.nama_asisten : '-';
                        let item7 = wilayahData.Jumlah_janjang;
                        let item8 = wilayahData.tnp_brd;
                        let item9 = wilayahData.persenTNP_brd;
                        let item10 = wilayahData.krg_brd;
                        let item11 = wilayahData.persenKRG_brd;
                        let item12 = wilayahData.total_jjg;
                        let item13 = wilayahData.persen_totalJjg;
                        let item14 = wilayahData.skor_total;
                        let item15 = wilayahData.jjg_matang;
                        let item16 = wilayahData.persen_jjgMtang;
                        let item17 = wilayahData.skor_jjgMatang;
                        let item18 = wilayahData.lewat_matang;
                        let item19 = wilayahData.persen_lwtMtng;
                        let item20 = wilayahData.skor_lewatMTng;
                        let item21 = wilayahData.janjang_kosong;
                        let item22 = wilayahData.persen_kosong;
                        let item23 = wilayahData.skor_kosong;
                        let item24 = wilayahData.vcut;
                        let item25 = wilayahData.vcut_persen;
                        let item26 = wilayahData.vcut_skor;
                        let item27 = wilayahData.abnormal;
                        let item28 = wilayahData.abnormal_persen;
                        let item29 = wilayahData.rat_dmg;
                        let item30 = wilayahData.rd_persen;
                        let item31 = wilayahData.TPH;
                        let item32 = wilayahData.persen_krg;
                        let item33 = wilayahData.skor_kr;
                        let item34 = wilayahData.all_skor;
                        let item35 = wilayahData.kategori;

                        let rowData = [
                            item1, item4, item5, item6, item7, item8, item9, item10,
                            item11, item12, item13, item14, item15, item16, item17, item18, item19, item20,
                            item21, item22, item23, item24, item25, item26, item27, item28, item29, item30,
                            item31, item32, item33, item34, item35
                        ];

                        rowData.forEach((data, cellIndex) => {
                            let cell = document.createElement('td');
                            cell.classList.add('text-center');

                            if (cellIndex === 2) {
                                cell.classList.add('freeze-reg');
                            } else if (cellIndex === 3) {
                                cell.classList.add('freeze-afd');
                            }

                            // Set background color for item35 (category)
                            if (cellIndex === 32) {
                                switch (data) {
                                    case "POOR":
                                        cell.style.backgroundColor = '#ff0404';
                                        break;
                                    case "GOOD":
                                        cell.style.backgroundColor = '#08fc2c';
                                        break;
                                    case "FAIR":
                                        cell.style.backgroundColor = '#ffa404';
                                        break;
                                    case "EXCELLENT":
                                        cell.style.backgroundColor = '#5074c4';
                                        break;
                                    case "SATISFACTORY":
                                        cell.style.backgroundColor = '#fffc04';
                                        break;
                                }
                            }


                            // Apply background color to the entire row except for cell 34
                            if (cellIndex !== 32) {
                                cell.style.backgroundColor = wilayahData['background_color'];
                            }

                            cell.innerText = data;
                            tr.appendChild(cell);
                        });
                        body2.appendChild(tr);
                    }
                });

                // console.log(plasma);

                var arrbody2v2 = plasma
                // console.log(arrbody2v2);
                // var ah = arrbody2v2[0][1];
                var data = plasma[0][1][1]['Plasma1']

                // console.log(data);

                var tbodyPlas = document.getElementById('data_weekTab2');

                // Loop through the keys in the data object
                function createAndAppendTableCellWithIndex(data, cellIndex, parentRow, backgroundColor = null) {
                    let cell;

                    if (cellIndex === 32) {
                        cell = createTableCellWithColor(data, data);
                    } else {
                        let isHTML = cellIndex === 2;
                        cell = createTableCell(data, isHTML);
                    }

                    if (cellIndex !== 32 && backgroundColor) {
                        cell.style.backgroundColor = backgroundColor;
                    }

                    parentRow.appendChild(cell);
                }
                count = 1;

                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        // let item1 = data[key].afd;
                        let item1 = count++
                        let item2 = data[key].est;
                        // 'detailtmutubuah/' + innerData.est + '/' + innerData.afd + '/' + bulan;
                        let item3 = '<a href="detailtmutubuah/' + data[key].est + '/' + data[key].afd + '/' + bulan + '">' + data[key].afd + ' </a>'
                        let item6 = data[key].nama_staff !== undefined ? data[key].nama_staff : '-';
                        let item7 = data[key].Jumlah_janjang;
                        let item8 = data[key].tnp_brd;
                        let item9 = data[key].persenTNP_brd;
                        let item10 = data[key].krg_brd;
                        let item11 = data[key].persenKRG_brd;
                        let item12 = data[key].total_jjg;
                        let item13 = data[key].persen_totalJjg;
                        let item14 = data[key].skor_total;
                        let item15 = data[key].jjg_matang;
                        let item16 = data[key].persen_jjgMtang;
                        let item17 = data[key].skor_jjgMatang;
                        let item18 = data[key].lewat_matang;
                        let item19 = data[key].persen_lwtMtng;
                        let item20 = data[key].skor_lewatMTng;
                        let item21 = data[key].janjang_kosong;
                        let item22 = data[key].persen_kosong;
                        let item23 = data[key].skor_kosong;
                        let item24 = data[key].vcut;
                        let item25 = data[key].vcut_persen;
                        let item26 = data[key].vcut_skor;
                        let item27 = data[key].abnormal;
                        let item28 = data[key].abnormal_persen;
                        let item29 = data[key].rat_dmg;
                        let item30 = data[key].rd_persen;
                        let item31 = data[key].TPH;
                        let item32 = data[key].persen_krg;
                        let item33 = data[key].skor_kr;
                        let item34 = data[key].All_skor;
                        let item35 = data[key].kategori;


                        let tr = document.createElement('tr');

                        let items = [
                            item1, item2, item3, item6, item7, item8, item9, item10,
                            item11, item12, item13, item14, item15, item16, item17, item18, item19, item20,
                            item21, item22, item23, item24, item25, item26, item27, item28, item29, item30,
                            item31, item32, item33, item34, item35
                        ];
                        let backgroundColor = '#F0F0F0'; // Replace with the desired color value


                        items.forEach((item, index) => {
                            createAndAppendTableCellWithIndex(item, index, tr, backgroundColor);
                        });

                        tbodyPlas.appendChild(tr);
                    }
                }





                let regInpt = reg
                let regs = ''
                if (regInpt === '1') {
                    regs = 'REG-I'
                } else if (regInpt === '2') {
                    regs = 'REG-II'
                } else if (regInpt === '3') {
                    regs = 'REG-III'
                } else {
                    regs = 'REG-IV'
                }



                var arrTbody1 = regional;
                // console.log(arrTbody1);
                var tbody1 = document.getElementById('data_weekTab2');
                counter = 1;


                arrTbody1.forEach(element => {
                    tr = document.createElement('tr');

                    let dataItems = {
                        item1: counter++,
                        item2: element[1].Total.reg,
                        item5: '',
                        item6: element[1].Total.nama_staff,
                        item7: element[1].Total.Jumlah_janjang,
                        item8: element[1].Total.tnp_brd,
                        item9: element[1].Total.persenTNP_brd,
                        item10: element[1].Total.krg_brd,
                        item11: element[1].Total.persenKRG_brd,
                        item12: element[1].Total.total_jjg,
                        item13: element[1].Total.persen_totalJjg,
                        item14: element[1].Total.skor_total,
                        item15: element[1].Total.jjg_matang,
                        item16: element[1].Total.persen_jjgMtang,
                        item17: element[1].Total.skor_jjgMatang,
                        item18: element[1].Total.lewat_matang,
                        item19: element[1].Total.persen_lwtMtng,
                        item20: element[1].Total.skor_lewatMTng,
                        item21: element[1].Total.janjang_kosong,
                        item22: element[1].Total.persen_kosong,
                        item23: element[1].Total.skor_kosong,
                        item24: element[1].Total.vcut,
                        item25: element[1].Total.vcut_persen,
                        item26: element[1].Total.vcut_skor,
                        item27: element[1].Total.abnormal,
                        item28: element[1].Total.abnormal_persen,
                        item29: element[1].Total.rat_dmg,
                        item30: element[1].Total.rd_persen,
                        item31: element[1].Total.TPH,
                        item32: element[1].Total.persen_krg,
                        item33: element[1].Total.skor_kr,
                        item34: element[1].Total.all_skor,
                        item35: element[1].Total.kategori,
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((data, cellIndex) => {
                        // Create a new cell with a background color based on the 'kategori' value
                        let isHTML = cellIndex === 2;
                        let cell = (cellIndex === 32) ? createTableCellWithColor(data, data) : createTableCell(data, isHTML);


                        // Add the freeze-reg and freeze-afd classes to the corresponding cells
                        // if (cellIndex === 3) {
                        //     cell.classList.add('freeze-reg');
                        // } else if (cellIndex === 4) {
                        //     cell.classList.add('freeze-afd');
                        // }
                        if (cellIndex !== 32) {
                            cell.style.backgroundColor = element[1].Total['background_color'];
                        }

                        tr.appendChild(cell);
                    });
                    tbody1.appendChild(tr);
                });






            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Data Kosong.'
                });
                // Handle the error here
                console.log("An error occurred:", error);
            }


        });
    }

    document.getElementById('show_sbithn').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        sbi_tahun()
    }

    var list_month = <?php echo json_encode($list_bulan); ?>;


    var selectedTahun = ''; // Global variable to store the selected tahun value

    function sbi_tahun() {
        $('#tahun1').empty()
        $('#tahun2').empty()
        $('#tahun3').empty()
        $('#tahun4').empty()
        $('#tahunreg').empty()

        // document.getElementById('sbiGraphYear').addEventListener('click', function() {
        //     const selectedEstateValue = estSidakYear.value;
        //     const selectedEstateText = estSidakYear.options[estSidakYear.selectedIndex].text;

        selectedTahun = document.getElementById('sbi_tahun').value; //
        //     // Perform any actions with the selected estate text here
        // });

        var reg = '';
        var tahun = '';
        var reg = document.getElementById('reg_sbiThun').value;
        var tahun = document.getElementById('sbi_tahun').value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('getahun_sbi') }}",
            method: "GET",
            data: {
                reg: reg,
                tahun: tahun,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {

                Swal.close();

                var parseResult = JSON.parse(result)
                var region = Object.entries(parseResult['listregion'])
                var mutu_buah = Object.entries(parseResult['mutu_buah'])
                var mutubuah_est = Object.entries(parseResult['mutubuah_est'])
                var mutuBuah_wil = Object.entries(parseResult['mutuBuah_wil'])
                var regIonal = Object.entries(parseResult['regional'])
                var queryAsisten = Object.entries(parseResult['queryAsisten'])

                var regionaltab = Object.entries(parseResult['regionaltab'])
                var list_esate = Object.entries(parseResult['list_est'])
                // console.log(chart_matang);
                const estSidakYear = document.getElementById('estSidakYear');
                estSidakYear.innerHTML = '';
                list_esate.forEach(([key, value]) => {
                    const option = document.createElement('option');
                    option.value = key;
                    option.text = value;
                    estSidakYear.add(option);
                });
                let regInpt = reg;
                // console.log(chart_matang);

                // untuk chart
                // console.log(region);
                var wilayah = '['
                region.forEach(element => {
                    wilayah += '"' + element + '",'
                });
                wilayah = wilayah.substring(0, wilayah.length - 1);
                wilayah += ']'

                var estate = JSON.parse(wilayah)



                const formatEst = estate.map((item) => item.split(',')[1]);
                let plasma1Index = formatEst.indexOf("Plasma1");

                if (plasma1Index !== -1) {
                    formatEst.splice(plasma1Index, 1);
                    formatEst.push("Plasma1");
                }

                //endchart


                function createTableCell(text, customClass = null) {
                    const cell = document.createElement('td');
                    cell.innerText = text;
                    if (customClass) {
                        cell.classList.add(customClass);
                    }
                    return cell;
                }

                function setBackgroundColor(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }

                function bgest(element, score) {
                    let color;
                    if (score >= 95) {
                        color = "#609cd4";
                    } else if (score >= 85 && score < 95) {
                        color = "#08b454";
                    } else if (score >= 75 && score < 85) {
                        color = "#fffc04";
                    } else if (score >= 65 && score < 75) {
                        color = "#ffc404";
                    } else {
                        color = "red";
                    }

                    element.style.backgroundColor = color;
                    element.style.color = (color === "#609cd4" || color === "#08b454" || color === "red") ? "white" : "black";
                }



                var arrTbody1 = mutu_buah[0][1];

                var tbody1 = document.getElementById('tahun1');

                Object.entries(arrTbody1).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] !== undefined ? data['nama_asisten'] : '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody1.appendChild(tr);
                    });
                });
                var arrEst1 = mutubuah_est[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('tahun1');

                Object.entries(arrEst1).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody1.appendChild(tr);

                });
                if (regInpt === '1') {
                    wil1 = 'WIL-I';
                    wil2 = 'WIL-II';
                    wil3 = 'WIL-III';
                    wil4 = 'Plasma1'
                } else if (regInpt === '2') {
                    wil1 = 'WIL-IV';
                    wil2 = 'WIL-V';
                    wil3 = 'WIL-VI';
                    wil4 = 'Plasma2'
                } else if (regInpt === '3') {
                    wil1 = 'WIL-VII';
                    wil2 = 'WIL-VIII';
                    wil3 = 'Plasma3';
                    wil4 = 'Plasma3'
                } else {
                    wil1 = 'WIL-IX';
                    wil2 = 'WIL-X';
                    wil3 = '-';
                    wil4 = '-'
                }
                var arrEst1 = mutuBuah_wil[0][1];
                // console.log(arrEst1);
                var tbody1 = document.getElementById('tahun1');
                const tr = document.createElement('tr');
                // console.log(estateData);
                let item3 = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil1 && asisten[1].afd === 'GM') {
                        item3 = asisten[1].nama;
                    }
                });

                const dataItems = {
                    item1: wil1,
                    item2: 'GM',
                    item3: item3,
                    item4: arrEst1['All_skor'],
                    item5: arrEst1['rankWil'],
                };


                const rowData = Object.values(dataItems);

                rowData.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tr.appendChild(cell);
                });

                tbody1.appendChild(tr);



                var tab2 = mutu_buah[1][1];
                var tbody2 = document.getElementById('tahun2');
                // console.log(tab2);
                Object.entries(tab2).forEach(([estateName, estateData]) => {
                    Object.entries(estateData).forEach(([key2, data], index) => {
                        const tr = document.createElement('tr');

                        const dataItems = {
                            item1: estateName,
                            item2: key2,
                            item3: data['nama_asisten'] || '-',
                            item4: data['All_skor'],
                            item5: data['rankAFD'],
                        };

                        const rowData = Object.values(dataItems);

                        rowData.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");

                            if (cellIndex === 3) {
                                setBackgroundColor(cell, item);
                            }

                            tr.appendChild(cell);
                        });

                        tbody2.appendChild(tr);
                    });
                });

                var arrEst2 = mutubuah_est[1][1];
                // console.log(arrEst2);
                var tbody2 = document.getElementById('tahun2');

                Object.entries(arrEst2).forEach(([estateName, estateData]) => {
                    const tr = document.createElement('tr');
                    // console.log(estateData);
                    const dataItems = {
                        item1: estateName,
                        item2: estateData['EM'],
                        item3: estateData['Nama_assist'] || '-',
                        item4: estateData['All_skor'],
                        item5: estateData['rankEST'],
                    };

                    const rowData = Object.values(dataItems);

                    rowData.forEach((item, cellIndex) => {
                        const cell = createTableCell(item, "text-center");
                        if (cellIndex <= 2) {
                            cell.style.backgroundColor = "#e8ecdc";
                            cell.style.color = "black";
                        } else if (cellIndex === 3) {
                            bgest(cell, item);
                        }


                        tr.appendChild(cell);
                    });

                    tbody2.appendChild(tr);

                });

                var arrWil2 = mutuBuah_wil[1][1];
                // console.log(arrWil2);
                var tbody2 = document.getElementById('tahun2');
                const tx = document.createElement('tr');
                // console.log(estateData);
                let item3s = '-';
                queryAsisten.forEach((asisten) => {
                    if (asisten[1].est === wil2 && asisten[1].afd === 'GM') {
                        item3s = asisten[1].nama;
                    }
                });
                const dataItemx = {

                    item1: wil2,
                    item2: 'GM',
                    item3: item3s,
                    item4: arrWil2['All_skor'],
                    item5: arrWil2['rankWil'],
                };

                const rowDatax = Object.values(dataItemx);

                rowDatax.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }


                    tx.appendChild(cell);
                });

                tbody2.appendChild(tx);

                var tbody3 = document.getElementById('tahun3');

                if (mutu_buah[2] !== undefined) {
                    var tab3 = mutu_buah[2][1];

                    if (tab3 !== null && tab3 !== undefined) {
                        Object.entries(tab3).forEach(([estateName, estateData]) => {
                            Object.entries(estateData).forEach(([key2, data], index) => {
                                const tr = document.createElement('tr');

                                const dataItems = {
                                    item1: estateName,
                                    item2: key2,
                                    item3: data['nama_asisten'] || '-',
                                    item4: data['All_skor'],
                                    item5: data['rankAFD'],
                                };

                                const rowData = Object.values(dataItems);

                                rowData.forEach((item, cellIndex) => {
                                    const cell = createTableCell(item, "text-center");

                                    if (cellIndex === 3) {
                                        setBackgroundColor(cell, item);
                                    }

                                    tr.appendChild(cell);
                                });

                                tbody3.appendChild(tr);
                            });
                        });
                    } else {
                        console.log("tab3 is null or undefined");
                    }
                } else {
                    console.log("mutu_buah[2] is undefined");
                }

                var tbody3 = document.getElementById('tahun3');

                if (mutubuah_est[2] !== undefined) {
                    var arrEst3 = mutubuah_est[2][1];

                    if (arrEst3 !== null && arrEst3 !== undefined) {
                        Object.entries(arrEst3).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody3.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst3 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[2] is undefined");
                }

                var tbody3 = document.getElementById('tahun3');

                if (mutuBuah_wil[2] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[2][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil3 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const tm = document.createElement('tr');

                        const dataitemc = {
                            item1: wil3,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowDatac = Object.values(dataitemc);

                        rowDatac.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tm.appendChild(cell);
                        });

                        tbody3.appendChild(tm);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[2] is undefined");
                }


                var arrTbody1 = mutu_buah[0]?.[1] || [];

                if (mutu_buah[3]) {
                    var tab4 = mutu_buah[3][1];
                }
                // var tab4 = mutu_buah[3][1];
                var tbody4 = document.getElementById('tahun4');

                if (tab4 !== null && tab4 !== undefined) {
                    Object.entries(tab4).forEach(([estateName, estateData]) => {
                        Object.entries(estateData).forEach(([key2, data], index) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: key2,
                                item3: data['nama_asisten'] || '-',
                                item4: data['All_skor'],
                                item5: data['rankAFD'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");

                                if (cellIndex === 3) {
                                    setBackgroundColor(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    });
                } else {
                    console.log("tab4 is null or undefined");
                }

                var tbody4 = document.getElementById('tahun4');

                if (mutubuah_est[3] !== undefined) {
                    var arrEst4 = mutubuah_est[3][1];

                    if (arrEst4 !== null && arrEst4 !== undefined) {
                        Object.entries(arrEst4).forEach(([estateName, estateData]) => {
                            const tr = document.createElement('tr');

                            const dataItems = {
                                item1: estateName,
                                item2: estateData['EM'],
                                item3: estateData['Nama_assist'] || '-',
                                item4: estateData['All_skor'],
                                item5: estateData['rankEST'],
                            };

                            const rowData = Object.values(dataItems);

                            rowData.forEach((item, cellIndex) => {
                                const cell = createTableCell(item, "text-center");
                                if (cellIndex <= 2) {
                                    cell.style.backgroundColor = "#e8ecdc";
                                    cell.style.color = "black";
                                } else if (cellIndex === 3) {
                                    bgest(cell, item);
                                }

                                tr.appendChild(cell);
                            });

                            tbody4.appendChild(tr);
                        });
                    } else {
                        console.log("arrEst4 is null or undefined");
                    }
                } else {
                    console.log("mutubuah_est[3] is undefined");
                }


                var tbody4 = document.getElementById('tahun4');
                const tl = document.createElement('tr');

                if (mutuBuah_wil[3] !== undefined) {
                    var arrWIl3 = mutuBuah_wil[3][1];
                    let item3s = '-';
                    queryAsisten.forEach((asisten) => {
                        if (asisten[1].est === wil4 && asisten[1].afd === 'GM') {
                            item3s = asisten[1].nama;
                        }
                    });
                    if (arrWIl3 !== null && arrWIl3 !== undefined) {
                        const dataOm = {
                            item1: wil4,
                            item2: 'GM',
                            item3: item3s,
                            item4: arrWIl3['All_skor'],
                            item5: arrWIl3['rankWil'],
                        };

                        const rowOm = Object.values(dataOm);

                        rowOm.forEach((item, cellIndex) => {
                            const cell = createTableCell(item, "text-center");
                            if (cellIndex <= 2) {
                                cell.style.backgroundColor = "#e8ecdc";
                                cell.style.color = "black";
                            } else if (cellIndex === 3) {
                                bgest(cell, item);
                            }

                            tl.appendChild(cell);
                        });

                        tbody4.appendChild(tl);
                    } else {
                        console.log("arrWIl3 is null or undefined");
                    }
                } else {
                    console.log("mutuBuah_wil[3] is undefined");
                }


                var regionals = regIonal;
                // console.log(regionals);
                var headregional = document.getElementById('tahunreg');
                const trreg = document.createElement('tr');

                const dataReg = {
                    // item1: regIonal[0] && regIonal[0][1] && regIonal[0][1].regional !== undefined ? regIonal[0][1].regional : '-',
                    item1: regionaltab[0][1]['nama'],
                    // item2: regIonal[0] && regIonal[0][1] && regIonal[0][1].jabatan !== undefined ? regIonal[0][1].jabatan : '-',
                    item2: regionaltab[0][1]['jabatan'],
                    // item3: regIonal[0] && regIonal[0][1] && regIonal[0][1].nama_asisten !== undefined ? regIonal[0][1].nama_asisten : '-',
                    item3: regionaltab[0][1]['nama_rh'],
                    item4: regIonal[0] && regIonal[0][1] && regIonal[0][1].all_skorYear !== undefined ? regIonal[0][1].all_skorYear : '-',
                };


                const rowREG = Object.values(dataReg);
                rowREG.forEach((item, cellIndex) => {
                    const cell = createTableCell(item, "text-center");
                    if (cellIndex <= 2) {
                        cell.style.backgroundColor = "#e8ecdc";
                        cell.style.color = "black";
                    } else if (cellIndex === 3) {
                        bgest(cell, item);
                    }
                    trreg.appendChild(cell);
                });
                headregional.appendChild(trreg);

                sbi_chart();
            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }
    var options_tahun = {

        series: [{
            name: '',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }],
        chart: {
            background: '#ffffff',
            height: 350,
            type: 'bar'
        },
        plotOptions: {
            bar: {
                distributed: true
            }
        },

        colors: [
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#00FF00',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#3063EC',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#FF8D1A',
            '#00ffff'
        ],

        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            type: '',
            // categories: estate
            categories: list_month
        }
    };


    var sbi1 = new ApexCharts(document.querySelector("#matang_tahun"), options_tahun);
    var sbi2 = new ApexCharts(document.querySelector("#mentah_tahun"), options_tahun);
    var sbi3 = new ApexCharts(document.querySelector("#lewatmatang_tahun"), options_tahun);
    var sbi4 = new ApexCharts(document.querySelector("#jangkos_tahun"), options_tahun);
    var sbi5 = new ApexCharts(document.querySelector("#tidakvcut_tahun"), options_tahun);
    var sbi6 = new ApexCharts(document.querySelector("#karungbrondolan_tahun"), options_tahun);

    sbi1.render();
    sbi2.render();
    sbi3.render();
    sbi4.render();
    sbi5.render();
    sbi6.render();

    document.getElementById('hiddenInput').value = document.getElementById('sbi_tahun').value;
    document.getElementById('sbi_tahun').addEventListener('change', function() {
        document.getElementById('hiddenInput').value = this.value;
    });



    document.getElementById('sbiGraphYear').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });
        sbi_chart()
    }

    function sbi_chart() {
        var estSidakYear = document.getElementById('estSidakYear');
        var est = estSidakYear.value;
        var estText = '';

        // Check if there are any options in the estSidakYear select element
        if (estSidakYear.options.length > 0) {
            estText = estSidakYear.options[estSidakYear.selectedIndex].text;
        }

        var tahun = document.getElementById('hiddenInput').value; // Get the value from the hidden input element

        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('chartsbi_oke') }}",
            method: "GET",
            data: {
                est: est,
                estText: estText,
                tahun: tahun, // Add the tahun value to the AJAX request
                _token: _token,
            },
            headers: {
                'X-CSRF-TOKEN': _token,
            },
            success: function(result) {

                Swal.close();
                var parseResult = JSON.parse(result)


                var chart_mentah = Object.entries(parseResult['chart_mentah'])
                var chart_lewatmatang = Object.entries(parseResult['chart_lewatmatang'])
                var chart_janjangkosong = Object.entries(parseResult['chart_janjangkosong'])
                var chart_vcut = Object.entries(parseResult['chart_vcut'])
                var chart_karung = Object.entries(parseResult['chart_karung'])

                var chart_matang = Object.entries(parseResult['chart_matang']);
                // console.log(chart_matang);

                var matang = '[';
                if (chart_matang.length > 0 && chart_matang[0].length > 0) {
                    var data = chart_matang[0][1];
                    matang += data.January + ',';
                    matang += data.February + ',';
                    matang += data.March + ',';
                    matang += data.April + ',';
                    matang += data.May + ',';
                    matang += data.June + ',';
                    matang += data.July + ',';
                    matang += data.August + ',';
                    matang += data.September + ',';
                    matang += data.October + ',';
                    matang += data.November + ',';
                    matang += data.December;
                }
                matang += ']';

                // console.log(matang);

                var mentah = '[';
                if (chart_mentah.length > 0 && chart_mentah[0].length > 0) {
                    var data = chart_mentah[0][1];
                    mentah += data.January + ',';
                    mentah += data.February + ',';
                    mentah += data.March + ',';
                    mentah += data.April + ',';
                    mentah += data.May + ',';
                    mentah += data.June + ',';
                    mentah += data.July + ',';
                    mentah += data.August + ',';
                    mentah += data.September + ',';
                    mentah += data.October + ',';
                    mentah += data.November + ',';
                    mentah += data.December;
                }
                mentah += ']';


                var lewatmatangs = '[';
                if (chart_lewatmatang.length > 0 && chart_lewatmatang[0].length > 0) {
                    var data = chart_lewatmatang[0][1];
                    lewatmatangs += data.January + ',';
                    lewatmatangs += data.February + ',';
                    lewatmatangs += data.March + ',';
                    lewatmatangs += data.April + ',';
                    lewatmatangs += data.May + ',';
                    lewatmatangs += data.June + ',';
                    lewatmatangs += data.July + ',';
                    lewatmatangs += data.August + ',';
                    lewatmatangs += data.September + ',';
                    lewatmatangs += data.October + ',';
                    lewatmatangs += data.November + ',';
                    lewatmatangs += data.December;
                }
                lewatmatangs += ']';

                var jjgkosongs = '[';
                if (chart_janjangkosong.length > 0 && chart_janjangkosong[0].length > 0) {
                    var data = chart_janjangkosong[0][1];
                    jjgkosongs += data.January + ',';
                    jjgkosongs += data.February + ',';
                    jjgkosongs += data.March + ',';
                    jjgkosongs += data.April + ',';
                    jjgkosongs += data.May + ',';
                    jjgkosongs += data.June + ',';
                    jjgkosongs += data.July + ',';
                    jjgkosongs += data.August + ',';
                    jjgkosongs += data.September + ',';
                    jjgkosongs += data.October + ',';
                    jjgkosongs += data.November + ',';
                    jjgkosongs += data.December;
                }
                jjgkosongs += ']';

                var vcuts = '[';
                if (chart_vcut.length > 0 && chart_vcut[0].length > 0) {
                    var data = chart_vcut[0][1];
                    vcuts += data.January + ',';
                    vcuts += data.February + ',';
                    vcuts += data.March + ',';
                    vcuts += data.April + ',';
                    vcuts += data.May + ',';
                    vcuts += data.June + ',';
                    vcuts += data.July + ',';
                    vcuts += data.August + ',';
                    vcuts += data.September + ',';
                    vcuts += data.October + ',';
                    vcuts += data.November + ',';
                    vcuts += data.December;
                }
                vcuts += ']';

                var karungs = '[';
                if (chart_karung.length > 0 && chart_karung[0].length > 0) {
                    var data = chart_karung[0][1];
                    karungs += data.January + ',';
                    karungs += data.February + ',';
                    karungs += data.March + ',';
                    karungs += data.April + ',';
                    karungs += data.May + ',';
                    karungs += data.June + ',';
                    karungs += data.July + ',';
                    karungs += data.August + ',';
                    karungs += data.September + ',';
                    karungs += data.October + ',';
                    karungs += data.November + ',';
                    karungs += data.December;
                }
                karungs += ']';


                var matang_chart = JSON.parse(matang)
                var mentah_chart = JSON.parse(mentah)

                var lwtmatang_chart = JSON.parse(lewatmatangs)
                var janjangksng_chart = JSON.parse(jjgkosongs)
                var vcuts_chart = JSON.parse(vcuts)
                var karungs_chart = JSON.parse(karungs)



                // console.log(matang_chart);

                // console.log(formatEst);


                // console.log(matang_chart);
                sbi1.updateSeries([{
                    name: 'matang',
                    data: matang_chart,

                }])

                ///////////
                sbi2.updateSeries([{
                    name: 'mentah',
                    data: mentah_chart,

                }])

                ///////////
                sbi3.updateSeries([{
                    name: 'lewat matang',
                    data: lwtmatang_chart,

                }])

                /////////
                sbi4.updateSeries([{
                    name: 'janjang kosong',
                    data: janjangksng_chart,

                }])

                ////////
                sbi5.updateSeries([{
                    name: 'vcut ',
                    data: vcuts_chart,

                }])

                ///////
                sbi6.updateSeries([{
                    name: 'karung',
                    data: karungs_chart,

                }])




            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle errors here
            },
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectedTab = localStorage.getItem('selectedTab');

        if (selectedTab) {
            // Get the tab element
            const tabElement = document.getElementById(selectedTab);

            if (tabElement) {
                // Click the tab to activate it
                tabElement.click();
            }

            // Clear the selectedTab value from local storage
            localStorage.removeItem('selectedTab');
        }
    });

    function getFindData() {
        $('#bodyIssue').empty()

        var regional = $("#regFind").val();
        var date = $("#dateFind").val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('findIssueSmb') }}",
            method: "POST",
            data: {
                regional: regional,
                date: date,
                _token: _token
            },
            success: function(result) {

                Swal.close();
                var parseResult = JSON.parse(result)
                var dataFinding = Object.entries(parseResult['dataFinding'])

                dataFinding.forEach(function(value, key) {
                    dataFinding[key].forEach(function(value1, key1) {
                        Object.entries(value1).forEach(function(value2, key2) {
                            if (value2[0] != 0) {
                                // console.log(value2)
                                var tbody1 = document.getElementById('bodyIssue');

                                tr = document.createElement('tr')

                                let item1 = value2[0]
                                let item2 = value2[1]['total_temuan']

                                let itemElement1 = document.createElement('td')
                                let itemElement2 = document.createElement('td')
                                let itemElement3 = document.createElement('td')

                                itemElement1.innerText = item1
                                itemElement2.innerText = item2
                                itemElement3.innerHTML = '<a href="/cetakFiSmb/' + value2[0] + '/' + date + '" class="btn btn-primary" target="_blank"><i class="nav-icon fa fa-download"></i></a>'

                                tr.appendChild(itemElement1)
                                tr.appendChild(itemElement2)
                                tr.appendChild(itemElement3)
                                tbody1.appendChild(tr)
                            }
                        });
                    });
                });
            },
            error: function(xhr, status, error) {
                Swal.close();
                // Handle the error here
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No data found.'
                });
                console.log("An error occurred:", error);
            }
        });
    }

    function sortTable(tableId, columnIndex, compareFunction, numRowsToSort, useSecondColumn = false) {
        const tbody = document.getElementById(tableId);
        const allRows = Array.from(tbody.rows);
        const rows = allRows.slice(0, numRowsToSort);
        const excludedRows = allRows.slice(numRowsToSort);

        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].innerText.toLowerCase();
            let bValue = b.cells[columnIndex].innerText.toLowerCase();

            if (useSecondColumn) {
                aValue += '|' + a.cells[columnIndex + 1].innerText.toLowerCase();
                bValue += '|' + b.cells[columnIndex + 1].innerText.toLowerCase();
            }

            let result = compareFunction(aValue, bValue);

            // If the values are equal, sort based on the name in column 2 (index 1)
            if (result === 0 && !useSecondColumn) {
                let nameA = a.cells[1].innerText.trim().toLowerCase();
                let nameB = b.cells[1].innerText.trim().toLowerCase();
                return nameA.localeCompare(nameB);
            }

            return result;
        });

        // Remove existing rows from the tbody
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        // Append sorted rows to the tbody
        rows.forEach(row => tbody.appendChild(row));

        // Append excluded rows to the tbody without sorting
        excludedRows.forEach(row => tbody.appendChild(row));
    }


    document.addEventListener('DOMContentLoaded', function() {
        const estBtn = document.getElementById('sort-est-btn');
        const rankBtn = document.getElementById('sort-rank-btn');
        const showBtn = document.getElementById('btnShow');
        const regionalSelect = document.getElementById('regionalPanen');

        let currentRegion = regionalSelect.value;

        let firstClick = true; // Add a flag to indicate the first click

        estBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('est');
        });
        rankBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('rank');
        });
        showBtn.addEventListener('click', handleShow);

        // Define the new handleShow function
        function handleShow() {
            currentRegion = regionalSelect.value;
            handleFilterShow(currentRegion);
        }

        function handleSort(sortType) {
            const sortMap = {
                '1': {
                    est: [16, 18, 17, 3],
                    rank: [16, 18, 17, 3]
                },
                '2': {
                    est: [16, 13, 10, 5],
                    rank: [16, 13, 10, 5]
                },
                '3': {
                    est: [20, 11, 2, 2],
                    rank: [20, 11, 2, 2]
                }
            };

            const tbodies = ['week1', 'week2', 'week3', 'plasma1'];

            const columnIndex = sortType === 'est' ? 0 : 4;
            const useSecondColumn = sortType === 'est';

            tbodies.forEach((tableId, index) => {
                if (sortType === 'rank') {
                    sortTable(tableId, columnIndex, (a, b) => parseInt(a) - parseInt(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                } else {
                    sortTable(tableId, columnIndex, (a, b) => a.localeCompare(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                }
            });

        }

        function handleFilterShow(filterShowValue) {
            // Implement your filtering logic here, if necessary
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const estBtn = document.getElementById('sort-est-btnWek');
        const rankBtn = document.getElementById('sort-rank-btnWek');
        const showBtn = document.getElementById('showTahung');
        const regionalSelect = document.getElementById('regionalData');

        let currentRegion = regionalSelect.value;

        let firstClick = true; // Add a flag to indicate the first click

        estBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('est');
        });
        rankBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('rank');
        });
        showBtn.addEventListener('click', handleShow);

        // Define the new handleShow function
        function handleShow() {
            currentRegion = regionalSelect.value;
            handleFilterShow(currentRegion);
        }

        function handleSort(sortType) {
            const sortMap = {
                '1': {
                    est: [16, 18, 17, 3],
                    rank: [16, 18, 17, 3]
                },
                '2': {
                    est: [16, 13, 10, 5],
                    rank: [16, 13, 10, 5]
                },
                '3': {
                    est: [20, 11, 2, 2],
                    rank: [20, 11, 2, 2]
                }
            };


            const tbodies2 = ['weeks1', 'weeks2', 'weeks3', 'plasmas1'];
            const columnIndex = sortType === 'est' ? 0 : 4;
            const useSecondColumn = sortType === 'est';

            tbodies2.forEach((tableId, index) => {
                if (sortType === 'rank') {
                    sortTable(tableId, columnIndex, (a, b) => parseInt(a) - parseInt(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                } else {
                    sortTable(tableId, columnIndex, (a, b) => a.localeCompare(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                }
            });
        }

        function handleFilterShow(filterShowValue) {
            // Implement your filtering logic here, if necessary
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const estBtn = document.getElementById('sort-est-btnSBI');
        const rankBtn = document.getElementById('sort-rank-btnSBI');
        const showBtn = document.getElementById('show_sbithn');
        const regionalSelect = document.getElementById('reg_sbiThun');

        let currentRegion = regionalSelect.value;

        let firstClick = true; // Add a flag to indicate the first click

        estBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('est');
        });
        rankBtn.addEventListener('click', () => {
            if (firstClick) {
                showBtn.click();
                firstClick = false; // Set the flag to false after the first click
            }
            handleSort('rank');
        });
        showBtn.addEventListener('click', handleShow);

        // Define the new handleShow function
        function handleShow() {
            currentRegion = regionalSelect.value;
            handleFilterShow(currentRegion);
        }

        function handleSort(sortType) {
            const sortMap = {
                '1': {
                    est: [16, 18, 17, 3],
                    rank: [16, 18, 17, 3]
                },
                '2': {
                    est: [16, 13, 10, 5],
                    rank: [16, 13, 10, 5]
                },
                '3': {
                    est: [20, 11, 2, 2],
                    rank: [20, 11, 2, 2]
                }
            };


            const tbodies2 = ['tahun1', 'tahun2', 'tahun3', 'tahun4'];
            const columnIndex = sortType === 'est' ? 0 : 4;
            const useSecondColumn = sortType === 'est';

            tbodies2.forEach((tableId, index) => {
                if (sortType === 'rank') {
                    sortTable(tableId, columnIndex, (a, b) => parseInt(a) - parseInt(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                } else {
                    sortTable(tableId, columnIndex, (a, b) => a.localeCompare(b), sortMap[currentRegion][sortType][index], useSecondColumn);
                }
            });
        }

        function handleFilterShow(filterShowValue) {
            // Implement your filtering logic here, if necessary
        }
    });

    // document.addEventListener("DOMContentLoaded", function() {
    //     // Get the value of the lokasiKerja variable from the session
    //     var lokasiKerja = "{{ session('lok') }}";

    //     // Set the default value for regionalData select field based on lokasiKerja
    //     var regionalDataSelect = document.getElementById("regionalData");
    //     regionalDataSelect.value = getRegionalValue(lokasiKerja);

    //     // Add event listener for the regionalData select field change
    //     regionalDataSelect.addEventListener("change", function() {
    //         // Retrieve the selected regional value
    //         var selectedRegionalValue = regionalDataSelect.value;

    //         // Update the value of the hidden input field
    //         document.getElementById("regPDF").value = selectedRegionalValue;
    //     });

    //     // Retrieve the value from the dateWeek input field
    //     var dateWeekInput = document.getElementById("dateWeek");
    //     var dateWeekValue = dateWeekInput.value;

    //     // Set the retrieved value to the hidden input field
    //     document.getElementById("tglPDF").value = dateWeekValue;

    //     // Submit the form when the page is first loaded
    //     submitForm();
    // });

    // Function to submit the form
    // function submitForm() {
    //     // Retrieve the selected regional value
    //     var selectedRegionalValue = document.getElementById("regionalData").value;

    //     // Update the value of the hidden input field
    //     document.getElementById("regPDF").value = selectedRegionalValue;

    //     // Retrieve the value from the dateWeek input field
    //     var dateWeekValue = document.getElementById("dateWeek").value;

    //     // Set the retrieved value to the hidden input field
    //     document.getElementById("tglPDF").value = dateWeekValue;

    //     // Submit the form
    //     document.getElementById("download-button").click();
    // }

    // function getRegionalValue(lokasiKerja) {
    //     // Define the mapping of lokasiKerja to regional values
    //     var regionalMapping = {
    //         "Regional I": "1",
    //         "Regional II": "2",
    //         "Regional III": "3",
    //         "Regional IV": "4"
    //     };

    //     // Return the regional value based on lokasiKerja
    //     return regionalMapping[lokasiKerja] || "1"; // Default to "1" if lokasiKerja is not found
    // }
    var downloadButton = document.getElementById("download-button");
    downloadButton.disabled = true;
    downloadButton.classList.add("disabled");

    // Enable PDF download button when Show button is clicked
    document.getElementById("showTahung").addEventListener("click", function() {
        var weekDate = document.getElementById("dateWeek").value;
        var selectedRegion = document.getElementById("regionalData").value; // Get the selected value

        document.getElementById("tglPDF").value = weekDate;
        document.getElementById("startWeek").value = weekDate;
        document.getElementById("lastWeek").value = weekDate;
        document.getElementById("regPDF").value = selectedRegion;
        // Enable PDF download button
        downloadButton.disabled = false;
        downloadButton.classList.remove("disabled");
    });
</script>