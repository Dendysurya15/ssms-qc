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
    <style>
        #back-to-data-btn {
            position: fixed;
            bottom: 30px;
            left: 80px;
            opacity: 0.7;
            transition: opacity 0.5s ease-in-out;
            z-index: 9999;
            /* Set a higher z-index value */
        }

        #back-to-data-btn:hover {
            opacity: 1;
        }
    </style>

    <button id="back-to-data-btn" class="btn btn-primary" onclick="goBack()">Back to Data</button>
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
                                <select class="form-control mb-2 mr-sm-2" name="date" id="inputDate"
                                    onchange="updateDate();">
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

                            <button type="button" class="ml-2 btn btn-primary mb-2" id="show-button"
                                onclick="updateTanggal();" disabled>Show</button>

                        </div>
                    </form>

                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}-{{$afd}}</div>
                    <!-- <div class="afd mt-2"> Rrgional : {{$reg}}-{{$afd}}</div> -->
                    <div class="afd">TANGGAL : <span id="selectedDate">{{ $tanggal }}</span></div>
                </div>
            </div>
        </div>
        <!-- animasi loading -->
        <div id="lottie-container"
            style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation"
                style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            </div>
        </div>
        <div id="lottie-container1"
            style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation"
                style="width: 100px; height: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            </div>
        </div>


        <!-- end animasi -->
    </div>
    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3">
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

            <form action="{{ route('pdfBA') }}" method="POST" class="form-inline" style="display: inline;"
                target="_blank">
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

            <form action="{{ route('pdfBA_excel') }}" method="POST" class="form-inline" style="display: inline;"
                target="_blank">
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
                        <th>Sample</th>
                        <th>Pokok_kuning</th>
                        <th>Piringan_semak</th>
                        <th>Underpruning</th>
                        <th>Overpruning</th>
                        <th>Jjg</th>
                        <th>Brtp</th>
                        <th>Brtk</th>
                        <th>Brtgl</th>
                        <th>Bhts</th>
                        <th>Bhtm1</th>
                        <th>Bhtm2</th>
                        <th>Bhtm3</th>
                        <th>Ps</th>
                        <th>Sp</th>
                        <th>Pokok_panen</th>
                        <th>Komentar</th>
                        <th>Status</th>
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
                        <th>Aksi</th>
                        @endif

                    </tr>
                </thead>
                <tbody id="tab1">
                </tbody>
            </table>
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
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
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
                        <th>Rst</th>
                        <th>Bt</th>
                        <th>Komentar</th>
                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
                        <th>Aksi</th>
                        @endif

                    </tr>
                </thead>


                <tbody id="tab3">
                </tbody>
            </table>
        </div>
    </div>

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
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            max-width: 400px;
            /* Adjust the width as needed */
            text-align: center;
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
    <div id="update-modal" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Ancak</h2>
            <button id="close-modal" class="btn btn-secondary">Tutup</button>
            <form id="update-form" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-id" name="id">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="mb-3">
                    <label for="update-blokCak" class="col-form-label">Blok</label>
                    <input type="text" class="form-control" id="update-blokCak" name="blokCak" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-StatusPnen" class="col-form-label">Status Panen</label>
                    <input type="text" class="form-control" id="update-StatusPnen" name="StatusPnen" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-sph" class="col-form-label">SPH</label>
                    <input type="text" class="form-control" id="update-sph" name="sph" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-br1" class="col-form-label">BR 1</label>
                    <input type="text" class="form-control" id="update-br1" name="br1" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-br2" class="col-form-label">BR 2</label>
                    <input type="text" class="form-control" id="update-br2" name="br2" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-sampCak" class="col-form-label">Sample</label>
                    <input type="text" class="form-control" id="update-sampCak" name="sampCak" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-pkKuning" class="col-form-label">Pokok Kuning</label>
                    <input type="text" class="form-control" id="update-pkKuning" name="pkKuning" value="" required>
                </div>

                <div class="mb-3">
                    <label for="update-prSmk" class="col-form-label">Piringan Semak</label>
                    <input type="text" class="form-control" id="update-prSmk" name="prSmk" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-undrPR" class="col-form-label">Underpruning</label>
                    <input type="text" class="form-control" id="update-undrPR" name="undrPR" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-overPR" class="col-form-label">Overpruning</label>
                    <input type="text" class="form-control" id="update-overPR" name="overPR" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-jjgCak" class="col-form-label">Janjang</label>
                    <input type="text" class="form-control" id="update-jjgCak" name="jjgCak" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtp" class="col-form-label">BRTP</label>
                    <input type="text" class="form-control" id="update-brtp" name="brtp" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtk" class="col-form-label">BRTK</label>
                    <input type="text" class="form-control" id="update-brtk" name="brtk" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtgl" class="col-form-label">BRTGL</label>
                    <input type="text" class="form-control" id="update-brtgl" name="brtgl" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-bhts" class="col-form-label">BHTS</label>
                    <input type="text" class="form-control" id="update-bhts" name="bhts" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-bhtm1" class="col-form-label">BHTM1</label>
                    <input type="text" class="form-control" id="update-bhtm1" name="bhtm1" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-bhtm2" class="col-form-label">BHTM2</label>
                    <input type="text" class="form-control" id="update-bhtm2" name="bhtm2" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-bhtm3" class="col-form-label">BHTM3</label>
                    <input type="text" class="form-control" id="update-bhtm3" name="bhtm3" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-ps" class="col-form-label">PS</label>
                    <input type="text" class="form-control" id="update-ps" name="ps" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-sp" class="col-form-label">SP</label>
                    <input type="text" class="form-control" id="update-sp" name="sp" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-pk_panenCAk" class="col-form-label">Pokok Panen</label>
                    <input type="text" class="form-control" id="update-pk_panenCAk" name="pk_panenCAk" value=""
                        required>
                </div>
                <!-- Add your other input fields here -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <div id="update-modal-buah" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Buah</h2>
            <button id="close-modal-buah" class="btn btn-secondary">Tutup</button>
            <form id="update-formBuah" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-ids" name="id_bh">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="mb-3">
                    <label for="update-estBH" class="col-form-label">Estate</label>
                    <input type="text" class="form-control" id="update-estBH" name="estBH" value="">
                </div>
                <div class="mb-3">
                    <label for="update-afdBH" class="col-form-label">Afdeling</label>
                    <input type="text" class="form-control" id="update-afdBH" name="afdBH" value="">
                </div>
                <div class="mb-3">
                    <label for="update-tphBH" class="col-form-label">TPH Baris</label>
                    <input type="text" class="form-control" id="update-tphBH" name="tphBH" value="">
                </div>
                <div class="mb-3">
                    <label for="update-blok_bh" class="col-form-label">Blok</label>
                    <input type="text" class="form-control" id="update-blok_bh" name="blok_bh" value="">
                </div>
                <div class="mb-3">
                    <label for="update-StatusBhpnen" class="col-form-label">Status Panen</label>
                    <input type="text" class="form-control" id="update-StatusBhpnen" name="StatusBhpnen" value="">
                </div>
                <div class="mb-3">
                    <label for="update-petugasBH" class="col-form-label">Petugas</label>
                    <input type="text" class="form-control" id="update-petugasBH" name="petugasBH" value="">
                </div>
                <div class="mb-3">
                    <label for="update-pemanen_bh" class="col-form-label">Ancak Pemanen</label>
                    <input type="text" class="form-control" id="update-pemanen_bh" name="pemanen_bh" value="">
                </div>
                <div class="mb-3">
                    <label for="update-bmt" class="col-form-label">BMT</label>
                    <input type="text" class="form-control" id="update-bmt" name="bmt" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-bmk" class="col-form-label">BMK </label>
                    <input type="text" class="form-control" id="update-bmk" name="bmk" value="" required>
                </div>

                <div class="mb-3">
                    <label for="update-emptyBH" class="col-form-label">Empty</label>
                    <input type="text" class="form-control" id="update-emptyBH" name="emptyBH" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-jjgBH" class="col-form-label">Jumlah Janjang</label>
                    <input type="text" class="form-control" id="update-jjgBH" name="jjgBH" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-overBH" class="col-form-label">OverRipe</label>
                    <input type="text" class="form-control" id="update-overBH" name="overBH" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-abrBH" class="col-form-label">Abnormal</label>
                    <input type="text" class="form-control" id="update-abrBH" name="abrBH" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-vcutBH" class="col-form-label">V Cut</label>
                    <input type="text" class="form-control" id="update-vcutBH" name="vcutBH" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-alsBR" class="col-form-label">Alas BR</label>
                    <input type="text" class="form-control" id="update-alsBR" name="alsBR" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-kmnBH" class="col-form-label">Komentar</label>
                    <input type="text" class="form-control" id="update-kmnBH" name="kmnBH" value="">
                </div>

                <!-- Add your other input fields here -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>




    <div id="update-modal-trans" class="modal-custom-update">
        <div class="modal-content-custom-update">
            <h2>Update Mutu Trans</h2>
            <button id="close-modal-trans" class="btn btn-secondary">Tutup</button>
            <form id="update-formTrans" action="{{ route('updateBA') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-id_trans" name="id_trans">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date">
                <div class="mb-3">
                    <label for="update-estTrans" class="col-form-label">Estate</label>
                    <input type="text" class="form-control" id="update-estTrans" name="estTrans" value="">
                </div>
                <div class="mb-3">
                    <label for="update-afd_trans" class="col-form-label">AFD</label>
                    <input type="text" class="form-control" id="update-afd_trans" name="afd_trans" value="">
                </div>
                <div class="mb-3">
                    <label for="update-blok_trans" class="col-form-label">Blok</label>
                    <input type="text" class="form-control" id="update-blok_trans" name="blok_trans" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-Status_trPanen" class="col-form-label">Status Panen</label>
                    <input type="text" class="form-control" id="update-Status_trPanen" name="Status_trPanen" value=""
                        required>
                </div>
                <div class="mb-3">
                    <label for="update-tphbrTrans" class="col-form-label">TPH Baris</label>
                    <input type="text" class="form-control" id="update-tphbrTrans" name="tphbrTrans" value="">
                </div>

                <div class="mb-3">
                    <label for="update-petugasTrans" class="col-form-label">Petugas</label>
                    <input type="text" class="form-control" id="update-petugasTrans" name="petugasTrans" value="">
                </div>
                <div class="mb-3">
                    <label for="update-bt_trans" class="col-form-label">BT </label>
                    <input type="text" class="form-control" id="update-bt_trans" name="bt_trans" value="" required>
                </div>

                <div class="mb-3">
                    <label for="update-rstTrans" class="col-form-label">Rst</label>
                    <input type="text" class="form-control" id="update-rstTrans" name="rstTrans" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-komentar_trans" class="col-form-label">Komentar</label>
                    <input type="text" class="form-control" id="update-komentar_trans" name="komentar_trans" value="">
                </div>

                <!-- Add your other input fields here -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>


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
            <button id="close-delete-modal" class="btn btn-secondary">Tutup</button>
            <form id="delete-form" action="{{ route('deleteBA') }}" method="POST"
                onsubmit="event.preventDefault(); handleDeleteFormSubmit();">
                {{ csrf_field() }}
                <input type="hidden" id="delete-id" name="id">
                <p>Apakah anda Yakin ingin Menghapus?</p>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    <div id="delete-modal-buah" class="modal">
        <div class="modal-content">
            <h2>Delete Mutu Buah</h2>
            <button id="close-delete-modals" class="btn btn-secondary">Tutup</button>
            <form id="delete-forms" action="{{ route('deleteBA') }}" method="POST" onsubmit="event.preventDefault();">
                {{ csrf_field() }}
                <input type="hidden" id="delete-ids" name="ids">
                <p>Apakah anda Yakin ingin Menghapus?</p>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <div id="delete-modal-transport" class="modal-custom">
        <div class="modal-content-custom">
            <h2>Delete Mutu Transport</h2>
            <button id="close-delete-transport" class="btn btn-secondary">Tutup</button>
            <form id="delete-form-trans" method="POST" onsubmit="event.preventDefault();">
                {{ csrf_field() }}
                <input type="hidden" id="delete-transport" name="id_transport">
                <p>Apakah anda Yakin ingin Menghapus?</p>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
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




</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
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
            method: "POST",
            data: {
                Tanggal,
                est,
                afd,
                _token: _token
            },
            success: function(result) {
                var polygonCoords = result.coords;
                var plot_blok = result.plot_blok;
                var trans_plot = result.trans_plot;
                var buah_plot = result.buah_plot;
                var ancak_plot = result.ancak_plot;
                var mapContainer = L.DomUtil.get('map');
                if (mapContainer != null) {
                    mapContainer._leaflet_id = null;
                }
                // Initialize the new map instance
                var map = L.map('map').fitBounds(polygonCoords.concat(plot_blok), 13);


                var googleStreet = L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }).addTo(map);

                var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });

                var baseMaps = {
                    "Google Street": googleStreet,
                    "Google Satellite": googleSatellite
                };
                L.control.layers(baseMaps).addTo(map);


                var estatePolygon = L.polygon(polygonCoords, {
                    color: '#003B73'
                }).addTo(map).bindPopup('<strong>Estate:</strong>' + est);
                // console.log(plot_blok);

                // Iterate over the keys of plot_blok
                for (var blockName in plot_blok) {
                    // Get the coordinates array for the current block
                    var coordinates = plot_blok[blockName];

                    // Create a polygon for the current block
                    var plotBlokPolygon = L.polygon(coordinates, {
                        color: 'yellow'
                    }).addTo(map).bindPopup('<strong>Afdeling:</strong>' + blockName);
                }




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
                    iconUrl: transIconUrl,
                    iconSize: [20, 35],
                    iconAnchor: [15, 20],
                    popupAnchor: [0, -20],

                });

                var transTmuanUrl = '{{ asset("img/placeholder2.png") }}';
                var transtemuan = L.icon({
                    iconUrl: transTmuanUrl,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],
                });
                var transFollowUrl = '{{ asset("img/placeholder3.png") }}';
                var transFollowup = L.icon({
                    iconUrl: transFollowUrl,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],

                });
                console.log(trans_plot);

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
                            var latLngs = []; // Array to store latitudes and longitudes for drawing lines

                            for (var i = 0; i < plots.length; i++) {
                                var plot = plots[i];
                                var lat = parseFloat(plot.lat);
                                var lon = parseFloat(plot.lon);
                                var blok = plot.blok;
                                var foto_temuan = plot.foto_temuan;
                                var foto_fu = plot.foto_fu;
                                var komentar = plot.komentar;

                                var markerIcon = foto_fu ? transFollowup : (foto_temuan ? transtemuan : transicon);

                                var popupContent = `<strong>Mutu Transport Blok: </strong>${blok}<br/>`;

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

                                    latLngs.push([lat, lon]); // Add latitudes and longitudes to the latLngs array
                                }
                            }

                            // Create a polyline from latLngs array to connect the plots within each block
                            var polyline = L.polyline(latLngs, {
                                color: 'blue'
                            }).addTo(map);
                        }
                    }
                }



                var myIconUrl = '{{ asset("img/pin.png") }}';
                var myIcon = L.icon({
                    iconUrl: myIconUrl,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],

                });
                var myIconUrl2 = '{{ asset("img/pin2.png") }}';
                var myIcon2 = L.icon({
                    iconUrl: myIconUrl2,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],

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

                                var markerIcon = foto_temuan ? myIcon : myIcon2; // Choose the icon based on the condition

                                var popupContent = `<strong>Mutu Buah Blok: </strong>${blok}<br/>`;

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
                    iconUrl: ancakTemuan1,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],
                    shadowUrl: ancakTemuan1,
                    shadowSize: [0, 0],
                    shadowAnchor: [0, 0],
                });

                var ancakTemuan2 = '{{ asset("img/push-pin1.png") }}';
                var caktemuan2 = L.icon({
                    iconUrl: ancakTemuan2,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],
                    shadowUrl: ancakTemuan2,
                    shadowSize: [0, 0],
                    shadowAnchor: [0, 0],
                });
                var ancak_fu1 = '{{ asset("img/push-pin1.png") }}';
                var cakfu1 = L.icon({
                    iconUrl: ancak_fu1,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],
                    shadowUrl: ancak_fu1,
                    shadowSize: [0, 0],
                    shadowAnchor: [0, 0],
                });
                var ancak_fu2 = '{{ asset("img/push-pin1.png") }}';
                var cakfu2 = L.icon({
                    iconUrl: ancak_fu2,
                    iconSize: [30, 45],
                    iconAnchor: [15, 45],
                    popupAnchor: [0, -30],
                    shadowUrl: ancak_fu2,
                    shadowSize: [0, 0],
                    shadowAnchor: [0, 0],
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

                        var markerIcon = (foto_fu1 || foto_fu2) ? cakfu2 : (foto_temuan1 || foto_temuan2) ? caktemuan2 : caktemuan1;

                        var popupContent = `<strong>Mutu Ancak Blok: </strong>${blok}<br/>`;

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

                        popupContent += `<strong>Komentar: </strong>${komentar}`;

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
                    var div = L.DomUtil.create('div', 'info legend');
                    var labels = ["Estate (klik icon untuk filter)", "Afdeling", "Mutu Ancak-Temuan-Follow Up", "Mutu Transport-Temuan-FolowUp", "Mutu Buah-Temuan"];
                    var icons = [Estatecon, afdCon, [caktemuan1, caktemuan2, cakfu1],
                        [transicon, transtemuan, transFollowup],
                        [myIcon2, myIcon]
                    ];
                    var layers = [estatePolygon, plotBlokPolygon, ancakGroup, transGroup, buahGroup];
                    var checkboxes = []; // Array to store checkbox elements

                    // Create legend content
                    for (var i = 0; i < icons.length; i++) {
                        var item = L.DomUtil.create('div', 'legend-item', div);

                        var checkbox = L.DomUtil.create('input');
                        checkbox.type = 'checkbox';
                        checkbox.checked = true; // Initially checked
                        checkbox.dataset.index = i; // Store the index of the layer

                        // Toggle layer visibility based on checkbox state
                        checkbox.addEventListener('change', function() {
                            var index = parseInt(this.dataset.index);
                            if (this.checked) {
                                map.addLayer(layers[index]);
                            } else {
                                map.removeLayer(layers[index]);
                            }
                        });

                        checkboxes.push(checkbox); // Add checkbox to the array

                        var iconContainer = L.DomUtil.create('div', 'icon-container', item);

                        // Check if icons[i] is an array of icons
                        if (Array.isArray(icons[i])) {
                            for (var j = 0; j < icons[i].length; j++) {
                                var icon = L.DomUtil.create('img', 'legend-icon', iconContainer);
                                icon.src = icons[i][j].options.iconUrl;
                            }
                        } else {
                            var icon = L.DomUtil.create('img', 'legend-icon', iconContainer);
                            icon.src = icons[i].options.iconUrl;
                        }

                        var label = L.DomUtil.create('span', 'label', item);
                        label.innerHTML = labels[i];

                        item.appendChild(checkbox); // Add checkbox to the legend item
                        item.appendChild(iconContainer);
                        item.appendChild(label);
                    }

                    // Function to show/hide layers based on checkbox selection
                    function updateLayerVisibility() {
                        for (var i = 0; i < checkboxes.length; i++) {
                            var checkbox = checkboxes[i];
                            var index = parseInt(checkbox.dataset.index);
                            if (checkbox.checked) {
                                map.addLayer(layers[index]);
                            } else {
                                map.removeLayer(layers[index]);

                                // If the layer being hidden is the plotBlokPolygon (Afdeling), hide all associated polygons
                                if (layers[index] === plotBlokPolygon) {
                                    for (var blockName in plot_blok) {
                                        var blockPolygons = plot_blok[blockName];
                                        for (var j = 0; j < blockPolygons.length; j++) {
                                            map.removeLayer(blockPolygons[j]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Initial layer visibility update
                    updateLayerVisibility();

                    return div;


                };

                // ...
                legend.addTo(map);




                // ...


                // Toggle layer visibility when the eye icon is clicked
                // Toggle layer visibility when the eye icon is clicked
                eye.addEventListener('click', function() {
                    var index = parseInt(this.dataset.index);

                    // Remove all layers
                    for (var j = 0; j < layers.length; j++) {
                        map.removeLayer(layers[j]);
                    }

                    // Add the clicked layer
                    map.addLayer(layers[index]);
                });


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
    document.addEventListener("DOMContentLoaded", function() {
        setInitialDate();

        // Add an event listener to update the date when the selected date changes
        const inputDate = document.getElementById("inputDate");
        inputDate.addEventListener("change", updateDate);

        // Add event listeners to close the modals when the close buttons are clicked
        const closeModalButton = document.getElementById("close-modal");
        closeModalButton.addEventListener("click", closeModal);

        const closeModalButtonBuah = document.getElementById("close-modal-buah");
        closeModalButtonBuah.addEventListener("click", closeModal);
        const closeModalButtonTrans = document.getElementById("close-modal-trans");
        closeModalButtonTrans.addEventListener("click", closeModal);
    });
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

        $('#tab1').empty()
        $('#tab2').empty()
        $('#tab3').empty()
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

                //modal

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
                //

                var parseResult = JSON.parse(result)
                var mutuAncak = Object.entries(parseResult['mutuAncak'])
                var mutuBuah = Object.entries(parseResult['mutuBuah'])
                var mutuTransport = Object.entries(parseResult['mutuTransport'])

                // Set the onclick event for the confirm delete button

                //modal untuk mnerima data untuk mutu ancak
                function openUpdateModal(id,
                    blok_ancak,
                    status_cak,
                    sph_ancak,
                    br1_cak,
                    br2_cak,
                    sample_cak,
                    pokok_kuning_cak,
                    piringan_semak_cak,
                    underpruning_cak,
                    overpruning_cak,
                    jjg_cak,
                    brtp_cak,
                    brtk_cak,
                    brtgl_cak,
                    bhts_cak,
                    bhtm1_cak,
                    bhtm2_cak,
                    bhtm3_cak,
                    ps_cak,
                    sp_cak,
                    pokok_panen_cak) {
                    const updateModal = document.getElementById('update-modal');
                    const updateForm = document.getElementById('update-form');
                    const updateId = document.getElementById('update-id');
                    const bloks = document.getElementById('update-blokCak');
                    const PanensStat = document.getElementById('update-StatusPnen');
                    const updateSph = document.getElementById('update-sph');
                    const updateBr1 = document.getElementById('update-br1');
                    const updateBr2 = document.getElementById('update-br2');
                    const sample = document.getElementById('update-sampCak');
                    const pokok_kuning = document.getElementById('update-pkKuning');

                    const piringan_semak = document.getElementById('update-prSmk');
                    const underpruning = document.getElementById('update-undrPR');
                    const overpruning = document.getElementById('update-overPR');
                    const janjang = document.getElementById('update-jjgCak');
                    const brtp = document.getElementById('update-brtp');
                    const brtk = document.getElementById('update-brtk');
                    const brtgl = document.getElementById('update-brtgl');
                    const bhts = document.getElementById('update-bhts');
                    const bhtm1 = document.getElementById('update-bhtm1');
                    const bhtm2 = document.getElementById('update-bhtm2');
                    const bhtm3 = document.getElementById('update-bhtm3');
                    const ps = document.getElementById('update-ps');
                    const sp = document.getElementById('update-sp');
                    const pk_panen = document.getElementById('update-pk_panenCAk');


                    updateId.value = id;
                    bloks.value = blok_ancak;
                    PanensStat.value = status_cak;
                    updateSph.value = sph_ancak;
                    updateBr1.value = br1_cak;
                    updateBr2.value = br2_cak;
                    sample.value = sample_cak;
                    pokok_kuning.value = pokok_kuning_cak;

                    piringan_semak.value = piringan_semak_cak;
                    underpruning.value = underpruning_cak;
                    overpruning.value = overpruning_cak;
                    janjang.value = jjg_cak;
                    brtp.value = brtp_cak;
                    brtk.value = brtk_cak;
                    brtgl.value = brtgl_cak;
                    bhts.value = bhts_cak;
                    bhtm1.value = bhtm1_cak;
                    bhtm2.value = bhtm2_cak;
                    bhtm3.value = bhtm3_cak;
                    ps.value = ps_cak;
                    sp.value = sp_cak;
                    pk_panen.value = pokok_panen_cak;

                    updateModal.style.display = 'block';

                    updateForm.onsubmit = function(event) {
                        event.preventDefault();
                        updateMutuAncak(event.target);
                    };
                }

                function createAksiButtons(row,
                    id,
                    blok_ancak,
                    status_cak,
                    sph_ancak,
                    br1_cak,
                    br2_cak,
                    sample_cak,
                    pokok_kuning_cak,
                    piringan_semak_cak,
                    underpruning_cak,
                    overpruning_cak,
                    jjg_cak,
                    brtp_cak,
                    brtk_cak,
                    brtgl_cak,
                    bhts_cak,
                    bhtm1_cak,
                    bhtm2_cak,
                    bhtm3_cak,
                    ps_cak,
                    sp_cak,
                    pokok_panen_cak) {
                    const td = document.createElement('td');
                    td.style.display = 'inline-flex';
                    if (currentUserName === 'Askep/Asisten' || currentUserName === 'Manager') {
                        const updateBtn = document.createElement('button');
                        updateBtn.className = 'btn btn-success mr-2';
                        updateBtn.innerHTML = '<i class="nav-icon fa-solid fa-edit"></i>';
                        updateBtn.onclick = function() {
                            openUpdateModal(id,
                                blok_ancak,
                                status_cak,
                                sph_ancak,
                                br1_cak,
                                br2_cak,
                                sample_cak,
                                pokok_kuning_cak,
                                piringan_semak_cak,
                                underpruning_cak,
                                overpruning_cak,
                                jjg_cak,
                                brtp_cak,
                                brtk_cak,
                                brtgl_cak,
                                bhts_cak,
                                bhtm1_cak,
                                bhtm2_cak,
                                bhtm3_cak,
                                ps_cak,
                                sp_cak,
                                pokok_panen_cak);
                        };

                        td.appendChild(updateBtn);

                        const deleteBtn = document.createElement('button');
                        deleteBtn.id = 'deleteBtn-' + id;
                        deleteBtn.className = 'btn btn-danger';
                        deleteBtn.innerHTML = '<i class="nav-icon fa-solid fa-trash"></i>';
                        deleteBtn.onclick = function() {
                            const deleteModal = document.getElementById('delete-modal');
                            deleteModal.style.display = 'block';
                            currentRowToDelete = row;
                            currentIdToDelete = id;
                            document.getElementById('delete-id').value = id;
                        };
                        td.appendChild(deleteBtn);
                    }
                    row.appendChild(td);
                }

                document.getElementById('close-delete-modal').addEventListener('click', function() {
                    const deleteModal = document.getElementById('delete-modal');
                    deleteModal.style.display = 'none';
                });

                document.getElementById('delete-form').addEventListener('submit', function(event) {
                    event.preventDefault();

                    // Perform the deletion using the global variables
                    deleteData(currentIdToDelete, currentRowToDelete);

                    // Reset the global variables
                    currentRowToDelete = null;
                    currentIdToDelete = null;

                    // Close the delete modal
                    const deleteModal = document.getElementById('delete-modal');
                    deleteModal.style.display = 'none';
                });

                function handleDeleteFormSubmit() {
                    const deleteId = document.getElementById('delete-id').value;
                    const row = document.querySelector(`tr[data-id="${deleteId}"]`);
                    deleteData(deleteId, row);
                }


                function deleteData(id, row) {
                    // Create an AJAX request to delete the data
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/deleteBA', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Successfully deleted, remove the row from the table
                            row.remove();

                            // Show the Lottie animation
                            lottieContainer1.style.display = 'block';
                            lottieAnimation1.play();

                            // Hide the Lottie animation after 3 seconds
                            setTimeout(() => {
                                lottieContainer1.style.display = 'none';
                                lottieAnimation1.stop();
                            }, 2000);
                        } else if (xhr.readyState === 4) {
                            // Error occurred, show an error message
                            alert('Error: Unable to delete data.');
                        }
                    };
                    xhr.send('id=' + encodeURIComponent(id));
                }

                //fungsi delete dan update mutu buah
                function mutuBuahUpdate(ids,
                    estateBH,
                    afdelingBH,
                    tph_barisBH,
                    blokBH,
                    status_bhpanen,
                    petugasBH,
                    ancak_pemanenBH,
                    bmkBH,
                    bmtBH,
                    emptyBH,
                    jumlah_jjgBH,
                    overripeBH,
                    abnormalBH,
                    vcutBH,
                    alas_brBH,
                    komentarBH) {
                    // console.log(ids, blokBH, bmk, bmt, ancak_pemanenBH);
                    const updateModal = document.getElementById('update-modal-buah');
                    const updateForm = document.getElementById('update-formBuah');

                    const updateId = document.getElementById('update-ids');
                    const updateBlok = document.getElementById('update-blok_bh');
                    const updtate_statusbh = document.getElementById('update-StatusBhpnen');
                    const updateBmt = document.getElementById('update-bmt');
                    const updateBmk = document.getElementById('update-bmk');
                    const updatePemanen = document.getElementById('update-pemanen_bh');

                    const estBH = document.getElementById('update-estBH');
                    const afdBH = document.getElementById('update-afdBH');

                    const tphBH = document.getElementById('update-tphBH');
                    const petugasBHs = document.getElementById('update-petugasBH');
                    const emptyBHS = document.getElementById('update-emptyBH');
                    const jjgBH = document.getElementById('update-jjgBH');
                    const overBH = document.getElementById('update-overBH');
                    const abrBH = document.getElementById('update-abrBH');
                    const vcutBHs = document.getElementById('update-vcutBH');
                    const alsBR = document.getElementById('update-alsBR');
                    const kmnBH = document.getElementById('update-kmnBH');

                    updateId.value = ids;
                    updateBlok.value = blokBH;
                    updtate_statusbh.value = status_bhpanen;
                    updateBmt.value = bmtBH;
                    updateBmk.value = bmkBH;
                    updatePemanen.value = ancak_pemanenBH;

                    estBH.value = estateBH;
                    afdBH.value = afdelingBH;
                    tphBH.value = tph_barisBH;

                    petugasBHs.value = petugasBH;
                    emptyBHS.value = emptyBH;
                    jjgBH.value = jumlah_jjgBH;
                    overBH.value = overripeBH;
                    abrBH.value = abnormalBH;
                    vcutBHs.value = vcutBH;
                    alsBR.value = alas_brBH;
                    kmnBH.value = komentarBH;

                    updateModal.style.display = 'block';

                    updateForm.onsubmit = function(event) {
                        event.preventDefault();
                        updateMutuBuah(event.target);
                    };
                }

                function buahAksibutton(row,
                    ids,
                    estateBH,
                    afdelingBH,
                    tph_barisBH,
                    blokBH,
                    status_bhpanen,
                    petugasBH,
                    ancak_pemanenBH,
                    bmkBH,
                    bmtBH,
                    emptyBH,
                    jumlah_jjgBH,
                    overripeBH,
                    abnormalBH,
                    vcutBH,
                    alas_brBH,
                    komentarBH
                ) {

                    const td = document.createElement('td');
                    td.style.display = 'inline-flex';
                    if (currentUserName === 'Askep/Asisten' || currentUserName === 'Manager') {
                        const updateBtn = document.createElement('button');
                        updateBtn.className = 'btn btn-success mr-2';
                        updateBtn.innerHTML = '<i class="nav-icon fa-solid fa-edit"></i>';
                        updateBtn.onclick = function() {
                            mutuBuahUpdate(ids,
                                estateBH,
                                afdelingBH,
                                tph_barisBH,
                                blokBH,
                                status_bhpanen,
                                petugasBH,
                                ancak_pemanenBH,
                                bmkBH,
                                bmtBH,
                                emptyBH,
                                jumlah_jjgBH,
                                overripeBH,
                                abnormalBH,
                                vcutBH,
                                alas_brBH,
                                komentarBH)
                        };
                        td.appendChild(updateBtn);

                        const deleteBtn = document.createElement('button');
                        deleteBtn.id = 'deleteBtn-' + ids;
                        deleteBtn.className = 'btn btn-danger';
                        deleteBtn.innerHTML = '<i class="nav-icon fa-solid fa-trash"></i>';
                        deleteBtn.onclick = function() {
                            // Open the delete modal
                            const deleteModal = document.getElementById('delete-modal-buah');
                            deleteModal.style.display = 'block';

                            // Store the current row and id in the global variables
                            currentRowToDelete = row;
                            currentIdToDelete = ids;

                            // Set the value of the delete-id input field
                            document.getElementById('delete-ids').value = ids;
                        };

                        td.appendChild(deleteBtn);
                    }
                    row.appendChild(td);
                }
                document.getElementById('close-delete-modals').addEventListener('click', function() {
                    const deleteModal = document.getElementById('delete-modal-buah');
                    deleteModal.style.display = 'none';
                });

                document.getElementById('delete-forms').addEventListener('submit', function(event) {
                    event.preventDefault();

                    // Perform the deletion using the global variables
                    deleteBuah(currentIdToDelete, currentRowToDelete);

                    // Reset the global variables
                    currentRowToDelete = null;
                    currentIdToDelete = null;

                    // Close the delete modal
                    const deleteModal = document.getElementById('delete-modal-buah');
                    deleteModal.style.display = 'none';
                });

                function deleteBuah(id, row) {
                    // Create an AJAX request to delete the data
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/deleteBA', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Successfully deleted, remove the row from the table
                            row.remove();

                            // Show the Lottie animation
                            lottieContainer1.style.display = 'block';
                            lottieAnimation1.play();

                            // Hide the Lottie animation after 3 seconds
                            setTimeout(() => {
                                lottieContainer1.style.display = 'none';
                                lottieAnimation1.stop();
                            }, 2000);
                        } else if (xhr.readyState === 4) {
                            // Error occurred, show an error message
                            alert('Error: Unable to delete data.');
                        }
                    };
                    xhr.send('ids=' + encodeURIComponent(id));

                }
                //fungsi delete dan update mutu transport

                function mutuTransUp(id_trans,
                    estate_trans,
                    afd_trans,
                    blok_trans,
                    panen_trStatus,
                    tphbrs_trans,
                    petugas_trans,
                    rst_trans,
                    bt_trans,
                    komentar_trans) {
                    // console.log(id_trans, afd_trans, blok_trans);
                    const updateModal = document.getElementById('update-modal-trans');
                    const updateForm = document.getElementById('update-formTrans');
                    const updateId = document.getElementById('update-id_trans');
                    const est = document.getElementById('update-estTrans');
                    const afd = document.getElementById('update-afd_trans');
                    const blok = document.getElementById('update-blok_trans');
                    const sttus_trPanen = document.getElementById('update-Status_trPanen');
                    const tphTrans = document.getElementById('update-tphbrTrans');
                    const petugas = document.getElementById('update-petugasTrans');
                    const bt_transs = document.getElementById('update-bt_trans');
                    const rst = document.getElementById('update-rstTrans');
                    const komen = document.getElementById('update-komentar_trans');

                    updateId.value = id_trans;
                    est.value = estate_trans;
                    afd.value = afd_trans;
                    blok.value = blok_trans;
                    sttus_trPanen.value = panen_trStatus;
                    tphTrans.value = tphbrs_trans;
                    petugas.value = petugas_trans;
                    bt_transs.value = bt_trans;
                    rst.value = rst_trans;
                    komen.value = komentar_trans;

                    updateModal.style.display = 'block';

                    updateForm.onsubmit = function(event) {
                        event.preventDefault();
                        updateMutuTrans(event.target);
                    };
                }

                function transportAksiButton(row,
                    id_trans,
                    estate_trans,
                    afd_trans,
                    blok_trans,
                    panen_trStatus,
                    tphbrs_trans,
                    petugas_trans,
                    rst_trans,
                    bt_trans,
                    komentar_trans
                ) {

                    const td = document.createElement('td');
                    td.style.display = 'flex'; // Change from 'inline-flex' to 'flex'
                    td.style.alignItems = 'center';
                    td.style.justifyContent = 'center';

                    if (currentUserName === 'Askep/Asisten' || currentUserName === 'Manager') {
                        const updateButton = document.createElement('button');
                        updateButton.className = 'btn btn-success mr-2';
                        updateButton.innerHTML = '<i class="nav-icon fa-solid fa-edit"></i>';
                        updateButton.addEventListener('click', function() {
                            mutuTransUp(id_trans,
                                estate_trans,
                                afd_trans,
                                blok_trans,
                                panen_trStatus,
                                tphbrs_trans,
                                petugas_trans,
                                rst_trans,
                                bt_trans,
                                komentar_trans);
                        });

                        const deleteBtn = document.createElement('button');
                        deleteBtn.id = 'deleteBtn-' + id_trans;
                        deleteBtn.className = 'btn btn-danger';
                        deleteBtn.innerHTML = '<i class="nav-icon fa-solid fa-trash"></i>';
                        deleteBtn.onclick = function() {
                            // Open the delete modal
                            const deleteModal = document.getElementById('delete-modal-transport');
                            deleteModal.style.display = 'block';

                            // Store the current row and id in the global variables
                            currentRowToDelete = row;
                            currentIdToDelete = id_trans;

                            // Set the value of the delete-id input field
                            document.getElementById('delete-transport').value = id_trans;
                        };

                        td.appendChild(updateButton);
                        td.appendChild(deleteBtn);
                    }
                    row.appendChild(td);
                }


                document.getElementById('close-delete-transport').addEventListener('click', function() {
                    const deleteModal = document.getElementById('delete-modal-transport');
                    deleteModal.style.display = 'none';
                });

                document.getElementById('delete-form-trans').addEventListener('submit', function(event) {
                    event.preventDefault();

                    // Perform the deletion using the global variables
                    deleteTrans(currentIdToDelete, currentRowToDelete);

                    // Reset the global variables
                    currentRowToDelete = null;
                    currentIdToDelete = null;

                    // Close the delete modal
                    const deleteModal = document.getElementById('delete-modal-transport');
                    deleteModal.style.display = 'none';
                });


                function deleteTrans(id, row) {
                    // Create an AJAX request to delete the data
                    const xhr = new XMLHttpRequest();
                    xhr.open('DELETE', `/deleteTrans/${encodeURIComponent(id)}`, true);
                    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Successfully deleted, remove the row from the table
                            row.remove();

                            // Show the Lottie animation
                            lottieContainer1.style.display = 'block';
                            lottieAnimation1.play();

                            // Hide the Lottie animation after 3 seconds
                            setTimeout(() => {
                                lottieContainer1.style.display = 'none';
                                lottieAnimation1.stop();
                            }, 2000);
                        } else if (xhr.readyState === 4) {
                            // Error occurred, show an error message
                            alert('Error: Unable to delete data.');
                        }
                    };
                    xhr.send();
                }


                //bagian menampilkan tabel semua mutu buah ancak dsb
                function createTableCell(value) {
                    const cell = document.createElement('td');
                    cell.innerText = value;
                    return cell;
                }

                function createTableRow(items) {
                    const tr = document.createElement('tr');
                    items.forEach(item => {
                        const td = document.createElement('td');
                        if (item instanceof HTMLElement) {
                            td.appendChild(item);
                        } else {
                            td.textContent = item;
                        }
                        tr.appendChild(td);
                    });
                    return tr;
                }

                function createImageElement(src) {
                    const img = document.createElement('img');
                    img.src = src;
                    img.style.width = '100px';
                    img.addEventListener('click', () => showModal(src));
                    return img;
                }

                var mutuAncak1 = mutuAncak
                var tRans = document.getElementById('tab1');
                mutuAncak1.forEach((element, index) => {

                    const items = [
                        index + 1,
                        element[1].estate,
                        element[1].afdeling,
                        element[1].blok,
                        element[1].petugas,
                        element[1].sph,
                        element[1].br1,
                        element[1].br2,
                        element[1].jalur_masuk,
                        element[1].status_panen,
                        element[1].kemandoran,
                        element[1].ancak_pemanen,
                        element[1].sample,
                        element[1].pokok_kuning,
                        element[1].piringan_semak,
                        element[1].underpruning,
                        element[1].overpruning,
                        element[1].jjg,
                        element[1].brtp,
                        element[1].brtk,
                        element[1].brtgl,
                        element[1].bhts,
                        element[1].bhtm1,
                        element[1].bhtm2,
                        element[1].bhtm3,
                        element[1].ps,
                        element[1].sp,
                        element[1].pokok_panen,
                        element[1].komentar,
                        element[1].aksi,
                    ];
                    const row = createTableRow(items);
                    // Inside the forEach loop

                    createAksiButtons(row, element[1].id,
                        element[1].blok,
                        element[1].status_panen,
                        element[1].sph,
                        element[1].br1,
                        element[1].br2,
                        element[1].sample,
                        element[1].pokok_kuning,
                        element[1].piringan_semak,
                        element[1].underpruning,
                        element[1].overpruning,
                        element[1].jjg,
                        element[1].brtp,
                        element[1].brtk,
                        element[1].brtgl,
                        element[1].bhts,
                        element[1].bhtm1,
                        element[1].bhtm2,
                        element[1].bhtm3,
                        element[1].ps,
                        element[1].sp,
                        element[1].pokok_panen,
                    );


                    tRans.appendChild(row);
                });



                // console.log(mutuBuah);

                var mutuBuahtb = mutuBuah
                var tbuah = document.getElementById('tab2');
                mutuBuahtb.forEach((element, index) => {
                    const items = [
                        index + 1,
                        element[1].estate,
                        element[1].afdeling,
                        element[1].tph_baris,
                        element[1].blok,
                        element[1].status_panen,
                        element[1].petugas,
                        element[1].ancak_pemanen,
                        element[1].bmk,
                        element[1].bmt,
                        element[1].empty,
                        element[1].jumlah_jjg,
                        element[1].overripe,
                        element[1].abnormal,
                        element[1].vcut,
                        element[1].alas_br,
                        element[1].komentar,
                    ];

                    const row = createTableRow(items);

                    buahAksibutton(row, element[1].id,
                        element[1].estate,
                        element[1].afdeling,
                        element[1].tph_baris,
                        element[1].blok,
                        element[1].status_panen,
                        element[1].petugas,
                        element[1].ancak_pemanen,
                        element[1].bmk,
                        element[1].bmt,
                        element[1].empty,
                        element[1].jumlah_jjg,
                        element[1].overripe,
                        element[1].abnormal,
                        element[1].vcut,
                        element[1].alas_br,
                        element[1].komentar
                    );

                    tbuah.appendChild(row);
                });
                // console.log(mutuTransport);
                var mutuTrans = mutuTransport
                var tTrans = document.getElementById('tab3');
                mutuTrans.forEach((element, index) => {
                    const items = [
                        index + 1,
                        element[1].estate,
                        element[1].afdeling,
                        element[1].blok,
                        element[1].status_panen,
                        element[1].tph_baris,
                        element[1].petugas,
                        element[1].rst,
                        element[1].bt,
                        element[1].komentar,
                    ];

                    const row = createTableRow(items);

                    transportAksiButton(row, element[1].id,
                        element[1].estate,
                        element[1].afdeling,
                        element[1].blok,
                        element[1].status_panen,
                        element[1].tph_baris,
                        element[1].petugas,
                        element[1].rst,
                        element[1].bt,
                        element[1].komentar,
                    );

                    tTrans.appendChild(row);
                });

            },
            error: function() {
                lottieAnimation.stop(); // Stop the Lottie animation
                lottieContainer.style.display = 'none'; // Hide the Lottie container
            }
        });



        function updateMutuAncak(form) {
            const formData = new FormData(form);

            $.ajax({
                type: "POST",
                url: "{{ route('updateBA') }}",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                success: function(data, textStatus, xhr) {
                    // Check if the status code is 200 OK
                    if (xhr.status === 200) {
                        // Close the modal
                        const updateModal = document.getElementById('update-modal');
                        updateModal.style.display = 'none';

                        // Show a success animation for 3 seconds
                        alert('Data berhasil di Perbaharui!')

                        // Refresh the data on the page
                        fetchAndUpdateData();
                    } else {
                        alert('Mutu ancak gagal diperbarui');
                        // Refresh the data on the page
                        fetchAndUpdateData();
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error data:", xhr.responseJSON);
                    console.error("There was a problem with the fetch operation:", error);
                },
            });
        }

        function updateMutuBuah(form) {
            const formData = new FormData(form);

            $.ajax({
                type: "POST",
                url: "{{ route('updateBA') }}",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                success: function(data, textStatus, xhr) {
                    // Check if the status code is 200 OK
                    if (xhr.status === 200) {
                        // Close the modal
                        const updateModal = document.getElementById('update-modal-buah');
                        updateModal.style.display = 'none';

                        // Show a success animation for 3 seconds
                        alert('Data berhasil di Perbaharui!')

                        // Refresh the data on the page
                        fetchAndUpdateData();
                    } else {
                        alert('Mutu ancak gagal diperbarui');
                        // Refresh the data on the page
                        fetchAndUpdateData();
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error data:", xhr.responseJSON);
                    console.error("There was a problem with the fetch operation:", error);
                },
            });
        }

        function updateMutuTrans(form) {
            const formData = new FormData(form);

            $.ajax({
                type: "POST",
                url: "{{ route('updateBA') }}",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                success: function(data, textStatus, xhr) {
                    // Check if the status code is 200 OK
                    if (xhr.status === 200) {
                        // Close the modal
                        const updateModal = document.getElementById('update-modal-trans');
                        updateModal.style.display = 'none';

                        // Show a success animation for 3 seconds
                        alert('Data berhasil di Perbaharui!')

                        // Refresh the data on the page
                        fetchAndUpdateData();
                    } else {
                        alert('Mutu ancak gagal diperbarui');
                        // Refresh the data on the page
                        fetchAndUpdateData();
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error data:", xhr.responseJSON);
                    console.error("There was a problem with the fetch operation:", error);
                },
            });
        }
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
        window.location.href = "http://ssms-qc.test/dashboard_inspeksi";
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


                if (reg == 2 || reg == 4) {
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
                        let item5 = element[1]['luas_blok'] ?? 0
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
                        let item52 = (element[1]['blok_mb'] ?? 0) + '/' + (element[1]['alas_mb'] ?? 0);
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
                                    itemElement.style.backgroundColor = '#5874c4';
                                } else if (item === 'GOOD') {
                                    itemElement.style.backgroundColor = '#10fc2c';
                                } else if (item === 'POOR') {
                                    itemElement.style.backgroundColor = '#ff0404';
                                } else if (item === 'FAIR') {
                                    itemElement.style.backgroundColor = '#ffa404';
                                }
                            }

                            if (index === 55) {
                                // Apply background color based on the value of item32
                                if (item >= 95) {
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item >= 85) {
                                    itemElement.style.backgroundColor = '#5874c4';
                                } else if (item >= 75) {
                                    itemElement.style.backgroundColor = '#10fc2c';
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
                        let item51 = (element[1]['blok_mb'] ?? 0) + '/' + (element[1]['alas_mb'] ?? 0);
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
                                    itemElement.style.backgroundColor = '#5874c4';
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
                                    itemElement.style.backgroundColor = '#fffc04';
                                } else if (item >= 85) {
                                    itemElement.style.backgroundColor = '#5874c4';
                                } else if (item >= 75) {
                                    itemElement.style.backgroundColor = '#10fc2c';
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
                }

            }
        });
    }
</script>