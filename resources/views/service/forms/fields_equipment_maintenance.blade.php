<div class="mb-3">
    <label for="service_client_id" class="form-label">Cliente</label>
    <select name="service_client_id" id="service_client_id" class="form-control" required>
        <option value="">Selecione um cliente</option>
        @foreach (\App\Models\ServiceClient::all() as $client)
            <option value="{{ $client->id }}"
                {{ isset($maintenance) && $maintenance->service_client_id == $client->id ? 'selected' : '' }}>
                {{ $client->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="assistance" class="form-label">Assistência</label>
    <input type="text" name="assistance" id="assistance" class="form-control"
        value="{{ $maintenance->assistance ?? '' }}" required>
</div>

<div class="mb-3">
    <label for="equipment" class="form-label">Equipamento</label>
    <input type="text" name="equipment" id="equipment" class="form-control"
        value="{{ $maintenance->equipment ?? '' }}" required>
</div>

<div class="mb-3">
    <label for="erro" class="form-label">Erro</label>
    <input type="text" name="erro" id="erro" class="form-control" value="{{ $maintenance->erro ?? '' }}"
        required>
</div>

<div class="mb-3">
    <label for="date_maintenance" class="form-label">Data Manutenção</label>
    <input type="date" name="date_maintenance" id="date_maintenance" class="form-control"
        value="{{ isset($maintenance) ? $maintenance->date_maintenance?->format('Y-m-d') : '' }}" required>
</div>

<div class="mb-3">
    <label for="date_send" class="form-label">Data Envio</label>
    <input type="date" name="date_send" id="date_send" class="form-control"
        value="{{ isset($maintenance) ? $maintenance->date_send?->format('Y-m-d') : '' }}">
</div>

<div class="mb-3">
    <label for="date_received" class="form-label">Data Recebimento</label>
    <input type="date" name="date_received" id="date_received" class="form-control"
        value="{{ isset($maintenance) ? $maintenance->date_received?->format('Y-m-d') : '' }}">
</div>

<div class="mb-3">
    <label for="solution" class="form-label">Solução</label>
    <textarea name="solution" id="solution" class="form-control" rows="4">{{ $maintenance->solution ?? '' }}</textarea>
</div>

<div class="mb-3">
    <label for="cost_enterprise" class="form-label">Custo Empresa</label>
    <input type="number" step="0.01" name="cost_enterprise" id="cost_enterprise" class="form-control"
        value="{{ $maintenance->cost_enterprise ?? '0.00' }}">
</div>

<div class="mb-3">
    <label for="cost_client" class="form-label">Custo Cliente</label>
    <input type="number" step="0.01" name="cost_client" id="cost_client" class="form-control"
        value="{{ $maintenance->cost_client ?? '0.00' }}">
</div>
