<div class="service-form-section">

    <h6 class="service-form-section-title">
        <i class="bi bi-person"></i>
        Dados do Cliente
    </h6>

    <div class="row g-3">

        <div class="col-md-8">

            <label for="name" class="form-label">
                Nome
                <span class="service-required">*</span>
            </label>

            <input type="text" id="name" name="name" class="form-control"
                value="{{ old('name', $client->name ?? '') }}" placeholder="Digite o nome do cliente" maxlength="150"
                required>

            <div class="service-form-help">
                Nome utilizado para identificar o cliente nas manutenções.
            </div>

        </div>

        <div class="col-md-4">

            <label for="status" class="form-label">
                Status
                <span class="service-required">*</span>
            </label>

            <select id="status" name="status" class="form-select" required>
                <option value="">Selecione</option>

                <option value="active" @selected(old('status', $client->status ?? '') == 'active')>
                    Ativo
                </option>

                <option value="inactive" @selected(old('status', $client->status ?? '') == 'inactive')>
                    Inativo
                </option>

            </select>

            <div class="service-form-help">
                Define se o cliente estará disponível para novos registros.
            </div>

        </div>

    </div>

</div>
