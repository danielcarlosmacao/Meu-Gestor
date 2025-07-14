<div class="mb-3">
    <label class="form-label fw-semibold">Veículo</label>
    <select name="vehicle_id" class="form-select rounded-pill vehicle-select" required>
        <option value="">Selecione o veículo</option>
        @foreach($vehicles as $v)
            <option value="{{ $v->id }}" data-vehicle-type="{{ strtolower($v->type) }}" {{ isset($maintenance) && $maintenance->vehicle_id == $v->id ? 'selected' : '' }}>
                {{ $v->license_plate }} - {{ $v->brand }} {{ $v->model }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="maintenance_date" class="form-label fw-semibold">Data</label>
<input
    type="text"
    name="maintenance_date"
    class="form-control rounded-pill datepicker"
    value="{{ old('maintenance_date', isset($maintenance) ? $maintenance->maintenance_date : '') }}"
    placeholder="dd/mm/aaaa"
    required
>
</div>



<div class="mb-3">
    <label class="form-label fw-semibold">Tipo</label>
    <select name="type" class="form-select rounded-pill" required>
        <option value="preventive" {{ isset($maintenance) && $maintenance->type === 'preventive' ? 'selected' : '' }}>Preventiva</option>
        <option value="corrective" {{ isset($maintenance) && $maintenance->type === 'corrective' ? 'selected' : '' }}>Corretiva</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold" id="mileage-label">Quilometragem</label>
    <input type="number" name="mileage" class="form-control rounded-pill" value="{{ $maintenance->mileage ?? '' }}">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Valor (R$)</label>
    <input type="number" name="cost" step="0.01" class="form-control rounded-pill" value="{{ $maintenance->cost ?? '' }}">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Status</label>
    <select name="status" class="form-select rounded-pill" required>
        <option value="pending" {{ isset($maintenance) && $maintenance->status === 'pending' ? 'selected' : '' }}>Pendente</option>
        <option value="completed" {{ isset($maintenance) && $maintenance->status === 'completed' ? 'selected' : '' }}>Concluída</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Oficina</label>
    <select name="workshop" class="form-select rounded-pill workshop-select">
        @foreach ($workshops as $workshop)
            <option value="{{ $workshop->name }}" data-workshop-type="{{ strtolower($workshop->vehicle_type) }}" {{ isset($maintenance) && $maintenance->workshop == $workshop->name ? 'selected' : '' }}>
                {{ $workshop->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Serviços Realizados</label>
    <div id="services-checkboxes" class="d-flex flex-wrap gap-2 mt-2">
        @foreach($vehicleServices as $service)
            <div class="form-check service-checkbox" data-service-type="{{ strtolower($service->vehicle_type) }}" style="display: none;">
                <input class="form-check-input" type="checkbox" name="vehicle_services[]" id="service{{ $service->id }}" value="{{ $service->id }}"
                    @if(isset($maintenance) && $maintenance->services->contains($service->id)) checked @endif>
                <label class="form-check-label" for="service{{ $service->id }}">
                    {{ $service->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Informações Adicionais</label>
    <textarea name="parts_used" class="form-control rounded-3" rows="3">{{ $maintenance->parts_used ?? '' }}</textarea>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#maintenance_date", {
            dateFormat: "d/m/Y",       // Formato legível dia/mês/ano
            locale: {
                firstDayOfWeek: 1      // Semana começa na segunda (opcional)
            },
            allowInput: true
        });
    });
</script>
