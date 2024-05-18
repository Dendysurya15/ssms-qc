<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QC APPS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/CBI-logo.png') }}">

    <link href="{{asset('fontawesome6/css/all.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css" integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js" integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
    <!-- Leaflet.PolylineDecorator CSS -->

    <script src="https://unpkg.com/leaflet-polylinedecorator/dist/leaflet.polylineDecorator.js"></script>
    <!-- Load Esri Leaflet from CDN -->
    <script src="https://unpkg.com/esri-leaflet@3.0.8/dist/esri-leaflet.js" integrity="sha512-E0DKVahIg0p1UHR2Kf9NX7x7TUewJb30mxkxEm2qOYTVJObgsAGpEol9F6iK6oefCbkJiA4/i6fnTHzM6H1kEA==" crossorigin=""></script>

    <!-- Load Esri Leaflet Vector from CDN -->
    <script src="https://unpkg.com/esri-leaflet-vector@4.0.0/dist/esri-leaflet-vector.js" integrity="sha512-EMt/tpooNkBOxxQy2SOE1HgzWbg9u1gI6mT23Wl0eBWTwN9nuaPtLAaX9irNocMrHf0XhRzT8B0vXQ/bzD0I0w==" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
    <link href="{{ asset('css/css.css') }}" rel="stylesheet">


    <script type="text/javascript" src="{{ asset('js/loader.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/buttons.dataTables.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.min.css') }}" />



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-image/v0.0.4/leaflet-image.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.0/jszip.min.js"></script>
    <link href="DataTables/datatables.min.css" rel="stylesheet">
    <script src="DataTables/datatables.min.js"></script>
    <style type="text/css">
        .center {
            margin: auto;
            height: 500px;
            width: 70%;
            padding: 10px;
            text-align: center;
        }

        .tengah {
            vertical-align: middle;
        }

        .hijau {
            background-color: #00621A;
            color: white;
        }

        .biru {
            background-color: #001494;
            color: white;
        }

        .merah {
            background-color: red;
            color: red;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/lottie-web@latest"></script>
</head>



<body class="hold-transition sidebar-mini sidebar-collapse layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="hover"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link">Selamat datang, {{ session('user_name') }} </a>
                </li>
            </ul>

        </nav>
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <a href="{{ asset('rekap') }}" class="brand-link">
                <img src="{{ asset('img/CBI-logo.png') }}" alt="Covid Tracker" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Dashboard</span>
            </a>
            <div class="sidebar">
                <nav class="" style="height: 100%">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="height: 100%">
                        <!-- USER LAB -->

                        <!-- Include Lottie library -->

                        <style>
                            .lottie-animation {
                                width: 40px;
                                /* adjust the width as per your preference */
                                height: 40px;
                                /* adjust the height as per your preference */
                                margin-right: 8px;
                                /* add some spacing between the icon and the text */
                                display: inline-block;
                                /* make the icon and the text appear on the same line */
                                vertical-align: middle;
                                /* align the icon vertically with the text */
                            }

                            .nav-link p {
                                display: inline-block;
                                vertical-align: middle;
                            }
                        </style>
                        <li class="nav-item">
                            <a href="{{ asset('/rekap') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="{{ asset('img/ALLREKAP.json') }}"></div>
                                <p>ALL SKOR PANEN </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_inspeksi') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets10.lottiefiles.com/packages/lf20_w4hwxwuq.json"></div>
                                <p>PANEN REGULAR</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <!-- uses solid style -->
                            <a href="{{ asset('/dashboardtph') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets10.lottiefiles.com/packages/lf20_Lpuvp7YT5K.json">
                                </div>

                                <p>
                                    SIDAK MUTU TRANSPORT
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_mutubuah') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets1.lottiefiles.com/packages/lf20_bENSfZ37DY.json">
                                </div>

                                <p>
                                    SIDAK MUTU BUAH

                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_gudang') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets9.lottiefiles.com/temp/lf20_vBnbOW.json"></div>
                                <p>
                                    GUDANG

                                </p>
                            </a>
                        </li>





                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_perum') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="{{ asset('img/homejson.json') }}">
                                </div>

                                <p>
                                    PERUMAHAN

                                </p>
                            </a>
                        </li>
                        @if (strpos(session('departemen'), 'QC') !== false)
                        <li class="nav-item">
                            <a href="{{ asset('/dashboardabsensi') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://lottie.host/237bc051-94b1-45d6-89da-3144341616a8/i4uJsopUfQ.json"></div>
                                <p>Absensi QC</p>
                            </a>
                        </li>
                        @endif

                        @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep' || session('jabatan') == 'Asisten')
                        <li class="nav-item">
                            <a href="{{ asset('/userqcpanel') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="{{ asset('img/homejson.json') }}"></div>
                                <p>Management User QC</p>
                            </a>
                        </li>
                        @endif


                        <!--@if (strpos(session('departemen'), 'QC') !== false)-->
                        <!--<li class="nav-item">-->
                        <!--    <a class="nav-link" id="deleteData">-->
                        <!--        <div class="nav-icon lottie-animation justify-between" data-animation-path="https://assets4.lottiefiles.com/packages/lf20_d6r9tuqy.json" style="width:25px; height:25px;"></div>-->
                        <!--        <p>-->
                        <!--            Hapus Data Duplikat-->
                        <!--        </p>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <!--@endif-->



                        <div class="fixed-bottom mb-3" style="position: absolute;">

                            @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep' || session('jabatan') == 'Asisten')
                            <li class="nav-item">
                                <a href="{{ route('user.show') }}" class="nav-link">

                                    <div class="nav-icon lottie-animation" data-animation-path="https://assets9.lottiefiles.com/packages/lf20_8y92hieq.json">
                                    </div>
                                    <p>
                                        User QC
                                    </p>
                                </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="nav-icon fa fa-sign-out-alt"></i>
                                    <p>
                                        Logout
                                    </p>
                                </a>
                            </li>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>

                    </ul>
                </nav>


            </div>
        </aside>
        @if (strpos(session('departemen'), 'QC') !== false)
        @section('js')
        <script>
            // $("#deleteData").click(function() {
            //     Swal.fire({
            //         title: 'Yakin menghapus data duplikat?',
            //         text: "Anda tidak dapat mengembalikan ini!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Ya, hapus data!'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             // Perform the request to the PHP file when the confirmation is triggered
            //             fetch('http://localhost/php/deleteDuplicate.php')
            //                 .then(response => response.json())
            //                 .then(data => {
            //                     // Show SweetAlert based on the response for each object
            //                     if (data.mutu_ancak_new.status === 'success') {
            //                         Swal.fire({
            //                             title: 'Berhasil!',
            //                             text: data.mutu_ancak_new.message,
            //                             icon: 'success'
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             title: 'Gagal!',
            //                             text: data.mutu_ancak_new.message,
            //                             icon: 'error'
            //                         });
            //                     }

            //                     if (data.mutu_buah.status === 'success') {
            //                         Swal.fire({
            //                             title: 'Berhasil!',
            //                             text: data.mutu_buah.message,
            //                             icon: 'success'
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             title: 'Gagal!',
            //                             text: data.mutu_buah.message,
            //                             icon: 'error'
            //                         });
            //                     }

            //                     if (data.mutu_transport.status === 'success') {
            //                         Swal.fire({
            //                             title: 'Berhasil!',
            //                             text: data.mutu_transport.message,
            //                             icon: 'success'
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             title: 'Gagal!',
            //                             text: data.mutu_transport.message,
            //                             icon: 'error'
            //                         });
            //                     }
            //                 })
            //                 .catch(error => {
            //                     // Handle any errors that occur during the request
            //                     console.error('Error:', error);
            //                 });
            //         }
            //     });
            // });
        </script>
        @endsection
        @endif