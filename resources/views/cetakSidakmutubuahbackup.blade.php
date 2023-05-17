<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<style>
    /* The rest of your CSS */
    .sticky-footer {
        margin-top: auto;
        /* Push the footer to the bottom */
    }

    .header {
        display: flex;
        align-items: center;
    }

    .text-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-left: 5px;
    }

    .logo-container {
        display: flex;
        align-items: center;
    }

    .logo {
        height: 60px;
        width: auto;
        align-items: flex-start;
    }

    .pt-name,
    .qc-name {
        margin: 0;
        padding-left: 1px;
    }

    .text-container {
        margin-left: 15px;
    }

    .right-container {
        text-align: right;
    }

    .form-inline {
        display: flex;
        align-items: center;
    }

    .table-1-no-border td {
        border: none;
    }

    .page-break {
        page-break-before: always;
    }

    .my-table {
        <?php $count = count($data['temuan']); ?>width: <?php echo ($count == 1 || $count == 2) ? 'fit-content' : '100%'; ?>;
        border-collapse: collapse;
        margin: 0 auto;
    }

    .my-table th,
    .my-table td {
        text-align: center;
        border: 1px solid black;
        padding: 5px;
    }

    .my-table tbody tr {
        height: 100%;
    }

    .my-table tbody td {
        <?php $count = count($data['temuan']); ?>width: <?php echo ($count == 1 || $count == 2) ? '50%' : '33.333%'; ?>;
    }

    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
</style>


<body>

    <div class="content-wrapper">
        <div class="content-inner">
            <style>
                .custom-border {
                    border: 1px solid #000;
                    padding: 20px;
                    margin-top: 50px;
                    margin-bottom: 50px;
                }
            </style>
            <!-- header -->
            <div class="header">
                <div class="d-flex justify-content-center custom-border">
                    <h2 class="text-center">MAIN ISSUE FOTO TEMUAN SIDAK PEMERIKSAAN MUTU BUAH DI TPH</h2>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                            <div>
                                <img src="{{ asset('img/logo-SSS.png') }}" style="height:60px">
                            </div>
                        </td>
                        <td style="width:30%;border:0;">

                            <p style="text-align: left;">PT. SAWIT SUMBERMAS SARANA,TBK</p>
                            <p style="text-align: left;">QUALITY CONTROL</p>

                        </td>
                        <td style=" width: 20%;border:0;">
                        </td>
                        <td style="vertical-align: middle; text-align: right;width:40%;border:0;">
                            <div class="right-container">
                                <div class="text-container">

                                    <p style="text-align: right;">REGIONAL: {{ $data['regional']}}</p>


                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- endheader -->

            <div class="d-flex justify-content-center mt-3 mb-2 ml-3 mr-3 border border-dark ">
                <div class="Wraping">
                    <table class="my-table">
                        <thead>
                            <tr>
                                <th colspan="9">FOTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 0;
                            $count = count($data['temuan']);
                            $imageWidth = ($count == 1) ? '500pt' : '350pt';
                            $imageHeight = ($count == 1) ? '500pt' : '350pt';
                            ?>
                            <tr>
                                @foreach($data['temuan'] as $key => $items)
                                <?php $counter++; ?>
                                <td class="align-middle">
                                    <div class="image-container">
                                        <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/{{$items['temuan']}}" style="width:{{$imageWidth}};height:{{$imageHeight}}">
                                        <div>{{$items['est']}}</div>
                                        <div>{{$items['komen']}}</div>
                                    </div>
                                </td>

                                @if($counter % 3 === 0 && $counter !== count($data['temuan']))
                            </tr>
                            <tr>
                                @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="page-break"></div>
        </div>
    </div>

</body>

</html>