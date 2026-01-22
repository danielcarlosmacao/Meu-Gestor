@extends('layouts.header')

@section('title', 'Logs de Envio WhatsApp')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Logs de Envio WhatsApp</h2>

        @if ($logs->isEmpty())
            <div class="alert alert-info">Nenhum log encontrado.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Destinatário</th>
                            <th>Telefone</th>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Mensagem</th>
                            <th>Resposta</th>
                            <th>Data Envio</th>
                            <th>Criado Em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                {{-- ID --}}
                                <td>{{ $log->id }}</td>

                                {{-- Destinatário --}}
                                <td>{{ $log->recipient->name ?? 'N/D' }}</td>

                                {{-- Telefone --}}
                                <td>{{ $log->recipient->number ?? 'N/D' }}</td>

                                {{-- Referência (Manutenção / Férias) --}}
                                <td>
                                    @if ($log->ref instanceof \App\Models\Maintenance)
                                        {{ $log->ref->tower->name ?? 'N/D' }}
                                    @elseif($log->ref instanceof \App\Models\Vacation)
                                        Férias – {{ $log->ref->collaborator->name ?? 'N/D' }}
                                    @else
                                        N/D
                                    @endif
                                </td>


                                {{-- Tipo --}}
                                <td>
                                    @if ($log->loggable instanceof \App\Models\Maintenance)
                                        <span class="badge bg-warning text-dark">Manutenção</span>
                                    @elseif($log->loggable instanceof \App\Models\Vacation)
                                        <span class="badge bg-info text-dark">Férias</span>
                                    @else
                                        <span class="badge bg-secondary">Outro</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if ($log->status === 'sent')
                                        <span class="badge bg-success">Enviado</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge bg-danger">Falha</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Pendente</span>
                                    @endif
                                </td>

                                {{-- Mensagem --}}
                                <td style="max-width: 280px; word-wrap: break-word;">
                                    {{ $log->message }}
                                </td>

                                {{-- Resposta --}}
                                <td style="max-width: 280px; word-wrap: break-word;">
                                    {{ $log->response ?? '-' }}
                                </td>

                                {{-- Data Envio --}}
                                <td>
                                    {{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('d/m/Y H:i') : '-' }}
                                </td>

                                {{-- Criado Em --}}
                                <td>
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
