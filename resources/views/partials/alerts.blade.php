@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SirAlert) {
                window.SirAlert.success(@json(session('success')), 'Informasi !');
            }
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SirAlert) {
                window.SirAlert.error(@json(session('error')), 'Gagal !');
            }
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SirAlert) {
                window.SirAlert.info(@json(session('info')), 'Informasi !');
            }
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SirAlert) {
                window.SirAlert.warning(@json(session('warning')), 'Peringatan !');
            }
        });
    </script>
@endif

@if(isset($errors) && $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SirAlert) {
                const list = @json($errors->all());
                window.SirAlert.error(list.join('<br>'), 'Terdapat Kesalahan !');
            }
        });
    </script>
@endif
