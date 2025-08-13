@extends('layouts.header')
@section('title', 'Logs de Notificações')
@section('content')

    <div class="container mt-5">
        <h2 class="text-center">Logs de Notificações</h2>

        <table class="table table-striped mt-4">
            <thead class="bgc-primary">
                <tr>
                    <th>Destinatário</th>
                    <th>ID Mensagem</th>
                    <th>Mensagem</th>
                    <th>Status</th>
                    <th>Data de Envio</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->recipient->name ?? '-' }}</td>
                        <td>{{ $log->notification_id }}</td>
                        <td>{{ Str::limit($log->message, 60) }}</td>
                        <td>
                            <span
                                class="badge bg-{{ $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                {{ __('status.' . ucfirst($log->status)) }}
                            </span>
                        </td>
                        <td>{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <form action="{{ route('admin.notification.logs.delete', $log->id) }}" method="POST"
                                onsubmit="return confirm('Tem certeza?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum log encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
    </div>

@endsection
