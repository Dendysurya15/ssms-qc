<footer class="main-footer">
    <strong>Copyright © 2021-2026 <a href="https://srs-ssms.com">SRS-SSMS.COM</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 4.0.1
    </div>
</footer>

<!-- Add the necessary JavaScript files for the AdminLTE template -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/adminlte.min.js') }}"></script>

<!-- Other JavaScript files -->
<script src="{{ asset('js/js_tabel/jquery-3.5.1.js') }}"></script>
<script src="{{ asset('js/js_tabel/jquery.dataTables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentUrl = window.location.href;
        var navLinks = document.querySelectorAll('.nav-link');

        // Define the mapping of URL segments to classes
        var urlClassMapping = {
            '/dashboard_inspeksi': 'bg-warning',
            '/dashboardtph': 'bg-warning',
            '/dashboard_mutubuah': 'bg-warning',
            '/dashboard_gudang': 'bg-primary',
            '/dashboard_perum': 'bg-success'
        };

        // Default class if no match is found
        var defaultClass = 'bg-light';

        navLinks.forEach(function(link) {
            // Check if any URL segment matches the current URL
            var found = Object.keys(urlClassMapping).find(function(segment) {
                return currentUrl.endsWith(segment) && link.href.endsWith(segment);
            });

            // Assign the class based on the match or default class
            link.classList.add(found ? urlClassMapping[found] : defaultClass);
        });


    });
    document.addEventListener('DOMContentLoaded', function() {
        var lottieElements = document.querySelectorAll('.lottie-animation');
        lottieElements.forEach(function(element) {
            var animationPath = element.getAttribute('data-animation-path');
            lottie.loadAnimation({
                container: element,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: animationPath
            });
        });
    });
</script>
</body>