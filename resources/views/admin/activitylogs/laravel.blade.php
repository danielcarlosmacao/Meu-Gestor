@extends('layouts.header')
@section('title', 'Log do sistema')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Log do sistema</h3>

    <div class="mb-2">
        <a href="{{ route('systemlogs.index') }}" class="btn btn-sm btn-primary">Todos</a>
        <a href="{{ route('systemlogs.index', ['level' => 'ERROR']) }}" class="btn btn-sm btn-danger">ERROR</a>
        <a href="{{ route('systemlogs.index', ['level' => 'WARNING']) }}" class="btn btn-sm btn-warning">WARNING</a>
        <a href="{{ route('systemlogs.index', ['level' => 'INFO']) }}" class="btn btn-sm btn-info">INFO</a>
        <a href="{{ route('systemlogs.index', ['level' => 'DEBUG']) }}" class="btn btn-sm btn-secondary">DEBUG</a>
    </div>

   <div class="card shadow rounded-2xl">
        <div class="card-body" style="max-height:600px; overflow:auto; font-family:monospace; background:#f8f9fa;">

            <div class="d-flex justify-content-end mb-2">
                {{ $logs->links() }}
            </div>

            @php $lastKey = count($logs) - 1; @endphp
            @foreach($logs as $key => $line)
                @php
                    $upper = strtoupper($line);
                    if(str_contains($upper, 'ERROR')) {
                        $class = 'text-danger';
                    } elseif(str_contains($upper, 'WARNING')) {
                        $class = 'text-warning';
                    } elseif(str_contains($upper, 'INFO')) {
                        $class = 'text-info';
                    } elseif(str_contains($upper, 'DEBUG')) {
                        $class = 'text-secondary';
                    } else {
                        $class = '';
                    }

                    // Última linha destacada
                    $highlight = ($key == 0) ? 'fw-bold bg-light border-start border-4 border-primary px-2' : '';
                @endphp

                <div class="{{ $class }} {{ $highlight }}" style="white-space:pre; word-break:normal;">{{ $line }}</div>
            @endforeach

            <div class="d-flex justify-content-center mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection