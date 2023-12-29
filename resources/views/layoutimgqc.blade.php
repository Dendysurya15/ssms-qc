<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Include Bootstrap CSS -->
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.0/jszip.min.js"></script>

<body>
    <style>
        #capturePage {
            display: none;
            /* Hide the button by default */
        }
    </style>

    <button id="capturePage">Capture Page</button>

    <div class="row">
        <div class="col-lg-12" style="border: 10px;background-color:#C6DFB6">
            <h1 style="text-align: center;">REKAPITULASI RANKING NILAI-{{$title}}</h1>
        </div>

        <div class="col-lg-4 text-center" style="padding-top: 10px;"> <!-- Center the content within the column -->
            <img src="data:image/jpeg;base64, {{ $tables['table1'] }}" alt="img1" style="max-width: 100%; height: auto;">
        </div>
        <div class="col-lg-4 text-center" style="padding-top: 10px;"> <!-- Center the content within the column -->
            <img src="data:image/jpeg;base64, {{ $tables['table2'] }}" alt="img2" style="max-width: 100%; height: auto;">
        </div>
        <div class="col-lg-4 text-center" style="padding-top: 10px;"> <!-- Center the content within the column -->
            <img src="data:image/jpeg;base64, {{ $tables['table3'] }}" alt="img3" style="max-width: 100%; height: auto;">
        </div>

        <div class="col-lg-12 text-center" style="padding-top: 10px;"> <!-- Center the content within the column -->
            <img src="data:image/jpeg;base64, {{ $tables['table4'] }}" alt="img4" style="max-width: 100%; height: auto;">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

<script>
    var date = <?php echo json_encode($date); ?>;
    var reg = <?php echo json_encode($reg); ?>;
    var href = <?php echo json_encode($href); ?>;
    var title = <?php echo json_encode($title); ?>;

    function captureAndDownloadPage() {
        const pageContent = document.body;

        html2canvas(pageContent).then(canvas => {
            const dataURL = canvas.toDataURL('image/jpeg');

            // Create an anchor element for download
            const downloadLink = document.createElement('a');
            downloadLink.href = dataURL;
            downloadLink.download = `REKAPITULASI RANKING NILAI ${title}-${date}-${reg}.jpg`; // Dynamically set the filename

            // Simulate a click on the anchor element to trigger the download
            downloadLink.click();

            // Define the URL where you want to redirect based on the href variable
            const newPageUrl = `${href}`; // Concatenating rootUrl and href

            // Redirect to the new URL
            window.location.href = newPageUrl;
        });
    }


    // Trigger the function immediately after the page loads
    captureAndDownloadPage();
</script>

</html>