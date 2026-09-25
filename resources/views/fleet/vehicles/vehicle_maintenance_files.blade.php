@extends('layouts.header')

@section('title', 'Arquivos das Manutenções')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

            <div>
                <h4 class="fw-bold mb-1">
                    Arquivos das Manutenções
                </h4>

                <div class="text-secondary">
                    Documentos e imagens vinculados às manutenções dos veículos.
                </div>
            </div>

            <div class="d-flex gap-2 mt-3 mt-md-0">

                <a href="{{ route('fleet.vehicle_maintenances.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i>
                    Manutenções
                </a>

                <button type="button" class="btn dcm-btn-primary rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#uploadMaintenanceFileModal">
                    <i class="bi bi-plus-lg"></i>
                    Adicionar arquivo
                </button>

            </div>

        </div>


        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th class="px-4">
                                    Veículo
                                </th>

                                <th>
                                    Manutenção
                                </th>

                                <th>
                                    Arquivo
                                </th>

                                <th>
                                    Tipo
                                </th>

                                <th>
                                    Tamanho
                                </th>

                                <th class="text-end px-4">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($files as $file)

                                @php
                                    $maintenance = $file->maintenance;
                                    $vehicle = $maintenance?->vehicle;
                                @endphp

                                <tr>

                                    <td class="px-4">

                                        @if ($vehicle)
                                            <strong>
                                                {{ $vehicle->model ?? 'Veículo' }}
                                            </strong>

                                            <div class="small text-secondary">
                                                {{ strtoupper($vehicle->license_plate ?? '-') }}
                                            </div>
                                        @else
                                            <span class="text-secondary">
                                                Veículo removido
                                            </span>
                                        @endif

                                    </td>


                                    <td>

                                        @if ($maintenance)
                                            <div>
                                                {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}
                                            </div>

                                            <small class="text-secondary">

                                                @if ($maintenance->type === 'preventive')
                                                    Preventiva
                                                @elseif ($maintenance->type === 'corrective')
                                                    Corretiva
                                                @else
                                                    {{ $maintenance->type }}
                                                @endif

                                            </small>
                                        @else
                                            -
                                        @endif

                                    </td>


                                    <td>

                                        <div class="text-truncate" style="max-width:260px"
                                            title="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </div>

                                    </td>


                                    <td>

                                        @if ($file->mime_type === 'application/pdf')
                                            <span class="badge text-bg-danger">
                                                PDF
                                            </span>
                                        @else
                                            <span class="badge text-bg-primary">
                                                IMAGEM
                                            </span>
                                        @endif

                                    </td>


                                    <td>
                                        {{ $file->formatted_size }}
                                    </td>


                                    <td class="text-end px-4">

                                        <a href="{{ route('fleet.vehicle_maintenances.files.view', $file->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-circle"
                                            title="Visualizar arquivo">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form
                                            action="{{ route('fleet.vehicle_maintenances.files.delete', $file->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Deseja excluir este arquivo?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"
                                                title="Excluir arquivo">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5 text-secondary">
                                        <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>

                                        Nenhum arquivo cadastrado.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        @if ($files->hasPages())
            <div class="mt-4">
                {{ $files->links() }}
            </div>
        @endif

    </div>


    {{-- ============================================================
MODAL UPLOAD
============================================================ --}}

    <div class="modal fade" id="uploadMaintenanceFileModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content border-0 rounded-4 shadow-lg">

                <form action="{{ route('fleet.vehicle_maintenances.files.upload') }}"
                    method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-header border-0">

                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-paperclip me-2"></i>
                            Adicionar arquivos
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>


                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Manutenção
                            </label>

                            <select name="vehicle_maintenance_id" class="form-select rounded-pill" required>

                                <option value="">
                                    Selecione a manutenção
                                </option>

                                @php
                                    $modalMaintenances = \App\Models\VehicleMaintenance::with('vehicle')
                                        ->orderByDesc('maintenance_date')
                                        ->get();
                                @endphp

                                @foreach ($modalMaintenances as $maintenance)
                                    <option value="{{ $maintenance->id }}">

                                        {{ $maintenance->vehicle->license_plate ?? 'Sem placa' }}

                                        -
                                        {{ $maintenance->vehicle->model ?? 'Veículo' }}

                                        |

                                        {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}

                                        -

                                        {{ $maintenance->type === 'preventive' ? 'Preventiva' : 'Corretiva' }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Arquivos
                            </label>

                            <input type="file" name="files[]" class="form-control rounded-3"
                                accept=".pdf,.jpg,.jpeg,.png,.webp" multiple required>

                            <div class="form-text">
                                Você pode selecionar um ou vários arquivos.
                                Formatos permitidos: PDF, JPG, JPEG, PNG e WEBP.
                                Máximo de 20 MB por arquivo.
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer border-0">

                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary rounded-pill">
                            <i class="bi bi-cloud-upload"></i>
                            Enviar arquivos
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
