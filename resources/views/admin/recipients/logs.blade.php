@extends('layouts.header')

@section('title', 'Logs de Envio WhatsApp')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notification-module.css') }}">
@endpush

@section('content')
    @php
        $sentCount = $logs->getCollection()->where('status', 'sent')->count();
        $failedCount = $logs->getCollection()->where('status', 'failed')->count();
        $pendingCount = $logs->getCollection()->whereNotIn('status', ['sent', 'failed'])->count();
    @endphp

    <div class="container-fluid recipients-page recipients-logs-page">
        <div class="recipients-card">
            <div class="recipients-header">
                <div class="recipients-title-group">
                    <div class="recipients-title-icon recipients-title-icon-whatsapp">
                        <i class="bi bi-whatsapp"></i>
                    </div>

                    <div>
                        <h1 class="recipients-title">Logs de envio</h1>
                        <p class="recipients-subtitle">
                            Histórico das mensagens enviadas pelo WhatsApp.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.recipients.index') }}"
                    class="btn recipients-btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    <span>Destinatários</span>
                </a>
            </div>

            <div class="recipients-log-summary">
                <div class="recipients-summary-item is-total">
                    <span class="recipients-summary-icon"><i class="bi bi-list-check"></i></span>
                    <div><small>Nesta página</small><strong>{{ $logs->count() }}</strong></div>
                </div>

                <div class="recipients-summary-item is-sent">
                    <span class="recipients-summary-icon"><i class="bi bi-check-circle"></i></span>
                    <div><small>Enviados</small><strong>{{ $sentCount }}</strong></div>
                </div>

                <div class="recipients-summary-item is-failed">
                    <span class="recipients-summary-icon"><i class="bi bi-x-circle"></i></span>
                    <div><small>Falhas</small><strong>{{ $failedCount }}</strong></div>
                </div>

                <div class="recipients-summary-item is-pending">
                    <span class="recipients-summary-icon"><i class="bi bi-clock"></i></span>
                    <div><small>Pendentes</small><strong>{{ $pendingCount }}</strong></div>
                </div>
            </div>

            @if ($logs->isEmpty())
                <div class="recipients-empty-state recipients-empty-block">
                    <i class="bi bi-journal-x"></i>
                    <strong>Nenhum log encontrado</strong>
                    <span>Os próximos envios aparecerão neste histórico.</span>
                </div>
            @else
                <div class="recipients-table-wrapper">
                    <table class="table recipients-table recipients-log-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Destinatário</th>
                                <th>Telefone</th>
                                <th>Referência</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Mensagem</th>
                                <th>Resposta</th>
                                <th>Data de envio</th>
                                <th>Criado em</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td><span class="recipient-log-id">#{{ $log->id }}</span></td>

                                    <td>
                                        <div class="recipient-person compact">
                                            <span class="recipient-avatar">
                                                {{ mb_strtoupper(mb_substr($log->recipient->name ?? 'N', 0, 1)) }}
                                            </span>
                                            <span class="recipient-name">{{ $log->recipient->name ?? 'N/D' }}</span>
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                        <i class="bi bi-telephone me-1 text-muted"></i>
                                        {{ $log->recipient->number ?? 'N/D' }}
                                    </td>

                                    <td>
                                        @if ($log->ref instanceof \App\Models\Maintenance)
                                            <span class="recipient-reference-text">
                                                <i class="bi bi-broadcast-pin"></i>
                                                {{ $log->ref->tower->name ?? 'N/D' }}
                                            </span>
                                        @elseif ($log->ref instanceof \App\Models\Vacation)
                                            <span class="recipient-reference-text">
                                                <i class="bi bi-calendar2-week"></i>
                                                Férias – {{ $log->ref->collaborator->name ?? 'N/D' }}
                                            </span>
                                        @else
                                            <span class="recipient-empty-value">N/D</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($log->loggable instanceof \App\Models\Maintenance)
                                            <span class="recipient-type-badge is-maintenance">Manutenção</span>
                                        @elseif ($log->loggable instanceof \App\Models\Vacation)
                                            <span class="recipient-type-badge is-vacation">Férias</span>
                                        @else
                                            <span class="recipient-type-badge">Outro</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($log->status === 'sent')
                                            <span class="recipient-status-badge is-sent">
                                                <i class="bi bi-check-circle-fill"></i> Enviado
                                            </span>
                                        @elseif ($log->status === 'failed')
                                            <span class="recipient-status-badge is-failed">
                                                <i class="bi bi-x-circle-fill"></i> Falha
                                            </span>
                                        @else
                                            <span class="recipient-status-badge is-pending">
                                                <i class="bi bi-clock-fill"></i> Pendente
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <button type="button"
                                            class="recipient-text-preview"
                                            data-expand-text
                                            data-short-text="{{ \Illuminate\Support\Str::limit($log->message, 70) }}"
                                            data-full-text="{{ $log->message }}">
                                            <span>{{ \Illuminate\Support\Str::limit($log->message, 70) }}</span>
                                            @if (mb_strlen($log->message) > 70)
                                                <small>Mostrar mais</small>
                                            @endif
                                        </button>
                                    </td>

                                    <td>
                                        @if ($log->response)
                                            <button type="button"
                                                class="recipient-text-preview"
                                                data-expand-text
                                                data-short-text="{{ \Illuminate\Support\Str::limit($log->response, 70) }}"
                                                data-full-text="{{ $log->response }}">
                                                <span>{{ \Illuminate\Support\Str::limit($log->response, 70) }}</span>
                                                @if (mb_strlen($log->response) > 70)
                                                    <small>Mostrar mais</small>
                                                @endif
                                            </button>
                                        @else
                                            <span class="recipient-empty-value">Sem resposta</span>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('d/m/Y H:i') : '-' }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="recipients-pagination">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/notification-module.js') }}"></script>
@endpush
