@php
    $maintenanceId = $maintenance->id ?? 'new';
@endphp

{{-- ============================================================
    DADOS DA MANUTENÇÃO
============================================================= --}}

<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-tools"></i>
        Dados da manutenção
    </h6>

    <div class="row g-3">

        <div class="col-md-7">

            <label for="service_client_id_{{ $maintenanceId }}" class="form-label">

                Cliente
                <span class="service-required">*</span>

            </label>

            <select name="service_client_id" id="service_client_id_{{ $maintenanceId }}" class="form-select" required>

                <option value="">
                    Selecione um cliente
                </option>

                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected(old('service_client_id', $maintenance->service_client_id ?? '') == $client->id)>

                        {{ $client->name }}

                    </option>
                @endforeach

            </select>

            <div class="invalid-feedback">
                Selecione um cliente.
            </div>

        </div>

        <div class="col-md-5">

            <label for="date_maintenance_{{ $maintenanceId }}" class="form-label">

                Data da manutenção
                <span class="service-required">*</span>

            </label>

            <input type="date" name="date_maintenance" id="date_maintenance_{{ $maintenanceId }}"
                class="form-control"
                value="{{ old(
                    'date_maintenance',
                    isset($maintenance) && $maintenance->date_maintenance ? $maintenance->date_maintenance->format('Y-m-d') : '',
                ) }}"
                required>

            <div class="invalid-feedback">
                Informe a data da manutenção.
            </div>

        </div>

        <div class="col-12">

            <label for="maintenance_{{ $maintenanceId }}" class="form-label">

                Serviço realizado
                <span class="service-required">*</span>

            </label>

            <textarea name="maintenance" id="maintenance_{{ $maintenanceId }}" class="form-control" rows="4" maxlength="2000"
                placeholder="Descreva o serviço, reparo ou atendimento realizado" required>{{ old('maintenance', $maintenance->maintenance ?? '') }}</textarea>

            <div class="invalid-feedback">
                Descreva o serviço realizado.
            </div>

            <div class="service-form-help">
                Informe os procedimentos executados e outras observações importantes.
            </div>

        </div>

    </div>

</div>

{{-- ============================================================
    CUSTOS
============================================================= --}}

<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-cash-coin"></i>
        Custos do serviço
    </h6>

    <div class="row g-3">

        <div class="col-md-6">

            <label for="cost_enterprise_{{ $maintenanceId }}" class="form-label">

                Custo da empresa

            </label>

            <div class="input-group">

                <span class="input-group-text">
                    R$
                </span>

                <input type="number" name="cost_enterprise" id="cost_enterprise_{{ $maintenanceId }}"
                    class="form-control"
                    value="{{ old('cost_enterprise', $maintenance->cost_enterprise ?? '0.00') }}"
                    min="0" step="0.01" inputmode="decimal" placeholder="0,00">

            </div>

            <div class="service-form-help">
                Valor gasto pela empresa para realizar o serviço.
            </div>

        </div>

        <div class="col-md-6">

            <label for="cost_client_{{ $maintenanceId }}" class="form-label">

                Custo do cliente

            </label>

            <div class="input-group">

                <span class="input-group-text">
                    R$
                </span>

                <input type="number" name="cost_client" id="cost_client_{{ $maintenanceId }}" class="form-control"
                    value="{{ old('cost_client', $maintenance->cost_client ?? '0.00') }}"
                    min="0" step="0.01" inputmode="decimal" placeholder="0,00">

            </div>

            <div class="service-form-help">
                Valor cobrado ou que será cobrado do cliente.
            </div>

        </div>

    </div>

</div>
