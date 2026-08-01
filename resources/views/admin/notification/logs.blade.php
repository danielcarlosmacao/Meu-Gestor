@extends('layouts.header')

@section('title', 'Logs de Notificações')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notification-module.css') }}">
@endpush

@section('content')

    <div class="container recipients-page">

        <div class="recipients-card">

            {{-- Cabeçalho --}}
            <div class="recipients-header">

                <div class="recipients-title-group">

                    <div class="recipients-title-icon">
                        <i class="bi bi-journal-text"></i>
                    </div>

                    <div>
                        <h1 class="recipients-title">
                            Logs de notificações
                        </h1>

                        <p class="recipients-subtitle">
                            Acompanhe o histórico, os destinatários e o resultado dos envios.
                        </p>
                    </div>

                </div>

                <div class="recipients-header-actions">

                    <a href="{{ route('admin.notification.index') }}" class="btn recipients-btn-secondary"
                        title="Voltar para notificações">
                        <i class="bi bi-arrow-left"></i>
                        <span>Voltar</span>
                    </a>

                </div>

            </div>

            {{-- Tabela --}}
            <div class="recipients-table-wrapper">

                <table class="table recipients-table">

                    <thead>
                        <tr>
                            <th>Destinatário</th>
                            <th>Notificação</th>
                            <th>Mensagem</th>
                            <th>Status</th>
                            <th>Data de envio</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($logs as $log)

                            @php
                                $recipientName = $log->recipient->name ?? 'Destinatário removido';

                                $recipientInitial = mb_strtoupper(mb_substr($recipientName, 0, 1));

                                $statusClass = match ($log->status) {
                                    'sent' => 'is-sent',
                                    'failed' => 'is-failed',
                                    default => 'is-pending',
                                };

                                $statusIcon = match ($log->status) {
                                    'sent' => 'bi-check-circle-fill',
                                    'failed' => 'bi-x-circle-fill',
                                    default => 'bi-clock-fill',
                                };

                                $statusText = match ($log->status) {
                                    'sent' => 'Enviado',
                                    'failed' => 'Falhou',
                                    default => ucfirst($log->status),
                                };

                                $sentAt = $log->sent_at
                                    ? \Carbon\Carbon::parse($log->sent_at)->format('d/m/Y H:i')
                                    : 'Não enviada';
                            @endphp

                            <tr>

                                {{-- Destinatário --}}
                                <td>

                                    <div class="recipient-person">

                                        <span class="recipient-avatar">
                                            {{ $recipientInitial }}
                                        </span>

                                        <div>
                                            <div class="recipient-name">
                                                {{ $recipientName }}
                                            </div>

                                            <small class="recipient-code">
                                                Log #{{ $log->id }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                {{-- Notificação --}}
                                <td>

                                    <span class="notification-log-id">
                                        <i class="bi bi-hash"></i>

                                        {{ $log->notification_id }}
                                    </span>

                                </td>

                                {{-- Mensagem --}}
                                <td>

                                    @if (!empty($log->message))
                                        <div class="notification-log-message">

                                            <span class="notification-log-message-short"
                                                id="log-message-short-{{ $log->id }}">
                                                {{ \Illuminate\Support\Str::limit($log->message, 80) }}
                                            </span>

                                            <span class="notification-log-message-full d-none"
                                                id="log-message-full-{{ $log->id }}">
                                                {{ $log->message }}
                                            </span>

                                            @if (mb_strlen($log->message) > 80)
                                                <button type="button" class="btn btn-link notification-log-toggle"
                                                    data-message-toggle
                                                    data-short-target="#log-message-short-{{ $log->id }}"
                                                    data-full-target="#log-message-full-{{ $log->id }}">
                                                    Mostrar mais
                                                </button>
                                            @endif

                                        </div>
                                    @else
                                        <span class="recipient-empty-value">
                                            Sem mensagem
                                        </span>
                                    @endif

                                </td>

                                {{-- Status --}}
                                <td>

                                    <span class="notification-status {{ $statusClass }}">

                                        <i class="bi {{ $statusIcon }}"></i>

                                        {{ $statusText }}

                                    </span>

                                </td>

                                {{-- Data --}}
                                <td>

                                    <div class="notification-date">

                                        <i class="bi bi-calendar3"></i>

                                        <span>
                                            {{ $sentAt }}
                                        </span>

                                    </div>

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="recipient-actions justify-content-end">

                                        <form
                                            action="{{ route('admin.notification.logs.delete', $log->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger recipient-action-btn"
                                                data-confirm="Tem certeza que deseja excluir este log?" title="Excluir log">
                                                <i class="bi bi-trash"></i>
                                                <span>Excluir</span>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="recipients-empty-state">
                                    <i class="bi bi-journal-x"></i>

                                    <strong>
                                        Nenhum log encontrado
                                    </strong>

                                    <span>
                                        Os registros de envio aparecerão aqui.
                                    </span>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($logs->hasPages())
                <div class="recipients-pagination">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/notification-module.js') }}"></script>
@endpush
