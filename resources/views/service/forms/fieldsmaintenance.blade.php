<div class="mb-2">
    <label>Cliente</label>
    <select name="service_client_id" class="form-control" required>
        <option value="">Selecione</option>
        @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ (isset($maintenance) && $maintenance->service_client_id == $client->id) ? 'selected' : '' }}>
                {{ $client->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-2">
    <label>Data da Manutenção</label>
    <input type="date" name="date_maintenance" class="form-control" 
        value="{{ isset($maintenance) ? $maintenance->date_maintenance->format('Y-m-d') : '' }}" required>
</div>

<div class="mb-2">
    <label>Serviço</label>
    <input type="text" name="maintenance" class="form-control" 
        value="{{ $maintenance->maintenance ?? '' }}" required>
</div>

<div class="mb-2">
    <label>Custo da Empresa (R$)</label>
    <input type="number" step="0.01" name="cost_enterprise" class="form-control" 
        value="{{ $maintenance->cost_enterprise ?? '' }}">
</div>

<div class="mb-2">
    <label>Custo do Cliente (R$)</label>
    <input type="number" step="0.01" name="cost_client" class="form-control" 
        value="{{ $maintenance->cost_client ?? '' }}">
</div>
