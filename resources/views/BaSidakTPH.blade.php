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
            <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
        </div>

        <!-- end animasi -->
    </div>
    <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3">
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
        <div id="lottie-animation" style="width: 200px; height: 200px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
    </div>
    <div class="d-flex justify-content-center mt-3 mb-4 ml-3 mr-3 border border-dark">
        <div class="Wraping">
            <h1 class="text-center">Tabel Sidak TPH</h1>
            <div class="table-responsive">
                <table class="table table-bordered" id="mutu_ancak">
                    <thead class="table-white">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Estate</th>
                            <th scope="col">Afdeling</th>
                            <th scope="col">Blok</th>
                            <th scope="col">QC</th>
                            <th scope="col">No TPH</th>
                            <th scope="col">BT TPH</th>
                            <th scope="col">BT Jalan</th>
                            <th scope="col">BT Bin</th>
                            <th scope="col">Jum Karung</th>
                            <th scope="col">Buah Tinggal</th>
                            <th scope="col">Restan Unreported</th>
                            <th scope="col">TPH Semak</th>


                            @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep')
                            <th scope="col">Aksi</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody id="tab1">
                        <!-- Table rows will be dynamically added using JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="pagination"></div>
        </div>
    </div>
    >

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="update-form">
                        @csrf
                        <input type="hidden" name="id" id="update-id">

                        <div class="form-group">
                            <label for="update-est">Estate</label>
                            <input type="text" class="form-control" name="Estate" id="update-est">
                        </div>
                        <div class="form-group">
                            <label for="update-afd">Afdeling</label>
                            <input type="text" class="form-control" name="Afdeling" id="update-afd">
                        </div>
                        <div class="form-group">
                            <label for="update-qc">QC</label>
                            <input type="text" class="form-control" name="QC" id="update-qc">
                        </div>
                        <div class="form-group">
                            <label for="update-notph">No TPH</label>
                            <input type="text" class="form-control" name="no_tph" id="update-notph">
                        </div>

                        <div class="form-group">
                            <label for="update-bttph">BT TPH</label>
                            <input type="text" class="form-control" name="bttph" id="update-bttph">
                        </div>
                        <div class="form-group">
                            <label for="update-btjalan">BT Jalan</label>
                            <input type="text" class="form-control" name="btjalan" id="update-btjalan">
                        </div>
                        <div class="form-group">
                            <label for="update-btbin">BT Bin</label>
                            <input type="text" class="form-control" name="btbin" id="update-btbin">
                        </div>
                        <div class="form-group">
                            <label for="update-jumkrng">Jum Karung</label>
                            <input type="text" class="form-control" name="jumkrng" id="update-jumkrng">
                        </div>
                        <div class="form-group">
                            <label for="update-buahtgl">Buah Tinggal</label>
                            <input type="text" class="form-control" name="buahtgl" id="update-buahtgl">
                        </div>
                        <div class="form-group">
                            <label for="update-restanunr">Restan unreported</label>
                            <input type="text" class="form-control" name="restanunr" id="update-restanunr">
                        </div>
                        <div class="form-group">
                            <label for="update-tphsemak">TPH Semak</label>
                            <input type="text" class="form-control" name="tphsemak" id="update-tphsemak">
                        </div>
                        <!-- Add more form fields for other properties -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-changes-button">Save changes</button>

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
        $('#tab1').empty()

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
                var sidak_tph = Object.entries(parseResult['sidak_tph']);
                var data = sidak_tph.find(([key, value]) => key === 'data')[1];
                var lastPage = sidak_tph.find(([key, value]) => key === 'last_page')[1];
                var currentPage = sidak_tph.find(([key, value]) => key === 'current_page')[1];


                var tableBody = document.getElementById('tab1');
                tableBody.innerHTML = ''; // Clear the table body

                function createAksiButtons(row, item) {
                    const td = document.createElement('td');
                    td.style.display = 'inline-flex';

                    if (currentUserName === 'Askep' || currentUserName === 'Manager') {
                        // Create the update button
                        const updateBtn = document.createElement('button');
                        updateBtn.className = 'btn btn-success mr-2';
                        updateBtn.innerHTML = '<i class="nav-icon fa-solid fa-edit"></i>';
                        updateBtn.onclick = function() {
                            showUpdateModal(item);
                        };
                        td.appendChild(updateBtn);

                        // Create the delete button
                        const deleteBtn = document.createElement('button');
                        deleteBtn.id = 'deleteBtn-' + item.id;
                        deleteBtn.className = 'btn btn-danger';
                        deleteBtn.innerHTML = '<i class="nav-icon fa-solid fa-trash"></i>';
                        deleteBtn.onclick = function() {
                            // Call your delete function with the id
                            deleteFunction(item.id);
                        };
                        td.appendChild(deleteBtn);
                    }

                    row.appendChild(td);
                }




                data.forEach(function(item, index) {
                    var row = tableBody.insertRow();

                    var cellNo = row.insertCell(0);
                    cellNo.innerText = index + 1;

                    var cellEstate = row.insertCell(1);
                    cellEstate.innerText = item.est;

                    // Add more cells as needed

                    // Example: Afdeling
                    var cellAfdeling = row.insertCell(2);
                    cellAfdeling.innerText = item.afd;

                    var blok = row.insertCell(3);
                    blok.innerText = item.blok;

                    var qc = row.insertCell(4);
                    qc.innerText = item.qc;
                    var no_tph = row.insertCell(5);
                    no_tph.innerText = item.no_tph;
                    var bt_tph = row.insertCell(6);
                    bt_tph.innerText = item.bt_tph;
                    var bt_jalan = row.insertCell(7);
                    bt_jalan.innerText = item.bt_jalan;
                    var bt_bin = row.insertCell(8);
                    bt_bin.innerText = item.bt_bin;
                    var jum_karung = row.insertCell(9);
                    jum_karung.innerText = item.jum_karung;
                    var buah_tinggal = row.insertCell(10);
                    buah_tinggal.innerText = item.buah_tinggal;
                    var restan_unreported = row.insertCell(11);
                    restan_unreported.innerText = item.restan_unreported;
                    var tph_semak = row.insertCell(12);
                    tph_semak.innerText = item.tph_semak;
                    // Continue adding cells for each column in your table
                    if (currentUserName === 'Askep' || currentUserName === 'Manager') {
                        createAksiButtons(row, item);
                    }

                });


                // console.log(sidak_tph);
                var paginationDiv = document.getElementById('pagination');
                paginationDiv.innerHTML = ''; // Clear the pagination div

                for (var i = 1; i <= lastPage; i++) {
                    var btn = document.createElement('button');
                    btn.innerText = i;
                    btn.classList.add('pagination-button');
                    if (i === currentPage) {
                        btn.classList.add('current-page');
                    }

                    btn.addEventListener('click', function() {
                        var newPage = parseInt(this.innerText);
                        lottieAnimation.play(); // Play the Lottie animation
                        dashboardFindingYear(newPage);
                    });

                    paginationDiv.appendChild(btn);
                }

            }


        });
    }



    function showUpdateModal(item) {
        // Populate the form with the item data
        document.getElementById('update-id').value = item.id;
        document.getElementById('update-est').value = item.est;
        document.getElementById('update-afd').value = item.afd;
        document.getElementById('update-qc').value = item.qc;
        document.getElementById('update-notph').value = item.no_tph;

        document.getElementById('update-bttph').value = item.bt_tph;
        document.getElementById('update-btjalan').value = item.bt_jalan;
        document.getElementById('update-btbin').value = item.bt_bin;
        document.getElementById('update-jumkrng').value = item.jum_karung;
        document.getElementById('update-buahtgl').value = item.buah_tinggal;
        document.getElementById('update-restanunr').value = item.restan_unreported;
        document.getElementById('update-tphsemak').value = item.tph_semak;
        // Show the modal
        $('#updateModal').modal('show');
    }

    async function updateFunction() {
        // Collect the form data
        const form = document.getElementById('update-form');
        const formData = new FormData(form);

        // Send the form data to your server
        try {
            const response = await fetch('/updateBASidakTPH', {
                method: 'POST',
                body: formData,
            });

            if (response.ok) {
                // The update was successful, close the modal and refresh the data
                $('#updateModal').modal('hide');
                // Refresh your data by calling the function that loads your table data
                dashboardFindingYear();

                // Show a success message
                Swal.fire(
                    'Updated!',
                    'Data Telah di update',
                    'success'
                );
            } else {
                // Show an error message
                Swal.fire(
                    'Error!',
                    'Error updating data: ' + response.statusText,
                    'error'
                );
            }
        } catch (error) {
            // Handle network errors
            Swal.fire(
                'Error!',
                'Network error: ' + error,
                'error'
            );
        }
    }


    async function deleteFunction(id) {
        // Show a stylish confirmation alert
        Swal.fire({
            title: 'Anda yakin?',
            text: 'Anda yakin ingin mendelete data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                // Create a FormData object to send the ID
                const formData = new FormData();
                formData.append('id', id);

                // Add the CSRF token to the FormData object
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                formData.append('_token', csrfToken);

                // Send the request to your server
                try {
                    const response = await fetch('/deleteBAsidakTPH', {
                        method: 'POST',
                        body: formData,
                    });

                    if (response.ok) {
                        // The delete was successful, refresh the data
                        Swal.fire(
                            'Deleted!',
                            'Data telah terhapus!.',
                            'success'
                        );
                        dashboardFindingYear();
                    } else {
                        // Show an error message or handle the error
                        Swal.fire(
                            'Error!',
                            'Error deleting data: ' + response.statusText,
                            'error'
                        );
                    }
                } catch (error) {
                    // Handle network errors
                    Swal.fire(
                        'Error!',
                        'Network error: ' + error,
                        'error'
                    );
                }
            }
        });
    }


    function goBack() {
        // Save the selected tab to local storage
        localStorage.setItem('selectedTab', 'nav-data-tab');

        // Redirect to the target page
        window.location.href = "https://qc-apps.srs-ssms.com/dashboardtph";
    }
</script>