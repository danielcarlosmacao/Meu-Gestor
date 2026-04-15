@extends('layouts.header')
@section('title', 'FTTH - PONs')

@section('content')

    <div class="container mb-1 mb-md-4 mt-1 mt-md-4">
        <h2 class="text-center">
            PONs da OLT
        </h2>
    </div>

    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-3">
            @can('ftth.create')
                <button class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalPon">
                    Nova PON
                </button>
            @endcan
        </div>
        <div class="card">
            <div class="card-body">

                <table class="table table-striped">

                    <thead>
                        <tr>
                            <th>OLT</th>
                            <th>Descrição</th>
                            <th>SINAL</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($pons as $pon)
                            <tr>
                                <td>{{ $pon->olt }}</td>
                                <td>
                                    <a href="{{ route('fiberbox.index', ['pon' => $pon->id]) }}"
                                        class="text-dark text-decoration-none fw-bold">{{ $pon->info }}
                                    </a>
                                </td>

                                <td>{{ $pon->signal }}</td>

                                <td>
                                    @can('ftth.delete')
                                        <form method="POST" action="{{ route('pon.destroy', $pon->id) }}"
                                            style="display:inline">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tem certeza que deseja excluir este registro? Essa ação não pode ser desfeita.')">
                                                Excluir
                                            </button>
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

    </div>
    <div class="modal fade" id="modalPon">

        <div class="modal-dialog">

            <form method="POST" action="{{ route('pon.store') }}">

                @csrf

                <div class="modal-content">

                    {{-- HEADER --}}
                    <div class="modal-header bgc-primary text-white">
                        <h5 class="modal-title fw-bold">
                            Nova PON
                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">

                        {{-- OLT --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">OLT</label>
                            <input name="olt" class="form-control shadow-sm"
                                placeholder="Ex: OLT-01 / Slot 1 / Porta 2">
                        </div>

                        {{-- DESCRIÇÃO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrição</label>
                            <input name="info" class="form-control shadow-sm" placeholder="Ex: PON-01">
                        </div>

                        {{-- SINAL --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sinal</label>
                            <input name="signal" class="form-control shadow-sm" placeholder="Ex: 4 dBm">
                        </div>

                        {{-- COORDENADAS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Coordenadas</label>
                            <input name="coordinates" class="form-control shadow-sm" placeholder="Ex: -10.12345, -62.12345">
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button class="btn btn-primary px-4">
                            Salvar
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
