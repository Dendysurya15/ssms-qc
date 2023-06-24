@include('layout/header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="http://w2ui.com/src/w2ui-1.4.2.min.css" />

<style>
    .label-bidang {
        font-size: 10pt;
        color: white;
        text-align: center;
        opacity: 0.6;
    }

    .popup_image {
        cursor: pointer;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
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
    <section class="content">
        <div class="container-fluid pt-3">
            <div class="card p-4">
                <h4 class="text-center mt-2" style="font-weight: bold">Tabel Sidak TPH - {{ $est }}
                    {{ $afd }}
                </h4>
                <h4 class="text-center mt-2" style="font-weight: bold">Tabel Sidak TPH - {{ $start }}
                    {{ $last }}
                </h4>
                <hr>
                <!--<select class="col-2 mb-3" id="dropBlok">
                    <option value="">All</option>
                    @foreach ($blok as $item)
                        <option value="{{ $item->blok }}">{{ $item->blok }}</option>
                    @endforeach
                </select>-->
                @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
                <table id="listSidakTPH" class="text-center" style="width:100%">
                    {{ csrf_field() }}
                    <thead>
                        <tr>
                            <th rowspan="3">Blok</th>
                            <th rowspan="3">No. TPH</th>
                            <th rowspan="3">Luas (Ha)</th>
                            <th colspan="3">Brondolan Tinggal</th>
                            <th rowspan="2">Karung Isi Brondolan </th>
                            <th rowspan="2">Buah Tinggal di TPH </th>
                            <th rowspan="2">Restan Tidak Dilaporkan </th>
                            <th colspan="2" rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th>Di TPH</th>
                            <th>Di JALAN</th>
                            <th>Di TPH </th>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <th>Jumlah</th>
                            <th>Jumlah </th>
                            <th>Jumlah </th>
                            <th>Janjang </th>
                            <th>Janjang </th>
                            <form id="hapusDetailSidakForm" action="{{ route('hapusDetailSidak') }}" method="POST">
                                {{ csrf_field() }}
                                <th colspan="2">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin menghapus data terpilih?')">
                                        <i class="nav-icon fa-solid fa-trash"></i>
                                    </button>
                                </th>
                        </tr>
                    </thead>
                    <tbody>
                        <input type="hidden" name="est" value="{{ $data[0]->est }}">
                        <input type="hidden" name="afd" value="{{ $data[0]->afd }}">
                        <input type="hidden" name="start" value="{{ $start }}">
                        <input type="hidden" name="last" value="{{ $last }}">
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->blok }}</td>
                            <td>{{ $item->no_tph }}</td>
                            <td>{{ $item->luas }}</td>
                            <td>{{ $item->bt_tph }}</td>
                            <td>{{ $item->bt_jalan }}</td>
                            <td>{{ $item->bt_bin }}</td>
                            <td>{{ $item->jum_karung }}</td>
                            <td>{{ $item->buah_tinggal }}</td>
                            <td>{{ $item->restan_unreported }}</td>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}">
                            </td>
                        </tr>
                        @endforeach
                        </form>
                    </tbody>
                </table>
                @else
                <table id="sidakBiasa" class="text-center" style="width:100%">
                    {{ csrf_field() }}
                    <thead>
                        <tr>
                            <th rowspan="3">Blok</th>
                            <th rowspan="3">No. TPH</th>
                            <th rowspan="3">Luas (Ha)</th>
                            <th colspan="3">Brondolan Tinggal</th>
                            <th rowspan="2">Karung Isi Brondolan </th>
                            <th rowspan="2">Buah Tinggal di TPH </th>
                            <th rowspan="2">Restan Tidak Dilaporkan </th>
                        </tr>
                        <tr>
                            <th>Di TPH</th>
                            <th>Di JALAN</th>
                            <th>Di TPH </th>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <th>Jumlah</th>
                            <th>Jumlah </th>
                            <th>Jumlah </th>
                            <th>Janjang </th>
                            <th>Janjang </th>
                        </tr>
                    </thead>
                    <tbody>
                        <input type="hidden" name="est" value="{{ $data[0]->est }}">
                        <input type="hidden" name="afd" value="{{ $data[0]->afd }}">
                        <input type="hidden" name="start" value="{{ $start }}">
                        <input type="hidden" name="last" value="{{ $last }}">
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->blok }}</td>
                            <td>{{ $item->no_tph }}</td>
                            <td>{{ $item->luas }}</td>
                            <td>{{ $item->bt_tph }}</td>
                            <td>{{ $item->bt_jalan }}</td>
                            <td>{{ $item->bt_bin }}</td>
                            <td>{{ $item->jum_karung }}</td>
                            <td>{{ $item->buah_tinggal }}</td>
                            <td>{{ $item->restan_unreported }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            <br>
            <div class="card p-3">
                <h4 class="text-center mt-2" style="font-weight: bold">FOTO TEMUAN</h4>
                <hr>
                <div class="row">
                    @foreach ($img as $item)
                    <div class="col-3">
                        @php
                        $test = $item['foto'];
                        $file = 'https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/' . $test;
                        $file_headers = @get_headers($file);
                        @endphp
                        @if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
                        @else
                        <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/{{ $item['foto'] }}" class="img-fluid  popup_image" alt="">
                        <input type="hidden" value="{{ $item['title'] }}" id="titleImg">
                        <p class="text-center mt-3" style="font-weight: bold">{{ $item['title'] }}</p>
                        @endif
                    </div>
                    {{-- @break --}}
                    @endforeach
                </div>
            </div>

            <div class="card p-4">
                <h4 class="text-center mt-2" style="font-weight: bold">Tracking Plot Sidak TPH - {{ $est }}
                    {{ $afd }}
                </h4>
                <hr>
                <div id="map" style="height:800px"></div>
            </div>
        </div>
    </section>
</div>
@include('layout/footer')

<script type="text/javascript" src="http://w2ui.com/src/w2ui-1.4.2.min.js"></script>

@if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep/Asisten')
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/js/lightbox.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/css/lightbox.min.css" rel="stylesheet">



<script>
    document.getElementById("hapusDetailSidakForm").addEventListener("submit", function() {
        var ids = [];
        var checkboxes = document.getElementsByName("ids[]");
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                ids.push(checkboxes[i].value);
            }
        }
        document.querySelector("input[name='ids']").value = ids.join(",");
    });
</script>
@endif

<script>
    $(document).ready(function() {
        $('#listSidakTPH').DataTable();
        $('#sidakBiasa').DataTable();

        $(".popup_image").on('click', function() {
            var titleImg = document.getElementById('titleImg').value
            w2popup.open({
                title: titleImg,
                body: '<div class="w2ui-centered" ><img src="' + $(this).attr('src') +
                    '" ></img></div>',
                width: 1280, // width of the popup
                height: 720 // height of the popup
            });
        });
    });

    date = new Date().toISOString().slice(0, 10)

    var map = L.map('map').setView([-2.2745234, 111.61404248], 13);

    googleSat = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    // const googleSat = L.tileLayer(
    //     "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
    // ).addTo(map);


    const params = new URLSearchParams(window.location.search)
    var paramArr = [];
    for (const param of params) {
        paramArr = param
    }

    var est = '<?php echo $est; ?>';
    var afd = '<?php echo $afd; ?>';
    var start = '<?php echo $start; ?>';
    var last = '<?php echo $last; ?>';

    var _token = $('input[name="_token"]').val();


    $.ajax({
        url: "{{ route('getPlotLine') }}",
        method: "get",
        data: {
            est: est,
            afd: afd,
            _token: _token,
            start: start,
            last: last
        },
        success: function(result) {

            var plot = JSON.parse(result);

            const plotResult = Object.entries(plot['plot']);
            const markerResult = Object.entries(plot['marker']);
            const blokResult = Object.entries(plot['blok']);

            // console.log(plotResult.length)
            // console.log(plotResult)
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
            // console.log(blokResult)
            // drawPlot(plotResult)
            drawBlok(blokResult)
            drawTemuan(markerResult)
            drawLegend(markerResult)




        }
    })


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
        map.fitBounds(test.getBounds());
    }

    function drawTemuan(markerResult) {

        console.log(markerResult);
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


            let popupContent = "<div> <span style='font-weight:bold'>Jam Sidak : </span>" + markerResult[i][1]['jam'] +
                "</div>" +
                "<div> <span style='font-weight:bold'>Nomor TPH : </span>" + markerResult[i][1]['notph'] + "</div>" +
                "<div ><span style='font-weight:bold'>Blok </span>: " + markerResult[i][1]['blok'] + "</div>" +
                "<div ><span style='font-weight:bold'>Brondolan Tinggal </span>: " + markerResult[i][1]['brondol_tinggal'] + "</div>" +
                "<div ><span style='font-weight:bold'>Jumlah Karung </span>: " + markerResult[i][1]['jum_karung'] + "</div>" +
                "<div ><span style='font-weight:bold'>Buah Tinggal </span>: " + markerResult[i][1]['buah_tinggal'] + "</div>" +
                "<div ><span style='font-weight:bold'>Restan Unreported </span>: " + markerResult[i][1]['restan_unreported'] + "</div>";

            // Add the image and comment for temuan1
            if (markerResult[i][1]['foto_temuan1'] && markerResult[i][1]['komentar1']) {
                popupContent += "<div><span style='font-weight:bold'>Temuan 1: </span><br>" +
                    "<a href='https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/" + markerResult[i][1]['foto_temuan1'] + "' data-lightbox='image1'>" +
                    "<img src='https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/" + markerResult[i][1]['foto_temuan1'] + "' style='max-width: 100%; height: auto;'>" +
                    "</a><br>" +
                    "<div class='image-comment'>" + markerResult[i][1]['komentar1'] + "</div>" +
                    "</div>";
            }

            // Add the image and comment for temuan2
            if (markerResult[i][1]['foto_temuan2'] && markerResult[i][1]['komentar2']) {
                popupContent += "<div><span style='font-weight:bold'>Temuan 2: </span><br>" +
                    "<a href='https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/" + markerResult[i][1]['foto_temuan2'] + "' data-lightbox='image2'>" +
                    "<img src='https://mobilepro.srs-ssms.com/storage/app/public/qc/sidak_tph/" + markerResult[i][1]['foto_temuan2'] + "' style='max-width: 100%; height: auto;'>" +
                    "</a><br>" +
                    "<div class='image-comment'>" + markerResult[i][1]['komentar2'] + "</div>" +
                    "</div>";
            }

            marker.bindPopup(popupContent);

            // Add the marker to the map
            marker.addTo(map);
        }

        // Adjust the map's bounds to fit all markers
        if (markerResult.length > 0) {
            let latlngs = markerResult.map(item => JSON.parse(item[1]['latln']));
            let bounds = L.latLngBounds(latlngs);
            map.fitBounds(bounds);
        }
    }

    function drawLegend(markerResult) {
        var legendContainer = L.control({
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
            div.innerHTML += '<div class="legend-item">Total Sidak TPH: ' + totalItemsCount + '</div>'; // Added the legend item for total items count

            div.innerHTML += '<div class="legend-item"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" class="legend-icon"> Temuan (' + temuanCount + ')</div>';

            return div;
        };

        legendContainer.addTo(map);
    }

    // function drawPlot(plot) {

    //     var getLineStr = '{"type"'
    //     getLineStr += ":"
    //     getLineStr += '"FeatureCollection",'
    //     getLineStr += '"features"'
    //     getLineStr += ":"
    //     getLineStr += '['

    //     for (let i = 0; i < plot.length; i++) {
    //         getLineStr += '{"type"'
    //         getLineStr += ":"
    //         getLineStr += '"Feature",'
    //         getLineStr += '"properties"'
    //         getLineStr += ":"
    //         getLineStr += '{},'
    //         getLineStr += '"geometry"'
    //         getLineStr += ":"
    //         getLineStr += '{"coordinates"'
    //         getLineStr += ":"
    //         getLineStr += plot[i][1]
    //         getLineStr += ',"type"'
    //         getLineStr += ":"
    //         getLineStr += '"Point"'
    //         getLineStr += '}},'
    //     }

    //     getLineStr = getLineStr.substring(0, getLineStr.length - 1);
    //     getLineStr += ']}'

    //     var line2 = JSON.parse(getLineStr)

    //     var test = L.geoJSON(line2['features'], {
    //             // onEachFeature: function(feature, layer){
    //             //     layer.myTag = 'LineMarker'
    //             //     layer.addTo(map);
    //             // },
    //             style: function(feature) {
    //                 return {
    //                     weight: 2,
    //                     opacity: 1,
    //                     color: 'yellow',
    //                     fillOpacity: 0.7
    //                 };
    //             }
    //         })
    //         .addTo(map);

    //     // map.fitBounds(test.getBounds());

    // }
</script>