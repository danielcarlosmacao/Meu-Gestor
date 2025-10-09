@extends('layouts.header')
@section('title', 'Manutenção de Equipamentos')
@section('content')

    <div class="container mt-5">
        <h2>Manutenção de Equipamentos
            @can('service.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

        <table class="table table-striped mt-3">
            <thead class="bgc-primary">
                <tr>
                    <th>Cliente</th>
                    <th>Assistência Tecnica</th>
                    <th>Equipamento</th>
                    <th>Erro</th>
                    <th>Data Manutenção</th>
                    <th>Data Envio</th>
                    <th>Data Recebimento</th>
                    <th>Solução</th>
                    <th>Custo Empresa</th>
                    <th>Custo Cliente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        @php
                            $fullText = $maintenance->solution;
                            $shortText = \Illuminate\Support\Str::limit($fullText, 15, '...');
                        @endphp
                        <td>{{ $maintenance->serviceClient->name ?? 'N/D' }}</td>
                        <td>{{ $maintenance->assistance }}</td>
                        <td>{{ $maintenance->equipment }}</td>
                        <td>{{ $maintenance->erro }}</td>
                        <td>{{ optional($maintenance->date_maintenance)->format('d/m/Y') }}</td>
                        <td>{{ optional($maintenance->date_send)->format('d/m/Y') }}</td>
                        <td>{{ optional($maintenance->date_received)->format('d/m/Y') }}</td>
                        <td>
                            <div>
                                <span id="solution-text-{{ $maintenance->id }}">{{ $shortText }}</span>
                                @if (strlen($fullText) > 50)
                                    <a href="javascript:void(0);" class="text-primary ms-1 d-inline-block"
                                        onclick="toggleSolutionText(this)" data-full-text="{{ $fullText }}"
                                        data-short-text="{{ $shortText }}"
                                        data-target="solution-text-{{ $maintenance->id }}" data-state="short">
                                        Mostrar mais
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>R$ {{ number_format($maintenance->cost_enterprise, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($maintenance->cost_client, 2, ',', '.') }}</td>
                        <td>
                            @can('service.edit')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">Editar</button>
                            @endcan
                        </td>
                    </tr>

                    {{-- Modal Editar --}}
                    @include('service.forms.equipment_maintenance', ['maintenance' => $maintenance])
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $maintenances->links() }}
        </div>

        {{-- Modal Adicionar --}}
        @include('service.forms.equipment_maintenance', ['maintenance' => null])
    </div>

    <script>
        function toggleSolutionText(link) {
            const targetId = link.getAttribute('data-target');
            const textEl = document.getElementById(targetId);

            const fullText = link.getAttribute('data-full-text');
            const shortText = link.getAttribute('data-short-text');
            const currentState = link.getAttribute('data-state');

            if (currentState === 'short') {
                textEl.innerText = fullText;
                link.innerText = 'Mostrar menos';
                link.setAttribute('data-state', 'full');
            } else {
                textEl.innerText = shortText;
                link.innerText = 'Mostrar mais';
                link.setAttribute('data-state', 'short');
            }
        }
    </script>


@endsection
