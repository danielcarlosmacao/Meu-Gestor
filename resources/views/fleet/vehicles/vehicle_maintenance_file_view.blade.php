@extends('layouts.header')

@section('title', 'Visualizar arquivo')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

            <div>

                <h4 class="fw-bold mb-1">
                    {{ $file->original_name }}
                </h4>

                <div class="text-secondary">

                    {{ $file->maintenance?->vehicle?->license_plate ?? 'Veículo' }}

                    @if ($file->maintenance)
                        -

                        {{ \Carbon\Carbon::parse($file->maintenance->maintenance_date)->format('d/m/Y') }}
                    @endif

                </div>

            </div>


            <div class="d-flex gap-2 mt-3 mt-md-0">

                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i>
                    Voltar
                </a>


                <form
                    action="{{ route('fleet.vehicle_maintenances.files.delete', $file->token) }}"
                    method="POST" onsubmit="return confirm('Deseja excluir este arquivo definitivamente?');">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-trash"></i>
                        Excluir
                    </button>

                </form>

            </div>

        </div>


        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body p-0" style="background:#f5f5f5;">

                @if ($file->mime_type === 'application/pdf')
                    <iframe src="{{ $file->url }}"
                        style="
                        width:100%;
                        height:80vh;
                        border:0;
                    "
                        title="{{ $file->original_name }}"></iframe>
                @elseif (str_starts_with($file->mime_type, 'image/'))
                    <div class="d-flex justify-content-center align-items-center p-4" style="min-height:70vh;">

                        <img src="{{ $file->url }}" alt="{{ $file->original_name }}"
                            style="
                            max-width:100%;
                            max-height:75vh;
                            object-fit:contain;
                        "
                            class="rounded-3 shadow-sm">

                    </div>
                @else
                    <div class="text-center p-5">

                        <i class="bi bi-file-earmark fs-1"></i>

                        <h5 class="mt-3">
                            Visualização não disponível
                        </h5>

                        <a href="{{ $file->url }}" target="_blank" class="btn dcm-btn-primary rounded-pill mt-2">
                            Abrir arquivo
                        </a>

                    </div>
                @endif

            </div>

        </div>

    </div>

@endsection
