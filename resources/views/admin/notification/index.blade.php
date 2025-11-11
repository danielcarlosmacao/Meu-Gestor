@extends('layouts.header')
@section('title', 'Notificações')
@section('content')

<div class="container mt-5">
    <h2 class="text-center">Notificações
        <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addNotificationModal">
            <i class="bi bi-plus-lg"></i>
        </button>
        @if(auth()->user()->hasRole('administrator'))
            <a href="{{ route('admin.notification.logs') }}" class="btn dcm-btn-primary btn-sm"><i class="bi bi-journal-text"></i></a>
            @endif
    </h2>
    <table class="table table-striped mt-4">
        <thead class="bgc-primary">
            <tr>
                <th>Usuario</th>
                <th>Informação</th>
                <th>Destinatários</th>
                <th>Enviada?</th>
                <th>Data envio</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notifications as $notification)
                <tr>
                    <td>{{ $notification->user->name ?? 'Usuário removido' }}</td>
                    <td>{{ $notification->info }}</td>
                    <td>
                        @foreach ($notification->recipients as $r)
                            <span class="badge bg-info text-dark">{{ $r->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($notification->sent)
                            <span class="badge bg-success">Sim</span>
                        @else
                            <span class="badge bg-warning text-dark">Não</span>
                        @endif
                    </td>
                    <td>{{ $notification->send_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                        <button 
                                class="btn btn-primary btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editNotificationModal"
                                data-id="{{ $notification->id }}"
                                data-msg="{{ $notification->msg }}"
                                data-send_at="{{ $notification->send_at }}"
                            >
                                Editar
                            </button>
                        @if(auth()->user()->hasRole('administrator'))
                            @if (!$notification->sent)
                                <form action="{{ route('admin.notification.send', $notification->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Enviar</button>
                                </form>
                            @endif
                            @if ($notification->sent)
                                <form action="{{ route('admin.notification.cleanSent', $notification->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Limpar envio</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.notification.resend', $notification->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-warning btn-sm">Reenviar</button>
                            </form>

                            <form action="{{ route('admin.notification.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
</div>

@include('admin.notification.partials.form') <!-- Modal de criação -->

<!-- Modal de edição -->
<div class="modal fade" id="editNotificationModal" tabindex="-1" aria-labelledby="editNotificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editNotificationForm" method="POST" action="">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editNotificationModalLabel">Editar Notificação</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit-msg" class="form-label">Mensagem</label>
            <textarea name="msg" id="edit-msg" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-send_at" class="form-label">Data envio</label>
            <input type="datetime-local" name="send_at" id="edit-send_at" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  var editModal = document.getElementById('editNotificationModal')
  editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget // Botão que abriu o modal
    var id = button.getAttribute('data-id')
    var msg = button.getAttribute('data-msg')
    var sendAt = button.getAttribute('data-send_at')

    // Atualiza o formulário
    var form = document.getElementById('editNotificationForm')
    form.action = '/admin/notification/' + id  // ajuste a URL conforme sua rota

    document.getElementById('edit-msg').value = msg
    document.getElementById('edit-send_at').value = sendAt ? sendAt.substring(0,10) : ''
  })
</script>
@endpush
