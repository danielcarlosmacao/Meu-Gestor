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
                                        <button class="btn btn-sm btn-outline-danger"
                                                    onclick="openConfirmModal(
                                                        '{{ route('fiberbox.destroy', $box->id) }}',
                                                        'Tem certeza que deseja excluir esta caixa?',
                                                        'Essa alteração não poderá ser revertida.',
                                                        'DELETE'
                                                    )">
                                                    
                                                    <i class="bi bi-trash"></i>
                                                </button>
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

    
    @include('ftth.modals.createbox')
@endsection
