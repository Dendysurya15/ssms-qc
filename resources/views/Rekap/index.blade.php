@include('layout/header')
<div class="content-wrapper">

    <nav>
        <div class="nav nav-tabs px-4" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Rekap</a>
            <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Data Perwilayah</a>

        </div>
    </nav>


    <div class="tab-content" id="nav-tabContent">
        <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
            <div class="row w-100">
                <div class="col-md-2 offset-md-8">
                    {{csrf_field()}}
                    <select class="form-control" id="regionalPanen">
                        @foreach($option_reg as $key => $item)
                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    {{csrf_field()}}
                    <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputbulan" id="inputbulan">
                </div>
            </div>
            <button class="btn btn-primary mb-4" style="float: right" id="btnShow">Show</button>
        </div>

        <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
            <div class="card  px-4">
                <div class="card header">
                </div>
                <div class="card body">

                    <p class="text-center mt-5">REKAPITULASI SKOR QC PANEN, SIDAK TPH (MUTU TRANSPORT) & SIDAK MUTU BUAH <span id="judtahun"> </span> </p>



                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab1s">
                            <div class="table-responsive">
                                <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table1">
                                    <thead>
                                        <tr>
                                            <th id="wil1" colspan="5" class="text-center bg-gradient-primary">Wilayah</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>Estate</th>
                                            <th>Afdeling</th>
                                            <th>Nama</th>
                                            <th>Skor</th>
                                            <th>Rank</th>
                                        </tr>
                                    </thead>
                                    <tbody id="week1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab2s">
                            <div class="table-responsive">
                                <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table2">
                                    <thead>
                                        <tr>
                                            <th id="wil2" colspan="5" class="text-center bg-gradient-primary">Wilayah</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>Estate</th>
                                            <th>Afdeling</th>
                                            <th>Nama</th>
                                            <th>Skor</th>
                                            <th>Rank</th>
                                        </tr>
                                    </thead>
                                    <tbody id="week2">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab3s">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 13px;background-color:white" id="table3">
                                    <thead>
                                        <tr>
                                            <th id="wil3" colspan="5" class="text-center bg-gradient-primary">Wilayah</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th>Estate</th>
                                            <th>Afdeling</th>
                                            <th>Nama</th>
                                            <th>Skor</th>
                                            <th>Rank</th>
                                        </tr>
                                    </thead>
                                    <tbody id="week3">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
            <div class="card  px-4">
                <div class="card header">
                </div>
                <div class="card body">
                    <p class="text-center mt-5">REKAPITULASI SKOR QC PANEN, SIDAK TPH (MUTU TRANSPORT) & SIDAK MUTU BUAH <span id="judtahunx"> </span> </p>

                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab1">
                            <div class="table-responsive">
                                <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table1">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="3">Afdeling</th>
                                            <th>Skor QC Panen</th>
                                            <th>Skor QC Sidak TPH</th>
                                            <th>Skor QC Mutu Buah</th>
                                        </tr>
                                        <tr id="thead1">
                                            <th>January</th>
                                            <th>January</th>
                                            <th>January</th>
                                        </tr>
                                    </thead>
                                    <tbody id="afd1">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab2">
                            <div class="table-responsive">
                                <table class=" table table-bordered" style="font-size: 13px;background-color:white" id="table2">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="3">Afdeling</th>
                                            <th>Skor QC Panen</th>
                                            <th>Skor QC Sidak TPH</th>
                                            <th>Skor QC Mutu Buah</th>
                                        </tr>
                                        <tr id="thead2">
                                            <th>January</th>
                                            <th>January</th>
                                            <th>January</th>
                                        </tr>
                                    </thead>
                                    <tbody id="afd2">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4" data-regional="1" id="Tab3">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 13px;background-color:white" id="table3">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="3">Afdeling</th>
                                            <th>Skor QC Panen</th>
                                            <th>Skor QC Sidak TPH</th>
                                            <th>Skor QC Mutu Buah</th>
                                        </tr>
                                        <tr id="thead3">
                                            <th>January</th>
                                            <th>January</th>
                                            <th>January</th>
                                        </tr>
                                    </thead>
                                    <tbody id="afd3">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>


@include('layout/footer')

<script>
    var lokasiKerja = "{{ session('lok') }}";
    let regs
    $(document).ready(function() {

        // console.log(lokasiKerja);
        if (lokasiKerja == 'Regional II' || lokasiKerja == 'Regional 2') {
            $('#regionalPanen').val('2');

            regs = 2
        } else if (lokasiKerja == 'Regional III' || lokasiKerja == 'Regional 3') {
            $('#regionalPanen').val('3');
            regs = 3
        } else if (lokasiKerja == 'Regional IV' || lokasiKerja == 'Regional 4') {
            $('#regionalPanen').val('4');
            regs = 4
        } else if (lokasiKerja == 'Regional I' || lokasiKerja == 'Regional 1') {
            $('#regionalPanen').val('1');
            regs = 1
        }
        getdata()


        fixtable(regs)
    });

    function resetClassList(element) {
        element.classList.remove("col-md-6", "col-lg-3", "col-lg-4", "col-lg-6");
        element.classList.add("col-md-6");
    }

    function fixtable(regs) {

        // console.log(regs);
        const s = document.getElementById("Tab1");
        const m = document.getElementById("Tab2");
        const l = document.getElementById("Tab3");
        const satu = document.getElementById("Tab1s");
        const dua = document.getElementById("Tab2s");
        const tiga = document.getElementById("Tab3s");

        const c = regs; // reg is already the number, no need for reg.value

        if (c === 1 || c === 2) {
            // console.log('Testing 1');
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            resetClassList(s);
            resetClassList(m);
            resetClassList(l);
            s.classList.add("col-lg-4");
            m.classList.add("col-lg-4");
            l.classList.add("col-lg-4");

            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "";
            resetClassList(satu);
            resetClassList(dua);
            resetClassList(tiga);
            satu.classList.add("col-lg-4");
            dua.classList.add("col-lg-4");
            tiga.classList.add("col-lg-4");
        } else if (c === 3 || c === 4) {
            // console.log('Testing 2');
            s.style.display = "";
            m.style.display = "";
            l.style.display = "none";
            resetClassList(s);
            resetClassList(m);
            s.classList.add("col-lg-6");
            m.classList.add("col-lg-6");

            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "none";
            resetClassList(satu);
            resetClassList(dua);
            satu.classList.add("col-lg-6");
            dua.classList.add("col-lg-6");
        }
    }


    const c = document.getElementById('btnShow');
    const o = document.getElementById('regionalPanen');
    const s = document.getElementById("Tab1");
    const m = document.getElementById("Tab2");
    const l = document.getElementById("Tab3");
    const satu = document.getElementById("Tab1s");
    const dua = document.getElementById("Tab2s");
    const tiga = document.getElementById("Tab3s");




    c.addEventListener('click', function() {
        const c = o.value;
        if (c === '1') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            resetClassList(s);
            resetClassList(m);
            resetClassList(l);
            s.classList.add("col-lg-4");
            m.classList.add("col-lg-4");
            l.classList.add("col-lg-4");

            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "";
            resetClassList(satu);
            resetClassList(dua);
            resetClassList(tiga);
            satu.classList.add("col-lg-4");
            dua.classList.add("col-lg-4");
            tiga.classList.add("col-lg-4");

        } else if (c === '2') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "";
            resetClassList(s);
            resetClassList(m);
            resetClassList(l); -
            s.classList.add("col-lg-4");
            m.classList.add("col-lg-4");
            l.classList.add("col-lg-4");
            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "";
            resetClassList(satu);
            resetClassList(dua);
            resetClassList(tiga);
            satu.classList.add("col-lg-4");
            dua.classList.add("col-lg-4");
            tiga.classList.add("col-lg-4");
        } else if (c === '3') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "none";
            resetClassList(s);
            resetClassList(m);
            s.classList.add("col-lg-6");
            m.classList.add("col-lg-6");

            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "none";
            resetClassList(satu);
            resetClassList(dua);
            satu.classList.add("col-lg-6");
            dua.classList.add("col-lg-6");

        } else if (c === '4') {
            s.style.display = "";
            m.style.display = "";
            l.style.display = "none";
            resetClassList(s);
            resetClassList(m);
            s.classList.add("col-lg-6");
            m.classList.add("col-lg-6");
            satu.style.display = "";
            dua.style.display = "";
            tiga.style.display = "none";
            resetClassList(satu);
            resetClassList(dua);
            satu.classList.add("col-lg-6");
            dua.classList.add("col-lg-6");


        }
    });
    document.getElementById('btnShow').onclick = function() {
        Swal.fire({
            title: 'Loading',
            html: '<span class="loading-text">Mohon Tunggu...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        getdata();
    }

    function setBackgroundColor(element, score) {
        if (score >= 95) {
            element.style.backgroundColor = "#609cd4";
        } else if (score >= 85) {
            element.style.backgroundColor = "#08b454";
        } else if (score >= 75) {
            element.style.backgroundColor = "#fffc04";
        } else if (score >= 65) {
            element.style.backgroundColor = "#ffc404";
        } else if (score === '-') {
            element.style.backgroundColor = "white";
        } else {
            element.style.backgroundColor = "red";
        }
        element.style.color = "black";
    }




    function getdata() {
        $('#afd1').empty()
        $('#afd2').empty()
        $('#afd3').empty()
        $('#week1').empty()
        $('#week2').empty()
        $('#week3').empty()
        var reg = document.getElementById('regionalPanen').value;
        var bulan = document.getElementById('inputbulan').value;
        var _token = $('input[name="_token"]').val();

        const dateParts = bulan.split('-'); // Split the string into parts
        const year = parseInt(dateParts[0]); // Extract the year
        const month = parseInt(dateParts[1]); // Extract the month

        // Creating a date object using the extracted year and month (assuming day is 01)
        const date = new Date(year, month - 1, 1);

        const monthName = date.toLocaleString('default', {
            month: 'long'
        });
        // console.log(monthName);

        const tableRows = ['thead1', 'thead2', 'thead3'];

        tableRows.forEach(rowId => {
            const tableRow = document.getElementById(rowId);
            const tableHeaders = tableRow.querySelectorAll('th');

            tableHeaders.forEach(header => {
                header.textContent = monthName;
            });
        });

        const inputYearMonth = bulan + '-01'; // Adding '-01' to make it 'YYYY-MM-01' for parsing as a date
        const inputDate = new Date(inputYearMonth);
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        const formattedDate = monthNames[inputDate.getMonth()] + ' ' + inputDate.getFullYear();

        var judul1 = document.getElementById('judtahun');
        var judul2 = document.getElementById('judtahunx');

        judul1.textContent = formattedDate;
        judul2.textContent = formattedDate;
        $.ajax({
            url: "{{ route('olahdata') }}",
            method: "GET",
            data: {
                reg: reg,
                bulan: bulan,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {
                Swal.close();

                var parseResult = JSON.parse(result)
                var rekapafd = Object.entries(parseResult['rekapafd'])
                // console.log(rekapafd);
                let table1 = rekapafd[0]
                let table2 = rekapafd[1]
                let table3 = rekapafd[2]

                function assignValue(checkValue, compareValue, assignIfEqual, assignIfNotEqual) {
                    return checkValue === compareValue ? assignIfEqual : assignIfNotEqual;
                }

                // console.log(table1);
                // untuk rekap 
                var title1 = document.getElementById('wil1');
                let key1 = table1[0];

                title1.textContent = 'Wilayah ' + key1;
                var trekap1 = document.getElementById('week1');
                Object.keys(table1[1]).forEach(key => {
                    Object.keys(table1[1][key]).forEach(subKey => {
                        let item1 = table1[1][key][subKey]['est'];
                        let item2 = table1[1][key][subKey]['afd'];
                        let item3 = table1[1][key][subKey]['nama']
                        let item4 = table1[1][key][subKey]['total'];
                        let item5 = '-';

                        let bg = table1[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');
                        let itemElement5 = document.createElement('td');



                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;
                        itemElement5.innerText = item5

                        setBackgroundColor(itemElement4, item4);
                        tr.style.backgroundColor = bg;

                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tr.appendChild(itemElement5)
                        trekap1.appendChild(tr);
                    });
                });
                var title2 = document.getElementById('wil2');
                let key2 = table2[0];

                title2.textContent = 'Wilayah ' + key2;
                var trekap2 = document.getElementById('week2');
                Object.keys(table2[1]).forEach(key => {
                    Object.keys(table2[1][key]).forEach(subKey => {
                        let item1 = table2[1][key][subKey]['est'];
                        let item2 = table2[1][key][subKey]['afd'];
                        let item3 = table2[1][key][subKey]['nama']
                        let item4 = table2[1][key][subKey]['total'];
                        let item5 = '-';

                        let bg = table2[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');
                        let itemElement5 = document.createElement('td');



                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;
                        itemElement5.innerText = item5

                        setBackgroundColor(itemElement4, item4);
                        tr.style.backgroundColor = bg;

                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tr.appendChild(itemElement5)
                        trekap2.appendChild(tr);
                    });
                });



                // untuk perwilayah 
                var tbody1 = document.getElementById('afd1');
                // console.log(table1);
                // Iterate through the main object keys (KNE, PLE, etc.)
                Object.keys(table1[1]).forEach(key => {
                    // Iterate through the nested objects (OA, OB, etc.) within each main key
                    Object.keys(table1[1][key]).forEach(subKey => {
                        let item1 = table1[1][key][subKey]['afd'];


                        const kosong = 'kosong';

                        let item2 = assignValue(
                            table1[1][key][subKey]['qc_check'],
                            kosong,
                            '-',
                            table1[1][key][subKey]['skor_qc']
                        );

                        let item3 = assignValue(
                            table1[1][key][subKey]['tph_check'],
                            kosong,
                            '-',
                            table1[1][key][subKey]['skor_tph']
                        );

                        let item4 = assignValue(
                            table1[1][key][subKey]['buah_check'],
                            kosong,
                            '-',
                            table1[1][key][subKey]['skor_buah']
                        );





                        let bg = table1[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');

                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;

                        // Set background color style to the table row
                        tr.style.backgroundColor = bg;
                        setBackgroundColor(itemElement2, item2);
                        setBackgroundColor(itemElement3, item3);
                        setBackgroundColor(itemElement4, item4);
                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tbody1.appendChild(tr);
                    });

                });
                var tbody2 = document.getElementById('afd2');

                // Iterate through the main object keys (KNE, PLE, etc.)
                Object.keys(table2[1]).forEach(key => {
                    // Iterate through the nested objects (OA, OB, etc.) within each main key
                    Object.keys(table2[1][key]).forEach(subKey => {
                        let item1 = table2[1][key][subKey]['afd'];
                        const kosong = 'kosong';

                        let item2 = assignValue(
                            table2[1][key][subKey]['qc_check'],
                            kosong,
                            '-',
                            table2[1][key][subKey]['skor_qc']
                        );

                        let item3 = assignValue(
                            table2[1][key][subKey]['tph_check'],
                            kosong,
                            '-',
                            table2[1][key][subKey]['skor_tph']
                        );

                        let item4 = assignValue(
                            table2[1][key][subKey]['buah_check'],
                            kosong,
                            '-',
                            table2[1][key][subKey]['skor_buah']
                        );

                        let bg = table2[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');

                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;

                        // Set background color style to the table row
                        tr.style.backgroundColor = bg;
                        setBackgroundColor(itemElement2, item2);
                        setBackgroundColor(itemElement3, item3);
                        setBackgroundColor(itemElement4, item4);
                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tbody2.appendChild(tr);
                    });
                });
                var tbody3 = document.getElementById('afd3');

                // Iterate through the main object keys (KNE, PLE, etc.)
                Object.keys(table3[1]).forEach(key => {
                    // Iterate through the nested objects (OA, OB, etc.) within each main key
                    Object.keys(table3[1][key]).forEach(subKey => {
                        let item1 = table3[1][key][subKey]['afd'];
                        const kosong = 'kosong';

                        let item2 = assignValue(
                            table3[1][key][subKey]['qc_check'],
                            kosong,
                            '-',
                            table3[1][key][subKey]['skor_qc']
                        );

                        let item3 = assignValue(
                            table3[1][key][subKey]['tph_check'],
                            kosong,
                            '-',
                            table3[1][key][subKey]['skor_tph']
                        );

                        let item4 = assignValue(
                            table3[1][key][subKey]['buah_check'],
                            kosong,
                            '-',
                            table3[1][key][subKey]['skor_buah']
                        );
                        let bg = table3[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');

                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;

                        // Set background color style to the table row
                        tr.style.backgroundColor = bg;
                        setBackgroundColor(itemElement2, item2);
                        setBackgroundColor(itemElement3, item3);
                        setBackgroundColor(itemElement4, item4);
                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tbody3.appendChild(tr);
                    });
                });
                var title3 = document.getElementById('wil3');
                let key = table3[0]; // Assuming table3 is defined elsewhere

                title3.textContent = 'Wilayah ' + key;
                // console.log(key);

                // console.log(table3);

                var trekap3 = document.getElementById('week3');
                Object.keys(table3[1]).forEach(key => {
                    Object.keys(table3[1][key]).forEach(subKey => {
                        let item1 = table3[1][key][subKey]['est'];
                        let item2 = table3[1][key][subKey]['afd'];
                        let item3 = table3[1][key][subKey]['nama']
                        let item4 = table3[1][key][subKey]['total'];
                        let item5 = '-';

                        let bg = table3[1][key][subKey]['bgcolor'];

                        // Create table row and cell for each 'total' value
                        let tr = document.createElement('tr');
                        let itemElement1 = document.createElement('td');
                        let itemElement2 = document.createElement('td');
                        let itemElement3 = document.createElement('td');
                        let itemElement4 = document.createElement('td');
                        let itemElement5 = document.createElement('td');



                        itemElement1.classList.add("text-center");
                        itemElement1.innerText = item1;
                        itemElement2.innerText = item2;
                        itemElement3.innerText = item3;
                        itemElement4.innerText = item4;
                        itemElement5.innerText = item5

                        setBackgroundColor(itemElement4, item4);
                        tr.style.backgroundColor = bg;

                        tr.appendChild(itemElement1)
                        tr.appendChild(itemElement2)
                        tr.appendChild(itemElement3)
                        tr.appendChild(itemElement4)
                        tr.appendChild(itemElement5)
                        trekap3.appendChild(tr);
                    });
                });



            },
            error: function(xhr, status, error) {
                // Handle the error, if any
                console.error(xhr.responseText);
            }
        });

    }
    // $('#btnShow').click(function() {


    // });
</script>