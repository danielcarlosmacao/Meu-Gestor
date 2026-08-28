@php
    $isEdit = isset($maintenance) && !is_null($maintenance);

    /*
    |--------------------------------------------------------------------------
    | SUFIXO ÚNICO DOS CAMPOS
    |--------------------------------------------------------------------------
    */
    $fieldSuffix = $isEdit ? 'edit_' . $maintenance->id : 'create';

    /*
    |--------------------------------------------------------------------------
    | DATA DA MANUTENÇÃO
    |--------------------------------------------------------------------------
    |
    | Na edição, usa o valor bruto salvo no banco para evitar que accessor,
    | cast ou formatação do model transforme a data incorretamente.
    |
    */
    $maintenanceDateValue = '';

    if ($isEdit) {
        $rawMaintenanceDate = method_exists($maintenance, 'getRawOriginal')
            ? $maintenance->getRawOriginal('maintenance_date')
            : $maintenance->maintenance_date;

        if (!empty($rawMaintenanceDate)) {
            try {
                $maintenanceDateValue = \Carbon\Carbon::parse($rawMaintenanceDate)->format('d/m/Y');
            } catch (\Throwable $exception) {
                $maintenanceDateValue = '';
            }
        }
    } else {
        $oldMaintenanceDate = old('maintenance_date');

        if (!empty($oldMaintenanceDate)) {
            try {
                $maintenanceDateValue = \Carbon\Carbon::createFromFormat('d/m/Y', $oldMaintenanceDate)->format('d/m/Y');
            } catch (\Throwable $exception) {
                try {
                    $maintenanceDateValue = \Carbon\Carbon::parse($oldMaintenanceDate)->format('d/m/Y');
                } catch (\Throwable $exception) {
                    $maintenanceDateValue = '';
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DEMAIS VALORES
    |--------------------------------------------------------------------------
    */

    $selectedVehicleId = $isEdit ? $maintenance->vehicle_id : old('vehicle_id');

    $selectedType = $isEdit ? $maintenance->type : old('type', 'preventive');

    $selectedStatus = $isEdit ? $maintenance->status : old('status', 'pending');

    $selectedWorkshop = $isEdit ? $maintenance->workshop : old('workshop');

    $mileageValue = $isEdit ? $maintenance->mileage : old('mileage');

    $costValue = $isEdit ? $maintenance->cost : old('cost');

    $partsUsedValue = $isEdit ? $maintenance->parts_used : old('parts_used');

    /*
    |--------------------------------------------------------------------------
    | PERMITIR QUILOMETRAGEM MENOR
    |--------------------------------------------------------------------------
    |
    | Na criação, mantém o valor após erro de validação.
    | Na edição, começa desmarcado e o usuário marca quando precisar.
    |
    */
    $allowLowerMileageChecked = old('allow_lower_mileage', false) == '1';
@endphp

{{-- VEÍCULO --}}
<div class="mb-3">
    <label for="vehicle_id_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Veículo
    </label>

    <select id="vehicle_id_{{ $fieldSuffix }}" name="vehicle_id"
        class="form-select rounded-pill vehicle-select @error('vehicle_id') is-invalid @enderror" required>
        <option value="">Selecione o veículo</option>

        @foreach ($vehicles as $v)
            <option value="{{ $v->id }}" data-vehicle-type="{{ strtolower($v->type) }}"
                @selected((string) $selectedVehicleId === (string) $v->id)>
                {{ $v->license_plate }} - {{ $v->brand }} {{ $v->model }}
            </option>
        @endforeach
    </select>

    @error('vehicle_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- DATA --}}
<div class="mb-3">
    <label for="maintenance_date_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Data
    </label>

    <input type="text" id="maintenance_date_{{ $fieldSuffix }}" name="maintenance_date"
        class="form-control rounded-pill maintenance-datepicker" value="{{ $maintenanceDateValue }}"
        data-initial-date="{{ $maintenanceDateValue }}" placeholder="dd/mm/aaaa" autocomplete="off" required>
</div>

{{-- TIPO --}}
<div class="mb-3">
    <label for="maintenance_type_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Tipo
    </label>

    <select id="maintenance_type_{{ $fieldSuffix }}" name="type"
        class="form-select rounded-pill @error('type') is-invalid @enderror" required>
        <option value="preventive" @selected($selectedType === 'preventive')>
            Preventiva
        </option>

        <option value="corrective" @selected($selectedType === 'corrective')>
            Corretiva
        </option>
    </select>

    @error('type')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- QUILOMETRAGEM --}}
<div class="mb-3">
    <label for="mileage_{{ $fieldSuffix }}" class="form-label fw-semibold" data-mileage-label>
        Quilometragem
    </label>

    <input type="number" id="mileage_{{ $fieldSuffix }}" name="mileage"
        class="form-control rounded-pill @error('mileage') is-invalid @enderror" value="{{ $mileageValue }}"
        min="0" step="1" data-mileage-input>

    @error('mileage')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

    <div class="form-check mt-2">
        <input type="checkbox" id="allow_lower_mileage_{{ $fieldSuffix }}" name="allow_lower_mileage" value="1"
            class="form-check-input allow-lower-mileage" @checked($allowLowerMileageChecked)>

        <label class="form-check-label" for="allow_lower_mileage_{{ $fieldSuffix }}">
            Permitir quilometragem menor que a última registrada
        </label>
    </div>

    <div class="form-text" data-mileage-help>
        A quilometragem deve ser igual ou maior que a última manutenção.
    </div>
</div>

{{-- VALOR --}}
<div class="mb-3">
    <label for="cost_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Valor (R$)
    </label>

    <input type="number" id="cost_{{ $fieldSuffix }}" name="cost" step="0.01" min="0"
        class="form-control rounded-pill @error('cost') is-invalid @enderror" value="{{ $costValue }}">

    @error('cost')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- STATUS --}}
<div class="mb-3">
    <label for="maintenance_status_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Status
    </label>

    <select id="maintenance_status_{{ $fieldSuffix }}" name="status"
        class="form-select rounded-pill @error('status') is-invalid @enderror" required>
        <option value="pending" @selected($selectedStatus === 'pending')>
            Pendente
        </option>

        <option value="completed" @selected($selectedStatus === 'completed')>
            Concluída
        </option>
    </select>

    @error('status')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- OFICINA --}}
<div class="mb-3">
    <label for="workshop_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Oficina
    </label>

    <select id="workshop_{{ $fieldSuffix }}" name="workshop"
        class="form-select rounded-pill workshop-select @error('workshop') is-invalid @enderror">
        <option value="">Selecione uma oficina</option>

        @foreach ($workshops as $workshop)
            <option value="{{ $workshop->name }}" data-workshop-type="{{ strtolower($workshop->vehicle_type) }}"
                @selected((string) $selectedWorkshop === (string) $workshop->name)>
                {{ $workshop->name }}
            </option>
        @endforeach
    </select>

    @error('workshop')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- SERVIÇOS --}}
<div class="mb-3">
    <label class="form-label fw-semibold">
        Serviços realizados
    </label>

    <div class="d-flex flex-wrap gap-2 mt-2" data-services-checkboxes>
        @foreach ($vehicleServices as $service)
            @php
                $serviceChecked = $isEdit
                    ? $maintenance->services->contains($service->id)
                    : in_array($service->id, array_map('intval', old('vehicle_services', [])), true);
            @endphp

            <div class="form-check service-checkbox" data-service-type="{{ strtolower($service->vehicle_type) }}"
                style="display: none;">
                <input type="checkbox" id="service_{{ $fieldSuffix }}_{{ $service->id }}" name="vehicle_services[]"
                    value="{{ $service->id }}" class="form-check-input" @checked($serviceChecked)>

                <label class="form-check-label" for="service_{{ $fieldSuffix }}_{{ $service->id }}">
                    {{ $service->name }}
                </label>
            </div>
        @endforeach
    </div>

    @error('vehicle_services')
        <div class="text-danger small mt-2">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- INFORMAÇÕES --}}
<div class="mb-3">
    <label for="parts_used_{{ $fieldSuffix }}" class="form-label fw-semibold">
        Informações adicionais
    </label>

    <textarea id="parts_used_{{ $fieldSuffix }}" name="parts_used"
        class="form-control rounded-3 @error('parts_used') is-invalid @enderror" rows="3">{{ $partsUsedValue }}</textarea>

    @error('parts_used')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
