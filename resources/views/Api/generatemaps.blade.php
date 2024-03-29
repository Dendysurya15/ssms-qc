<!DOCTYPE html>
<html>

<head>
    <title>Map Example</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <h1>ini maps testing</h1>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./assets/html2canvas.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="./assets/src/Leaflet.BigImage.css">
    <script src="./assets/src/Leaflet.BigImage.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.1.3/socket.io.js" crossorigin="anonymous"></script>

    <script>
        // Declare map globally
        $(document).ready(function() {
            createMapImage();
        });


        const createMapImage = async () => {
            const width = 1920;
            const height = 1080;

            const mapElement = document.createElement("div");
            mapElement.style.width = `${width}px`;
            mapElement.style.height = `${height}px`;
            document.body.appendChild(mapElement);

            const map = L.map(mapElement, {
                attributionControl: false,
                zoomControl: false,
                fadeAnimation: false,
                zoomAnimation: false
            });

            const tileLayer = L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            ).addTo(map);

            try {
                // const response = await axios.get('http://ssms-qc.test/api/plotmaps', {
                //     params: {
                //         est: 'SPE',
                //         date: '2024-03-13',
                //     }
                // });

                const response = @json($blokLatLn);

                console.log(response);

                generatemaps(response, map);
            } catch (error) {
                console.error('Error fetching data:', error);
            }

            await new Promise(resolve => tileLayer.on("load", () => resolve()));

            const dataURL = await domtoimage.toJpeg(mapElement, {
                width,
                height,
                quality: 0.7
            });

            //     // console.log(dataURL);
            $.ajax({
                url: 'https://srs-ssms.com/uploadFotoTaksasi.php',
                type: 'post',
                data: {
                    image: dataURL,
                    estate: 'testing',
                    datetime: '2023-03-13',
                },
                success: function(data) {
                    console.log('Upload successfully gan');
                },
                error: function(xhr, status, error) {
                    console.log('Upload failed:', error);
                }
            });
        };

        function generatemaps(data, map) {
            var getPlotStr = '{"type"'
            getPlotStr += ":"
            getPlotStr += '"FeatureCollection",'
            getPlotStr += '"features"'
            getPlotStr += ":"
            getPlotStr += '['
            for (let i = 0; i < data.length; i++) {
                getPlotStr += '{"type"'
                getPlotStr += ":"
                getPlotStr += '"Feature",'
                getPlotStr += '"properties"'
                getPlotStr += ":"
                getPlotStr += '{"blok"'
                getPlotStr += ":"
                getPlotStr += '"' + data[i]['blok'] + '",'
                getPlotStr += '"estate"'
                getPlotStr += ":"
                getPlotStr += '"' + data[i]['estate'] + '",'
                getPlotStr += '"afdeling"'
                getPlotStr += ":"
                getPlotStr += '"' + data[i]['afdeling'] + '"'
                getPlotStr += '},'
                getPlotStr += '"geometry"'
                getPlotStr += ":"
                getPlotStr += '{"coordinates"'
                getPlotStr += ":"
                getPlotStr += '[['
                getPlotStr += data[i]['latln']
                getPlotStr += ']],"type"'
                getPlotStr += ":"
                getPlotStr += '"Polygon"'
                getPlotStr += '}},'
            }
            getPlotStr = getPlotStr.substring(0, getPlotStr.length - 1);
            getPlotStr += ']}'

            var blok = JSON.parse(getPlotStr)
            var centerBlok = L.geoJSON(blok, {
                    onEachFeature: function(feature, layer) {

                        layer.myTag = 'BlokMarker'
                        var label = L.marker(layer.getBounds().getCenter(), {
                            icon: L.divIcon({
                                className: 'label-bidang',
                                // iconAnchor: [0, 0],
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
                                    fillColor: "#77543f",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OG':
                                return {
                                    fillColor: "#dfd29e",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OH':
                                return {
                                    fillColor: "#db423c",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OI':
                                return {
                                    fillColor: "#ba9355",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OJ':
                                return {
                                    fillColor: "#ccff00",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OK':
                                return {
                                    fillColor: "#8f9e8a",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OL':
                                return {
                                    fillColor: "#14011c",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                            case 'OM':
                                return {
                                    fillColor: "#01b9c5",
                                        color: 'white',
                                        fillOpacity: 0.4,
                                        opacity: 0.4,

                                };
                        }
                    }
                })
                .addTo(map);
            map.fitBounds(centerBlok.getBounds());
        }
    </script>
</body>

</html>