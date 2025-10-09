<div class="mb-3">
    <label for="name" class="form-label">Nome</label>
    <input
        type="text"
        id="name"
        name="name"
        class="form-control"
        value="{{ old('name', $client->name ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select
        id="status"
        name="status"
        class="form-control"
        required
    >
        <option value="">Selecione</option>
        <option value="active" {{ (old('status', $client->status ?? '') == 'active') ? 'selected' : '' }}>Ativo</option>
        <option value="inactive" {{ (old('status', $client->status ?? '') == 'inactive') ? 'selected' : '' }}>Inativo</option>
    </select>
</div>
