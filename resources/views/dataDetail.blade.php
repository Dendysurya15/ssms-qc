<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<!-- JavaScript dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>


@include('layout/header')


<style>
    .Wraping {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
        padding: 0;
        /* Remove padding */
    }


    .legend {
        padding: 6px 8px;
        font: 14px Arial, Helvetica, sans-serif;
        background: white;
        /* background: rgba(255, 255, 255, 0.8); */
        /*box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);*/
        /*border-radius: 5px;*/
        line-height: 24px;
        color: #555;
    }

    .legend h4 {
        text-align: center;
        font-size: 16px;
        margin: 2px 12px 8px;
        color: #777;
    }

    .legend span {
        position: relative;
        bottom: 3px;
    }

    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin: 0 8px 0 0;
        opacity: 0.7;
    }

    .legend i.icon {
        background-size: 18px;
        background-color: rgba(255, 255, 255, 1);
    }

    .text-icon-estate {
        font-size: 15pt;
        color: white;
        text-align: center;
        opacity: 0.6;
    }

    .blok_visit {
        color: white;
        text-align: center;
        opacity: 0.7;
    }

    .blok_all {
        color: white;
        font-size: 7pt;
        text-align: center;
        opacity: 0.5;
    }

    .text-icon-blok {
        color: white;
        text-align: center;
        opacity: 0.7;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        /* Remove the margin property to prevent centering */
    }



    th,
    td {
        border: 1px solid black;
        text-align: center;
        padding: 8px;
    }

    .sticky-footer {
        margin-top: auto;
        /* Push the footer to the bottom */
    }

    .my-table {
        margin-bottom: 50px;
        /* Adjust this value as needed */
    }

    .header {
        align-items: center;
    }

    .logo-container {
        display: flex;
        align-items: center;
    }

    .logo {
        height: 80px;
        width: auto;
    }

    .text-container {
        margin-left: 15px;
    }

    .pt-name,
    .qc-name {
        margin: 0;
    }

    .center-space {
        flex-grow: 1;
    }

    .right-container {
        text-align: right;
    }

    .rights-container {
        display: flex;

        justify-content: flex-end;
    }


    .form-inline {
        display: flex;
        align-items: center;
    }

    /* The Modal (background) */
    /* Add Bootstrap-like styling for the modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.5s;
    }




    /* Add Bootstrap-like button styling */
    .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        user-select: none;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-primary:hover,
    .btn-secondary:hover {
        filter: brightness(90%);
    }

    .btn-primary:active,
    .btn-secondary:active {
        filter: brightness(80%);
    }

    .btn:focus,
    .btn:active {
        outline: none;
    }

    /* Add Bootstrap-like form control styling */
    .form-control {
        display: block;
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }


    /* The image inside the modal */
    #modalImage {
        width: 100%;
        /* Adjust this value to change the image width */
        max-height: 70vh;
        /* Limit the height of the image */
        object-fit: contain;
        /* Maintain aspect ratio */
    }

    /* Add Animation */
    @keyframes animatetop {
        from {
            top: -300px;
            opacity: 0;
        }

        to {
            top: 0;
            opacity: 1;
        }
    }

    /* The Close Button */
    .close {
        color: white;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }



    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes scaleUp {
        from {
            transform: scale(0.95);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .header-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .header {
        width: 98%;
    }

    .date {
        display: flex;
        align-items: center;
    }

    @media (max-width: 767px) {
        .header {
            flex-direction: column;
        }

        .right-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-inline {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .date {
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 0;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    }

    /* The image inside the modal */
</style>


<div class="content-wrapper">



    <div class="card table_wrapper">
        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <h2>REKAP HARIAN SIDAK INPEKSI </h2>
        </div>
        <div class="header-container">
            <div class="header d-flex justify-content-center mt-3 mb-2 ml-3 mr-3">
                <div class="logo-container">

                    <img src="{{ asset('img/Logo-SSS.png') }}" alt="Logo" class="logo">
                    <div class="text-container">
                        <div class="pt-name">PT. SAWIT SUMBERMAS SARANA, TBK</div>
                        <div class="qc-name">QUALITY CONTROL</div>
                    </div>
                </div>
                <div class="center-space"></div>

                <div class="right-container">
                    <form action="{{ route('filterDataDetail') }}" method="POST" class="form-inline">
                        <div class="date">
                            {{ csrf_field() }}

                            <input type="hidden" name="est" id="est" value="{{$est}}">
                            <input type="hidden" name="afd" id="afd" value="{{$afd}}">

                            <div class="form-group">
                                <select class="form-control mb-2 mr-sm-2" name="date" id="inputDate" onchange="updateDate();">
                                    <option value="" disabled selected hidden>Pilih tanggal</option>
                                    <optgroup label="Mutu Ancak">
                                        @foreach($ancakDates as $ancakDate)
                                        <option value="{{ $ancakDate->date }}" {{ $ancakDate->date === $tanggal ?
                                            'selected' : '' }}>{{ $ancakDate->date }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Mutu Buah">
                                        @foreach($buahDates as $buah)
                                        <option value="{{ $buah->date }}" {{ $buah->date === $tanggal ? 'selected' : ''
                                            }}>{{ $buah->date }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Mutu Transport">
                                        @foreach($TransportDates as $TransportDate)
                                        <option value="{{ $TransportDate->date }}" {{ $TransportDate->date === $tanggal
                                            ? 'selected' : '' }}>{{ $TransportDate->date }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Mutu all">
                                        @foreach($commonDates as $common)
                                        <option value="{{ $common->date }}" {{ $common->date === $tanggal ? 'selected' :
                                            '' }}>{{ $common->date }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <button type="button" class="ml-2 btn btn-primary mb-2" id="show-button" onclick="updateTanggal();" disabled>Show</button>

                        </div>
                    </form>

                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}-{{$afd}}</div>
                    <!-- <div class="afd mt-2"> Rrgional : {{$reg}}-{{$afd}}</div> -->
                    <div class="afd">TANGGAL : <span id="selectedDate">{{ $tanggal }}</span></div>
                </div>
            </div>
        </div>
        <!-- animasi loading -->
        <div id="lottie-container" style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            </div>
        </div>
        <div id="lottie-container1" style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation" style="width: 100px; height: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            </div>
        </div>


        <!-- end animasi -->
    </div>
    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3">
        <button id="back-to-data-btn" class="btn btn-primary" onclick="goBack()">Back to Data</button>
        <div class="d-flex align-items-center">
            <!-- Your existing PDF and Excel buttons and their respective forms -->
            <style>
                .download-btn {
                    display: inline-flex;
                    align-items: center;
                    padding: 8px 12px;
                    background-color: #007bff;
                    color: #ffffff;
                    font-weight: 500;
                    text-decoration: none;
                    border-radius: 4px;
                    cursor: pointer;
                    border: none;
                }

                .download-btn:hover,
                .download-btn:focus {
                    background-color: #0056b3;
                }

                .download-btn svg {
                    width: 24px;
                    height: 24px;
                    margin-right: 8px;
                }
            </style>

            <form action="{{ route('pdfBA') }}" method="POST" class="form-inline" style="display: inline;" target="_blank">
                {{ csrf_field() }}
                <!-- Your hidden inputs -->

                <input type="hidden" name="estBA" id="estpdf" value="{{$est}}">
                <input type="hidden" name="afdBA" id="afdpdf" value="{{$afd}}">
                <input type="hidden" name="tglPDF" id="tglPDF" value="{{ $tanggal }}">
                <input type="hidden" name="regPDF" id="regPDF" value="{{ $reg }}">
                <button type="submit" class="download-btn ml-2" id="download-button" disabled>
                    <div id="lottie-download" style="width: 24px; height: 24px; display: inline-block;"></div> Download
                    BA PDF
                </button>
            </form>

            <form action="{{ route('pdfBA_excel') }}" method="POST" class="form-inline" style="display: inline;" target="_blank">
                {{ csrf_field() }}
                <!-- Your hidden inputs -->
                <input type="hidden" name="estBA_excel" id="estpdf" value="{{$est}}">
                <input type="hidden" name="afdBA_excel" id="afdpdf" value="{{$afd}}">
                <input type="hidden" name="tglPDF_excel" id="tglPDF_excel" value="{{ $tanggal }}">
                <input type="hidden" name="regExcel" id="regExcel" value="{{$reg}}">
                <button type="submit" class="download-btn ml-2" id="download-excel-button" disabled>
                    <div id="lottie-download" style="width: 24px; height: 24px; display: inline-block;"></div> Download
                    BA Excel
                </button>
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <h1 style="text-align: center;">Tabel Mutu Ancak</h1>
                        <table class="table table-striped table-bordered" id="mutuAncakTable">
                            <thead>
                                <!-- Table header content -->
                            </thead>
                            <tbody>
                                <!-- Table body content will be dynamically generated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <h1 style="text-align: center;">Tabel Mutu Buah</h1>
                        <table class="table table-striped table-bordered" id="mutuBuahable">
                            <thead>
                                <!-- Table header content -->
                            </thead>
                            <tbody>
                                <!-- Table body content will be dynamically generated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h1 style="text-align: center;">Tabel Mutu Transport</h1>
                    <table class="table table-striped" id="mutuTransportable">
                        <thead>
                            <!-- Table header content -->
                        </thead>
                        <tbody>
                            <!-- Table body content will be dynamically generated -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>









    <!-- 
    <div class="d-flex justify-content-center mt-3 mb-4 ml-3 mr-3 border border-dark ">
        <div class="Wraping">
            <h1 class="text-center">Tabel Mutu Ancak</h1>
            <table class="table table-striped table-bordered" border="1" id="mutu_ancak">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Estate</th>
                        <th>Afdeling</th>
                        <th>Blok</th>
                        <th>Petugas</th>
                        <th>Sph</th>
                        <th>Br1</th>
                        <th>Br2</th>
                        <th>Jalur_masuk</th>
                        <th>Status_panen</th>
                        <th>Kemandoran</th>
                        <th>Ancak_pemanen</th>
                        <th>Pokok_panen</th>
                        <th>Pokok_sample</th>
                        <th>Jjg_panen</th>
                        <th>Brd_p</th>
                        <th>Brd_k</th>
                        <th>brd_gl</th>
                        <th>bt_s</th>
                        <th>bt_m1</th>
                        <th>bt_m2</th>
                        <th>bt_m3</th>
                        <th>Ps</th>
                        <th>frond_stacking</th>
                        <th>Pokok_kuning</th>
                        <th>Piringan_semak</th>
                        <th>Underpruning</th>
                        <th>Overpruning</th>



                        <th>Komentar</th>
                        <th>Status</th>
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep')
                        <th>Aksi</th>
                        @endif

                    </tr>
                </thead>
                <tbody id="tab1">
                </tbody>
            </table>
            <div id="pagination"></div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
        <div class="Wraping">
            <h1 class="text-center">Tabel Mutu Buah</h1>
            <table class="table table-striped table-bordered" border="1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Estate</th>
                        <th>Afdeling</th>
                        <th>Tph Baris</th>
                        <th>Blok</th>
                        <th>Status Panen</th>
                        <th>Petugas</th>
                        <th>Ancak Pemanen</th>
                        <th>Bmk</th>
                        <th>Bmt</th>
                        <th>Empty</th>
                        <th>Jumlah janjang</th>
                        <th>Overripe</th>
                        <th>Abnormal</th>
                        <th>Vcut</th>
                        <th>Alas_br</th>
                        <th>Komentar</th>
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep')
                        <th>Aksi</th>
                        @endif

                    </tr>
                </thead>


                <tbody id="tab2">
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
        <div class="Wraping">
            <h1 class="text-center">Tabel Mutu Transport</h1>
            <table class="table table-striped table-bordered" border="1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Estate</th>
                        <th>Afdeling</th>
                        <th>Blok</th>
                        <th>Status Panen</th>
                        <th>Tph_baris</th>
                        <th>Petugas</th>
                        <th>Bt_tph</th>
                        <th>Brd_tph</th>
                        <th>Komentar</th>
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep')
                        <th>Aksi</th>
                        @endif

                    </tr>
                </thead>


                <tbody id="tab3">
                </tbody>
            </table>
        </div>
    </div> -->

    <!-- Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modalImage" src="" style="width: 100%;">
        </div>
    </div>
    <style>
        .modal-custom-update {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content-custom-update {
            background-color: #fefefe;
            margin-left: 12%;
            margin-top: 8%;
            padding: 20px;
            border: 1px solid #888;
            max-width: 80%;
            /* Adjust the width as needed */
            text-align: center;
            /* display: flex; */
            /* justify-content: center;
            align-items: center; */
        }

        .modal-content-custom-update h2 {
            margin-top: 0;
        }

        .modal-content-custom-update .form-control {
            width: 100%;
        }

        .modal-content-custom-update .btn {
            margin-top: 10px;
        }

        .modal-content-custom-update .mb-3 {
            margin-bottom: 15px;
        }
    </style>

    <!-- <div id="update-modal" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Ancak</h2>
            <button id="close-modal" class="btn btn-secondary">Tutup</button>
            <form id="update-form" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-id" name="id">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="row m-1">
                    <div class="col">

                        <label for="update-blokCak" class="col-form-label">Blok</label>
                        <input type="text" class="form-control" id="update-blokCak" name="blokCak" value="" required>


                        <label for="update-StatusPnen" class="col-form-label">Status Panen</label>
                        <input type="text" class="form-control" id="update-StatusPnen" name="StatusPnen" value="" required>


                        <label for="update-sph" class="col-form-label">SPH</label>
                        <input type="text" class="form-control" id="update-sph" name="sph" value="" required>


                        <label for="update-br1" class="col-form-label">BR 1</label>
                        <input type="text" class="form-control" id="update-br1" name="br1" value="" required>


                        <label for="update-br2" class="col-form-label">BR 2</label>
                        <input type="text" class="form-control" id="update-br2" name="br2" value="" required>


                        <label for="update-sampCak" class="col-form-label">Sample</label>
                        <input type="text" class="form-control" id="update-sampCak" name="sampCak" value="" required>


                        <label for="update-pkKuning" class="col-form-label">Pokok Kuning</label>
                        <input type="text" class="form-control" id="update-pkKuning" name="pkKuning" value="" required>

                    </div>
                    <div class="col">

                        <label for="update-prSmk" class="col-form-label">Piringan Semak</label>
                        <input type="text" class="form-control" id="update-prSmk" name="prSmk" value="" required>


                        <label for="update-undrPR" class="col-form-label">Underpruning</label>
                        <input type="text" class="form-control" id="update-undrPR" name="undrPR" value="" required>


                        <label for="update-overPR" class="col-form-label">Overpruning</label>
                        <input type="text" class="form-control" id="update-overPR" name="overPR" value="" required>


                        <label for="update-jjgCak" class="col-form-label">Janjang</label>
                        <input type="text" class="form-control" id="update-jjgCak" name="jjgCak" value="" required>


                        <label for="update-brtp" class="col-form-label">BRTP</label>
                        <input type="text" class="form-control" id="update-brtp" name="brtp" value="" required>


                        <label for="update-brtk" class="col-form-label">BRTK</label>
                        <input type="text" class="form-control" id="update-brtk" name="brtk" value="" required>


                        <label for="update-brtgl" class="col-form-label">BRTGL</label>
                        <input type="text" class="form-control" id="update-brtgl" name="brtgl" value="" required>

                    </div>
                    <div class="col">

                        <label for="update-bhts" class="col-form-label">BHTS</label>
                        <input type="text" class="form-control" id="update-bhts" name="bhts" value="" required>


                        <label for="update-bhtm1" class="col-form-label">BHTM1</label>
                        <input type="text" class="form-control" id="update-bhtm1" name="bhtm1" value="" required>


                        <label for="update-bhtm2" class="col-form-label">BHTM2</label>
                        <input type="text" class="form-control" id="update-bhtm2" name="bhtm2" value="" required>


                        <label for="update-bhtm3" class="col-form-label">BHTM3</label>
                        <input type="text" class="form-control" id="update-bhtm3" name="bhtm3" value="" required>


                        <label for="update-ps" class="col-form-label">PS</label>
                        <input type="text" class="form-control" id="update-ps" name="ps" value="" required>


                        <label for="update-sp" class="col-form-label">SP</label>
                        <input type="text" class="form-control" id="update-sp" name="sp" value="" required>


                        <label for="update-pk_panenCAk" class="col-form-label">Pokok Panen</label>
                        <input type="text" class="form-control" id="update-pk_panenCAk" name="pk_panenCAk" value="" required>

                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>
    </div> -->

    <!-- <div id="update-modal-buah" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Buah</h2>
            <button id="close-modal-buah" class="btn btn-secondary">Tutup</button>
            <form id="update-formBuah" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-ids" name="id_bh">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="row m-1">
                    <div class="col">
                        <label for="update-estBH" class="col-form-label">Estate</label>
                        <input type="text" class="form-control" id="update-estBH" name="estBH" value="">


                        <label for="update-afdBH" class="col-form-label">Afdeling</label>
                        <input type="text" class="form-control" id="update-afdBH" name="afdBH" value="">


                        <label for="update-tphBH" class="col-form-label">TPH Baris</label>
                        <input type="text" class="form-control" id="update-tphBH" name="tphBH" value="">


                        <label for="update-blok_bh" class="col-form-label">Blok</label>
                        <input type="text" class="form-control" id="update-blok_bh" name="blok_bh" value="">


                        <label for="update-StatusBhpnen" class="col-form-label">Status Panen</label>
                        <input type="text" class="form-control" id="update-StatusBhpnen" name="StatusBhpnen" value="">

                        <label for="update-petugasBH" class="col-form-label">Petugas</label>
                        <input type="text" class="form-control" id="update-petugasBH" name="petugasBH" value="">

                    </div>
                    <div class="col">


                        <label for="update-pemanen_bh" class="col-form-label">Ancak Pemanen</label>
                        <input type="text" class="form-control" id="update-pemanen_bh" name="pemanen_bh" value="">


                        <label for="update-bmt" class="col-form-label">BMT</label>
                        <input type="text" class="form-control" id="update-bmt" name="bmt" value="" required>


                        <label for="update-bmk" class="col-form-label">BMK </label>
                        <input type="text" class="form-control" id="update-bmk" name="bmk" value="" required>



                        <label for="update-emptyBH" class="col-form-label">Empty</label>
                        <input type="text" class="form-control" id="update-emptyBH" name="emptyBH" value="" required>
                        <label for="update-jjgBH" class="col-form-label">Jumlah Janjang</label>
                        <input type="text" class="form-control" id="update-jjgBH" name="jjgBH" value="" required>

                        <label for="update-overBH" class="col-form-label">OverRipe</label>
                        <input type="text" class="form-control" id="update-overBH" name="overBH" value="" required>

                    </div>
                    <div class="col">





                        <label for="update-abrBH" class="col-form-label">Abnormal</label>
                        <input type="text" class="form-control" id="update-abrBH" name="abrBH" value="" required>


                        <label for="update-vcutBH" class="col-form-label">V Cut</label>
                        <input type="text" class="form-control" id="update-vcutBH" name="vcutBH" value="" required>


                        <label for="update-alsBR" class="col-form-label">Alas BR</label>
                        <input type="text" class="form-control" id="update-alsBR" name="alsBR" value="" required>


                        <label for="update-kmnBH" class="col-form-label">Komentar</label>
                        <textarea rows="4" class="form-control" id="update-kmnBH" name="kmnBH" value=""> </textarea>

                    </div>
                </div>

              
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</div> -->




    <!-- <div id="update-modal-trans" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Trans</h2>
            <button id="close-modal-trans" class="btn btn-secondary">Tutup</button>
            <form id="update-formTrans" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-id_trans" name="id_trans">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="row m-1">
                    <div class="col">
                        <label for="update-estTrans" class="col-form-label">Estate</label>
                        <input type="text" class="form-control" id="update-estTrans" name="estTrans" value="">


                        <label for="update-afd_trans" class="col-form-label">AFD</label>
                        <input type="text" class="form-control" id="update-afd_trans" name="afd_trans" value="">


                        <label for="update-blok_trans" class="col-form-label">Blok</label>
                        <input type="text" class="form-control" id="update-blok_trans" name="blok_trans" value="" required>
                        <label for="update-Status_trPanen" class="col-form-label">Status Panen</label>
                        <input type="text" class="form-control" id="update-Status_trPanen" name="Status_trPanen" value="" required>


                        <label for="update-tphbrTrans" class="col-form-label">TPH Baris</label>
                        <input type="text" class="form-control" id="update-tphbrTrans" name="tphbrTrans" value="">


                    </div>
                    <div class="col">


                        <label for="update-petugasTrans" class="col-form-label">Petugas</label>
                        <input type="text" class="form-control" id="update-petugasTrans" name="petugasTrans" value="">
                        <label for="update-bt_trans" class="col-form-label">BT </label>
                        <input type="text" class="form-control" id="update-bt_trans" name="bt_trans" value="" required>



                        <label for="update-rstTrans" class="col-form-label">Rst</label>
                        <input type="text" class="form-control" id="update-rstTrans" name="rstTrans" value="" required>


                        <label for="update-komentar_trans" class="col-form-label">Komentar</label>
                        <textarea rows="4" class="form-control" id="update-komentar_trans" name="komentar_trans" value=""> </textarea>

                    </div>

                </div>

             
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</div> -->


    <style>
        .modal-custom {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content-custom {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            text-align: center;
        }

        @media (max-width: 600px) {
            .modal-content-custom {
                width: 80%;
            }
        }
    </style>

    <div id="delete-modal" class="modal-custom">
        <div class="modal-content-custom">
            <h2>Delete Mutu Ancak</h2>
            <p>Apakah anda Yakin ingin Menghapus?</p>
            <div class="row">
                <div class="col text-right">
                    <form id="delete-form" action="{{ route('deleteBA') }}" method="POST" onsubmit="event.preventDefault(); handleDeleteFormSubmit();">
                        {{ csrf_field() }}
                        <input type="hidden" id="delete-id" name="id">

                        <div class="button-group">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
                <div class="col  text-left">

                    <button id="close-delete-modal" class="btn btn-secondary">Tutup</button>
                </div>
            </div>

        </div>
    </div>
    <div id="delete-modal-buah" class="modal-custom">
        <div class="modal-content-custom">
            <h2>Delete Mutu Buah</h2>
            <p>Apakah anda Yakin ingin Menghapus?</p>
            <div class="row">
                <div class="col text-right">

                    <form id="delete-forms" action="{{ route('deleteBA') }}" method="POST" onsubmit="event.preventDefault();">
                        {{ csrf_field() }}
                        <input type="hidden" id="delete-ids" name="ids">

                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
                <div class="col text-left">
                    <button id="close-delete-modals" class="btn btn-secondary">Tutup</button>

                </div>
            </div>
        </div>
    </div>

    <div id="delete-modal-transport" class="modal-custom">
        <div class="modal-content-custom">
            <h2>Delete Mutu Transport</h2>
            <p>Apakah anda Yakin ingin Menghapus?</p>
            <div class="row">
                <div class="col text-right">
                    <form id="delete-form-trans" method="POST" onsubmit="event.preventDefault();">
                        {{ csrf_field() }}
                        <input type="hidden" id="delete-transport" name="id_transport">

                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
                <div class="col text-left">
                    <button id="close-delete-transport" class="btn btn-secondary">Tutup</button>

                </div>
            </div>
        </div>
    </div>

    <style>
        .info.legend {
            background-color: white;
            padding: 10px;
            font-size: 14px;
        }

        .info.legend .eye-icon {
            font-size: 14px;
        }

        .info.legend .color-box {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            display: inline-block;
        }

        .legend-item {
            display: flex;
            align-items: center;
            padding: 4px;
            border: 1px solid #ccc;
            background-color: white;
        }

        .eye-icon {
            font-size: 20px;
            color: #888;
        }

        .legend-icon {
            width: 16px;
            /* Adjust the width to make the icons smaller */
            height: 16px;
            /* Adjust the height to make the icons smaller */
        }
    </style>
    <br>
    <br>

    @if ($reg == 2 || $reg == 4 )
    <div class="ml-3 mr-3 mb-3">
        <div class="row text-center tbl-fixed">
            <table class="table-responsive">
                <thead style="color: white;">
                    <tr>
                        <th class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">No</th>
                        <th class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">BLOK</th>
                        <th class="align-middle" colspan="5" rowspan="2" bgcolor="#588434">DATA BLOK SAMPEL</th>
                        <th class="align-middle" colspan="17" bgcolor="#588434">Mutu Ancak (MA)</th>
                        <th class="align-middle" colspan="8" bgcolor="blue">Mutu Transport (MT)</th>
                        <th class="align-middle" colspan="23" bgcolor="#ffc404" style="color: #000000;">Mutu Buah (MB)
                        <th class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">All Skor</th>
                        <th class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">Kategori</th>
                        </th>
                    </tr>
                    <tr>
                        <!-- Table Mutu Ancak -->
                        <th class="align-middle" colspan="6" bgcolor="#588434">Brondolan Tinggal</th>
                        <th class="align-middle" colspan="7" bgcolor="#588434">Buah Tinggal</th>
                        <th class="align-middle" colspan="3" bgcolor="#588434">Pelepah Sengkleh</th>
                        <th class="align-middle" rowspan="2" bgcolor="#588434">Total Skor</th>

                        <th class="align-middle" rowspan="2" bgcolor="blue">TPH Sampel</th>
                        <th class="align-middle" colspan="3" bgcolor="blue">Brd Tinggal</th>
                        <th class="align-middle" colspan="3" bgcolor="blue">Buah Tinggal</th>
                        <th class="align-middle" rowspan="2" bgcolor="blue">Total Skor</th>

                        <!-- Table Mutu Buah -->
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">TPH Sampel</th>
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Janjang
                            Sampel</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Mentah (A)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Matang (N)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Lewat Matang (O)
                        </th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Janjang Kosong
                            (E)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Tidak Standar
                            V-Cut</th>
                        <th class="align-middle" colspan="2" bgcolor="#ffc404" style="color: #000000;">Abnormal</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Penggunaan Karung
                            Brondolan</th>
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Skor</th>
                    </tr>
                    <tr>
                        <!-- Table Mutu Ancak -->
                        <th class="align-middle" bgcolor="#588434">Status Panen</th>
                        <th class="align-middle" bgcolor="#588434">Jumlah Pokok Sampel</th>
                        <th class="align-middle" bgcolor="#588434">Luas Ha Sampel</th>
                        <th class="align-middle" bgcolor="#588434">Jumlah Jjg Panen</th>
                        <th class="align-middle" bgcolor="#588434">AKP Realisasi</th>
                        <th class="align-middle" bgcolor="#588434">P</th>
                        <th class="align-middle" bgcolor="#588434">K</th>
                        <th class="align-middle" bgcolor="#588434">GL</th>
                        <th class="align-middle" bgcolor="#588434">Total Brd</th>
                        <th class="align-middle" bgcolor="#588434">Brd/JJG</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>
                        <th class="align-middle" bgcolor="#588434">S</th>
                        <th class="align-middle" bgcolor="#588434">M1</th>
                        <th class="align-middle" bgcolor="#588434">M2</th>
                        <th class="align-middle" bgcolor="#588434">M3</th>
                        <th class="align-middle" bgcolor="#588434">Total JJG</th>
                        <th class="align-middle" bgcolor="#588434">%</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>
                        <th class="align-middle" bgcolor="#588434">Pokok</th>
                        <th class="align-middle" bgcolor="#588434">%</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>

                        <th class="align-middle" bgcolor="blue">Butir</th>
                        <th class="align-middle" bgcolor="blue">Butir/TPH</th>
                        <th class="align-middle" bgcolor="blue">Skor</th>
                        <th class="align-middle" bgcolor="blue">Jjg</th>
                        <th class="align-middle" bgcolor="blue">Jjg/TPH</th>
                        <th class="align-middle" bgcolor="blue">Skor</th>
                        <!-- Table Mutu Buah -->
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Ya</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>
                    </tr>
                </thead>

                <tbody id="dataInspeksi">
                    <!-- <td>PLE</td>
                                    <td>OG</td> -->
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="ml-3 mr-3 mb-3">
        <div class="row text-center tbl-fixed">
            <table class="table-responsive">
                <thead style="color: white;">
                    <tr>
                        <th class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">No</th>
                        <th class="freeze-col align-middle" rowspan="3" bgcolor="#1c5870">BLOK</th>
                        <th class="align-middle" colspan="4" rowspan="2" bgcolor="#588434">DATA BLOK SAMPEL</th>
                        <th class="align-middle" colspan="17" bgcolor="#588434">Mutu Ancak (MA)</th>
                        <th class="align-middle" colspan="8" bgcolor="blue">Mutu Transport (MT)</th>
                        <th class="align-middle" colspan="23" bgcolor="#ffc404" style="color: #000000;">Mutu Buah (MB)
                        <th class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">All Skor</th>
                        <th class="align-middle" rowspan="3" bgcolor="gray" style="color: #fff;">Kategori</th>
                        </th>
                    </tr>
                    <tr>
                        <!-- Table Mutu Ancak -->
                        <th class="align-middle" colspan="6" bgcolor="#588434">Brondolan Tinggal</th>
                        <th class="align-middle" colspan="7" bgcolor="#588434">Buah Tinggal</th>
                        <th class="align-middle" colspan="3" bgcolor="#588434">Pelepah Sengkleh</th>
                        <th class="align-middle" rowspan="2" bgcolor="#588434">Total Skor</th>

                        <th class="align-middle" rowspan="2" bgcolor="blue">TPH Sampel</th>
                        <th class="align-middle" colspan="3" bgcolor="blue">Brd Tinggal</th>
                        <th class="align-middle" colspan="3" bgcolor="blue">Buah Tinggal</th>
                        <th class="align-middle" rowspan="2" bgcolor="blue">Total Skor</th>

                        <!-- Table Mutu Buah -->
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">TPH Sampel</th>
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Janjang
                            Sampel</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Mentah (A)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Matang (N)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Lewat Matang (O)
                        </th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Janjang Kosong
                            (E)</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Tidak Standar
                            V-Cut</th>
                        <th class="align-middle" colspan="2" bgcolor="#ffc404" style="color: #000000;">Abnormal</th>
                        <th class="align-middle" colspan="3" bgcolor="#ffc404" style="color: #000000;">Penggunaan Karung
                            Brondolan</th>
                        <th class="align-middle" rowspan="2" bgcolor="#ffc404" style="color: #000000;">Total Skor</th>
                    </tr>
                    <tr>
                        <!-- Table Mutu Ancak -->
                        <th class="align-middle" bgcolor="#588434">Jumlah Pokok Sampel</th>
                        <th class="align-middle" bgcolor="#588434">Luas Ha Sampel</th>
                        <th class="align-middle" bgcolor="#588434">Jumlah Jjg Panen</th>
                        <th class="align-middle" bgcolor="#588434">AKP Realisasi</th>
                        <th class="align-middle" bgcolor="#588434">P</th>
                        <th class="align-middle" bgcolor="#588434">K</th>
                        <th class="align-middle" bgcolor="#588434">GL</th>
                        <th class="align-middle" bgcolor="#588434">Total Brd</th>
                        <th class="align-middle" bgcolor="#588434">Brd/JJG</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>
                        <th class="align-middle" bgcolor="#588434">S</th>
                        <th class="align-middle" bgcolor="#588434">M1</th>
                        <th class="align-middle" bgcolor="#588434">M2</th>
                        <th class="align-middle" bgcolor="#588434">M3</th>
                        <th class="align-middle" bgcolor="#588434">Total JJG</th>
                        <th class="align-middle" bgcolor="#588434">%</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>
                        <th class="align-middle" bgcolor="#588434">Pokok</th>
                        <th class="align-middle" bgcolor="#588434">%</th>
                        <th class="align-middle" bgcolor="#588434">Skor</th>

                        <th class="align-middle" bgcolor="blue">Butir</th>
                        <th class="align-middle" bgcolor="blue">Butir/TPH</th>
                        <th class="align-middle" bgcolor="blue">Skor</th>
                        <th class="align-middle" bgcolor="blue">Jjg</th>
                        <th class="align-middle" bgcolor="blue">Jjg/TPH</th>
                        <th class="align-middle" bgcolor="blue">Skor</th>
                        <!-- Table Mutu Buah -->
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Jjg</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>

                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Ya</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">%</th>
                        <th class="align-middle" bgcolor="#ffc404" style="color: #000000;">Skor</th>
                    </tr>
                </thead>

                <tbody id="dataInspeksi">
                    <!-- <td>PLE</td>
                                    <td>OG</td> -->
                </tbody>
            </table>
        </div>
    </div>
    @endif


    <div class="card p-4">
        <h4 class="text-center mt-2" style="font-weight: bold">Tracking Plot Inpeksi - {{$est}} {{$afd}} </h4>
        <hr>
        <div id="map" style="height:650px"></div>
    </div>




    <style>
        .modal-dialog {
            max-width: 100%;
            margin: auto;
        }

        .modal-content {
            width: 100%;
        }

        .modal-body {
            text-align: center;
        }

        .modal-image {
            max-width: 100%;
            max-height: calc(100vh - 200px);
            object-fit: contain;
        }
    </style>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" id="modalCloseButton" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img class="modal-image" id="img01">
                    <p>Komentar:</p>
                    <p id="modalKomentar"></p>
                </div>
            </div>
        </div>
    </div>



    <div id="editModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-align: center; font-weight: bold;">Update Mutu Ancak</h5>
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('updateBA') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="row m-1">
                            <div class="col">

                                <label for="estate">id</label>
                                <input type="text" class="form-control" id="editId" name="id">


                                <label for="estate">Estate</label>
                                <input type="text" class="form-control" id="estate" name="estate">


                                <label for="afdeling">Afdeling</label>
                                <input type="text" class="form-control" id="afdeling" name="afdeling">

                                <label for="update-blokCak" class="col-form-label">Blok</label>
                                <input type="text" class="form-control" id="update-blokCak" name="blokCak" value="" required>


                                <label for="update-StatusPnen" class="col-form-label">Status Panen</label>
                                <input type="text" class="form-control" id="update-StatusPnen" name="StatusPnen" value="" required>

                            </div>
                            <div class="col">



                                <label for="update-sph" class="col-form-label">SPH</label>
                                <input type="text" class="form-control" id="update-sph" name="sph" value="" required>


                                <label for="update-br1" class="col-form-label">BR 1</label>
                                <input type="text" class="form-control" id="update-br1" name="br1" value="" required>


                                <label for="update-br2" class="col-form-label">BR 2</label>
                                <input type="text" class="form-control" id="update-br2" name="br2" value="" required>


                                <label for="update-sampCak" class="col-form-label">Pokok Sample</label>
                                <input type="text" class="form-control" id="update-sampCak" name="sampCak" value="" required>


                                <label for="update-pkKuning" class="col-form-label">Pokok Kuning</label>
                                <input type="text" class="form-control" id="update-pkKuning" name="pkKuning" value="" required>

                            </div>
                            <div class="col">

                                <label for="update-prSmk" class="col-form-label">Piringan Semak</label>
                                <input type="text" class="form-control" id="update-prSmk" name="prSmk" value="" required>


                                <label for="update-undrPR" class="col-form-label">Underpruning</label>
                                <input type="text" class="form-control" id="update-undrPR" name="undrPR" value="" required>


                                <label for="update-overPR" class="col-form-label">Overpruning</label>
                                <input type="text" class="form-control" id="update-overPR" name="overPR" value="" required>


                                <label for="update-jjgCak" class="col-form-label">Janjang</label>
                                <input type="text" class="form-control" id="update-jjgCak" name="jjgCak" value="" required>


                                <label for="update-brtp" class="col-form-label">BRTP</label>
                                <input type="text" class="form-control" id="update-brtp" name="brtp" value="" required>



                            </div>
                            <div class="col">
                                <label for="update-brtk" class="col-form-label">BRTK</label>
                                <input type="text" class="form-control" id="update-brtk" name="brtk" value="" required>


                                <label for="update-brtgl" class="col-form-label">BRTGL</label>
                                <input type="text" class="form-control" id="update-brtgl" name="brtgl" value="" required>


                                <label for="update-bhts" class="col-form-label">BHTS</label>
                                <input type="text" class="form-control" id="update-bhts" name="bhts" value="" required>


                                <label for="update-bhtm1" class="col-form-label">BHTM1</label>
                                <input type="text" class="form-control" id="update-bhtm1" name="bhtm1" value="" required>


                                <label for="update-bhtm2" class="col-form-label">BHTM2</label>
                                <input type="text" class="form-control" id="update-bhtm2" name="bhtm2" value="" required>



                            </div>
                            <div class="col">
                                <label for="update-bhtm3" class="col-form-label">BHTM3</label>
                                <input type="text" class="form-control" id="update-bhtm3" name="bhtm3" value="" required>


                                <label for="update-ps" class="col-form-label">PS</label>
                                <input type="text" class="form-control" id="update-ps" name="ps" value="" required>


                                <label for="update-sp" class="col-form-label">frond Stacking</label>
                                <input type="text" class="form-control" id="update-sp" name="sp" value="" required>


                                <label for="update-pk_panenCAk" class="col-form-label">Pokok Panen</label>
                                <input type="text" class="form-control" id="update-pk_panenCAk" name="pk_panenCAk" value="" required>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                    <button type="button" class="btn btn-secondary" id="closeModalBtn_Ancak" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="editModalBuah" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-align: center; font-weight: bold;">Update Mutu Buah</h5>
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm_buah" action="{{ route('updateBA') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="row m-1">
                            <div class="col">


                                <input type="hidden" class="form-control" id="editId_buah" name="editId_buah">


                                <input type="hidden" class="form-control" id="update-estBH" name="estBH" value="">



                                <input type="hidden" class="form-control" id="update-afdBH" name="afdBH" value="">


                                <label for="update-tphBH" class="col-form-label">TPH Baris</label>
                                <input type="text" class="form-control" id="update-tphBH" name="tphBH" value="">


                                <label for="update-blok_bh" class="col-form-label">Blok</label>
                                <input type="text" class="form-control" id="update-blok_bh" name="blok_bh" value="">


                                <label for="update-StatusBhpnen" class="col-form-label">Status Panen</label>
                                <input type="text" class="form-control" id="update-StatusBhpnen" name="StatusBhpnen" value="">

                                <label for="update-petugasBH" class="col-form-label">Petugas</label>
                                <input type="text" class="form-control" id="update-petugasBH" name="petugasBH" value="">



                                <label for="update-pemanen_bh" class="col-form-label">Ancak Pemanen</label>
                                <input type="text" class="form-control" id="update-pemanen_bh" name="pemanen_bh" value="">

                            </div>
                            <div class="col">


                                <label for="update-bmt" class="col-form-label">BMT</label>
                                <input type="text" class="form-control" id="update-bmt" name="bmt" value="" required>


                                <label for="update-bmk" class="col-form-label">BMK </label>
                                <input type="text" class="form-control" id="update-bmk" name="bmk" value="" required>



                                <label for="update-emptyBH" class="col-form-label">Empty</label>
                                <input type="text" class="form-control" id="update-emptyBH" name="emptyBH" value="" required>

                                <label for="update-jjgBH" class="col-form-label">Jumlah Janjang</label>
                                <input type="text" class="form-control" id="update-jjgBH" name="jjgBH" value="" required>

                                <label for="update-overBH" class="col-form-label">OverRipe</label>
                                <input type="text" class="form-control" id="update-overBH" name="overBH" value="" required>

                            </div>
                            <div class="col">


                                <label for="update-abrBH" class="col-form-label">Abnormal</label>
                                <input type="text" class="form-control" id="update-abrBH" name="abrBH" value="" required>


                                <label for="update-vcutBH" class="col-form-label">V Cut</label>
                                <input type="text" class="form-control" id="update-vcutBH" name="vcutBH" value="" required>


                                <label for="update-alsBR" class="col-form-label">Alas BR</label>
                                <input type="text" class="form-control" id="update-alsBR" name="alsBR" value="" required>



                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveChangesBtn_buah">Save Changes</button>
                    <button type="button" class="btn btn-secondary" id="closeModalBtn_buah" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="editModalTrans" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-align: center; font-weight: bold;">Update Mutu Trans</h5>
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm_Trans" action="{{ route('updateBA') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="row m-1">
                            <div class="col">


                                <input type="hidden" class="form-control" id="id_trans" name="id_trans">



                                <input type="hidden" class="form-control" id="update-estTrans" name="estTrans" value="">



                                <input type="hidden" class="form-control" id="update-afd_trans" name="afd_trans" value="">


                                <label for="update-blok_trans" class="col-form-label">Blok</label>
                                <input type="text" class="form-control" id="update-blok_trans" name="blok_trans" value="" required>

                                <label for="update-Status_trPanen" class="col-form-label">Status Panen</label>
                                <input type="text" class="form-control" id="update-Status_trPanen" name="Status_trPanen" value="" required>


                                <label for="update-tphbrTrans" class="col-form-label">TPH Baris</label>
                                <input type="text" class="form-control" id="update-tphbrTrans" name="tphbrTrans" value="">

                            </div>
                            <div class="col">




                                <label for="update-petugasTrans" class="col-form-label">Petugas</label>
                                <input type="text" class="form-control" id="update-petugasTrans" name="petugasTrans" value="">

                                <label for="update-bt_trans" class="col-form-label">Brondolan di TPH </label>
                                <input type="text" class="form-control" id="update-bt_trans" name="bt_trans" value="" required>



                                <label for="update-rstTrans" class="col-form-label">Buah di TPH</label>
                                <input type="text" class="form-control" id="update-rstTrans" name="rstTrans" value="" required>



                            </div>


                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveChangesBtn_trans">Save Changes</button>
                    <button type="button" class="btn btn-secondary" id="closeModalBtn_Trans" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="deleteModalancak" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus data??</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModalBuah" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus data??</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn_buah">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModalTrans" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus data??</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn_trans">Yes</button>
                </div>
            </div>
        </div>
    </div>




</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>


@include('layout/footer')

<script>
    function openModal(src, komentar) {
        var modalImg = document.getElementById("img01");
        modalImg.src = src;
        var modalKomentar = document.getElementById("modalKomentar");
        modalKomentar.textContent = komentar;

        var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
        myModal.show();

        var closeButton = document.getElementById('modalCloseButton');
        closeButton.addEventListener('click', function() {
            myModal.hide();
        });
    }

    // bagian untuk map
    function updateTanggal() {
        const selectedDate = document.getElementById("inputDate").value;
        document.getElementById("tglPDF_excel").value = selectedDate;
        document.getElementById("selectedDate").textContent = selectedDate;
    }





    function getmaps() {
        var map = L.map('map');
        map.remove();

        var Tanggal = document.getElementById('inputDate').value;
        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('getMapsdetail') }}",
            method: "get",
            data: {
                Tanggal,
                est,
                afd,
                _token: _token
            },
            success: function(result) {
                var polygonCoords = result.coords;

                var blok_sidak = result.blok_sidak;
                var plot_blok_all = result.plot_blok_all;
                var plot_line = result.plot_line;
                var trans_plot = result.trans_plot;
                var buah_plot = result.buah_plot;
                var ancak_plot = result.ancak_plot;
                var mapContainer = L.DomUtil.get('map');
                if (mapContainer != null) {
                    mapContainer._leaflet_id = null;
                }
                // Initialize the new map instance
                var map = L.map('map').fitBounds(polygonCoords.concat(plot_blok_all), 13);


                var googleStreet = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

                var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });

                var baseMaps = {
                    "Google Street": googleStreet,
                    "Google Satellite": googleSatellite
                };
                L.control.layers(baseMaps).addTo(map);

                // polygonCoords.forEach(function(coordinate, index) {
                //     // Create a custom icon for the marker
                //     var customIcon = new L.Icon({
                //         iconUrl: "https://raw.githubusercontent.com/sheiun/leaflet-color-number-markers/main/dist/img/blue/marker-icon-2x-blue-" + (index + 1) + ".png",
                //         shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                //         iconSize: [25, 41],
                //         iconAnchor: [12, 41],
                //         popupAnchor: [1, -34],
                //         shadowSize: [41, 41]
                //     });

                //     // Create a marker for the coordinate using the custom icon
                //     var marker = L.marker(coordinate, { icon: customIcon }).addTo(map);

                //     // You can customize the marker if needed
                //     // marker.bindPopup('Marker popup content');
                // });
                // var estatePolygon = L.polygon(polygonCoords, {
                //     color: '#003B73'
                // }).addTo(map).bindPopup('<strong>Estate:</strong>' + est);
                // // console.log(plot_blok_all);

                // var bounds = estatePolygon.getBounds();
                //         var center = bounds.getCenter();

                //         // Create a custom HTML icon with centered text
                //         var textIcon = L.divIcon({
                //             className: 'text-icon-estate',
                //             html: '<strong>' + est + ' <br> Estate</strong>',
                //             iconSize: [100, 20],
                //             iconAnchor: [50, 10]
                //         });

                for (var blockName in plot_blok_all) {
                    // Get the coordinates array for the current block
                    var coordinates = plot_blok_all[blockName];

                    // Create a polygon for the current block
                    var polygonOptions = {
                        color: 'rgba(39, 138, 216, 0.5)',
                        fillColor: '#278ad8',
                        fillOpacity: 0.5
                    };
                    var textIcon;

                    if (blok_sidak.includes(blockName)) {
                        polygonOptions.color = 'green';
                        polygonOptions.fillColor = 'green';
                        polygonOptions.fillOpacity = 0.5;

                        textIcon = L.divIcon({
                            className: 'blok_visit',
                            html: blockName,
                            iconSize: [100, 20],
                            iconAnchor: [50, 10]
                        });
                    } else {
                        textIcon = L.divIcon({
                            className: 'blok_all',
                            html: blockName,
                            iconSize: [100, 20],
                            iconAnchor: [50, 10]
                        });
                    }

                    var plotBlokPolygon = L.polygon(coordinates, polygonOptions)
                        .addTo(map)
                        .bindPopup('<strong>Afdeling:</strong>' + blockName);

                    var bounds = plotBlokPolygon.getBounds();
                    var center = bounds.getCenter();

                    // Create a custom HTML icon with text and modified class name


                    // Place the text icon in the center of the polygon
                    L.marker(center, {
                        icon: textIcon
                    }).addTo(map);
                }

                var latlngs = [];

                for (var i = 0; i < plot_line.length; i++) {
                    var coordinates = plot_line[i].split("],[");
                    var latlngGroup = [];

                    for (var j = 0; j < coordinates.length; j++) {
                        var latlng = coordinates[j].replace("[", "").replace("]", "").split(",");
                        latlngGroup.push([parseFloat(latlng[0]), parseFloat(latlng[1])]);
                    }

                    latlngs.push(latlngGroup);
                }

                // console.log(latlngs)
                // var latlngs = [
                //     [45.51, -122.68],
                //     [37.77, -122.43],
                //     [34.04, -118.2]
                // ];

                var polyline = L.polyline(latlngs, {
                    color: 'yellow'
                }).addTo(map);

                var decorator = L.polylineDecorator(polyline, {
                    patterns: [{
                        offset: 0,
                        repeat: 50,
                        symbol: L.Symbol.arrowHead({
                            pixelSize: 8,
                            pathOptions: {
                                fillOpacity: 1
                            }
                        })
                    }]
                }).addTo(map);
                // Place the text icon in the center of the polygon
                L.marker(center, {
                    icon: textIcon
                }).addTo(map);
                // Iterate over the keys of plot_blok





                var yellowIcon = L.icon({
                    iconSize: [38, 95], // size of the icon
                    shadowSize: [50, 64], // size of the shadow
                    iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                    shadowAnchor: [4, 62], // the same for the shadow
                    popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
                });

                // Red marker icon
                var redIcon = L.icon({
                    iconSize: [38, 95], // size of the icon
                    shadowSize: [50, 64], // size of the shadow
                    iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                    shadowAnchor: [4, 62], // the same for the shadow
                    popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
                });

                // Create Layer Groups for each layer type
                var transGroup = L.layerGroup();
                var buahGroup = L.layerGroup();
                var ancakGroup = L.layerGroup();


                var transIconUrl = '{{ asset("img/placeholder.png") }}';
                var transicon = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],

                });

                var transTmuanUrl = '{{ asset("img/placeholder2.png") }}';
                var transtemuan = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });
                var transFollowUrl = '{{ asset("img/placeholder3.png") }}';
                var transFollowup = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],


                });
                // console.log(trans_plot);

                // function trans() {
                //     for (var i = 0; i < trans_plot.length; i++) {
                //         var lat = parseFloat(trans_plot[i].lat);
                //         var lon = parseFloat(trans_plot[i].lon);
                //         var blok = trans_plot[i].blok;
                //         var foto_temuan = trans_plot[i].foto_temuan;
                //         var foto_fu = trans_plot[i].foto_fu;
                //         var komentar = trans_plot[i].komentar;

                //         var markerIcon = foto_fu ? transFollowup : (foto_temuan ? transtemuan : transicon);


                //         var popupContent = `<strong>Mutu Transport Blok: </strong>${blok}<br/>`;

                //         if (foto_temuan) {
                //             popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mt/${foto_temuan}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                //         }

                //         if (foto_fu) {
                //             popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mt/${foto_fu}" alt="Foto FU" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                //         }


                //         popupContent += `<strong>Komentar: </strong>${komentar}`;

                //         var marker = L.marker([lat, lon], {
                //             icon: markerIcon
                //         });

                //         marker.bindPopup(popupContent);

                //         transGroup.addLayer(marker);
                //     }
                // }

                function trans() {
                    for (var key in trans_plot) {
                        if (trans_plot.hasOwnProperty(key)) {
                            var plots = trans_plot[key];
                            // var latLngs = []; // Array to store latitudes and longitudes for drawing lines

                            for (var i = 0; i < plots.length; i++) {
                                var plot = plots[i];
                                var lat = parseFloat(plot.lat);
                                var lon = parseFloat(plot.lon);
                                var blok = plot.blok;
                                var foto_temuan = plot.foto_temuan;
                                var foto_fu = plot.foto_fu;
                                var komentar = plot.komentar;
                                var status_panen = plot.status_panen;
                                var luas_blok = plot.luas_blok;
                                var bt = plot.bt;
                                var rst = plot.Rst;
                                var time = plot.time;

                                var markerIcon = foto_fu ? transFollowup : (foto_temuan ? transtemuan : transicon);

                                var popupContent = `<strong>Mutu Transport Blok: </strong>${blok}<br/>`;
                                if (status_panen) {
                                    popupContent += `<strong>Status Panen: </strong>${status_panen}<br/>`;
                                }

                                popupContent += `<strong>Luas Blok: </strong>${luas_blok}<br/>`;


                                popupContent += `<strong>Brondolan tinggal: </strong>${bt}<br/>`;


                                popupContent += `<strong>Buah Tinggal: </strong>${rst}<br/>`;


                                popupContent += `<strong>Sidak: </strong>${time}<br/>`;


                                if (foto_temuan) {
                                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mt/${foto_temuan}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                                }

                                if (foto_fu) {
                                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mt/${foto_fu}" alt="Foto FU" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                                }

                                if (!isNaN(lat) && !isNaN(lon)) { // Check if lat and lon are valid numbers
                                    var marker = L.marker([lat, lon], {
                                        icon: markerIcon
                                    });

                                    marker.bindPopup(popupContent);

                                    transGroup.addLayer(marker);

                                    // latLngs.push([lat, lon]); // Add latitudes and longitudes to the latLngs array
                                }
                            }


                            // Create a polyline from latLngs array to connect the plots within each block
                            // var polyline = L.polyline(latLngs, {
                            //     color: 'blue'
                            // }).addTo(map);
                        }
                    }
                }



                var myIconUrl = '{{ asset("img/pin.png") }}';
                var myIcon = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],

                });
                var myIconUrl2 = '{{ asset("img/pin2.png") }}';
                var myIcon2 = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],

                });


                // console.log(buah_plot);

                // function buah() {
                //     for (var i = 0; i < buah_plot.length; i++) {
                //         var lat = parseFloat(buah_plot[i].lat);
                //         var lon = parseFloat(buah_plot[i].lon);
                //         var blok = buah_plot[i].blok;
                //         var foto_temuan = buah_plot[i].foto_temuan;
                //         var komentar = buah_plot[i].komentar;

                //         var markerIcon = foto_temuan ? myIcon : myIcon2; // Choose the icon based on the condition

                //         var popupContent = `<strong>Mutu Buah Blok: </strong>${blok}<br/>`;

                //         if (foto_temuan) {
                //             popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mb/${foto_temuan}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                //         }

                //         popupContent += `<strong>Komentar: </strong>${komentar}`;

                //         var marker = L.marker([lat, lon], {
                //             icon: markerIcon
                //         });

                //         marker.bindPopup(popupContent);

                //         buahGroup.addLayer(marker);
                //     }
                // }

                function buah() {
                    for (var key in buah_plot) {
                        if (buah_plot.hasOwnProperty(key)) {
                            var plots = buah_plot[key];
                            for (var i = 0; i < plots.length; i++) {
                                var plot = plots[i];
                                var lat = parseFloat(plot.lat);
                                var lon = parseFloat(plot.lon);
                                var blok = plot.blok;
                                var foto_temuan = plot.foto_temuan;
                                var komentar = plot.komentar;

                                var tph_baris = plot.tph_baris;
                                var status_panen = plot.status_panen;
                                var jumlah_jjg = plot.jumlah_jjg;
                                var bmt = plot.bmt;
                                var bmk = plot.bmk;
                                var overripe = plot.overripe;
                                var empty_bunch = plot.empty_bunch;
                                var abnormal = plot.abnormal;
                                var vcut = plot.vcut;
                                var alas_br = plot.alas_br;
                                var time = plot.time;
                                var markerIcon = foto_temuan ? myIcon : myIcon2; // Choose the icon based on the condition

                                var popupContent = `<strong>Mutu Buah Blok: </strong>${blok}<br/>`;

                                popupContent += `<strong>Tph Baris: </strong>${tph_baris}<br/>`;
                                popupContent += `<strong>Status Panen: </strong>${status_panen}<br/>`;
                                popupContent += `<strong>Buah Mentah Kurang Brondol: </strong>${bmt}<br/>`;
                                popupContent += `<strong>Buah Masak Kurang Brondol: </strong>${bmk}<br/>`;
                                popupContent += `<strong>overripe: </strong>${overripe}<br/>`;
                                popupContent += `<strong>Janjang Kosong: </strong>${empty_bunch}<br/>`;
                                popupContent += `<strong>abnormal: </strong>${abnormal}<br/>`;
                                popupContent += `<strong>Tidak Standar vcut: </strong>${vcut}<br/>`;
                                popupContent += `<strong>Alas Karung: </strong>${alas_br}<br/>`;
                                popupContent += `<strong>Sidak: </strong>${time}<br/>`;


                                if (foto_temuan) {
                                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_mb/${foto_temuan}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                                }

                                popupContent += `<strong>Komentar: </strong>${komentar}`;

                                if (!isNaN(lat) && !isNaN(lon)) { // Check if lat and lon are valid numbers
                                    var marker = L.marker([lat, lon], {
                                        icon: markerIcon
                                    });

                                    marker.bindPopup(popupContent);

                                    buahGroup.addLayer(marker);
                                }
                            }
                        }
                    }
                }







                var ancakTemuan1 = '{{ asset("img/push-pin.png") }}';
                var caktemuan1 = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });

                var ancakTemuan2 = '{{ asset("img/push-pin1.png") }}';
                var caktemuan2 = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });
                var ancak_fu1 = '{{ asset("img/push-pin1.png") }}';
                var cakfu1 = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });
                var ancak_fu2 = '{{ asset("img/push-pin1.png") }}';
                var cakfu2 = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });

                function ancak() {
                    for (var i = 0; i < ancak_plot.length; i++) {
                        var lat = parseFloat(ancak_plot[i].lat);
                        var lon = parseFloat(ancak_plot[i].lon);
                        var blok = ancak_plot[i].blok;
                        var ket = ancak_plot[i].ket;
                        var foto_temuan1 = ancak_plot[i].foto_temuan1;
                        var foto_temuan2 = ancak_plot[i].foto_temuan2;
                        var foto_fu1 = ancak_plot[i].foto_fu1;
                        var foto_fu2 = ancak_plot[i].foto_fu2;
                        var komentar = ancak_plot[i].komentar;

                        var luas_blok = ancak_plot[i].luas_blok;
                        var sph = ancak_plot[i].sph;
                        var sample = ancak_plot[i].sample;
                        var pokok_kuning = ancak_plot[i].pokok_kuning;
                        var piringan_semak = ancak_plot[i].piringan_semak;
                        var underpruning = ancak_plot[i].underpruning;
                        var overpruning = ancak_plot[i].overpruning;
                        var jjg = ancak_plot[i].jjg;
                        var brtp = ancak_plot[i].brtp;
                        var brtk = ancak_plot[i].brtk;
                        var brtgl = ancak_plot[i].brtgl;
                        var bhts = ancak_plot[i].bhts;
                        var bhtm1 = ancak_plot[i].bhtm1;
                        var bhtm2 = ancak_plot[i].bhtm2;
                        var bhtm3 = ancak_plot[i].bhtm3;
                        var ps = ancak_plot[i].ps;
                        var sp = ancak_plot[i].sp;
                        var time = ancak_plot[i].time;
                        var markerIcon = (foto_fu1 || foto_fu2) ? cakfu2 : (foto_temuan1 || foto_temuan2) ? caktemuan2 : caktemuan1;

                        var popupContent = `<strong>Mutu Ancak Blok: </strong>${blok}<br/>`;

                        popupContent += `<strong>luas_blok: </strong>${luas_blok}<br/>`;
                        popupContent += `<strong>sph: </strong>${sph}<br/>`;
                        popupContent += `<strong>sample: </strong>${sample}<br/>`;
                        popupContent += `<strong>Pokok Kuning: </strong>${pokok_kuning}<br/>`;
                        popupContent += `<strong>Piringan Semak: </strong>${piringan_semak}<br/>`;
                        popupContent += `<strong>Underpruning: </strong>${underpruning}<br/>`;
                        popupContent += `<strong>Overpruning: </strong>${overpruning}<br/>`;
                        popupContent += `<strong>Janjang: </strong>${jjg}<br/>`;
                        popupContent += `<strong>Brondolan (P): </strong>${brtp}<br/>`;
                        popupContent += `<strong>Brondolan (K): </strong>${brtk}<br/>`;
                        popupContent += `<strong>Brondolan (TGL): </strong>${brtgl}<br/>`;
                        popupContent += `<strong>Buah Tinggal (S): </strong>${bhts}<br/>`;
                        popupContent += `<strong>Buah Tinggal (M1): </strong>${bhtm1}<br/>`;
                        popupContent += `<strong>Buah Tinggal (M2): </strong>${bhtm2}<br/>`;
                        popupContent += `<strong>Buah Tinggal (M3): </strong>${bhtm3}<br/>`;
                        popupContent += `<strong>Palepah Sengklek: </strong>${ps}<br/>`;
                        popupContent += `<strong>Frond Stacking: </strong>${sp}<br/>`;
                        popupContent += `<strong>Sidak: </strong>${time}<br/>`;
                        if (foto_temuan1) {
                            popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_ma/${foto_temuan1}" alt="Foto Temuan 1" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                        }

                        if (foto_temuan2) {
                            popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_ma/${foto_temuan2}" alt="Foto Temuan 2" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                        }

                        if (foto_fu1) {
                            popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_ma/${foto_fu1}" alt="Foto FU 1" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                        }

                        if (foto_fu2) {
                            popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_ma/${foto_fu2}" alt="Foto FU 2" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                        }

                        if (komentar) {
                            popupContent += `<strong>Komentar: </strong>${komentar}`;
                        }
                        var marker = L.marker([lat, lon], {
                            icon: markerIcon
                        });

                        marker.bindPopup(popupContent);

                        ancakGroup.addLayer(marker);
                    }
                }

                // Call the functions to create the markers and add them to their Layer Groups
                trans();
                buah();
                ancak();

                // Add the Layer Groups to the map
                transGroup.addTo(map);
                buahGroup.addTo(map);
                ancakGroup.addTo(map);
                var legend = L.control({
                    position: 'bottomright'
                });


                var estateIcon = '{{ asset("img/brazil.png") }}';
                var Estatecon = L.icon({
                    iconUrl: estateIcon,
                    iconSize: [30, 80],
                    iconAnchor: [30, 80],
                    popupAnchor: [-3, -76],
                    shadowUrl: estateIcon,
                    shadowSize: [30, 80],
                    shadowAnchor: [30, 80],
                });
                var afdIcon = '{{ asset("img/territory.png") }}';
                var afdCon = L.icon({
                    iconUrl: afdIcon,
                    iconSize: [30, 80],
                    iconAnchor: [30, 80],
                    popupAnchor: [-3, -76],
                    shadowUrl: afdIcon,
                    shadowSize: [30, 80],
                    shadowAnchor: [30, 80],
                });


                legend.onAdd = function(map) {

                    var div = L.DomUtil.create("div", "legend");
                    div.innerHTML += "<h4>Keterangan :</h4>";
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" style="width:12pt;height:13pt" >  Mutu Ancak';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" style="width:12pt;height:13pt" >  MA Temuan';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png" style="width:12pt;height:13pt" >  MA Follow Up';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png" style="width:12pt;height:13pt" >  Mutu Transport';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png" style="width:12pt;height:13pt" >  MT Temuan';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png" style="width:12pt;height:13pt" >  MT Follow Up';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png" style="width:12pt;height:13pt" >  Mutu Buah';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div>  <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png" style="width:12pt;height:13pt" >  MB Temuan';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div><i style="background: #88b87a;width:15px;height:15px;margin-top:5px;border:1px solid green"></i> Blok yang dikunjungi';
                    div.innerHTML += '</div>';
                    div.innerHTML += '<div><i style="margin-top:7px;  width: 0; height: 0; border-left: 6px solid transparent;border-right: 6px solid transparent;border-bottom: 10px solid #4e86fc;"></i> Arah jalan sidak';
                    div.innerHTML += '</div>';

                    return div;
                };

                legend.addTo(map);




                // ...


                // Toggle layer visibility when the eye icon is clicked
                // Toggle layer visibility when the eye icon is clicked
                // eye.addEventListener('click', function() {
                //     var index = parseInt(this.dataset.index);

                //     // Remove all layers
                //     for (var j = 0; j < layers.length; j++) {
                //         map.removeLayer(layers[j]);
                //     }

                //     // Add the clicked layer
                //     map.addLayer(layers[index]);
                // });


            },


            error: function() {

            }
        });
    }

    function enableExcelDownloadButton() {
        const downloadExcelButton = document.getElementById('download-excel-button');
        downloadExcelButton.disabled = false;
    }


    // end bagian untuk map 
    var currentUserName = "{{ session('jabatan') }}";
    //untuk mengirim parameter tanggal ke download pdf BA
    document.addEventListener('DOMContentLoaded', function() {
        const showButton = document.getElementById('show-button');
        const inputDate = document.getElementById('inputDate');
        const selectedDate = document.getElementById('selectedDate');
        const tglPDF = document.getElementById('tglPDF');
        const downloadButton = document.getElementById('download-button');
        const lottieDownload = document.getElementById('lottie-download');

        // Initialize Lottie animation
        const downloadAnimation = lottie.loadAnimation({
            container: lottieDownload,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://assets2.lottiefiles.com/packages/lf20_eUext1.json'
        });

        showButton.addEventListener('click', function() {
            selectedDate.textContent = inputDate.value;
            tglPDF.value = inputDate.value;
            downloadButton.disabled = false;
            enableExcelDownloadButton();
        });
    });
    ///

    function enableShowButton() {
        const showButton = document.getElementById("show-button");
        showButton.disabled = false;
    }

    function updateDate() {
        const inputDate = document.getElementById("inputDate");
        const selectedDate = inputDate.value;
        document.getElementById("date").value = selectedDate;

        // Update the "selectedDate" span
        document.getElementById("selectedDate").textContent = selectedDate;

        // Enable the "Show" button once a date is selected
        enableShowButton();
    }


    document.addEventListener("DOMContentLoaded", function() {
        const inputDate = document.getElementById("inputDate");
        if (inputDate.value) {
            enableShowButton();
        }
    });

    function setInitialDate() {
        const inputDate = document.getElementById("inputDate");
        const selectedDate = inputDate.value;
        document.getElementById("date").value = selectedDate;
    }

    document.addEventListener("DOMContentLoaded", function() {
        setInitialDate();

        // Add an event listener to update the date when the selected date changes
        const inputDate = document.getElementById("inputDate");
        inputDate.addEventListener("change", updateDate);
    });
    //untuk menbuat timbol tutup di modal
    function closeModal() {
        const updateModal = document.getElementById("update-modal");
        updateModal.style.display = "none";
        const updateModal2 = document.getElementById("update-modal-buah");
        updateModal2.style.display = "none";
        const updateModal3 = document.getElementById("update-modal-trans");
        updateModal3.style.display = "none";
    }
    // document.addEventListener("DOMContentLoaded", function() {
    //     setInitialDate();

    //     // Add an event listener to update the date when the selected date changes
    //     const inputDate = document.getElementById("inputDate");
    //     inputDate.addEventListener("change", updateDate);

    //     // Add event listeners to close the modals when the close buttons are clicked
    //     const closeModalButton = document.getElementById("close-modal");
    //     closeModalButton.addEventListener("click", closeModal);

    //     const closeModalButtonBuah = document.getElementById("close-modal-buah");
    //     closeModalButtonBuah.addEventListener("click", closeModal);
    //     const closeModalButtonTrans = document.getElementById("close-modal-trans");
    //     closeModalButtonTrans.addEventListener("click", closeModal);
    // });
    //buat animasi loading ketika tombol show di klik
    const lottieContainer = document.getElementById('lottie-container');
    const lottieAnimation = lottie.loadAnimation({
        container: lottieContainer,
        renderer: "svg",
        loop: true,
        autoplay: false,
        path: "https://assets3.lottiefiles.com/private_files/lf30_fup2uejx.json",
    });
    const lottieContainer1 = document.getElementById('lottie-container1');
    const lottieAnimation1 = lottie.loadAnimation({
        container: lottieContainer1,
        renderer: "svg",
        loop: true,
        autoplay: false,
        path: "https://assets3.lottiefiles.com/packages/lf20_vfcbh2yp.json",
    });




    function fetchAndUpdateData() {
        lottieAnimation.play(); // Start the Lottie animation
        lottieContainer.style.display = 'block'; // Display the Lottie container

        // $('#tab1').empty()
        // $('#tab2').empty()
        // $('#tab3').empty()


        if ($.fn.DataTable.isDataTable('#mutuAncakTable')) {
            $('#mutuAncakTable').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#mutuBuahable')) {
            $('#mutuBuahable').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#mutuTransportable')) {
            $('#mutuTransportable').DataTable().destroy();
        }
        var Tanggal = document.getElementById('inputDate').value;
        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('filterDataDetail') }}",
            method: "GET",
            data: {
                Tanggal,
                est,
                afd,
                _token: _token
            },
            success: function(result) {
                lottieAnimation.stop(); // Stop the Lottie animation
                lottieContainer.style.display = 'none'; // Hide the Lottie container



                // Get the modal
                const modal = document.getElementById("imageModal");

                // Get the image element inside the modal
                const modalImage = document.getElementById("modalImage");

                // Get the close button
                const closeBtn = document.getElementsByClassName("close")[0];

                // Function to show the modal with the clicked image
                function showModal(src) {
                    modalImage.src = src;
                    modal.style.display = "block";
                }

                // When the user clicks on the close button, close the modal
                closeBtn.onclick = function() {
                    modal.style.display = "none";
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }


                var parseResult = JSON.parse(result)

                var mutuBuah = Object.entries(parseResult['mutuBuah'])
                var mutuTransport = Object.entries(parseResult['mutuTransport'])
                var mutuAncak = Object.entries(parseResult['mutuAncak'])



                var mutuAncakData = [];
                for (var i = 0; i < mutuAncak.length; i++) {
                    var rowData = Object.values(mutuAncak[i][1]);
                    mutuAncakData.push(rowData);
                }


                console.log(mutuAncak);
                console.log(mutuAncakData);

                document.getElementById('closeModalBtn').addEventListener('click', function() {
                    $('#editModal').modal('hide');
                });


                function editRow(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the id from the first column of the selected row
                    var rowData = mutuAncakTable.row(id).data();
                    var rowId = rowData[0];

                    // Populate the form with the data of the selected row
                    $('#editId').val(rowData[0]).prop('disabled', true); // Use rowId instead of id
                    $('#estate').val(rowData[1]).prop('disabled', true);
                    $('#afdeling').val(rowData[2]).prop('disabled', true);
                    $('#update-blokCak').val(rowData[3]);
                    $('#update-StatusPnen').val(rowData[15]);
                    $('#update-sph').val(rowData[10]);
                    $('#update-br1').val(rowData[12]);
                    $('#update-br2').val(rowData[13]);
                    $('#update-sampCak').val(rowData[18]);
                    $('#update-pkKuning').val(rowData[19]);

                    $('#update-prSmk').val(rowData[20]);
                    $('#update-undrPR').val(rowData[21]);
                    $('#update-overPR').val(rowData[22]);
                    $('#update-jjgCak').val(rowData[23]);

                    $('#update-brtp').val(rowData[24]);
                    $('#update-brtk').val(rowData[25]);
                    $('#update-brtgl').val(rowData[26]);
                    $('#update-bhts').val(rowData[27]);

                    $('#update-bhtm1').val(rowData[28]);
                    $('#update-bhtm2').val(rowData[29]);
                    $('#update-bhtm3').val(rowData[30]);
                    $('#update-ps').val(rowData[31]);
                    $('#update-sp').val(rowData[32]);
                    $('#update-pk_panenCAk').val(rowData[33]);

                    // Add similar lines for other fields

                    // Show the modal
                    $('#editModal').modal('show');
                }

                $(document).ready(function() {
                    // Close modal when the close button is clicked
                    $('#closeModalBtn_Ancak').click(function() {
                        $('#editModal').modal('hide');
                    });

                    // Submit the form when the Save Changes button is clicked
                    $('#saveChangesBtn').off('click').on('click', function() {
                        $('#editForm').submit();
                    });

                    $('#editForm').submit(function(e) {
                        e.preventDefault(); // Prevent the default form submission

                        // Get the form data
                        var formData = new FormData(this);
                        formData.append('id', $('#editId').val()); // Append the id field to the form data


                        var sampCak = $('#update-sampCak').val();
                        var pkKuning = $('#update-pkKuning').val();
                        var prSmk = $('#update-prSmk').val();
                        var undrPR = $('#update-undrPR').val();
                        var overPR = $('#update-overPR').val();
                        var jjgCak = $('#update-jjgCak').val();
                        var brtp = $('#update-brtp').val();
                        var brtk = $('#update-brtk').val();
                        var brtgl = $('#update-brtgl').val();
                        var bhts = $('#update-bhts').val();
                        var bhtm1 = $('#update-bhtm1').val();
                        var bhtm2 = $('#update-bhtm2').val();
                        var bhtm3 = $('#update-bhtm3').val();
                        var ps = $('#update-ps').val();
                        var sp = $('#update-sp').val();
                        var pk_panenCAk = $('#update-pk_panenCAk').val();

                        if (!isNumber(sampCak) ||
                            !isNumber(pkKuning) ||
                            !isNumber(prSmk) ||
                            !isNumber(undrPR) ||
                            !isNumber(overPR) ||
                            !isNumber(jjgCak) ||
                            !isNumber(brtp) ||
                            !isNumber(brtk) ||
                            !isNumber(brtgl) ||
                            !isNumber(bhts) ||
                            !isNumber(bhtm1) ||
                            !isNumber(bhtm2) ||
                            !isNumber(bhtm3) ||
                            !isNumber(ps) ||
                            !isNumber(sp) ||
                            !isNumber(pk_panenCAk)
                        ) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Masukan Error',
                                text: 'Hanya bisa di masukan angka Saja!'
                            });
                            return;
                        }

                        // Send the AJAX request
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("updateBA") }}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response);
                                // Close the modal
                                $('#editModal').modal('hide');

                                // Show a success message or perform any other actions
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Data berhasil diperbarui!'
                                }).then(function() {
                                    // Refresh the data on the page
                                    // fetchAndUpdateData();
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal memperbarui data!'
                                });
                            }
                        });
                    });
                });





                var selectedRowIndex; // Variable to store the selected row index

                function deleteRow(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the ID from the first column of the selected row
                    var rowData = mutuAncakTable.row(id).data();
                    var rowId = rowData[0];

                    // Show the delete modal
                    $('#deleteModalancak').modal('show');

                    $(document).ready(function() {
                        // Handle delete confirmation
                        $('#confirmDeleteBtn').click(function() {
                            // Get the selected row index and ID
                            var rowIndex = selectedRowIndex;
                            var id = rowId;

                            // Create a form data object
                            var formData = new FormData();
                            formData.append('delete_id', id);

                            // Get the CSRF token from the meta tag
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');

                            // Set the CSRF token in the request headers
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            // Send the AJAX request to the controller
                            $.ajax({
                                url: '{{ route("deleteBA") }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Handle the response from the controller if needed
                                    // console.log(response);

                                    // Implement the logic to delete the row with the provided ID
                                    // You can use the rowIndex to delete the corresponding row from the DataTable
                                    // Example code:
                                    // Assuming you have a DataTable variable called 'mutuAncakTable'
                                    mutuAncakTable.row(rowIndex).remove().draw();

                                    // Close the delete modal
                                    $('#deleteModalancak').modal('hide');

                                    // location.reload()
                                    fetchAndUpdateData()
                                },
                                error: function(xhr, status, error) {
                                    // Handle the error if needed
                                    console.error(error);

                                    // Close the delete modal
                                    $('#deleteModalancak').modal('hide');
                                    // fetchAndUpdateData()
                                }
                            });
                        });
                    });
                }

                // Example function to save changes
                document.getElementById('saveChangesBtn').addEventListener('click', function() {

                    $('#editModal').modal('hide');
                });

                // mutu ancak 
                var columnDefs = [{
                        targets: 0,
                        title: 'id'

                    },
                    {
                        targets: 1,
                        title: 'estate'
                    },
                    {
                        targets: 2,
                        title: 'afdeling'
                    },
                    {
                        targets: 3,
                        title: 'Blok'
                    },
                    {
                        targets: 4,
                        title: 'Petugas'
                    },
                    {
                        targets: 5,
                        title: 'datetime'
                    },
                    {
                        targets: 11,
                        title: 'luas blok',
                        render: function(data, type, row, meta) {
                            return row[11]; // Access the value from index 3 of the data array
                        }
                    },

                    {
                        targets: 10,
                        title: 'Sph',
                        render: function(data, type, row, meta) {
                            return row[10]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 12,
                        title: 'Baris 1',
                        render: function(data, type, row, meta) {
                            return row[12]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 13,
                        title: 'Baris 2',
                        render: function(data, type, row, meta) {
                            return row[13]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 14,
                        title: 'Jalur masuk',
                        render: function(data, type, row, meta) {
                            return row[14]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 15,
                        title: 'Status Panen',
                        render: function(data, type, row, meta) {
                            return row[15]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 16,
                        title: 'Kemandoran',
                        render: function(data, type, row, meta) {
                            return row[16]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 17,
                        title: 'Ancak Pemanen',
                        render: function(data, type, row, meta) {
                            return row[17]; // Access the value from index 3 of the data array
                        }
                    },


                    {

                        title: 'Pokok Panen',
                        render: function(data, type, row, meta) {
                            return row[33]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        title: 'Pokok Sample',
                        render: function(data, type, row, meta) {
                            return row[18]; // Access the value from index 3 of the data array
                        }
                    },


                    {
                        targets: 23,
                        title: 'Janjang Panen',
                        render: function(data, type, row, meta) {
                            return row[23]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 24,
                        title: 'Brondolan (P)',
                        render: function(data, type, row, meta) {
                            return row[24]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 25,
                        title: 'Brondolan (K)',
                        render: function(data, type, row, meta) {
                            return row[25]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 26,
                        title: 'Brondolan (GL)',
                        render: function(data, type, row, meta) {
                            return row[26]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 27,
                        title: 'Buah Tinggal (S)',
                        render: function(data, type, row, meta) {
                            return row[27]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 28,
                        title: 'Buah Tinggal (M1)',
                        render: function(data, type, row, meta) {
                            return row[28]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 29,
                        title: 'Buah Tinggal (M2)',
                        render: function(data, type, row, meta) {
                            return row[29]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 30,
                        title: 'Buah Tinggal (M3)',
                        render: function(data, type, row, meta) {
                            return row[30]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 31,
                        title: 'Pelepah Sengkleh',
                        render: function(data, type, row, meta) {
                            return row[31]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 32,
                        title: 'Frond Stacking',
                        render: function(data, type, row, meta) {
                            return row[32]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 33,
                        title: 'Piringan Semak',
                        render: function(data, type, row, meta) {
                            return row[20]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 20,
                        title: 'Pokok Kuning',
                        render: function(data, type, row, meta) {
                            return row[19]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 21,
                        title: 'Underpruning',
                        render: function(data, type, row, meta) {
                            return row[21]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: 22,
                        title: 'Overpruning',
                        render: function(data, type, row, meta) {
                            return row[22]; // Access the value from index 3 of the data array
                        }
                    },
                    {
                        targets: -1, // -1 targets the last column
                        title: 'Actions',
                        visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
                        render: function(data, type, row, meta) {
                            var buttons =
                                '<button class="edit-btn">Edit</button>' +
                                '<button class="delete-btn">Delete</button>';
                            return buttons;
                        }
                    }
                ];
                // Initialize DataTables for mutuAncak

                var mutuAncakTable = $('#mutuAncakTable').DataTable({
                    data: mutuAncakData, // Use modifiedMutuAncakData instead of mutuAncakData
                    columns: columnDefs,
                    scrollX: true
                });


                // Attach event handlers to dynamically created buttons
                $('#mutuAncakTable').on('click', '.edit-btn', function() {
                    var rowData = mutuAncakTable.row($(this).closest('tr')).data();
                    var rowIndex = mutuAncakTable.row($(this).closest('tr')).index();
                    editRow(rowIndex);
                });

                $('#mutuAncakTable').on('click', '.delete-btn', function() {
                    var rowIndex = mutuAncakTable.row($(this).closest('tr')).index();
                    deleteRow(rowIndex);
                });

                // end table ajax mutu ancak 

                // table mutu buah 
                var mutuBuahData = [];
                for (var i = 0; i < mutuBuah.length; i++) {
                    var rowData = Object.values(mutuBuah[i][1]);
                    mutuBuahData.push(rowData);
                }

                // console.log(mutuBuahData);

                function editRowBuah(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the id from the first column of the selected row
                    var rowData = mutuBuahable.row(id).data();
                    var rowId = rowData[0];

                    // Populate the form with the data of the selected row
                    $('#editId_buah').val(rowData[0]);

                    $('#update-estBH').val(rowData[1]);
                    $('#update-afdBH').val(rowData[2]);
                    $('#update-tphBH').val(rowData[8]);
                    $('#update-blok_bh').val(rowData[3]);
                    $('#update-StatusBhpnen').val(rowData[9]);
                    $('#update-petugasBH').val(rowData[4]);
                    $('#update-pemanen_bh').val(rowData[10]);
                    $('#update-bmt').val(rowData[12]);
                    $('#update-bmk').val(rowData[13]);
                    $('#update-emptyBH').val(rowData[15]);
                    $('#update-jjgBH').val(rowData[11]);
                    $('#update-overBH').val(rowData[14]);
                    $('#update-abrBH').val(rowData[16]);
                    $('#update-vcutBH').val(rowData[17]);
                    $('#update-alsBR').val(rowData[18]);


                    $('#editModalBuah').modal('show');
                }

                $(document).ready(function() {
                    // Close modal when the close button is clicked
                    $('#closeModalBtn_buah').click(function() {
                        $('#editModalBuah').modal('hide');
                    });

                    // Submit the form when the Save Changes button is clicked
                    $('#saveChangesBtn_buah').off('click').on('click', function() {
                        $('#editForm_buah').submit();
                    });

                    $('#editForm_buah').submit(function(e) {
                        e.preventDefault(); // Prevent the default form submission

                        // Get the form data
                        var formData = new FormData(this);
                        formData.append('id', $('#editId_buah').val()); // Append the id field to the form data

                        var bmt = $('#update-bmt').val();
                        var bmk = $('#update-bmk').val();
                        var emptyBH = $('#update-emptyBH').val();
                        var jjgBH = $('#update-jjgBH').val();
                        var overBH = $('#update-overBH').val();
                        var abrBH = $('#update-abrBH').val();
                        var vcutBH = $('#update-vcutBH').val();
                        var alsBR = $('#update-alsBR').val();

                        if (!isNumber(bmt) ||
                            !isNumber(bmk) ||
                            !isNumber(emptyBH) ||
                            !isNumber(jjgBH) ||
                            !isNumber(overBH) ||
                            !isNumber(abrBH) ||
                            !isNumber(vcutBH) ||
                            !isNumber(alsBR)
                        ) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Masukan Error',
                                text: 'Hanya bisa di masukan angka Saja!'
                            });
                            return;
                        }
                        // Send the AJAX request
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("updateBA") }}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response);
                                // Close the modal
                                $('#editModalBuah').modal('hide');

                                // Show a success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Data berhasil diperbarui!'
                                }).then(function() {
                                    // Refresh the data on the page
                                    // fetchAndUpdateData();
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                // Show an error message or perform any other actions
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal memperbarui data!'
                                });
                            }
                        });
                    });
                });





                var selectedRowIndex; // Variable to store the selected row index

                function deleteRowBuah(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the ID from the first column of the selected row
                    var rowData = mutuBuahable.row(id).data();
                    var rowId = rowData[0];

                    // Show the delete modal
                    $('#deleteModalBuah').modal('show');

                    $(document).ready(function() {
                        // Handle delete confirmation
                        $('#confirmDeleteBtn_buah').click(function() {
                            // Get the selected row index and ID
                            var rowIndex = selectedRowIndex;
                            var id = rowId;

                            // Create a form data object
                            var formData = new FormData();
                            formData.append('delete_idBuah', id);

                            // Get the CSRF token from the meta tag
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');

                            // Set the CSRF token in the request headers
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            // Send the AJAX request to the controller
                            $.ajax({
                                url: '{{ route("deleteBA") }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Handle the response from the controller if needed
                                    // console.log(response);

                                    // Implement the logic to delete the row with the provided ID
                                    // You can use the rowIndex to delete the corresponding row from the DataTable
                                    // Example code:
                                    // Assuming you have a DataTable variable called 'mutuAncakTable'
                                    mutuAncakTable.row(rowIndex).remove().draw();

                                    // Close the delete modal
                                    $('#deleteModalBuah').modal('hide');

                                    location.reload()
                                },
                                error: function(xhr, status, error) {
                                    // Handle the error if needed
                                    console.error(error);

                                    // Close the delete modal
                                    $('#deleteModalBuah').modal('hide');
                                }
                            });
                        });
                    });
                }

                // Example function to save changes
                document.getElementById('saveChangesBtn').addEventListener('click', function() {

                    $('#editModal').modal('hide');
                });


                var columnBuah = [{
                        targets: 0,
                        title: 'id',
                        render: function(data, type, row, meta) {
                            return row[0];
                        }
                    },

                    {
                        targets: 1,
                        title: 'estate'
                    },
                    {
                        targets: 2,
                        title: 'afdeling'
                    },
                    {
                        targets: 8,
                        title: 'TPH Baris',
                        render: function(data, type, row, meta) {
                            return row[8];
                        }
                    },
                    {
                        targets: 3,
                        title: 'Blok',
                        render: function(data, type, row, meta) {
                            return row[3];
                        }
                    },
                    {
                        targets: 9,
                        title: 'Status Panen',
                        render: function(data, type, row, meta) {
                            return row[9];
                        }
                    },
                    {
                        targets: 4,
                        title: 'Petugas',
                        render: function(data, type, row, meta) {
                            return row[4];
                        }
                    },
                    {
                        targets: 10,
                        title: 'Ancak Pemanen',
                        render: function(data, type, row, meta) {
                            return row[10];
                        }
                    },
                    {
                        targets: 13,
                        title: 'Buah Mentah Tanpa  Brondol',
                        render: function(data, type, row, meta) {
                            return row[13];
                        }
                    },
                    {
                        targets: 12,
                        title: 'Buah Mentah Kurang Brondol',
                        render: function(data, type, row, meta) {
                            return row[12];
                        }
                    },
                    {
                        targets: 15,
                        title: 'Empty Bunch',
                        render: function(data, type, row, meta) {
                            return row[15];
                        }
                    },
                    {
                        targets: 11,
                        title: 'Jumlah Janjang',
                        render: function(data, type, row, meta) {
                            return row[11];
                        }
                    },
                    {
                        targets: 14,
                        title: 'Overripe',
                        render: function(data, type, row, meta) {
                            return row[14];
                        }
                    },
                    {
                        targets: 16,
                        title: 'Abnormal',
                        render: function(data, type, row, meta) {
                            return row[16];
                        }
                    },
                    {
                        targets: 17,
                        title: 'Tidak Standar V-cut',
                        render: function(data, type, row, meta) {
                            return row[17];
                        }
                    },
                    {
                        targets: 18,
                        title: 'Alas Brondolan',
                        render: function(data, type, row, meta) {
                            return row[18];
                        }
                    },

                    {
                        targets: -1, // -1 targets the last column
                        title: 'Actions',
                        visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
                        render: function(data, type, row, meta) {
                            var buttons =
                                '<button class="edit-btn">Edit</button>' +
                                '<button class="delete-btn">Delete</button>';
                            return buttons;
                        }
                    }
                ];
                // Initialize DataTables for mutuAncak
                // console.log(mutuBuahData);

                var mutuBuahable = $('#mutuBuahable').DataTable({
                    data: mutuBuahData, // Use modifiedMutuAncakData instead of mutuAncakData
                    columns: columnBuah,
                    scrollX: true
                });


                // Attach event handlers to dynamically created buttons
                $('#mutuBuahable').on('click', '.edit-btn', function() {
                    var rowData = mutuBuahable.row($(this).closest('tr')).data();
                    var rowIndex = mutuBuahable.row($(this).closest('tr')).index();
                    editRowBuah(rowIndex);
                });

                $('#mutuBuahable').on('click', '.delete-btn', function() {
                    var rowIndex = mutuBuahable.row($(this).closest('tr')).index();
                    deleteRowBuah(rowIndex);
                });


                // end table mutu buah 

                function removeLatLon(array) {
                    array.forEach((item) => {
                        if (Array.isArray(item)) {
                            removeLatLon(item); // Recursively remove "lat" and "lon" from nested arrays
                        } else {
                            delete item.lat;
                            delete item.lon;
                            delete item.bulan;
                            delete item.tahun;
                            delete item.app_version;
                            delete item.foto_fu;
                            delete item.foto_temuan;
                            delete item.foto_komentar;
                            delete item.komentar;
                        }
                    });
                }


                // Call the function to remove "lat" and "lon" properties
                removeLatLon(mutuTransport);

                // console.log(mutuTransport);
                var mutuTransData = [];
                for (var i = 0; i < mutuTransport.length; i++) {
                    var rowData = Object.values(mutuTransport[i][1]);
                    mutuTransData.push(rowData);
                }

                // console.log(mutuTransData);

                function editRowTrans(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the id from the first column of the selected row
                    var rowData = mutuTransTable.row(id).data();
                    var rowId = rowData[0];

                    // Populate the form with the data of the selected row
                    $('#id_trans').val(rowData[0]);

                    $('#update-estTrans').val(rowData[1]);
                    $('#update-afd_trans').val(rowData[2]);
                    $('#update-tphbrTrans').val(rowData[6]);
                    $('#update-blok_trans').val(rowData[3]);
                    $('#update-Status_trPanen').val(rowData[7]);
                    $('#update-petugasTrans').val(rowData[4]);
                    $('#update-bt_trans').val(rowData[9]);
                    $('#update-rstTrans').val(rowData[10]);

                    $('#editModalTrans').modal('show');
                }

                $(document).ready(function() {
                    // Close modal when the close button is clicked
                    $('#closeModalBtn_Trans').click(function() {
                        $('#editModalTrans').modal('hide');
                    });

                    // Submit the form when the Save Changes button is clicked
                    $('#saveChangesBtn_trans').off('click').on('click', function() {
                        $('#editForm_Trans').submit();
                    });

                    $('#editForm_Trans').submit(function(e) {
                        e.preventDefault(); // Prevent the default form submission

                        // Get the form data
                        var formData = new FormData(this);
                        formData.append('id', $('#id_trans').val()); // Append the id field to the form data

                        // Validate the bt_trans and rstTrans fields
                        var btTransValue = $('#update-bt_trans').val();
                        var rstTransValue = $('#update-rstTrans').val();
                        if (!isNumber(btTransValue) || !isNumber(rstTransValue)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Masukan Error',
                                text: 'Hanya bisa di masukan angka Saja!'
                            });
                            return;
                        }

                        // Send the AJAX request
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("updateBA") }}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response);
                                // Close the modal
                                $('#editModalTrans').modal('hide');

                                // Show a success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Data berhasil diperbarui!'
                                }).then(function() {
                                    // Refresh the data on the page
                                    // fetchAndUpdateData();
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                // Show an error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal memperbarui data!'
                                });
                            }
                        });
                    });
                });






                var selectedRowIndex; // Variable to store the selected row index

                function deleteRowTrans(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the ID from the first column of the selected row
                    var rowData = mutuTransTable.row(id).data();
                    var rowId = rowData[0];

                    // Show the delete modal
                    $('#deleteModalTrans').modal('show');

                    $(document).ready(function() {
                        // Handle delete confirmation
                        $('#confirmDeleteBtn_trans').click(function() {
                            // Get the selected row index and ID
                            var rowIndex = selectedRowIndex;
                            var id = rowId;

                            // Create a form data object
                            var formData = new FormData();
                            formData.append('id_trans', id);

                            // Get the CSRF token from the meta tag
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');

                            // Set the CSRF token in the request headers
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            // Send the AJAX request to the controller
                            $.ajax({
                                url: '{{ route("deleteBA") }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Handle the response from the controller if needed
                                    console.log(response);

                                    // Implement the logic to delete the row with the provided ID
                                    // You can use the rowIndex to delete the corresponding row from the DataTable
                                    // Example code:
                                    // Assuming you have a DataTable variable called 'mutuAncakTable'
                                    mutuTransTable.row(rowIndex).remove().draw();

                                    // Close the delete modal
                                    $('#deleteModalTrans').modal('hide');

                                    location.reload()
                                },
                                error: function(xhr, status, error) {
                                    // Handle the error if needed
                                    console.error(error);

                                    // Close the delete modal
                                    $('#deleteModalTrans').modal('hide');
                                }
                            });
                        });
                    });
                }

                function isNumber(value) {
                    return !isNaN(parseFloat(value)) && isFinite(value);
                }
                // Example function to save changes
                document.getElementById('saveChangesBtn').addEventListener('click', function() {

                    $('#editModal').modal('hide');
                });


                var columnTrans = [{
                        targets: 0,
                        title: 'id',

                    },

                    {
                        targets: 1,
                        title: 'estate'
                    },
                    {
                        targets: 2,
                        title: 'afdeling'
                    },
                    {
                        targets: 3,
                        title: 'Blok'
                    },
                    {
                        targets: 4,
                        title: 'Petugas'
                    },
                    {
                        targets: 5,
                        title: 'Datetime'
                    },
                    {
                        targets: 6,
                        title: 'TPH Baris'
                    },
                    {
                        targets: 7,
                        title: 'Status Panen'
                    },
                    {
                        targets: 8,
                        title: 'Luas Blok'
                    },
                    {
                        targets: 9,
                        title: 'Brondol di TPH'
                    },
                    {
                        targets: 10,
                        title: 'Buah di TPH'
                    },

                    {
                        targets: -1, // -1 targets the last column
                        title: 'Actions',
                        visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
                        render: function(data, type, row, meta) {
                            var buttons =
                                '<button class="edit-btn">Edit</button>' +
                                '<button class="delete-btn">Delete</button>';
                            return buttons;
                        }
                    }
                ];
                // Initialize DataTables for mutuAncak
                // console.log(mutuTransData);

                var mutuTransTable = $('#mutuTransportable').DataTable({
                    data: mutuTransData,
                    columns: columnTrans,
                    scrollX: true
                });


                // Attach event handlers to dynamically created buttons
                $('#mutuTransportable').on('click', '.edit-btn', function() {
                    var rowData = mutuTransTable.row($(this).closest('tr')).data();
                    var rowIndex = mutuTransTable.row($(this).closest('tr')).index();
                    editRowTrans(rowIndex);
                });

                $('#mutuTransportable').on('click', '.delete-btn', function() {
                    var rowIndex = mutuTransTable.row($(this).closest('tr')).index();
                    deleteRowTrans(rowIndex);
                });



            },
            error: function() {
                lottieAnimation.stop(); // Stop the Lottie animation
                lottieContainer.style.display = 'none'; // Hide the Lottie container
            }
        });




    }

    function Show() {
        fetchAndUpdateData();
        getmaps();
        getDataDay();
    }

    document.querySelector('button[type="button"]').addEventListener('click', Show);



    function goBack() {
        // Save the selected tab to local storage
        localStorage.setItem('selectedTab', 'nav-data-tab');

        // Redirect to the target page
        window.location.href = "https://qc-apps.srs-ssms.com/dashboard_inspeksi";
    }
    var regional = '{{$reg}}';

    function getDataDay() {
        $('#dataInspeksi').empty()
        var Tanggal = document.getElementById('inputDate').value;
        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        var reg = regional; // Assign the "regional" variable to "reg"
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('getDataDay') }}",
            method: "GET",
            data: {
                Tanggal: Tanggal,
                est: est,
                afd: afd,
                reg: reg, // Pass the "reg" value in the data object
                _token: _token
            },
            success: function(result) {
                var parseResult = JSON.parse(result)
                var mutuAncak = Object.entries(parseResult['mutuAncak'])
                var mutuBuah = Object.entries(parseResult['mutuBuah'])
                var mutuTransport = Object.entries(parseResult['mutuTransport'])
                var all_data = Object.entries(parseResult['data_chuack'])


                var tbody1 = document.getElementById('dataInspeksi');

                // console.log(all_data);
                var arrTbody1 = all_data

                function brd_tph(skor) {
                    if (skor <= 3) {
                        return 10;
                    } else if (skor >= 3 && skor <= 5) {
                        return 8;
                    } else if (skor >= 5 && skor <= 7) {
                        return 6;
                    } else if (skor >= 7 && skor <= 9) {
                        return 4;
                    } else if (skor >= 9 && skor <= 11) {
                        return 2;
                    } else if (skor >= 11) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function skor_brd_ma(skor) {
                    if (skor <= 1.0) {
                        return 20;
                    } else if (skor >= 1 && skor <= 1.5) {
                        return 16;
                    } else if (skor >= 1.5 && skor <= 2.0) {
                        return 12;
                    } else if (skor >= 2.0 && skor <= 2.5) {
                        return 8;
                    } else if (skor >= 2.5 && skor <= 3.0) {
                        return 4;
                    } else if (skor >= 3.0 && skor <= 3.5) {
                        return 0;
                    } else if (skor >= 3.5 && skor <= 4.0) {
                        return -4;
                    } else if (skor >= 4.0 && skor <= 4.5) {
                        return -8;
                    } else if (skor >= 4.5 && skor <= 5.0) {
                        return -12;
                    } else if (skor >= 5.0) {
                        return -16;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function skor_buah_Ma(skor) {
                    if (skor <= 0.0) {
                        return 20;
                    } else if (skor >= 0.0 && skor <= 1.0) {
                        return 18;
                    } else if (skor >= 1 && skor <= 1.5) {
                        return 16;
                    } else if (skor >= 1.5 && skor <= 2.0) {
                        return 12;
                    } else if (skor >= 2.0 && skor <= 2.5) {
                        return 8;
                    } else if (skor >= 2.5 && skor <= 3.0) {
                        return 4;
                    } else if (skor >= 3.0 && skor <= 3.5) {
                        return 0;
                    } else if (skor >= 3.5 && skor <= 4.0) {
                        return -4;
                    } else if (skor >= 4.0 && skor <= 4.5) {
                        return -8;
                    } else if (skor >= 4.5 && skor <= 5.0) {
                        return -12;
                    } else if (skor >= 5.0) {
                        return -16;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function skor_palepah_ma(skor) {
                    if (skor <= 0.5) {
                        return 5;
                    } else if (skor >= 0.5 && skor <= 1.0) {
                        return 4;
                    } else if (skor >= 1.0 && skor <= 1.5) {
                        return 3;
                    } else if (skor >= 1.5 && skor <= 2.0) {
                        return 2;
                    } else if (skor >= 2.0 && skor <= 2.5) {
                        return 1;
                    } else if (skor >= 2.5) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function buah_tph(skor) {
                    if (skor <= 0.0) {
                        return 10;
                    } else if (skor >= 0.0 && skor <= 0.5) {
                        return 8;
                    } else if (skor >= 0.5 && skor <= 1) {
                        return 6;
                    } else if (skor >= 1.0 && skor <= 1.5) {
                        return 4;
                    } else if (skor >= 1.5 && skor <= 2.0) {
                        return 2;
                    } else if (skor >= 2.0 && skor <= 2.5) {
                        return 0;
                    } else if (skor >= 2.5 && skor <= 3.0) {
                        return -2;
                    } else if (skor >= 3.0 && skor <= 3.5) {
                        return -4;
                    } else if (skor >= 3.5 && skor <= 4.0) {
                        return -6;
                    } else if (skor >= 4.0) {
                        return -8;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mb_mentah(skor) {
                    if (skor <= 1.0) {
                        return 10;
                    } else if (skor >= 1.0 && skor <= 2.0) {
                        return 8;
                    } else if (skor >= 2.0 && skor <= 3.0) {
                        return 6;
                    } else if (skor >= 3.0 && skor <= 4.0) {
                        return 4;
                    } else if (skor >= 4.0 && skor <= 5.0) {
                        return 2;
                    } else if (skor >= 5.0) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mb_masak(skor) {
                    if (skor <= 75.0) {
                        return 0;
                    } else if (skor >= 75.0 && skor <= 80.0) {
                        return 1;
                    } else if (skor >= 80.0 && skor <= 85.0) {
                        return 2;
                    } else if (skor >= 85.0 && skor <= 90.0) {
                        return 3;
                    } else if (skor >= 90.0 && skor <= 95.0) {
                        return 4;
                    } else if (skor >= 95.0) {
                        return 5;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mb_over(skor) {
                    if (skor <= 2.0) {
                        return 5;
                    } else if (skor >= 2.0 && skor <= 4.0) {
                        return 4;
                    } else if (skor >= 4.0 && skor <= 6.0) {
                        return 3;
                    } else if (skor >= 6.0 && skor <= 8.0) {
                        return 2;
                    } else if (skor >= 8.0 && skor <= 10.0) {
                        return 1;
                    } else if (skor >= 10.0) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mb_jangkos(skor) {
                    if (skor <= 1.0) {
                        return 5;
                    } else if (skor >= 1.0 && skor <= 2.0) {
                        return 4;
                    } else if (skor >= 2.0 && skor <= 3.0) {
                        return 3;
                    } else if (skor >= 3.0 && skor <= 4.0) {
                        return 2;
                    } else if (skor >= 4.0 && skor <= 5.0) {
                        return 1;
                    } else if (skor >= 5.0) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mb_vcut(skor) {
                    if (skor <= 2.0) {
                        return 5;
                    } else if (skor >= 2.0 && skor <= 4.0) {
                        return 4;
                    } else if (skor >= 4.0 && skor <= 6.0) {
                        return 3;
                    } else if (skor >= 6.0 && skor <= 8.0) {
                        return 2;
                    } else if (skor >= 8.0 && skor <= 10.0) {
                        return 1;
                    } else if (skor >= 10.0) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function mbalas_br(skor) {
                    if (skor >= 100) {
                        return 5;
                    } else if (skor >= 90 && skor <= 100) {
                        return 4;
                    } else if (skor >= 80 && skor <= 90) {
                        return 3;
                    } else if (skor >= 70 && skor <= 80) {
                        return 2;
                    } else if (skor >= 60 && skor <= 70) {
                        return 1;
                    } else if (skor <= 60) {
                        return 0;
                    } else if (skor == undefined) {
                        return 0;
                    }
                }

                function kategori(skor) {
                    if (skor >= 95) {
                        return "EXCELLENT";
                    } else if (skor >= 85) {
                        return "GOOD";
                    } else if (skor >= 75) {
                        return "SATISFACTORY";
                    } else if (skor >= 65) {
                        return "FAIR";
                    } else {
                        return "POOR";
                    }
                }



                if (reg == 2) {
                    let inc = 1;
                    let tod = 0;
                    let ted = 0;
                    let tok = 0;
                    arrTbody1.forEach(element => {
                        tr = document.createElement('tr');
                        let item1 = inc++;
                        let item2 = element[0];
                        let item3 = element[1]['status_panen'] ?? 0;
                        let item4 = element[1]['pokok_sample'] ?? 0;
                        let item5 = (element[1]['luas_blok'] ?? [0])[0]; // Extract the first element from the array

                        let item6 = element[1]['jml_jjg_panen'] ?? 0;
                        let item7 = element[1]['akp_real'] ?? 0;
                        let item8 = element[1]['p_ma'] ?? 0;
                        let item9 = element[1]['k_ma'] ?? 0;
                        let item10 = element[1]['gl_ma'] ?? 0;
                        let item11 = element[1]['total_brd_ma'] ?? 0;
                        let item12 = element[1]['btr_jjg_ma'] ?? 0;
                        let item13 = element[1]['skor_brd'] ?? 0;

                        let item14 = element[1]['bhts_ma'] ?? 0;
                        let item15 = element[1]['bhtm1_ma'] ?? 0;
                        let item16 = element[1]['bhtm2_ma'] ?? 0;
                        let item17 = element[1]['bhtm3_ma'] ?? 0;
                        let item18 = element[1]['tot_jjg_ma'] ?? 0;
                        let item19 = element[1]['jjg_tgl_ma'] ?? 0;
                        let item20 = element[1]['skor_buah'] ?? 0;
                        let item21 = element[1]['ps_ma'] ?? 0;
                        let item22 = element[1]['PerPSMA'] ?? 0;
                        let item23 = element[1]['skor_pale'] ?? 0;
                        let item24 = (element[1]['skor_pale'] ?? 0) + (element[1]['skor_brd'] ?? 0) + (element[1]['skor_buah'] ?? 0);


                        let item25 = element[1]['tph_sample'] ?? 0;
                        let item26 = element[1]['bt_total'] ?? 0;
                        let item27 = element[1]['skor'] ?? 0;
                        let item28 = brd_tph(element[1]['skor']);
                        let item29 = element[1]['restan_total'] ?? 0;
                        let item30 = element[1]['skor_restan'] ?? 0;
                        let item31 = buah_tph(element[1]['skor_restan'])
                        let item32 = buah_tph(element[1]['skor_restan']) + brd_tph(element[1]['skor']);

                        let item33 = element[1]['blok_mb'] ?? 0;
                        let item34 = element[1]['jml_janjang'] ?? 0;
                        let item35 = element[1]['jml_mentah'] ?? 0;
                        let item36 = element[1]['PersenBuahMentah'] ?? 0;
                        let item37 = mb_mentah(element[1]['PersenBuahMentah']);
                        let item38 = element[1]['jml_masak'] ?? 0;
                        let item39 = element[1]['PersenBuahMasak'] ?? 0;
                        let item40 = mb_masak(element[1]['PersenBuahMasak']);
                        let item41 = element[1]['jml_over'] ?? 0;
                        let item42 = element[1]['PersenBuahOver'] ?? 0;
                        let item43 = mb_over(element[1]['PersenBuahOver']);
                        let item44 = element[1]['jml_empty'] ?? 0;
                        let item45 = element[1]['PersenPerJanjang'] ?? 0;
                        let item46 = mb_jangkos(element[1]['PersenPerJanjang']);
                        let item47 = element[1]['jml_vcut'] ?? 0;
                        let item48 = element[1]['PersenVcut'] ?? 0;
                        let item49 = mb_vcut(element[1]['PersenVcut']);
                        let item50 = element[1]['jml_abnormal'] ?? 0;
                        let item51 = element[1]['PersenAbr'] ?? 0;
                        let item52 = (element[1]['alas_mb'] ?? 0);
                        let item53 = element[1]['PersenKrgBrd'] ?? 0;
                        let item54 = mbalas_br(element[1]['PersenKrgBrd']);
                        let item55 = mbalas_br(element[1]['PersenKrgBrd']) + mb_vcut(element[1]['PersenVcut']) +
                            mb_jangkos(element[1]['PersenPerJanjang']) + mb_over(element[1]['PersenBuahOver']) +
                            mb_masak(element[1]['PersenBuahMasak']) + mb_mentah(element[1]['PersenBuahMentah']);
                        tod = (element[1]['skor_pale'] ?? 0) + (element[1]['skor_brd'] ?? 0) + (element[1]['skor_buah'] ?? 0)
                        ted = buah_tph(element[1]['skor_restan']) + brd_tph(element[1]['skor']);
                        tok = mbalas_br(element[1]['PersenKrgBrd']) + mb_vcut(element[1]['PersenVcut']) +
                            mb_jangkos(element[1]['PersenPerJanjang']) + mb_over(element[1]['PersenBuahOver']) +
                            mb_masak(element[1]['PersenBuahMasak']) + mb_mentah(element[1]['PersenBuahMentah']);
                        let item56 = tod + ted + tok;
                        let item57 = kategori(item56)
                        const items = [];
                        for (let i = 1; i <= 57; i++) {
                            items.push(eval(`item${i}`));
                        }

                        items.forEach((item, index) => {
                            const itemElement = document.createElement('td');
                            itemElement.classList.add('text-center');
                            itemElement.innerText = item;

                            if (index === 56) {
                                // Apply background color based on the value of item32
                                if (item === 'SATISFACTORY') {
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item === 'EXCELLENT') {
                                    itemElement.style.backgroundColor = '#08fc2c';
                                } else if (item === 'GOOD') {
                                    itemElement.style.backgroundColor = '#6074c4';
                                } else if (item === 'POOR') {
                                    itemElement.style.backgroundColor = '#ff0404';
                                } else if (item === 'FAIR') {
                                    itemElement.style.backgroundColor = '#ffa404';
                                }
                            }

                            if (index === 55) {
                                // Apply background color based on the value of item32
                                if (item >= 95) {
                                    itemElement.style.backgroundColor = '#08fc2c';
                                } else if (item >= 85) {
                                    itemElement.style.backgroundColor = '#5874c4';
                                } else if (item >= 75) {
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item >= 65) {
                                    itemElement.style.backgroundColor = '#ffa404';
                                } else {
                                    itemElement.style.backgroundColor = '#ff0404';
                                }
                            }

                            if (index >= 3 && index <= 55) {
                                // Apply background color based on the value of item32
                                if (item == 0) {
                                    itemElement.style.opacity = '0.5';
                                }
                            }
                            if (index == 3) {
                                if (Array.isArray(item) && item.length > 0) {
                                    var selectedValue = item[0];
                                    if (typeof selectedValue === 'number') {
                                        itemElement.innerText = selectedValue;
                                    } else if (typeof selectedValue === 'string') {
                                        // If selectedValue is a string, you can split it by comma and take the first value
                                        var values = selectedValue.split(',');
                                        itemElement.innerText = values[0].trim();
                                    }
                                }
                            }
                            if (index == 2) {
                                if (Array.isArray(item) && item.length > 0) {
                                    var selectedValue = item[0];
                                    if (typeof selectedValue === 'number') {
                                        itemElement.innerText = selectedValue;
                                    } else if (typeof selectedValue === 'string') {
                                        // If selectedValue is a string, you can split it by comma and take the first value
                                        var values = selectedValue.split(',');
                                        itemElement.innerText = values[0].trim();
                                    }
                                }
                            }




                            tr.appendChild(itemElement);

                        });

                        tbody1.appendChild(tr)
                        // }
                    });

                    var cellsx = document.getElementById('dataInspeksi');
                    let total = 0;
                    let total2 = 0;
                    let total3 = 0;
                    let total4 = 0;
                    let total5 = 0;
                    let total6 = 0;
                    let total7 = 0;
                    let total8 = 0;
                    let total9 = 0;
                    let total10 = 0;
                    let total11 = 0;
                    let total12 = 0;
                    let total13 = 0;
                    let total14 = 0;
                    let total15 = 0;
                    let total16 = 0;
                    let total17 = 0;
                    let total18 = 0;
                    let total19 = 0;
                    let total20 = 0;
                    let total21 = 0;
                    let total22 = 0;
                    let total23 = 0;
                    let total24 = 0;
                    let total25 = 0;
                    let total26 = 0;
                    let total27 = 0;
                    let total28 = 0;
                    let total29 = 0;

                    for (let i = 0; i < cellsx.rows.length; i++) {
                        if (cellsx.rows[i].cells.length > 3) {
                            total += Number(cellsx.rows[i].cells[3].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 4) {
                            total2 += Number(cellsx.rows[i].cells[4].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 5) {
                            total3 += Number(cellsx.rows[i].cells[5].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 6) {
                            total4 += Number(cellsx.rows[i].cells[6].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 7) {
                            total5 += Number(cellsx.rows[i].cells[7].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 8) {
                            total6 += Number(cellsx.rows[i].cells[8].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 9) {
                            total7 += Number(cellsx.rows[i].cells[9].innerText);
                        }

                        if (cellsx.rows[i].cells.length > 10) {
                            total8 += Number(cellsx.rows[i].cells[10].innerText);
                        }

                        if (cellsx.rows[i].cells.length > 11) {
                            total9 = total8 / total3;
                        }
                        if (cellsx.rows[i].cells.length > 12) {
                            total10 = skor_brd_ma((total8 / total3));
                        }
                        if (cellsx.rows[i].cells.length > 13) {
                            total11 += Number(cellsx.rows[i].cells[13].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 14) {
                            total12 += Number(cellsx.rows[i].cells[14].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 15) {
                            total13 += Number(cellsx.rows[i].cells[15].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 16) {
                            total14 += Number(cellsx.rows[i].cells[16].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 17) {
                            total15 += Number(cellsx.rows[i].cells[17].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 18) {
                            total16 = total15 / (total3 + total15) * 100;
                        }
                        if (cellsx.rows[i].cells.length > 20) {
                            total17 += Number(cellsx.rows[i].cells[20].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 24) {
                            total18 += Number(cellsx.rows[i].cells[24].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 25) {
                            total19 += Number(cellsx.rows[i].cells[25].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 28) {
                            total20 += Number(cellsx.rows[i].cells[28].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 32) {
                            total21 += Number(cellsx.rows[i].cells[32].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 33) {
                            total22 += Number(cellsx.rows[i].cells[33].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 34) {
                            total23 += Number(cellsx.rows[i].cells[34].innerText);
                        }
                        // abnormal
                        if (cellsx.rows[i].cells.length > 49) {
                            total24 += Number(cellsx.rows[i].cells[49].innerText);
                        }
                        //
                        if (cellsx.rows[i].cells.length > 37) {
                            total25 += Number(cellsx.rows[i].cells[37].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 40) {
                            total26 += Number(cellsx.rows[i].cells[40].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 43) {
                            total27 += Number(cellsx.rows[i].cells[43].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 46) {
                            total28 += Number(cellsx.rows[i].cells[46].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 51) {
                            total29 += Number(cellsx.rows[i].cells[51].innerText);
                        }

                    }

                    tr = document.createElement('tr');
                    let item1 = 'Total';
                    let item2 = total;
                    let item3 = total2.toFixed(2);
                    let item4 = total3;
                    let item5 = total4.toFixed(2);
                    let item6 = total5;
                    let item7 = total6;
                    let item8 = total7;
                    let item9 = total8;
                    let item10 = total9.toFixed(2);
                    let item11 = skor_brd_ma(total9);
                    let item12 = total11;
                    let item13 = total12;
                    let item14 = total13;
                    let item15 = total14;
                    let item16 = total15;
                    let item17 = total16.toFixed(2);
                    let item18 = skor_buah_Ma(total16.toFixed(2))
                    let item19 = total17
                    let item20 = (total17 / total * 100).toFixed(2)
                    let item21 = skor_palepah_ma(total17 / total * 100)
                    let item22 = skor_palepah_ma(total17 / total * 100) + skor_buah_Ma(total16.toFixed(2)) + skor_brd_ma(total9);
                    let item23 = total18.toFixed(2)
                    let item24 = total19
                    let item25 = (total19 / total18).toFixed(2)
                    let item26 = brd_tph(total19 / total18)
                    let item27 = total20
                    let item28 = (total20 / total18).toFixed(2)
                    let item29 = buah_tph(total20 / total18)
                    let item30 = buah_tph(total20 / total18) + brd_tph(total19 / total18)
                    let item31 = total21
                    let item32 = total22
                    let item33 = total23
                    let item34 = (total23 / (total22 - total24) * 100).toFixed(2)
                    let item35 = mb_mentah(total23 / (total22 - total24) * 100)
                    let item36 = total25
                    let item37 = (total25 / (total22 - total24) * 100).toFixed(2)
                    let item38 = mb_masak(total25 / (total22 - total24) * 100)
                    let item39 = total26
                    let item40 = (total26 / (total22 - total24) * 100).toFixed(2)
                    let item41 = mb_over(total26 / (total22 - total24) * 100)
                    let item42 = total27
                    let item43 = (total27 / (total22 - total24) * 100).toFixed(2)
                    let item44 = mb_jangkos(total27 / (total22 - total24) * 100)
                    let item45 = total28
                    let item46 = ((total28 / total22) * 100).toFixed(2)
                    let item47 = mb_vcut((total28 / total22) * 100)
                    let item48 = total24
                    let item49 = ((total24 / total22) * 100).toFixed(2)
                    // let item50 = total21 + '/' + total29
                    let item50 = total29 + '/' + total21
                    // (element[1]['blok_mb'] ?? 0) + '/' + (element[1]['alas_mb'] ?? 0);
                    let item51 = ((total29 / total21) * 100).toFixed(2)
                    let item52 = mbalas_br((total29 / total21) * 100)
                    let item53 = mbalas_br((total29 / total21) * 100) + mb_vcut((total28 / total22) * 100) + mb_jangkos(total27 / (total22 - total24) * 100) +
                        mb_over(total26 / (total22 - total24) * 100) + mb_masak(total25 / (total22 - total24) * 100) + mb_mentah(total23 / (total22 - total24) * 100)
                    let item54 = item53 + item30 + item22
                    let item55 = kategori(item54)
                    const items = [];
                    for (let i = 1; i <= 55; i++) {
                        items.push(eval(`item${i}`));
                    }


                    items.forEach((item, index) => {
                        const itemElement = document.createElement('td');
                        item
                        itemElement.classList.add('text-center');
                        itemElement.innerText = item;
                        if (index === 0) {
                            itemElement.setAttribute('colspan', '3'); // Add colspan attribute for item1
                        }
                        if (index === 54) {
                            // Apply background color based on the value of item32
                            if (item === 'SATISFACTORY') {
                                itemElement.style.backgroundColor = '#fffc04';
                            } else if (item === 'EXCELLENT') {
                                itemElement.style.backgroundColor = '#5074c4';
                            } else if (item === 'GOOD') {
                                itemElement.style.backgroundColor = '#6074c4';
                            } else if (item === 'POOR') {
                                itemElement.style.backgroundColor = '#ff0404';
                            } else if (item === 'FAIR') {
                                itemElement.style.backgroundColor = '#ffb004';
                            }
                        }

                        if (item54 >= 95) {
                            tr.style.backgroundColor = '#5074c4';
                        } else if (item54 >= 85) {
                            tr.style.backgroundColor = '#5874c4';
                        } else if (item54 >= 75) {
                            tr.style.backgroundColor = '#10fc2c';
                        } else if (item54 >= 65) {
                            tr.style.backgroundColor = '#ffa404';
                        } else {
                            tr.style.backgroundColor = '#ff0404';
                        }

                        tr.appendChild(itemElement);
                    });

                    tbody1.appendChild(tr);
                } else {
                    let inc = 1;
                    let tod = 0;
                    let ted = 0;
                    let tok = 0;
                    arrTbody1.forEach(element => {
                        tr = document.createElement('tr');
                        let item1 = inc++;
                        let item2 = element[0];
                        let item3 = element[1]['pokok_sample'] ?? 0;
                        let item4 = element[1]['luas_blok'] ?? 0
                        let item5 = element[1]['jml_jjg_panen'] ?? 0;
                        let item6 = element[1]['akp_real'] ?? 0;
                        let item7 = element[1]['p_ma'] ?? 0;
                        let item8 = element[1]['k_ma'] ?? 0;
                        let item9 = element[1]['gl_ma'] ?? 0;
                        let item10 = element[1]['total_brd_ma'] ?? 0;
                        let item11 = element[1]['btr_jjg_ma'] ?? 0;
                        let item12 = element[1]['skor_brd'] ?? 0;

                        let item13 = element[1]['bhts_ma'] ?? 0;
                        let item14 = element[1]['bhtm1_ma'] ?? 0;
                        let item15 = element[1]['bhtm2_ma'] ?? 0;
                        let item16 = element[1]['bhtm3_ma'] ?? 0;
                        let item17 = element[1]['tot_jjg_ma'] ?? 0;
                        let item18 = element[1]['jjg_tgl_ma'] ?? 0;
                        let item19 = element[1]['skor_buah'] ?? 0;
                        let item20 = element[1]['ps_ma'] ?? 0;
                        let item21 = element[1]['PerPSMA'] ?? 0;
                        let item22 = element[1]['skor_pale'] ?? 0;
                        let item23 = (element[1]['skor_pale'] ?? 0) + (element[1]['skor_brd'] ?? 0) + (element[1]['skor_buah'] ?? 0);


                        let item24 = element[1]['tph_sample'] ?? 0;
                        let item25 = element[1]['bt_total'] ?? 0;
                        let item26 = element[1]['skor'] ?? 0;
                        let item27 = brd_tph(element[1]['skor']);
                        let item28 = element[1]['restan_total'] ?? 0;
                        let item29 = element[1]['skor_restan'] ?? 0;
                        let item30 = buah_tph(element[1]['skor_restan'])
                        let item31 = buah_tph(element[1]['skor_restan']) + brd_tph(element[1]['skor']);

                        let item32 = element[1]['blok_mb'] ?? 0;
                        let item33 = element[1]['jml_janjang'] ?? 0;
                        let item34 = element[1]['jml_mentah'] ?? 0;
                        let item35 = element[1]['PersenBuahMentah'] ?? 0;
                        let item36 = mb_mentah(element[1]['PersenBuahMentah']);
                        let item37 = element[1]['jml_masak'] ?? 0;
                        let item38 = element[1]['PersenBuahMasak'] ?? 0;
                        let item39 = mb_masak(element[1]['PersenBuahMasak']);
                        let item40 = element[1]['jml_over'] ?? 0;
                        let item41 = element[1]['PersenBuahOver'] ?? 0;
                        let item42 = mb_over(element[1]['PersenBuahOver']);
                        let item43 = element[1]['jml_empty'] ?? 0;
                        let item44 = element[1]['PersenPerJanjang'] ?? 0;
                        let item45 = mb_jangkos(element[1]['PersenPerJanjang']);
                        let item46 = element[1]['jml_vcut'] ?? 0;
                        let item47 = element[1]['PersenVcut'] ?? 0;
                        let item48 = mb_vcut(element[1]['PersenVcut']);
                        let item49 = element[1]['jml_abnormal'] ?? 0;
                        let item50 = element[1]['PersenAbr'] ?? 0;
                        // let item51 = (element[1]['blok_mb'] ?? 0) + '/' + (element[1]['alas_mb'] ?? 0);
                        let item51 = (element[1]['alas_mb'] ?? 0);
                        let item52 = element[1]['PersenKrgBrd'] ?? 0;
                        let item53 = mbalas_br(element[1]['PersenKrgBrd']);
                        let item54 = mbalas_br(element[1]['PersenKrgBrd']) + mb_vcut(element[1]['PersenVcut']) +
                            mb_jangkos(element[1]['PersenPerJanjang']) + mb_over(element[1]['PersenBuahOver']) +
                            mb_masak(element[1]['PersenBuahMasak']) + mb_mentah(element[1]['PersenBuahMentah']);
                        tod = (element[1]['skor_pale'] ?? 0) + (element[1]['skor_brd'] ?? 0) + (element[1]['skor_buah'] ?? 0)
                        ted = buah_tph(element[1]['skor_restan']) + brd_tph(element[1]['skor']);
                        tok = mbalas_br(element[1]['PersenKrgBrd']) + mb_vcut(element[1]['PersenVcut']) +
                            mb_jangkos(element[1]['PersenPerJanjang']) + mb_over(element[1]['PersenBuahOver']) +
                            mb_masak(element[1]['PersenBuahMasak']) + mb_mentah(element[1]['PersenBuahMentah']);
                        let item55 = tod + ted + tok;
                        let item56 = kategori(item55)
                        const items = [];
                        for (let i = 1; i <= 56; i++) {
                            items.push(eval(`item${i}`));
                        }

                        items.forEach((item, index) => {
                            const itemElement = document.createElement('td');
                            itemElement.classList.add('text-center');
                            itemElement.innerText = item;

                            if (index === 55) {
                                // Apply background color based on the value of item32
                                if (item === 'SATISFACTORY') {
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item === 'EXCELLENT') {
                                    itemElement.style.backgroundColor = '#08fc2c';
                                } else if (item === 'GOOD') {
                                    itemElement.style.backgroundColor = '#10fc2c';
                                } else if (item === 'POOR') {
                                    itemElement.style.backgroundColor = '#ff0404';
                                } else if (item === 'FAIR') {
                                    itemElement.style.backgroundColor = '#ffa404';
                                }
                            }

                            if (index === 54) {
                                // Apply background color based on the value of item32
                                if (item >= 95) {
                                    itemElement.style.backgroundColor = '#08fc2c';
                                } else if (item >= 85) {
                                    itemElement.style.backgroundColor = '#5874c4';
                                } else if (item >= 75) {
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item >= 65) {
                                    itemElement.style.backgroundColor = '#ffa404';
                                } else {
                                    itemElement.style.backgroundColor = '#ff0404';
                                }
                            }

                            if (index >= 2 && index <= 54) {
                                // Apply background color based on the value of item32
                                if (item == 0) {
                                    itemElement.style.opacity = '0.5';
                                }
                            }
                            if (index == 3) {
                                if (Array.isArray(item) && item.length > 0) {
                                    var selectedValue = item[0];
                                    if (typeof selectedValue === 'number') {
                                        itemElement.innerText = selectedValue;
                                    } else if (typeof selectedValue === 'string') {
                                        // If selectedValue is a string, you can split it by comma and take the first value
                                        var values = selectedValue.split(',');
                                        itemElement.innerText = values[0].trim();
                                    }
                                }
                            }




                            tr.appendChild(itemElement);

                        });

                        tbody1.appendChild(tr)
                        // }
                    });


                    var cellsx = document.getElementById('dataInspeksi');
                    let total = 0;
                    let total2 = 0;
                    let total3 = 0;
                    let total4 = 0;
                    let total5 = 0;
                    let total6 = 0;
                    let total7 = 0;
                    let total8 = 0;
                    let total9 = 0;
                    let total10 = 0;
                    let total11 = 0;
                    let total12 = 0;
                    let total13 = 0;
                    let total14 = 0;
                    let total15 = 0;
                    let total16 = 0;
                    let total17 = 0;
                    let total18 = 0;
                    let total19 = 0;
                    let total20 = 0;
                    let total21 = 0;
                    let total22 = 0;
                    let total23 = 0;
                    let total24 = 0;
                    let total25 = 0;
                    let total26 = 0;
                    let total27 = 0;
                    let total28 = 0;
                    let total29 = 0;

                    for (let i = 0; i < cellsx.rows.length; i++) {
                        if (cellsx.rows[i].cells.length > 2) {
                            total += Number(cellsx.rows[i].cells[2].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 3) {
                            total2 += Number(cellsx.rows[i].cells[3].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 4) {
                            total3 += Number(cellsx.rows[i].cells[4].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 5) {
                            total4 += Number(cellsx.rows[i].cells[5].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 6) {
                            total5 += Number(cellsx.rows[i].cells[6].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 7) {
                            total6 += Number(cellsx.rows[i].cells[7].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 8) {
                            total7 += Number(cellsx.rows[i].cells[8].innerText);
                        }

                        if (cellsx.rows[i].cells.length > 9) {
                            total8 += Number(cellsx.rows[i].cells[9].innerText);
                        }

                        if (cellsx.rows[i].cells.length > 10) {
                            total9 = total8 / total3;
                        }
                        if (cellsx.rows[i].cells.length > 11) {
                            total10 = skor_brd_ma((total8 / total3));
                        }
                        if (cellsx.rows[i].cells.length > 12) {
                            total11 += Number(cellsx.rows[i].cells[12].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 13) {
                            total12 += Number(cellsx.rows[i].cells[13].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 14) {
                            total13 += Number(cellsx.rows[i].cells[14].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 15) {
                            total14 += Number(cellsx.rows[i].cells[15].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 16) {
                            total15 += Number(cellsx.rows[i].cells[16].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 17) {
                            total16 = total15 / (total3 + total15) * 100;
                        }
                        if (cellsx.rows[i].cells.length > 19) {
                            total17 += Number(cellsx.rows[i].cells[19].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 23) {
                            total18 += Number(cellsx.rows[i].cells[23].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 24) {
                            total19 += Number(cellsx.rows[i].cells[24].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 27) {
                            total20 += Number(cellsx.rows[i].cells[27].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 31) {
                            total21 += Number(cellsx.rows[i].cells[31].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 32) {
                            total22 += Number(cellsx.rows[i].cells[32].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 33) {
                            total23 += Number(cellsx.rows[i].cells[33].innerText);
                        }
                        // abnormal
                        if (cellsx.rows[i].cells.length > 48) {
                            total24 += Number(cellsx.rows[i].cells[48].innerText);
                        }
                        //
                        if (cellsx.rows[i].cells.length > 36) {
                            total25 += Number(cellsx.rows[i].cells[36].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 39) {
                            total26 += Number(cellsx.rows[i].cells[39].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 42) {
                            total27 += Number(cellsx.rows[i].cells[42].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 45) {
                            total28 += Number(cellsx.rows[i].cells[45].innerText);
                        }
                        if (cellsx.rows[i].cells.length > 50) {
                            total29 += Number(cellsx.rows[i].cells[50].innerText);
                        }

                    }

                    tr = document.createElement('tr');
                    let item1 = 'Total';
                    let item2 = total;
                    let item3 = total2.toFixed(2);
                    let item4 = total3;
                    let item5 = total4.toFixed(2);
                    let item6 = total5;
                    let item7 = total6;
                    let item8 = total7;
                    let item9 = total8;
                    let item10 = total9.toFixed(2);
                    let item11 = skor_brd_ma(total9);
                    let item12 = total11;
                    let item13 = total12;
                    let item14 = total13;
                    let item15 = total14;
                    let item16 = total15;
                    let item17 = total16.toFixed(2);
                    let item18 = skor_buah_Ma(total16.toFixed(2))
                    let item19 = total17
                    let item20 = (total17 / total * 100).toFixed(2)
                    let item21 = skor_palepah_ma(total17 / total * 100)
                    let item22 = skor_palepah_ma(total17 / total * 100) + skor_buah_Ma(total16.toFixed(2)) + skor_brd_ma(total9);
                    let item23 = total18
                    let item24 = total19
                    let item25 = (total19 / total18).toFixed(2)
                    let item26 = brd_tph(total19 / total18)
                    let item27 = total20
                    let item28 = (total20 / total18).toFixed(2)
                    let item29 = buah_tph(total20 / total18)
                    let item30 = buah_tph(total20 / total18) + brd_tph(total19 / total18)
                    let item31 = total21
                    let item32 = total22
                    let item33 = total23
                    let item34 = (total23 / (total22 - total24) * 100).toFixed(2)
                    let item35 = mb_mentah(total23 / (total22 - total24) * 100)
                    let item36 = total25
                    let item37 = (total25 / (total22 - total24) * 100).toFixed(2)
                    let item38 = mb_masak(total25 / (total22 - total24) * 100)
                    let item39 = total26
                    let item40 = (total26 / (total22 - total24) * 100).toFixed(2)
                    let item41 = mb_over(total26 / (total22 - total24) * 100)
                    let item42 = total27
                    let item43 = (total27 / (total22 - total24) * 100).toFixed(2)
                    let item44 = mb_jangkos(total27 / (total22 - total24) * 100)
                    let item45 = total28
                    let item46 = ((total28 / total22) * 100).toFixed(2)
                    let item47 = mb_vcut((total28 / total22) * 100)
                    let item48 = total24
                    let item49 = ((total24 / total22) * 100).toFixed(2)
                    // let item50 = total21 + '/' + total29
                    let item50 = total29 + '/' + total21
                    // (element[1]['blok_mb'] ?? 0) + '/' + (element[1]['alas_mb'] ?? 0);
                    let item51 = ((total29 / total21) * 100).toFixed(2)
                    let item52 = mbalas_br((total29 / total21) * 100)
                    let item53 = mbalas_br((total29 / total21) * 100) + mb_vcut((total28 / total22) * 100) + mb_jangkos(total27 / (total22 - total24) * 100) +
                        mb_over(total26 / (total22 - total24) * 100) + mb_masak(total25 / (total22 - total24) * 100) + mb_mentah(total23 / (total22 - total24) * 100)
                    let item54 = mbalas_br((total29 / total21) * 100) + mb_vcut((total28 / total22) * 100) + mb_jangkos(total27 / (total22 - total24) * 100) +
                        mb_over(total26 / (total22 - total24) * 100) + mb_masak(total25 / (total22 - total24) * 100) + mb_mentah(total23 / (total22 - total24) * 100) +
                        buah_tph(total20 / total18) + buah_tph(total20 / total18) + skor_palepah_ma(total17 / total * 100) + skor_buah_Ma(total16.toFixed(2)) + skor_brd_ma(total9);
                    let item55 = kategori(item54)
                    const items = [];
                    for (let i = 1; i <= 55; i++) {
                        items.push(eval(`item${i}`));
                    }


                    items.forEach((item, index) => {
                        const itemElement = document.createElement('td');
                        item
                        itemElement.classList.add('text-center');
                        itemElement.innerText = item;
                        if (index === 0) {
                            itemElement.setAttribute('colspan', '2'); // Add colspan attribute for item1
                        }




                        if (index === 55) {
                            // Apply background color based on the value of item32
                            if (item === 'SATISFACTORY') {
                                itemElement.style.backgroundColor = '#ffdc04';
                            } else if (item === 'EXCELLENT') {
                                itemElement.style.backgroundColor = '#5074c4';
                            } else if (item === 'GOOD') {
                                itemElement.style.backgroundColor = '#08fc2c';
                            } else if (item === 'POOR') {
                                itemElement.style.backgroundColor = '#ff0404';
                            } else if (item === 'FAIR') {
                                itemElement.style.backgroundColor = '#ffb004';
                            }
                        }

                        if (item54 >= 95) {
                            tr.style.backgroundColor = '#5074c4';
                        } else if (item54 >= 85) {
                            tr.style.backgroundColor = '#08fc2c';
                        } else if (item54 >= 75) {
                            tr.style.backgroundColor = '#ffdc04';
                        } else if (item54 >= 65) {
                            tr.style.backgroundColor = '#ffa404';
                        } else {
                            tr.style.backgroundColor = '#ff0404';
                        }

                        tr.appendChild(itemElement);
                    });

                    tbody1.appendChild(tr);


                    // }

                    // end table 
                }

            }
        });
    }
</script>