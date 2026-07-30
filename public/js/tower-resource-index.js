document.addEventListener('DOMContentLoaded', function () {

    initializeResourceSearch();
    initializeResourceSorting();
    initializeResourceFilters();
    initializeResourceEditButtons();
    initializeResourceForms();
    initializeResourceDeleteButtons();
    initializeAutomaticFilters();

});

/*
|--------------------------------------------------------------------------
| PESQUISA LOCAL
|--------------------------------------------------------------------------
*/

function initializeResourceSearch() {

    document
        .querySelectorAll('[data-resource-search]')
        .forEach(function (input) {

            const tableSelector =
                input.dataset.resourceSearch;

            const table =
                document.querySelector(tableSelector);

            if (!table) {
                return;
            }

            const rows = Array.from(
                table.querySelectorAll(
                    'tbody [data-resource-row]'
                )
            );

            const emptyRow =
                table.querySelector('[data-search-empty]');

            input.addEventListener('input', function () {

                const search =
                    normalizeResourceText(input.value);

                let visible = 0;

                rows.forEach(function (row) {

                    const content =
                        normalizeResourceText(
                            row.dataset.search ||
                            row.innerText
                        );

                    const show =
                        !search || content.includes(search);

                    row.classList.toggle('d-none', !show);

                    if (show) {
                        visible++;
                    }
                });

                if (emptyRow) {
                    emptyRow.classList.toggle(
                        'd-none',
                        visible !== 0
                    );
                }
            });
        });
}

/*
|--------------------------------------------------------------------------
| ORDENAÇÃO
|--------------------------------------------------------------------------
*/

function initializeResourceSorting() {

    document
        .querySelectorAll('.resource-table')
        .forEach(function (table) {

            const headers =
                table.querySelectorAll(
                    'thead th[data-sortable="true"]'
                );

            headers.forEach(function (header) {

                header.addEventListener('click', function () {

                    const row =
                        header.parentElement;

                    const columnIndex =
                        Array.from(row.children)
                            .indexOf(header);

                    const current =
                        header.dataset.sort || 'desc';

                    const direction =
                        current === 'asc'
                            ? 'desc'
                            : 'asc';

                    headers.forEach(function (item) {
                        delete item.dataset.sort;
                    });

                    header.dataset.sort = direction;

                    sortResourceTable(
                        table,
                        columnIndex,
                        direction
                    );
                });
            });
        });
}

function sortResourceTable(
    table,
    columnIndex,
    direction
) {

    const tbody =
        table.querySelector('tbody');

    if (!tbody) {
        return;
    }

    const rows = Array.from(
        tbody.querySelectorAll(
            '[data-resource-row]'
        )
    );

    rows.sort(function (rowA, rowB) {

        const valueA =
            getResourceCellValue(
                rowA.cells[columnIndex]
            );

        const valueB =
            getResourceCellValue(
                rowB.cells[columnIndex]
            );

        if (valueA < valueB) {
            return direction === 'asc' ? -1 : 1;
        }

        if (valueA > valueB) {
            return direction === 'asc' ? 1 : -1;
        }

        return 0;
    });

    rows.forEach(function (row) {
        tbody.appendChild(row);
    });
}

function getResourceCellValue(cell) {

    if (!cell) {
        return '';
    }

    const value =
        cell.dataset.value ??
        cell.innerText ??
        '';

    return parseResourceSortableValue(value);
}

function parseResourceSortableValue(value) {

    const text =
        String(value ?? '').trim();

    if (/^\d{4}-\d{2}-\d{2}$/.test(text)) {

        const timestamp =
            Date.parse(text);

        return Number.isNaN(timestamp)
            ? 0
            : timestamp;
    }

    const normalizedNumber = text
        .replace(/\s/g, '')
        .replace(/\./g, '')
        .replace(',', '.')
        .replace(/[^\d.-]/g, '');

    if (
        normalizedNumber !== '' &&
        !Number.isNaN(Number(normalizedNumber))
    ) {
        return Number(normalizedNumber);
    }

    return normalizeResourceText(text);
}

/*
|--------------------------------------------------------------------------
| EXIBIR E OCULTAR FILTROS
|--------------------------------------------------------------------------
*/

function initializeResourceFilters() {

    document
        .querySelectorAll('[data-filter-toggle]')
        .forEach(function (button) {

            const targetSelector =
                button.dataset.filterToggle;

            const target =
                document.querySelector(targetSelector);

            if (!target) {
                return;
            }

            button.addEventListener('click', function () {

                const visible =
                    target.classList.toggle('is-visible');

                button.setAttribute(
                    'aria-expanded',
                    visible ? 'true' : 'false'
                );

                const label =
                    button.querySelector(
                        '[data-filter-label]'
                    );

                if (label) {
                    label.textContent =
                        visible
                            ? 'Ocultar filtros'
                            : 'Filtros';
                }
            });
        });
}

/*
|--------------------------------------------------------------------------
| FILTROS COM ENVIO AUTOMÁTICO
|--------------------------------------------------------------------------
*/

function initializeAutomaticFilters() {

    document
        .querySelectorAll('[data-auto-submit]')
        .forEach(function (field) {

            field.addEventListener('change', function () {

                if (field.form) {
                    field.form.submit();
                }
            });
        });
}

/*
|--------------------------------------------------------------------------
| MODAIS GENÉRICOS DE EDIÇÃO
|--------------------------------------------------------------------------
|
| O botão precisa ter:
|
| data-resource-edit="#editModal"
| data-update-url="/recurso/1"
| data-record='{"name":"Nome","watts":"100"}'
|
| Os campos precisam ter:
|
| data-edit-field="name"
|--------------------------------------------------------------------------
*/

function initializeResourceEditButtons() {

    document
        .querySelectorAll('[data-resource-edit]')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const modalSelector =
                    button.dataset.resourceEdit;

                const modal =
                    document.querySelector(modalSelector);

                if (!modal) {
                    console.error(
                        'Modal de edição não encontrado:',
                        modalSelector
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | PREENCHE OS CAMPOS
                |--------------------------------------------------------------------------
                |
                | Exemplo:
                |
                | data-name="Bateria Moura"
                | data-mark="Moura"
                |
                | Campo:
                |
                | data-edit-field="name"
                |
                */

                modal
                    .querySelectorAll('[data-edit-field]')
                    .forEach(function (field) {

                        const property =
                            field.dataset.editField;

                        /*
                        | data-edit-field="name"
                        | procura button.dataset.name
                        */
                        const value =
                            button.dataset[property] ?? '';

                        if (
                            field.type === 'checkbox'
                        ) {
                            field.checked =
                                value === '1' ||
                                value === 'true';

                            return;
                        }

                        if (
                            field.type === 'radio'
                        ) {
                            field.checked =
                                field.value === value;

                            return;
                        }

                        field.value = value;

                        /*
                        | Dispara o evento change caso algum select
                        | tenha outro JavaScript associado.
                        */
                        field.dispatchEvent(
                            new Event('change', {
                                bubbles: true
                            })
                        );
                    });

                /*
                |--------------------------------------------------------------------------
                | DEFINE A ROTA DE UPDATE
                |--------------------------------------------------------------------------
                */

                const form =
                    modal.querySelector('[data-edit-form]');

                if (
                    form &&
                    button.dataset.updateUrl
                ) {
                    form.action =
                        button.dataset.updateUrl;
                }

                /*
                |--------------------------------------------------------------------------
                | REMOVE VALIDAÇÃO ANTIGA
                |--------------------------------------------------------------------------
                */

                if (form) {
                    form.classList.remove('was-validated');
                }
            });
        });
}

/*
|--------------------------------------------------------------------------
| VALIDAÇÃO E BLOQUEIO DE ENVIO DUPLO
|--------------------------------------------------------------------------
*/

function initializeResourceForms() {

    document
        .querySelectorAll('.js-resource-form')
        .forEach(function (form) {

            form.addEventListener('submit', function (event) {

                if (!form.checkValidity()) {

                    event.preventDefault();
                    event.stopPropagation();

                    form.classList.add('was-validated');

                    return;
                }

                const submitButton =
                    form.querySelector(
                        '[data-submit-button]'
                    );

                if (!submitButton) {
                    return;
                }

                submitButton.disabled = true;

                const spinner =
                    submitButton.querySelector(
                        '[data-submit-spinner]'
                    );

                const icon =
                    submitButton.querySelector(
                        '[data-submit-icon]'
                    );

                const text =
                    submitButton.querySelector(
                        '[data-submit-text]'
                    );

                if (spinner) {
                    spinner.classList.remove('d-none');
                }

                if (icon) {
                    icon.classList.add('d-none');
                }

                if (text) {
                    text.textContent =
                        submitButton.dataset.loadingText ||
                        'Salvando...';
                }
            });
        });
}

/*
|--------------------------------------------------------------------------
| EXCLUSÃO GENÉRICA
|--------------------------------------------------------------------------
*/

function initializeResourceDeleteButtons() {

    document
        .querySelectorAll('[data-resource-delete]')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const url =
                    button.dataset.resourceDelete;

                const title =
                    button.dataset.deleteTitle ||
                    'Deseja excluir este registro?';

                const description =
                    button.dataset.deleteDescription ||
                    'Essa alteração não poderá ser desfeita.';

                if (
                    typeof openConfirmModal === 'function'
                ) {
                    openConfirmModal(
                        url,
                        title,
                        description,
                        'DELETE'
                    );

                    return;
                }

                if (!window.confirm(title)) {
                    return;
                }

                createResourceDeleteForm(url);
            });
        });
}

function createResourceDeleteForm(url) {

    const csrfToken =
        document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;

    if (!csrfToken) {
        return;
    }

    const form =
        document.createElement('form');

    form.method = 'POST';
    form.action = url;
    form.hidden = true;

    form.innerHTML = `
        <input
            type="hidden"
            name="_token"
            value="${escapeResourceHtml(csrfToken)}">

        <input
            type="hidden"
            name="_method"
            value="DELETE">
    `;

    document.body.appendChild(form);

    form.submit();
}

/*
|--------------------------------------------------------------------------
| FUNÇÕES AUXILIARES
|--------------------------------------------------------------------------
*/

function normalizeResourceText(value) {

    return String(value ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}

function escapeResourceHtml(value) {

    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}