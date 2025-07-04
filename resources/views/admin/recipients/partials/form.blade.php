@php
    $isEdit = isset($recipient) && $recipient !== null;
@endphp

<div class="modal fade" id="{{ $isEdit ? 'editRecipientModal' . $recipient->id : 'addRecipientModal' }}" tabindex="-1"
    aria-labelledby="recipientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST"
            action="{{ $isEdit ? route('admin.recipients.update', $recipient->id) : route('admin.recipients.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Editar' : 'Adicionar' }} Destinatário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ $recipient->name ?? '' }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referência</label>
                        <select name="reference" class="form-select">
                            <option value="{{ empty($recipient->reference) ? 'selected' : 'notSent' }}">Nenhuma</option>
                            <option value="serviceTowe"
                                {{ isset($recipient) && $recipient->reference === 'serviceTowe' ? 'selected' : '' }}>
                                {{__('reference.' . 'serviceTowe')}}
                            </option>
                            <option value="notification"
                                {{ isset($recipient) && $recipient->reference === 'notification' ? 'selected' : '' }}>
                                {{__('reference.' . 'notification')}}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="number" class="form-label">Número</label>
                        <input type="text" name="number" class="form-control" value="{{ $recipient->number ?? '' }}"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar' : 'Adicionar' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
