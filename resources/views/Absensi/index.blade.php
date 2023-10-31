@include('layout/header')

<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Absensi</a>
                        <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Foto Bukti</a>

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
                        <div class="mt-3 mb-2 ml-3 mr-3">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div style=" display: flex; justify-content: flex-start;padding-bottom:20px">
                                        <button class="btn btn-primary" id="pdfdownload">Download PDF</button>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                                        {{ csrf_field() }}
                                        <select name="regional" id="regional" style="height:37px;width:auto">
                                            <option value="1">Regional 1</option>
                                            <option value="2">Regional 2</option>
                                            <option value="3">Regional 3</option>
                                            <option value="4">Regional 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                                        {{ csrf_field() }}
                                        <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputDateMonth" id="inputDateMonth">
                                    </div>
                                </div>

                            </div>
                            <style>
                                .table-container {
                                    max-height: auto;
                                    overflow-y: auto;
                                }

                                .large-text {
                                    font-size: 15px;
                                }


                                td:first-child {
                                    min-width: 150px;
                                }

                                td:nth-child(2) {
                                    min-width: 5px;
                                }

                                .label-blok {
                                    background-color: transparent;
                                    /* Set the background color to transparent */
                                    color: white;
                                    /* Set the text color to white */
                                    border: none;
                                    /* Remove any border */
                                    font-size: 12px;
                                    /* Adjust the font size as needed */
                                    text-align: center;
                                    width: auto;
                                    padding: 0;
                                    /* Adjust padding as needed */
                                }
                            </style>


                            <div class="table-container">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" colspan="35" style="text-align: center;">ABSENSI KEHADIRAN QC</th>

                                        </tr>
                                        <tr>
                                            <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">NAMA</th>

                                            <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">PAYROLL</th>
                                        </tr>
                                        <tr>
                                            <th scope="col" colspan="31" style="text-align: center;" id="header_month">{{$header_month}}</th>

                                        </tr>
                                        <tr id="dates-container">
                                            <!-- <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">Total</th> -->
                                        </tr>


                                    </thead>

                                    <tbody id="data">

                                    </tbody>
                                </table>
                            </div>


                        </div>

                        <div class="mt-3 mb-2 ml-3 mr-3">


                            <div class="row">
                                <div class="col-sm-4">
                                    <select name="userdata" id="userdata" style="height:37px;width:auto">
                                        @foreach ($useroption as $item)
                                        <option value="{{$item['user_id']}}"> {{$item['nama_lengkap']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">

                                    <div style=" display: flex; justify-content: center;padding-bottom:20px;">
                                        <p style="text-align: center;">Tracking User QC Map</p style="text-align: center;">
                                    </div>


                                </div>
                                <div class="col-sm-4">
                                    <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                                        {{ csrf_field() }}
                                        <input class="form-control" value="{{ date('Y-m') }}" type="date" name="getDate" id="getDate" style="width: 200px;height:auto">
                                    </div>
                                </div>

                            </div>
                            <div id="map" style="height: 540px; z-index: 1;"></div>



                        </div>
                    </div>

                    <!-- tab 2  -->
                    <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
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

                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <select class="form-control" id="tahunafd">

                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="btnShow">Show</button>

                            </div>
                        </div>
                    </div>




                </div>
            </div>
    </section>
</div>


@include('layout/footer')
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
<script>
    $(document).ready(function() {
        var lokasiKerja = "{{ session('lok') }}";

        // console.log(lokasiKerja);
        if (lokasiKerja == 'Regional II' || lokasiKerja == 'Regional 2') {
            $('#regional').val('2');
            $('#afdreg').val('2');

        } else if (lokasiKerja == 'Regional III' || lokasiKerja == 'Regional 3') {
            $('#regional').val('3');
            $('#afdreg').val('3');
        } else if (lokasiKerja == 'Regional IV' || lokasiKerja == 'Regional 4') {
            $('#regional').val('4');
            $('#afdreg').val('4');
        } else if (lokasiKerja == 'Regional IV' || lokasiKerja == 'Regional 4') {
            $('#regional').val('1');
            $('#afdreg').val('1');
        }

        var reg = document.getElementById('regional');
        var tahun = document.getElementById('inputDateMonth');

        // Function to handle the change event for both elements
        function handleFiltersChange() {
            // Get the selected values
            var selectedRegional = reg.value;
            var selectedDateMonth = tahun.value;

            // Perform your AJAX request here with the selected values
            // Example:
            $('#data').empty()
            $.ajax({
                type: 'get',
                url: "{{ route('absensidata') }}",
                data: {
                    regional: selectedRegional,
                    dateMonth: selectedDateMonth
                },
                success: function(data) {
                    // Handle the response data
                    var header_month = data.header_month; // Access the header_month directly
                    var dates = data.dates;
                    var JumlahBulan = data.JumlahBulan;
                    var data_absensi = data.data_absensi;

                    var header_table = document.getElementById('header_month');
                    header_table.textContent = header_month;

                    // Set the colspan attribute based on the 'dates' value
                    header_table.setAttribute('colspan', JumlahBulan);


                    // Get the table row where you want to add the <th> elements
                    var datesContainer = document.getElementById("dates-container");

                    let addPlus = parseFloat(JumlahBulan) + 1;
                    datesContainer.innerHTML = '';

                    // Loop from 1 to 31 and create <th> elements for each number
                    for (var i = 1; i <= addPlus; i++) {
                        var th = document.createElement("th");
                        th.setAttribute("scope", "col");
                        if (i < addPlus) {
                            th.textContent = i;
                        } else {
                            th.textContent = "Total";
                        }
                        datesContainer.appendChild(th);
                    }



                    var Datatables = document.getElementById('data');
                    var DrawTables = data_absensi

                    for (var key in DrawTables) {
                        if (DrawTables.hasOwnProperty(key)) {
                            var element = DrawTables[key];
                            var tr = document.createElement('tr');

                            for (var prop in element) {
                                if (element.hasOwnProperty(prop) && prop !== "id") {
                                    var td = document.createElement('td');
                                    td.innerText = element[prop];
                                    td.classList.add("large-text", "vertical-center"); // Add classes for styling

                                    // Check if it's the first cell, and set its width to 500px
                                    if (prop === Object.keys(element)[0]) {
                                        td.style.width = '500px';
                                    }

                                    // Check if the text is a string and contains "minggu", then change the background color to red
                                    if (typeof element[prop] === 'string' && element[prop].includes("minggu")) {
                                        td.style.backgroundColor = 'red';
                                        td.innerText = '';
                                    }

                                    if (typeof element[prop] === 'string' && element[prop].includes("CT")) {
                                        td.style.color = 'Blue';
                                        // td.innerText = '';
                                    }

                                    tr.appendChild(td);
                                }
                            }
                            Datatables.appendChild(tr);
                        }
                    }

                },

            });

            // You can put your AJAX request code here
        }

        // Add event listeners to trigger the function when the values change
        reg.addEventListener('change', handleFiltersChange);
        tahun.addEventListener('change', handleFiltersChange);

        // Trigger the function when the document is ready
        handleFiltersChange();

        sendAjaxRequest();

        $('#pdfdownload').click(function() {
            var _token = $('input[name="_token"]').val();
            var regional = $("#regional").val();
            var date = $("#inputDateMonth").val();

            // Construct the URL
            var url = "{{ route('absensipdf') }}?regional=" + regional + "&date=" + date + "&_token=" + _token;

            // Open the URL in a new tab
            window.open(url, '_blank');
        });



    });


    var map = L.map('map').setView([-2.2745234, 111.61404248], 11);

    // Define the "Google Satellite" tile layer
    var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    // Add "Google Satellite" as the only base map
    googleSatellite.addTo(map);

    map.addControl(new L.Control.Fullscreen());

    // Rest of your code, including the marker-related functions
    var markersLayer = L.layerGroup().addTo(map);
    var markerBlok = L.layerGroup().addTo(map);



    function drawusermaps(datauser) {
        markersLayer.clearLayers();

        // console.log(datauser);
        var coordinatesArray = [];

        datauser.features.forEach(function(feature) {
            var coordinates = feature.geometry.coordinates;
            var properties = feature.properties;
            var images = feature.images;

            // Create a marker at the specified coordinates
            var marker = L.marker(coordinates).addTo(markersLayer);

            // Create a popup to display information
            var popupContent = `
            <b>Nama User:</b> ${properties.nama_user}<br>
            <b>Waktu Absensi:</b> ${properties.waktu_absensi}<br>
            <b>Jenis Kerja:</b> ${properties.jenis_kerja}<br>
            <img src="${images.img}" alt="Image">
        `;

            marker.bindPopup(popupContent);

            // Add the coordinates to the array for fitting bounds
            coordinatesArray.push(coordinates);
        });

        // Fit the map view to the bounds of the markers
        // Fit the map view to the bounds of the markers with a maximum zoom level
        if (coordinatesArray.length > 0) {
            map.fitBounds(coordinatesArray, {
                maxZoom: 18
            }); // Adjust the maxZoom value as needed
        }

    }

    function drawMap(new_blok) {
        markerBlok.clearLayers();
        var bounds = new L.LatLngBounds();
        // console.log(new_blok);
        // Iterate through the keys (Rangda, Sulung, etc.) in new_blok
        for (var key in new_blok) {
            if (new_blok.hasOwnProperty(key)) {
                var coordinates = [];
                var points = new_blok[key];

                // Extract the latlng values and convert them to an array of coordinates
                for (var i = 0; i < points.length; i++) {
                    var latlng = points[i].lating.split("$");
                    var lat = parseFloat(latlng[0]);
                    var lng = parseFloat(latlng[1]);
                    coordinates.push([lat, lng]);
                }

                // Create a polygon for each key and add it to the map
                var polygon = L.polygon(coordinates).addTo(markerBlok);
                // var polygon = L.polygon(coordinates, polygonStyle).addTo(markerBlok);



                // Extend the bounds to include the new polygon
                bounds.extend(polygon.getBounds().getNorthEast());
                bounds.extend(polygon.getBounds().getSouthWest());

                var polygonCenter = polygon.getBounds().getCenter();
                // Get other properties from your data
                var afd_nama = key

                var popupContent = `<div class="custom-popup"><strong>Nama Estate: </strong>${afd_nama}<br/>`;


                var label = L.marker(polygonCenter, {
                    icon: L.divIcon({
                        className: 'label-blok',
                        html: afd_nama,
                        iconSize: [50, 10],
                        iconColor: 'white' // Set iconColor to 'white'
                    })
                }).addTo(markerBlok);


                label.bindPopup(popupContent);
            }
        }

        // Fit the map to the bounds of all polygons
        // map.fitBounds(bounds);
    }




    // Call the function with your GeoPlot data

    function sendAjaxRequest() {
        var _token = $('input[name="_token"]').val();
        var user_id = $("#userdata").val();
        var getDate = $("#getDate").val();

        // Check if getDate is empty, and if so, set it to the current date
        if (!getDate) {
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = String(currentDate.getMonth() + 1).padStart(2, '0');
            var day = String(currentDate.getDate()).padStart(2, '0');
            getDate = year + '-' + month + '-' + day;
            $("#getDate").val(getDate);
        }

        $.ajax({
            url: "{{ route('absenmaps') }}",
            method: "GET",
            data: {
                userid: user_id,
                date: getDate,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {
                var datauser = result.datauser;
                var new_blok = result.GeoPlot;

                drawusermaps(datauser)
                drawMap(new_blok)
                // console.log(datauser);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle errors here.
            }
        });
    }

    // $("#userdata").on('change', sendAjaxRequest);
    var userD = document.getElementById('userdata');
    var getDate = document.getElementById('getDate');
    userD.addEventListener('change', sendAjaxRequest);
    getDate.addEventListener('change', sendAjaxRequest);
</script>