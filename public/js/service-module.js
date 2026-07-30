/**
 * ================================================================
 * SERVICE MODULE
 * Recursos compartilhados do módulo de serviços
 * ================================================================
 */

(function () {
    'use strict';

    /**
     * Normaliza texto para pesquisa e ordenação.
     */
    function normalizeText(value) {
        return String(value ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .toLowerCase();
    }

    /**
     * Converte valores monetários brasileiros para número.
     */
    function parseNumber(value) {
        const normalized = String(value ?? '')
            .replace(/\s/g, '')
            .replace(/R\$/gi, '')
            .replace(/\./g, '')
            .replace(',', '.')
            .replace(/[^\d.-]/g, '');

        const number = Number.parseFloat(normalized);

        return Number.isNaN(number) ? 0 : number;
    }

    /**
     * Converte datas para timestamp.
     *
     * Suporta:
     * - YYYY-MM-DD
     * - DD/MM/YYYY
     */
    function parseDate(value) {
        const text = String(value ?? '').trim();

        if (!text) {
            return 0;
        }

        if (/^\d{4}-\d{2}-\d{2}/.test(text)) {
            const timestamp = Date.parse(text);

            return Number.isNaN(timestamp) ? 0 : timestamp;
        }

        const brazilianDate = text.match(
            /^(\d{2})\/(\d{2})\/(\d{4})$/
        );

        if (brazilianDate) {
            const [, day, month, year] = brazilianDate;

            return new Date(
                Number(year),
                Number(month) - 1,
                Number(day)
            ).getTime();
        }

        const timestamp = Date.parse(text);

        return Number.isNaN(timestamp) ? 0 : timestamp;
    }

    /**
     * Inicializa a validação dos formulários Bootstrap.
     */
    function initializeValidation(root = document) {
        const forms = root.querySelectorAll(
            '.needs-validation:not([data-service-validation-ready])'
        );

        forms.forEach((form) => {
            form.dataset.serviceValidationReady = 'true';

            form.addEventListener('submit', function (event) {
                const submitter = event.submitter;

                /*
                 * Não valida o formulário de edição quando o botão pertence
                 * a outro formulário, como o botão de exclusão.
                 */
                if (
                    submitter &&
                    submitter.getAttribute('form') &&
                    submitter.getAttribute('form') !== form.id
                ) {
                    return;
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    const firstInvalid = form.querySelector(':invalid');

                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }

                form.classList.add('was-validated');
            });
        });
    }

    /**
     * Ativa estado de carregamento no botão submit.
     */
    function initializeSubmitLoading(root = document) {
        const forms = root.querySelectorAll(
            '[data-service-form]:not([data-service-submit-ready]), ' +
            '.service-submit-form:not([data-service-submit-ready])'
        );

        forms.forEach((form) => {
            form.dataset.serviceSubmitReady = 'true';

            form.addEventListener('submit', function (event) {
                if (event.defaultPrevented || !form.checkValidity()) {
                    return;
                }

                const button =
                    event.submitter ||
                    form.querySelector('[data-service-submit]');

                if (!button || button.form !== form) {
                    return;
                }

                button.disabled = true;
                button.setAttribute('aria-busy', 'true');

                const spinner = button.querySelector(
                    '[data-submit-spinner]'
                );

                const icon = button.querySelector(
                    '[data-submit-icon]'
                );

                const text = button.querySelector(
                    '[data-submit-text]'
                );

                if (spinner) {
                    spinner.classList.remove('d-none');
                }

                if (icon) {
                    icon.classList.add('d-none');
                }

                if (text) {
                    button.dataset.originalText = text.textContent.trim();

                    text.textContent =
                        button.dataset.loadingText || 'Salvando...';
                }
            });
        });
    }

    /**
     * Confirma exclusão dos registros.
     */
    function initializeDeleteConfirmation(root = document) {
        const forms = root.querySelectorAll(
            '[data-confirm-delete]:not([data-service-delete-ready]), ' +
            '.service-delete-form:not([data-service-delete-ready])'
        );

        forms.forEach((form) => {
            form.dataset.serviceDeleteReady = 'true';

            form.addEventListener('submit', function (event) {
                const message =
                    form.dataset.confirmMessage ||
                    'Tem certeza que deseja excluir este registro?';

                if (!window.confirm(message)) {
                    event.preventDefault();
                    return;
                }

                const submitButtons = document.querySelectorAll(
                    `[form="${CSS.escape(form.id)}"]`
                );

                submitButtons.forEach((button) => {
                    button.disabled = true;
                });
            });
        });
    }

    /**
     * Pesquisa nas tabelas.
     *
     * Exemplo:
     * data-service-table-search="#serviceClientsTable"
     */
    function initializeTableSearch(root = document) {
        const inputs = root.querySelectorAll(
            '[data-service-table-search]:not([data-service-search-ready])'
        );

        inputs.forEach((input) => {
            input.dataset.serviceSearchReady = 'true';

            const tableSelector = input.dataset.serviceTableSearch;
            const table = document.querySelector(tableSelector);

            if (!table) {
                return;
            }

            const tbody = table.querySelector('tbody');

            if (!tbody) {
                return;
            }

            const originalRows = Array.from(
                tbody.querySelectorAll('tr:not(.service-search-empty-row)')
            );

            const emptyRow = document.createElement('tr');
            const columnCount =
                table.querySelectorAll('thead th').length || 1;

            emptyRow.className =
                'service-search-empty-row service-row-hidden';

            emptyRow.innerHTML = `
                <td colspan="${columnCount}">
                    <i class="bi bi-search me-1"></i>
                    Nenhum resultado encontrado.
                </td>
            `;

            tbody.appendChild(emptyRow);

            function filterRows() {
                const search = normalizeText(input.value);
                let visibleRows = 0;

                originalRows.forEach((row) => {
                    const content = normalizeText(
                        row.dataset.searchValue || row.textContent
                    );

                    const visible =
                        search === '' || content.includes(search);

                    row.classList.toggle(
                        'service-row-hidden',
                        !visible
                    );

                    if (visible) {
                        visibleRows++;
                    }
                });

                emptyRow.classList.toggle(
                    'service-row-hidden',
                    visibleRows !== 0
                );
            }

            input.addEventListener('input', filterRows);

            filterRows();
        });
    }

    /**
     * Ordenação das tabelas.
     *
     * Tabela:
     * data-service-sortable
     *
     * Coluna:
     * data-service-sort="text|number|date"
     */
    function initializeSortableTables(root = document) {
        const tables = root.querySelectorAll(
            '[data-service-sortable]:not([data-service-sort-ready])'
        );

        tables.forEach((table) => {
            table.dataset.serviceSortReady = 'true';

            const tbody = table.querySelector('tbody');

            if (!tbody) {
                return;
            }

            const headers = table.querySelectorAll(
                'thead th[data-service-sort]'
            );

            headers.forEach((header, columnIndex) => {
                header.setAttribute('role', 'button');
                header.setAttribute('tabindex', '0');

                function sortColumn() {
                    const type =
                        header.dataset.serviceSort || 'text';

                    const currentDirection =
                        header.dataset.sortDirection;

                    const direction =
                        currentDirection === 'asc' ? 'desc' : 'asc';

                    headers.forEach((item) => {
                        delete item.dataset.sortDirection;
                    });

                    header.dataset.sortDirection = direction;

                    const rows = Array.from(
                        tbody.querySelectorAll(
                            'tr:not(.service-search-empty-row)'
                        )
                    );

                    rows.sort((rowA, rowB) => {
                        const cellA = rowA.children[columnIndex];
                        const cellB = rowB.children[columnIndex];

                        if (!cellA || !cellB) {
                            return 0;
                        }

                        const rawA =
                            cellA.dataset.sortValue ||
                            cellA.textContent;

                        const rawB =
                            cellB.dataset.sortValue ||
                            cellB.textContent;

                        let valueA;
                        let valueB;

                        if (type === 'number') {
                            valueA = parseNumber(rawA);
                            valueB = parseNumber(rawB);
                        } else if (type === 'date') {
                            valueA = parseDate(rawA);
                            valueB = parseDate(rawB);
                        } else {
                            valueA = normalizeText(rawA);
                            valueB = normalizeText(rawB);
                        }

                        let result;

                        if (
                            typeof valueA === 'number' &&
                            typeof valueB === 'number'
                        ) {
                            result = valueA - valueB;
                        } else {
                            result = String(valueA).localeCompare(
                                String(valueB),
                                'pt-BR',
                                {
                                    numeric: true,
                                    sensitivity: 'base'
                                }
                            );
                        }

                        return direction === 'asc'
                            ? result
                            : result * -1;
                    });

                    rows.forEach((row) => {
                        tbody.appendChild(row);
                    });
                }

                header.addEventListener('click', sortColumn);

                header.addEventListener('keydown', function (event) {
                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {
                        event.preventDefault();
                        sortColumn();
                    }
                });
            });
        });
    }

    /**
     * Expande e recolhe textos longos.
     *
     * Estrutura:
     *
     * <div data-service-expandable>
     *     <span data-text-short>...</span>
     *     <span data-text-full class="d-none">...</span>
     *     <button data-text-toggle>Mostrar mais</button>
     * </div>
     */
    function initializeExpandableTexts(root = document) {
        const containers = root.querySelectorAll(
            '[data-service-expandable]:not([data-service-expand-ready])'
        );

        containers.forEach((container) => {
            container.dataset.serviceExpandReady = 'true';

            const shortText = container.querySelector(
                '[data-text-short]'
            );

            const fullText = container.querySelector(
                '[data-text-full]'
            );

            const button = container.querySelector(
                '[data-text-toggle]'
            );

            if (!shortText || !fullText || !button) {
                return;
            }

            button.addEventListener('click', function () {
                const expanded =
                    !fullText.classList.contains('d-none');

                shortText.classList.toggle('d-none', !expanded);
                fullText.classList.toggle('d-none', expanded);

                button.textContent = expanded
                    ? button.dataset.moreText || 'Mostrar mais'
                    : button.dataset.lessText || 'Mostrar menos';

                button.setAttribute(
                    'aria-expanded',
                    expanded ? 'false' : 'true'
                );
            });
        });
    }

    /**
     * Inicializa os modais.
     */
    function initializeModals(root = document) {
        const modals = root.querySelectorAll(
            '.modal:not([data-service-modal-ready])'
        );

        modals.forEach((modal) => {
            modal.dataset.serviceModalReady = 'true';

            modal.addEventListener('shown.bs.modal', function () {
                const autofocus = modal.querySelector(
                    '[data-autofocus]'
                );

                if (autofocus) {
                    window.setTimeout(() => {
                        autofocus.focus();
                    }, 100);
                }
            });

            modal.addEventListener('hidden.bs.modal', function () {
                const forms = modal.querySelectorAll(
                    '[data-reset-on-close]'
                );

                forms.forEach((form) => {
                    form.reset();
                    form.classList.remove('was-validated');

                    form.querySelectorAll('.is-invalid').forEach(
                        (field) => {
                            field.classList.remove('is-invalid');
                        }
                    );
                });
            });
        });
    }

    /**
     * Abre automaticamente o modal marcado quando há erro.
     *
     * Exemplo:
     * data-open-on-error="true"
     */
    function openModalWithErrors() {
        const modalElement = document.querySelector(
            '.modal[data-open-on-error="true"]'
        );

        if (
            !modalElement ||
            typeof window.bootstrap === 'undefined'
        ) {
            return;
        }

        const modal = window.bootstrap.Modal.getOrCreateInstance(
            modalElement
        );

        modal.show();
    }

    /**
     * Fecha alertas automaticamente.
     *
     * Exemplo:
     * data-service-auto-dismiss="5000"
     */
    function initializeAutoDismissAlerts(root = document) {
        const alerts = root.querySelectorAll(
            '[data-service-auto-dismiss]:not([data-service-dismiss-ready])'
        );

        alerts.forEach((alert) => {
            alert.dataset.serviceDismissReady = 'true';

            const delay = Number(
                alert.dataset.serviceAutoDismiss || 5000
            );

            window.setTimeout(() => {
                alert.style.transition =
                    'opacity 0.25s ease, transform 0.25s ease';

                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-5px)';

                window.setTimeout(() => {
                    alert.remove();
                }, 260);
            }, delay);
        });
    }

    /**
     * Inicializa todos os recursos.
     */
    function initialize(root = document) {
        initializeValidation(root);
        initializeSubmitLoading(root);
        initializeDeleteConfirmation(root);
        initializeTableSearch(root);
        initializeSortableTables(root);
        initializeExpandableTexts(root);
        initializeModals(root);
        initializeAutoDismissAlerts(root);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initialize();
        openModalWithErrors();
    });

    /**
     * API pública para páginas que adicionam conteúdo dinamicamente.
     */
    window.ServiceModule = {
        initialize,
        normalizeText,
        parseNumber,
        parseDate,
        initializeValidation,
        initializeSubmitLoading,
        initializeDeleteConfirmation,
        initializeTableSearch,
        initializeSortableTables,
        initializeExpandableTexts,
        initializeModals
    };
})();