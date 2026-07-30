@extends('layouts.header')

@section('title', 'FTTH - PONs')

@section('content')

    <div class="container-fluid py-3 py-md-4">

        {{-- CABEÇALHO --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">

            <div>
                <h2 class="fw-bold mb-1">
                    PONs da OLT
                </h2>

                <p class="text-muted mb-0">
                    Gerencie as PONs, sinais e mapas da rede FTTH.
                </p>
            </div>

            @can('ftth.create')
                <button type="button" class="btn dcm-btn-primary px-3" data-bs-toggle="modal" data-bs-target="#modalPon">

                    <i class="bi bi-plus-lg me-1"></i>
                    Nova PON
                </button>
            @endcan

        </div>

        {{-- CARD --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-0 py-3">

                <div class="d-flex align-items-center justify-content-between">

                    <div class="fw-semibold">
                        <i class="bi bi-diagram-3 me-1"></i>
                        PONs cadastradas
                    </div>

                    <span class="badge bg-secondary">
                        {{ $pons->count() }}
                    </span>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-striped table-hover align-middle mb-0">

                        <thead class="bgc-primary text-white">

                            <tr>
                                <th class="ps-3">
                                    OLT
                                </th>

                                <th>
                                    Descrição
                                </th>

                                <th class="text-center">
                                    Sinal
                                </th>

                                <th class="text-center">
                                    Coordenadas
                                </th>

                                <th width="140" class="text-end pe-3">
                                    Ações
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($pons as $pon)
                                <tr>

                                    {{-- OLT --}}
                                    <td class="ps-3">

                                        <a href="{{ route('pon.ponsmap', ['olt' => $pon->olt]) }}"
                                            class="text-decoration-none">

                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">

                                                <i class="bi bi-router me-1"></i>

                                                {{ $pon->olt }}

                                            </span>

                                        </a>

                                    </td>

                                    {{-- DESCRIÇÃO --}}
                                    <td>

                                        <a href="{{ route('fiberbox.index', [
                                            'pon' => $pon->id,
                                            'map' => 'yes',
                                        ]) }}"
                                            class="text-dark text-decoration-none fw-bold">

                                            {{ $pon->info }}

                                        </a>

                                        <div class="small text-muted mt-1">

                                            <i class="bi bi-globe-americas me-1"></i>
                                            Abrir mapa da PON

                                        </div>

                                    </td>

                                    {{-- SINAL --}}
                                    <td class="text-center">

                                        @if ($pon->signal !== null && $pon->signal !== '')
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">

                                                <i class="bi bi-reception-4 me-1"></i>

                                                {{ $pon->signal }}

                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">

                                                Não informado

                                            </span>
                                        @endif

                                    </td>

                                    {{-- COORDENADAS --}}
                                    <td class="text-center">

                                        @if ($pon->coordinates)
                                            <span class="small text-muted">

                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>

                                                {{ $pon->coordinates }}

                                            </span>
                                        @else
                                            <span class="text-muted">
                                                —
                                            </span>
                                        @endif

                                    </td>

                                    {{-- AÇÕES --}}
                                    <td class="text-end pe-3">

                                        <div class="d-inline-flex gap-1">

                                            <a href="{{ route('fiberbox.index', ['pon' => $pon->id]) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver caixas">

                                                <i class="bi bi-box-seam"></i>

                                            </a>

                                            <a href="{{ route('fiberbox.index', [
                                                'pon' => $pon->id,
                                                'map' => 'yes',
                                            ]) }}"
                                                class="btn btn-sm btn-outline-success" title="Abrir mapa">

                                                <i class="bi bi-map"></i>

                                            </a>

                                            @can('ftth.delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="openConfirmModal(
                                                        '{{ route('pon.destroy', $pon->id) }}',
                                                        'Tem certeza que deseja excluir esta PON?',
                                                        'Essa alteração não poderá ser revertida.',
                                                        'DELETE'
                                                    )"
                                                    title="Excluir PON">

                                                    <i class="bi bi-trash"></i>

                                                </button>
                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted py-5">

                                        <i class="bi bi-diagram-3 display-5 d-block mb-2"></i>

                                        <div class="fw-semibold">
                                            Nenhuma PON cadastrada
                                        </div>

                                        <div class="small">
                                            Cadastre uma nova PON para começar.
                                        </div>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- MODAL NOVA PON --}}
    <div class="modal fade" id="modalPon" tabindex="-1" aria-labelledby="modalPonLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <form method="POST" action="{{ route('pon.store') }}" class="w-100">

                @csrf

                <div class="modal-content border-0 shadow">

                    {{-- HEADER --}}
                    <div class="modal-header bgc-primary text-white">

                        <h5 class="modal-title fw-bold" id="modalPonLabel">

                            <i class="bi bi-plus-circle me-1"></i>
                            Nova PON

                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Fechar">
                        </button>

                    </div>

                    {{-- BODY --}}
                    <div class="modal-body p-4">

                        {{-- OLT --}}
                        <div class="mb-3">

                            <label for="olt" class="form-label fw-semibold">

                                OLT
                                <span class="text-danger">*</span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-router"></i>
                                </span>

                                <input type="text" name="olt" id="olt" class="form-control"
                                    value="{{ old('olt') }}" placeholder="Ex.: OLT-01" maxlength="255" required>

                            </div>

                            <div class="form-text">
                                Informe o nome ou identificação da OLT.
                            </div>

                        </div>

                        {{-- DESCRIÇÃO --}}
                        <div class="mb-3">

                            <label for="info" class="form-label fw-semibold">

                                Descrição
                                <span class="text-danger">*</span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-card-text"></i>
                                </span>

                                <input type="text" name="info" id="info" class="form-control"
                                    value="{{ old('info') }}" placeholder="Ex.: PON-01 / Slot 1 / Porta 2"
                                    maxlength="255" required>

                            </div>

                        </div>

                        {{-- SINAL --}}
                        <div class="mb-3">

                            <label for="signal" class="form-label fw-semibold">

                                Sinal

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-reception-4"></i>
                                </span>

                                <input type="text" name="signal" id="signal" class="form-control"
                                    value="{{ old('signal') }}" placeholder="Ex.: +4.00">

                                <span class="input-group-text">
                                    dBm
                                </span>

                            </div>

                        </div>

                        {{-- COORDENADAS --}}
                        <div class="mb-0">

                            <label for="coordinates" class="form-label fw-semibold">

                                Coordenadas
                                <span class="text-danger">*</span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt"></i>
                                </span>

                                <input type="text" name="coordinates" id="coordinates" class="form-control"
                                    value="{{ old('coordinates') }}" placeholder="Ex.: -10.12345, -62.12345"
                                    maxlength="255" required>

                            </div>

                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer">

                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="submit" class="btn dcm-btn-primary px-4">

                            <i class="bi bi-check-lg me-1"></i>
                            Salvar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection


@push('scripts')

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('modalPon');

                if (modalElement) {
                    const modalPon = new bootstrap.Modal(modalElement);
                    modalPon.show();
                }
            });
        </script>
    @endif

@endpush
