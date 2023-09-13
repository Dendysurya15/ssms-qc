@include('layout/header')


<style>
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

    .custom_background {
        background: linear-gradient(to right, #ff5733, #ff9900);
    }
</style>


<div class="content-wrapper">
    <div class="card table_wrapper">
        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <h2>Pemeriksaan Emplashment</h2>
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
                    <div class="date">
                        {{ csrf_field() }}
                        <input type="hidden" name="est" id="est" value="{{$est}}">
                        <select class="form-control" name="date" id="inputDate">
                            <option value="" disabled selected hidden>Pilih tanggal</option>
                            @foreach($date as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="ml-2 btn btn-primary mb-2" id="empData" onclick="showData()">Show</button>
                    </div>
                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}</div>
                    <div class="afd">Tahun/Bulan : <span id="selectedDate">{{ $tanggal }}</span></div>
                    <!-- <button id="back-to-data-btn" class="btn btn-primary" onclick="downloadpdf()">Download PDF</button> -->

                    <form action="{{ route('downloadPDF') }}" method="POST" class="form-inline" style="display: inline;" target="_blank" id="downloadPDF">
                        {{ csrf_field() }}
                        <input type="hidden" name="estPDF" id="estPDF" value="{{$est}}">
                        <input type="hidden" name="tglpdfnew" id="tglpdfnew">
                        <button type="submit" class="btn btn-primary" id="downloadpdf" disabled>
                            Download PDF
                        </button>
                    </form>

                    <form action="{{ route('downloadBAemp') }}" method="POST" class="form-inline" style="display: inline;" target="_blank" id="download-form">
                        {{ csrf_field() }}
                        <input type="hidden" name="estBA" id="estpdf" value="{{$est}}">
                        <input type="hidden" name="tglPDF" id="tglPDF">
                        <button type="submit" class="btn btn-primary" id="downloadba" disabled>
                            Download BA
                        </button>
                    </form>
                    <button id="back-to-data-btn" class="btn btn-primary" onclick="goBack()">Back to Home</button>
                </div>

            </div>
        </div>
        <br>


        <div class="card table_wrapper">
            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark  custom_background">
                <h2>Temuan ESTATE</h2>
            </div>


            <style>
                /* Add this CSS to create the hover effect */
                .card-title,
                .card-text {
                    opacity: 0;
                    /* Set the initial opacity to 0 to hide the title and text */
                    transition: opacity 0.3s ease-in-out;
                    /* Add a smooth transition effect */
                }

                .card:hover .card-title,
                .card:hover .card-text {
                    opacity: 1;
                    /* Set the opacity to 1 on hover to show the title and text */
                }
            </style>


            <div class="text-center mt-3 mb-2 border border-dark" id="perumahan">
            </div>

            <div class="text-center mt-3 mb-2 border border-dark" id="landscape">
            </div>

            <div class="text-center mt-3 mb-2 border border-dark" id="lingkungan">
            </div>
        </div>


        <div class="card table_wrapper">
            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark custom_background">
                <h2>Temuan AFDELING</h2>
            </div>
            <div class="text-center mt-3 mb-2 border border-dark" id="afd_rmh">
            </div>

            <div class="text-center mt-3 mb-2 border border-dark" id="afd_landscape">
            </div>
            <div class="text-center mt-3 mb-2 border border-dark" id="afd_lingkungan">
            </div>
        </div>
        <input type="file" id="fileInput" style="display: none;">
        <style>
            .btn-container {
                display: flex;
                gap: 10px;
                /* Adjust the spacing between buttons as needed */
            }
        </style>
    </div>
    @include('layout/footer')

    <!-- Using a CDN link -->


    <script>
        function goBack() {
            // Save the selected tab to local storage
            localStorage.setItem('selectedTab', 'nav-data-tab');

            // Redirect to the target page
            window.location.href = "https://qc-apps.srs-ssms.com/dashboard_perum";
        }

        $('#empData').click(function() {
            getTemuan();

            Swal.fire({
                title: 'Loading',
                html: '<span class="loading-text">Mohon Tunggu...</span>',
                allowOutsideClick: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });
        });



        function rumahupdate(Perumahan) {
            const container = document.getElementById("perumahan");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/perumahan/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (Perumahan.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Perumahan</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                Perumahan.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_rmh;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_rmh;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_rmh}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_rmh}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_rmh}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);

                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'perumahan';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'perumahan'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Perumahan not found.";
                container.appendChild(noDataMessage);
            }
        }

        function landscapeupdate(Landscape) {
            const container = document.getElementById("landscape");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/landscape/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (Landscape.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Landscape</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                Landscape.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_ls;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_ls;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_ls}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_ls}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_ls}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);
                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'landscape';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'landscape'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Landscape not found.";
                container.appendChild(noDataMessage);
            }
        }

        function lingkunganupdate(lingkungan) {
            const container = document.getElementById("lingkungan");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/lingkungan/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (lingkungan.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Lingkungan</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                lingkungan.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_ll;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_ll;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_ll}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_ll}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_ll}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);

                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'lingkungan';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'lingkungan'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Lingkungan not found.";
                container.appendChild(noDataMessage);
            }
        }


        function afd_rmh(rumah_afd) {
            const container = document.getElementById("afd_rmh");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/perumahan/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (rumah_afd.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Perumahan</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                rumah_afd.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_rmh;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_rmh;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_rmh}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_rmh}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_rmh}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);

                    // Create a container div for the buttons
                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'perumahan';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'perumahan'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });


                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Perumahan not found.";
                container.appendChild(noDataMessage);
            }
        }

        function afd_landscape(lcp_afd) {
            const container = document.getElementById("afd_landscape");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/landscape/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (lcp_afd.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Landscape</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                lcp_afd.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_lcp;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_lcp;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_lcp}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_lcp}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_lcp}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);

                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'landscape';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'landscape'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });

                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Landscape not found.";
                container.appendChild(noDataMessage);
            }
        }

        function afd_lingkungan(lingkungan_afd) {
            const container = document.getElementById("afd_lingkungan");
            const imageBaseUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/lingkungan/";
            const defaultImageUrl = "{{ asset('img/404img.png') }}"; // Use the asset function to get the correct URL

            // Check if there is data to display
            if (lingkungan_afd.length > 0) {
                // Create the heading
                const heading = document.createElement("div");
                heading.classList.add("text-center");
                heading.innerHTML = "<h1>Foto Temuan Lingkungan</h1>";
                container.appendChild(heading);

                // Create the row container
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row", "justify-content-center");
                container.appendChild(rowContainer);

                // Iterate through the array data
                lingkungan_afd.forEach((item) => {
                    const id = item[0];
                    const data = item[1];
                    const imageUrl = imageBaseUrl + data.foto_temuan_lk;
                    const image = new Image();
                    image.src = imageUrl;
                    image.alt = data.foto_temuan_lk;
                    image.classList.add("img-thumbnail");
                    image.setAttribute("data-toggle", "modal");
                    image.setAttribute("data-target", `#myModal${id}`);
                    image.onerror = function() {
                        // If the image fails to load, use the default image
                        this.src = defaultImageUrl;
                    };

                    const card = document.createElement("div");
                    card.classList.add("col-md-6", "col-lg-3", "mb-3");
                    card.innerHTML = `
                    <div class="card">
                    <img src="${imageUrl}" alt="${data.foto_temuan_lk}" class="img-thumbnail" data-toggle="modal" data-target="#myModal${id}">
                    <div class="card-body mt-2">
                        <h5 class="card-title text-right">Est: ${data.title}</h5>
                        <p class="card-text text-left">Temuan: ${data.komentar_temuan_lk}</p>
                        <p class="card-text text-left">Komentar: ${data.komentar_lk}</p>
                    </div>
                    </div>
                     `;
                    rowContainer.appendChild(card);

                    const buttonContainer = document.createElement("div");
                    buttonContainer.classList.add("btn-container");

                    const downloadLink = document.createElement("a");
                    downloadLink.href = "#"; // Set a placeholder link initially
                    downloadLink.innerHTML = '<i class="fas fa-download"></i> Download Image';
                    downloadLink.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(downloadLink);

                    const uploading = document.createElement("a");
                    uploading.href = "#"; // Set a placeholder link initially
                    uploading.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Image';
                    uploading.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(uploading);

                    const deletes = document.createElement("a");
                    deletes.href = "#"; // Set a placeholder link initially
                    deletes.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete The Image';
                    deletes.classList.add("btn", "btn-primary", "btn-sm");
                    buttonContainer.appendChild(deletes);

                    // Append the container to the card body
                    card.querySelector(".card-body").appendChild(buttonContainer);



                    deletes.addEventListener("click", () => {
                        // Display a confirmation dialog
                        Swal.fire({
                            title: 'Delete Confirmation',
                            text: 'Anda Yaking Ingin Menghapus Foto??',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // User confirmed deletion, proceed with the deletion logic

                                // Hardcode the item type as 'perumahan' (change as needed)
                                const itemType = 'lingkungan';

                                // Construct the delete URL
                                const deleteUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Create a FormData object to send the filename, item type, and action (delete)
                                const formData = new FormData();
                                formData.append('filename', filename); // Send the filename to be deleted
                                formData.append('itemType', itemType); // Send the item type to the PHP script for validation
                                formData.append('action', 'delete'); // Specify the action as 'delete'

                                // Send a POST request to your PHP script for deletion
                                fetch(deleteUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'Image deleted successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Delete Success',
                                                text: 'The image was deleted successfully.',
                                            });

                                            // Reload the page with cache-busting
                                            location.reload(true); // Force a hard reload (including cache)
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Delete Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Delete Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                    // Add click event to trigger download
                    downloadLink.addEventListener("click", () => {
                        // Use the image URL to construct the download URL
                        const downloadUrl = "https://srs-ssms.com/qc_inspeksi/get_qcIMG.php?image=" + encodeURIComponent(imageUrl);

                        // Open a new tab/window to initiate the download
                        window.open(downloadUrl, "_blank");
                    });

                    uploading.addEventListener("click", () => {
                        // Create a file input element
                        const fileInput = document.createElement("input");
                        fileInput.type = "file";

                        // Trigger the file input when the button is clicked
                        fileInput.click();

                        // Listen for changes in the file input
                        fileInput.addEventListener("change", (event) => {
                            const selectedFile = event.target.files[0]; // Get the selected file

                            if (selectedFile) {
                                // Get the filename from the image URL
                                const imageUrlParts = imageUrl.split('/');
                                const filename = imageUrlParts[imageUrlParts.length - 1];

                                // Hardcode the item type as 'perumahan'
                                const itemType = 'lingkungan'; // Change this to 'landscape' or 'lingkungan' as needed

                                // Construct the upload URL
                                const uploadUrl = "https://srs-ssms.com/qc_inspeksi/uploadIMG.php";

                                // Create a FormData object to send the file, item type, and action (upload)
                                const formData = new FormData();
                                formData.append('image', selectedFile, filename); // Use the selected file with the correct filename
                                formData.append('itemType', itemType); // Send the item type to the PHP script
                                formData.append('action', 'upload'); // Specify the action as 'upload'

                                // Send a POST request to your PHP script for upload
                                fetch(uploadUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(result => {
                                        if (result === 'File uploaded successfully.') {
                                            // Display a success message using SweetAlert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Success',
                                                text: 'The image was uploaded successfully.',
                                            });

                                            location.reload(true);
                                        } else {
                                            // Display an error message using SweetAlert
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Error: ' + result, // Display the error message from the server
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        // Display an error message using SweetAlert
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Error',
                                            text: 'Error: ' + error, // Display the fetch error message
                                        });
                                    });
                            }
                        });
                    });
                });
            } else {
                // If no data, show the "Perumahan not found" message
                const noDataMessage = document.createElement("p");
                noDataMessage.textContent = "Lingkungan not found.";
                container.appendChild(noDataMessage);
            }
        }


        function getTemuan() {
            var _token = $('input[name="_token"]').val();
            var estData = $("#est").val();
            var tanggal = $("#inputDate").val();
            // $perumahan.empty
            $('#perumahan').empty();
            $('#landscape').empty();
            $('#lingkungan').empty();
            $('#afd_rmh').empty();
            $('#afd_landscape').empty();
            $('#afd_lingkungan').empty();
            $.ajax({
                url: "{{ route('getTemuan') }}",
                method: "get",
                data: {
                    estData: estData,
                    tanggal: tanggal,
                    _token: _token
                },
                success: function(result) {
                    Swal.close();
                    var parseResult = JSON.parse(result);
                    var Perumahan = Object.entries(parseResult['Perumahan']);
                    var Landscape = Object.entries(parseResult['Landscape']);
                    var lingkungan = Object.entries(parseResult['lingkungan']);

                    var rumah_afd = Object.entries(parseResult['rumah_afd']);
                    var lcp_afd = Object.entries(parseResult['lcp_afd']);
                    var lingkungan_afd = Object.entries(parseResult['lingkungan_afd']);


                    // estate 
                    rumahupdate(Perumahan);
                    landscapeupdate(Landscape);
                    lingkunganupdate(lingkungan);

                    // afdeling 
                    afd_rmh(rumah_afd);
                    afd_landscape(lcp_afd);
                    afd_lingkungan(lingkungan_afd);

                },
                error: function(xhr, status, error) {
                    // Handle the error here
                    console.error('AJAX request error:', error);
                }

            });
        }

        function showData() {
            var selectedDate = document.getElementById("inputDate").value;
            if (selectedDate) {
                document.getElementById("downloadba").disabled = false;
                document.getElementById("downloadpdf").disabled = false;
                document.getElementById("tglPDF").value = selectedDate;
                document.getElementById("tglpdfnew").value = selectedDate;
            } else {
                alert("Please select a date first.");
            }
        }



        function downloadpdf() {
            // Get the selected date from the inputDate select element
            var selectedDate = $("#inputDate").val();

            // Set the value of the tglPDF hidden input field
            $("#tglPDF").val(selectedDate);

            // Submit the form
            $("#download-form").submit();


        }

        function downloadPDFpi() {
            var selectedDate = $("#inputDate").val();
            $("#tglpdfnew").val(selectedDate);

            $("#downloadPDF").submit();
        }
    </script>