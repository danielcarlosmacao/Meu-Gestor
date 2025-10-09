@php
    $selectedRoles = isset($user) ? $user->roles->pluck('name')->toArray() : [];
    $selectedPermissions = isset($user) ? $user->permissions->pluck('name')->toArray() : [];
@endphp

<div class="mb-3">
    <label for="roles" class="form-label">Papéis</label><br>
    @foreach($roles as $role)
        <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input role-checkbox"
                   name="roles[]" value="{{ $role->name }}"
                   data-permissions='@json($rolePermissions[$role->name] ?? [])'
                   {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}>
            <label class="form-check-label">{{ $role->name }}</label>
        </div>
    @endforeach
</div>

<div class="mb-4">
    <label class="form-label fw-bold">Permissões Individuais</label>
    <div class="row">
        @php
            $grupos = [
                'Gestor de Torres' => fn($p) => str_contains($p->name, 'towers'),
                'Gestor de Frota' => fn($p) => str_contains($p->name, 'fleets'),
                'Gestor de Serviços' => fn($p) => str_contains($p->name, 'service'),
                'Gestor de Férias' => fn($p) => str_contains($p->name, 'vacations') || str_contains($p->name, 'vacation_manager') || str_contains($p->name, 'collaborators'),
                'Gestor Extras' => fn($p) => str_contains($p->name, 'extra') || str_contains($p->name, 'recipients') || str_contains($p->name, 'notification') ||  str_contains($p->name, 'api') ||  str_contains($p->name, 'stock'),
                'Administrador' => fn($p) => str_contains($p->name, 'user') || str_contains($p->name, 'admin'),
            ];
        @endphp

        @foreach($grupos as $titulo => $filtro)
            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 shadow-sm h-100">
                    <h6 class="fw-bold">{{ $titulo }}</h6>
                    @foreach ($permissions->filter($filtro) as $permission)
                        <div class="form-check">
                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                   value="{{ $permission->name }}"
                                   {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    function togglePermissions() {
        const selectedRoles = Array.from(roleCheckboxes).filter(cb => cb.checked);
        const hasRoleSelected = selectedRoles.length > 0;

        // Coleta permissões de todos os roles selecionados
        let rolePermissions = [];
        selectedRoles.forEach(cb => {
            try {
                const perms = JSON.parse(cb.dataset.permissions);
                rolePermissions = rolePermissions.concat(perms);
            } catch (e) {
                console.warn("Erro ao ler permissões do role", e);
            }
        });

        permissionCheckboxes.forEach(cb => {
            cb.disabled = hasRoleSelected;

            // Remove marcações visuais antigas
            cb.parentElement.classList.remove('text-muted', 'bg-light', 'fw-bold');

            if (hasRoleSelected) {
                cb.parentElement.classList.add('text-muted');

                // Se essa permissão pertence ao role selecionado → destaque
                if (rolePermissions.includes(cb.value)) {
                    cb.parentElement.classList.add('bg-light', 'fw-bold');
                }
            }
        });
    }

    // Executa ao carregar a página
    togglePermissions();

    // Monitora mudanças nos roles
    roleCheckboxes.forEach(cb => {
        cb.addEventListener('change', togglePermissions);
    });
});
</script>
@endpush
