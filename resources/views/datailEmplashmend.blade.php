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
</style>


<div class="content-wrapper">
    <div class="card table_wrapper">
        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
            <h2>Pemeriksaan Perumahan</h2>
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


                    <div class="afd mt-2"> ESTATE/ AFD : {{$est}}-{{$afd}}</div>
                    <div class="afd">TANGGAL : <span id="selectedDate">{{ $tanggal }}</span></div>
                </div>
            </div>
        </div>
        <br>
        <div class="card table_wrapper">
            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                <h2>FOTO</h2>
            </div>
            <div class="container mt-3 mb-3">
                @if (!empty($rmh))
                <h1 class="text-center">Foto Temuan</h1>
                @foreach ($rmh as $key => $item)
                @foreach ($item as $items)
                @foreach ($items as $items1)
                @php
                $fotoKeys = preg_grep('/^foto_temuan_rmh\d+$/', array_keys($items1));
                $fotoKeys = array_slice($fotoKeys, 0, 14); // Take the first 14 items
                $index = 1; // Initialize the index as 1
                @endphp

                <div class="row">

                    @foreach ($fotoKeys as $fotoKey)
                    @php
                    $komentarKey = 'komentar_rmh' . $index;
                    $index++;
                    @endphp
                    <div class="col-4">
                        <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/perumahan/{{ $items1[$fotoKey] }}" alt="Foto {{ $fotoKey }}" class="img-thumbnail">
                        <p class="text-center mt-3" style="font-weight: bold">{{ $items1['est_afd'] }} - {{ $items1[$komentarKey] }}</p>
                    </div>
                    @endforeach
                </div>
                @endforeach
                @endforeach
                @endforeach
                @endif
            </div>




        </div>

    </div>
    @include('layout/footer')



    <script>

    </script>