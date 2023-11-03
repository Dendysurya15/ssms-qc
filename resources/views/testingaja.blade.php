@include('layout/header')


<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="mt-3 mb-2 ml-3 mr-3">
                <div class="row">
                    <div class="col-sm-4">
                        <div style=" display: flex; justify-content: flex-left;padding-bottom:20px;">
                            {{ csrf_field() }}
                            <select name="regional" id="regional" style="height:37px;width:auto" onchange="fillestate(this.value)">
                                @foreach ($reg as $item )

                                <option value="{{$item['id']}}">{{$item['nama']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div style=" display: flex; justify-content:center;padding-bottom:20px;">
                            <select name="estate" id="estate" style="height:37px;width:auto" onchange="filafd(this.value)">

                            </select>
                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                            <select name="afdling" id="afdling" style="height:37px;width:auto">

                            </select>

                        </div>
                    </div>

                    <button class="buttton button-primary" type="submit" id="showres">Show</button>

                </div>

            </div>
            <div id="map" style="height: 540px; z-index: 1;"></div>



        </div>
    </section>
</div>
<style>
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

@include('layout/footer')
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
<script src="{{asset('leaflet.browser.print/dist/leaflet.browser.print.min.js')}}"></script>
<link rel="stylesheet" href="{{asset('Leaflet.BigImage/dist/Leaflet.BigImage.min.css')}}">
<script src="{{asset('Leaflet.BigImage/dist/Leaflet.BigImage.min.js')}}"></script>
<script src="{{asset('leaflet-easyPrint/dist/bundle.js')}}"></script>
<!-- <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}"> -->
<script>
    var estate = <?php echo json_encode($estate); ?>;
    var afd = <?php echo json_encode($afd); ?>;

    var estatediv = document.getElementById('estate')
    var afddiv = document.getElementById('afdling')



    function fillestate(regvalue) {
        // Clear existing options

        // console.log(selectedWilIdx);
        estatediv.innerHTML = '';


        // Filter the opt_est array based on the selectedWilIdx
        var filteredEstates = estate.filter(function(estate) {
            return estate.regid == regvalue;
        });
        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate.est;
            optionElement.textContent = estate.est;
            estatediv.appendChild(optionElement);
        });



        estatediv.dispatchEvent(new Event('change'));
    }

    function filafd(afdValue) {
        // Clear existing options

        // console.log(selectedWilIdx);
        afddiv.innerHTML = '';

        // Filter the opt_est array based on the selectedWilIdx
        var filteredEstates = afd.filter(function(estate) {
            return estate.est == afdValue;
        });
        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate.afdid
            optionElement.textContent = estate.nama;
            afddiv.appendChild(optionElement);
        });

        // console.log(filteredEstates);

        afddiv.dispatchEvent(new Event('change'));
    }

    var map = L.map('map').setView([-2.2745234, 111.61404248], 11);

    // Define the "Google Satellite" tile layer
    var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    var googleStreets = L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    // Create an object to hold the base layers
    var baseMaps = {
        "Satellite": googleSatellite,
        "Streets": googleStreets
    };

    // Add a control to switch between base layers
    // L.control.layers(baseMaps).addTo(map);

    // Initially, set the satellite map as the default
    googleSatellite.addTo(map);


    var markersLayer = L.layerGroup().addTo(map);

    function drawMaps(geojsonFeatureCollection) {
        markersLayer.clearLayers();
        geojsonLayer = L.geoJSON(geojsonFeatureCollection, {
            style: {
                color: 'blue',
                fillColor: 'grey',
                fillOpacity: 0.4,
            },
            onEachFeature: function(feature, layer) {
                // Access the properties from the GeoJSON feature
                var nama = feature.properties.nama;


                // Create a text label for the polygon
                var label = L.marker(layer.getBounds().getCenter(), {
                    icon: L.divIcon({
                        className: 'label-blok', // Add your custom CSS class for styling
                        html: '<div style="color: #000;">' + nama + '</div>',
                        iconSize: [50, 10],
                        iconColor: 'white'
                    })
                });

                // Create a popup and bind it to the feature
                var popupText = "Nama: " + nama;
                layer.bindPopup(popupText);

                // Add the label to the map
                label.addTo(markersLayer);

                // Add the polygon to the map
                layer.addTo(markersLayer);
            }
        });

        map.fitBounds(geojsonLayer.getBounds());
    }

    var printer = L.easyPrint({
        tileLayer: googleSatellite,
        sizeModes: ['Current', 'A4Landscape', 'A4Portrait'],
        filename: 'myMap',
        exportOnly: true,
        hideControlContainer: true
    }).addTo(map);

    function manualPrint() {
        printer.printMap('CurrentSize', 'MyManualPrint')
    }

    $('#showres').on('click', function() {
        var estate = $('#estate').val();
        var afdling = $('#afdling').val();

        console.log(estate);
        console.log(afdling);



        // Fetch GeoJSON data based on the selected regional value
        $.ajax({
            url: 'https://srs-ssms.com/mapdata/geomaps.php?geojson=blok_est_afd&est=' + estate + '&afd=' + afdling,
            method: 'GET',
            success: function(result) {
                // console.log(url);
                // Assuming result is your GeoJSON data
                var geojsonFeatureCollection = result;
                console.log(geojsonFeatureCollection);
                drawMaps(geojsonFeatureCollection);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle errors here.
            },
        });
    });
</script>