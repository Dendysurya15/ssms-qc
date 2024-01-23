@include('layout/header')
<style>
  table.dataTable thead tr th {
    border: 1px solid black
  }

  .dataTables_scrollBody thead tr[role="row"] {
    visibility: collapse !important;
  }

  .dataTables_scrollHeaderInner table {
    margin-bottom: 0px !important;
  }

  .detailBa {
    color: black;
  }

  .detailBa:hover {
    color: rgb(0, 255, 0);
  }

  .tbl-fixed {
    overflow-y: scroll;
    height: fit-content;
    max-height: 70vh;
    border: 1px solid #777777;
  }

  .tbl-fixed table {
    border-spacing: 0;
    font-size: 15px;
  }

  .tbl-fixed th {
    border: 1px solid #bbbbbb;
  }

  .tbl-fixed thead {
    position: sticky;
    top: 0px;
    z-index: 2;
  }

  .tbl-fixed td {
    border: 1px solid #bbbbbb;
    padding: 5px;
  }

  .tbl-fixed td:nth-child(1) {
    position: sticky;
    left: -0.1%;
  }

  .tbl-fixed td:nth-child(2) {
    position: sticky;
    left: 2%;
    width: 50px;
    min-width: 50px;
  }

  .tbl-fixed td:nth-child(1),
  .tbl-fixed td:nth-child(2) {
    background: #cfcfcf;
  }

  @media (max-width: 360px) {
    .table-sticky-header {
      width: 100%;
      margin: auto;
    }

    .table-responsive {
      overflow-x: auto;
      margin: auto;
      position: relative;
    }

    .table-responsive::-webkit-scrollbar {
      height: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
      background-color: rgba(0, 0, 0, 0.2);
    }

    .table-responsive .first-col {
      position: sticky;
      left: 0;
      background-color: #f8f9fa;
      z-index: 1;
    }

    .second-col {
      position: sticky;
      left: 50px;
      /* Adjust this value based on the width of your first column */
      background-color: #cfcfcf;
      z-index: 1;
    }
  }

  .filter-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .date-filter,
  .regional-filter,
  .show-button {
    margin-left: 5px;
    margin-right: 18px;
  }

  .show-button {
    margin-bottom: 2px;
  }

  @media (max-width: 767px) {

    .date-filter,
    .regional-filter {
      margin-right: 30px;
    }

    .show-button {
      flex-basis: 100%;
      max-width: 100%;
      margin-bottom: 0px;
      margin-left: 10px;
    }
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
<div class="content-wrapper">
  <section class="content"><br>
    <div class="container-fluid">
      <div class="card table_wrapper">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Halaman Utama</a>
            <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Data</a>
            <a class="nav-item nav-link" id="nav-sbi-tab" data-toggle="tab" href="#nav-sbi" role="tab" aria-controls="nav-sbi" aria-selected="false">SBI</a>
          </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
            <div class="d-flex justify-content-center mt-3 mb-3 ml-3 mr-3 border border-dark">
              <h5><b>REKAPITULASI RANKING NILAI SIDAK PEMERIKSAAN TPH</b></h5>
            </div>

            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
              <div class="row w-100">
                <div class="col-md-2 offset-md-8">
                  {{csrf_field()}}
                  <select class="form-control" name="regionalSidakMonth" id="regionalSidakMonth">
                    @foreach($option_reg as $key => $item)
                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                  {{ csrf_field() }}
                  <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputDateMonth" id="inputDateMonth">

                </div>
              </div>
              <button class="btn btn-primary mb-3 ml-3" id="btnShowMonth">Show</button>
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
              <button id="sort-est-btn">sort by Afd</button>
              <button id="sort-rank-btn">Sort by Rank</button>
              <button onclick="openNewTabAndSendData()" id="downladbulan">Download As IMG</button>
            </div>
            <div id="tablesContainer">
              <div class="tabContainer">
                <div class="ml-3 mr-3">
                  <div class="row justify-content-center">
                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="table1Month">
                      <div class="table-responsive">
                        <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table1">
                          <thead>
                            <tr bgcolor="yellow">
                              <th colspan="5" id="thWilOneMonth">WILAYAH I</th>
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
                          <tbody id="tbody1Month">
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classTwoMonth">
                      <div class="table-responsive">
                        <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table2">
                          <thead>
                            <tr bgcolor="yellow">
                              <th colspan="5" id="thWilTwoMonth">WILAYAH II</th>
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
                          <tbody id="tbody2Month">

                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classThreeMonth">
                      <div class="table-responsive">
                        <table class="table table-bordered" style="font-size: 13px;background-color:white" id="table3">
                          <thead>
                            <tr bgcolor="yellow">
                              <th colspan="5" id="thWilThreeMonth">WILAYAH III</th>
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
                          <tbody id="tbody3Month">

                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classFourMonth">
                      <div class="table-responsive">
                        <table class="table table-bordered" style="font-size: 13px;background-color:white" id="table4">
                          <thead>
                            <tr bgcolor="yellow">
                              <th colspan="5" id="thPlasmamonth">PLASMA</th>
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
                          <tbody id="plasmaMonth">

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
                <thead id="tbodySkorRHMonth">
                </thead>
              </table>
            </div>

            <div id="accordion" class="ml-3 mr-3">
              <div class="card">
                <button class="btn btn-secondary text-uppercase" data-toggle="collapse" data-target="#graphEstMonth" aria-expanded="false" aria-controls="graphEstMonth">
                  Grafik Sidak TPH berdasarkan Estate
                </button>
                <div id="graphEstMonth" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                              <b>Brondolan Tinggal (Brondol / Blok)</b>
                            </p>
                            <div id="bttinggalMonth"></div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Karung
                                Berisi Brondolan (Karung / Blok)</b>
                            <div id="karungMonth"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Buah
                                Tinggal (Janjang / Blok)</b>
                            </p>
                            <div id="btt_tglMonth"></div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Restan
                                Tidak Dilaporkan (Janjang / Blok)</b>
                            <div id="rst_noneMonth"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="accordion" class="ml-3 mr-3">
              <div class="card">
                <button class="btn btn-secondary text-uppercase" data-toggle="collapse" data-target="#graphWilMonth" aria-expanded="false" aria-controls="graphWilMonth">
                  Grafik Sidak TPH berdasarkan Wilayah
                </button>
                <div id="graphWilMonth" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                              <b>Brondolan Tinggal (Brondol / Blok)</b>
                            </p>
                            <div id="btt_idMonth"></div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Karung
                                Berisi Brondolan (Karung / Blok)</b>
                            <div id="karung_idMonth"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Buah
                                Tinggal (Janjang / Blok)</b>
                            </p>
                            <div id="bttTglTph_idMonth"></div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="card">
                          <div class="card-body">
                            <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Restan
                                Tidak Dilaporkan (Janjang / Blok)</b>
                            <div id="rst_none_idMonth"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="ml-3 mb-3 mr-3">
              <button style="width: 100%" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#showByWeek" aria-expanded="false" aria-controls="showByWeek">
                TAMPILKAN PER MINGGU
              </button>
            </p>

            <div class="collapse" id="showByWeek">
              <div class="d-flex justify-content-center ml-3 mr-3 border border-dark">
                <h5><b>REKAPITULASI RANKING NILAI SIDAK PEMERIKSAAN TPH</b></h5>
              </div>
              <style>
                /* Custom button height */
                .custom-btn-height {
                  height: calc(1.5em + .75rem + 2px);
                  /* Adjust this value according to your desired height */
                }

                /* CSS for mobile view */
                @media (max-width: 767.98px) {
                  .mobile-view .form-container {
                    margin-bottom: 10px;
                  }
                }
              </style>

              <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                <div class="row w-100 mobile-view">
                  <div class="col-lg-2 col-md-4 col-sm-6 col-6 offset-lg-8 offset-md-4 offset-sm-0 offset-3 form-container">
                    {{csrf_field()}}
                    <select class="form-control" name="regionalSidak" id="regionalSidak">
                      @foreach($option_reg as $key => $item)
                      <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-lg-2 col-md-4 col-sm-6 col-6 form-container">
                    {{ csrf_field() }}
                    <input class="form-control" type="week" name="dateWeek" id="dateWeek" value="{{ date('Y') . '-W' . date('W') }}">
                  </div>
                </div>
                <button class="btn btn-primary mb-3 ml-3 custom-btn-height" id="btnShow">Show</button>
                <button class="btn btn-primary mb-3 ml-3 custom-btn-height" id="btnExport"><i class="fa fa-file-pdf"></i> Download PDF</button>
                <input class="form-control" type="hidden" id="startWeek" name="start" value="">
                <input class="form-control" type="hidden" id="lastWeek" name="last" value="">
                <input class="form-control" type="hidden" id="regional" name="regional" value="">
              </div>




              <style>
                @media (min-width: 992px) {
                  .d-flex.flex-row-reverse .col-2 {
                    flex: 0 0 16.66667%;
                    max-width: 16.66667%;
                    float: right;
                  }
                }
              </style>
              <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 ">
                <button id="sort-afd-btnWeek">sort by Afd</button>
                <button id="sort-est-btnWeek">Sort by Rank</button>
              </div>
              <div id="tablesContainer">
                <div class="tabContainer">
                  <div class="ml-3 mr-3">
                    <div class="row justify-content-center">
                      <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classOne">
                        <div class="table-responsive">
                          <table class=" table table-bordered" style="font-size: 13px" id="table1">
                            <thead>
                              <tr bgcolor="yellow">
                                <th colspan="5" id="thWilOne">WILAYAH I</th>
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
                            <tbody id="tbody1">
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classTwo">
                        <div class="table-responsive">
                          <table class=" table table-bordered" style="font-size: 13px" id="table1">
                            <thead>
                              <tr bgcolor="yellow">
                                <th colspan="5" id="thWilTwo">WILAYAH II</th>
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
                            <tbody id="tbody2">

                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classThree">
                        <div class="table-responsive">
                          <table class="table table-bordered" style="font-size: 13px" id="Reg3">
                            <thead>
                              <tr bgcolor="yellow">
                                <th colspan="5" id="thWilThree">WILAYAH III</th>
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
                            <tbody id="tbody3">

                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-3" data-regional="1" id="classFour">
                        <div class="table-responsive">
                          <table class="table table-bordered" style="font-size: 13px" id="plasmaID">
                            <thead>
                              <tr bgcolor="yellow">
                                <th colspan="5" id="thwillPlas">PLASMAa</th>
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
                            <tbody id="plasma">

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
                  <thead id="tbodySkorRH">
                  </thead>
                </table>
              </div>


              <div id="accordion" class="ml-3 mr-3">
                <div class="card">
                  <button class="btn btn-secondary text-uppercase" style="width: 100%;" data-toggle="collapse" data-target="#graphEstWeek" aria-expanded="false" aria-controls="graphEstWeek">
                    Grafik Sidak TPH berdasarkan Estate
                  </button>
                  <div id="graphEstWeek" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                                <b>Brondolan Tinggal (Brondol / Blok)</b>
                              </p>
                              <div id="bttinggal"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Karung
                                  Berisi Brondolan (Karung / Blok)</b>
                              <div id="karung"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Buah
                                  Tinggal (Janjang / Blok)</b>
                              </p>
                              <div id="btt_tgl"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Restan
                                  Tidak Dilaporkan (Janjang / Blok)</b>
                              <div id="rst_none"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="accordion" class="ml-3 mr-3">
                <div class="card">
                  <button class="btn btn-secondary text-uppercase" style="width: 100%;" data-toggle="collapse" data-target="#graphWilWeek" aria-expanded="false" aria-controls="graphWilWeek">
                    Grafik Sidak TPH berdasarkan Wilayah
                  </button>
                  <div id="graphWilWeek" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                                <b>Brondolan Tinggal (Brondol / Blok)</b>
                              </p>
                              <div id="btt_id"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Karung
                                  Berisi Brondolan (Karung / Blok)</b>
                              <div id="karung_id"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Buah
                                  Tinggal (Janjang / Blok)</b>
                              </p>
                              <div id="bttTglTph_id"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="card">
                            <div class="card-body">
                              <p style="font-size: 15px; text-align: center;" class="text-uppercase"><b>Restan
                                  Tidak Dilaporkan (Janjang / Blok)</b>
                              <div id="rst_none_id"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
              <h5><b>DATA</b></h5>
            </div>

            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
              <div class="row w-100">
                <div class="col-md-2 offset-md-8">
                  {{csrf_field()}}
                  <select class="form-control" id="regDataTph">
                    @foreach($option_reg as $key => $item)
                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                  {{ csrf_field() }}
                  <input class="form-control" value="{{ date('Y-m') }}" type="month" name="tgl" id="dateDataTph">

                </div>
              </div>
              <button class="btn btn-primary mb-3 ml-3" id="showDataTph">Show</button>
            </div>




            <div class="ml-3 mr-3 mb-3">
              <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="week1-tab" data-toggle="tab" href="#week1" role="tab" aria-controls="week1" aria-selected="true">week1</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="week2-tab" data-toggle="tab" href="#week2" role="tab" aria-controls="week2" aria-selected="false">week2</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="week3-tab" data-toggle="tab" href="#week3" role="tab" aria-controls="week3" aria-selected="false">week3</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="week4-tab" data-toggle="tab" href="#week4" role="tab" aria-controls="week4" aria-selected="false">week4</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="month-tab" data-toggle="tab" href="#month" role="tab" aria-controls="month" aria-selected="false">week5</a>
                </li>
              </ul>
              <div class="tab-content" id="myTabsContent">
                <style>
                  #newweek1 thead {
                    background-color: white;
                  }

                  #newweek2 thead {
                    background-color: white;
                  }

                  #newweek3 thead {
                    background-color: white;
                  }

                  #newweek4 thead {
                    background-color: white;
                  }

                  #newweek5 thead {
                    background-color: white;
                  }

                  feFlood
                </style>

                <!-- mingg pertama  -->
                <div class="tab-pane fade show active" id="week1" role="tabpanel" aria-labelledby="week1-tab">
                  <div class="row text-center tbl-fixed">
                    <table style="width: 100%;" id="newweek1">
                      <thead>
                        <tr>
                          <th rowspan="3">EST</th>
                          <th rowspan="3">AFD</th>
                          <th colspan="10"> H+1</th>
                          <th colspan="10"> H+2</th>
                          <th colspan="10"> H+3</th>
                          <th colspan="10"> H+4</th>
                          <th colspan="10"> H+5</th>
                          <th colspan="10"> H+6</th>
                          <th colspan="10"> H+7</th>
                          <th colspan="10"> >H+7 </th>
                          <th rowspan="3"> All Skor</th>
                          <th rowspan="3"> Kategori</th>
                        </tr>
                        <tr>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                        </tr>
                        <tr>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>

                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- minggu ke dua  -->
                <div class="tab-pane fade" id="week2" role="tabpanel" aria-labelledby="week2-tab">
                  <div class="row text-center tbl-fixed">
                    <table style="width: 100%;" id="newweek2">
                      <thead>
                        <tr>
                          <th rowspan="3">EST</th>
                          <th rowspan="3">AFD</th>
                          <th colspan="10"> H+1</th>
                          <th colspan="10"> H+2</th>
                          <th colspan="10"> H+3</th>
                          <th colspan="10"> H+4</th>
                          <th colspan="10"> H+5</th>
                          <th colspan="10"> H+6</th>
                          <th colspan="10"> H+7</th>
                          <th colspan="10"> >H+7 </th>
                          <th rowspan="3"> All Skor</th>
                          <th rowspan="3"> Kategori</th>
                        </tr>
                        <tr>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                        </tr>
                        <tr>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>

                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- minggu ke 3  -->
                <div class="tab-pane fade" id="week3" role="tabpanel" aria-labelledby="week3-tab">
                  <div class="row text-center tbl-fixed">
                    <table style="width: 100%;" id="newweek3">
                      <thead>
                        <tr>
                          <th rowspan="3">EST</th>
                          <th rowspan="3">AFD</th>
                          <th colspan="10"> H+1</th>
                          <th colspan="10"> H+2</th>
                          <th colspan="10"> H+3</th>
                          <th colspan="10"> H+4</th>
                          <th colspan="10"> H+5</th>
                          <th colspan="10"> H+6</th>
                          <th colspan="10"> H+7</th>
                          <th colspan="10"> >H+7 </th>
                          <th rowspan="3"> All Skor</th>
                          <th rowspan="3"> Kategori</th>
                        </tr>
                        <tr>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                        </tr>
                        <tr>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>

                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>

                </div>

                <!-- minggu ke 4  -->
                <div class="tab-pane fade" id="week4" role="tabpanel" aria-labelledby="week4-tab">
                  <div class="row text-center tbl-fixed">
                    <table style="width: 100%;" id="newweek4">
                      <thead>
                        <tr>
                          <th rowspan="3">EST</th>
                          <th rowspan="3">AFD</th>
                          <th colspan="10"> H+1</th>
                          <th colspan="10"> H+2</th>
                          <th colspan="10"> H+3</th>
                          <th colspan="10"> H+4</th>
                          <th colspan="10"> H+5</th>
                          <th colspan="10"> H+6</th>
                          <th colspan="10"> H+7</th>
                          <th colspan="10"> >H+7 </th>
                          <th rowspan="3"> All Skor</th>
                          <th rowspan="3"> Kategori</th>
                        </tr>
                        <tr>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                        </tr>
                        <tr>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>

                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>
                </div>


                <!-- bulan  -->
                <div class="tab-pane fade" id="month" role="tabpanel" aria-labelledby="month-tab">
                  <div class="row text-center tbl-fixed">
                    <table style="width: 100%;" id="newweek5">
                      <thead>
                        <tr>
                          <th rowspan="3">EST</th>
                          <th rowspan="3">AFD</th>
                          <th colspan="10"> H+1</th>
                          <th colspan="10"> H+2</th>
                          <th colspan="10"> H+3</th>
                          <th colspan="10"> H+4</th>
                          <th colspan="10"> H+5</th>
                          <th colspan="10"> H+6</th>
                          <th colspan="10"> H+7</th>
                          <th colspan="10"> >H+7 </th>
                          <th rowspan="3"> All Skor</th>
                          <th rowspan="3"> Kategori</th>
                        </tr>
                        <tr>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                          <th colspan="6">Brondolan Tinggal</th>
                          <th colspan="4">Buah Tinggal</th>
                        </tr>
                        <tr>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>
                          <th> Di TPH</th>
                          <th> Di Jalan</th>
                          <th> Di Bin</th>
                          <th> Di Karung</th>
                          <th> Total Brd</th>
                          <th> Skor</th>
                          <th> Buah Sortiran / Buah Jatuh </th>
                          <th>Restan Tidak Dilaporkan </th>
                          <th>Total Jjg </th>
                          <th>Skor</th>

                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                    </table>
                  </div>

                </div>

              </div>
            </div>




          </div>

          <div class="tab-pane fade" id="nav-sbi" role="tabpanel" aria-labelledby="nav-sbi-tab">
            <div class="d-flex justify-content-center mt-3 mb-3 ml-3 mr-3 border border-dark">
              <h5><b>REKAPITULASI RANKING NILAI SIDAK PEMERIKSAAN TPH</b></h5>
            </div>

            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
              <div class="row w-100">
                <div class="col-md-2 offset-md-8">
                  {{csrf_field()}}
                  <select class="form-control" name="regionalSidakYear" id="regionalSidakYear">
                    @foreach($option_reg as $key => $item)
                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                  {{ csrf_field() }}
                  <select class="form-control" name="inputYear" id="inputYear">
                    @foreach ($optYear as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                  </select>

                </div>
              </div>
              <button class="btn btn-primary mb-3 ml-3" id="btnShowYear">Show</button>
            </div>

            <div id="tablesContainer">
              <div class="tabContainer">
                <div class="ml-3 mr-3">
                  <div class="row text-center">
                    <div class="col-12 col-md-6 col-lg-4" id="Tab1">
                      <div class="table-responsive">
                        <table class=" table table-bordered" style="font-size: 13px" id="table1">
                          <thead>
                            <tr bgcolor="darkorange">
                              <th colspan="5" id="thead1">WILAYAH I</th>
                            </tr>
                            <tr bgcolor="#2044a4" style="color: white">
                              <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                              <th rowspan="2" style="vertical-align: middle;">AFD</th>
                              <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                              <th colspan="2" class="text-center">Todate</th>
                            </tr>
                            <tr bgcolor="darkblue" style="color: white">
                              <th>Score</th>
                              <th>Rank</th>
                            </tr>
                          </thead>
                          <tbody id="tbody1Year">
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4" id="Tab2">
                      <div class="table-responsive">
                        <table class=" table table-bordered" style="font-size: 13px" id="table1">
                          <thead>
                            <tr bgcolor="darkorange">
                              <th colspan="5" id="thead2">WILAYAH II</th>
                            </tr>
                            <tr bgcolor="#2044a4" style="color: white">
                              <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                              <th rowspan="2" style="vertical-align: middle;">AFD</th>
                              <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                              <th colspan="2" class="text-center">Todate</th>
                            </tr>
                            <tr bgcolor="darkblue" style="color: white">
                              <th>Score</th>
                              <th>Rank</th>
                            </tr>
                          </thead>
                          <tbody id="tbody2Year">

                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4" id="Tab3">
                      <div class="table-responsive">
                        <table class="table table-bordered" style="font-size: 13px" id="Reg3">
                          <thead>
                            <tr bgcolor="darkorange">
                              <th colspan="5" id="thead3">WILAYAH III</th>
                            </tr>
                            <tr bgcolor="#2044a4" style="color: white">
                              <th rowspan="2" class="text-center" style="vertical-align: middle;">KEBUN</th>
                              <th rowspan="2" style="vertical-align: middle;">AFD</th>
                              <th rowspan="2" style="text-align:center; vertical-align: middle;">Nama</th>
                              <th colspan="2" class="text-center">Todate</th>
                            </tr>
                            <tr bgcolor="darkblue" style="color: white">
                              <th>Score</th>
                              <th>Rank</th>
                            </tr>
                          </thead>
                          <tbody id="tbody3Year">

                          </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-sm-12">
                <table class="table table-bordered">
                  <thead id="tbodySkorRHYear">
                  </thead>
                </table>
              </div>
            </div>


            <div class="d-flex justify-content-center mb-3 ml-3 mr-3 border border-dark text-uppercase">
              <h5><b>GRAFIK REKAPITULASI SIDAK PEMERIKSAAN TPH</b></h5>
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
              <button class="btn btn-primary mb-3 mr-2" style="float: right" id="showGraphYear">Show</button>
              <div class="form-container mr-2">
                {{ csrf_field() }}
                <select class="form-control" name="estSidakYear" id="estSidakYear">
                </select>
              </div>
            </div>


            <div class="row ml-2 mr-2">
              <div class="col-sm-6">
                <div class="card">
                  <div class="card-body">
                    <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                      <b>Total Brondolan Tinggal</b>
                    </p>
                    <div id="bttinggalYear"></div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <div class="card-body">
                    <p style="font-size: 15px; text-align: center;" class="text-uppercase">
                      <b>Total Buah Tinggal</b>
                    <div id="karungYear"></div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
  </section>

  @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep')
  <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmationModalLabel">Terdeteksi data duplicate</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-primary">
            <thead>
              <tr>
                <th>id</th>
                <th>Estate</th>
                <th>Afdeling</th>
                <th>blok</th>
                <th>datetime</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($check_data as $items)
              <tr>
                <td>{{$items['id']}}</td>
                <td>{{$items['est']}}</td>
                <td>{{$items['afd']}}</td>
                <td>{{$items['blok']}}</td>
                <td>{{$items['datetime']}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          Apakah anda ingin menghapus?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary" id="confirmBtn">Yes</button>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
<div id="lottie-container" style="display: none; width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: 9999; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
  <div id="lottie-animation" style="width: 200px; height: 200px;"></div>
</div>

@include('layout/footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>

<script>
  let checkdata = @json($check);
  let recordsdupt = @json($idduplicate);
  if (checkdata === 'ada') {
    // Show Bootstrap modal
    $('#confirmationModal').modal('show');

    // Attach a click event to the "Yes" button
    $('#confirmBtn').on('click', function() {
      // User clicked 'Yes', proceed with your actions
      // console.log('User clicked Yes, proceed with AJAX request here');
      // console.log(recordsdupt);

      // Hide the Bootstrap modal
      var _token = $('input[name="_token"]').val();
      let type = 'sidaktph'

      $.ajax({
        url: '{{ route("duplicatesidakmtb") }}', // Replace with your actual endpoint URL
        type: 'post',
        data: {
          data: recordsdupt,
          type: type,
        },
        headers: {
          'X-CSRF-TOKEN': _token
        },
        success: function(response) {
          if (response.success) {
            // Show success alert
            alert('Data berhasil dihapus');
            // Reload the page
            location.reload();
          } else {
            // Show error alert
            alert('Gagal menghapus data');
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
        }
      });

      $('#confirmationModal').modal('hide');
    });
  }






  $(document).ready(function() {
    const estDataMapSelect = document.querySelector('#estSidakYear');
    const regDataMapSelect = document.querySelector('#regionalSidakYear');

    ///membuat temp data value 0 untuk chart ketika ganti tanggal tidak ada data
    var list_estate = <?php echo json_encode($list_estate); ?>;
    var list_wilayah = <?php echo json_encode($list_wilayah); ?>;
    var list_month = <?php echo json_encode($list_month); ?>;

    //buat grafik temporary untuk value 0 untuk estate
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
        '#9208FD'
      ],
      stroke: {
        curve: 'smooth'
      },
      xaxis: {
        labels: {
          rotate: -50,
          rotateAlways: true,
        },
        type: '',
        categories: list_estate
      }
    };
    //buat grafik dengan value 0 dengan wilayah
    var will = {
      series: [{
        name: '',
        data: [0, 0, 0]
      }],
      chart: {
        height: 250,
        background: '#E2EAEA',
        type: 'bar'
      },
      plotOptions: {
        bar: {
          // horizontal: false
          distributed: true
        }
      },
      colors: ['#E6F011', '#0F0F0E', '#0068A3'],
      stroke: {
        curve: 'smooth'
      },
      xaxis: {
        type: 'string',
        categories: list_wilayah
      }
    };
    // Options List Month
    var lbMonth = {
      series: [{
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
        '#6fda7e',
        '#2596be',
        '#D04545',
        '#D0bf45',
        '#B3D816',
        '#16d8b9',
        '#1683D8',
        '#0d3df1',
        '#950df1',
        '#F10d85',
        '#2b516b',
        '#339691',
        '#3c9c5a'
      ],
      stroke: {
        curve: 'smooth'
      },
      xaxis: {
        labels: {
          rotate: -50,
          rotateAlways: true,
        },
        type: '',
        categories: list_month
      }
    };

    //render chart perestate temporary/ 0 value
    var renderChartTph = new ApexCharts(document.querySelector("#bttinggal"), options);
    renderChartTph.render();

    var renderChartKarung = new ApexCharts(document.querySelector("#karung"), options);
    renderChartKarung.render();

    var renderChartBuahTglTph = new ApexCharts(document.querySelector("#btt_tgl"), options);
    renderChartBuahTglTph.render();

    var renderChartBuahRestanNone = new ApexCharts(document.querySelector("#rst_none"), options);
    renderChartBuahRestanNone.render();

    //render chart perwilayah temporary /0 value
    var will_btt = new ApexCharts(document.querySelector("#btt_id"), will);
    will_btt.render();

    var renderChartKarungWil = new ApexCharts(document.querySelector("#karung_id"), will);
    renderChartKarungWil.render();

    var renderChartBuahTglTphWil = new ApexCharts(document.querySelector("#bttTglTph_id"), will);
    renderChartBuahTglTphWil.render();

    var renderChartBuahRestanNoneWil = new ApexCharts(document.querySelector("#rst_none_id"), will);
    renderChartBuahRestanNoneWil.render();

    // Render Chart Month
    var renderChartTphMonth = new ApexCharts(document.querySelector("#bttinggalMonth"), options);
    renderChartTphMonth.render();
    var renderChartKarungMonth = new ApexCharts(document.querySelector("#karungMonth"), options);
    renderChartKarungMonth.render();
    var renderChartBuahTglTphMonth = new ApexCharts(document.querySelector("#btt_tglMonth"), options);
    renderChartBuahTglTphMonth.render();
    var renderChartBuahRestanNoneMonth = new ApexCharts(document.querySelector("#rst_noneMonth"), options);
    renderChartBuahRestanNoneMonth.render();

    //render chart perwilayah temporary /0 value
    var will_bttMonth = new ApexCharts(document.querySelector("#btt_idMonth"), will);
    will_bttMonth.render();
    var renderChartKarungWilMonth = new ApexCharts(document.querySelector("#karung_idMonth"), will);
    renderChartKarungWilMonth.render();
    var renderChartBuahTglTphWilMonth = new ApexCharts(document.querySelector("#bttTglTph_idMonth"), will);
    renderChartBuahTglTphWilMonth.render();
    var renderChartBuahRestanNoneWilMonth = new ApexCharts(document.querySelector("#rst_none_idMonth"),
      will);
    renderChartBuahRestanNoneWilMonth.render();

    // Render Chart Year
    var renderChartTphYear = new ApexCharts(document.querySelector("#bttinggalYear"), lbMonth);
    renderChartTphYear.render();
    var renderChartKarungYear = new ApexCharts(document.querySelector("#karungYear"), lbMonth);
    renderChartKarungYear.render();

    var lokasiKerja = "{{ session('lok') }}";
    // console.log(lokasiKerja);
    if (lokasiKerja == 'Regional II' || lokasiKerja == 'Regional 2') {
      $('#regionalSidak').val('2');
      $('#regionalSidakMonth').val('2');
      $('#regionalSidakYear').val('2');
      $('#regDataTph').val('2');
    } else if (lokasiKerja == 'Regional III' || lokasiKerja == 'Regional 3') {
      $('#regionalSidak').val('3');
      $('#regionalSidakMonth').val('3');
      $('#regionalSidakYear').val('3');
      $('#regDataTph').val('3');
    } else if (lokasiKerja == 'Regional IV' || lokasiKerja == 'Regional 4') {
      $('#regionalSidak').val('4');
      $('#regionalSidakMonth').val('4');
      $('#regionalSidakYear').val('4');
      $('#regDataTph').val('4');
    }
    getDataTph()
    getDataTphMonth()
    getDataTphYear()
    changeData()
    fetchEstates(regDataMapSelect.value)



    $("#btnShow").click(function() {
      $('#tbody1').empty()
      $('#tbody2').empty()
      $('#tbody3').empty()
      $('#plasma').empty()
      $('#tbodySkorRH').empty()
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      getDataTph()
    });

    $("#btnShowMonth").click(function() {
      $('#tbody1Month').empty()
      $('#tbody2Month').empty()
      $('#tbody3Month').empty()
      $('#plasmaMonth').empty()
      $('#tbodySkorRHMonth').empty()
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      getDataTphMonth()
    });


    $("#btnShowYear").click(function() {
      $('#tbody1Year').empty()
      $('#tbody2Year').empty()
      $('#tbody3Year').empty()
      $('#tbodySkorRHYear').empty()
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      getDataTphYear()
    });

    $("#showDataTph").click(function() {
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      changeData()
    });

    $("#showGraphYear").click(function() {
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      graphFilterYear()
    });

    setTimeout(function() {
      Swal.fire({
        title: 'Loading',
        html: '<span class="loading-text">Mohon Tunggu...</span>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      $("#showGraphYear").click();
    }, 1000);

    regDataMapSelect.addEventListener('change', function() {
      const selectedOptionValue = this.value;
      fetchEstates(selectedOptionValue);
    });

    function fetchEstates(region) {
      var _token = $('input[name="_token"]').val();
      // Fetch the estates for the selected region and update the estate filter
      $.ajax({
        url: "{{ route('changeRegionEst') }}",
        method: "POST",
        data: {
          region: region,
          _token: _token
        },
        success: function(result) {
          estDataMapSelect.innerHTML = '';
          result.estates.forEach(function(estate) {
            const option = document.createElement('option');
            option.value = estate;
            option.textContent = estate;
            estDataMapSelect.appendChild(option);
          });
          estDataMapSelect.disabled = false;
        }
      });
    }

    function changeData() {
      var regTph = $("#regDataTph").val();
      var dateTph = $("#dateDataTph").val();
      var _token = $('input[name="_token"]').val();
      if ($.fn.DataTable.isDataTable('#newweek1')) {
        $('#newweek1').DataTable().destroy();
      }
      if ($.fn.DataTable.isDataTable('#newweek2')) {
        $('#newweek2').DataTable().destroy();
      }
      if ($.fn.DataTable.isDataTable('#newweek3')) {
        $('#newweek3').DataTable().destroy();
      }
      if ($.fn.DataTable.isDataTable('#newweek4')) {
        $('#newweek4').DataTable().destroy();
      }
      if ($.fn.DataTable.isDataTable('#newweek5')) {
        $('#newweek5').DataTable().destroy();
      }
      $.ajax({
        url: "{{ route('changeDataTph') }}",
        method: "GET",
        cache: false,
        data: {
          _token: _token,
          regional: regTph,
          date: dateTph
        },
        success: function(result) {
          Swal.close();
          var parseResult = JSON.parse(result)

          var datatableweek1 = $('#newweek1').DataTable({
            "iDisplayLength": 100,
            columns: [{
                data: 'est'
              },
              {
                data: 'afd',
                render: function(data, type, row) {
                  if (row.afd === 'EST') {



                    var linkText = 'Link';
                    var linkUrl = 'BaSidakTPH/' + row.est + '/' + row.start + '/' + row.end + '/' + row.reg;
                    return '<a href="' + linkUrl + '">' + row.afd + '</a>';
                  } else {

                    return row.afd;
                  }
                }
              },

              {
                data: 'tph1'
              },
              {
                data: 'jalan1'
              },
              {
                data: 'bin1'
              },
              {
                data: 'karung1'
              },

              {
                data: 'tot_brd1'
              },
              {
                data: 'skor_brd1'
              },
              {
                data: 'buah1'
              },
              {
                data: 'restan1'
              },
              {
                data: 'tod_jjg1'
              },
              {
                data: 'skor_janjang1'
              },


              {
                data: 'tph2'
              },
              {
                data: 'jalan2'
              },
              {
                data: 'bin2'
              },
              {
                data: 'karung2'
              },

              {
                data: 'tot_brd2'
              },
              {
                data: 'skor_brd2'
              },
              {
                data: 'buah2'
              },
              {
                data: 'restan2'
              },
              {
                data: 'tod_jjg2'
              },
              {
                data: 'skor_janjang2'
              },



              {
                data: 'tph3'
              },
              {
                data: 'jalan3'
              },
              {
                data: 'bin3'
              },
              {
                data: 'karung3'
              },

              {
                data: 'tot_brd3'
              },
              {
                data: 'skor_brd3'
              },
              {
                data: 'buah3'
              },
              {
                data: 'restan3'
              },
              {
                data: 'tod_jjg3'
              },
              {
                data: 'skor_janjang3'
              },


              {
                data: 'tph4'
              },
              {
                data: 'jalan4'
              },
              {
                data: 'bin4'
              },
              {
                data: 'karung4'
              },

              {
                data: 'tot_brd4'
              },
              {
                data: 'skor_brd4'
              },
              {
                data: 'buah4'
              },
              {
                data: 'restan4'
              },
              {
                data: 'tod_jjg4'
              },
              {
                data: 'skor_janjang4'
              },


              {
                data: 'tph5'
              },
              {
                data: 'jalan5'
              },
              {
                data: 'bin5'
              },
              {
                data: 'karung5'
              },

              {
                data: 'tot_brd5'
              },
              {
                data: 'skor_brd5'
              },
              {
                data: 'buah5'
              },
              {
                data: 'restan5'
              },
              {
                data: 'tod_jjg5'
              },
              {
                data: 'skor_janjang5'
              },


              {
                data: 'tph6'
              },
              {
                data: 'jalan6'
              },
              {
                data: 'bin6'
              },
              {
                data: 'karung6'
              },

              {
                data: 'tot_brd6'
              },
              {
                data: 'skor_brd6'
              },
              {
                data: 'buah6'
              },
              {
                data: 'restan6'
              },
              {
                data: 'tod_jjg6'
              },
              {
                data: 'skor_janjang6'
              },


              {
                data: 'tph7'
              },
              {
                data: 'jalan7'
              },
              {
                data: 'bin7'
              },
              {
                data: 'karung7'
              },

              {
                data: 'tot_brd7'
              },
              {
                data: 'skor_brd7'
              },
              {
                data: 'buah7'
              },
              {
                data: 'restan7'
              },
              {
                data: 'tod_jjg7'
              },
              {
                data: 'skor_janjang7'
              },

              {
                data: 'tph8'
              },
              {
                data: 'jalan8'
              },
              {
                data: 'bin8'
              },
              {
                data: 'karung8'
              },

              {
                data: 'tot_brd8'
              },
              {
                data: 'skor_brd8'
              },
              {
                data: 'buah8'
              },
              {
                data: 'restan8'
              },
              {
                data: 'tod_jjg8'
              },
              {
                data: 'skor_janjang8'
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var ispeksi = row.inspek; // Assuming 'inspek' is a property in the 'row' object
                  var totalScoreValue = row.total_score; // Assuming 'total_score' is a property in the 'row' object
                  var kategori = '-';

                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      kategori = (totalScoreValue < 0) ? 0 : totalScoreValue;
                    } else if (totalScoreValue === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue < 0) {
                      kategori = 0;
                    } else {
                      kategori = totalScoreValue;
                    }
                  }

                  return kategori;
                }
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var totalScoreValue = parseFloat(data);
                  var kategori = '';
                  var ispeksi = row.inspek;
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      if (totalScoreValue >= 95) {
                        kategori = 'EXCELLENT';
                      } else if (totalScoreValue >= 85) {
                        kategori = 'GOOD';
                      } else if (totalScoreValue >= 75) {
                        kategori = 'SATISFACTORY';
                      } else if (totalScoreValue >= 65) {
                        kategori = 'FAIR';
                      } else if (totalScoreValue < 65) {
                        kategori = 'POOR';
                      } else {
                        kategori = '-';
                      }
                    } else if (ispeksi === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue >= 95) {
                      kategori = 'EXCELLENT';
                    } else if (totalScoreValue >= 85) {
                      kategori = 'GOOD';
                    } else if (totalScoreValue >= 75) {
                      kategori = 'SATISFACTORY';
                    } else if (totalScoreValue >= 65) {
                      kategori = 'FAIR';
                    } else if (totalScoreValue < 65) {
                      kategori = 'POOR';
                    } else {
                      kategori = '-';
                    }
                  }
                  return kategori;
                }
              }
            ],
            "createdRow": function(row, data, dataIndex) {
              // Assuming 'data' contains the data for each row
              var afdValue = data['afd']; // Replace with the correct column name or data source
              var totalScoreValue = parseFloat(data['total_score']); // Convert total_score to a number

              // Check if the 'afd' value is 'EST' and set background color to blue
              if (afdValue === 'EST') {
                $(row).css('background-color', '#b0d48c');
              }


              if (totalScoreValue <= 65) {
                $(row).find('td:eq(82)').css('background-color', 'red');
                $(row).find('td:eq(83)').css('background-color', 'red');
              }

              if (totalScoreValue >= 65 && totalScoreValue <= 75) {
                $(row).find('td:eq(82)').css('background-color', '#ffc404');
                $(row).find('td:eq(83)').css('background-color', '#ffc404');
              }

              if (totalScoreValue >= 75 && totalScoreValue <= 85) {
                $(row).find('td:eq(82)').css('background-color', '#fffc04');
                $(row).find('td:eq(83)').css('background-color', '#fffc04');
              }

              if (totalScoreValue >= 85 && totalScoreValue <= 95) {
                $(row).find('td:eq(82)').css('background-color', '#08b454');
                $(row).find('td:eq(83)').css('background-color', '#08b454');
              }

              if (totalScoreValue >= 95) {
                $(row).find('td:eq(82)').css('background-color', '#609cd4');
                $(row).find('td:eq(83)').css('background-color', '#609cd4');
              }

            }
          });
          datatableweek1.clear().rows.add(parseResult['week1']).draw();

          // console.log(parseResult['week1']);
          var datatableweek2 = $('#newweek2').DataTable({
            "iDisplayLength": 100,
            columns: [{
                data: 'est'
              },
              {
                data: 'afd',
                render: function(data, type, row) {
                  if (row.afd === 'EST') {



                    var linkText = 'Link';
                    var linkUrl = 'BaSidakTPH/' + row.est + '/' + row.start + '/' + row.end + '/' + row.reg;
                    return '<a href="' + linkUrl + '">' + row.afd + '</a>';
                  } else {

                    return row.afd;
                  }
                }
              },

              {
                data: 'tph1'
              },
              {
                data: 'jalan1'
              },
              {
                data: 'bin1'
              },
              {
                data: 'karung1'
              },

              {
                data: 'tot_brd1'
              },
              {
                data: 'skor_brd1'
              },
              {
                data: 'buah1'
              },
              {
                data: 'restan1'
              },
              {
                data: 'tod_jjg1'
              },
              {
                data: 'skor_janjang1'
              },


              {
                data: 'tph2'
              },
              {
                data: 'jalan2'
              },
              {
                data: 'bin2'
              },
              {
                data: 'karung2'
              },

              {
                data: 'tot_brd2'
              },
              {
                data: 'skor_brd2'
              },
              {
                data: 'buah2'
              },
              {
                data: 'restan2'
              },
              {
                data: 'tod_jjg2'
              },
              {
                data: 'skor_janjang2'
              },



              {
                data: 'tph3'
              },
              {
                data: 'jalan3'
              },
              {
                data: 'bin3'
              },
              {
                data: 'karung3'
              },

              {
                data: 'tot_brd3'
              },
              {
                data: 'skor_brd3'
              },
              {
                data: 'buah3'
              },
              {
                data: 'restan3'
              },
              {
                data: 'tod_jjg3'
              },
              {
                data: 'skor_janjang3'
              },


              {
                data: 'tph4'
              },
              {
                data: 'jalan4'
              },
              {
                data: 'bin4'
              },
              {
                data: 'karung4'
              },

              {
                data: 'tot_brd4'
              },
              {
                data: 'skor_brd4'
              },
              {
                data: 'buah4'
              },
              {
                data: 'restan4'
              },
              {
                data: 'tod_jjg4'
              },
              {
                data: 'skor_janjang4'
              },


              {
                data: 'tph5'
              },
              {
                data: 'jalan5'
              },
              {
                data: 'bin5'
              },
              {
                data: 'karung5'
              },

              {
                data: 'tot_brd5'
              },
              {
                data: 'skor_brd5'
              },
              {
                data: 'buah5'
              },
              {
                data: 'restan5'
              },
              {
                data: 'tod_jjg5'
              },
              {
                data: 'skor_janjang5'
              },


              {
                data: 'tph6'
              },
              {
                data: 'jalan6'
              },
              {
                data: 'bin6'
              },
              {
                data: 'karung6'
              },

              {
                data: 'tot_brd6'
              },
              {
                data: 'skor_brd6'
              },
              {
                data: 'buah6'
              },
              {
                data: 'restan6'
              },
              {
                data: 'tod_jjg6'
              },
              {
                data: 'skor_janjang6'
              },


              {
                data: 'tph7'
              },
              {
                data: 'jalan7'
              },
              {
                data: 'bin7'
              },
              {
                data: 'karung7'
              },

              {
                data: 'tot_brd7'
              },
              {
                data: 'skor_brd7'
              },
              {
                data: 'buah7'
              },
              {
                data: 'restan7'
              },
              {
                data: 'tod_jjg7'
              },
              {
                data: 'skor_janjang7'
              },

              {
                data: 'tph8'
              },
              {
                data: 'jalan8'
              },
              {
                data: 'bin8'
              },
              {
                data: 'karung8'
              },

              {
                data: 'tot_brd8'
              },
              {
                data: 'skor_brd8'
              },
              {
                data: 'buah8'
              },
              {
                data: 'restan8'
              },
              {
                data: 'tod_jjg8'
              },
              {
                data: 'skor_janjang8'
              },

              {
                data: 'total_score',
                render: function(data, type, row) {
                  var ispeksi = row.inspek; // Assuming 'inspek' is a property in the 'row' object
                  var totalScoreValue = row.total_score; // Assuming 'total_score' is a property in the 'row' object
                  var kategori = '-';
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      kategori = (totalScoreValue < 0) ? 0 : totalScoreValue;
                    } else if (totalScoreValue === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue < 0) {
                      kategori = 0;
                    } else {
                      kategori = totalScoreValue;
                    }
                  }

                  return kategori;
                }
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var totalScoreValue = parseFloat(data);
                  var kategori = '';
                  var ispeksi = row.inspek;
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      if (totalScoreValue >= 95) {
                        kategori = 'EXCELLENT';
                      } else if (totalScoreValue >= 85) {
                        kategori = 'GOOD';
                      } else if (totalScoreValue >= 75) {
                        kategori = 'SATISFACTORY';
                      } else if (totalScoreValue >= 65) {
                        kategori = 'FAIR';
                      } else if (totalScoreValue < 65) {
                        kategori = 'POOR';
                      } else {
                        kategori = '-';
                      }
                    } else if (ispeksi === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue >= 95) {
                      kategori = 'EXCELLENT';
                    } else if (totalScoreValue >= 85) {
                      kategori = 'GOOD';
                    } else if (totalScoreValue >= 75) {
                      kategori = 'SATISFACTORY';
                    } else if (totalScoreValue >= 65) {
                      kategori = 'FAIR';
                    } else if (totalScoreValue < 65) {
                      kategori = 'POOR';
                    } else {
                      kategori = '-';
                    }
                  }
                  return kategori;
                }
              }
            ],
            "createdRow": function(row, data, dataIndex) {
              // Assuming 'data' contains the data for each row
              var afdValue = data['afd']; // Replace with the correct column name or data source
              var totalScoreValue = parseFloat(data['total_score']); // Convert total_score to a number

              // Check if the 'afd' value is 'EST' and set background color to blue
              if (afdValue === 'EST') {
                $(row).css('background-color', '#b0d48c');
              }


              if (totalScoreValue <= 65) {
                $(row).find('td:eq(82)').css('background-color', 'red');
                $(row).find('td:eq(83)').css('background-color', 'red');
              }

              if (totalScoreValue >= 65 && totalScoreValue <= 75) {
                $(row).find('td:eq(82)').css('background-color', '#ffc404');
                $(row).find('td:eq(83)').css('background-color', '#ffc404');
              }

              if (totalScoreValue >= 75 && totalScoreValue <= 85) {
                $(row).find('td:eq(82)').css('background-color', '#fffc04');
                $(row).find('td:eq(83)').css('background-color', '#fffc04');
              }

              if (totalScoreValue >= 85 && totalScoreValue <= 95) {
                $(row).find('td:eq(82)').css('background-color', '#08b454');
                $(row).find('td:eq(83)').css('background-color', '#08b454');
              }

              if (totalScoreValue >= 95) {
                $(row).find('td:eq(82)').css('background-color', '#609cd4');
                $(row).find('td:eq(83)').css('background-color', '#609cd4');
              }
            }
          });
          datatableweek2.clear().rows.add(parseResult['week2']).draw();


          var datatableweek3 = $('#newweek3').DataTable({
            "iDisplayLength": 100,
            columns: [{
                data: 'est'
              },
              {
                data: 'afd',
                render: function(data, type, row) {
                  if (row.afd === 'EST') {



                    var linkText = 'Link';
                    var linkUrl = 'BaSidakTPH/' + row.est + '/' + row.start + '/' + row.end + '/' + row.reg;
                    return '<a href="' + linkUrl + '">' + row.afd + '</a>';
                  } else {

                    return row.afd;
                  }
                }
              },
              {
                data: 'tph1'
              },
              {
                data: 'jalan1'
              },
              {
                data: 'bin1'
              },
              {
                data: 'karung1'
              },

              {
                data: 'tot_brd1'
              },
              {
                data: 'skor_brd1'
              },
              {
                data: 'buah1'
              },
              {
                data: 'restan1'
              },
              {
                data: 'tod_jjg1'
              },
              {
                data: 'skor_janjang1'
              },


              {
                data: 'tph2'
              },
              {
                data: 'jalan2'
              },
              {
                data: 'bin2'
              },
              {
                data: 'karung2'
              },

              {
                data: 'tot_brd2'
              },
              {
                data: 'skor_brd2'
              },
              {
                data: 'buah2'
              },
              {
                data: 'restan2'
              },
              {
                data: 'tod_jjg2'
              },
              {
                data: 'skor_janjang2'
              },



              {
                data: 'tph3'
              },
              {
                data: 'jalan3'
              },
              {
                data: 'bin3'
              },
              {
                data: 'karung3'
              },

              {
                data: 'tot_brd3'
              },
              {
                data: 'skor_brd3'
              },
              {
                data: 'buah3'
              },
              {
                data: 'restan3'
              },
              {
                data: 'tod_jjg3'
              },
              {
                data: 'skor_janjang3'
              },


              {
                data: 'tph4'
              },
              {
                data: 'jalan4'
              },
              {
                data: 'bin4'
              },
              {
                data: 'karung4'
              },

              {
                data: 'tot_brd4'
              },
              {
                data: 'skor_brd4'
              },
              {
                data: 'buah4'
              },
              {
                data: 'restan4'
              },
              {
                data: 'tod_jjg4'
              },
              {
                data: 'skor_janjang4'
              },


              {
                data: 'tph5'
              },
              {
                data: 'jalan5'
              },
              {
                data: 'bin5'
              },
              {
                data: 'karung5'
              },

              {
                data: 'tot_brd5'
              },
              {
                data: 'skor_brd5'
              },
              {
                data: 'buah5'
              },
              {
                data: 'restan5'
              },
              {
                data: 'tod_jjg5'
              },
              {
                data: 'skor_janjang5'
              },


              {
                data: 'tph6'
              },
              {
                data: 'jalan6'
              },
              {
                data: 'bin6'
              },
              {
                data: 'karung6'
              },

              {
                data: 'tot_brd6'
              },
              {
                data: 'skor_brd6'
              },
              {
                data: 'buah6'
              },
              {
                data: 'restan6'
              },
              {
                data: 'tod_jjg6'
              },
              {
                data: 'skor_janjang6'
              },


              {
                data: 'tph7'
              },
              {
                data: 'jalan7'
              },
              {
                data: 'bin7'
              },
              {
                data: 'karung7'
              },

              {
                data: 'tot_brd7'
              },
              {
                data: 'skor_brd7'
              },
              {
                data: 'buah7'
              },
              {
                data: 'restan7'
              },
              {
                data: 'tod_jjg7'
              },
              {
                data: 'skor_janjang7'
              },

              {
                data: 'tph8'
              },
              {
                data: 'jalan8'
              },
              {
                data: 'bin8'
              },
              {
                data: 'karung8'
              },

              {
                data: 'tot_brd8'
              },
              {
                data: 'skor_brd8'
              },
              {
                data: 'buah8'
              },
              {
                data: 'restan8'
              },
              {
                data: 'tod_jjg8'
              },
              {
                data: 'skor_janjang8'
              },

              {
                data: 'total_score',
                render: function(data, type, row) {
                  var ispeksi = row.inspek; // Assuming 'inspek' is a property in the 'row' object
                  var totalScoreValue = row.total_score; // Assuming 'total_score' is a property in the 'row' object
                  var kategori = '-';
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      kategori = (totalScoreValue < 0) ? 0 : totalScoreValue;
                    } else if (totalScoreValue === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue < 0) {
                      kategori = 0;
                    } else {
                      kategori = totalScoreValue;
                    }
                  }

                  return kategori;
                }
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var totalScoreValue = parseFloat(data);
                  var kategori = '';
                  var ispeksi = row.inspek;
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      if (totalScoreValue >= 95) {
                        kategori = 'EXCELLENT';
                      } else if (totalScoreValue >= 85) {
                        kategori = 'GOOD';
                      } else if (totalScoreValue >= 75) {
                        kategori = 'SATISFACTORY';
                      } else if (totalScoreValue >= 65) {
                        kategori = 'FAIR';
                      } else if (totalScoreValue < 65) {
                        kategori = 'POOR';
                      } else {
                        kategori = '-';
                      }
                    } else if (ispeksi === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue >= 95) {
                      kategori = 'EXCELLENT';
                    } else if (totalScoreValue >= 85) {
                      kategori = 'GOOD';
                    } else if (totalScoreValue >= 75) {
                      kategori = 'SATISFACTORY';
                    } else if (totalScoreValue >= 65) {
                      kategori = 'FAIR';
                    } else if (totalScoreValue < 65) {
                      kategori = 'POOR';
                    } else {
                      kategori = '-';
                    }
                  }
                  return kategori;
                }
              }

            ],
            "createdRow": function(row, data, dataIndex) {
              // Assuming 'data' contains the data for each row
              var afdValue = data['afd']; // Replace with the correct column name or data source
              var totalScoreValue = parseFloat(data['total_score']); // Convert total_score to a number

              // Check if the 'afd' value is 'EST' and set background color to blue
              if (afdValue === 'EST') {
                $(row).css('background-color', '#b0d48c');
              }


              if (totalScoreValue <= 65) {
                $(row).find('td:eq(82)').css('background-color', 'red');
                $(row).find('td:eq(83)').css('background-color', 'red');
              }

              if (totalScoreValue >= 65 && totalScoreValue <= 75) {
                $(row).find('td:eq(82)').css('background-color', '#ffc404');
                $(row).find('td:eq(83)').css('background-color', '#ffc404');
              }

              if (totalScoreValue >= 75 && totalScoreValue <= 85) {
                $(row).find('td:eq(82)').css('background-color', '#fffc04');
                $(row).find('td:eq(83)').css('background-color', '#fffc04');
              }

              if (totalScoreValue >= 85 && totalScoreValue <= 95) {
                $(row).find('td:eq(82)').css('background-color', '#08b454');
                $(row).find('td:eq(83)').css('background-color', '#08b454');
              }

              if (totalScoreValue >= 95) {
                $(row).find('td:eq(82)').css('background-color', '#609cd4');
                $(row).find('td:eq(83)').css('background-color', '#609cd4');
              }
            }
          });
          datatableweek3.clear().rows.add(parseResult['week3']).draw();

          var datatableweek4 = $('#newweek4').DataTable({
            "iDisplayLength": 100,
            columns: [{
                data: 'est'
              },
              {
                data: 'afd',
                render: function(data, type, row) {
                  if (row.afd === 'EST') {



                    var linkText = 'Link';
                    var linkUrl = 'BaSidakTPH/' + row.est + '/' + row.start + '/' + row.end + '/' + row.reg;
                    return '<a href="' + linkUrl + '">' + row.afd + '</a>';
                  } else {

                    return row.afd;
                  }
                }
              },
              {
                data: 'tph1'
              },
              {
                data: 'jalan1'
              },
              {
                data: 'bin1'
              },
              {
                data: 'karung1'
              },

              {
                data: 'tot_brd1'
              },
              {
                data: 'skor_brd1'
              },
              {
                data: 'buah1'
              },
              {
                data: 'restan1'
              },
              {
                data: 'tod_jjg1'
              },
              {
                data: 'skor_janjang1'
              },


              {
                data: 'tph2'
              },
              {
                data: 'jalan2'
              },
              {
                data: 'bin2'
              },
              {
                data: 'karung2'
              },

              {
                data: 'tot_brd2'
              },
              {
                data: 'skor_brd2'
              },
              {
                data: 'buah2'
              },
              {
                data: 'restan2'
              },
              {
                data: 'tod_jjg2'
              },
              {
                data: 'skor_janjang2'
              },



              {
                data: 'tph3'
              },
              {
                data: 'jalan3'
              },
              {
                data: 'bin3'
              },
              {
                data: 'karung3'
              },

              {
                data: 'tot_brd3'
              },
              {
                data: 'skor_brd3'
              },
              {
                data: 'buah3'
              },
              {
                data: 'restan3'
              },
              {
                data: 'tod_jjg3'
              },
              {
                data: 'skor_janjang3'
              },


              {
                data: 'tph4'
              },
              {
                data: 'jalan4'
              },
              {
                data: 'bin4'
              },
              {
                data: 'karung4'
              },

              {
                data: 'tot_brd4'
              },
              {
                data: 'skor_brd4'
              },
              {
                data: 'buah4'
              },
              {
                data: 'restan4'
              },
              {
                data: 'tod_jjg4'
              },
              {
                data: 'skor_janjang4'
              },


              {
                data: 'tph5'
              },
              {
                data: 'jalan5'
              },
              {
                data: 'bin5'
              },
              {
                data: 'karung5'
              },

              {
                data: 'tot_brd5'
              },
              {
                data: 'skor_brd5'
              },
              {
                data: 'buah5'
              },
              {
                data: 'restan5'
              },
              {
                data: 'tod_jjg5'
              },
              {
                data: 'skor_janjang5'
              },


              {
                data: 'tph6'
              },
              {
                data: 'jalan6'
              },
              {
                data: 'bin6'
              },
              {
                data: 'karung6'
              },

              {
                data: 'tot_brd6'
              },
              {
                data: 'skor_brd6'
              },
              {
                data: 'buah6'
              },
              {
                data: 'restan6'
              },
              {
                data: 'tod_jjg6'
              },
              {
                data: 'skor_janjang6'
              },


              {
                data: 'tph7'
              },
              {
                data: 'jalan7'
              },
              {
                data: 'bin7'
              },
              {
                data: 'karung7'
              },

              {
                data: 'tot_brd7'
              },
              {
                data: 'skor_brd7'
              },
              {
                data: 'buah7'
              },
              {
                data: 'restan7'
              },
              {
                data: 'tod_jjg7'
              },
              {
                data: 'skor_janjang7'
              },

              {
                data: 'tph8'
              },
              {
                data: 'jalan8'
              },
              {
                data: 'bin8'
              },
              {
                data: 'karung8'
              },

              {
                data: 'tot_brd8'
              },
              {
                data: 'skor_brd8'
              },
              {
                data: 'buah8'
              },
              {
                data: 'restan8'
              },
              {
                data: 'tod_jjg8'
              },
              {
                data: 'skor_janjang8'
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var ispeksi = row.inspek; // Assuming 'inspek' is a property in the 'row' object
                  var totalScoreValue = row.total_score; // Assuming 'total_score' is a property in the 'row' object
                  var kategori = '-';

                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      kategori = (totalScoreValue < 0) ? 0 : totalScoreValue;
                    } else if (totalScoreValue === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue < 0) {
                      kategori = 0;
                    } else {
                      kategori = totalScoreValue;
                    }
                  }

                  return kategori;
                }
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var totalScoreValue = parseFloat(data);
                  var kategori = '';
                  var ispeksi = row.inspek;
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      if (totalScoreValue >= 95) {
                        kategori = 'EXCELLENT';
                      } else if (totalScoreValue >= 85) {
                        kategori = 'GOOD';
                      } else if (totalScoreValue >= 75) {
                        kategori = 'SATISFACTORY';
                      } else if (totalScoreValue >= 65) {
                        kategori = 'FAIR';
                      } else if (totalScoreValue < 65) {
                        kategori = 'POOR';
                      } else {
                        kategori = '-';
                      }
                    } else if (ispeksi === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue >= 95) {
                      kategori = 'EXCELLENT';
                    } else if (totalScoreValue >= 85) {
                      kategori = 'GOOD';
                    } else if (totalScoreValue >= 75) {
                      kategori = 'SATISFACTORY';
                    } else if (totalScoreValue >= 65) {
                      kategori = 'FAIR';
                    } else if (totalScoreValue < 65) {
                      kategori = 'POOR';
                    } else {
                      kategori = '-';
                    }
                  }
                  return kategori;
                }
              }

            ],
            "createdRow": function(row, data, dataIndex) {
              // Assuming 'data' contains the data for each row
              var afdValue = data['afd']; // Replace with the correct column name or data source
              var totalScoreValue = parseFloat(data['total_score']); // Convert total_score to a number

              // Check if the 'afd' value is 'EST' and set background color to blue
              if (afdValue === 'EST') {
                $(row).css('background-color', '#b0d48c');
              }

              if (totalScoreValue <= 65) {
                $(row).find('td:eq(82)').css('background-color', 'red');
                $(row).find('td:eq(83)').css('background-color', 'red');
              }

              if (totalScoreValue >= 65 && totalScoreValue <= 75) {
                $(row).find('td:eq(82)').css('background-color', '#ffc404');
                $(row).find('td:eq(83)').css('background-color', '#ffc404');
              }

              if (totalScoreValue >= 75 && totalScoreValue <= 85) {
                $(row).find('td:eq(82)').css('background-color', '#fffc04');
                $(row).find('td:eq(83)').css('background-color', '#fffc04');
              }

              if (totalScoreValue >= 85 && totalScoreValue <= 95) {
                $(row).find('td:eq(82)').css('background-color', '#08b454');
                $(row).find('td:eq(83)').css('background-color', '#08b454');
              }

              if (totalScoreValue >= 95) {
                $(row).find('td:eq(82)').css('background-color', '#609cd4');
                $(row).find('td:eq(83)').css('background-color', '#609cd4');
              }
            }
          });
          datatableweek4.clear().rows.add(parseResult['week4']).draw();

          var datatableweek5 = $('#newweek5').DataTable({
            "iDisplayLength": 100,
            columns: [{
                data: 'est'
              },
              {
                data: 'afd',
                render: function(data, type, row) {
                  if (row.afd === 'EST') {



                    var linkText = 'Link';
                    var linkUrl = 'BaSidakTPH/' + row.est + '/' + row.start + '/' + row.end + '/' + row.reg;
                    return '<a href="' + linkUrl + '">' + row.afd + '</a>';
                  } else {

                    return row.afd;
                  }
                }
              },
              {
                data: 'tph1'
              },
              {
                data: 'jalan1'
              },
              {
                data: 'bin1'
              },
              {
                data: 'karung1'
              },

              {
                data: 'tot_brd1'
              },
              {
                data: 'skor_brd1'
              },
              {
                data: 'buah1'
              },
              {
                data: 'restan1'
              },
              {
                data: 'tod_jjg1'
              },
              {
                data: 'skor_janjang1'
              },


              {
                data: 'tph2'
              },
              {
                data: 'jalan2'
              },
              {
                data: 'bin2'
              },
              {
                data: 'karung2'
              },

              {
                data: 'tot_brd2'
              },
              {
                data: 'skor_brd2'
              },
              {
                data: 'buah2'
              },
              {
                data: 'restan2'
              },
              {
                data: 'tod_jjg2'
              },
              {
                data: 'skor_janjang2'
              },



              {
                data: 'tph3'
              },
              {
                data: 'jalan3'
              },
              {
                data: 'bin3'
              },
              {
                data: 'karung3'
              },

              {
                data: 'tot_brd3'
              },
              {
                data: 'skor_brd3'
              },
              {
                data: 'buah3'
              },
              {
                data: 'restan3'
              },
              {
                data: 'tod_jjg3'
              },
              {
                data: 'skor_janjang3'
              },


              {
                data: 'tph4'
              },
              {
                data: 'jalan4'
              },
              {
                data: 'bin4'
              },
              {
                data: 'karung4'
              },

              {
                data: 'tot_brd4'
              },
              {
                data: 'skor_brd4'
              },
              {
                data: 'buah4'
              },
              {
                data: 'restan4'
              },
              {
                data: 'tod_jjg4'
              },
              {
                data: 'skor_janjang4'
              },


              {
                data: 'tph5'
              },
              {
                data: 'jalan5'
              },
              {
                data: 'bin5'
              },
              {
                data: 'karung5'
              },

              {
                data: 'tot_brd5'
              },
              {
                data: 'skor_brd5'
              },
              {
                data: 'buah5'
              },
              {
                data: 'restan5'
              },
              {
                data: 'tod_jjg5'
              },
              {
                data: 'skor_janjang5'
              },


              {
                data: 'tph6'
              },
              {
                data: 'jalan6'
              },
              {
                data: 'bin6'
              },
              {
                data: 'karung6'
              },

              {
                data: 'tot_brd6'
              },
              {
                data: 'skor_brd6'
              },
              {
                data: 'buah6'
              },
              {
                data: 'restan6'
              },
              {
                data: 'tod_jjg6'
              },
              {
                data: 'skor_janjang6'
              },


              {
                data: 'tph7'
              },
              {
                data: 'jalan7'
              },
              {
                data: 'bin7'
              },
              {
                data: 'karung7'
              },

              {
                data: 'tot_brd7'
              },
              {
                data: 'skor_brd7'
              },
              {
                data: 'buah7'
              },
              {
                data: 'restan7'
              },
              {
                data: 'tod_jjg7'
              },
              {
                data: 'skor_janjang7'
              },

              {
                data: 'tph8'
              },
              {
                data: 'jalan8'
              },
              {
                data: 'bin8'
              },
              {
                data: 'karung8'
              },

              {
                data: 'tot_brd8'
              },
              {
                data: 'skor_brd8'
              },
              {
                data: 'buah8'
              },
              {
                data: 'restan8'
              },
              {
                data: 'tod_jjg8'
              },
              {
                data: 'skor_janjang8'
              },

              {
                data: 'total_score',
                render: function(data, type, row) {
                  var ispeksi = row.inspek; // Assuming 'inspek' is a property in the 'row' object
                  var totalScoreValue = row.total_score; // Assuming 'total_score' is a property in the 'row' object
                  var kategori = '-';
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      kategori = (totalScoreValue < 0) ? 0 : totalScoreValue;
                    } else if (totalScoreValue === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue < 0) {
                      kategori = 0;
                    } else {
                      kategori = totalScoreValue;
                    }
                  }

                  return kategori;
                }
              },
              {
                data: 'total_score',
                render: function(data, type, row) {
                  var totalScoreValue = parseFloat(data);
                  var kategori = '';
                  var ispeksi = row.inspek;
                  if ('inspek' in row) {
                    if (ispeksi === 'ada') {
                      if (totalScoreValue >= 95) {
                        kategori = 'EXCELLENT';
                      } else if (totalScoreValue >= 85) {
                        kategori = 'GOOD';
                      } else if (totalScoreValue >= 75) {
                        kategori = 'SATISFACTORY';
                      } else if (totalScoreValue >= 65) {
                        kategori = 'FAIR';
                      } else if (totalScoreValue < 65) {
                        kategori = 'POOR';
                      } else {
                        kategori = '-';
                      }
                    } else if (ispeksi === 'kosong') {
                      kategori = '-';
                    }
                  } else {
                    if (totalScoreValue >= 95) {
                      kategori = 'EXCELLENT';
                    } else if (totalScoreValue >= 85) {
                      kategori = 'GOOD';
                    } else if (totalScoreValue >= 75) {
                      kategori = 'SATISFACTORY';
                    } else if (totalScoreValue >= 65) {
                      kategori = 'FAIR';
                    } else if (totalScoreValue < 65) {
                      kategori = 'POOR';
                    } else {
                      kategori = '-';
                    }
                  }
                  return kategori;
                }
              }

            ],
            "createdRow": function(row, data, dataIndex) {
              // Assuming 'data' contains the data for each row
              var afdValue = data['afd']; // Replace with the correct column name or data source
              var totalScoreValue = parseFloat(data['total_score']); // Convert total_score to a number

              // Check if the 'afd' value is 'EST' and set background color to blue
              if (afdValue === 'EST') {
                $(row).css('background-color', '#b0d48c');
              }
              if (totalScoreValue == '-') {
                $(row).find('td:eq(82)').css('background-color', 'white');
                $(row).find('td:eq(83)').css('background-color', 'white');
              }


              if (totalScoreValue <= 65) {
                $(row).find('td:eq(82)').css('background-color', 'red');
                $(row).find('td:eq(83)').css('background-color', 'red');
              }

              if (totalScoreValue >= 65 && totalScoreValue <= 75) {
                $(row).find('td:eq(82)').css('background-color', '#ffc404');
                $(row).find('td:eq(83)').css('background-color', '#ffc404');
              }

              if (totalScoreValue >= 75 && totalScoreValue <= 85) {
                $(row).find('td:eq(82)').css('background-color', '#fffc04');
                $(row).find('td:eq(83)').css('background-color', '#fffc04');
              }

              if (totalScoreValue >= 85 && totalScoreValue <= 95) {
                $(row).find('td:eq(82)').css('background-color', '#08b454');
                $(row).find('td:eq(83)').css('background-color', '#08b454');
              }

              if (totalScoreValue >= 95) {
                $(row).find('td:eq(82)').css('background-color', '#609cd4');
                $(row).find('td:eq(83)').css('background-color', '#609cd4');
              }
            }
          });
          datatableweek5.clear().rows.add(parseResult['week5']).draw();
        }
      });
    }



    function changeClass() {
      var regSidak = document.getElementById('regionalSidak').value

      const element1 = document.getElementById('classOne');
      const element2 = document.getElementById('classTwo');
      const element3 = document.getElementById('classThree');
      const element4 = document.getElementById('classFour');
      const thElement1 = document.getElementById('thWilOne');
      const thElement2 = document.getElementById('thWilTwo');
      const thElement3 = document.getElementById('thWilThree');
      const thElement4 = document.getElementById('thwillPlas');
      if (regSidak == '3') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH VII';
        thElement2.textContent = 'WILAYAH VIII';
        thElement4.textContent = 'Plasma3';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      } else if (regSidak === '1') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH I';
        thElement2.textContent = 'WILAYAH II';
        thElement3.textContent = 'WILAYAH III';

        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-4", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-4", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-4", "col-lg-4");

      } else if (regSidak === '2') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH IV';
        thElement2.textContent = 'WILAYAH V';
        thElement3.textContent = 'WILAYAH VI';
        thElement4.textContent = 'Plasma2';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-6", "col-lg-4");

      } else if (regSidak === '4') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH Inti';
        thElement2.textContent = 'WILAYAH Plasma';
        // thElement3.textContent = 'WILAYAH VI';
        // thElement4.textContent = 'Plasma2';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        // thElement3.classList.add("text-center");
        // thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      }
    }


    function changeClassMonth() {
      var regSidak = document.getElementById('regionalSidakMonth').value
      const element1 = document.getElementById('table1Month');
      const element2 = document.getElementById('classTwoMonth');
      const element3 = document.getElementById('classThreeMonth');
      const element4 = document.getElementById('classFourMonth');
      const thElement1 = document.getElementById('thWilOneMonth');
      const thElement2 = document.getElementById('thWilTwoMonth');
      const thElement3 = document.getElementById('thWilThreeMonth');
      const thElement4 = document.getElementById('thPlasmamonth');
      if (regSidak == '3') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH VII';
        thElement2.textContent = 'WILAYAH VIII';
        thElement4.textContent = 'Plasma3';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      } else if (regSidak === '1') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH I';
        thElement2.textContent = 'WILAYAH II';
        thElement3.textContent = 'WILAYAH III';
        thElement4.textContent = 'Plasma1';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-4", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-4", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-4", "col-lg-4");

      } else if (regSidak === '2') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH IV';
        thElement2.textContent = 'WILAYAH V';
        thElement3.textContent = 'WILAYAH VI';
        thElement4.textContent = 'Plasma2';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");
        thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-6", "col-lg-4");

      } else if (regSidak === '4') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";
        element4.style.display = "none";
        thElement1.textContent = 'WILAYAH Inti';
        thElement2.textContent = 'WILAYAH Plasma';
        // thElement3.textContent = 'WILAYAH VI';
        // thElement4.textContent = 'Plasma2';
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        // thElement3.classList.add("text-center");
        // thElement4.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      }
    }



    function changeClassYear() {
      var regSidak = document.getElementById('regionalSidakYear').value;

      const element1 = document.getElementById('Tab1');
      const element2 = document.getElementById('Tab2');
      const element3 = document.getElementById('Tab3');

      const thElement1 = document.getElementById('thead1');
      const thElement2 = document.getElementById('thead2');
      const thElement3 = document.getElementById('thead3');

      if (regSidak == '3') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";

        thElement1.textContent = 'WILAYAH VII';
        thElement2.textContent = 'WILAYAH VIII';

        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");

        element1.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-3", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      } else if (regSidak === '1') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";

        thElement1.textContent = 'WILAYAH I';
        thElement2.textContent = 'WILAYAH II';
        thElement3.textContent = 'WILAYAH III';

        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");

        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-4", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-4", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-4", "col-lg-4");

      } else if (regSidak === '2') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "";

        thElement1.textContent = 'WILAYAH IV';
        thElement2.textContent = 'WILAYAH V';
        thElement3.textContent = 'WILAYAH VI';

        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        thElement3.classList.add("text-center");

        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-4");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-4");
        element3.classList.remove("col-md-6", "col-lg-4");
        element3.classList.add("col-md-6", "col-lg-4");

      } else if (regSidak === '4') {
        element1.style.display = "";
        element2.style.display = "";
        element3.style.display = "none";
        thElement1.classList.add("text-center");
        thElement2.classList.add("text-center");
        element1.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element1.classList.add("col-md-6", "col-lg-6");
        element2.classList.remove("col-md-6", "col-lg-4", "col-lg-6");
        element2.classList.add("col-md-6", "col-lg-6");

      }
    }


    document.getElementById('btnExport').onclick = function() {
      var _token = $('input[name="_token"]').val();
      var weekData = document.getElementById('dateWeek').value;
      var regional = document.getElementById('regionalSidak').value;
      const week = weekData.substring(6, 8);
      const year = weekData.substring(0, 4);
      const month = moment(document.getElementById('dateWeek').value).subtract(-6, 'days').format("M");
      const url = 'https://mobilepro.srs-ssms.com/storage/app/public/pdf/sidak_tph/STPH-' + year + '-0' + month + '-Week' + week + '-Reg1.pdf';
      const phpUrl = 'https://srs-ssms.com/sidak_tph/render_chart.php?regional=' + encodeURIComponent(regional);
      const pdf = 'https://srs-ssms.com/sidak_tph/generate_pdf_sidak_tph.php?regional=' + encodeURIComponent(regional);

      // AJAX request to your PHP script
      $.ajax({
        url: phpUrl,
        method: "GET",
        success: function(result) {
          console.log(result);
        }
      });

      // AJAX request to generate PDF
      $.ajax({
        url: pdf,
        method: "GET",
        success: function(result) {
          console.log(result);

          // Then you can make another AJAX request to download the PDF
          $.ajax({
            url: "{{ route('downloadPDF') }}",
            method: "POST",
            data: {
              url: url,
              _token: _token
            },
            success: function(result) {
              var parseResult = JSON.parse(result);
              if (parseResult['status'] == 200) {
                window.open(parseResult['url'], '_blank');
              } else {
                alert('FILE PDF BELUM TERSEDIA!');
              }
            }
          });
        }
      });
    }



    function getDataTph() {

      changeClass()
      var firstWeek = ''
      var lastWeek = ''
      var _token = $('input[name="_token"]').val();
      var weekData = document.getElementById('dateWeek').value
      var regSidak = document.getElementById('regionalSidak').value
      const year = weekData.substring(0, 4);
      const week = weekData.substring(6, 8);
      const date = new Date(year, 0, 1);
      const date2 = new Date(year, 0, 1);
      var getDateFirst = date.setDate(date.getDate() + (week - 1) * 7);
      var getDateLast = date.setDate(date2.getDate() + (week - 1) * 7);
      // first week
      var getDateFirst = new Date(getDateFirst)
      var convertFirstWeek = getDateFirst.setDate(getDateFirst.getDate() + 2)
      //mengubah data dari Mon Jan 16 2023 00:00:00 GMT+0700 (Western Indonesia Time) convert javascript to YYYY/MM/DD
      //ke format Tahun/bulan/hari
      var firstWeekCon = new Date(convertFirstWeek);
      let firstWeekData = JSON.stringify(firstWeekCon)
      firstWeek = firstWeekData.slice(1, 11)
      //last week
      var getDateLast = new Date(getDateLast)
      var convertLastWeek = getDateLast.setDate(getDateLast.getDate() + 8)
      //mengubah data dari Mon Jan 16 2023 00:00:00 GMT+0700 (Western Indonesia Time) convert javascript to YYYY/MM/DD
      //ke format Tahun/bulan/hari
      var lastWeekCon = new Date(convertLastWeek);
      let lastWeekData = JSON.stringify(lastWeekCon)
      lastWeek = lastWeekData.slice(1, 11)

      $.ajax({
        url: "{{ route('getBtTph') }}",
        method: "POST",
        data: {
          start: firstWeek,
          finish: lastWeek,
          reg: regSidak,
          _token: _token
        },
        success: function(result) {
          Swal.close();
          //parsing result ke json untuk dalam estate
          var parseResult = JSON.parse(result)
          // ubah json ke array agar bisa di for atau foreach
          var listBtTph = Object.entries(parseResult[
            'val_bt_tph']) //parsing data brondolan ke dalam var list
          // console.log(listBtTph)
          var listKRTph = Object.entries(parseResult[
            'val_kr_tph']) //parsing data karung isi brondolan ke dalam var list
          var lisBHtph = Object.entries(parseResult[
            'val_bh_tph']) //parsing data buah tinggal ke dalam var list
          // console.log(listKRTph)
          var listRStph = Object.entries(parseResult[
            'val_rs_tph']) //parse data dari restand tidak di laporkan

          //parsing result ke json untuk dalam wilayah   
          var listBtTphWil = Object.entries(parseResult[
            'val_bt_tph_wil']) //parsing data brondolan ke dalam var list
          // console.log(listBtTph)
          var listKRTphWil = Object.entries(parseResult[
            'val_kr_tph_wil']) //parsing data karung isi brondolan ke dalam var list
          var lisBHtphWil = Object.entries(parseResult[
            'val_bh_tph_wil']) //parsing data buah tinggal ke dalam var list
          // console.log(listKRTph)
          var listRStphWil = Object.entries(parseResult[
            'val_rs_tph_wil']) //parse data dari restand tidak di laporkan     
          var listWill = Object.entries(parseResult[
            'list_wilayah']) //parse data dari restand tidak di laporkan
          var listWill2 = Object.entries(parseResult[
            'list_wilayah2']) //parse data dari restand tidak di laporkan
          //list untuk table di parse ke json

          var list_all_wil = Object.entries(parseResult['list_all_wil'])
          var list_all_est = Object.entries(parseResult['list_all_est'])
          var list_skor_gm = Object.entries(parseResult['list_skor_gm'])
          var list_skor_rh = Object.entries(parseResult['list_skor_rh'])
          var list_skor_gmNew = Object.entries(parseResult['list_skor_gmNew'])
          const plasma = Object.entries(parseResult['PlasmaWIl']);
          const plasmaEM = Object.entries(parseResult['PlasmaEM']);
          const plasmaGM = Object.entries(parseResult['plasmaGM']);

          var listEstate = Object.entries(parseResult[
            'estate_new']) ////pasring data estate ke dalam var list
          let textArr = listEstate.map(subArr => subArr[1]);
          //mnghitung dan mengurai string dengan substrack untuk chart
          //list estate
          // var listEst = '['
          // listEstate.forEach(element => {
          //   if (!element[1]['est'].includes('CWS')) {
          //     listEst += '"' + element[1]['est'] + '",'
          //   }
          // });
          // listEst = listEst.substring(0, listEst.length - 1);
          // listEst += ']'
          // var listEstJson = JSON.parse(listEst)
          //list wilayah
          var listWilChart = '['
          listWill2.forEach(element => {
            listWilChart += '"' + element[1] + '",'
          });
          listWilChart = listWilChart.substring(0, listWilChart.length - 1);
          listWilChart += ']'
          var listWilChartJson = JSON.parse(listWilChart)
          //brondolan tgl
          var valBtTph = '['
          listBtTph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valBtTph += '"' + element[1] + '",'
            }
          });
          valBtTph = valBtTph.substring(0, valBtTph.length - 1);
          valBtTph += ']'
          var valBtTphJson = JSON.parse(valBtTph)
          //karung tgl
          var valKRtgl = '['
          listKRTph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valKRtgl += '"' + element[1] + '",'
            }
          });
          valKRtgl = valKRtgl.substring(0, valKRtgl.length - 1);
          valKRtgl += ']'
          var valKRTtphJson = JSON.parse(valKRtgl)
          //buah tinggal
          var valBHtgl = '['
          lisBHtph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valBHtgl += '"' + element[1] + '",'
            }
          });
          valBHtgl = valBHtgl.substring(0, valBHtgl.length - 1);
          valBHtgl += ']'
          var valBHtglJson = JSON.parse(valBHtgl)

          //buah restan tidak di laporkan
          var valRSnone = '['
          listRStph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valRSnone += '"' + element[1] + '",'
            }
          });
          valRSnone = valRSnone.substring(0, valRSnone.length - 1);
          valRSnone += ']'
          var valRSnoneJson = JSON.parse(valRSnone)
          // mengubah data estate agar bisa mengurangi nilai
          var categoryEst = '['
          listEstate.forEach(element => {
            categoryEst += '"' + element[1]['est'] + '",'
          });
          categoryEst = categoryEst.substring(0, categoryEst.length - 1);
          categoryEst += ']'
          var categoryEstJson = JSON.parse(categoryEst)

          /// mengubah data wilayah agar bisa mengurangi nilai
          //brondolan tgl
          var valBtTphWil = '['
          listBtTphWil.forEach(element => {
            valBtTphWil += '"' + element[1] + '",'
          });
          valBtTphWil = valBtTphWil.substring(0, valBtTphWil.length - 1);
          valBtTphWil += ']'
          var valBtTphWilJson = JSON.parse(valBtTphWil)
          var arrayvalBtTphWilJson = valBtTphWilJson;
          for (let i = 0; i < arrayvalBtTphWilJson.length; i++) {

            arrayvalBtTphWilJson.splice(3);

          }

          //karung tinggal
          var valKRtglWil = '['
          listKRTphWil.forEach(element => {
            valKRtglWil += '"' + element[1] + '",'
          });
          valKRtglWil = valKRtglWil.substring(0, valKRtglWil.length - 1);
          valKRtglWil += ']'
          var valKRtglWilJson = JSON.parse(valKRtglWil)
          var arrayvalKRtglWilJson = valKRtglWilJson;
          for (let i = 0; i < arrayvalKRtglWilJson.length; i++) {

            arrayvalKRtglWilJson.splice(3);

          }

          //buah tinggal
          var valBHtglWil = '['
          lisBHtphWil.forEach(element => {
            valBHtglWil += '"' + element[1] + '",'
          });
          valBHtglWil = valBHtglWil.substring(0, valBHtglWil.length - 1);
          valBHtglWil += ']'
          var valBHtglWilJson = JSON.parse(valBHtglWil)
          var arrayvalBHtglWilJson = valBHtglWilJson;
          for (let i = 0; i < arrayvalBHtglWilJson.length; i++) {
            {
              arrayvalBHtglWilJson.splice(3);
            }
          }

          //buah restant
          var valRSnoneWil = '['
          listRStphWil.forEach(element => {
            valRSnoneWil += '"' + element[1] + '",'
          });
          valRSnoneWil = valRSnoneWil.substring(0, valRSnoneWil.length - 1);
          valRSnoneWil += ']'
          var valRSnoneWilJson = JSON.parse(valRSnoneWil)
          var arrayvalRSnoneWilJson = valRSnoneWilJson;
          for (let i = 0; i < arrayvalRSnoneWilJson.length; i++) {
            arrayvalRSnoneWilJson.splice(3);
          }

          var categoryWill = '['
          listWill.forEach(element => {
            categoryWill += '"' + element[1]['nama'] + '",'
          });
          categoryWill = categoryWill.substring(0, categoryWill.length - 1);
          categoryWill += ']'
          var categoryWillJson = JSON.parse(categoryWill)

          if (regSidak == 1) {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#3063EC', '#FF8D1A', '#FF8D1A', '#FF8D1A',
              '#FF8D1A', '#9208FD'
            ]
          } else if (regSidak == 2) {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#FF8D1A', '#FF8D1A', '#9208FD'
            ]
          } else {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#3063EC', '#9208FD'
            ]
          }
          // let filteredList = listEstJson.filter(item => {
          //   return item !== "SKE" && item !== "LDE" && item !== "SRE";
          // });
          renderChartTph.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartKarung.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartBuahTglTph.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartBuahRestanNone.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          will_btt.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartKarungWil.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartBuahTglTphWil.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartBuahRestanNoneWil.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });

          // Check if the result is an empty object
          if ($.isEmptyObject(result)) {
            result = null;
            renderChartTph.updateSeries([{
              name: 'Brondolan/Blok Tinggal di TPH',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartKarung.updateSeries([{
              name: 'Karung/Blok  Berisi Brondolan',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartBuahTglTph.updateSeries([{
              name: 'Buah/Blok  Tinggal TPH',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartBuahRestanNone.updateSeries([{
              name: 'Restan/Blok  Tidak dilaporkan',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            //perwilawyah
            will_btt.updateSeries([{
              name: 'Brondolan Tinggal di TPH',
              data: [0, 0, 0]
            }])

            renderChartKarungWil.updateSeries([{
              name: 'Karung Tinggal di TPH',
              data: [0, 0, 0]
            }])

            renderChartBuahTglTphWil.updateSeries([{
              name: 'Buah Tinggal Di TPH',
              data: [0, 0, 0]
            }])

            renderChartBuahRestanNoneWil.updateSeries([{
              name: 'Buah Restan Tidak di Laporkan',
              data: [0, 0, 0]
            }])
          } else {
            const newPlasma = plasma.map(([_, data]) => ({
              est: data.est,
              afd: data.afd,
              nama: data.nama,
              skor: data.skor,
              rank: data.rank,
            }));
            const newPlasmaEM = plasmaEM.map(([_, data]) => ({
              est: data.est,
              afd: data.afd,
              nama: data.namaEM,
              skor: data.Skor,
              // namaEM: namaEM,
              // namaGM: namaGM,
            }));
            const newPlasmaGM = plasmaGM.map(([_, data]) => ({
              est: data.est,
              afd: data.afd,
              nama: data.namaGM,
              skor: data.Skor,
              // namaEM: namaEM,
              // namaGM: namaGM,
            }));

            var plasmaGMe = document.getElementById('plasma');
            var arrPlasmaGM = newPlasma
            arrPlasmaGM.forEach(element => {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              //   let item2 = 'GM'
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']
              // let item6 = newPlasmaEM['EM']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")

              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }

              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              plasmaGMe.appendChild(tr)
              // }
            });

            var plasmaGMe = document.getElementById('plasma');
            var arrPlasmaGM = newPlasmaEM
            arrPlasmaGM.forEach(element => {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              //   let item2 = 'GM'
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = ''
              // let item6 = newPlasmaEM['EM']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")

              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";

              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }

              itemElement4.innerText = item4
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              //   itemElement4.innerText  = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              plasmaGMe.appendChild(tr)
              // }
            });

            var plasmaGMe = document.getElementById('plasma');
            var arrPlasmaGM = newPlasmaGM
            arrPlasmaGM.forEach(element => {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              //   let item2 = 'GM'
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = ''
              // let item6 = newPlasmaEM['EM']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")

              itemElement1.style.backgroundColor = "#Fff4cc";
              itemElement2.style.backgroundColor = "#Fff4cc";
              itemElement3.style.backgroundColor = "#Fff4cc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement4.innerText = item4;
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              //   itemElement4.innerText  = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              plasmaGMe.appendChild(tr)
              // }
            });
            //persetate
            renderChartTph.updateSeries([{
              name: 'Brondolan/Blok Tinggal di TPH',
              data: valBtTphJson
            }])

            renderChartKarung.updateSeries([{
              name: 'Karung/Blok  Berisi Brondolan',
              data: valKRTtphJson
            }])

            renderChartBuahTglTph.updateSeries([{
              name: 'Buah/Blok  Tinggal TPH',
              data: valBHtglJson
            }])

            renderChartBuahRestanNone.updateSeries([{
              name: 'Restan/Blok  Tidak dilaporkan',
              data: valRSnoneJson
            }])

            //perwilayah
            will_btt.updateSeries([{
              name: 'Brondolan Tinggal di TPH',
              data: arrayvalBtTphWilJson
            }])

            renderChartKarungWil.updateSeries([{
              name: 'Karung Tinggal di TPH',
              data: arrayvalKRtglWilJson
            }])

            renderChartBuahTglTphWil.updateSeries([{
              name: 'Buah Tinggal Di TPH',
              data: arrayvalBHtglWilJson
            }])

            renderChartBuahRestanNoneWil.updateSeries([{
              name: 'Buah Restan Tidak di Laporkan',
              data: arrayvalRSnoneWilJson
            }])

            //          //untuk table
            //table wil 1
            function filterArrayByEst(array) {
              return array.filter(obj => !obj.est.includes('Plasma'));
            }

            const originalArray = list_all_wil[0][1]
            const filteredArray = filterArrayByEst(originalArray);
            const wilarr = filterArrayByEst(list_all_est[0][1])
            var arrTbody1 = filteredArray

            let sortedArray = [...arrTbody1].sort((a, b) => b.skor - a.skor);

            // Create a map of objects to ranks
            let objectToRank = new Map();

            sortedArray.forEach((item, index) => {
              objectToRank.set(item, index + 1);
            });
            var table1 = document.getElementById('table1');
            var tbody1 = document.getElementById('tbody1');
            arrTbody1.forEach((element, index) => {

              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = objectToRank.get(element);

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }

              itemElement4.innerText = item4;
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              //   itemElement4.innerText  = item4

              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)
              // }
            });
            //  testing

            var arrTbody1 = wilarr
            var table1 = document.getElementById('table1');
            var tbody1 = document.getElementById('tbody1');
            arrTbody1.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['EM']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              // if (item4 != 0) {
              //   itemElement4.innerHTML =
              //     '<a class="detailBa" href="BaSidakTPH/' + element[
              //       'est'] + '/' + firstWeek + '/' + lastWeek +
              //     '" target="_blank">' + element['skor'] + ' </a>'
              // } else {
              //   itemElement4.innerText = item4
              // }
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)
              // }
            });
            // endtesting
            tbodySkorRH = document.getElementById('tbodySkorRH')
            var reg = ''
            if (list_skor_rh[0][0] == 1) {
              reg = 'REG I'
            } else if (list_skor_rh[0][0] == 2) {
              reg = 'REG II'
            } else if (list_skor_rh[0][0] == 3) {
              reg = 'REG III'
            } else {
              reg = 'REG IV'
            }
            tr = document.createElement('tr')
            let item1 = reg
            let item2 = 'RH - ' + list_skor_rh[0][0]
            let item3 = list_skor_rh[0][1]['nama']
            let item4 = list_skor_rh[0][1]['skor']
            let itemElement1 = document.createElement('td')
            let itemElement2 = document.createElement('td')
            let itemElement3 = document.createElement('td')
            let itemElement4 = document.createElement('td')

            itemElement1.classList.add("text-center")
            itemElement2.classList.add("text-center")
            itemElement3.classList.add("text-center")
            itemElement4.classList.add("text-center")
            itemElement1.style.backgroundColor = "#e8ecdc";
            itemElement2.style.backgroundColor = "#e8ecdc";
            itemElement3.style.backgroundColor = "#e8ecdc";
            if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
              itemElement3.style.color = "red";
            } else {
              itemElement3.style.color = "black";
            }


            if (item4 >= 95) {
              itemElement4.style.backgroundColor = "#609cd4";
              itemElement4.style.color = "black";
            } else if (item4 >= 85 && item4 < 95) {
              itemElement4.style.backgroundColor = "#08b454";
              itemElement4.style.color = "black";
            } else if (item4 >= 75 && item4 < 85) {
              itemElement4.style.backgroundColor = "#fffc04";
              itemElement4.style.color = "black";
            } else if (item4 >= 65 && item4 < 75) {
              itemElement4.style.backgroundColor = "#ffc404";
              itemElement4.style.color = "black";
            } else {
              itemElement4.style.backgroundColor = "red";
              itemElement4.style.color = "black";
            }

            if (itemElement4.style.backgroundColor === "#609cd4") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#08b454") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#fffc04") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#ffc404") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "red") {
              itemElement4.style.color = "black";
            }
            itemElement4.innerText = item4;
            itemElement1.innerText = item1
            itemElement2.innerText = item2
            itemElement3.innerText = item3
            tr.appendChild(itemElement1)
            tr.appendChild(itemElement2)
            tr.appendChild(itemElement3)
            tr.appendChild(itemElement4)
            tbodySkorRH.appendChild(tr)
            ///table wil 2
            const originalArray2 = list_all_wil[1][1]
            const filteredArray2 = filterArrayByEst(originalArray2);
            var arrTbody2 = filteredArray2



            var tbody2 = document.getElementById('tbody2');
            arrTbody2.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')
              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")

              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5
              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              tbody2.appendChild(tr)
              // }
            });
            //untuk estate wil 2
            var arrTbody1 = list_all_est[1][1]
            var tbody1 = document.getElementById('tbody2');
            arrTbody1.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['EM']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3

              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)
              // }
            });

            ///table wil 3
            const originalArray3 = list_all_wil[2][1]
            const filteredArray3 = filterArrayByEst(originalArray3);
            var arrTbody3 = filteredArray3


            // if (regSidak != 3) {

            var tbody3 = document.getElementById('tbody3');
            arrTbody3.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['afd']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody3.appendChild(tr)
              // }
            });

            // untuk estate will 3
            var arrTbody1 = list_all_est[2][1]
            var tbody1 = document.getElementById('tbody3');
            arrTbody1.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element['est']
              let item2 = element['EM']
              let item3 = element['nama']
              let item4 = element['skor']
              let item5 = element['rank']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }

              itemElement4.innerText = item4
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              // itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)
              // }
            });

            var inc = 0;
            for (let i = 1; i <= 3; i++) {
              var tbody = document.getElementById('tbody' + i);
              var wil = ''
              if (i == 1) {
                wil = 'I'
              } else if (i == 2) {
                wil = 'II'
              } else {
                wil = 'III'
              }

              tr = document.createElement('tr')
              let item1 = list_skor_gmNew[inc][1]['est']
              let item2 = list_skor_gmNew[inc][1]['afd']
              let item3 = list_skor_gmNew[inc][1]['namaGM']
              let item4 = list_skor_gmNew[inc][1]['skor']
              let item5 = ''

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')
              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#fff4cc";
              itemElement2.style.backgroundColor = "#fff4cc";
              itemElement3.style.backgroundColor = "#fff4cc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement4.innerText = item4;
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement5.innerText = item5
              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              tbody.appendChild(tr)
              inc++
            }

            // }
          }
        }
      })
    }

    function getDataTphMonth() {
      changeClassMonth()
      var _token = $('input[name="_token"]').val();
      var monthSidak = document.getElementById('inputDateMonth').value
      var regSidak = document.getElementById('regionalSidakMonth').value
      $.ajax({
        url: "{{ route('getBtTphMonth') }}",
        method: "get",
        data: {
          month: monthSidak,
          reg: regSidak,
          _token: _token
        },
        success: function(result) {
          Swal.close();
          //parsing result ke json untuk dalam estate
          var parseResult = JSON.parse(result)
          // ubah json ke array agar bisa di for atau foreach
          var listBtTph = Object.entries(parseResult[
            'val_bt_tph']) //parsing data brondolan ke dalam var list
          // console.log(listBtTph)
          var listKRTph = Object.entries(parseResult[
            'val_kr_tph']) //parsing data karung isi brondolan ke dalam var list
          var lisBHtph = Object.entries(parseResult[
            'val_bh_tph']) //parsing data buah tinggal ke dalam var list
          // console.log(listKRTph)
          var listRStph = Object.entries(parseResult[
            'val_rs_tph']) //parse data dari restand tidak di laporkan
          var listEstate = Object.entries(parseResult[
            'estate_new']) ////pasring data estate ke dalam var list
          //parsing result ke json untuk dalam wilayah   
          var listBtTphWil = Object.entries(parseResult[
            'val_bt_tph_wil']) //parsing data brondolan ke dalam var list
          // console.log(listBtTph)
          var listKRTphWil = Object.entries(parseResult[
            'val_kr_tph_wil']) //parsing data karung isi brondolan ke dalam var list
          var lisBHtphWil = Object.entries(parseResult[
            'val_bh_tph_wil']) //parsing data buah tinggal ke dalam var list
          // console.log(listKRTph)
          var listRStphWil = Object.entries(parseResult[
            'val_rs_tph_wil']) //parse data dari restand tidak di laporkan     
          var listWill = Object.entries(parseResult[
            'list_wilayah']) //parse data dari restand tidak di laporkan
          var listWill2 = Object.entries(parseResult[
            'list_wilayah2']) //parse data dari restand tidak di laporkan
          //list untuk table di parse ke json

          var list_all_wil = Object.entries(parseResult['list_all_wil'])
          var list_all_est = Object.entries(parseResult['list_all_est'])
          var list_skor_gm = Object.entries(parseResult['list_skor_gm'])
          var list_skor_rh = Object.entries(parseResult['list_skor_rh'])
          var list_skor_gmNew = Object.entries(parseResult['list_skor_gmNew'])


          // new tph 
          var afdeling1 = Object.entries(parseResult['afdeling1'])
          var afdeling2 = Object.entries(parseResult['afdeling2'])
          var afdeling3 = Object.entries(parseResult['afdeling3'])
          var afdeling4 = Object.entries(parseResult['afdeling4'])

          var estate1 = Object.entries(parseResult['estate1'])
          var estate2 = Object.entries(parseResult['estate2'])
          var estate3 = Object.entries(parseResult['estate3'])
          var estate4 = Object.entries(parseResult['estate4'])
          var hasilRh = Object.entries(parseResult['hasilRh'])


          //mnghitung dan mengurai string dengan substrack untuk chart
          //list estate
          // console.log(listEstate);
          let textArr = listEstate.map(subArr => subArr[1]);
          var listEst = '['
          listEstate.forEach(element => {
            if (!element[1]['est']) {
              listEst += '"' + element[1]['est'] + '",'
            }
          });
          listEst = listEst.substring(0, listEst.length - 1);
          listEst += ']'
          var listEstJson = JSON.parse(listEst)
          //list wilayah
          var listWilChart = '['
          listWill2.forEach(element => {
            listWilChart += '"' + element[1] + '",'
          });
          listWilChart = listWilChart.substring(0, listWilChart.length - 1);
          listWilChart += ']'
          var listWilChartJson = JSON.parse(listWilChart)
          //brondolan tgl
          var valBtTph = '['
          listBtTph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valBtTph += '"' + element[1] + '",'
            }
          });
          valBtTph = valBtTph.substring(0, valBtTph.length - 1);
          valBtTph += ']'
          var valBtTphJson = JSON.parse(valBtTph)
          //karung tgl
          var valKRtgl = '['
          listKRTph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valKRtgl += '"' + element[1] + '",'
            }
          });
          valKRtgl = valKRtgl.substring(0, valKRtgl.length - 1);
          valKRtgl += ']'
          var valKRTtphJson = JSON.parse(valKRtgl)
          //buah tinggal
          var valBHtgl = '['
          lisBHtph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valBHtgl += '"' + element[1] + '",'
            }
          });
          valBHtgl = valBHtgl.substring(0, valBHtgl.length - 1);
          valBHtgl += ']'
          var valBHtglJson = JSON.parse(valBHtgl)

          //buah restan tidak di laporkan
          var valRSnone = '['
          listRStph.forEach(element => {
            if (!element[0].includes('CWS')) {
              valRSnone += '"' + element[1] + '",'
            }
          });
          valRSnone = valRSnone.substring(0, valRSnone.length - 1);
          valRSnone += ']'
          var valRSnoneJson = JSON.parse(valRSnone)
          // mengubah data estate agar bisa mengurangi nilai
          var categoryEst = '['
          listEstate.forEach(element => {
            categoryEst += '"' + element[1]['est'] + '",'
          });
          categoryEst = categoryEst.substring(0, categoryEst.length - 1);
          categoryEst += ']'
          var categoryEstJson = JSON.parse(categoryEst)

          /// mengubah data wilayah agar bisa mengurangi nilai
          //brondolan tgl
          var valBtTphWil = '['
          listBtTphWil.forEach(element => {
            valBtTphWil += '"' + element[1] + '",'
          });
          valBtTphWil = valBtTphWil.substring(0, valBtTphWil.length - 1);
          valBtTphWil += ']'
          var valBtTphWilJson = JSON.parse(valBtTphWil)
          var arrayvalBtTphWilJson = valBtTphWilJson;
          for (let i = 0; i < arrayvalBtTphWilJson.length; i++) {

            arrayvalBtTphWilJson.splice(3);

          }

          //karung tinggal
          var valKRtglWil = '['
          listKRTphWil.forEach(element => {
            valKRtglWil += '"' + element[1] + '",'
          });
          valKRtglWil = valKRtglWil.substring(0, valKRtglWil.length - 1);
          valKRtglWil += ']'
          var valKRtglWilJson = JSON.parse(valKRtglWil)
          var arrayvalKRtglWilJson = valKRtglWilJson;
          for (let i = 0; i < arrayvalKRtglWilJson.length; i++) {

            arrayvalKRtglWilJson.splice(3);

          }

          //buah tinggal
          var valBHtglWil = '['
          lisBHtphWil.forEach(element => {
            valBHtglWil += '"' + element[1] + '",'
          });
          valBHtglWil = valBHtglWil.substring(0, valBHtglWil.length - 1);
          valBHtglWil += ']'
          var valBHtglWilJson = JSON.parse(valBHtglWil)
          var arrayvalBHtglWilJson = valBHtglWilJson;
          for (let i = 0; i < arrayvalBHtglWilJson.length; i++) {
            {
              arrayvalBHtglWilJson.splice(3);
            }
          }

          //buah restant
          var valRSnoneWil = '['
          listRStphWil.forEach(element => {
            valRSnoneWil += '"' + element[1] + '",'
          });
          valRSnoneWil = valRSnoneWil.substring(0, valRSnoneWil.length - 1);
          valRSnoneWil += ']'
          var valRSnoneWilJson = JSON.parse(valRSnoneWil)
          var arrayvalRSnoneWilJson = valRSnoneWilJson;
          for (let i = 0; i < arrayvalRSnoneWilJson.length; i++) {
            arrayvalRSnoneWilJson.splice(3);
          }

          var categoryWill = '['
          listWill.forEach(element => {
            categoryWill += '"' + element[1]['nama'] + '",'
          });
          categoryWill = categoryWill.substring(0, categoryWill.length - 1);
          categoryWill += ']'
          var categoryWillJson = JSON.parse(categoryWill)

          if (regSidak == 1) {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#3063EC', '#FF8D1A', '#FF8D1A', '#FF8D1A',
              '#FF8D1A', '#9208FD'
            ]
          } else if (regSidak == 2) {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#FF8D1A', '#FF8D1A', '#9208FD'
            ]
          } else {
            colorChart = ['#00FF00', '#00FF00', '#00FF00', '#00FF00', '#3063EC',
              '#3063EC', '#3063EC', '#3063EC', '#9208FD'
            ]
          }
          // console.log(list_estate);
          // let filteredList = list_estate.filter(item => {
          //   return item !== "SKE" && item !== "LDE" && item !== "SRE";
          // });
          renderChartTphMonth.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartKarungMonth.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartBuahTglTphMonth.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          renderChartBuahRestanNoneMonth.updateOptions({
            colors: colorChart,
            xaxis: {
              categories: textArr,
            }
          });
          will_bttMonth.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartKarungWilMonth.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartBuahTglTphWilMonth.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });
          renderChartBuahRestanNoneWilMonth.updateOptions({
            xaxis: {
              categories: listWilChartJson,
            }
          });

          // Check if the result is an empty object
          if ($.isEmptyObject(result)) {
            result = null;
            renderChartTphMonth.updateSeries([{
              name: 'Brondolan/Blok Tinggal di TPH',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartKarungMonth.updateSeries([{
              name: 'Karung/Blok  Berisi Brondolan',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartBuahTglTphMonth.updateSeries([{
              name: 'Buah/Blok  Tinggal TPH',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            renderChartBuahRestanNoneMonth.updateSeries([{
              name: 'Restan/Blok  Tidak dilaporkan',
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }])

            //perwilawyah
            will_bttMonth.updateSeries([{
              name: 'Brondolan Tinggal di TPH',
              data: [0, 0, 0]
            }])

            renderChartKarungWilMonth.updateSeries([{
              name: 'Karung Tinggal di TPH',
              data: [0, 0, 0]
            }])

            renderChartBuahTglTphWilMonth.updateSeries([{
              name: 'Buah Tinggal Di TPH',
              data: [0, 0, 0]
            }])

            renderChartBuahRestanNoneWilMonth.updateSeries([{
              name: 'Buah Restan Tidak di Laporkan',
              data: [0, 0, 0]
            }])
          } else {


            //persetate
            renderChartTphMonth.updateSeries([{
              name: 'Brondolan/Blok Tinggal di TPH',
              data: valBtTphJson
            }])

            renderChartKarungMonth.updateSeries([{
              name: 'Karung/Blok  Berisi Brondolan',
              data: valKRTtphJson
            }])

            renderChartBuahTglTphMonth.updateSeries([{
              name: 'Buah/Blok  Tinggal TPH',
              data: valBHtglJson
            }])

            renderChartBuahRestanNoneMonth.updateSeries([{
              name: 'Restan/Blok  Tidak dilaporkan',
              data: valRSnoneJson
            }])

            //perwilayah
            will_bttMonth.updateSeries([{
              name: 'Brondolan Tinggal di TPH',
              data: arrayvalBtTphWilJson
            }])

            renderChartKarungWilMonth.updateSeries([{
              name: 'Karung Tinggal di TPH',
              data: arrayvalKRtglWilJson
            }])

            renderChartBuahTglTphWilMonth.updateSeries([{
              name: 'Buah Tinggal Di TPH',
              data: arrayvalBHtglWilJson
            }])

            renderChartBuahRestanNoneWilMonth.updateSeries([{
              name: 'Buah Restan Tidak di Laporkan',
              data: arrayvalRSnoneWilJson
            }])

            ///untuk table
            //table wil 1

            var arrTbody1 = afdeling1
            // console.log(arrTbody1);
            var table1 = document.getElementById('table1Month');
            var tbody1 = document.getElementById('tbody1Month');
            // Create a copy of the original array and sort it in descending order of scores
            arrTbody1.forEach((element, index) => {

              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']
              let item4 = element[1]['skor'];

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;

              let item5 = element[1]['ranking']
              // console.log(item5);

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }

              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              /* if (item4 != 0) {
                  itemElement4.innerHTML = '<a class="detailBa" href="detailSidakTph/' +
                      element['est'] + '/' + element['afd'] + '/' +
                      firstWeek + '/' + lastWeek + '">' + element['skor'] +
                      ' </a>'
              } else {
                  itemElement4.innerText = item4
              } */
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)
              // }
            });
            // table will 1 bagian perestate skor 
            var arrTbody1 = estate1
            var table1 = document.getElementById('table1Month');
            var tbody1 = document.getElementById('tbody1Month');
            arrTbody1.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']
              let item4 = element[1]['skor'];

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;

              let item5 = element[1]['ranking']
              let item6 = element[1]['est_score']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody1.appendChild(tr)

            });




            tbodySkorRH = document.getElementById('tbodySkorRHMonth')
            var reg = ''
            if (list_skor_rh[0][0] == 1) {
              reg = 'REG I'
            } else if (list_skor_rh[0][0] == 2) {
              reg = 'REG II'
            } else if (list_skor_rh[0][0] == 3) {
              reg = 'REG III'
            } else {
              reg = 'REG IV'
            }
            tr = document.createElement('tr')
            let item1 = reg
            let item2 = 'RH - ' + list_skor_rh[0][0]
            let item3 = list_skor_rh[0][1]['nama']
            // let item4 = hasilRh[0][1]['skor']
            let item4 = hasilRh[0][1]['skor']

            // Check if item4 is less than 0, and set it to 0 if true
            item4 = (item4 < 0) ? 0 : item4;

            // let item4 = hasilRh[0][1]['skor']
            // let item4 = 'Test'
            let itemElement1 = document.createElement('td')
            let itemElement2 = document.createElement('td')
            let itemElement3 = document.createElement('td')
            let itemElement4 = document.createElement('td')

            itemElement1.classList.add("text-center")
            itemElement2.classList.add("text-center")
            itemElement3.classList.add("text-center")
            itemElement4.classList.add("text-center")
            itemElement1.style.backgroundColor = "#e8ecdc";
            itemElement2.style.backgroundColor = "#e8ecdc";
            itemElement3.style.backgroundColor = "#e8ecdc";
            if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
              itemElement3.style.color = "red";
            } else {
              itemElement3.style.color = "black";
            }


            if (item4 === '-') {
              itemElement4.style.backgroundColor = "white";
              itemElement4.style.color = "black";
            } else if (item4 >= 95) {
              itemElement4.style.backgroundColor = "#609cd4";
              itemElement4.style.color = "black";
            } else if (item4 >= 85 && item4 < 95) {
              itemElement4.style.backgroundColor = "#08b454";
              itemElement4.style.color = "black";
            } else if (item4 >= 75 && item4 < 85) {
              itemElement4.style.backgroundColor = "#fffc04";
              itemElement4.style.color = "black";
            } else if (item4 >= 65 && item4 < 75) {
              itemElement4.style.backgroundColor = "#ffc404";
              itemElement4.style.color = "black";
            } else {
              itemElement4.style.backgroundColor = "red";
              itemElement4.style.color = "black";
            }

            if (itemElement4.style.backgroundColor === "#609cd4") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#08b454") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#fffc04") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "#ffc404") {
              itemElement4.style.color = "black";
            } else if (itemElement4.style.backgroundColor === "red") {
              itemElement4.style.color = "black";
            }
            itemElement1.innerText = item1
            itemElement2.innerText = item2
            itemElement3.innerText = item3
            itemElement4.innerText = item4
            tr.appendChild(itemElement1)
            tr.appendChild(itemElement2)
            tr.appendChild(itemElement3)
            tr.appendChild(itemElement4)
            tbodySkorRH.appendChild(tr)
            // endtesting

            ///table wil 2
            var arrTbody2 = afdeling2
            var tbody2 = document.getElementById('tbody2Month');
            arrTbody2.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']
              // let item4 = element[1]['skor']

              let item4 = element[1]['skor']

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;
              let item5 = element[1]['ranking']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')
              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")

              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              /* if (item4 != 0) {
                  itemElement4.innerHTML = '<a class="detailBa" href="detailSidakTph/' +
                      element['est'] + '/' + element['afd'] + '/' +
                      firstWeek + '/' + lastWeek + '">' + element['skor'] +
                      ' </a>'
              } else {
                  itemElement4.innerText = item4
              } */
              itemElement5.innerText = item5
              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              tbody2.appendChild(tr)
              // }
            });
            //untuk estate wil 2
            var arrTbody2 = estate2
            arrTbody2.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']

              let item4 = element[1]['skor']

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;
              let item5 = element[1]['ranking']
              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody2.appendChild(tr)
              // }
            });

            ///table wil 3
            // if (regSidak != 3) {
            var arrTbody3 = afdeling3
            var tbody3 = document.getElementById('tbody3Month');
            arrTbody3.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']

              let item4 = element[1]['skor']

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;
              let item5 = element[1]['ranking']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }


              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              /* if (item4 != 0) {
                  itemElement4.innerHTML = '<a class="detailBa" href="detailSidakTph/' +
                      element['est'] + '/' + element['afd'] + '/' +
                      firstWeek + '/' + lastWeek + '">' + element['skor'] +
                      ' </a>'
              } else {
                  itemElement4.innerText = item4
              } */
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody3.appendChild(tr)
              // }
            });
            // untuk estate will 3
            var arrTbody3 = estate3
            arrTbody3.forEach(element => {
              // for (let i = 0; i < 5; i++) {
              tr = document.createElement('tr')
              let item1 = element[1]['est']
              let item2 = element[1]['afd']
              let item3 = element[1]['asisten']

              let item4 = element[1]['skor']

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;
              let item5 = element[1]['ranking']

              let itemElement1 = document.createElement('td')
              let itemElement2 = document.createElement('td')
              let itemElement3 = document.createElement('td')
              let itemElement4 = document.createElement('td')
              let itemElement5 = document.createElement('td')

              itemElement1.classList.add("text-center")
              itemElement2.classList.add("text-center")
              itemElement3.classList.add("text-center")
              itemElement4.classList.add("text-center")
              itemElement5.classList.add("text-center")
              itemElement1.style.backgroundColor = "#e8ecdc";
              itemElement2.style.backgroundColor = "#e8ecdc";
              itemElement3.style.backgroundColor = "#e8ecdc";
              if (item3.trim() === "VACANT") { // Use trim to remove leading/trailing spaces
                itemElement3.style.color = "red";
              } else {
                itemElement3.style.color = "black";
              }

              if (item4 === '-') {
                itemElement4.style.backgroundColor = "white";
                itemElement4.style.color = "black";
              } else if (item4 >= 95) {
                itemElement4.style.backgroundColor = "#609cd4";
                itemElement4.style.color = "black";
              } else if (item4 >= 85 && item4 < 95) {
                itemElement4.style.backgroundColor = "#08b454";
                itemElement4.style.color = "black";
              } else if (item4 >= 75 && item4 < 85) {
                itemElement4.style.backgroundColor = "#fffc04";
                itemElement4.style.color = "black";
              } else if (item4 >= 65 && item4 < 75) {
                itemElement4.style.backgroundColor = "#ffc404";
                itemElement4.style.color = "black";
              } else {
                itemElement4.style.backgroundColor = "red";
                itemElement4.style.color = "black";
              }

              if (itemElement4.style.backgroundColor === "#609cd4") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#08b454") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#fffc04") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "#ffc404") {
                itemElement4.style.color = "black";
              } else if (itemElement4.style.backgroundColor === "red") {
                itemElement4.style.color = "black";
              }
              /* if (item4 != 0) {
                  itemElement4.innerHTML = '<a class="detailBa" href="BaSidakTPH/' + element[
                          'est'] + '/' + firstWeek + '/' + lastWeek +
                      '" target="_blank">' + element['skor'] + ' </a>'
              } else {
                  itemElement4.innerText = item4
              } */
              itemElement1.innerText = item1
              itemElement2.innerText = item2
              itemElement3.innerText = item3
              itemElement4.innerText = item4
              itemElement5.innerText = item5

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)

              tbody3.appendChild(tr)
              // }
            });



            // }
          }
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
      })
    }

    function setBackgroundColor(element, score) {
      if (score >= 95) {
        element.style.backgroundColor = "#609cd4";
      } else if (score >= 85) {
        element.style.backgroundColor = "#08b454";
      } else if (score >= 75) {
        element.style.backgroundColor = "#fffc04";
      } else if (score >= 65) {
        element.style.backgroundColor = "#ffc404";
      } else if (score === '-') {
        element.style.backgroundColor = "white";
      } else {
        element.style.backgroundColor = "red";
      }
      element.style.color = "black";
    }

    function getDataTphYear() {
      changeClassYear()
      var _token = $('input[name="_token"]').val();
      var yearSidak = document.getElementById('inputYear').value
      var regSidak = document.getElementById('regionalSidakYear').value
      $.ajax({
        url: "{{ route('getBtTphYear') }}",
        method: "get",
        data: {
          year: yearSidak,
          reg: regSidak,
          _token: _token
        },
        success: function(result) {
          Swal.close();
          //parsing result ke json untuk dalam estate
          var parseResult = JSON.parse(result)
          var newsidakend = Object.entries(parseResult['newsidakend'])
          var rhdata = Object.entries(parseResult['rhdata'])

          // console.log(newsidakend);

          let table1 = newsidakend[0]
          let table2 = newsidakend[1]
          let table3 = newsidakend[2]

          var theadreg = document.getElementById('tbodySkorRHYear');

          // console.log(regional);

          tr = document.createElement('tr')
          let reg1 = rhdata[1][1]
          let reg2 = rhdata[3][1]
          let reg3 = '-'
          let reg4 = rhdata[0][1]
          // let reg4 = '-'
          let regElement1 = document.createElement('td')
          let regElement2 = document.createElement('td')
          let regElement3 = document.createElement('td')
          let regElement4 = document.createElement('td')

          regElement1.classList.add("text-center")
          regElement2.classList.add("text-center")
          regElement3.classList.add("text-center")
          regElement4.classList.add("text-center")

          regElement1.innerText = reg1;
          regElement2.innerText = reg2;
          regElement3.innerText = reg3;
          regElement4.innerText = reg4;
          setBackgroundColor(regElement4, reg4);
          tr.appendChild(regElement1)
          tr.appendChild(regElement2)
          tr.appendChild(regElement3)
          tr.appendChild(regElement4)

          theadreg.appendChild(tr)
          //     $('#tbody1Year').empty()
          // $('#tbody2Year').empty()
          // $('#tbody3Year').empty()
          var trekap1 = document.getElementById('tbody1Year');
          Object.keys(table1[1]).forEach(key => {
            Object.keys(table1[1][key]).forEach(subKey => {
              let item1 = table1[1][key][subKey]['est'];
              let item2 = table1[1][key][subKey]['afd'];
              let item3 = table1[1][key][subKey]['nama']
              let item4 = table1[1][key][subKey]['total_score'];

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;

              let item5 = table1[1][key][subKey]['rank'] ?? '-';

              let bg = table1[1][key][subKey]['bgcolor'];

              // Create table row and cell for each 'total' value
              let tr = document.createElement('tr');
              let itemElement1 = document.createElement('td');
              let itemElement2 = document.createElement('td');
              let itemElement3 = document.createElement('td');
              let itemElement4 = document.createElement('td');
              let itemElement5 = document.createElement('td');



              itemElement1.classList.add("text-center");
              itemElement1.innerText = item1;
              itemElement2.innerText = item2;
              itemElement3.innerText = item3;
              itemElement4.innerText = item4;
              itemElement5.innerText = item5

              setBackgroundColor(itemElement4, item4);
              tr.style.backgroundColor = bg;

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              trekap1.appendChild(tr);
            });
          });

          var trekap2 = document.getElementById('tbody2Year');
          Object.keys(table2[1]).forEach(key => {
            Object.keys(table2[1][key]).forEach(subKey => {
              let item1 = table2[1][key][subKey]['est'];
              let item2 = table2[1][key][subKey]['afd'];
              let item3 = table2[1][key][subKey]['nama'] ?? '-'
              let item4 = table2[1][key][subKey]['total_score'];

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;

              let item5 = table2[1][key][subKey]['rank'] ?? '-';

              let bg = table2[1][key][subKey]['bgcolor'];

              // Create table row and cell for each 'total' value
              let tr = document.createElement('tr');
              let itemElement1 = document.createElement('td');
              let itemElement2 = document.createElement('td');
              let itemElement3 = document.createElement('td');
              let itemElement4 = document.createElement('td');
              let itemElement5 = document.createElement('td');



              itemElement1.classList.add("text-center");
              itemElement1.innerText = item1;
              itemElement2.innerText = item2;
              itemElement3.innerText = item3;
              itemElement4.innerText = item4;
              itemElement5.innerText = item5

              setBackgroundColor(itemElement4, item4);
              tr.style.backgroundColor = bg;

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              trekap2.appendChild(tr);
            });
          });

          var trekap3 = document.getElementById('tbody3Year');
          Object.keys(table3[1]).forEach(key => {
            Object.keys(table3[1][key]).forEach(subKey => {
              let item1 = table3[1][key][subKey]['est'];
              let item2 = table3[1][key][subKey]['afd'];
              let item3 = table3[1][key][subKey]['nama']
              let item4 = table3[1][key][subKey]['total_score'];

              // Check if item4 is less than 0, and set it to 0 if true
              item4 = (item4 < 0) ? 0 : item4;

              let item5 = table3[1][key][subKey]['rank'] ?? '-';

              let bg = table3[1][key][subKey]['bgcolor'];

              // Create table row and cell for each 'total' value
              let tr = document.createElement('tr');
              let itemElement1 = document.createElement('td');
              let itemElement2 = document.createElement('td');
              let itemElement3 = document.createElement('td');
              let itemElement4 = document.createElement('td');
              let itemElement5 = document.createElement('td');



              itemElement1.classList.add("text-center");
              itemElement1.innerText = item1;
              itemElement2.innerText = item2;
              itemElement3.innerText = item3;
              itemElement4.innerText = item4;
              itemElement5.innerText = item5

              setBackgroundColor(itemElement4, item4);
              tr.style.backgroundColor = bg;

              tr.appendChild(itemElement1)
              tr.appendChild(itemElement2)
              tr.appendChild(itemElement3)
              tr.appendChild(itemElement4)
              tr.appendChild(itemElement5)
              trekap3.appendChild(tr);
            });
          });
        },
        error: function() {
          Swal.close();
        }
      })
    }

    function graphFilterYear() {
      var reg = document.getElementById('regionalSidakYear').value
      var est = document.getElementById('estSidakYear').value
      var yearGraph = document.getElementById('inputYear').value
      var _token = $('input[name="_token"]').val()
      $.ajax({
        url: "{{ route('graphFilterYear') }}",
        method: "get",
        data: {
          est: est,
          yearGraph: yearGraph,
          reg: reg,
          _token: _token
        },
        success: function(result) {
          Swal.close();
          var parseResult = JSON.parse(result);
          var chart_brd = parseResult['brdgraph'];
          var chart_krg = parseResult['graphbuah'];
          var ktg = parseResult['ktg'];

          // Assuming chart_brd is an array of arrays, you can directly use it for data in Apex chart
          renderChartTphYear.updateSeries([{
            name: 'Brondolan Tinggal',
            data: chart_brd
          }]);

          // If ktg is an array, you can use it for x-axis categories
          renderChartTphYear.updateOptions({
            xaxis: {
              categories: ktg
            }
          });

          // renderChartTphYear.updateSeries([{
          //   name: 'Brondolan Tinggal',
          //   data: chart_brd
          // }])

          renderChartKarungYear.updateSeries([{
            name: 'Buah Tinggal',
            data: chart_krg
          }])


          renderChartKarungYear.updateOptions({
            xaxis: {
              categories: ktg
            }
          })


          // renderChartBuahTglTphYear.updateSeries([{
          //   name: 'Buah/Blok  Tinggal TPH',
          //   data: chart_krg
          // }])


        },
        error: function() {
          Swal.close();
        }
      });
    }
  });


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
    const showBtn = document.getElementById('btnShowMonth');
    const regionalSelect = document.getElementById('regionalSidakMonth');

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
          est: [16, 18, 16, 3],
          rank: [16, 18, 16, 3]
        },
        '2': {
          est: [17, 13, 10, 5],
          rank: [17, 13, 10, 5]
        },
        '3': {
          est: [20, 11, 10, 3],
          rank: [20, 11, 10, 3]
        }
      };

      const tbodies = ['tbody1Month', 'tbody2Month', 'tbody3Month', 'plasmaMonth'];
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
    const estBtn = document.getElementById('sort-afd-btnWeek');
    const rankBtn = document.getElementById('sort-est-btnWeek');
    const showBtn = document.getElementById('btnShow');
    const regionalSelect = document.getElementById('regionalSidak');

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
          est: [16, 18, 16, 3],
          rank: [16, 18, 16, 3]
        },
        '2': {
          est: [17, 13, 10, 5],
          rank: [17, 13, 10, 5]
        },
        '3': {
          est: [20, 11, 10, 3],
          rank: [20, 11, 10, 3]
        }
      };

      const tbodies = ['tbody1', 'tbody2', 'tbody3', 'plasma'];
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

  function openNewTabAndSendData() {
    // Define the URL of the new page where you want to send the data
    const newPageUrl = '/getimgqc'; // Replace with the actual URL

    // Retrieve the CSRF token from the input field using jQuery
    var csrfToken = $('input[name="_token"]').val(); // Changed variable name to csrfToken

    // Create an empty form element
    const form = document.createElement('form');
    form.method = 'POST'; // Change the method to POST
    form.action = newPageUrl;

    // Add a hidden input field for the CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    // Add hidden input fields for your data
    const tables = [
      document.getElementById('table1'),
      document.getElementById('table2'),
      document.getElementById('table3'),
      document.getElementById('table4')
    ];
    let date = document.getElementById('inputDateMonth').value;
    let reg = document.getElementById('regionalSidakMonth').value;


    // Track how many tables have been processed
    let tablesProcessed = 0;

    // Function to submit the form when all tables are processed
    function submitFormIfReady() {
      tablesProcessed++;
      if (tablesProcessed === tables.length) {
        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'date';
        dateInput.value = date;

        const regInput = document.createElement('input');
        regInput.type = 'hidden';
        regInput.name = 'reg';
        regInput.value = reg;
        const title = document.createElement('input');
        title.type = 'hidden';
        title.name = 'title';
        title.value = 'Sidak QC Mutu Transport';

        const href = document.createElement('input');
        href.type = 'hidden';
        href.name = 'href';
        href.value = '/dashboardtph';

        form.appendChild(dateInput);
        form.appendChild(regInput);
        form.appendChild(title);
        form.appendChild(href);

        // Submit the form to open the new tab
        document.body.appendChild(form);
        form.submit();
      }
    }

    tables.forEach((table, index) => {
      const options = {
        scale: 10, // Increase the scale for higher resolution (adjust as needed)
      };

      html2canvas(table, options).then(canvas => {
        const dataURL = canvas.toDataURL('image/jpeg');
        const base64Data = dataURL.split(',')[1];

        // Create a hidden input field for each table's data
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `table${index + 1}`;
        input.value = base64Data;

        form.appendChild(input);

        // Check if it's the last table, then add date and reg and submit
        if (index === tables.length - 1) {
          submitFormIfReady();
        } else {
          submitFormIfReady();
        }
      });
    });
  }
</script>