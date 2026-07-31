document.addEventListener('DOMContentLoaded', () => {
    initStockSubmitLock();
    initStockConfirmations();
    initStockItemStatus();
    initStockMovementForm();
    initStockReportDates();
});

function initStockSubmitLock() {
    document.querySelectorAll('form[data-submit-lock]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) return;
            const button = event.submitter || form.querySelector('button[type="submit"],input[type="submit"]');
            if (!button || button.disabled) return;
            button.disabled = true;
            if (button.tagName === 'BUTTON') {
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Salvando...';
            } else {
                button.value = 'Salvando...';
            }
        });
    });
}

function initStockConfirmations() {
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

function initStockItemStatus() {
    const form = document.querySelector('[data-stock-item-form]');
    if (!form) return;
    const status = form.querySelector('[name="status"]');
    if (!status) return;
    const fields = form.querySelectorAll('[data-disable-when-inactive]');
    const sync = () => fields.forEach((field) => field.disabled = !status.checked);
    status.addEventListener('change', sync);
    sync();
}

function initStockMovementForm() {
    const form = document.querySelector('[data-stock-movement-form]');
    if (!form) return;

    const typeSelect = form.querySelector('#type');
    const container = form.querySelector('#items-container');
    const template = document.getElementById('stock-item-row-template');
    const addButton = form.querySelector('[data-add-stock-item]');
    if (!typeSelect || !container) return;

    let itemIndex = Number.parseInt(container.dataset.nextIndex || '1', 10);

    const syncRows = () => {
        const type = typeSelect.value;
        container.querySelectorAll('.stock-item-row').forEach((row) => {
            const movementColumn = row.querySelector('.movement-column');
            const movementField = row.querySelector('.movement-field');
            const priceColumn = row.querySelector('.price-column');
            const priceField = row.querySelector('.price-field');

            const isMovement = type === 'movement';
            const isInput = type === 'input';

            if (movementColumn) movementColumn.hidden = !isMovement;
            if (movementField) movementField.disabled = !isMovement;
            if (priceColumn) priceColumn.hidden = !isInput;
            if (priceField) {
                priceField.disabled = !isInput;
                if (!isInput) priceField.value = '';
            }
        });
    };

    const addRow = () => {
        if (!template) return;
        const html = template.innerHTML.replaceAll('__INDEX__', String(itemIndex));
        container.insertAdjacentHTML('beforeend', html);
        itemIndex += 1;
        syncRows();
    };

    addButton?.addEventListener('click', addRow);
    typeSelect.addEventListener('change', syncRows);

    container.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-stock-item]');
        if (!removeButton) return;
        const rows = container.querySelectorAll('.stock-item-row');
        if (rows.length <= 1) {
            window.alert('É necessário manter pelo menos um item.');
            return;
        }
        removeButton.closest('.stock-item-row')?.remove();
    });

    syncRows();
}

function initStockReportDates() {
    const form = document.querySelector('[data-stock-report-form]');
    if (!form) return;
    const start = form.querySelector('[name="start_date"]');
    const end = form.querySelector('[name="end_date"]');
    if (!start || !end) return;
    const validate = () => {
        end.setCustomValidity('');
        if (start.value && end.value && end.value < start.value) {
            end.setCustomValidity('A data final não pode ser anterior à data inicial.');
        }
    };
    start.addEventListener('change', validate);
    end.addEventListener('change', validate);
}
