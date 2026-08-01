/**
 * Admin Module
 * Arquivo: public/js/admin-module.js
 */
document.addEventListener('DOMContentLoaded', () => {
    initAdminConfirmations();
    initAdminSubmitLock();
    initRolePermissionControl();
    initAdminPasswordToggles();
    initSystemLogViewer();
});

function initAdminConfirmations() {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            const message = element.dataset.confirm || 'Deseja continuar?';
            if (!window.confirm(message)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        });
    });
}

function initAdminSubmitLock() {
    document.querySelectorAll('form[data-submit-lock]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) return;
            const button = event.submitter || form.querySelector('button[type="submit"],input[type="submit"]');
            if (!button || button.disabled) return;
            button.disabled = true;
            if (button.tagName === 'BUTTON') {
                button.dataset.originalHtml = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            }
        });
    });
}

function initRolePermissionControl() {
    const roleCheckboxes = Array.from(document.querySelectorAll('.role-checkbox'));
    const permissionCheckboxes = Array.from(document.querySelectorAll('.permission-checkbox'));
    if (!roleCheckboxes.length || !permissionCheckboxes.length) return;

    const update = () => {
        const selectedRoles = roleCheckboxes.filter((checkbox) => checkbox.checked);
        const inherited = new Set();
        selectedRoles.forEach((checkbox) => {
            try {
                JSON.parse(checkbox.dataset.permissions || '[]').forEach((permission) => inherited.add(permission));
            } catch (error) {
                console.warn('Não foi possível ler as permissões do papel.', error);
            }
        });

        permissionCheckboxes.forEach((checkbox) => {
            const container = checkbox.closest('.form-check');
            checkbox.disabled = selectedRoles.length > 0;
            container?.classList.toggle('text-muted', selectedRoles.length > 0);
            container?.classList.toggle('fw-bold', inherited.has(checkbox.value));
        });
    };

    roleCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', update));
    update();
}

function initAdminPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const selector = button.dataset.passwordToggle;
            const input = selector ? document.querySelector(selector) : null;
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            button.setAttribute('aria-pressed', input.type === 'text' ? 'true' : 'false');
        });
    });
}

function initSystemLogViewer() {
    const viewer = document.querySelector('[data-system-log-viewer]');
    if (viewer) viewer.scrollTop = 0;
}
