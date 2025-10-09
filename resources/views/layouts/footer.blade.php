<footer class="bg-dark text-white text-center py-3">
    @php
    $horaAtual = \Carbon\Carbon::now()->format('d/m/Y H:i:s');
@endphp
    &copy; {{ date('Y') }} Gestor WF <strong>{{ $horaAtual }}</strong>

</footer>