<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<!-- JavaScript dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />




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

    .modal-content {
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

    .modal-content h2 {
        /* margin-top: 20px; */
    }

    .modal-content .form-control {
        width: 100%;
    }

    .modal-content-custom-update .btn {
        margin-top: 10px;
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

    .pagination-container {
        display: flex;
        justify-content: center;
    }

    .header-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .header {
        width: 98%;
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
            display: flex;
            justify-content: center;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .pagination {
        display: flex;
        flex-wrap: wrap;
    }

    @media (max-width: 767px) {
        .pagination li {
            margin: 2px;
            font-size: 12px;
        }

        .pagination .page-link {
            padding: 0.125rem 0.25rem;
        }
    }

    .modal-image {
        max-width: 100%;
        height: auto;
    }

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


<div class="content-wrapper">
    <!-- <style>
        #back-to-data-btn {
            position: fixed;
            bottom: 70px;
            left: 80px;
            opacity: 0.2;
            transition: opacity 0.5s ease-in-out;
        }

        #back-to-data-btn:hover {
            opacity: 1;
        }
    </style> -->

    <div class="card table_wrapper">
        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <h2>REKAP HARIAN SIDAK MUTU BUAH </h2>
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
                    <form action="{{ route('filterdetialMutubuah') }}" method="POST" class="form-inline">
                        <div class="date">
                            {{ csrf_field() }}

                            <input type="hidden" name="est" id="est" value="{{$est}}">
                            <input type="hidden" name="afd" id="afd" value="{{$afd}}">
                            <select class="form-control" name="date" id="inputDate">
                                <option value="" disabled selected hidden>Pilih tanggal</option>
                                @foreach($tanggal as $item)
                                <option value="{{ $item}}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="ml-2 btn btn-primary mb-2" id="show-button">Show</button>
                        </div>
                    </form>

                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}-{{$afd}}</div>
                    <div class="afd">TANGGAL : <span id="selectedDate">{{ $bulan }}</span></div>
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

        <form action="{{ route('pdfBA_sidakbuah') }}" method="POST" class="form-inline" style="display: inline;" target="_blank">
            {{ csrf_field() }}
            <input type="hidden" name="estBA" id="estpdf" value="{{$est}}">
            <input type="hidden" name="afdBA" id="afdpdf" value="{{$afd}}">
            <input type="hidden" name="tglPDF" id="tglPDF" value="{{ isset($inputdate) ? $inputdate : '' }}">

            <button type="submit" class="btn btn-primary ml-2" id="download-button" disabled>
                Download BA
            </button>
        </form>
    </div>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h1 style="text-align: center;">Tabel Mutu Buah</h1>
                    <table class="table table-striped table-bordered" id="new_Sidak">
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



    <style>
        .table th,
        .table td {
            text-align: center;
            vertical-align: middle !important;
        }
    </style>

    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead>
                    <tr>
                        <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Estate</th>
                        <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Blok</th>
                        <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Petugas
                        </th>
                        <th colspan="27" style="background-color: #ffc404; color: white;" class="text-center">MUTU BUAH
                        </th>
                        <th rowspan="4" style="background-color: #b0a4a4; color: white;" class="text-center">All Skor
                        </th>
                        <th rowspan="4" style="background-color: #b0a4a4; color: white;" class="text-center">Kategori
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Total
                            Janjang Sample</th>
                        <th colspan="7" style="background-color: #ffc404; color: white;" class="text-center">Mentah</th>
                        <th rowspan="2" colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Matang</th>
                        <th rowspan="2" colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Lewat Matang (O)</th>
                        <th rowspan="2" colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Janjang Kosong (E)</th>
                        <th rowspan="2" colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Tidak Standar Vcut</th>
                        <th rowspan="2" colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Abnormal</th>
                        <th rowspan="2" colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Rat Damage</th>
                        <th rowspan="2" colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Penggunaan Karung Brondolan</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Tanpa
                            Brondol</th>
                        <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Kurang
                            Brondol</th>
                        <th colspan="3" style="background-color: #ffc404; color: white;" class="text-center">Total</th>
                    </tr>
                    <tr>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Total</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Total</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Total</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Total</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Total</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>Jjg</th>
                        <th>%</th>
                        <th>TPH</th>
                        <th>%</th>
                        <th>Skor</th>
                    </tr>

                </thead>
                <tbody id="data_tahunTab">
                </tbody>
            </table>

        </div>
    </div>



    <div class="card p-4">
        <h4 class="text-center mt-2" style="font-weight: bold">Tracking Plot Sidak Mutu Buah - {{$est}} {{$afd}} </h4>
        <hr>
        <div id="map" style="height:650px"></div>
    </div>




    <!-- Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modalImage" src="" style="width: 100%;">
        </div>
    </div>



    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <h2>Delete Sidak mutu buah</h2>
            <p>Apakah anda Yakin ingin Menghapus?</p>
            <div class="row">
                <div class="col   text-right">
                    <form id="delete-form" action="{{ route('deleteBA_mutubuah') }}" method="POST" onsubmit="event.preventDefault(); handleDeleteFormSubmit();">
                        {{ csrf_field() }}
                        <input type="hidden" id="delete-id" name="id">

                        <div class="button-group">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
                <div class="col   text-left">

                    <button id="close-delete-modal" class="btn btn-secondary">Tutup</button>
                </div>
            </div>


        </div>
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

        .modal-image-container {
            position: relative;
            display: inline-block;
        }

        .download-button-container {
            position: absolute;
            top: 0;
            right: 0;
            padding: 10px;
        }
    </style>

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" id="modalCloseButton" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-image-container">
                        <img class="modal-image" id="img01">
                        <div class="download-button-container">
                            <!-- Remove the "download" attribute from the anchor element -->
                            <a id="downloadButton" class="btn btn-primary" href="#">Download Image</a>
                        </div>
                    </div>
                    <p>Komentar:</p>
                    <p id="modalKomentar"></p>
                </div>
            </div>
        </div>
    </div>

    <div id="editModalTPH" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-align: center; font-weight: bold;">Update Mutu Buah</h5>
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm_buah" action="{{ route('updateBA_mutubuah') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <input type="hidden" class="form-control" id="idbuah" name="idbuah" value="">
                                <label for="update-blokCak" class="col-form-label">Blok</label>
                                <input type="text" class="form-control" id="update-blokCak" name="blokCak" value="">
                                <label for="petugasrow" class="col-form-label">Petugas</label>
                                <input type="text" class="form-control" id="petugasrow" name="petugasrow" value="">
                                <label for="tphbaris" class="col-form-label">TPH Baris</label>
                                <input type="text" class="form-control" id="tphbaris" name="tphbaris" value="">
                                <label for="ancakpemanen" class="col-form-label">Ancak Pemanen</label>
                                <input type="text" class="form-control" id="ancakpemanen" name="ancakpemanen" value="">
                                <label for="jumlahjanjang" class="col-form-label">Jumlah Janjang</label>
                                <input type="text" class="form-control" id="jumlahjanjang" name="jumlahjanjang" value="" required>
                            </div>
                            <div class="col">
                                <label for="bmt" class="col-form-label">BMT</label>
                                <input type="text" class="form-control" id="bmt" name="bmt" value="" required>



                                <label for="bmk" class="col-form-label">BMK</label>
                                <input type="text" class="form-control" id="bmk" name="bmk" value="" required>


                                <label for="overripe" class="col-form-label">OverRipe</label>
                                <input type="text" class="form-control" id="overripe" name="overripe" value="" required>


                                <label for="empty" class="col-form-label">Empty</label>
                                <input type="text" class="form-control" id="empty" name="empty" value="" required>


                                <label for="abnormal" class="col-form-label">Abnormal</label>
                                <input type="text" class="form-control" id="abnormal" name="abnormal" value="" required>
                            </div>
                            <div class="col">

                                <label for="ratdmg" class="col-form-label">Rat Damage</label>
                                <input type="text" class="form-control" id="ratdmg" name="ratdmg" value="" required>


                                <label for="vcut" class="col-form-label">Tidak Standar Vcut</label>
                                <input type="text" class="form-control" id="vcut" name="vcut" value="" required>


                                <label for="alasbr" class="col-form-label">Alas BR</label>
                                <input type="text" class="form-control" id="alasbr" name="alasbr" value="" required>


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



</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />



@include('layout/footer')

<script>
    $(document).ready(function() {
        // Close modal when the close button is clicked
        $('#closeModalBtn_buah').click(function() {
            $('#editModalTPH').modal('hide');
        });

        // Submit the form when the Save Changes button is clicked

        function isNumber(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        }



    });
    $("#show-button").click(function() {
        $('#data_tahunTab').empty()
        getDataTphYear()
        getmapsbuah();
        fetchAndUpdateData();
    });

    document.getElementById('show-button').addEventListener('click', function() {
        document.getElementById('download-button').disabled = false;
    });


    document.getElementById('show-button').addEventListener('click', function() {
        var selectedDate = document.getElementById('inputDate').value;
        document.getElementById('tglPDF').value = selectedDate;

        // Call the fetchAndUpdateData function to update the data
    });

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

    var table = $('#new_Sidak').DataTable({
        columns: [{
                title: 'ID',
                data: 'id',
            },
            {
                title: 'Estate',
                data: 'estate'
            },
            {
                title: 'Afdeling',
                data: 'afdeling'
            },
            {
                title: 'Blok',
                data: 'blok'
            },
            {
                title: 'Petugas',
                data: 'petugas'
            },
            {
                title: 'Waktu',
                data: 'datetime'
            },
            {
                title: 'TPH Baris',
                data: 'tph_baris'
            },
            {
                title: 'Ancak Pemanen',
                data: 'ancak_pemanen'
            },
            {
                title: 'Jumlah Janjang',
                data: 'jumlah_jjg'
            }, {
                title: 'Buah Mentah',
                data: 'bmt'
            },
            {
                title: 'Buah Masak',
                data: 'bmk'
            },
            {
                title: 'Buah Lewat Masak',
                data: 'overripe'
            },
            {
                title: 'Buah Kosong',
                data: 'empty_bunch'
            },
            {
                title: 'Buah Abnormal',
                data: 'abnormal'
            },
            {
                title: 'Rat Damage',
                data: 'rd'
            },
            {
                title: 'Tidak Vcut',
                data: 'vcut'
            },
            {
                title: 'Alas Karung',
                data: 'alas_br'
            },
            {
                title: 'Maps',
                data: 'app_version'
            },
            {
                title: 'Actions',
                visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
                render: function(data, type, row, meta) {
                    // Assuming 'id' is the field containing the ID
                    var id = row.id; // Fetch the ID from the row data
                    var buttons =
                        '<button class="edit-btn" data-id="' + id + '">Edit</button>' +
                        '<button class="delete-btn">Delete</button>';
                    return buttons;
                }
            }
        ],
        // Add other DataTable options as needed
    });
    $('#closeModalBtn_buah').click(function() {
        $('#editModalTPH').modal('hide');
    });

    function fetchAndUpdateData() {
        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        var tanggal = document.getElementById('inputDate').value
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('filterdetialMutubuah') }}",
            method: "GET",
            data: {
                tanggal,
                est,
                afd,
                _token: _token
            },
            success: function(result) {

                var parseResult = JSON.parse(result);

                // Clear existing data and add new data to the DataTable
                table.clear().rows.add(parseResult['mutubuah']).draw();
                $('#new_Sidak').on('click', '.edit-btn', function() {
                    var id = $(this).data('id'); // Retrieve the ID from the button's data attribute

                    var rowData = table.row($(this).closest('tr')).data();
                    var rowIndex = table.row($(this).closest('tr')).index();

                    editSidakTPh(rowIndex); // Pass the ID to your edit function
                });

                $('#new_Sidak').on('click', '.delete-btn', function() {
                    var rowIndex = table.row($(this).closest('tr')).index();
                    deleteRowBuah(rowIndex);
                });


            },
            error: function() {
                lottieAnimation.stop(); // Stop the Lottie animation
                lottieContainer.style.display = 'none'; // Hide the Lottie container
            }
        });

        function editSidakTPh(id) {

            // Retrieve the id from the first column of the selected row
            var rowData = table.row(id).data();

            // console.log(rowId);

            $('#update-blokCak').val(rowData.blok)
            $('#idbuah').val(rowData.id)
            $('#petugasrow').val(rowData.petugas)
            $('#tphbaris').val(rowData.tph_baris)
            $('#ancakpemanen').val(rowData.ancak_pemanen)
            $('#jumlahjanjang').val(rowData.jumlah_jjg)
            $('#bmt').val(rowData.bmt)
            $('#bmk').val(rowData.bmk)
            $('#overripe').val(rowData.overripe)
            $('#empty').val(rowData.empty_bunch)
            $('#abnormal').val(rowData.abnormal)
            $('#ratdmg').val(rowData.rd)
            $('#vcut').val(rowData.vcut)
            $('#alasbr').val(rowData.alas_br)
            $('#editModalTPH').modal('show')



        }


        function deleteRowBuah(id) {
            // Get the selected row data
            var rowData = table.row(id).data();
            var rowId = rowData.id;

            // Show the confirmation alert
            Swal.fire({
                title: 'Anda Yakin?',
                text: 'Data yang di hapus tidak dapat di kembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oke!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    // console.log(brdtgl);
                    // Send the AJAX request
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    $.ajax({
                        url: '{{ route("deleteBA_mutubuah") }}',
                        type: 'POST', // or 'GET' based on your setup
                        data: {
                            id: rowId
                        }, // send the row ID to your controller
                        success: function(response) {
                            // Handle success response if needed
                            if (response.message === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Data berhasil dihapus!'
                                }).then(function() {
                                    // Reload the page after the user clicks 'OK' on the success alert
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message === 'error' ? 'Error updating record' : 'Gagal memperbarui data!'
                                }).then(function() {
                                    // Reload the page after the user clicks 'OK' on the success alert
                                    location.reload();
                                });
                            }

                        },
                        error: function(xhr, status, error) {
                            // Handle error response if needed
                            console.error('Error deleting row:', error);
                        }
                    });
                }
            });
        }



        $(document).ready(function() {
            // Close modal when the close button is clicked
            $('#closeModalBtn_buah').click(function() {
                $('#editModalTPH').modal('hide');
            });

            // Submit the form when the Save Changes button is clicked
            $('#saveChangesBtn_buah').off('click').on('click', function() {
                $('#editForm_buah').submit();
            });

            function isNumber(value) {
                return !isNaN(parseFloat(value)) && isFinite(value);
            }

            $('#editForm_buah').submit(function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Get the form data
                var formData = new FormData(this);
                formData.append('id', $('#idbuah').val());

                var blok = $('#update-blokCak').val();

                var petugas = $('#petugasrow').val();
                var Tph_baris = $('#tphbaris').val();
                var ancak_pemanen = $('#ancakpemanen').val();
                var jml_jjg = $('#jumlahjanjang').val();
                var bmk = $('#bmk').val();
                var bmt = $('#bmt').val();
                var overripe = $('#overripe').val();
                var empty = $('#empty').val();
                var abnormal = $('#abnormal').val();
                var rd = $('#ratdmg').val();
                var vcut = $('#vcut').val();
                var alas_br = $('#alasbr').val();

                if (!isNumber(jml_jjg) ||
                    !isNumber(bmk) ||
                    !isNumber(bmt) ||
                    !isNumber(overripe) ||
                    !isNumber(empty) ||
                    !isNumber(rd) ||
                    !isNumber(vcut) ||
                    !isNumber(alas_br) ||
                    !isNumber(abnormal)
                ) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Masukan Error',
                        text: 'Hanya bisa di masukan angka Saja!'
                    });
                    return;
                }
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // console.log(brdtgl);
                // Send the AJAX request
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '{{ route("updateBA_mutubuah") }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editModalTPH').modal('hide');

                        if (response.message === 'Success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Data berhasil diperbarui!'
                            }).then(function() {
                                // Reload the page after the user clicks 'OK' on the success alert
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message === 'Invalid ID' ? 'Error updating record' : 'Gagal memperbarui data!'
                            }).then(function() {
                                // Reload the page after the user clicks 'OK' on the success alert
                                location.reload();
                            });
                        }


                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        // Show an error message or perform any other actions
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memperbarui data!'
                        }).then(function() {
                            // Reload the page after the user clicks 'OK' on the success alert
                            location.reload();
                        });
                    }
                });
            });

            $('#confirmDeleteBtn').off('click').on('click', function() {
                e.preventDefault(); // Prevent the default form submission
                // Create a form data object
                var formData = new FormData();
                // formData.append('delete_id', rowId);
                formData.append('delete_id', $('#idbuah').val());

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
                    url: '{{ route("deletedetailtph") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Close the delete modal
                        $('#deleteModalancak').modal('hide');

                        // Show a success message using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data deleted successfully!',
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle the error if needed
                        console.error(error);

                        // Close the delete modal
                        $('#deleteModalancak').modal('hide');
                    }
                });
            });
        });


    }




    function goBack() {
        // Save the selected tab to local storage
        localStorage.setItem('selectedTab', 'nav-data-tab');

        // Redirect to the target page
        window.location.href = "https://qc-apps.srs-ssms.com/dashboard_mutubuah";
    }

    document.getElementById("show-button").disabled = true;

    document.getElementById("inputDate").addEventListener("change", function() {
        document.getElementById("show-button").disabled = false;
    });



    function getDataTphYear() {

        var _token = $('input[name="_token"]').val();
        var est = document.getElementById('est').value
        var afd = document.getElementById('afd').value
        var tanggal = document.getElementById('inputDate').value



        $.ajax({
            url: "{{ route('getDataRekap') }}",
            method: "GET",
            data: {
                est,
                afd,
                tanggal,
                _token: _token
            },
            success: function(result) {
                //parsing result ke json untuk dalam estate
                var parseResult = JSON.parse(result)
                var afdResult = Object.entries(parseResult['sidak_buah'])
                var EstTotal = Object.entries(parseResult['total_buah'])

                // console.log(arrEst1);
                var arrEst1 = afdResult
                var tbody1 = document.getElementById('data_tahunTab');

                // console.log(arrEst1);
                // Assuming `arrEst1` is your array of data

                arrEst1.forEach((element) => {
                    const tr = document.createElement('tr');
                    let item1 = element[1]['estate']
                    let item2 = element[1]['est']
                    let item3 = element[1]['petugas']
                    let item4 = element[1]['Jumlah_janjang']
                    // mentah
                    let item5 = element[1]['tnp_brd']
                    let item6 = element[1]['persenTNP_brd']
                    let item7 = element[1]['krg_brd']
                    let item8 = element[1]['persenKRG_brd']
                    let item9 = element[1]['total_jjg']
                    let item10 = element[1]['persen_totalJjg']
                    let item11 = element[1]['skor_total']
                    // masak 
                    let item12 = element[1]['jjg_matang']
                    let item13 = element[1]['persen_jjgMtang']
                    let item14 = element[1]['skor_jjgMatang']
                    // lewat matang 
                    let item15 = element[1]['lewat_matang']
                    let item16 = element[1]['persen_lwtMtng']
                    let item17 = element[1]['skor_lewatMTng']
                    //janjang kosong
                    let item18 = element[1]['janjang_kosong']
                    let item19 = element[1]['persen_kosong']
                    let item20 = element[1]['skor_kosong']
                    // tidak standar vcut 
                    let item21 = element[1]['vcut']
                    let item22 = element[1]['vcut_persen']
                    let item23 = element[1]['vcut_skor']
                    // abnormal 
                    let item24 = element[1]['abnormal']
                    let item25 = element[1]['abnormal_persen']
                    // rat dmg
                    let item26 = element[1]['rat_dmg']
                    let item27 = element[1]['rd_persen']
                    // penggunaan  karung
                    let item28 = element[1]['TPH']
                    let item29 = element[1]['persen_krg']
                    let item30 = element[1]['skor_kr']
                    // all skor 
                    let item31 = element[1]['All_skor']
                    let item32 = element[1]['kategori']
                    const items = [];
                    for (let i = 1; i <= 32; i++) {
                        items.push(eval(`item${i}`));
                    }


                    items.forEach((item, index) => {
                        const itemElement = document.createElement('td');
                        itemElement.classList.add('text-center');
                        itemElement.innerText = item;

                        if (index === 31) {
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

                        tr.appendChild(itemElement);
                    });

                    tbody1.appendChild(tr);
                });
                // console.log(EstTotal);

                const tr = document.createElement('tr');
                let item1 = EstTotal[2][1];
                let item2 = EstTotal[3][1];
                let item3 = EstTotal[0][1];
                let item4 = EstTotal[4][1];
                let item5 = EstTotal[6][1];
                let item6 = EstTotal[5][1];
                let item7 = EstTotal[7][1];
                let item8 = EstTotal[8][1];
                let item9 = EstTotal[9][1];
                let item10 = EstTotal[10][1];

                let item11 = EstTotal[11][1];
                let item12 = EstTotal[12][1];
                let item13 = EstTotal[13][1];
                let item14 = EstTotal[14][1];
                let item15 = EstTotal[15][1];
                let item16 = EstTotal[16][1];
                let item17 = EstTotal[17][1];
                let item18 = EstTotal[18][1];
                let item19 = EstTotal[19][1];
                let item20 = EstTotal[20][1];
                let item21 = EstTotal[21][1];
                let item22 = EstTotal[22][1];
                let item23 = EstTotal[23][1];
                let item24 = EstTotal[25][1];
                let item25 = EstTotal[26][1];
                let item26 = EstTotal[27][1];
                let item27 = EstTotal[28][1];
                let item28 = EstTotal[29][1];
                let item29 = EstTotal[30][1];
                let item30 = EstTotal[31][1];
                let item31 = EstTotal[32][1];


                const items = [];
                for (let i = 1; i <= 31; i++) {
                    items.push(eval(`item${i}`));
                }

                items.forEach((item, index) => {
                    const itemElement = document.createElement('td');
                    itemElement.classList.add('text-center');
                    itemElement.innerText = item;
                    if (index === 0) {
                        itemElement.setAttribute('colspan', '2'); // Add colspan attribute for item1
                    }
                    if (index < 30) {
                        // Apply background color for indices 0 to 29
                        itemElement.style.backgroundColor = '#b8d48c'; // Set your desired background color here
                    }
                    if (index === 30) {
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
                    tr.appendChild(itemElement);
                });

                tbody1.appendChild(tr);
            }
        })
    }

    function openModal(src, komentar) {
        var modalImg = document.getElementById("img01");
        modalImg.src = src;
        var modalKomentar = document.getElementById("modalKomentar");
        modalKomentar.textContent = komentar;

        var downloadButton = document.getElementById("downloadButton");
        downloadButton.addEventListener("click", handleDownload);

        var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
        myModal.show();

        var closeButton = document.getElementById('modalCloseButton');
        closeButton.addEventListener('click', function() {
            myModal.hide();
            downloadButton.removeEventListener("click", handleDownload); // Remove the event listener when the modal is closed
            URL.revokeObjectURL(modalImg.src); // Clean up the object URL to avoid memory leaks
        });
    }

    function handleDownload(event) {
        var src = document.getElementById("img01").src;
        var filename = getFilenameFromSrc(src);
        downloadImage(src, filename);
    }

    function getFilenameFromSrc(src) {
        var startIndex = src.lastIndexOf("/") + 1;
        var endIndex = src.lastIndexOf(".");
        var filename = src.substring(startIndex, endIndex);

        // Split the filename into an array using "_" as the delimiter
        var parts = filename.split("_");

        // Extract the desired parts from the array
        var part1 = parts[0]; // IMA
        var part2 = parts[1]; // 2023710
        var part3 = parts[2]; // 100348
        var part4 = parts[3]; // KNE
        var part5 = parts[4]; // OA
        var part6 = parts[5]; // R01404
        var part7 = parts[6]; // 102

        // Construct the desired filename using the extracted parts and spaces
        var customPart = "Est " + "_" + part4 + " Afd " + "_" + part5 + " Sidak " + "_" + part1 + " Blok " + "_" + part6;

        return customPart;
    }




    function downloadImage(imageName, filename) {
        var downloadLink = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageName);

        fetch(downloadLink)
            .then(response => response.blob())
            .then(blob => {
                var url = URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.href = url;
                a.download = filename + ".jpg"; // Use the filename for the downloaded image
                a.style.display = "none"; // Hide the anchor element

                document.body.appendChild(a);

                a.click(); // Trigger the click event on the hidden anchor element

                // Clean up and remove the anchor element after the download
                a.remove();
                URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error("Error downloading image:", error);
            });
    }

    var map = L.map('map').setView([-2.2745234, 111.61404248], 13);
    var googleStreet = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });
    map.addControl(new L.Control.Fullscreen());
    var baseMaps = {
        "Google Street": googleStreet,
        "Google Satellite": googleSatellite
    };
    L.control.layers(baseMaps).addTo(map);


    var blokLayer, temuanLayer, legendContainer; // Define layer and legend variables


    function getmapsbuah() {


        if (blokLayer) {
            map.removeLayer(blokLayer);
        }
        if (temuanLayer) {
            map.removeLayer(temuanLayer);
        }
        if (legendContainer) {
            legendContainer.remove(map);
        }

        var Tanggal = document.getElementById('inputDate').value;
        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: "{{ route('getMapsData') }}",
            method: "get",
            data: {
                Tanggal,
                est,
                afd,
                _token: _token
            },
            success: function(result) {
                var plot = JSON.parse(result);
                const blokResult = Object.entries(plot['blok']);
                const markerResult = Object.entries(plot['marker']);
                const plotarrow = Object.entries(plot['plotarrow']);
                drawBlok(blokResult)
                drawTemuan(markerResult);
                drawLegend(markerResult)
                drawArrow(plotarrow)
            },



            error: function() {

            }
        });

        function drawBlok(blok) {

            var getPlotStr = '{"type"'
            getPlotStr += ":"
            getPlotStr += '"FeatureCollection",'
            getPlotStr += '"features"'
            getPlotStr += ":"
            getPlotStr += '['

            // console.log(blok)
            for (let i = 0; i < blok.length; i++) {
                getPlotStr += '{"type"'
                getPlotStr += ":"
                getPlotStr += '"Feature",'
                getPlotStr += '"properties"'
                getPlotStr += ":"
                getPlotStr += '{"blok"'
                getPlotStr += ":"
                getPlotStr += '"' + blok[i][1]['blok'] + '",'
                getPlotStr += '"estate"'
                getPlotStr += ":"
                getPlotStr += '"' + blok[i][1]['estate'] + '"'
                getPlotStr += '},'
                getPlotStr += '"geometry"'
                getPlotStr += ":"
                getPlotStr += '{"coordinates"'
                getPlotStr += ":"
                getPlotStr += '[['
                getPlotStr += blok[i][1]['latln']
                getPlotStr += ']],"type"'
                getPlotStr += ":"
                getPlotStr += '"Polygon"'
                getPlotStr += '}},'
            }
            getPlotStr = getPlotStr.substring(0, getPlotStr.length - 1);
            getPlotStr += ']}'


            var blok = JSON.parse(getPlotStr)

            var test = L.geoJSON(blok, {
                    onEachFeature: function(feature, layer) {

                        layer.myTag = 'BlokMarker'
                        var label = L.marker(layer.getBounds().getCenter(), {
                            icon: L.divIcon({
                                className: 'label-bidang',
                                html: feature.properties.blok,
                                iconSize: [50, 10]
                            })
                        }).addTo(map);

                        layer.addTo(map);
                    },
                    style: function(feature) {
                        switch (feature.properties.afdeling) {
                            case 'OA':
                                return {
                                    fillColor: "#ff1744",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,
                                };
                            case 'OB':
                                return {
                                    fillColor: "#d500f9",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,
                                };
                            case 'OC':
                                return {
                                    fillColor: "#ffa000",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,
                                };
                            case 'OD':
                                return {
                                    fillColor: "#00b0ff",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,
                                };

                            case 'OE':
                                return {
                                    fillColor: "#67D98A",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OF':
                                return {
                                    fillColor: "#666666",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                        }
                    }
                })
                .addTo(map);

            blokLayer = test; // Store the reference to the new blokLayer

            map.fitBounds(test.getBounds());

            // map.remove();
        }

        function drawTemuan(markerResult) {

            // console.log(markerResult);

            temuanLayer = L.layerGroup();

            for (let i = 0; i < markerResult.length; i++) {
                let latlng = JSON.parse(markerResult[i][1]['latln']);
                // Define the custom icons
                let numberIcon = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });

                let fotoTemuanIcon = L.icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
                    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                    iconSize: [14, 21],
                    iconAnchor: [7, 22],
                    popupAnchor: [1, -34],
                    shadowSize: [28, 20],
                });

                let markerIcon = numberIcon; // Default icon

                if (markerResult[i][1]['foto_temuan1'] || markerResult[i][1]['foto_temuan2']) {
                    markerIcon = fotoTemuanIcon; // Use fotoTemuanIcon if either foto_temuan1 or foto_temuan2 exists
                }

                let marker = L.marker(latlng, {
                    icon: markerIcon
                });

                var popupContent = `<strong>Jam Sidak: </strong>${markerResult[i][1]['time']}<br/>`;
                popupContent += `<strong>Mentah: </strong>${markerResult[i][1]['bmt']}<br/>`;
                popupContent += `<strong>Matang: </strong>${markerResult[i][1]['bmk']}<br/>`;
                popupContent += `<strong>Lewat Matang: </strong>${markerResult[i][1]['overripe']}<br/>`;
                popupContent += `<strong>Janjang Kosong: </strong>${markerResult[i][1]['empty_bunch']}<br/>`;
                popupContent += `<strong>Abnormal: </strong>${markerResult[i][1]['abnormal']}<br/>`;
                popupContent += `<strong> Rat Damage: </strong>${markerResult[i][1]['rd']}<br/>`;
                popupContent += `<strong>Tidak Standar Vcut: </strong>${markerResult[i][1]['vcut']}<br/>`;
                popupContent += `<strong>Alas Brondol: </strong>${markerResult[i][1]['alas_br']}<br/>`;
                if (markerResult[i][1]['verif']) {
                    popupContent += `<strong>Foto verifikasi.: </strong><br/>`;
                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/${markerResult[i][1]['verif']}" alt="Foto Temuan" style="max-width:150px; height:auto;" onclick="openModal(this.src, 'Verifikasi')"><br/>`;
                }
                if (markerResult[i][1]['foto_temuan']) {
                    popupContent += `<strong>Foto Temuan: </strong><br/>`;

                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/${markerResult[i][1]['foto_temuan']}" alt="Foto Temuan" style="max-width:150px; height:auto;" onclick="openModal(this.src, '${markerResult[i][1]['komentar']}')"><br/>`;
                }
                if (markerResult[i][1]['foto_temuan1']) {
                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/${markerResult[i][1]['foto_temuan1']}" alt="Foto Temuan" style="max-width:150px; height:auto;" onclick="openModal(this.src, '${markerResult[i][1]['komentar1']}')"><br/>`;
                }
                if (markerResult[i][1]['foto_temuan2']) {
                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/${markerResult[i][1]['foto_temuan2']}" alt="Foto Temuan" style="max-width:150px; height:auto;" onclick="openModal(this.src, '${markerResult[i][1]['komentar2']}')"><br/>`;
                }


                marker.bindPopup(popupContent);

                // Add the marker to the map
                marker.addTo(temuanLayer);

            }
            // Adjust the map's bounds to fit all markers
            if (markerResult.length > 0) {
                let latlngs = markerResult.map(item => JSON.parse(item[1]['latln']));
                let bounds = L.latLngBounds(latlngs);
                map.fitBounds(bounds);
            }

            temuanLayer.addTo(map); // Add the entire layer group to the map
        }

        function drawLegend(markerResult) {


            legendContainer = L.control({
                position: 'bottomright'
            });

            legendContainer.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'legend');
                div.innerHTML = '<h4 style="text-align: center;">Info</h4>';

                var temuanCount = 0;
                for (let i = 0; i < markerResult.length; i++) {
                    if (markerResult[i][1]['foto_temuan1'] || markerResult[i][1]['foto_temuan2']) {
                        temuanCount++;
                    }
                }


                var verifcount = 0;
                for (let i = 0; i < markerResult.length; i++) {
                    if (markerResult[i][1]['verif']) {
                        verifcount++;
                    }
                }
                var totalItemsCount = markerResult.length;
                // div.innerHTML += '<div class="legend-item">Total Sidak TPH: ' + totalItemsCount + '</div>'; // Added the legend item for total items count

                div.innerHTML += '<div class="legend-item"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" class="legend-icon"> Temuan (' + temuanCount + ')</div>';
                div.innerHTML += '<div class="legend-item"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png" class="legend-icon"> Verif (' + verifcount + ')</div>';

                return div;
            };

            legendContainer.addTo(map);
        }

        function drawArrow(plotarrow) {
            const latLngArray = plotarrow.map((item) => {
                const latLng = item[1].latln.split(','); // Split the latlng string into latitude and longitude
                return [parseFloat(latLng[0]), parseFloat(latLng[1])]; // Convert strings to numbers
            });


            for (let i = 0; i < latLngArray.length - 1; i++) {
                const startLatLng = latLngArray[i];
                const endLatLng = latLngArray[i + 1];

                const arrow = L.polyline([startLatLng, endLatLng], {
                    color: 'red',
                    weight: 2
                }).addTo(map);

                const arrowHead = L.polylineDecorator(arrow, {
                    patterns: [{
                        offset: '50%',
                        repeat: 50,
                        symbol: L.Symbol.arrowHead({
                            pixelSize: 12,
                            polygon: false,
                            pathOptions: {
                                color: 'yellow'
                            }
                        })
                    }]
                }).addTo(map);
            }
        }

    }
</script>