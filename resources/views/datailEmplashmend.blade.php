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
                            <option value="{{ $item}}">{{ $item }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="ml-2 btn btn-primary mb-2" id="empData">Show</button>
                    </div>
                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}</div>
                    <div class="afd">Tahun/Bulan : <span id="selectedDate">{{ $tanggal }}</span></div>
                    <button id="back-to-data-btn" class="btn btn-primary" onclick="downloadpdf()" disabled>Download PDF</button>
                    <button id="back-to-data-btn" class="btn btn-primary" onclick="downloadBA()" disabled>Download BA</button>
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

            <!-- 
            <div class="text-center mt-3 mb-2 border border-dark">
                @if ($Perumahan->count() > 0)
             
                <div class="text-center">
                    <h1>Foto Temuan Perumahan</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($Perumahan as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/perumahan/" . $item['foto_temuan_rmh'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_rmh'] }}" class="img-thumbnail" data-toggle="modal" data-target="#myModal{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_rmh'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_rmh'] }}</p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $Perumahan->links() }}
                </div>

                @else
                <p>Perumahan not found.</p>
                @endif
            </div> -->
            <!-- 
            <div class="text-center mt-3 mb-2 border border-dark">
                @if ($lingkungan->count() > 0)

                <div class="text-center">
                    <h1>Foto Temuan Lingkungan</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($lingkungan as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/lingkungan/" . $item['foto_temuan_ll'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">

                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_ll'] }}" class="img-thumbnail" data-toggle="modal" data-target="#lingkunganMod{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_ll'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_ll'] }}</p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $lingkungan->links() }}
                </div>

                @else
                <p>Lingkungan not found.</p>
                @endif
            </div> -->

            <!-- 
            <div class="text-center mt-3 mb-2 border border-dark">
                @if ($Landscape->count() > 0)

                <div class="text-center">
                    <h1>Foto Temuan Landscape</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($Landscape as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/landscape/" . $item['foto_temuan_ls'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">
                            <img src="{{ $imageSrc }}" alt="" class="img-thumbnail" data-toggle="modal" data-target="#landcsp{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_ls'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_ls'] }}</p>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $Landscape->links() }}
                </div>

                @else
                <p>Landscape not found.</p>
                @endif
            </div> -->



            <!-- modoal  -->
            <!-- Move the modal section outside of the loop -->

            <!-- @foreach ($Perumahan as $item)
            <div class="modal fade" id="myModal{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_rmh'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['komentar_temuan_rmh'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_rmh'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @foreach ($lingkungan as $item)
            <div class="modal fade" id="lingkunganMod{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_ll'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['foto_temuan_ll'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_ll'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @foreach ($Landscape as $item)
            <div class="modal fade" id="landcsp{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_ls'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['foto_temuan_ls'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_ls'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach -->




        </div>


        <div class="card table_wrapper">
            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark custom_background">
                <h2>Temuan AFDELING</h2>
            </div>


            <!-- <div class="text-center mt-3 mb-2 border border-dark">
                @if ($rumah_afd->count() > 0)

                <div class="text-center">
                    <h1>Foto Temuan Perumahan</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($rumah_afd as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/perumahan/" . $item['foto_temuan_rmh'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_rmh'] }}" class="img-thumbnail" data-toggle="modal" data-target="#rumah_afd{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_rmh'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_rmh'] }}</p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $rumah_afd->links() }}
                </div>

                @else
                <p>Perumahan not found.</p>
                @endif
            </div>

            <div class="text-center mt-3 mb-2 border border-dark">
                @if ($lingkungan_afd->count() > 0)

                <div class="text-center">
                    <h1>Foto Temuan Lingkungan</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($lingkungan_afd as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/lingkungan/" . $item['foto_temuan_lk'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">

                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_lk'] }}" class="img-thumbnail" data-toggle="modal" data-target="#afd_ling{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_lk'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_lk'] }}</p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $lingkungan_afd->links() }}
                </div>

                @else
                <p>Lingkungan not found.</p>
                @endif
            </div>

            <div class="text-center mt-3 mb-2 border border-dark">
                @if ($lcp_afd->count() > 0)

                <div class="text-center">
                    <h1>Foto Temuan Landscape</h1>
                </div>
                <div class="row justify-content-center">
                    @foreach ($lcp_afd as $item)
                    @php
                    $imageUrl = "https://mobilepro.srs-ssms.com/storage/app/public/qc/landscape/" . $item['foto_temuan_lcp'];
                    $imageInfo = @getimagesize($imageUrl);
                    $imageSrc = $imageInfo ? $imageUrl : asset('img/404img.png');
                    @endphp

                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card">
                            <img src="{{ $imageSrc }}" alt="" class="img-thumbnail" data-toggle="modal" data-target="#afd_lcp{{ $loop->iteration }}">

                            <div class="card-body mt-2">
                                <h5 class="card-title text-right">Est: {{ $item['title'] }}</h5>
                                <p class="card-text text-left">Temuan: {{ $item['komentar_temuan_lcp'] }}</p>
                                <p class="card-text text-left">Komentar: {{ $item['komentar_lcp'] }}</p>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $lcp_afd->links() }}
                </div>

                @else
                <p>Landscape not found.</p>
                @endif
            </div> -->


            <!-- modal  -->

            <!-- @foreach ($rumah_afd as $item)
            <div class="modal fade" id="rumah_afd{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_rmh'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['komentar_temuan_rmh'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_rmh'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @foreach ($lingkungan_afd as $item)
            <div class="modal fade" id="afd_ling{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_lk'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['komentar_temuan_lk'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_lk'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @foreach ($lcp_afd as $item)
            <div class="modal fade" id="afd_lcp{{ $loop->iteration }}" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center">{{ $item['title'] }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $imageSrc }}" alt="{{ $item['foto_temuan_lcp'] }}" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <p class="card-text text-left mb-0"><strong>Temuan:</strong> {{ $item['komentar_temuan_lcp'] }}</p>
                            <p class="card-text text-left mb-0"><strong>Komentar:</strong> {{ $item['komentar_lcp'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach -->

        </div>

    </div>
    @include('layout/footer')



    <script>
        function goBack() {
            // Save the selected tab to local storage
            localStorage.setItem('selectedTab', 'nav-data-tab');

            // Redirect to the target page
            window.location.href = "http://ssms-qc.test/dashboard_perum";
        }

        $('#empData').click(function() {
            getTemuan();

            // Swal.fire({
            //     title: 'Loading',
            //     html: '<span class="loading-text">Mohon Tunggu...</span>',
            //     allowOutsideClick: false,
            //     showConfirmButton: false,
            //     onBeforeOpen: () => {
            //         Swal.showLoading();
            //     }
            // });
        });

        function getTemuan() {
            var _token = $('input[name="_token"]').val();
            var estData = $("#est").val();
            var tanggal = $("#inputDate").val();

            $.ajax({
                url: "{{ route('getTemuan') }}",
                method: "get",
                data: {
                    estData: estData,
                    tanggal: tanggal,

                    _token: _token
                },
                success: function(result) {
                    var parseResult = JSON.parse(result)
                    // Swal.close();
                    var Landscape = Object.entries(parseResult['Landscape'])
                    var lingkungan = Object.entries(parseResult['lingkungan'])
                    var Perumahan = Object.entries(parseResult['Perumahan'])

                    console.log(Landscape);

                }
            });
        }
    </script>