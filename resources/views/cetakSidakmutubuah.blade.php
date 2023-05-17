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

    .custom-border {
        border: 1px solid #000;
        padding: 20px;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .container {

        display: flex;
        justify-content: center;
        margin-top: 3px;
        margin-bottom: 2px;

        margin-right: 3px;
        border: 1px solid #000;
    }

    .container h3 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .container table {
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
        margin: 0 auto;
    }

    .container td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    .container td img {
        max-width: 100%;
        height: auto;
    }

    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin-left: 13px;
    }
</style>


<body>

    <!-- here is head -->
    <div class="d-flex justify-content-center border border-dark " style="margin: 50px 50px 50px 50px; max-width: calc(100% - 100px);">
        <h2 class="text-center">MAIN ISSUE FOTO TEMUAN SIDAK PEMERIKSAAN MUTU BUAH DI TPH</h2>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: middle; padding-left: 50px; width: 10%;border:0;">
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
            <td style="vertical-align: middle; text-align: right;width:40%;border:0;padding-right: 50px;">
                <div class="right-container">
                    <div class="text-container">
                        <p style="text-align: right;">REGIONAL: {{ $data['regional']}}</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <!-- end head -->


    <!-- here is content -->
    <div class="d-flex justify-content-center border border-dark " style="margin: 50px 50px 50px 50px; max-width: calc(100% - 100px);">
        <table class="my-table">
            <thead>
                <tr style="text-align: center;border:#000">
                    <th colspan="12">FOTO</th>
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
                    <td colspan="4" class="align-middle" style="border-width: 1px;margin: 5px 5px 5px 5px; max-width: calc(100% - 100px);">
                        <div class="image-container">
                            <img src="https://mobilepro.srs-ssms.com/storage/app/public/qc/sidakMutuBuah/{{$items['temuan']}}" style="width:{{$imageWidth}};height:{{$imageHeight}}">
                            <div style="text-align: center;">{{$items['est']}}</div>
                            <div style="text-align: center;">{{$items['komen']}}</div>
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

    <!-- end content -->


</body>

</html>