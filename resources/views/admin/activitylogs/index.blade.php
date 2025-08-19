@extends('layouts.header')
@section('title', 'Logs de Atividade')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">📜 Logs de Atividade</h3>
    <div class="card shadow rounded-2xl">
        <div class="card-body">

            {{-- Paginação no topo --}}
            <div class="d-flex justify-content-end mb-2">
                {{ $logs->links() }}
            </div>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Modelo</th>
                        <th>ID</th>
                        <th>Detalhes</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $action = $log->description; 
                            $rowClass = '';
                            $badgeClass = 'secondary';
                            $icon = 'bi-circle';

                            if (\Illuminate\Support\Str::contains($action, ['Criado','Criada'])) {
                                $rowClass = 'table-success';
                                $badgeClass = 'success';
                                $icon = 'bi-plus-circle';
                            } elseif (\Illuminate\Support\Str::contains($action, ['Atualizado','Atualizada'])) {
                                $rowClass = 'table-warning';
                                $badgeClass = 'warning';
                                $icon = 'bi-pencil-square';
                            } elseif (\Illuminate\Support\Str::contains($action, ['Deletado','Deletada'])) {
                                $rowClass = 'table-danger';
                                $badgeClass = 'danger';
                                $icon = 'bi-trash';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $log->causer?->name ?? 'Sistema' }}</td>
                            <td>
                                <span class="badge bg-{{ $badgeClass }}">
                                    <i class="bi {{ $icon }}"></i>
                                    {{ $log->description }}
                                </span>
                            </td>
                            <td>{{ class_basename($log->subject_type) ?? '-' }}</td>
                            <td>{{ $log->subject_id ?? '-' }}</td>
                            <td>
                                @if($log->properties)
                                    <details>
                                        <summary class="text-primary small">Ver detalhes</summary>
                                        <pre class="small text-muted mt-2">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Nenhum log encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginação no rodapé --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
