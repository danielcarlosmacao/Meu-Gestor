{{-- Veículo --}}
<div class="mb-2">
    <label>Veículo</label>
    <select name="vehicle_id" class="form-control vehicle-select" required>
        <option value="">Selecione o veículo</option>
        @foreach($vehicles as $v)
            <option value="{{ $v->id }}"
                data-vehicle-type="{{ strtolower($v->type) }}"
                {{ isset($maintenance) && $maintenance->vehicle_id == $v->id ? 'selected' : '' }}>
                {{ $v->license_plate }} - {{ $v->brand }} {{ $v->model }}
            </option>
        @endforeach
    </select>
</div>

{{-- Data --}}
<div class="mb-2">
    <label>Data</label>
    <input type="date" name="maintenance_date" class="form-control"
        value="{{ isset($maintenance) ? \Carbon\Carbon::parse($maintenance->maintenance_date)->format('Y-m-d') : '' }}"
        required>
</div>

{{-- Tipo --}}
<div class="mb-2">
    <label>Tipo</label>
    <select name="type" class="form-control" required>
        <option value="preventive" {{ isset($maintenance) && $maintenance->type === 'preventive' ? 'selected' : '' }}>Preventiva</option>
        <option value="corrective" {{ isset($maintenance) && $maintenance->type === 'corrective' ? 'selected' : '' }}>Corretiva</option>
    </select>
</div>

{{-- Quilometragem --}}
<div class="mb-2">
    <label>Quilometragem</label>
    <input type="number" name="mileage" class="form-control" value="{{ $maintenance->mileage ?? '' }}">
</div>

{{-- Valor --}}
<div class="mb-2">
    <label>Valor (R$)</label>
    <input type="number" name="cost" step="0.01" class="form-control" value="{{ $maintenance->cost ?? '' }}">
</div>

{{-- Status --}}
<div class="mb-2">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="pending" {{ isset($maintenance) && $maintenance->status === 'pending' ? 'selected' : '' }}>Pendente</option>
        <option value="completed" {{ isset($maintenance) && $maintenance->status === 'completed' ? 'selected' : '' }}>Concluída</option>
    </select>
</div>

{{-- Oficina --}}
<div class="mb-2">
    <label>Oficina</label>
    <select name="workshop" class="form-control workshop-select">
        @foreach ($workshops as $workshop)
            <option 
                value="{{ $workshop->name }}" 
                data-workshop-type="{{ strtolower($workshop->vehicle_type) }}"
                {{ isset($maintenance) && $maintenance->workshop == $workshop->name ? 'selected' : '' }}>
                {{ $workshop->name }}
            </option>
        @endforeach
    </select>
</div>


{{-- Serviços Realizados --}}
<div class="mb-2">
    <label>Serviços Realizados</label>
    
    <div id="services-checkboxes" class="d-flex flex-wrap gap-3 mt-2">
        @foreach($vehicleServices as $service)
            <div class="service-checkbox" 
                 data-service-type="{{ strtolower($service->vehicle_type) }}" 
                 style="display: none;">
                <input 
                    type="checkbox" 
                    name="vehicle_services[]" 
                    id="service{{ $service->id }}" 
                    value="{{ $service->id }}"
                    @if(isset($maintenance) && $maintenance->services->contains($service->id)) checked @endif
                >
                <label for="service{{ $service->id }}">
                    {{ $service->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>


{{-- parts_used --}}
<div class="mb-2">
    <label>Info</label>
    <textarea type="text" name="parts_used" class="form-control" >{{ $maintenance->parts_used ?? '' }}</textarea>
</div>