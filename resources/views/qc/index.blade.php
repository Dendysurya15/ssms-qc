@include('layout/header')

<div class="content-wrapper">

    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper p-4">
                <h2 style="color:#013C5E;font-weight: 550">Semua User QC
                </h2>
                <hr>
                <div class="col-2 mb-3" style="color: white">
                    <a href="{{ route('create')}}" class="btn btn-success"><i class="nav-icon fa-solid fa-plus"></i>
                        Tambah User</a>
                </div>

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
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $user)
                        <tr>
                            <td>{{$user->nama_lengkap}}</td>
                            <td>{{$user->email}}</td>
                            <td style="display: inline-flex">

                                <a href="{{ route('edit', ['id' => $user->user_id])}}" class="btn btn-success mr-2"><i class="nav-icon fa-solid fa-edit"></i></a>
                                <form action="{{ route('delete', ['id' => $user->user_id])}}" method="POST">{{
                                    csrf_field() }}
                                    <input type="hidden" name="id" value="{{$user->user_id}}"><button type="submit" class="btn btn-danger"><i class="nav-icon fa-solid fa-trash"></i></button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@include('layout/footer')

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $('#boxAlert').click(function() {
        $('#boxAlert').attr('hidden', true);
    })
</script>