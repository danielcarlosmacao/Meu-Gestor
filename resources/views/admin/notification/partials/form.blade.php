<div class="modal fade" id="addNotificationModal" tabindex="-1" aria-labelledby="addNotificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.notification.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Notificação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="info" class="form-label">Info (opcional)</label>
                        <input type="text" name="info" class="form-control" value="{{ old('info') }}">
                    </div>
                    <div class="mb-3">
                        <label for="msg" class="form-label">Mensagem</label>
                        <textarea name="msg" class="form-control" required>{{ old('msg') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-send_at" class="form-label">Data envio</label>
                        <input type="datetime-local" name="send_at" id="edit-send_at" class="form-control" required
                            value="{{ old('send_at') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Destinatários</label>
                        <div class="d-flex flex-wrap gap-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($recipients as $recipient)
                                @if ($recipient != null)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipient_ids[]"
                                            value="{{ $recipient->id }}" id="recipient_{{ $recipient->id }}"
                                            @if (is_array(old('recipient_ids')) && in_array($recipient->id, old('recipient_ids'))) checked @endif>
                                        <label class="form-check-label" for="recipient_{{ $recipient->id }}">
                                            {{ $recipient->name }} ({{ $recipient->number }})
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <small class="text-muted">Selecione pelo menos um destinatário para a notificação.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#addNotificationModal form');
    form.addEventListener('submit', function(event) {
        const checkboxes = form.querySelectorAll('input[name="recipient_ids[]"]');
        const checkedOne = Array.from(checkboxes).some(chk => chk.checked);
        if (!checkedOne) {
            event.preventDefault();
            alert('Por favor, selecione pelo menos um destinatário.');
        }
    });
});
</script>
