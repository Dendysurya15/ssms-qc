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

    .modal-content {
        background-color: #ffffff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #dee2e6;
        width: 40%;
        max-width: 500px;
        max-height: 70%;
        /* Set a maximum height */
        overflow-y: auto;
        /* Enable vertical scrolling if content overflows */
        border-radius: 0.3rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        animation: scaleUp 0.3s;
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
</style>


<div class="content-wrapper">
    <style>
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
    </style>
    <button id="back-to-data-btn" class="btn btn-primary" onclick="goBack()">Back to Data</button>
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
            <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
        </div>
        <div id="lottie-container1" style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation" style="width: 100px; height: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
        </div>


        <!-- end animasi -->
    </div>

    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3">
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

    <div class="d-flex flex-column align-items-center mt-3 mb-2 ml-3 mr-3">
        <h1 class="text-center">Tabel Sidak Mutu Buah</h1>
        <div class="table-responsive">
            <table class="table table-bordered" id="mutu_ancak">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Estate</th>
                        <th>Afdeling</th>
                        <th>Blok</th>
                        <th>Petugas</th>
                        <th>TPH Baris</th>
                        <th>Ancak Pemanen</th>
                        <th>Jumlah Janjang</th>
                        <th>BMT</th>
                        <th>BMK</th>
                        <th>OverRipe</th>
                        <th>Empty</th>
                        <th>Abnormal</th>
                        <th>Rat Damage</th>
                        <th>Tidak Standar Vcut</th>
                        <th>Alas BR</th>

                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
                        <th colspan="2">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tab1">
                </tbody>
            </table>
        </div>
        <!-- Add this after the </table> tag -->
        <div class="pagination-container d-flex justify-content-center mt-3">
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">
                </ul>
            </nav>
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
                        <th rowspan="4" style="background-color: #903c0c; color: white;" class="text-center">Petugas</th>
                        <th colspan="27" style="background-color: #ffc404; color: white;" class="text-center">MUTU BUAH</th>
                        <th rowspan="4" style="background-color: #b0a4a4; color: white;" class="text-center">All Skor</th>
                        <th rowspan="4" style="background-color: #b0a4a4; color: white;" class="text-center">Kategori</th>
                    </tr>
                    <tr>
                        <th rowspan="3" style="background-color: #ffc404; color: white;" class="text-center">Total Janjang Sample</th>
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
                        <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Tanpa Brondol</th>
                        <th colspan="2" style="background-color: #ffc404; color: white;" class="text-center">Kurang Brondol</th>
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






    <!-- Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modalImage" src="" style="width: 100%;">
        </div>
    </div>
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <h2>Update Sidak Mutu Buah</h2>
            <button id="close-modal" class="btn btn-secondary">Tutup</button>
            <form id="update-form" action="{{ route('updateBA_mutubuah') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="update-id" name="id">
                <input type="hidden" id="est" name="est" value="{{$est}}">
                <input type="hidden" id="afd" name="afd" value="{{$afd}}">
                <input type="hidden" id="date" name="date" value="{{$bulan}}">
                <div class=" mb-3">
                    <label for="update-blokCak" class="col-form-label">Blok</label>
                    <input type="text" class="form-control" id="update-blokCak" name="blokCak" value="">
                </div>
                <div class="mb-3">
                    <label for="update-sph" class="col-form-label">Petugas</label>
                    <input type="text" class="form-control" id="update-sph" name="sph" value="">
                </div>
                <div class="mb-3">
                    <label for="update-br1" class="col-form-label">TPH Baris</label>
                    <input type="text" class="form-control" id="update-br1" name="br1" value="">
                </div>
                <div class="mb-3">
                    <label for="update-br2" class="col-form-label">Ancak Pemanen</label>
                    <input type="text" class="form-control" id="update-br2" name="br2" value="">
                </div>
                <div class="mb-3">
                    <label for="update-sampCak" class="col-form-label">Jumlah Janjang</label>
                    <input type="text" class="form-control" id="update-sampCak" name="sampCak" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-pkKuning" class="col-form-label">BMT</label>
                    <input type="text" class="form-control" id="update-pkKuning" name="pkKuning" value="" required>
                </div>

                <div class="mb-3">
                    <label for="update-prSmk" class="col-form-label">BMK</label>
                    <input type="text" class="form-control" id="update-prSmk" name="prSmk" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-undrPR" class="col-form-label">OverRipe</label>
                    <input type="text" class="form-control" id="update-undrPR" name="undrPR" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-overPR" class="col-form-label">Empty</label>
                    <input type="text" class="form-control" id="update-overPR" name="overPR" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-jjgCak" class="col-form-label">Abnormal</label>
                    <input type="text" class="form-control" id="update-jjgCak" name="jjgCak" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtp" class="col-form-label">Rat Damage</label>
                    <input type="text" class="form-control" id="update-brtp" name="brtp" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtk" class="col-form-label">Tidak Standar Vcut</label>
                    <input type="text" class="form-control" id="update-brtk" name="brtk" value="" required>
                </div>
                <div class="mb-3">
                    <label for="update-brtgl" class="col-form-label">Alas BR</label>
                    <input type="text" class="form-control" id="update-brtgl" name="brtgl" value="" required>
                </div>

                <!-- Add your other input fields here -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>



    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <h2>Delete Sidak mutu buah</h2>
            <button id="close-delete-modal" class="btn btn-secondary">Tutup</button>
            <form id="delete-form" action="{{ route('deleteBA_mutubuah') }}" method="POST" onsubmit="event.preventDefault(); handleDeleteFormSubmit();">


                {{ csrf_field() }}
                <input type="hidden" id="delete-id" name="id">
                <p>Apakah anda Yakin ingin Menghapus?</p>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>





</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@include('layout/footer')

<script>
    document.getElementById('show-button').addEventListener('click', function() {
        document.getElementById('download-button').disabled = false;
    });


    document.getElementById('show-button').addEventListener('click', function() {
        var selectedDate = document.getElementById('inputDate').value;
        document.getElementById('tglPDF').value = selectedDate;

        // Call the fetchAndUpdateData function to update the data
        fetchAndUpdateData();
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
    ///
    // Function to close the update modal
    function closeModal() {
        const updateModal = document.getElementById("update-modal");
        updateModal.style.display = "none";
    }

    // Find the "Tutup" button in the update modal
    const closeModalButton = document.getElementById('close-modal');

    // Add an event listener to the "Tutup" button
    closeModalButton.addEventListener('click', closeModal);

    // The rest of your code...

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


        var est = document.getElementById('est').value;
        var afd = document.getElementById('afd').value;
        // var bulan = document.getElementById('bulan').value;


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


                // Set the onclick event for the confirm delete button

                //modal untuk mnerima data untuk mutu ancak
                function openUpdateModal(id,
                    blok_ancak,
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
                ) {
                    const updateModal = document.getElementById('update-modal');
                    const updateForm = document.getElementById('update-form');
                    const updateId = document.getElementById('update-id');
                    const bloks = document.getElementById('update-blokCak');
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


                    updateId.value = id;
                    bloks.value = blok_ancak;
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

                    updateModal.style.display = 'block';

                    updateForm.onsubmit = function(event) {
                        event.preventDefault();
                        updateMutuAncak(event.target);
                    };
                }

                function createAksiButtons(row,
                    id,
                    blok_ancak,
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
                ) {
                    const td = document.createElement('td');
                    td.style.display = 'inline-flex';
                    if (currentUserName === 'Askep/Asisten' || currentUserName === 'Manager') {
                        const updateBtn = document.createElement('button');
                        updateBtn.className = 'btn btn-success mr-2';
                        updateBtn.innerHTML = '<i class="nav-icon fa-solid fa-edit"></i>';
                        updateBtn.onclick = function() {
                            openUpdateModal(id,
                                blok_ancak,
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
                            );
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
                    handleDeleteFormSubmit();

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

                    const form = document.getElementById('delete-form');
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        success: function(data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                // Successfully deleted, remove the row from the table
                                row.remove();

                                // Show a SweetAlert
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Data has been deleted successfully.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Refresh the page
                                        location.reload();
                                    }
                                });
                            } else {
                                alert('Error: Unable to delete data.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Error data:", xhr.responseJSON);
                            console.error("There was a problem with the fetch operation:", error);
                        },
                    });
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
                const rowsPerPage = 10;
                let currentPage = 1;
                const totalPages = Math.ceil(mutuAncak1.length / rowsPerPage);

                function renderData(page) {
                    const start = (page - 1) * rowsPerPage;
                    const end = start + rowsPerPage;

                    const paginatedData = mutuAncak1.slice(start, end);

                    // Clear the table body
                    tRans.innerHTML = '';

                    paginatedData.forEach((element, index) => {

                        const items = [
                            index + 1,
                            element[1].estate,
                            element[1].afdeling,
                            element[1].blok,
                            element[1].petugas,
                            element[1].tph_baris,
                            element[1].ancak_pemanen,
                            element[1].jumlah_jjg,
                            element[1].bmt,
                            element[1].bmk,
                            element[1].overripe,
                            element[1].empty,
                            element[1].abnormal,
                            element[1].rd,
                            element[1].vcut,
                            element[1].alas_br,
                            element[1].aksi,
                        ];
                        const row = createTableRow(items);
                        // Inside the forEach loop
                        if (currentUserName === 'Askep/Asisten' || currentUserName === 'Manager') {
                            createAksiButtons(row, element[1].id,
                                element[1].blok,
                                element[1].petugas,
                                element[1].tph_baris,
                                element[1].ancak_pemanen,
                                element[1].jumlah_jjg,
                                element[1].bmt,
                                element[1].bmk,
                                element[1].overripe,
                                element[1].empty,
                                element[1].abnormal,
                                element[1].rd,
                                element[1].vcut,
                                element[1].alas_br,

                            );
                        }

                        tRans.appendChild(row);
                    });
                }

                function renderPagination() {
                    const paginationElement = document.getElementById('pagination');
                    paginationElement.innerHTML = '';

                    for (let i = 1; i <= totalPages; i++) {
                        const li = document.createElement('li');
                        li.classList.add('page-item');
                        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                        li.addEventListener('click', (e) => {
                            e.preventDefault();
                            currentPage = i;
                            renderData(currentPage);
                        });
                        paginationElement.appendChild(li);
                    }
                }

                // Render the first page and the pagination
                renderData(currentPage);
                renderPagination();



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
                url: "{{ route('updateBA_mutubuah') }}",
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

                        // Show a success SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Data berhasil diperbaharui',
                            showConfirmButton: false,
                            timer: 3000,
                        });

                        // Refresh the data on the page
                        fetchAndUpdateData();
                    } else {
                        // Show an error SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Mutu ancak gagal diperbarui',
                            showConfirmButton: false,
                            timer: 3000,
                        });

                        // Refresh the data on the page
                        fetchAndUpdateData();
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error data:", xhr.responseJSON);
                    console.error("There was a problem with the fetch operation:", error);

                    // Show an error SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was a problem with the fetch operation',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                },
            });
        }





    }

    function Show() {
        fetchAndUpdateData();
    }

    document.querySelector('button[type="button"]').addEventListener('click', Show);

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


    $("#show-button").click(function() {
        $('#data_tahunTab').empty()
        getDataTphYear()
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
</script>