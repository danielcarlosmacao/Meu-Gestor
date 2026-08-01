@extends('layouts.header')

@section('title', 'Destinatários')

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
                        <i class="bi bi-send-fill"></i>
                    </div>

                    <div>
                        <h1 class="recipients-title">
                            Destinatários
                        </h1>

                        <p class="recipients-subtitle">
                            Gerencie os contatos que recebem notificações do sistema.
                        </p>
                    </div>

                </div>

                <div class="recipients-header-actions">

                    <a href="{{ route('admin.recipients.logs') }}" class="btn recipients-btn-secondary"
                        title="Visualizar histórico de envios">
                        <i class="bi bi-journal-text"></i>
                        <span>Histórico</span>
                    </a>

                    <button type="button" class="btn recipients-btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addRecipientModal" title="Adicionar destinatário">
                        <i class="bi bi-plus-lg"></i>
                        <span>Novo destinatário</span>
                    </button>

                </div>

            </div>

            {{-- Tabela --}}
            <div class="recipients-table-wrapper">

                <table class="table recipients-table">

                    <thead>
                        <tr>
                            <th>Destinatário</th>
                            <th>Tipos de envio</th>
                            <th>Número</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($recipients as $recipient)

                            <tr>

                                {{-- Destinatário --}}
                                <td>

                                    <div class="recipient-person">

                                        <span class="recipient-avatar">
                                            {{ mb_strtoupper(mb_substr($recipient->name, 0, 1)) }}
                                        </span>

                                        <div>
                                            <div class="recipient-name">
                                                {{ $recipient->name }}
                                            </div>

                                            <small class="recipient-code">
                                                Código #{{ $recipient->id }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                {{-- Referências --}}
                                <td>

                                    <div class="recipient-reference-list">

                                        @forelse ($recipient->references as $reference)
                                            <span class="recipient-reference-badge">
                                                <i class="bi bi-bell"></i>

                                                {{ __('reference.' . $reference->name) }}
                                            </span>

                                        @empty

                                            <span class="recipient-empty-value">
                                                Nenhuma referência
                                            </span>
                                        @endforelse

                                    </div>

                                </td>

                                {{-- Número --}}
                                <td>

                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $recipient->number) }}"
                                        class="recipient-phone" target="_blank" rel="noopener noreferrer"
                                        title="Abrir no WhatsApp">
                                        <i class="bi bi-whatsapp"></i>

                                        <span>
                                            {{ $recipient->number }}
                                        </span>
                                    </a>

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="recipient-actions justify-content-end">

                                        <button type="button" class="btn btn-warning recipient-action-btn"
                                            data-bs-toggle="modal" data-bs-target="#editRecipientModal{{ $recipient->id }}"
                                            title="Editar destinatário">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Editar</span>
                                        </button>

                                        <form
                                            action="{{ route('admin.recipients.destroy', $recipient->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger recipient-action-btn"
                                                data-confirm="Tem certeza que deseja excluir este destinatário?"
                                                title="Excluir destinatário">
                                                <i class="bi bi-trash"></i>
                                                <span>Excluir</span>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="recipients-empty-state">

                                    <i class="bi bi-person-lines-fill"></i>

                                    <strong>
                                        Nenhum destinatário cadastrado
                                    </strong>

                                    <span>
                                        Adicione o primeiro contato para iniciar os envios.
                                    </span>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($recipients->hasPages())
                <div class="recipients-pagination">
                    {{ $recipients->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de criação --}}
    @include('admin.recipients.partials.form', [
        'recipient' => null,
    ])

    {{--
        Modais de edição precisam ficar fora da tabela.
        Não coloque includes de modal dentro do tbody.
    --}}
    @foreach ($recipients as $recipient)
        @include('admin.recipients.partials.form', [
            'recipient' => $recipient,
        ])
    @endforeach

@endsection

@push('scripts')
    <script src="{{ asset('js/notification-module.js') }}"></script>
@endpush
