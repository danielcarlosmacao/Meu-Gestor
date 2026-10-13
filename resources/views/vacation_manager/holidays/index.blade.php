@extends('layouts.header')

@section('title', 'Gerenciamento de Feriados')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Cabeçalho e Botão Novo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Feriados</h1>
                <p class="text-muted mb-0">Cadastre feriados recorrentes ou datas específicas para aplicação automática nas
                    escalas.</p>
            </div>
            @can('collaborators.workschedule.edit')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                    <i class="bi bi-plus-lg me-1"></i> Novo Feriado
                </button>
            @endcan
        </div>



        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabela de Feriados -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Descrição / Nome</th>
                                <th>Data</th>
                                <th>Recorrência</th>
                                <th class="text-end pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($holidays as $holiday)
                                @php
                                    $carbonDate = \Carbon\Carbon::parse($holiday->date);
                                    $isAppellant = $holiday->type === 'appellant';
                                    $formattedDate = $isAppellant
                                        ? $carbonDate->format('d/m')
                                        : $carbonDate->format('d/m/Y');
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-medium">
                                        {{ $holiday->name ?? ($holiday->title ?? ($holiday->description ?? 'Feriado')) }}
                                    </td>
                                    <td>{{ $formattedDate }}</td>
                                    <td>
                                        @if ($isAppellant)
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle">Recorrente
                                                (Anual)
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Data
                                                Única</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @can('collaborators.workschedule.edit')
                                            <form action="{{ route('vacation_manager.holidays.destroy', $holiday->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir este feriado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Nenhum feriado cadastrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Adição de Feriado -->
    <div class="modal fade" id="addHolidayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('vacation_manager.holidays.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i> Adicionar Feriado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nome do Feriado -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nome do Feriado</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Ex: Natal, Independência, Carnaval..." required>
                    </div>

                    <!-- Data -->
                    <div class="mb-3">
                        <label for="date" class="form-label fw-semibold">Data</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>

                    <!-- Tipo de Recorrência (compatível com o recalculate) -->
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Tipo</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="appellant">Recorrente (Repete todos os anos no mesmo dia/mês)</option>
                            <option value="unique">Único (Apenas no ano selecionado)</option>
                        </select>
                        <div class="form-text">Para feriados móveis como Carnaval ou Sexta-feira Santa, utilize "Único".
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Salvar
                        Feriado</button>
                </div>
            </form>
        </div>
    </div>
@endsection
