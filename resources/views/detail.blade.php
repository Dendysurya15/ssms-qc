@include('layout/header')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid pt-3">
            <a href="{{ route('dashboard_gudang') }}" class="btn btn-dark"> <i class="nav-icon fa-solid fa-arrow-left "></i></a>
            <a href="/cetakpdf/{{ $data->id }}" class=" btn btn-primary mb-3 mt-3"><i class="fa-solid fa-file-pdf"></i>
                Unduh File</a>

            <?php

            if (session('user_name') == 'Dennis Irawan' || session('user_name') == 'Ferry Suhada') {
            ?>
                <a onclick="return confirm('Anda yakin untuk mengahpus record ini?')" href="/hapusRecord/{{ $data->id }}" class=" btn btn-danger mb-3 mt-3"><i class="fa-solid fa-trash"></i>
                    Hapus Record</a>
            <?php
            }
            ?>




            <div class="card">
                <div class="card-body">
                    <table class="table table-primary col-xs-1 text-center mb-3">
                        <thead>
                            <tr>
                                <th>III.PEMERIKSAAN GUDANG</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>ESTATE</th>
                                        <td>{{ $data->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>TANGGAL</th>
                                        <td>{{ $data->tanggal_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>KTU</th>
                                        <td>{{{ $data->nama_ktu }}}</td>
                                    </tr>
                                    <tr>
                                        <th>KEPALA GUDANG</th>
                                        <td>{{ $data->kpl_gudang }}</td>
                                    </tr>
                                    <tr>
                                        <th>DIPERIKSA OLEH</th>
                                        <td>{{ $data->qc }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 col-lg-3 offset-lg-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="table text-center">SKOR</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">{{ $data->skor_total }}</th>
                                    </tr>
                                    <tr>
                                        @if ($data->skor_total >= 95)
                                        <th class="table-primary text-center">EXCELLENT</th>
                                        @elseif($data->skor_total >= 85 && $data->skor_total <95) <th class="table-success text-center">Good</th>
                                            @elseif($data->skor_total >= 75 && $data->skor_total <85) <th class="table text-center" style="background-color: yellow">Satisfactory
                                                </th>
                                                @elseif($data->skor_total >= 65 && $data->skor_total <75) <th class="table-warning text-center">Fair</th>
                                                    @elseif($data->skor_total <75) <th class="table text-center" style="background-color: red;color:white">Poor
                                                        </th>
                                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th>1.KESESUAIAN FISIK VS BINCARD</th>
                                        <th></th>
                                        <th>2.KESESUAIAN FISIK VS PPRO</th>
                                        <th></th>
                                        <th>3.BARANG CHEMICAL EXPIRED</th>
                                    </tr>
                                    <tr>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2">sesuai</td>
                                        @if ($data->foto_kesesuaian_bincard_1)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_1}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif
                                        <td rowspan="2">sesuai</td>
                                        @if ($data->foto_kesesuaian_ppro_1)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_1}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif
                                        <td rowspan="2">sesuai</td>
                                        @if ($data->foto_chemical_expired_1)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_chemical_expired_1}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                    </tr>
                                    <tr>
                                        @if ($data->foto_kesesuaian_bincard_2)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                        @if ($data->foto_kesesuaian_ppro_2)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                        @if ($data->foto_chemical_expired_2)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_chemical_expired_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center">{{ $data->komentar_kesesuaian_bincard }}</td>
                                        <td colspan="2" class="text-center">{{ $data->komentar_kesesuaian_ppro }}</td>
                                        <td colspan="2" class="text-center">{{ $data->komentar_chemical_expired }}</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>4.BARANG NON-STOCK</th>
                                        <th></th>
                                        <th>5.SELURUH MR DITANDATANGANI EM</th>
                                        <th></th>
                                        <th>6.KEBERSIHAN DAN KERAPIHAN GUDANG </th>
                                    </tr>
                                    <tr>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                        <td>HASIL</td>
                                        <td>FOTO</td>

                                    </tr>
                                    <!-- style="width :290pt;height:150pt" -->
                                    <style>
                                        .image-wrapper {
                                            position: relative;
                                            display: inline-block;
                                            width: 290pt;
                                            height: 150pt;
                                            overflow: hidden;
                                        }

                                        .image-wrapper img {
                                            width: 100%;
                                            height: 100%;
                                            object-fit: contain;
                                        }

                                        .rotate-button {
                                            position: absolute;
                                            bottom: 10px;
                                            right: 10px;
                                            z-index: 10;
                                            background-color: rgba(255, 255, 255, 0.7);
                                            border: none;
                                            padding: 5px 10px;
                                            cursor: pointer;
                                        }
                                    </style>

                                    <script>
                                        function rotateImage(imageId) {
                                            const image = document.getElementById(imageId);
                                            const currentRotation = parseInt(image.getAttribute('data-rotation')) || 0;
                                            const newRotation = currentRotation + 90;

                                            if (newRotation % 180 === 0) {
                                                image.style.width = '180pt';
                                                image.style.height = '300pt';
                                            } else {
                                                image.style.width = '300pt';
                                                image.style.height = '280pt';
                                                image.style.margin = '20pt'
                                            }

                                            image.style.transform = `rotate(${newRotation}deg)`;
                                            image.setAttribute('data-rotation', newRotation);
                                        }
                                    </script>


                                    <tr>
                                        <td rowspan="2">sesuai</td>
                                        @if ($data->foto_barang_nonstok_1)
                                        <td>
                                            <img id="image1" src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_1}}" style="weight:75pt;height:150pt">
                                        </td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                        <td rowspan="2">sesuai</td>
                                        @if ($data->foto_mr_ditandatangani_1)
                                        <td>
                                            <div class="image-wrapper">
                                                <img id="image2" src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_1}}" style="width: 180pt; height: 300pt;">
                                                <button class="rotate-button" onclick="rotateImage('image2')">Rotate</button>
                                            </div>
                                        </td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="width: 180pt; height: 300pt;"></td>
                                        @endif
                                        <td rowspan="2">sesuai</td>



                                        @if ($data->foto_kebersihan_gudang_1)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_1}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                    </tr>
                                    <tr>
                                        @if ($data->foto_barang_nonstok_2)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                        @if ($data->foto_mr_ditandatangani_2)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif
                                        @if ($data->foto_kebersihan_gudang_2)
                                        <td><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif
                                    </tr>
                                    <td colspan="2" class="text-center">{{ $data->komentar_barang_nonstok }}</td>
                                    <td colspan="2" class="text-center">{{ $data->komentar_mr_ditandatangani }}</td>
                                    <td colspan="2" class="text-center">{{ $data->komentar_kebersihan_gudang }}</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>7. BUKU INSPEKSI KTU</th>
                                    <tr>
                                        <td>HASIL</td>
                                        <td>FOTO</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2">SELESAI</td>
                                        @if ($data->foto_inspeksi_ktu_1)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_1}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                    </tr>
                                    <tr>
                                        @if ($data->foto_inspeksi_ktu_2)
                                        <td> <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_2}}" style="weight:75pt;height:150pt"></td>
                                        @else
                                        <td><img src="{{asset('noimage.png')}}" style="weight:75pt;height:150pt"></td>
                                        @endif

                                    </tr>

                                    <tr>
                                        <td colspan="2" class="text-center">{{ $data->komentar_inspeksi_ktu }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
@include('layout/footer')