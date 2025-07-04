@extends('layouts.header')
@section('title', 'Logs de Notificações')
@section('content')

<div class="container mt-5">
    <h2 class="text-center">Logs de Notificações</h2>

    <table class="table table-striped mt-4">
        <thead class="bgc-primary">
            <tr>
                <th>Destinatário</th>
                <th>Mensagem</th>
                <th>Status</th>
                <th>Data de Envio</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->recipient->name ?? '-' }}</td>
                    <td>{{ Str::limit($log->message, 60) }}</td>
                    <td>
                        <span class="badge bg-{{ $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                            {{ __('status.' . ucfirst($log->status)) }}
                        </span>
                    </td>
                    <td>{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum log encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $logs->links() }}
    </div>
</div>

@endsection
