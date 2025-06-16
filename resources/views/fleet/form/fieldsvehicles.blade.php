<div class="mb-2">
    <label>Placa</label>
    <input type="text" name="license_plate" class="form-control" value="{{ $vehicle->license_plate ?? '' }}" required>
</div>
<div class="mb-2">
    <label>Marca</label>
    <input type="text" name="brand" class="form-control" value="{{ $vehicle->brand ?? '' }}" required>
</div>
<div class="mb-2">
    <label>Modelo</label>
    <input type="text" name="model" class="form-control" value="{{ $vehicle->model ?? '' }}" required>
</div>
<div class="mb-2">
    <label for="type">Tipo</label>
    <select name="type" id="type" class="form-control" required>
        <option value="">Selecione o tipo</option>
        <option value="car" {{ (isset($vehicle) && $vehicle->type === 'car') ? 'selected' : '' }}>Carro</option>
        <option value="motorcycle" {{ (isset($vehicle) && $vehicle->type === 'motorcycle') ? 'selected' : '' }}>Moto</option>
        <option value="truck" {{ (isset($vehicle) && $vehicle->type === 'truck') ? 'selected' : '' }}>Caminhão</option>
        <option value="others" {{ (isset($vehicle) && $vehicle->type === 'others') ? 'selected' : '' }}>Outro</option>
    </select>
</div>
<div class="mb-2">
    <label>Ano</label>
    <input type="number" name="year" class="form-control" value="{{ $vehicle->year ?? '' }}" required>
</div>
<div class="mb-2">
    <label>Combustível</label>
    <input type="text" name="fuel_type" class="form-control" value="{{ $vehicle->fuel_type ?? '' }}" required>
</div>
<div class="mb-2">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="active" {{ isset($vehicle) && $vehicle->status == 'active' ? 'selected' : '' }}>Ativo</option>
        <option value="inactive" {{ isset($vehicle) && $vehicle->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
    </select>
</div>
