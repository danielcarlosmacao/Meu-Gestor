@php
    $isEdit = isset($recipient) && !is_null($recipient);

    $modalId = $isEdit
        ? 'editRecipientModal' . $recipient->id
        : 'addRecipientModal';

    $modalLabelId = $modalId . 'Label';

    $action = $isEdit
        ? route('admin.recipients.update', $recipient->id)
        : route('admin.recipients.store');
@endphp

<div class="modal fade recipients-modal"
    id="{{ $modalId }}"
    tabindex="-1"
    aria-labelledby="{{ $modalLabelId }}"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <form method="POST"
            action="{{ $action }}"
            class="modal-content"
            data-submit-lock>
            @csrf

            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalLabelId }}">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    {{ $isEdit ? 'Editar destinatário' : 'Novo destinatário' }}
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="{{ $modalId }}_name" class="form-label">Nome</label>

                    <input type="text"
                        id="{{ $modalId }}_name"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $recipient->name ?? '') }}"
                        placeholder="Nome do destinatário"
                        autocomplete="name"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipos de envio</label>

                    <div class="recipient-reference-options">
                        @foreach ($references as $reference)
                            @php
                                $referenceInputId = $modalId . '_reference_' . $reference->id;
                                $selectedReferences = old(
                                    'references',
                                    $isEdit
                                        ? $recipient->references->pluck('id')->all()
                                        : []
                                );
                            @endphp

                            <label class="recipient-reference-option" for="{{ $referenceInputId }}">
                                <input type="checkbox"
                                    class="form-check-input"
                                    name="references[]"
                                    value="{{ $reference->id }}"
                                    id="{{ $referenceInputId }}"
                                    @checked(in_array($reference->id, $selectedReferences))>

                                <span>
                                    <i class="bi bi-bell"></i>
                                    {{ __('reference.' . $reference->name) }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <small class="form-text text-muted">
                        Selecione uma ou mais notificações que este contato deverá receber.
                    </small>
                </div>

                <div class="mb-0">
                    <label for="{{ $modalId }}_number" class="form-label">Número do WhatsApp</label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-whatsapp"></i>
                        </span>

                        <input type="text"
                            id="{{ $modalId }}_number"
                            name="number"
                            class="form-control"
                            value="{{ old('number', $recipient->number ?? '') }}"
                            placeholder="Ex.: 55 11 99999-9999"
                            inputmode="tel"
                            autocomplete="tel"
                            data-phone-input
                            required>
                    </div>

                    <small class="form-text text-muted">
                        Informe o DDI e o DDD quando necessário.
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>

                <button type="submit" class="btn dcm-btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $isEdit ? 'Salvar alterações' : 'Cadastrar destinatário' }}
                </button>
            </div>
        </form>
    </div>
</div>
