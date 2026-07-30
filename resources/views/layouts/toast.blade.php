<!-- CSS Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        toastr.options = {
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            preventDuplicates: true,
            positionClass: "toast-top-right",
            timeOut: 4000,
            extendedTimeOut: 1000,
            showDuration: 300,
            hideDuration: 300,
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
            onclick: null
        };

        /*
        |--------------------------------------------------------------------------
        | MENSAGENS DA SESSÃO
        |--------------------------------------------------------------------------
        */

        @if (session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if (session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if (session('warning'))
            toastr.warning(@json(session('warning')));
        @endif

        @if (session('info'))
            toastr.info(@json(session('info')));
        @endif

        /*
        |--------------------------------------------------------------------------
        | ERROS DE VALIDAÇÃO
        |--------------------------------------------------------------------------
        */

        @if ($errors->any())

            @foreach ($errors->all() as $error)
                toastr.warning(@json($error));
            @endforeach
        @endif

    });
</script>
