document.addEventListener('DOMContentLoaded', function () {

    const config = window.towerIndexConfig || {};

    const table = document.getElementById('towersTable');
    const searchInput = document.getElementById('towerSearch');
    const perPageSelect = document.getElementById('towerPerPage');

    const searchEmptyRow =
        document.getElementById('towerSearchEmpty');

    /*
    |--------------------------------------------------------------------------
    | PESQUISA LOCAL
    |--------------------------------------------------------------------------
    */

    if (table && searchInput) {

        const rows = Array.from(
            table.querySelectorAll('tbody .tower-row')
        );

        searchInput.addEventListener('input', function () {

            const searchValue = normalizeText(this.value);

            let visibleRows = 0;

            rows.forEach(function (row) {

                const rowValue = normalizeText(
                    row.dataset.search || row.innerText
                );

                const isVisible =
                    !searchValue || rowValue.includes(searchValue);

                row.classList.toggle('d-none', !isVisible);

                if (isVisible) {
                    visibleRows++;
                }
            });

            if (searchEmptyRow) {
                searchEmptyRow.classList.toggle(
                    'd-none',
                    visibleRows !== 0
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTROS POR PÁGINA
    |--------------------------------------------------------------------------
    */

    if (perPageSelect) {

        perPageSelect.addEventListener('change', function () {

            const url = new URL(window.location.href);

            url.searchParams.set(
                config.perPageParameter || 'perPage',
                this.value
            );

            /*
            | Ao alterar o tamanho, retorna para a primeira página.
            */
            url.searchParams.delete('page');

            window.location.href = url.toString();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ORDENAÇÃO LOCAL
    |--------------------------------------------------------------------------
    */

    if (table) {

        const headers = table.querySelectorAll(
            'thead th[data-sortable="true"]'
        );

        headers.forEach(function (header) {

            header.addEventListener('click', function () {

                const columnIndex =
                    Array.from(header.parentElement.children)
                        .indexOf(header);

                const currentDirection =
                    header.dataset.sort || 'desc';

                const newDirection =
                    currentDirection === 'asc'
                        ? 'desc'
                        : 'asc';

                headers.forEach(function (item) {
                    delete item.dataset.sort;
                });

                header.dataset.sort = newDirection;

                sortTable(
                    table,
                    columnIndex,
                    newDirection
                );
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DO FORMULÁRIO DE CADASTRO
    |--------------------------------------------------------------------------
    */

    const createForm =
        document.getElementById('towerCreateForm');

    const submitButton =
        document.getElementById('towerSubmitButton');

    const submitSpinner =
        document.getElementById('towerSubmitSpinner');

    const submitIcon =
        document.getElementById('towerSubmitIcon');

    const submitText =
        document.getElementById('towerSubmitText');

    if (createForm) {

        createForm.addEventListener('submit', function (event) {

            if (!createForm.checkValidity()) {

                event.preventDefault();
                event.stopPropagation();

                createForm.classList.add('was-validated');

                return;
            }

            if (submitButton) {
                submitButton.disabled = true;
            }

            if (submitSpinner) {
                submitSpinner.classList.remove('d-none');
            }

            if (submitIcon) {
                submitIcon.classList.add('d-none');
            }

            if (submitText) {
                submitText.textContent = 'Salvando...';
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REABRIR MODAL QUANDO HOUVER ERRO DE VALIDAÇÃO
    |--------------------------------------------------------------------------
    */

    if (
        config.hasValidationErrors &&
        typeof bootstrap !== 'undefined'
    ) {

        const modalElement =
            document.getElementById('addTower');

        if (modalElement) {

            bootstrap.Modal
                .getOrCreateInstance(modalElement)
                .show();
        }
    }

});

/*
|--------------------------------------------------------------------------
| ORDENAÇÃO
|--------------------------------------------------------------------------
*/

function sortTable(table, columnIndex, direction) {

    const tbody = table.querySelector('tbody');

    if (!tbody) {
        return;
    }

    const rows = Array.from(
        tbody.querySelectorAll('.tower-row')
    );

    rows.sort(function (rowA, rowB) {

        const valueA = getCellValue(
            rowA.cells[columnIndex]
        );

        const valueB = getCellValue(
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

function getCellValue(cell) {

    if (!cell) {
        return '';
    }

    const rawValue =
        cell.dataset.value ?? cell.innerText ?? '';

    return parseSortableValue(rawValue);
}

function parseSortableValue(value) {

    const text = String(value ?? '').trim();

    /*
    | Data no formato YYYY-MM-DD.
    */
    if (/^\d{4}-\d{2}-\d{2}$/.test(text)) {

        const timestamp = Date.parse(text);

        return Number.isNaN(timestamp)
            ? 0
            : timestamp;
    }

    /*
    | Remove símbolos e unidades, preservando números.
    */
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

    return normalizeText(text);
}

function normalizeText(value) {

    return String(value ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}