<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> DASHBOARD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/CBI-logo.png') }}">

    <link href="{{asset('fontawesome6/css/all.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css" integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js" integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
    <!-- Leaflet.PolylineDecorator CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-polylinedecorator/dist/leaflet.polylineDecorator.css" />
    <!-- Leaflet.PolylineDecorator JavaScript -->
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
                <!-- <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link">Jabatan, {{ session('jabatan') }} </a>
                </li> -->
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item d-none d-sm-inline-block">
                    <a class="nav-link">
                        {{-- {{ session('user_name') }} --}}
                    </a>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <a href="{{ asset('dashboard') }}" class="brand-link">
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
                                width: 24px;
                                /* adjust the width as per your preference */
                                height: 24px;
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
                            <a href="{{ asset('/dashboard_gudang') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets9.lottiefiles.com/temp/lf20_vBnbOW.json"></div>
                                <p>
                                    QC Gudang
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <!-- uses solid style -->
                            <a href="{{ asset('/dashboardtph') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets10.lottiefiles.com/packages/lf20_Lpuvp7YT5K.json">
                                </div>

                                <p>
                                    QC Sidak TPH
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <!-- uses solid style -->
                            <a href="{{ asset('/dashboard_inspeksi') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets10.lottiefiles.com/packages/lf20_w4hwxwuq.json">
                                </div>

                                <p>
                                    QC Inspeksi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_mutubuah') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets1.lottiefiles.com/packages/lf20_bENSfZ37DY.json">
                                </div>

                                <p>
                                    Mutu Buah
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('/dashboard_perum') }}" class="nav-link">
                                <div class="nav-icon lottie-animation" data-animation-path="https://assets1.lottiefiles.com/packages/lf20_bENSfZ37DY.json">
                                </div>

                                <p>
                                    Perumahan
                                </p>
                            </a>
                        </li>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var lottieElements = document.querySelectorAll('.lottie-animation');
                                lottieElements.forEach(function(element) {
                                    var animationPath = element.getAttribute('data-animation-path');
                                    lottie.loadAnimation({
                                        container: element,
                                        renderer: 'svg',
                                        loop: true,
                                        autoplay: true,
                                        path: animationPath
                                    });
                                });
                            });
                        </script>
                        {{--
                        <li class="nav-item">
                            <a href="{{ asset('/vm') }}" class="nav-link">
                        <i class="nav-icon fa fa-car"></i>
                        <p>
                            Vehicle Management
                        </p>
                        </a>
                        </li> --}}


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