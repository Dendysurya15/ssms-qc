@include('layout/header')
<style>
    .table-wrapper {
        overflow-x: auto;
        /* margin: 0 auto; */
        margin-left: 10px;
        margin-right: 10px;
    }

    .my-table {
        border-collapse: collapse;
        width: 100%;
        text-align: center;
        margin-bottom: 2rem;
    }

    .my-table th,
    .my-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .my-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
</style>
<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Perumahan Afdeling</a>
                        <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Perumahan Estate</a>

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>SUMMARY SCORE PERUMAHAN AFDELING REGIONAL - I

                                </b></h5>
                        </div>
                        <div class="content">
                            <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                                <div class="row w-100">
                                    <div class="col-md-2 offset-md-8">
                                        {{csrf_field()}}
                                        <select class="form-control" id="afdreg">
                                            <option value="1" selected>Regional 1</option>
                                            <option value="2">Regional 2</option>
                                            <option value="3">Regional 3</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <select class="form-control" id="tahunafd">
                                            <option value="2023" selected>2023</option>
                                            <option value="2022">2022</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="btnShow">Show</button>
                            </div>
                        </div>



                        <div class="table-wrapper">
                            <table class="my-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">EST</th>
                                        <th rowspan="2">AFDELING</th>
                                        <th rowspan="2">Asisten</th>

                                        <th colspan="14" id="yearHeader" style="text-align: center;">2023</th>

                                    </tr>
                                    <tr id="month_header">
                                        <td id="January">Jan</td>
                                        <td id="February">Feb</td>
                                        <td id="March">Mar</td>
                                        <td id="April">Apr</td>
                                        <td id="May">May</td>
                                        <td id="June">Jun</td>
                                        <td id="July">Jul</td>
                                        <td id="August">Aug</td>
                                        <td id="September">Sep</td>
                                        <td id="October">October</td>
                                        <td id="November">Nov</td>
                                        <td id="December">December</td>
                                        <td id="Ave">Ave</td>
                                        <td id="Status">Status</td>
                                    </tr>

                                </thead>
                                <tbody id="data_afd">


                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
                        <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark">
                            <h5><b>SUMMARY SCORE PERUMAHAN ESTATE REGIONAL - I</b></h5>
                        </div>
                        <div class="d-flex justify-content-end mt-3 mb-2 ml-3 mr-3" style="padding-top: 20px;">
                            <div class="row w-100">
                                <div class="col-md-2 offset-md-8">
                                    {{csrf_field()}}
                                    <select class="form-control" id="estreg">
                                        <option value="1" selected>Regional 1</option>
                                        <option value="2">Regional 2</option>
                                        <option value="3">Regional 3</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    {{csrf_field()}}
                                    <select class="form-control" id="tahunest">
                                        <option value="2023" selected>2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary mb-3" style="float: right" id="btnShoWEst">Show</button>
                        </div>

                        <div class="table-wrapper">
                            <table class="my-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">UNIT KERJA</th>
                                        <th rowspan="2">KODE</th>
                                        <th rowspan="2">PIC</th>
                                        <th colspan="14" id="yearHeader2" style="text-align: center;">2023</th>

                                    </tr>
                                    <tr id="month_header2">
                                        <td id="Jan">Jan</td>
                                        <td id="Feb">Feb</td>
                                        <td id="Mar">Mar</td>
                                        <td id="Apr">Apr</td>
                                        <td id="May">May</td>
                                        <td id="Jun">Jun</td>
                                        <td id="Jul">Jul</td>
                                        <td id="Aug">Aug</td>
                                        <td id="Sept">Sep</td>
                                        <td id="Oct">October</td>
                                        <td id="Nov">Nov</td>
                                        <td id="Dec">December</td>
                                        <td id="Ave">Ave</td>
                                        <td id="Status">Status</td>
                                    </tr>
                                </thead>
                                <tbody id="data_est">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
@include('layout/footer')
<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        //untuk table etc
        getAFD()
        getEST()

    });

    document.getElementById('btnShow').onclick = function() {
        getAFD();
    }
    document.getElementById('btnShoWEst').onclick = function() {
        getEST();
    }

    function getAFD() {
        $('#data_afd').empty()
        var reg = '';
        var tahun = '';

        var reg = document.getElementById('afdreg').value;
        var tahun = document.getElementById('tahunafd').value;
        var _token = $('input[name="_token"]').val();
        document.getElementById('yearHeader').innerHTML = tahun;

        $.ajax({
            url: "{{ route('getAFD') }}",
            method: "GET",
            data: {
                reg: reg,
                tahun: tahun,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {


                var parseResult = JSON.parse(result)
                var bulan = Object.entries(parseResult['bulan'])
                var rekap = Object.entries(parseResult['afd_rekap'])


                // console.log(rekap);
                var afd_bulan = rekap
                // var tbody1 = document.getElementById('data_afd');
                var tbody1 = document.getElementById('data_afd');
                //         $('#thead1').empty()
                const header = Object.entries(parseResult['header_cell']);
                const header_head = Object.entries(parseResult['header_head']);
                // console.log(header_head);

                const head_year = document.getElementById('yearHeader');
                const colspanValue = header_head.find(entry => entry[0] === "head")[1];

                // Update the colspan attribute of the "yearHeader" element
                head_year.setAttribute("colspan", colspanValue);
                // Function to set the colspan for the month header cells
                function setColspanForMonths() {
                    const jan = document.getElementById('January');
                    const feb = document.getElementById('February');
                    const mar = document.getElementById('March');
                    const apr = document.getElementById('April');
                    const may = document.getElementById('May');
                    const June = document.getElementById('June');
                    const July = document.getElementById('July');
                    const August = document.getElementById('August');
                    const September = document.getElementById('September');
                    const October = document.getElementById('October');
                    const November = document.getElementById('November');
                    const December = document.getElementById('December');
                    // ... add the rest of the months

                    const monthHeaders = [jan, feb, mar, apr, may, June, July, August, September, October, November, December];

                    header.forEach((monthData, index) => {
                        const [monthId, colspanValue] = monthData;
                        const headerCell = monthHeaders[index];

                        if (headerCell) {
                            headerCell.colSpan = colspanValue > 1 ? colspanValue : 1;
                        } else {
                            console.error(`Header cell for month ${monthId} not found.`);
                        }
                    });
                }


                // Call the function to set the colspans
                setColspanForMonths();


                afd_bulan.forEach((element, index) => {
                    item1 = index + 1;
                    let estate = element[0];
                    let namaAFD = Object.keys(element[1]);

                    let allMonths = Object.keys(element[1][namaAFD[0]]); // Assuming all AFDs have the same months

                    namaAFD.forEach((key) => {
                        tr = document.createElement('tr');
                        let item0 = '-';
                        let item1 = estate;
                        let item2 = key;
                        let item3 = '-';


                        let items = [item0, item1, item2, item3, ];

                        // ... Your previous code ...

                        allMonths.forEach((month) => {
                            let monthData = element[1][key][month];
                            // console.log(element[1][key]);
                            if (monthData) {
                                for (let visit in monthData) {
                                    let skor_total = monthData[visit].skor_total;
                                    let est = monthData[visit].est;
                                    let afd = monthData[visit].afd;
                                    let date = monthData[visit].date;
                                    if (skor_total != 0) {
                                        let itemUrl = document.createElement('a');
                                        itemUrl.href = 'detailEmplashmend/' + est + '/' + afd + '/' + date;
                                        itemUrl.textContent = skor_total;

                                        let td = document.createElement('td');
                                        td.appendChild(itemUrl); // Append the anchor to the table cell

                                        items.push(td); // Push the table cell containing the anchor to the items array
                                    } else {
                                        items.push(skor_total);
                                    }
                                }
                            }
                        });

                        let column = 1; // Start column after the first three items
                        for (let j = 0; j < items.length; j++) {
                            let item = items[j];
                            let td = document.createElement('td');

                            column++;
                            if (item instanceof Node) { // Check if the item is a Node (e.g., a <td> element)
                                td.appendChild(item); // Append the item (which is a Node) to the table cell
                            } else {
                                td.innerText = item; // Otherwise, treat it as a regular string and set its text content
                            }
                            tr.appendChild(td); // Append the table cell to the table row
                        }

                        // ... Your remaining code ...

                        let item4 = 'Ave';
                        let item5 = 'GooD';
                        let td4 = document.createElement('td');
                        let td5 = document.createElement('td');
                        td4.innerText = item4;
                        td5.innerText = item5;
                        tr.appendChild(td4);
                        tr.appendChild(td5);

                        tbody1.appendChild(tr);
                    });
                });

            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }

    function getEST() {
        $('#data_est').empty()
        var reg = '';
        var tahun = '';

        var reg = document.getElementById('estreg').value;
        var tahun = document.getElementById('tahunest').value;
        var _token = $('input[name="_token"]').val();

        document.getElementById('yearHeader2').innerHTML = tahun;

        $.ajax({
            url: "{{ route('estAFD') }}",
            method: "GET",
            data: {
                reg: reg,
                tahun: tahun,
                _token: _token
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            success: function(result) {


                var parseResult = JSON.parse(result)
                var bulan = Object.entries(parseResult['bulan'])
                var rekap = Object.entries(parseResult['afd_rekap'])
                var rata_rata = Object.entries(parseResult['rata_rata'])

                var afd_bulan = rekap

                var tbody1 = document.getElementById('data_est');
                //         $('#thead1').empty()
                const header = Object.entries(parseResult['header_cell']);
                const header_head = Object.entries(parseResult['header_head']);
                // console.log(header_head);

                const head_year = document.getElementById('yearHeader2');
                const colspanValue = header_head.find(entry => entry[0] === "head")[1];

                // Update the colspan attribute of the "yearHeader" element
                head_year.setAttribute("colspan", colspanValue);



                function setColspanForMonths() {
                    const jan = document.getElementById('Jan');
                    const feb = document.getElementById('Feb');
                    const mar = document.getElementById('Mar');
                    const apr = document.getElementById('Apr');
                    const may = document.getElementById('May');
                    const June = document.getElementById('Jun');
                    const July = document.getElementById('Jul');
                    const August = document.getElementById('Aug');
                    const September = document.getElementById('Sept');
                    const October = document.getElementById('Oct');
                    const November = document.getElementById('Nov');
                    const December = document.getElementById('Dec');
                    // ... add the rest of the months

                    const monthHeaders = [jan, feb, mar, apr, may, June, July, August, September, October, November, December];

                    header.forEach((monthData, index) => {
                        const [monthId, colspanValue] = monthData;
                        const headerCell = monthHeaders[index];

                        if (headerCell) {
                            headerCell.colSpan = colspanValue > 1 ? colspanValue : 1;
                        } else {
                            console.error(`Header cell for month ${monthId} not found.`);
                        }
                    });
                }

                setColspanForMonths();
                console.log(rata_rata);
                afd_bulan.forEach((element, index) => {
                    item1 = index + 1;
                    let estate = element[0];
                    let namaAFD = Object.keys(element[1]);

                    let allMonths = Object.keys(element[1][namaAFD[0]]); // Assuming all AFDs have the same months
                    let rataRataElement = rata_rata[index];
                    let item4 = rataRataElement[1].avg; // Accessing 'afd' property from the second element of the array
                    let tr = document.createElement('tr'); // Create the tr element here


                    let item5;
                    if (item4 >= 95) {
                        tr.style.backgroundColor = "#0804fc";
                        item5 = 'mantap';
                    } else if (item4 >= 85 && item4 < 95) {
                        tr.style.backgroundColor = "#08b454";
                        item5 = 'bagus';
                    } else if (item4 >= 75 && item4 < 85) {
                        tr.style.backgroundColor = "#fffc04";
                        item5 = 'lumayan';
                    } else if (item4 >= 65 && item4 < 75) {
                        tr.style.backgroundColor = "#ffc404";
                        item5 = 'oke';
                    } else if (item4 === 0) {
                        tr.style.backgroundColor = "white";
                        item5 = 'sip';
                    } else {
                        tr.style.backgroundColor = "red";
                    }

                    namaAFD.forEach((key) => {
                        tr = document.createElement('tr');
                        let item0 = '-';
                        let item1 = estate;
                        let item2 = key;
                        let item3 = '-';


                        let items = [item0, item1, item2, item3, ];

                        // ... Your previous code ...

                        allMonths.forEach((month) => {
                            let monthData = element[1][key][month];
                            // console.log(element[1][key]);
                            if (monthData) {
                                for (let visit in monthData) {
                                    let skor_total = monthData[visit].skor_total;
                                    let est = monthData[visit].est;
                                    let afd = monthData[visit].afd;
                                    let date = monthData[visit].date;
                                    if (skor_total != 0) {
                                        let itemUrl = document.createElement('a');
                                        itemUrl.href = 'detailEmplashmend/' + est + '/' + afd + '/' + date;
                                        itemUrl.textContent = skor_total;

                                        let td = document.createElement('td');
                                        td.appendChild(itemUrl); // Append the anchor to the table cell

                                        items.push(td); // Push the table cell containing the anchor to the items array
                                    } else {
                                        items.push(skor_total);
                                    }
                                }
                            }
                        });

                        let column = 1; // Start column after the first three items
                        for (let j = 0; j < items.length; j++) {
                            let item = items[j];
                            let td = document.createElement('td');

                            column++;
                            if (item instanceof Node) { // Check if the item is a Node (e.g., a <td> element)
                                td.appendChild(item); // Append the item (which is a Node) to the table cell
                            } else {
                                td.innerText = item; // Otherwise, treat it as a regular string and set its text content
                            }
                            tr.appendChild(td); // Append the table cell to the table row
                        }





                        tbody1.appendChild(tr);
                    });


                    let td4 = document.createElement('td');
                    let td5 = document.createElement('td');
                    td4.innerText = item4;
                    td5.innerText = item5;
                    tr.appendChild(td4);
                    tr.appendChild(td5);



                    tbody1.appendChild(tr);

                });

            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }
</script>