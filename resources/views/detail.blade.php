<style>
    td.my-cell {
        border: 2px solid #ddd;
    }

    th.my-cell {
        border: 2px solid #ddd;
    }

    */ .modal {
        display: none;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        position: relative;
    }

    .modal-image:hover {
        cursor: pointer;
    }

    .modal-image {
        max-width: 100%;
        max-height: 90%;
        transition: transform 0.3s ease-out;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 40px;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    .button-group {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        bottom: 10px;
        width: 100%;
    }

    .rotate {
        margin-right: 5px;
    }

    /* Added media query to adjust button position for small screens */
    @media only screen and (max-width: 600px) {
        .button-group {
            flex-direction: column;
            align-items: center;
            width: 80%;
        }

        .rotate {
            margin-right: 0;
            margin-bottom: 5px;
        }
    }
</style>
@include('layout/header')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid pt-3">
            <a href="{{ route('dashboard_gudang') }}" class="btn btn-dark"> <i class="nav-icon fa-solid fa-arrow-left "></i></a>
            <a href="/cetakpdf/{{ $data->id }}" class=" btn btn-primary mb-3 mt-3"><i class="fa-solid fa-file-pdf"></i>
                Unduh File</a>

            <?php

            if (session('user_name') == 'Dennis Irawan' || session('user_name') == 'Ferry Suhada' || session('user_name') == 'Andri Mursalim') {
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
                                    @php
                                    $splitQC = explode(";", $data->qc);
                                    @endphp
                                    <tr>
                                        <th>DIPERIKSA OLEH</th>
                                        <td>@if (!empty($splitQC[1])) 1. @endif {{ $splitQC[0] }}</td>
                                    </tr>
                                    @if (!empty($splitQC[1]))
                                    <tr>
                                        <th></th>
                                        <td>2. {{ $splitQC[1] }}</td>
                                    </tr>
                                    @endif
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

                        @if ($data->unit == 'CWS1' || $data->unit == 'CWS2' || $data->unit == 'CWS3')
                        <table class="table">


                            <thead>
                                <tr class="table-primary">
                                    <th class="my-cell text-center" colspan="2">1.KESESUAIAN FISIK VS BINCARD</th>
                                    <th class="my-cell text-center" colspan="2">2.KESESUAIAN FISIK VS PPRO</th>
                                    <th class="my-cell text-center" colspan="2">3.BARANG NON-STOCK</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                </tr>

                                <tr>


                                    <td class="my-cell" rowspan="2">
                                        {{ $data->kesesuaian_bincard == 25 ? 'Tidak ditemukan selisih'
                                        : ($data->kesesuaian_bincard == 22 ? 'Ditemukan Selisih >0 s.d ≤0,5% dari total
                                        sample'
                                        : ($data->kesesuaian_bincard == 17 ? 'Ditemukan Selisih >0,5 s.d ≤1% dari total
                                        sample'
                                        : ($data->kesesuaian_bincard == 12 ? 'Ditemukan Selisih >0,5 s.d ≤1% dari total
                                        sample'
                                        : ($data->kesesuaian_bincard == 7 ? 'Ditemukan Selisih >2 s.d ≤3% dari total
                                        sample'
                                        : ($data->kesesuaian_bincard == 0 ? 'Ditemukan Selisih >3% dari total sample' :
                                        ''))))) }}
                                    </td>

                                    <td class="my-cell col-md-4 "><span><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_1}}" class="img-fluid modal-image"></span></td>

                                    <td class="my-cell" rowspan="2">
                                        {{ $data->kesesuaian_ppro == 25 ? 'Tidak ditemukan selisih'
                                        : ($data->kesesuaian_ppro == 22 ? 'Ditemukan Selisih >0 s.d ≤0,5% dari total
                                        sample'
                                        : ($data->kesesuaian_ppro == 17 ? 'Ditemukan Selisih >0,5 s.d ≤1% dari total
                                        sample'
                                        : ($data->kesesuaian_ppro == 12 ? 'Ditemukan Selisih >0,5 s.d ≤1% dari total
                                        sample'
                                        : ($data->kesesuaian_ppro == 7 ? 'Ditemukan Selisih >2 s.d ≤3% dari total
                                        sample'
                                        : ($data->kesesuaian_ppro == 0 ? 'Ditemukan Selisih >3% dari total sample' :
                                        ''))))) }}
                                    </td>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_1}}" class="img-fluid  modal-image"></td>

                                    <td class="my-cell" rowspan="2">
                                        {{ $data->barang_nonstok == 5 ? 'Tidak ada barang non-stock'
                                        : ($data->barang_nonstok == 0 ? 'Ada barang non-stock' : '') }}
                                    </td>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_1}}" class="img-fluid modal-image"></td>
                                </tr>

                                <tr>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_2}}" class="img-fluid modal-image"></td>

                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_2}}" class="img-fluid modal-image"></td>

                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_2}}" class="img-fluid modal-image"></td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kesesuaian_bincard }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kesesuaian_ppro }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_barang_nonstok }}
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">4.SELURUH MR DITANDATANGANI MANAGER CWS
                                    </th>
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">5.KEBERSIHAN DAN KERAPIHAN GUDANG</th>
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">6.BUKU INSPEKSI KTU (LOGBOOK KTU)</th>

                                </tr>
                                <tr>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                </tr>
                                <tr>
                                    <td class="my-cell" rowspan="2">
                                        {{
                                        $data->mr_ditandatangani == 15 ? 'MR Ditandatangani oleh EM Seluruhnya' :
                                        ($data->mr_ditandatangani == 10 ? 'Ditemukan MR (H+2) yang tidak ditandatangani
                                        EM' :
                                        ($data->mr_ditandatangani == 5 ? 'Ditemukan MR (H+3) yang tidak ditandatangani
                                        EM' :
                                        ($data->mr_ditandatangani == 0 ? 'Ditemukan MR (>H+3) yang tidak ditandatangani
                                        EM' : '')))
                                        }}
                                    </td>



                                    @if ($data->foto_mr_ditandatangani_1)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif




                                    <td class="my-cell" rowspan="2">
                                        {{$data->kebersihan_gudang + $data->bincard_terbungkus +
                                        $data->peletakan_bincard + $data->rak_ditutup + $data->cat_sesuai}}
                                    </td>





                                    @if ($data->foto_kebersihan_gudang_1)
                                    <td class="my-cell col-md-4"><img data-original-src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_1}}" src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif



                                    <td class="my-cell" rowspan="2">
                                        {{ $data->inspeksi_ktu == 5 ? ' Logbook todate & lengkap'
                                        : ($data->inspeksi_ktu == 0 ? ') Logbook tidak todate ' : '') }}
                                    </td>
                                    @if ($data->foto_inspeksi_ktu_1)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($data->foto_mr_ditandatangani_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    @if ($data->foto_kebersihan_gudang_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    @if ($data->foto_inspeksi_ktu_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_mr_ditandatangani }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kebersihan_gudang }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_inspeksi_ktu }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        @else
                        <table class="table">


                            <thead>
                                <tr class="table-primary">
                                    <th class="my-cell text-center" colspan="2">1.KESESUAIAN FISIK VS BINCARD</th>
                                    <th class="my-cell text-center" colspan="2">2.KESESUAIAN FISIK VS PPRO</th>
                                    <th class="my-cell text-center" colspan="2">3.BARANG CHEMICAL EXPIRED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                </tr>


                                <div class="modal">
                                    <div class="modal-content">
                                        <img src="" alt="" class="modal-image">
                                        <div class="button-group">
                                            <button class="rotate btn btn-primary">Rotate</button>
                                            <button id="save-btn" class="btn btn-success">Save</button>
                                            <label for="image-upload" class="btn btn-success">Upload</label>
                                            <!-- Add the form element for image upload -->
                                            <form id="uploadForm" method="post" enctype="multipart/form-data">
                                                <input type="file" id="image-upload" accept="image/*">
                                                <button id="upload-button">Upload</button>
                                            </form>
                                        </div>
                                        <span class="close">&times;</span>
                                    </div>
                                </div>


                                <tr>


                                    <td class="my-cell" rowspan="2">
                                        {{$data->kesesuaian_bincard == 15 ? 'Sesuai'
                                        :($data->kesesuaian_bincard == 10 ? 'Selisih 1 Item Barang'
                                        : ($data->kesesuaian_bincard == 5 ? 'Tidak Sesuai / Selisih >1 item barang'
                                        :''))}}
                                    </td>

                                    <td class="my-cell col-md-4 "><span><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_1}}" class="img-fluid modal-image"></span></td>

                                    <td class="my-cell" rowspan="2">
                                        {{$data->kesesuaian_ppro == 20 ? 'Sesuai'
                                        :($data->kesesuaian_ppro == 15 ? 'Selisih 1 Item Barang'
                                        : ($data->kesesuaian_ppro == 5 ? 'Tidak Sesuai / Selisih >1 item barang'
                                        :''))}}
                                    </td>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_1}}" class="img-fluid  modal-image"></td>

                                    <td class="my-cell" rowspan="2">{{$data->chemical_expired == 15 ? 'Tidak ada
                                        chemical expired'
                                        :($data->chemical_expired == 10 ? '< 10% jenis chemical expired '
                                            : ($data->chemical_expired == 5 ? '>= 10% jenis chemical expired' :''))}}
                                    </td>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_chemical_expired_1}}" class="img-fluid modal-image"></td>
                                </tr>

                                <tr>
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_bincard_2}}" class="img-fluid modal-image"></td>

                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kesesuaian_ppro_2}}" class="img-fluid modal-image"></td>

                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_chemical_expired_2}}" class="img-fluid modal-image"></td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kesesuaian_bincard }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kesesuaian_ppro }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_chemical_expired }}
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">4.TIDAK TERDAPAT BARANG NON-STOCK</th>
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">5.SELURUH MR DITANDATANGANI EM</th>
                                    {{-- <th class="my-cell" scope="col"></th> --}}
                                    <th class="my-cell text-center" colspan="2">6.KEBERSIHAN DAN KERAPIHAN GUDANG</th>

                                </tr>
                                <tr>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>
                                </tr>
                                <tr>
                                    <td class="my-cell" rowspan="2">
                                        {{
                                        $data->barang_nonstok == 5 ? 'Ya' :
                                        ($data->barang_nonstok == 0 ? 'Tidak Ada' : '')
                                        }}
                                    </td>
                                    @if ($data->foto_barang_nonstok_1)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    <td class="my-cell" rowspan="2">
                                        {{($data->mr_ditandatangani == 10) ? 'MR Ditandatangani oleh EM Seluruhnya ' :
                                        (($data->mr_ditandatangani ==
                                        7) ?
                                        ' Ditemukan MR (H+2) yang tidak ditandatangani EM' : (($data->mr_ditandatangani
                                        == 4) ? ' Ditemukan MR (>H+2) yang tidak ditandatangani EM' :
                                        ''))}}
                                    </td>
                                    @if ($data->foto_mr_ditandatangani_1)
                                    <td class="my-cell col-md-4"><img data-original-src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_1}}" src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    <td class="my-cell" rowspan="2">
                                        {{$data->kebersihan_gudang + $data->gudang_pupuk + $data->bincard_terbungkus +
                                        $data->peletakan_bincard + $data->rak_ditutup + $data->cat_sesuai}}
                                    </td>
                                    @if ($data->foto_kebersihan_gudang_1)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($data->foto_barang_nonstok_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_barang_nonstok_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    @if ($data->foto_mr_ditandatangani_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_mr_ditandatangani_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                    @if ($data->foto_kebersihan_gudang_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_kebersihan_gudang_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_barang_nonstok }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_mr_ditandatangani }}
                                    </td>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_kebersihan_gudang }}
                                    </td>
                                </tr>
                                <tr class="table-primary">

                                    <th class="my-cell text-center" colspan="2">7. BUKU INSPEKSI KTU</th>

                                </tr>
                                <tr>
                                    <td class="my-cell">HASIL</td>
                                    <td class="my-cell">FOTO</td>

                                </tr>
                                <tr>
                                    <td class="my-cell" rowspan="2">
                                        {{($data->inspeksi_ktu == 5) ? 'Logbook todate & lengkap ' :
                                        (($data->inspeksi_ktu == 0) ? ' Logbook tidak todate' : '')}}
                                    </td>
                                    @if ($data->foto_inspeksi_ktu_1)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_1}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($data->foto_inspeksi_ktu_2)
                                    <td class="my-cell col-md-4"><img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/inspeksi_gudang/{{$data->foto_inspeksi_ktu_2}}" class="img-fluid modal-image"></td>
                                    @else
                                    <td><img src="{{asset('img/404img.png')}}" style="weight:75pt;height:150pt"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center my-cell">{{ $data->komentar_inspeksi_ktu }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                        @endif



                    </div>
                </div>
            </div>
    </section>
</div>
@include('layout/footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the main modal element
        const modal = document.querySelector('.modal');

        // Get the modal content element
        const modalContent = document.querySelector('.modal-content');

        // Get the close button for the modal
        const closeButton = modal.querySelector('.close');

        // Get the rotate button
        const rotateButton = modal.querySelector('.rotate');

        // Get the save button
        const saveButton = modal.querySelector('#save-btn');
        // Get the modal image element
        const modalImage = modal.querySelector('.modal-image');

        let totalRotation = 0;
        const imageUploadInput = document.getElementById('image-upload');
        const uploadButton = document.getElementById('upload-button');

        uploadButton.addEventListener('click', function() {
            const file = imageUploadInput.files[0];

            if (file) {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('filename', file.name);

                const xhr = new XMLHttpRequest();
                const url = 'https://srs-ssms.com/qc_inspeksi/upGudang.php';
                xhr.open('POST', url, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log(xhr.responseText);
                            // Handle response from server
                        } else {
                            console.log('Error:', xhr.statusText);
                        }
                    }
                };
                xhr.send(formData);
            } else {
                console.log('No file selected.');
            }
        });

        // Add a click event listener to the image upload label
        imageUploadInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                modalImage.src = URL.createObjectURL(file);
                modalImage.alt = file.name;
                modal.style.display = 'block';
                resetRotation();

                // Set the filename for upload based on modalImage src
                const modalImageSrc = modalImage.getAttribute('src');
                const filename = modalImageSrc.substring(modalImageSrc.lastIndexOf('/') + 1);
                imageUploadInput.setAttribute('data-filename', filename);
            }
        });


        // Loop through each image
        const images = document.querySelectorAll('.modal-image');
        images.forEach((image) => {
            // Add a click event listener to the image
            image.addEventListener('click', () => {
                // Get the clicked image source and alt attributes
                const src = image.getAttribute('src');
                const alt = image.getAttribute('alt');

                // Update the modal image source and alt attributes
                modalImage.setAttribute('src', src);
                modalImage.setAttribute('alt', alt);
                resetRotation();

                // Show the main modal
                modal.style.display = 'block';
            });
        });

        // Add a click event listener to the close button
        modalContent.addEventListener('click', (event) => {
            if (event.target === closeButton || event.target === modalContent) {
                // Hide the modal
                modal.style.display = 'none';
            }
        });


        // Add a click event listener to the rotate button
        rotateButton.addEventListener('click', () => {
            // Get the modal image
            const modalImage = modal.querySelector('.modal-image');

            // Calculate the new rotation angle
            const newRotation = totalRotation + 90;

            // Update the rotation angle and style
            modalImage.style.transform = `rotate(${newRotation}deg)`;

            // Update the total rotation angle
            totalRotation = newRotation;
        });

        // Add a click event listener to the save button
        saveButton.addEventListener('click', function() {
            sendRequest();
            // sendIMG();
        });
        // Function to reset rotation
        function resetRotation() {
            totalRotation = 0;

            // Get the modal image and reset the rotation angle and style
            const modalImage = modal.querySelector('.modal-image');
            modalImage.style.transform = 'rotate(0deg)';
            modalImage.removeAttribute('data-rotation');
        }

        // Function to send image rotation request
        function sendRequest() {
            // Get the modal image source, file name, and degree of rotation
            const modalImage = modal.querySelector('.modal-image');
            const src = modalImage.getAttribute('src');
            const fileName = src.substring(src.lastIndexOf('/') + 1);

            // Get the degree of rotation
            let rotation = totalRotation;
            if (rotation === undefined) {
                rotation = 0;
            }

            // Send the data to the server using Ajax
            const xhr = new XMLHttpRequest();
            const url = 'https://srs-ssms.com/gudang/rotateImage.php';
            const params = `fileName=${fileName}&rotation=${rotation}`;
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);
                    // Hide the modal and reset the rotation
                    modal.style.display = 'none';
                    resetRotation();

                    // Remove the event listener to prevent multiple requests
                    saveButton.removeEventListener('click', sendRequest);
                    alert('Foto baru sudah tersimpan');
                    location.reload();
                } else if (xhr.readyState === 4 && xhr.status !== 200) {
                    console.log('Error:', xhr.statusText);
                }
            };
            xhr.send(params);
        }



    });
</script>