@include('layout/header')
<style>
    .table-wrapper {
        overflow-x: auto;
        /* margin: 0 auto; */
        margin-left: 10px;
        margin-right: 10px;
    }

    .my-table {
        border-collapse: collapse;
        width: 100%;
        text-align: center;
        margin-bottom: 2rem;
    }

    .my-table th,
    .my-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .my-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
</style>
<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Perumahan Afdeling</a>
                        <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Perumahan Estate</a>

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>SUMMARY SCORE PERUMAHAN AFDELING REGIONAL - I

                                </b></h5>
                        </div>
                        <div class="content">
                            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                                <div class="row w-100">
                                    <div class="col-md-2 offset-md-8">
                                        {{csrf_field()}}
                                        <select class="form-control" id="afdreg">
                                            <option value="1" selected>Regional 1</option>
                                            <option value="2">Regional 2</option>
                                            <option value="3">Regional 3</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <select class="form-control" id="tahunafd">
                                            <option value="2023" selected>2023</option>
                                            <option value="2022">2022</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="btnShow">Show</button>
                            </div>
                        </div>



                        <div class="table-wrapper">
                            <table class="my-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">EST</th>
                                        <th rowspan="2">AFDELING</th>
                                        <th rowspan="2">Asisten</th>
                                        <th colspan="14" id="yearHeader" style="text-align: center;">2023</th>

                                    </tr>
                                    <tr>
                                        @foreach($shortMonth as $items)
                                        <th>{{$items}}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody id="data_afd">
                                    <tr>
                                        <td>1</td>
                                        <td>Kenambui</td>
                                        <td>OA</td>
                                        <td>Jojok</td>
                                        <td>88,5</td>
                                        <td>88,5</td>
                                        <td>88,5</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>87,9</td>
                                        <td>Good</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>SUMMARY SCORE PERUMAHAN ESTATE REGIONAL - I</b></h5>
                        </div>
                        <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                            <div class="row w-100">
                                <div class="col-md-2 offset-md-8">
                                    {{csrf_field()}}
                                    <select class="form-control" id="estreg">
                                        <option value="1" selected>Regional 1</option>
                                        <option value="2">Regional 2</option>
                                        <option value="3">Regional 3</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    {{csrf_field()}}
                                    <select class="form-control" id="tahunest">
                                        <option value="2023" selected>2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3" style="float: right" id="btnShoWEst">Show</button>
                        </div>

                        <div class="table-wrapper">
                            <table class="my-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">UNIT KERJA</th>
                                        <th rowspan="2">KODE</th>
                                        <th rowspan="2">PIC</th>
                                        <th colspan="14" id="yearHeader2" style="text-align: center;">2023</th>

                                    </tr>
                                    <tr>
                                        @foreach($shortMonth as $items)
                                        <th>{{$items}}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody id="data_est">
                                    <tr>
                                        <td>1</td>
                                        <td>Kenambui</td>
                                        <td>OA</td>
                                        <td>Jojok</td>
                                        <td>88,5</td>
                                        <td>88,5</td>
                                        <td>88,5</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>87,9</td>
                                        <td>Good</td>
                                    </tr>
                                </tbody>
                            </table>
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
    $(document).ready(function() {
        //untuk table etc
        getAFD()
        getEST()

    });

    document.getElementById('btnShow').onclick = function() {
        getAFD();
    }
    document.getElementById('btnShoWEst').onclick = function() {
        getEST();
    }

    function getAFD() {

        var reg = '';
        var tahun = '';

        var reg = document.getElementById('afdreg').value;
        var tahun = document.getElementById('tahunafd').value;
        var _token = $('input[name="_token"]').val();
        document.getElementById('yearHeader').innerHTML = tahun;

        $.ajax({
            url: "{{ route('getAFD') }}",
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


                var parseResult = JSON.parse(result)
                var bulan = Object.entries(parseResult['bulan'])




            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }

    function getEST() {

        var reg = '';
        var tahun = '';

        var reg = document.getElementById('estreg').value;
        var tahun = document.getElementById('tahunest').value;
        var _token = $('input[name="_token"]').val();

        document.getElementById('yearHeader2').innerHTML = tahun;

        $.ajax({
            url: "{{ route('estAFD') }}",
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


                var parseResult = JSON.parse(result)
                var bulan = Object.entries(parseResult['bulan'])



            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }
</script>