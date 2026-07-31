<div class="modal fade notification-modal" id="addNotificationModal" tabindex="-1"
    aria-labelledby="addNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <form method="POST" action="{{ route('admin.notification.store') }}" class="modal-content" data-submit-lock
            data-recipient-required>
            @csrf

            <div class="modal-header">

                <h5 class="modal-title" id="addNotificationModalLabel">
                    <i class="bi bi-bell-fill me-2"></i>
                    Nova notificação
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>

            </div>

            <div class="modal-body">

                {{-- Informação --}}
                <div class="mb-3">

                    <label for="notificationInfo" class="form-label">
                        Informação
                        <span class="text-muted fw-normal">
                            (opcional)
                        </span>
                    </label>

                    <input type="text" id="notificationInfo" name="info"
                        class="form-control @error('info') is-invalid @enderror" value="{{ old('info') }}"
                        placeholder="Ex.: Aviso de manutenção" maxlength="255">

                    @error('info')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Mensagem --}}
                <div class="mb-3">

                    <label for="notificationMessage" class="form-label">
                        Mensagem
                        <span class="text-danger">*</span>
                    </label>

                    <textarea id="notificationMessage" name="msg" class="form-control @error('msg') is-invalid @enderror" rows="5"
                        placeholder="Digite a mensagem que será enviada" required>{{ old('msg') }}</textarea>

                    @error('msg')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Data de envio --}}
                <div class="mb-3">

                    <label for="notificationSendAt" class="form-label">
                        Data e hora do envio
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-calendar-event"></i>
                        </span>

                        <input type="datetime-local" id="notificationSendAt" name="send_at"
                            class="form-control @error('send_at') is-invalid @enderror" value="{{ old('send_at') }}"
                            required>

                        @error('send_at')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                {{-- Destinatários --}}
                <div class="mb-0">

                    <div class="d-flex justify-content-between align-items-center gap-3 mb-2">

                        <label class="form-label mb-0">
                            Destinatários
                            <span class="text-danger">*</span>
                        </label>

                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle-recipients>
                            <i class="bi bi-check2-square me-1"></i>
                            Selecionar todos
                        </button>

                    </div>

                    <div class="notification-recipient-list @error('recipient_ids') is-invalid @enderror"
                        data-recipient-list>
                        @forelse ($recipients as $recipient)
                            @if ($recipient)
                                @php
                                    $recipientInputId = 'notification_recipient_' . $recipient->id;
                                @endphp

                                <label class="notification-recipient-option" for="{{ $recipientInputId }}">
                                    <input class="form-check-input" type="checkbox" name="recipient_ids[]"
                                        value="{{ $recipient->id }}" id="{{ $recipientInputId }}"
                                        @checked(is_array(old('recipient_ids')) && in_array($recipient->id, old('recipient_ids')))>

                                    <span class="notification-recipient-avatar">
                                        {{ mb_strtoupper(mb_substr($recipient->name, 0, 1)) }}
                                    </span>

                                    <span class="notification-recipient-data">

                                        <strong>
                                            {{ $recipient->name }}
                                        </strong>

                                        <small>
                                            <i class="bi bi-whatsapp me-1"></i>
                                            {{ $recipient->number }}
                                        </small>

                                    </span>

                                </label>
                            @endif

                        @empty

                            <div class="notification-recipient-empty">
                                <i class="bi bi-person-x"></i>
                                Nenhum destinatário disponível.
                            </div>
                        @endforelse
                    </div>

                    <div class="invalid-feedback notification-recipient-feedback" data-recipient-feedback>
                        Selecione pelo menos um destinatário.
                    </div>

                    @error('recipient_ids')
                        <div class="text-danger small mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <small class="text-muted d-block mt-2">
                        Selecione pelo menos um destinatário para a notificação.
                    </small>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>

                <button type="submit" class="btn dcm-btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Salvar notificação
                </button>

            </div>

        </form>

    </div>
</div>
