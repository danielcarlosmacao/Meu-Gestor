@extends('layouts.header')

@section('title', 'FTTH - Boxes')

@section('content')

    <div class="container mb-1 mb-md-4 mt-1 mt-md-4">
        <h2 class="text-center">

            {{ $pon->info }}

            <a href="{{ route('pon.index') }}" class="btn dcm-btn-primary" title="Voltar para PONs">

                <i class="bi bi-house"></i>
            </a>

            <a href="{{ route('fiberbox.index', [
                'pon' => $pon->id,
                'map' => 'yes',
            ]) }}"
                class="btn dcm-btn-primary" title="Visualizar mapa">

                <i class="bi bi-globe-americas"></i>
            </a>

        </h2>
    </div>

    <div class="container-fluid">

        <div class="d-flex justify-content-end mb-3 gap-2">

            @can('ftth.create')
                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalBox">

                    <i class="bi bi-plus-lg me-1"></i>
                    Nova Box
                </button>
            @endcan

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-striped table-hover align-middle">

                        <thead class="bgc-primary text-white">
                            <tr>
                                <th>Número</th>
                                <th>Descrição</th>
                                <th>PON</th>
                                <th width="140" class="text-end">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($boxes as $box)
                                <tr>

                                    {{-- NÚMERO --}}
                                    <td>
                                        <span class="badge bg-secondary fs-6">
                                            {{ $box->number }}
                                        </span>
                                    </td>

                                    {{-- DESCRIÇÃO --}}
                                    <td>
                                        <a href="{{ route('fiberbox.show', $box->id) }}"
                                            class="text-dark text-decoration-none fw-bold">

                                            {{ $box->info ?: 'Sem descrição' }}
                                        </a>

                                        @if ($box->coordinates)
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-geo-alt"></i>
                                                {{ $box->coordinates }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- PON --}}
                                    <td>
                                        {{ $box->pon?->info ?? 'PON não encontrada' }}
                                    </td>

                                    {{-- AÇÕES --}}
                                    <td class="text-end">

                                        @can('ftth.create')
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-box"
                                                data-bs-toggle="modal" data-bs-target="#modalEditBox"
                                                data-id="{{ $box->id }}" data-number="{{ $box->number }}"
                                                data-info="{{ $box->info }}" data-coordinates="{{ $box->coordinates }}"
                                                data-pon-id="{{ $box->pon_id }}" title="Editar caixa">

                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan

                                        @can('ftth.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="openConfirmModal(
                                                    '{{ route('fiberbox.destroy', $box->id) }}',
                                                    'Tem certeza que deseja excluir esta caixa?',
                                                    'Essa alteração não poderá ser revertida.',
                                                    'DELETE'
                                                )"
                                                title="Excluir caixa">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">

                                        Nenhuma box encontrada
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- MODAL DE CRIAÇÃO --}}
    @include('ftth.modals.createbox')

    {{-- MODAL DE EDIÇÃO --}}
    @include('ftth.modals.editbox')

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const formEditBox = document.getElementById('formEditBox');

            const editBoxId = document.getElementById('editBoxId');
            const editNumber = document.getElementById('editNumber');
            const editInfo = document.getElementById('editInfo');
            const editCoordinates = document.getElementById('editCoordinates');
            const editPonId = document.getElementById('editPonId');

            const buttons = document.querySelectorAll('.btn-edit-box');

            buttons.forEach(function(button) {

                button.addEventListener('click', function() {

                    const boxId = this.dataset.id;

                    /*
                    |--------------------------------------------------------------------------
                    | ROTA DE ATUALIZAÇÃO
                    |--------------------------------------------------------------------------
                    |
                    | Resultado:
                    | /ftth/fiber-box/10
                    |
                    */

                    formEditBox.action =
                        `{{ url('/ftth/fiber-box') }}/${boxId}`;

                    /*
                    |--------------------------------------------------------------------------
                    | PREENCHE OS CAMPOS
                    |--------------------------------------------------------------------------
                    */

                    editBoxId.value = boxId ?? '';

                    editNumber.value =
                        this.dataset.number ?? '';

                    editInfo.value =
                        this.dataset.info ?? '';

                    editCoordinates.value =
                        this.dataset.coordinates ?? '';

                    editPonId.value =
                        this.dataset.ponId ?? '';
                });

            });

        });
    </script>
@endpush
