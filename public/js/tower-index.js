/**
 * ===========================================================
 * Tower Index
 * Arquivo: public/js/tower-index.js
 * ===========================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    const config = window.towerIndexConfig || {};

    const table = document.getElementById('towersTable');
    const searchInput = document.getElementById('towerSearch');
    const perPageSelect = document.getElementById('towerPerPage');
    const searchEmptyRow = document.getElementById('towerSearchEmpty');

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
                    searchValue === '' ||
                    rowValue.includes(searchValue);

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

            url.searchParams.delete('page');

            window.location.href = url.toString();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ORDENAÇÃO LOCAL
    |--------------------------------------------------------------------------
    |
    | Se existem 55 registros e o paginate está em 100,
    | as 55 linhas estão carregadas e serão ordenadas juntas.
    |--------------------------------------------------------------------------
    */

    if (table) {
        initializeTableSorting(table);
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
| INICIALIZAÇÃO DA ORDENAÇÃO
|--------------------------------------------------------------------------
*/

function initializeTableSorting(table) {
    const headers = Array.from(
        table.querySelectorAll(
            'thead th[data-sortable="true"]'
        )
    );

    if (headers.length === 0) {
        return;
    }

    headers.forEach(function (header) {
        header.setAttribute('role', 'button');
        header.setAttribute('tabindex', '0');
        header.setAttribute('aria-sort', 'none');

        const executeSort = function () {
            const columnIndex =
                Array.from(header.parentElement.children)
                    .indexOf(header);

            const currentDirection =
                header.dataset.sortDirection || '';

            const newDirection =
                currentDirection === 'asc'
                    ? 'desc'
                    : 'asc';

            headers.forEach(function (item) {
                delete item.dataset.sortDirection;

                item.setAttribute(
                    'aria-sort',
                    'none'
                );

                resetSortIcon(item);
            });

            header.dataset.sortDirection =
                newDirection;

            header.setAttribute(
                'aria-sort',
                newDirection === 'asc'
                    ? 'ascending'
                    : 'descending'
            );

            updateSortIcon(
                header,
                newDirection
            );

            const sortType = getColumnSortType(
                header,
                columnIndex
            );

            sortTable(
                table,
                columnIndex,
                newDirection,
                sortType
            );
        };

        header.addEventListener(
            'click',
            executeSort
        );

        header.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key !== 'Enter' &&
                    event.key !== ' '
                ) {
                    return;
                }

                event.preventDefault();
                executeSort();
            }
        );
    });
}

/*
|--------------------------------------------------------------------------
| TIPOS DAS COLUNAS
|--------------------------------------------------------------------------
*/

function getColumnSortType(
    header,
    columnIndex
) {
    const declaredType =
        header.dataset.sortType;

    if (
        declaredType === 'text' ||
        declaredType === 'number' ||
        declaredType === 'date'
    ) {
        return declaredType;
    }

    const columnTypes = {
        0: 'text',
        1: 'number',
        2: 'number',
        3: 'number',
        4: 'number',
        5: 'text',
        6: 'number',
        7: 'date',
        8: 'number',
        9: 'number',
        10: 'number'
    };

    return columnTypes[columnIndex] || 'text';
}

/*
|--------------------------------------------------------------------------
| ORDENAÇÃO DA TABELA
|--------------------------------------------------------------------------
*/

function sortTable(
    table,
    columnIndex,
    direction,
    sortType
) {
    const tbody = table.querySelector('tbody');

    if (!tbody) {
        return;
    }

    const rows = Array.from(
        tbody.querySelectorAll('.tower-row')
    );

    const rowsWithOriginalIndex =
        rows.map(function (row, index) {
            return {
                row: row,
                originalIndex: index,
                value: getCellSortValue(
                    row.cells[columnIndex],
                    sortType
                )
            };
        });

    rowsWithOriginalIndex.sort(
        function (itemA, itemB) {
            const comparison = compareValues(
                itemA.value,
                itemB.value,
                sortType
            );

            if (comparison !== 0) {
                return direction === 'asc'
                    ? comparison
                    : comparison * -1;
            }

            return (
                itemA.originalIndex -
                itemB.originalIndex
            );
        }
    );

    const fragment =
        document.createDocumentFragment();

    rowsWithOriginalIndex.forEach(
        function (item) {
            fragment.appendChild(item.row);
        }
    );

    const specialRows = Array.from(
        tbody.querySelectorAll(
            'tr:not(.tower-row)'
        )
    );

    tbody.appendChild(fragment);

    specialRows.forEach(function (row) {
        tbody.appendChild(row);
    });
}

/*
|--------------------------------------------------------------------------
| VALOR DA CÉLULA
|--------------------------------------------------------------------------
*/

function getCellSortValue(
    cell,
    sortType
) {
    if (!cell) {
        return getEmptySortValue(sortType);
    }

    const rawValue =
        cell.dataset.value ??
        cell.textContent ??
        '';

    if (sortType === 'number') {
        return parseNumberValue(rawValue);
    }

    if (sortType === 'date') {
        return parseDateValue(rawValue);
    }

    return normalizeText(rawValue);
}

function getEmptySortValue(sortType) {
    if (
        sortType === 'number' ||
        sortType === 'date'
    ) {
        return Number.NEGATIVE_INFINITY;
    }

    return '';
}

/*
|--------------------------------------------------------------------------
| COMPARAÇÃO
|--------------------------------------------------------------------------
*/

function compareValues(
    valueA,
    valueB,
    sortType
) {
    if (
        sortType === 'number' ||
        sortType === 'date'
    ) {
        const numberA =
            Number.isFinite(valueA)
                ? valueA
                : Number.NEGATIVE_INFINITY;

        const numberB =
            Number.isFinite(valueB)
                ? valueB
                : Number.NEGATIVE_INFINITY;

        return numberA - numberB;
    }

    return String(valueA).localeCompare(
        String(valueB),
        'pt-BR',
        {
            sensitivity: 'base',
            numeric: true,
            ignorePunctuation: true
        }
    );
}

/*
|--------------------------------------------------------------------------
| CONVERSÃO NUMÉRICA
|--------------------------------------------------------------------------
*/

function parseNumberValue(value) {
    if (
        value === null ||
        value === undefined
    ) {
        return Number.NEGATIVE_INFINITY;
    }

    let text = String(value).trim();

    if (
        text === '' ||
        text === '—' ||
        text === '-'
    ) {
        return Number.NEGATIVE_INFINITY;
    }

    text = text
        .replace(/\s+/g, '')
        .replace(/[^\d,.\-+]/g, '');

    if (text === '') {
        return Number.NEGATIVE_INFINITY;
    }

    if (
        text.includes(',') &&
        text.includes('.')
    ) {
        text = text
            .replace(/\./g, '')
            .replace(',', '.');
    } else if (text.includes(',')) {
        text = text.replace(',', '.');
    } else if (
        /^\d{1,3}(\.\d{3})+$/.test(text)
    ) {
        text = text.replace(/\./g, '');
    }

    const parsedValue = Number(text);

    return Number.isFinite(parsedValue)
        ? parsedValue
        : Number.NEGATIVE_INFINITY;
}

/*
|--------------------------------------------------------------------------
| CONVERSÃO DE DATA
|--------------------------------------------------------------------------
*/

function parseDateValue(value) {
    if (
        value === null ||
        value === undefined
    ) {
        return Number.NEGATIVE_INFINITY;
    }

    const text =
        String(value).trim();

    if (
        text === '' ||
        text === '—' ||
        text === '-'
    ) {
        return Number.NEGATIVE_INFINITY;
    }

    const isoMatch = text.match(
        /^(\d{4})-(\d{2})-(\d{2})/
    );

    if (isoMatch) {
        return new Date(
            Number(isoMatch[1]),
            Number(isoMatch[2]) - 1,
            Number(isoMatch[3])
        ).getTime();
    }

    const brazilianMatch = text.match(
        /^(\d{2})\/(\d{2})\/(\d{4})/
    );

    if (brazilianMatch) {
        return new Date(
            Number(brazilianMatch[3]),
            Number(brazilianMatch[2]) - 1,
            Number(brazilianMatch[1])
        ).getTime();
    }

    const timestamp = Date.parse(text);

    return Number.isNaN(timestamp)
        ? Number.NEGATIVE_INFINITY
        : timestamp;
}

/*
|--------------------------------------------------------------------------
| ÍCONES DA ORDENAÇÃO
|--------------------------------------------------------------------------
*/

function resetSortIcon(header) {
    const icon =
        header.querySelector('.sort-icon');

    if (!icon) {
        return;
    }

    icon.classList.remove(
        'bi-arrow-up',
        'bi-arrow-down'
    );

    icon.classList.add(
        'bi-arrow-down-up'
    );
}

function updateSortIcon(
    header,
    direction
) {
    const icon =
        header.querySelector('.sort-icon');

    if (!icon) {
        return;
    }

    icon.classList.remove(
        'bi-arrow-down-up',
        'bi-arrow-up',
        'bi-arrow-down'
    );

    icon.classList.add(
        direction === 'asc'
            ? 'bi-arrow-up'
            : 'bi-arrow-down'
    );
}

/*
|--------------------------------------------------------------------------
| NORMALIZAÇÃO DE TEXTO
|--------------------------------------------------------------------------
*/

function normalizeText(value) {
    return String(value ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .toLowerCase()
        .trim();
}
