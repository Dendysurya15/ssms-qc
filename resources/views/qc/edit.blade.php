@include('layout/header')
<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper p-4">
                <h2 style="color:#013C5E;font-weight: 550">Profile User
                </h2>
                <hr>
                @if($errors->any())
                <div id="boxAlert">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{$errors->first()}}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif
                @if(session()->has('message'))
                <div id="boxAlert">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong> {{ session()->get('message') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif
                <form action="{{route('update', ['id' => $data->user_id, 'lokasi_kerja'=> session('lok')]) }}"
                    method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="exampleInputEmail1">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" id="exampleInputEmail1"
                            aria-describedby="emailHelp" placeholder="Masukkan Nama Lengkap"
                            value="{{$data->nama_lengkap}}">

                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input type="text" class="form-control" name="email" id="exampleInputEmail1"
                            aria-describedby="emailHelp" placeholder="Masukkan Email" value="{{$data->email}}">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password baru</label>
                        <input type="text" name="password" class="form-control" id="exampleInputPassword1"
                            placeholder="Masukkan Password" value="{{$data->password}}">
                    </div>

                    <button type="submit" class="btn btn-success">Submit</button>
                    <a href="{{ route('user_qc', ['lokasi_kerja' => session('lok')])}}"
                        class=" btn btn-danger">Kembali</a>
                </form>
            </div>
        </div>
    </section>
</div>
@include('layout/footer')