@extends('layouts.header')

@section('title', 'FTTH - Boxes')

@section('content')


    <div class="container mb-1 mb-md-4 mt-1 mt-md-4">
        <h2 class="text-center">
            {{ $pon->info }}
            <a href="{{ route('pon.index') }}" class="btn dcm-btn-primary">
                <i class="bi bi-house"></i>
            </a>
            <a href="{{ route('fiberbox.index', ['pon' => $pon->id, 'map' => 'yes']) }}" class="btn dcm-btn-primary ">
                <i class="bi bi-globe-americas"></i>
            </a>
        </h2>
    </div>

    <div class="container-fluid">

        <div class="d-flex justify-content-end mb-3 gap-2">
            @can('ftth.create')
                <button class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalBox">
                    Nova Box
                </button>
            @endcan


        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <table class="table table-striped">
                    <thead class="bgc-primary text-white">
                        <tr>
                            <th>Numero</th>
                            <th>Descrição</th>
                            <th width="120" class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($boxes as $box)
                            <tr>

                                <td>
                                    {{ $box->number }}

                                </td>

                                <td>
                                    <a href="{{ route('fiberbox.show', $box->id) }}"
                                        class="text-dark text-decoration-none fw-bold">{{ $box->info }}</a>
                                </td>

                                <td class="text-end">
                                    @can('ftth.delete')
                                        {{-- ÚNICA AÇÃO: EXCLUIR --}}
                                        <form method="POST" action="{{ route('fiberbox.destroy', $box->id) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tem certeza que deseja excluir esta caixa?')">
                                                Excluir
                                            </button>

                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Nenhuma box encontrada
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalBox">

        <div class="modal-dialog">

            <form method="POST" action="{{ route('fiberbox.store') }}">

                @csrf

                <input type="hidden" name="pon_id" value="{{ $pon->id }}">

                <div class="modal-content">

                    {{-- HEADER --}}
                    <div class="modal-header bgc-primary text-white">
                        <h5 class="modal-title fw-bold">Nova Caixa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- NUMERO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Número</label>
                            <span class="badge bg-success">
                                Menor disponivel: {{ $nextnumber }}
                            </span>
                            <span class="badge bg-success">
                                Próximo: {{ $nextnumbermax }}
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <input name="number" type="number" class="form-control shadow-sm" step="1"
                                    min="1" required>


                            </div>
                        </div>

                        {{-- DESCRIÇÃO --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrição</label>
                            <input name="info" class="form-control shadow-sm">
                        </div>

                        {{-- COORDENADAS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Coordenadas</label>
                            <input name="coordinates" class="form-control shadow-sm">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            Salvar
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>
@endsection
