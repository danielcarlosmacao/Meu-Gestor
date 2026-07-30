@php
    $maintenanceId = $maintenance->id ?? 'new';
@endphp

{{-- ============================================================
    CLIENTE E EQUIPAMENTO
============================================================= --}}

<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-pc-display-horizontal"></i>
        Identificação do equipamento
    </h6>

    <div class="row g-3">

        <div class="col-md-6">

            <label for="service_client_id_{{ $maintenanceId }}" class="form-label">
                Cliente
                <span class="service-required">*</span>
            </label>

            <select name="service_client_id" id="service_client_id_{{ $maintenanceId }}" class="form-select" required>

                <option value="">
                    Selecione um cliente
                </option>

                @foreach (\App\Models\ServiceClient::where('status', 'active')->orderBy('name')->get() as $client)
                    <option value="{{ $client->id }}" @selected(old('service_client_id', $maintenance->service_client_id ?? '') == $client->id)>

                        {{ $client->name }}

                    </option>
                @endforeach

            </select>

            <div class="invalid-feedback">
                Selecione um cliente.
            </div>

        </div>

        <div class="col-md-6">

            <label for="assistance_{{ $maintenanceId }}" class="form-label">
                Assistência técnica
                <span class="service-required">*</span>
            </label>

            <input type="text" name="assistance" id="assistance_{{ $maintenanceId }}" class="form-control"
                value="{{ old('assistance', $maintenance->assistance ?? '') }}"
                placeholder="Nome da assistência técnica" maxlength="150" required>

            <div class="invalid-feedback">
                Informe a assistência técnica.
            </div>

        </div>

        <div class="col-md-6">

            <label for="equipment_{{ $maintenanceId }}" class="form-label">
                Equipamento
                <span class="service-required">*</span>
            </label>

            <input type="text" name="equipment" id="equipment_{{ $maintenanceId }}" class="form-control"
                value="{{ old('equipment', $maintenance->equipment ?? '') }}"
                placeholder="Ex.: roteador, switch, fonte..." maxlength="150" required>

            <div class="invalid-feedback">
                Informe o equipamento.
            </div>

        </div>

        <div class="col-md-6">

            <label for="erro_{{ $maintenanceId }}" class="form-label">
                Defeito apresentado
                <span class="service-required">*</span>
            </label>

            <input type="text" name="erro" id="erro_{{ $maintenanceId }}" class="form-control"
                value="{{ old('erro', $maintenance->erro ?? '') }}" placeholder="Descreva o erro ou defeito"
                maxlength="255" required>

            <div class="invalid-feedback">
                Informe o defeito apresentado.
            </div>

        </div>

    </div>

</div>

{{-- ============================================================
    DATAS
============================================================= --}}

<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-calendar3"></i>
        Datas da manutenção
    </h6>

    <div class="row g-3">

        <div class="col-md-4">

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

        <div class="col-md-4">

            <label for="date_send_{{ $maintenanceId }}" class="form-label">
                Data de envio
            </label>

            <input type="date" name="date_send" id="date_send_{{ $maintenanceId }}" class="form-control"
                value="{{ old(
                    'date_send',
                    isset($maintenance) && $maintenance->date_send ? $maintenance->date_send->format('Y-m-d') : '',
                ) }}">

            <div class="service-form-help">
                Data em que o equipamento foi enviado para a assistência.
            </div>

        </div>

        <div class="col-md-4">

            <label for="date_received_{{ $maintenanceId }}" class="form-label">
                Data de recebimento
            </label>

            <input type="date" name="date_received" id="date_received_{{ $maintenanceId }}" class="form-control"
                value="{{ old(
                    'date_received',
                    isset($maintenance) && $maintenance->date_received ? $maintenance->date_received->format('Y-m-d') : '',
                ) }}">

            <div class="service-form-help">
                Preencha quando o equipamento retornar da assistência.
            </div>

        </div>

    </div>

</div>

{{-- ============================================================
    SOLUÇÃO
============================================================= --}}

<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-wrench-adjustable-circle"></i>
        Solução aplicada
    </h6>

    <div class="row g-3">

        <div class="col-12">

            <label for="solution_{{ $maintenanceId }}" class="form-label">
                Solução
            </label>

            <textarea name="solution" id="solution_{{ $maintenanceId }}" class="form-control" rows="4" maxlength="2000"
                placeholder="Descreva o reparo, peças substituídas e testes realizados">{{ old('solution', $maintenance->solution ?? '') }}</textarea>

            <div class="service-form-help">
                Este campo pode ser preenchido ou atualizado após o retorno do equipamento.
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
        Custos da manutenção
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
                    value="{{ old('cost_enterprise', $maintenance->cost_enterprise ?? '0.00') }}" min="0"
                    step="0.01" inputmode="decimal">

            </div>

            <div class="service-form-help">
                Valor pago pela empresa à assistência técnica.
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
                    value="{{ old('cost_client', $maintenance->cost_client ?? '0.00') }}" min="0"
                    step="0.01" inputmode="decimal">

            </div>

            <div class="service-form-help">
                Valor que será cobrado do cliente.
            </div>

        </div>

    </div>

</div>
