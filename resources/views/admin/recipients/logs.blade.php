@extends('layouts.header')

@section('title', 'Logs de Envio WhatsApp')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Logs de Envio WhatsApp</h2>

    @if($logs->isEmpty())
        <div class="alert alert-info">Nenhum log encontrado.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Destinatário</th>
                    <th>Telefone</th>
                    <th>Torre</th>
                    <th>Status</th>
                    <th>Mensagem</th>
                    <th>Resposta</th>
                    <th>Data Envio</th>
                    <th>Criado Em</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->recipient->name ?? 'N/D' }}</td>
                    <td>{{ $log->recipient->number ?? 'N/D' }}</td>
                    <td>{{ $log->maintenance?->tower?->name ?? 'N/D' }}</td>
                    <td>
                        @if($log->status === 'sent')
                            <span class="badge bg-success">Enviado</span>
                        @elseif($log->status === 'failed')
                            <span class="badge bg-danger">Falha</span>
                        @else
                            <span class="badge bg-secondary text-white">Pendente</span>
                        @endif
                    </td>
                    <td style="max-width: 250px; word-wrap: break-word;">{{ $log->message }}</td>
                    <td style="max-width: 250px; word-wrap: break-word;">{{ $log->response ?? '-' }}</td>
                    <td>{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
