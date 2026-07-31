@extends('layouts.header')

@section('title', 'Notificações')

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
                        <i class="bi bi-bell-fill"></i>
                    </div>

                    <div>
                        <h1 class="recipients-title">
                            Notificações
                        </h1>

                        <p class="recipients-subtitle">
                            Gerencie mensagens, agendamentos e destinatários.
                        </p>
                    </div>

                </div>

                <div class="recipients-header-actions">

                    @if (auth()->user()->hasRole('administrator'))
                        <a href="{{ route('admin.notification.logs') }}" class="btn recipients-btn-secondary"
                            title="Histórico de notificações">
                            <i class="bi bi-journal-text"></i>

                            <span>
                                Histórico
                            </span>
                        </a>
                    @endif

                    <button type="button" class="btn recipients-btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addNotificationModal" title="Cadastrar notificação">
                        <i class="bi bi-plus-lg"></i>

                        <span>
                            Nova notificação
                        </span>
                    </button>

                </div>

            </div>

            {{-- Tabela --}}
            <div class="recipients-table-wrapper">

                <table class="table recipients-table">

                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Informação</th>
                            <th>Destinatários</th>
                            <th>Status</th>
                            <th>Data de envio</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($notifications as $notification)

                            @php
                                $userName = $notification->user->name ?? 'Usuário removido';

                                $userInitial = mb_strtoupper(mb_substr($userName, 0, 1));

                                $sendAtFormatted = $notification->send_at
                                    ? \Carbon\Carbon::parse($notification->send_at)->format('d/m/Y H:i')
                                    : 'Não informada';

                                $sendAtInput = $notification->send_at
                                    ? \Carbon\Carbon::parse($notification->send_at)->format('Y-m-d\TH:i')
                                    : '';
                            @endphp

                            <tr>

                                {{-- Usuário --}}
                                <td>

                                    <div class="recipient-person">

                                        <span class="recipient-avatar">
                                            {{ $userInitial }}
                                        </span>

                                        <div>
                                            <div class="recipient-name">
                                                {{ $userName }}
                                            </div>

                                            <small class="recipient-code">
                                                Notificação #{{ $notification->id }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                {{-- Informação --}}
                                <td>

                                    @if (!empty($notification->info))
                                        <span title="{{ $notification->info }}">
                                            {{ \Illuminate\Support\Str::limit($notification->info, 80) }}
                                        </span>
                                    @else
                                        <span class="recipient-empty-value">
                                            Sem informação
                                        </span>
                                    @endif

                                </td>

                                {{-- Destinatários --}}
                                <td>

                                    <div class="recipient-reference-list">

                                        @forelse ($notification->recipients as $recipient)
                                            <span class="recipient-reference-badge">
                                                <i class="bi bi-person"></i>

                                                {{ $recipient->name }}
                                            </span>

                                        @empty

                                            <span class="recipient-empty-value">
                                                Nenhum destinatário
                                            </span>
                                        @endforelse

                                    </div>

                                </td>

                                {{-- Status --}}
                                <td>

                                    @if ($notification->sent)
                                        <span class="notification-status is-sent">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Enviada
                                        </span>
                                    @else
                                        <span class="notification-status is-pending">
                                            <i class="bi bi-clock-fill"></i>
                                            Pendente
                                        </span>
                                    @endif

                                </td>

                                {{-- Data de envio --}}
                                <td>

                                    <div class="notification-date">

                                        <i class="bi bi-calendar3"></i>

                                        <span>
                                            {{ $sendAtFormatted }}
                                        </span>

                                    </div>

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="recipient-actions justify-content-end">

                                        <button type="button" class="btn btn-primary recipient-action-btn"
                                            data-bs-toggle="modal" data-bs-target="#editNotificationModal"
                                            data-id="{{ $notification->id }}" data-msg="{{ $notification->msg }}"
                                            data-send-at="{{ $sendAtInput }}"
                                            data-update-url="{{ route('admin.notification.update', $notification->id) }}"
                                            title="Editar notificação">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </button>

                                        @if (auth()->user()->hasRole('administrator'))
                                            @if (!$notification->sent)
                                                <form
                                                    action="{{ route('admin.notification.send', $notification->id) }}"
                                                    method="POST" data-submit-lock>
                                                    @csrf

                                                    <button type="submit" class="btn btn-success recipient-action-btn"
                                                        title="Enviar agora">
                                                        <i class="bi bi-send"></i>
                                                        <span>Enviar</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form
                                                    action="{{ route('admin.notification.cleanSent', $notification->id) }}"
                                                    method="POST" data-submit-lock>
                                                    @csrf

                                                    <button type="submit"
                                                        class="btn btn-outline-success recipient-action-btn"
                                                        data-confirm="Deseja limpar o status de envio desta notificação?"
                                                        title="Limpar envio">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        <span>Limpar</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <form
                                                action="{{ route('admin.notification.resend', $notification->id) }}"
                                                method="POST" data-submit-lock>
                                                @csrf

                                                <button type="submit" class="btn btn-warning recipient-action-btn"
                                                    data-confirm="Deseja reenviar esta notificação?"
                                                    title="Reenviar notificação">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                    <span>Reenviar</span>
                                                </button>
                                            </form>

                                            <form
                                                action="{{ route('admin.notification.destroy', $notification->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger recipient-action-btn"
                                                    data-confirm="Tem certeza que deseja excluir esta notificação?"
                                                    title="Excluir notificação">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Excluir</span>
                                                </button>
                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="recipients-empty-state">
                                    <i class="bi bi-bell-slash"></i>

                                    <strong>
                                        Nenhuma notificação cadastrada
                                    </strong>

                                    <span>
                                        Cadastre a primeira notificação para iniciar os envios.
                                    </span>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($notifications->hasPages())
                <div class="recipients-pagination">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de criação --}}
    @include('admin.notification.partials.form')

    {{-- Modal único de edição --}}
    <div class="modal fade notification-modal" id="editNotificationModal" tabindex="-1"
        aria-labelledby="editNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <form id="editNotificationForm" method="POST" action="" class="modal-content" data-submit-lock>
                @csrf
                @method('PUT')

                <div class="modal-header">

                    <h5 class="modal-title" id="editNotificationModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>
                        Editar notificação
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label for="edit-msg" class="form-label">
                            Mensagem
                        </label>

                        <textarea name="msg" id="edit-msg" class="form-control" rows="5"
                            placeholder="Digite a mensagem da notificação" required></textarea>

                    </div>

                    <div class="mb-0">

                        <label for="edit-send_at" class="form-label">
                            Data e hora do envio
                        </label>

                        <input type="datetime-local" name="send_at" id="edit-send_at" class="form-control" required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar alterações
                    </button>

                </div>

            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/notification-module.js') }}"></script>
@endpush
