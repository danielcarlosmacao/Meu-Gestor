@extends('layouts.header')
@section('title', 'Placas')
@section('content')

<div class="container mb-2 mb-md-5 mt-2 mt-md-5">
  <h2 class="text-center">Placas
    @can('towers.create')
    <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#addPlate">
      <i class="bi bi-plus-lg"></i>
    </button>
    @endcan
  </h2>
</div>

<div class="container table-responsive">
  <table class="table table-striped">
    <thead class="bgc-primary text-white">
      <tr>
        <th scope="col">Nome</th>
        <th scope="col">Watts</th>
        <th scope="col">Ah</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($plates as $plate)
      <tr>
        <th scope="row">{{ $plate->name }}</th>
        <td>{{ $plate->watts }}</td>
        <td>{{ $plate->amps }}</td>
        <td class="text-center align-middle p-1">
          @can('towers.edit')
          <button type="button" class="btn btn-warning btn-sm edit-plate-btn"
            data-id="{{ $plate->id }}"
            data-name="{{ $plate->name }}"
            data-watts="{{ $plate->watts }}"
            data-amps="{{ $plate->amps }}"
            data-bs-toggle="modal"
            data-bs-target="#editModal">
            <i class="bi bi-pencil"></i> Editar
          </button>
          @endcan
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
          <div class="d-flex justify-content-center mt-4">
            {{ $plates->links() }}
        </div>
        <br>


</div>

<!-- Modal Adicionar -->
<div class="modal fade" id="addPlate" tabindex="-1" aria-labelledby="addPlateLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-bgc-primary" id="addPlateLabel">Nova Placa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('plate.store') }}" method="post">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="watts" class="form-label">Watts</label>
            <input type="number" class="form-control" id="watts" name="watts" min="0" max="1000" step="1" required>
          </div>
          <div class="mb-3">
            <label for="amps" class="form-label">Amperes</label>
            <input type="number" class="form-control" id="amps" name="amps" min="0" max="1000" step="0.01" required>
          </div>
          <div class="text-end">
            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-bgc-primary" id="editModalLabel">Editar Placa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_id" name="id">
          <div class="mb-3">
            <label for="edit_name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="edit_watts" class="form-label">Watts</label>
            <input type="number" class="form-control" id="edit_watts" name="watts" min="0" max="1000" step="1" required>
          </div>
          <div class="mb-3">
            <label for="edit_amps" class="form-label">Amperes</label>
            <input type="number" class="form-control" id="edit_amps" name="amps" min="0" max="1000" step="0.01" required>
          </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
            @can('towers.delete')
            <button type="button" class="btn btn-danger" onclick="deletar(document.getElementById('edit_id').value)"><i class="bi bi-trash"></i> Deletar</button>
            @endcan
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Confirmação de sucesso -->
@if (session('success'))
  <script>
    alert("{{ session('success') }}");
  </script>
@endif

<!-- Scripts -->
<script>
  const routeDestroy = "{{ route('plate.destroy', ['id' => ':id']) }}";
  const refDestroy = "esta placa";
  const routeUpdate = "{{ route('plate.update', ['id' => ':id']) }}";



  document.querySelectorAll('.edit-plate-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      const name = button.dataset.name;
      const watts = button.dataset.watts;
      const amps = button.dataset.amps;

      document.getElementById('edit_id').value = id;
      document.getElementById('edit_name').value = name;
      document.getElementById('edit_watts').value = watts;
      document.getElementById('edit_amps').value = amps;

      const form = document.getElementById('editForm');
      form.action = routeUpdate.replace(':id', id);
    });
  });
</script>
@endsection
