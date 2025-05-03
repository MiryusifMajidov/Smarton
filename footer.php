
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/Chart.min.js"></script>
<script src="assets/js/dynamic-pie-chart.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/fullcalendar.js"></script>
<script src="assets/js/jvectormap.min.js"></script>
<script src="assets/js/world-merc.js"></script>
<script src="assets/js/polyfill.js"></script>
<script src="assets/js/main.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>


<script>
    document.getElementById('profile').addEventListener('click', function (e) {
        e.stopPropagation();
        var profileMenu = document.querySelector('#profile + .dropdown-menu');
        var messageMenu = document.querySelector('#message + .dropdown-menu');


        if (profileMenu.classList.contains('show')) {
            profileMenu.classList.remove('show');
        } else {
            profileMenu.classList.add('show');

            if (messageMenu) messageMenu.classList.remove('show');
        }
    });

    document.getElementById('message').addEventListener('click', function (e) {
        e.stopPropagation();
        var messageMenu = document.querySelector('#message + .dropdown-menu');
        var profileMenu = document.querySelector('#profile + .dropdown-menu');

        if (messageMenu.classList.contains('show')) {
            messageMenu.classList.remove('show');
        } else {
            messageMenu.classList.add('show');
            if (profileMenu) profileMenu.classList.remove('show');
        }
    });


    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
            menu.classList.remove('show');
        });
    });


</script>