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
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
    }

    .my-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .my-table th {
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 15px;
    }

    .my-table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    th,
    td {
        border: 1px solid black;
        text-align: center;
        padding: 2px;
    }

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

    .custom-tables-container {
        display: flex;
        justify-content: space-between;
    }

    .custom-table {
        border-collapse: collapse;
        width: 45%;
    }

    .custom-table,
    .custom-table th,
    .custom-table td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }



    .table-1-no-border td {
        border: none;
    }

    .content-wrapper {

        padding: 30px;
        box-sizing: border-box;
    }
</style>

<body>
    <?php
    function checkImageExists($imageUrl)
    {
        $headers = @get_headers($imageUrl);
        return (is_array($headers) && strpos($headers[0], '200 OK') !== false);
    }
    ?>

    <div class="content-wrapper">

        <!-- -- -->

        <style>
            .custom-border {
                border: 1px solid #000;
                padding: 20px;
                margin-top: 50px;
                margin-bottom: 50px;
            }
        </style>

        <div class="d-flex justify-content-center custom-border">
            <h2 class="text-center">PEMERIKSAAN KUALITAS PANEN PERTAHUN</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; padding-left: 0; width: 10%;border:0;">
                    <div>
                        <img src="{{ asset('img/Logo-SSS.png') }}" style="height:60px">
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
                            <div class="afd">ESTATE :{{$estData}} </div>

                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <style>
            .Wraping {
                width: 100%;
                overflow-x: auto;
            }

            .my-table {
                width: 100%;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
            }

            .my-table th,
            .my-table td {
                border: 1px solid #e0e0e0;
                padding: 8px;
                text-align: center;
            }

            .my-table th {
                background-color: #f5f5f5;
                font-weight: bold;
            }

            .my-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .my-table tr:hover {
                background-color: #f0f0f0;
            }

            .my-table img {
                width: 100px;
                height: 100px;
                object-fit: contain;
                border-radius: 5px;
            }

            .status-cell {
                font-weight: bold;
                color: white;
            }

            .status-tuntas {
                background-color: #4caf50;
            }

            .status-berkelanjutan {
                background-color: #ffc107;
            }

            .status-belum-tuntas {
                background-color: #f44336;
            }

            .Wraping {
                text-align: center;
                /* Center horizontally */
                display: flex;
                align-items: center;
                /* Center vertically */
                justify-content: center;
                /* Center horizontally within the container */
                height: 100%;
                /* Ensure the container covers the entire parent */
            }

            .Wraping img {
                max-width: 100%;
                /* Ensure the image doesn't overflow the container */
                max-height: 100%;
                /* Ensure the image doesn't overflow the container */
            }
        </style>



        <div class="d-flex justify-content-center align-items-center" style="margin-top: 20px;">
            <div class="Wraping">
                <img src="{{ asset('img_result/' . $url) }}" alt="" style="height: 1800px; width:2100px">
            </div>
        </div>


        <div style="clear:both;"></div>
    </div>


</body>

</html>