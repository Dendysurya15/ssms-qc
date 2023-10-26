@include('layout/header')

<div class="content-wrapper">
    <section class="content"><br>
        <div class="container-fluid">
            <div class="card table_wrapper">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-utama-tab" data-toggle="tab" href="#nav-utama" role="tab" aria-controls="nav-utama" aria-selected="true">Absensi</a>
                        <a class="nav-item nav-link" id="nav-data-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false">Foto Bukti</a>

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-utama" role="tabpanel" aria-labelledby="nav-utama-tab">
                        <div class="mt-3 mb-2 ml-3 mr-3">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div style=" display: flex; justify-content: flex-start;padding-bottom:20px">
                                        <button class="btn btn-primary">Download PDF</button>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                                        {{ csrf_field() }}
                                        <select name="regional" id="regional" style="height:37px;width:auto">
                                            <option value="1">Regional 1</option>
                                            <option value="2">Regional 2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div style=" display: flex; justify-content: flex-end;padding-bottom:20px;">
                                        {{ csrf_field() }}
                                        <input class="form-control" value="{{ date('Y-m') }}" type="month" name="inputDateMonth" id="inputDateMonth">
                                    </div>
                                </div>

                            </div>
                            <style>
                                .table-container {
                                    max-height: 400px;
                                    /* Set your desired maximum height */
                                    overflow-y: auto;
                                }
                            </style>
                            <div class="table-container">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" colspan="34" style="text-align: center;">ABSENSI KEHADIRAN QC</th>

                                        </tr>
                                        <tr>
                                            <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">NAMA</th>

                                            <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">PAYROLL</th>
                                        </tr>
                                        <tr>
                                            <th scope="col" colspan="31" style="text-align: center;" id="header_month">{{$header_month}}</th>

                                        </tr>
                                        <tr id="dates-container">
                                            <!-- <th scope="col" rowspan="3" style="text-align: center; vertical-align: middle;">Total</th> -->
                                        </tr>


                                    </thead>

                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>

                    <!-- tab 2  -->
                    <div class=" tab-pane fade" id="nav-data" role="tabpanel" aria-labelledby="nav-data-tab">
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

                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        {{csrf_field()}}
                                        <select class="form-control" id="tahunafd">

                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary mb-3" style="float: right" id="btnShow">Show</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>


@include('layout/footer')

<script>
    $(document).ready(function() {
        var reg = document.getElementById('regional');
        var tahun = document.getElementById('inputDateMonth');

        // Function to handle the change event for both elements
        function handleFiltersChange() {
            // Get the selected values
            var selectedRegional = reg.value;
            var selectedDateMonth = tahun.value;

            // Perform your AJAX request here with the selected values
            // Example:
            $.ajax({
                type: 'get',
                url: "{{ route('absensidata') }}",
                data: {
                    regional: selectedRegional,
                    dateMonth: selectedDateMonth
                },
                success: function(data) {
                    // Handle the response data
                    var header_month = data.header_month; // Access the header_month directly
                    var dates = data.dates;
                    var JumlahBulan = data.JumlahBulan;

                    var header_table = document.getElementById('header_month');

                    // console.log(dates);
                    // Set its text content with the value you want (as a string)
                    header_table.textContent = header_month;

                    // Set the colspan attribute based on the 'dates' value
                    header_table.setAttribute('colspan', JumlahBulan);


                    // Get the table row where you want to add the <th> elements
                    var datesContainer = document.getElementById("dates-container");

                    let addPlus = parseFloat(JumlahBulan) + 1;
                    datesContainer.innerHTML = '';

                    // Loop from 1 to 31 and create <th> elements for each number
                    for (var i = 1; i <= addPlus; i++) {
                        var th = document.createElement("th");
                        th.setAttribute("scope", "col");
                        if (i < addPlus) {
                            th.textContent = i;
                        } else {
                            th.textContent = "Total";
                        }
                        datesContainer.appendChild(th);
                    }


                },

            });

            // You can put your AJAX request code here
        }

        // Add event listeners to trigger the function when the values change
        reg.addEventListener('change', handleFiltersChange);
        tahun.addEventListener('change', handleFiltersChange);

        // Trigger the function when the document is ready
        handleFiltersChange();
    });
</script>