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

    #pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1rem 0;
    }

    .pagination-button {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        text-align: center;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        cursor: pointer;
        margin-right: 0.25rem;
    }

    .pagination-button:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #495057;
    }

    .current-page {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    /* Add this @media query for mobile view */
    @media (max-width: 767px) {
        .header {
            flex-direction: column;
        }

        .right-container {
            text-align: center;
            margin-top: 15px;
        }

        .form-inline {
            justify-content: center;
        }
    }

    /* The rest of the CSS */
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

    .legend {
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .legend h4 {
        margin-top: 0;
        margin-bottom: 10px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .legend-icon {
        width: 14px;
        height: 21px;
        margin-right: 5px;
    }
</style>


<div class="content-wrapper">
    <!-- <style>
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
    </style> -->


    <div class="card table_wrapper">
        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <h2>REKAP HARIAN SIDAK TPH </h2>
        </div>

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
                <form action="{{ route('filtersidaktphrekap') }}" method="POST" class="form-inline">
                    <div class="date">
                        {{ csrf_field() }}
                        <input type="hidden" name="est" id="est" value="{{$est}}">
                        <select class="form-control" name="date" id="inputDate" onchange="updateButtonState()">
                            <option value="" disabled selected hidden>Pilih tanggal</option>
                            @foreach($filter as $item)
                            <option value="{{ $item}}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="ml-2 btn btn-primary" id="showFindingYear" disabled>Show</button>
                </form>
                <div class="afd"> ESTATE : {{$est}} / {{$afd}}</div>
                <div class="afd">TANGGAL : <span id="selectedDate">{{ $tanggal }}</span></div>
            </div>
        </div>

        <!-- animasi loading -->
        <div id="lottie-container" style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
            <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            </div>
        </div>

        <!-- end animasi -->
    </div>
    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3">
        <button id="back-to-data-btn" class="btn btn-primary" onclick="goBack()">Back to Data</button>
        <form action="{{ route('pdfBAsidak') }}" method="GET" class="form-inline" style="display: inline;" target="_blank">
            {{ csrf_field() }}
            <input type="hidden" name="est" id="est" value="{{$est}}">
            <input type="hidden" name="afdling" id="afdling" value="{{$afd}}">
            <input type="hidden" name="inputDates" id="inputDates" value="">

            <button type="submit" class="btn btn-primary ml-2" id="download-button">
                Download BA
            </button>
        </form>
    </div>


    <!-- animasi loading -->
    <div id="lottie-container" style="width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(255, 255, 255, 0.8); display: none; z-index: 9999;">
        <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        </div>
    </div>


    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h1 style="text-align: center;">Tabel Mutu Transport</h1>
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


    <div id="editModalTPH" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-align: center; font-weight: bold;">Update Mutu Transport</h5>
                    <button type="button" class="close" id="closeModalBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm_buah" action="{{ route('updatesidakTPhnew') }}" method="POST">
                        {{ csrf_field() }}
                        <div class=" row m-1">
                            <div class="col">

                                <label for="update-editId_buah" class="col-form-label">ID</label>
                                <input type="text" class="form-control" id="editId_buah" name="id">

                                <label for="update-estBH" class="col-form-label">Estate</label>
                                <input type="text" class="form-control" id="update-estBH" name="estBH" value="">


                                <label for="update-afdBH" class="col-form-label">Afdeling</label>
                                <input type="text" class="form-control" id="update-afdBH" name="afdBH" value="">


                                <label for="update-blok_bh" class="col-form-label">Blok</label>
                                <input type="text" class="form-control" id="update-blok_bh" name="blok_bh" value="">


                            </div>

                            <div class="col">

                                <label for="update-brdtgl" class="col-form-label">Brondolan TPH</label>
                                <input type="text" class="form-control" id="update-brdtgl" name="brdtgl" value="" required>

                                <label for="update-brdjln" class="col-form-label">Brondolan Jalan</label>
                                <input type="text" class="form-control" id="update-brdjln" name="brdjln" value="" required>

                                <label for="update-brdbin" class="col-form-label">Brondolan BIN</label>
                                <input type="text" class="form-control" id="update-brdbin" name="brdbin" value="" required>

                                <label for="update-qc" class="col-form-label">QC</label>
                                <input type="text" class="form-control" id="update-qc" name="qc" value="">

                            </div>
                        </div>

                        <div class="row m-1">
                            <div class="col">
                                <label for="update-jumkrng" class="col-form-label">Jumlah Karung</label>
                                <input type="text" class="form-control" id="update-jumkrng" name="jumkrng" value="" required>

                                <label for="update-buahtgl" class="col-form-label">Buah Tinggal</label>
                                <input type="text" class="form-control" id="update-buahtgl" name="buahtgl" value="" required>

                                <label for="update-restan" class="col-form-label">Restan Unreported Karung</label>
                                <input type="text" class="form-control" id="update-restan" name="restan" value="" required>

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


    <div id="foto_temuan">


    </div>


    <div class="card p-4">
        <h4 class="text-center mt-2" style="font-weight: bold">Tracking Plot Sidak TPH - {{ $est }}
            {{ $afd }}
        </h4>
        <hr>
        <div id="map" style="height:800px"></div>
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

</div>
<input type="hidden" id="estate" value="{{$est}}">
<input type="hidden" id="afd" value="{{$afd}}">
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- jQuery and Bootstrap JS -->

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />


@include('layout/footer')

<script>
    function updateButtonState() {
        var inputDate = document.getElementById("inputDate");
        var showFindingYear = document.getElementById("showFindingYear");
        var inputDates = document.getElementById("inputDates");

        if (inputDate.value !== "") {
            showFindingYear.disabled = false;
            inputDates.value = inputDate.value; // Update the hidden input field value
        } else {
            showFindingYear.disabled = true;
            inputDates.value = ""; // Reset the hidden input field value
        }
    }

    const lottieContainer = document.getElementById('lottie-container');
    const lottieAnimation = lottie.loadAnimation({
        container: lottieContainer,
        renderer: "svg",
        loop: true,
        autoplay: false,
        path: "https://assets3.lottiefiles.com/private_files/lf30_fup2uejx.json",
    });
    document.getElementById('showFindingYear').onclick = function() {
        lottieContainer.style.display = 'block'; // Show the Lottie container
        lottieAnimation.play(); // Start the Lottie animation
        dashboardFindingYear()
    }



    var currentUserName = "{{ session('jabatan') }}";
    window.onload = function() {
        // Add the event listener for the "Save changes" button when the DOM is ready
        document.getElementById('save-changes-button').addEventListener('click', updateFunction);
    };

    function dashboardFindingYear(page = 1) {
        // $('#tab1').empty()


        if ($.fn.DataTable.isDataTable('#new_Sidak')) {
            $('#new_Sidak').DataTable().destroy();
        }
        var tanggal = ''
        var est = ''

        var _token = $('input[name="_token"]').val();

        var tanggal = document.getElementById('inputDate').value
        var est = document.getElementById('est').value

        var estate = document.getElementById('estate').value;
        var afd = document.getElementById('afd').value;

        // console.log(tanggal);
        // console.log(est);
        $.ajax({
            url: "{{ route('filtersidaktphrekap') }}",
            method: "GET",
            data: {
                est,
                estate,
                afd,
                tanggal,
                page,
                _token: _token
            },
            success: function(result) {
                lottieAnimation.stop(); // Stop the Lottie animation
                lottieContainer.style.display = 'none'; // Hide the Lottie container

                var parseResult = JSON.parse(result);


                // new sida 
                var sidakTPhNEw = $('#new_Sidak').DataTable({
                    columns: [{
                            title: 'ID',
                            data: 'id',
                        },
                        {
                            title: 'Estate',
                            data: 'est'
                        },
                        {
                            title: 'Afdeling',
                            data: 'afd'
                        },
                        {
                            title: 'Blok',
                            data: 'blok'
                        },
                        {
                            title: 'QC',
                            data: 'qc'
                        },
                        {
                            title: 'No TPH',
                            data: 'no_tph'
                        },
                        {
                            title: 'Brondolan Tinggal TPH',
                            data: 'bt_tph'
                        },
                        {
                            title: 'Brondolan TInggal di Jalan',
                            data: 'bt_jalan'
                        },
                        {
                            title: 'Brondolan Tinggal di BIN',
                            data: 'bt_bin'
                        },
                        {
                            title: 'Jumlah Karung',
                            data: 'jum_karung'
                        },
                        {
                            title: 'Buah Tinggal',
                            data: 'buah_tinggal'
                        },
                        {
                            title: 'Restan Unreported',
                            data: 'restan_unreported'
                        },
                        {
                            // -1 targets the last column
                            title: 'Actions',
                            visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
                            render: function(data, type, row, meta) {
                                var buttons =
                                    '<button class="edit-btn">Edit</button>' +
                                    '<button class="delete-btn">Delete</button>';
                                return buttons;
                            }
                        }
                    ],

                });

                // Populate DataTable with data
                sidakTPhNEw.clear().rows.add(parseResult['sidak_tph2']).draw();


                $('#new_Sidak').on('click', '.edit-btn', function() {
                    var rowData = sidakTPhNEw.row($(this).closest('tr')).data();
                    var rowIndex = sidakTPhNEw.row($(this).closest('tr')).index();
                    editSidakTPh(rowIndex);
                });

                $('#new_Sidak').on('click', '.delete-btn', function() {
                    var rowIndex = sidakTPhNEw.row($(this).closest('tr')).index();
                    deleteRowBuah(rowIndex);
                });


                function editSidakTPh(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Retrieve the id from the first column of the selected row
                    var rowData = sidakTPhNEw.row(id).data();
                    var rowId = rowData[0];

                    // Populate the form with the data of the selected row
                    $('#editId_buah').val(rowData.id).prop('disabled', true);
                    $('#update-estBH').val(rowData.est).prop('disabled', true);
                    $('#update-afdBH').val(rowData.afd).prop('disabled', true);
                    $('#update-blok_bh').val(rowData.blok)
                    $('#update-qc').val(rowData.qc)


                    $('#update-brdtgl').val(rowData.bt_tph)
                    $('#update-brdjln').val(rowData.bt_jalan)
                    $('#update-brdbin').val(rowData.bt_bin)
                    $('#update-jumkrng').val(rowData.jum_karung)
                    $('#update-buahtgl').val(rowData.buah_tinggal)
                    $('#update-restan').val(rowData.restan_unreported)



                    $('#editModalTPH').modal('show');
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
                        formData.append('id', $('#editId_buah').val());

                        var brdtgl = $('#update-brdtgl').val();
                        var brdjln = $('#update-brdjln').val();
                        var brdbin = $('#update-brdbin').val();
                        var jumkarung = $('#update-jumkrng').val();
                        var buahtgl = $('#update-buahtgl').val();
                        var restan = $('#update-restan').val();

                        if (!isNumber(brdtgl) ||
                            !isNumber(brdjln) ||
                            !isNumber(brdbin) ||
                            !isNumber(jumkarung) ||
                            !isNumber(buahtgl) ||
                            !isNumber(restan)
                        ) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Masukan Error',
                                text: 'Hanya bisa di masukan angka Saja!'
                            });
                            return;
                        }

                        // console.log(brdtgl);
                        // Send the AJAX request
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("updatesidakTPhnew") }}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                // console.log(response);
                                // Close the modal
                                $('#editModalTPH').modal('hide');
                                // console.log(formData);

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

                function deleteRowBuah(id) {
                    // Save the selected row index
                    selectedRowIndex = id;

                    // Get the selected row data
                    var rowData = sidakTPhNEw.row(id).data();
                    var rowId = rowData.id;

                    // Show the delete modal
                    $('#deleteModalancak').modal('show');

                    // Handle delete confirmation
                    $('#confirmDeleteBtn').off('click').on('click', function() {
                        // Create a form data object
                        var formData = new FormData();
                        formData.append('delete_id', rowId);

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
                }


                // end new sidak 
            }


        });
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

    function goBack() {
        // Save the selected tab to local storage
        localStorage.setItem('selectedTab', 'nav-data-tab');

        // Redirect to the target page
        window.location.href = "https://qc-apps.srs-ssms.com/dashboardtph";
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

    $("#showFindingYear").click(function() {

        getMapsTph();
    });

    function getMapsTph() {


        var _token = $('input[name="_token"]').val();
        var est = $("#est").val();
        var afd = $("#afd").val();
        var date = $("#inputDate").val();

        if (blokLayer) {
            map.removeLayer(blokLayer);
        }
        if (temuanLayer) {
            map.removeLayer(temuanLayer);
        }
        if (legendContainer) {
            legendContainer.remove(map);
        }
        $.ajax({
            url: "{{ route('getMapsTph') }}",
            method: "get",
            data: {
                est: est,
                afd: afd,
                date: date,
                _token: _token
            },
            success: function(result) {
                var plot = JSON.parse(result);

                const plotResult = Object.entries(plot['plot']);
                const markerResult = Object.entries(plot['marker']);
                const blokResult = Object.entries(plot['blok']);
                var imgArray = Object.entries(plot['img']);
                const plotarrow = Object.entries(plot['plotarrow']);
                $('#foto_temuan').empty();

                // Add the header and horizontal rule
                $('#foto_temuan').append('<h4 class="text-center mt-2" style="font-weight: bold">FOTO TEMUAN 2</h4>');
                $('#foto_temuan').append('<hr>');

                // Create the row div
                const rowDiv = $('<div>').addClass('row');
                $('#foto_temuan').append(rowDiv);

                // Iterate over the imgArray and populate the div with the images
                imgArray.forEach(function(item) {
                    const foto = item[1]['foto'];
                    const title = item[1]['title'];
                    const file = 'https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/' + foto;
                    const file_headers = $.ajax({
                        url: file,
                        type: 'HEAD',
                        async: false
                    }).done(function() {
                        return true;
                    }).fail(function() {
                        return false;
                    });

                    if (file_headers !== false) {
                        // Create the column div
                        const colDiv = $('<div>').addClass('col-3');
                        rowDiv.append(colDiv);

                        // Add the image, hidden input, and paragraph
                        colDiv.append($('<img>').attr('src', 'https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/' + foto).addClass('img-fluid popup_image'));
                        colDiv.append($('<input>').attr('type', 'hidden').val(title).attr('id', 'titleImg'));
                        colDiv.append($('<p>').addClass('text-center mt-3').css('font-weight', 'bold').text(title));
                    }
                });








                drawBlok(blokResult)

                drawTemuan(markerResult);
                drawLegend(markerResult)

                drawArrow(plotarrow)
            },
            error: function(xhr, status, error) {
                console.log("An error occurred:", error);
            }
        });
    }

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


            var popupContent = `<strong>Jam Sidak: </strong>${markerResult[i][1]['jam']}<br/>`;
            popupContent += `<strong>Nomor TPH: </strong>${markerResult[i][1]['notph']}<br/>`;
            popupContent += `<strong>Afdeling: </strong>${markerResult[i][1]['afd']}<br/>`;
            popupContent += `<strong>Blok: </strong>${markerResult[i][1]['blok']}<br/>`;
            popupContent += `<strong>Brondol_tinggal: </strong>${markerResult[i][1]['brondol_tinggal']}<br/>`;
            popupContent += `<strong>Jumlah Karung: </strong>${markerResult[i][1]['jum_karung']}<br/>`;
            popupContent += `<strong>Buah Tinggal: </strong>${markerResult[i][1]['buah_tinggal']}<br/>`;
            popupContent += `<strong>Restan Unreported: </strong>${markerResult[i][1]['restan_unreported']}<br/>`;

            if (markerResult[i][1]['foto_temuan1']) {
                popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/${markerResult[i][1]['foto_temuan1']}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${markerResult[i][1]['komentar1']}')"><br/>`;
            }
            if (markerResult[i][1]['foto_temuan2']) {
                popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/${markerResult[i][1]['foto_temuan2']}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${markerResult[i][1]['komentar2']}')"><br/>`;
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

    var legendContainer = null; // Declare the variable outside the function

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

            var totalItemsCount = markerResult.length;
            // div.innerHTML += '<div class="legend-item">Total Sidak TPH: ' + totalItemsCount + '</div>'; // Added the legend item for total items count

            div.innerHTML += '<div class="legend-item"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" class="legend-icon"> Temuan (' + temuanCount + ')</div>';

            return div;
        };

        legendContainer.addTo(map);
    }

    function drawArrow(plotarrow) {
        console.log(plotarrow);
        const latLngArray = plotarrow.map((item) => {
            const latLngString = item[1].latln;
            const coordinates = latLngString.match(/\[(.*?)\]/g);
            if (coordinates) {
                return coordinates.map((coord) => {
                    const [longitude, latitude] = coord
                        .replace('[', '')
                        .replace(']', '')
                        .split(',')
                        .map(parseFloat);
                    return [latitude, longitude]; // Reversed to follow [lat, lon] format
                });
            }
            return [];
        });

        latLngArray.forEach((coordinates) => {
            for (let i = 0; i < coordinates.length - 1; i++) {
                const startLatLng = coordinates[i];
                const endLatLng = coordinates[i + 1];

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
        });
    }
</script>