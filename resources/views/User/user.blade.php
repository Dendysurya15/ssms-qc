@include('layout/header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<div class="content-wrapper">
    <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
        <h5><b>Informasi User QC
            </b></h5>
    </div>

    @if(session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
    @endif

    <div class="row mt-3 mb-2 ml-3 mr-3">
        <div class="col-md-12 col-md-offset-2">
            <div class="card">
                <div class="card-header text-center">PROFILE</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama :</label>
                                <p id="nama_lengkap"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email:</label>
                                <p id="email"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Departemen:</label>
                                <p id="departemen"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jabatan:</label>
                                <p id="jabatan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Afdeling:</label>
                                <p id="afdeling"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No HP:</label>
                                <p id="no_hp"></p>
                            </div>
                        </div>
                    </div>
                    <!-- Other fields -->



                    @if (session('jabatan') == 'Manager' || session('jabatan') == 'Askep' || session('jabatan') == 'Asisten')
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                        Edit Profile
                    </button>
                    <div class="d-flex justify-content-center mt-3 mb-2 border border-dark">
                        <h5><b>Akses Khusus QC
                            </b></h5>
                    </div>

                    <button class="btn btn-primary mb-2" style="float: right;" data-toggle="modal" data-target="#addDataModal">Tambah Data</button>


                    <table id="listAsisten" class="table-striped text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Estate</th>
                                <th>Afdeling</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($asisten as $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->nama }}</td>
                                <td>{{ $value->est }}</td>
                                <td>{{ $value->afd }}</td>
                                <td style="display: inline-flex">
                                    <button class="btn btn-success mr-2" data-toggle="modal" data-target="#updateModal-{{$value->id}}"><i class="nav-icon fa-solid fa-edit"></i></button>


                                    <form action="{{ route('deleteAsisten') }}" method="POST">{{ csrf_field() }}
                                        <input type="hidden" name="id" value="{{ $value->id }}"><button type="submit" class="btn btn-danger" onclick="return confirm('Yakin menghapus data?')"><i class="nav-icon fa-solid fa-trash"></i></button>
                                    </form>
                                </td>

                                <div class="modal fade" id="updateModal-{{$value->id}}" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('updateAsisten') }}" method="POST">
                                                {{ csrf_field() }}
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateModalLabel">Update Asisten</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="{{ $value->id }}">
                                                    <div class="form-group">
                                                        <label for="nama">Nama</label>
                                                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $value->nama }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="est">Estate</label>
                                                        <input type="text" class="form-control" id="est" name="est" value="{{ $value->est }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="afd">Afdeling</label>
                                                        <input type="text" class="form-control" id="afd" name="afd" value="{{ $value->afd }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add Data Modal -->
                                <!-- Add Data Modal -->
                                <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('storeAsisten') }}" method="POST">
                                                {{ csrf_field() }}
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addDataModalLabel">Tambah Data Asisten</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="nama">Nama</label>
                                                        <input type="text" class="form-control" id="nama" name="nama" value="" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="est">Estate</label>
                                                        <input type="text" class="form-control" id="est" name="est" value="" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="afd">Afdeling</label>
                                                        <input type="text" class="form-control" id="afd" name="afd" value="" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Tambah Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Add Data Modal -->

                                <!-- End of Add Data Modal -->


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
<div class=" modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editProfileForm" action="{{ route('update_user') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" class="form-control" id="userqc_id" value="" required>

                    <div class="form-group">
                        <label for="edit-name">Name</label>
                        <input type="text" name="name" class="form-control" id="edit-name" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="text" name="email" class="form-control" id="edit-email" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-dept">Departemen</label>
                        <input type="text" name="departemen" class="form-control" id="edit-dept" value="">
                    </div>
                    <!-- <div class="form-group">
                        <label for="edit-jabatan">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" id="edit-jabatan" value="">
                    </div>
                    <div class="form-group">
                        <label for="edit-afdeling">Afdeling</label>
                        <input type="text" name="afdeling" class="form-control" id="edit-afdeling" value="">
                    </div> -->

                    <!-- <div class="form-group">
                        <label for="edit-nohp">No Hp</label>
                        <input type="text" name="nohp" class="form-control" id="edit-nohp" value="">
                    </div> -->
                    <div class="form-group">
                        <label for="edit-pass">Password</label>
                        <input type="text" name="pass" class="form-control" id="edit-pass" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


@include('layout/footer')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        if ($('.alert-success').length) {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 500);

        }
        $('#listAsisten').DataTable();
    });

    function getUserProfile(user_id) {
        $.ajax({
            url: '/getuser',
            type: 'post',
            data: {
                user_id: user_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                var user = Object.entries(response['user']);
                console.log(user);
                // Loop through the user data array
                user.forEach(function(entry) {
                    var key = entry[0];
                    var value = entry[1];

                    // Check if there's an element with the ID equal to the key
                    var element = document.getElementById(key);

                    if (element) {
                        // Populate the element with the corresponding value
                        element.textContent = value;
                    }
                });

                // Set the user_id in the hidden input field
                $('#userqc_id').val(user_id);
                $('#edit-name').val(response['user']['nama_lengkap']); // Replace 'nama_lengkap' with the correct key for the user's name
                $('#edit-email').val(response['user']['email']);
                $('#edit-dept').val(response['user']['departemen']);
                $('#edit-jabatan').val(response['user']['jabatan']);
                $('#edit-afdeling').val(response['user']['afdeling']);
                $('#edit-nohp').val(response['user']['no_hp']);
                $('#edit-pass').val(response['user']['password']);
            },
            error: function() {
                // Handle errors
                console.error('Error fetching user profile');
            }
        });
    }


    var user_id = "{{ session('user_id') }}";
    getUserProfile(user_id);

    // console.log(user_id);
</script>