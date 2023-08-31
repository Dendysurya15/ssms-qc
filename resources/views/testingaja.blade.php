    function fetchAndUpdateData() {
    lottieAnimation.play(); // Start the Lottie animation
    lottieContainer.style.display = 'block'; // Display the Lottie container

    dataTableAncakTest.clear().draw();

    var Tanggal = document.getElementById('inputDate').value;
    var est = document.getElementById('est').value;
    var afd = document.getElementById('afd').value;
    var _token = $('input[name="_token"]').val();

    $.ajax({
    url: "{{ route('filterDataDetail') }}",
    method: "GET",
    data: {
    Tanggal,
    est,
    afd,
    _token: _token
    },
    success: function(result) {
    lottieAnimation.stop(); // Stop the Lottie animation
    lottieContainer.style.display = 'none'; // Hide the Lottie container

    var dataTableAncakTest = $('#mutuAncakTable').DataTable({
    columns: [{
    title: 'ID',
    data: 'id'
    },
    {
    title: 'Estate',
    data: 'estate'
    },
    {
    title: 'Afdeling',
    data: 'afdeling'
    },
    {
    title: 'Blok',
    data: 'blok'
    },
    {
    title: 'Blok',
    data: 'petugas'
    },
    {
    title: 'Blok',
    data: 'datetime'
    },
    {
    title: 'luas blok',
    data: 'luas_blok',

    },
    {
    title: 'Sph',
    data: 'sph',

    },
    {
    title: 'Baris 1',
    data: 'br1',

    },
    {
    title: 'Baris 2',
    data: 'br2',

    },
    {
    title: 'Jalur masuk',
    data: 'jalur_masuk',
    },
    {
    title: 'Status Panen',
    data: 'status_panen',
    },
    {
    title: 'Kemandoran',
    data: 'kemandoran',
    },
    {
    title: 'Ancak Pemanen',
    data: 'ancak_pemanen',
    },
    {
    title: 'Pokok Panen',
    data: 'pokok_panen',

    },
    {
    title: 'Pokok Sample',
    data: 'sample',

    },
    {
    title: 'Janjang Panen',
    data: 'jjg',

    },
    {
    title: 'Brondolan (P)',
    data: 'brtp',

    },
    {
    title: 'Brondolan (K)',
    data: 'brtk',

    },
    {
    title: 'Brondolan (GL)',
    data: 'brtgl',

    },
    {
    title: 'Buah Tinggal (S)',
    data: 'bhts',

    },
    {
    title: 'Buah Tinggal (M1)',
    data: 'bhtm1',

    },
    {
    title: 'Buah Tinggal (M2)',
    data: 'bhtm2',

    },
    {
    title: 'Buah Tinggal (M3)',
    data: 'bhtm3',

    },
    {
    title: 'Pelepah Sengkleh',
    data: 'ps',

    },
    {
    title: 'Frond Stacking',
    data: 'sp',

    },
    {
    title: 'Piringan Semak',
    data: 'piringan_semak',

    },
    {
    title: 'Pokok Kuning',
    data: 'pokok_kuning',

    },
    {
    title: 'Underpruning',
    data: 'underpruning',

    },
    {
    title: 'Overpruning',
    data: 'overpruning',

    },
    {
    title: 'Blok',
    data: 'app_version',

    },
    {
    // -1 targets the last column
    title: 'Actions',
    visible: (currentUserName === 'Askep' || currentUserName === 'Manager'),
    render: function(data, type, row, meta) {
    var buttons =
    '<button class="edit-btn">Edit</button>' +
    '<button class="delete-btn">Delete</button>';
    return buttons;
    }
    }
    ],
    });

    dataTableAncakTest.rows.add(parseResult['mutuAncak']);
    dataTableAncakTest.draw();


    // Attach event handlers to dynamically created buttons
    $('#mutuAncakTable').on('click', '.edit-btn', function() {
    var rowData = dataTableAncakTest.row($(this).closest('tr')).data();
    var rowIndex = dataTableAncakTest.row($(this).closest('tr')).index();
    editRow(rowIndex);
    });

    $('#mutuAncakTable').on('click', '.delete-btn', function() {
    var rowIndex = dataTableAncakTest.row($(this).closest('tr')).index();
    deleteRow(rowIndex);
    });

    },
    error: function() {
    lottieAnimation.stop(); // Stop the Lottie animation
    lottieContainer.style.display = 'none'; // Hide the Lottie container
    }
    });




    }

    function Show() {
    fetchAndUpdateData();
    getmaps();
    getDataDay();
    }