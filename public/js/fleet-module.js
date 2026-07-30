/**
 * ================================================================
 * MÓDULO DE FROTAS
 * JavaScript compartilhado por:
 * - Veículos
 * - Manutenções
 * - Serviços
 * - Oficinas
 * ================================================================
 */

(function () {
    'use strict';

    /**
     * Retorna os dados de quilometragem declarados pela página.
     *
     * A view poderá declarar:
     *
     * window.fleetMaxMileages = {
     *     1: 45000,
     *     2: 78000
     * };
     */
    function getMaxMileages() {
        if (
            typeof window.fleetMaxMileages === 'object'
            && window.fleetMaxMileages !== null
        ) {
            return window.fleetMaxMileages;
        }

        /*
         * Compatibilidade temporária com o nome antigo.
         */
        if (
            typeof window.maxMileages === 'object'
            && window.maxMileages !== null
        ) {
            return window.maxMileages;
        }

        return {};
    }

    /**
     * Normaliza textos utilizados em filtros.
     */
    function normalizeValue(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    /**
     * Inicializa Bootstrap Tooltip.
     */
    function initializeTooltips() {
        if (typeof bootstrap === 'undefined') {
            return;
        }

        document
            .querySelectorAll('[data-bs-toggle="tooltip"]')
            .forEach(function (element) {
                bootstrap.Tooltip.getOrCreateInstance(element);
            });
    }

    /**
     * Validação Bootstrap e spinner de envio.
     *
     * Para ativar em um formulário:
     *
     * class="needs-validation fleet-submit-form"
     *
     * No botão:
     *
     * data-fleet-submit
     *
     * Nos elementos internos:
     *
     * data-submit-spinner
     * data-submit-icon
     * data-submit-text
     */
    function initializeFormSubmissions() {
        document
            .querySelectorAll(
                '.fleet-submit-form, form[data-fleet-form]'
            )
            .forEach(function (form) {
                if (form.dataset.fleetInitialized === 'true') {
                    return;
                }

                form.dataset.fleetInitialized = 'true';

                form.addEventListener('submit', function (event) {
                    if (
                        form.classList.contains('needs-validation')
                        && !form.checkValidity()
                    ) {
                        event.preventDefault();
                        event.stopPropagation();

                        form.classList.add('was-validated');

                        const invalidField =
                            form.querySelector(':invalid');

                        invalidField?.focus();

                        return;
                    }

                    const submitButton =
                        form.querySelector(
                            '[data-fleet-submit], button[type="submit"]'
                        );

                    if (!submitButton) {
                        return;
                    }

                    /*
                     * Não altera botão de formulário cuja submissão
                     * já tenha sido cancelada por outro listener.
                     */
                    window.setTimeout(function () {
                        if (event.defaultPrevented) {
                            return;
                        }

                        setButtonLoading(submitButton, true);
                    }, 0);
                });
            });
    }

    /**
     * Controla o estado visual do botão.
     */
    function setButtonLoading(button, loading) {
        if (!button) {
            return;
        }

        const spinner =
            button.querySelector('[data-submit-spinner]');

        const icon =
            button.querySelector('[data-submit-icon]');

        const text =
            button.querySelector('[data-submit-text]');

        if (loading) {
            button.disabled = true;

            spinner?.classList.remove('d-none');
            icon?.classList.add('d-none');

            if (text) {
                text.dataset.originalText =
                    text.dataset.originalText
                    || text.textContent.trim();

                text.textContent =
                    button.dataset.loadingText || 'Salvando...';
            }

            return;
        }

        button.disabled = false;

        spinner?.classList.add('d-none');
        icon?.classList.remove('d-none');

        if (text?.dataset.originalText) {
            text.textContent = text.dataset.originalText;
        }
    }

    /**
     * Restaura formulários quando o modal é fechado.
     */
    function initializeModalReset() {
        document
            .querySelectorAll('.modal')
            .forEach(function (modal) {
                if (modal.dataset.fleetResetInitialized === 'true') {
                    return;
                }

                modal.dataset.fleetResetInitialized = 'true';

                modal.addEventListener(
                    'hidden.bs.modal',
                    function () {
                        modal
                            .querySelectorAll('form')
                            .forEach(function (form) {
                                form.classList.remove('was-validated');

                                const submitButton =
                                    form.querySelector(
                                        '[data-fleet-submit]'
                                    );

                                setButtonLoading(
                                    submitButton,
                                    false
                                );
                            });
                    }
                );
            });
    }

    /**
     * Confirma exclusões.
     *
     * Utilização:
     *
     * <form
     *     data-confirm-delete
     *     data-confirm-message="Excluir este veículo?">
     */
    function initializeDeleteConfirmation() {
        document
            .querySelectorAll(
                '[data-confirm-delete], .fleet-delete-form'
            )
            .forEach(function (form) {
                if (form.dataset.confirmInitialized === 'true') {
                    return;
                }

                form.dataset.confirmInitialized = 'true';

                form.addEventListener('submit', function (event) {
                    const message =
                        form.dataset.confirmMessage
                        || 'Tem certeza que deseja excluir este registro?';

                    if (!window.confirm(message)) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });
            });
    }

    /**
     * Abre e fecha filtros.
     *
     * Botão:
     *
     * data-fleet-filter-toggle="#fleetFilter"
     *
     * Área:
     *
     * id="fleetFilter"
     */
    function initializeFilterToggles() {
        document
            .querySelectorAll('[data-fleet-filter-toggle]')
            .forEach(function (button) {
                if (button.dataset.filterInitialized === 'true') {
                    return;
                }

                button.dataset.filterInitialized = 'true';

                const targetSelector =
                    button.dataset.fleetFilterToggle;

                const target =
                    document.querySelector(targetSelector);

                if (!target) {
                    return;
                }

                const icon =
                    button.querySelector('.fleet-filter-icon');

                button.addEventListener('click', function () {
                    const isHidden = target.hasAttribute('hidden');

                    if (isHidden) {
                        target.removeAttribute('hidden');
                        target.classList.add('fleet-fade-in');
                    } else {
                        target.setAttribute('hidden', '');
                        target.classList.remove('fleet-fade-in');
                    }

                    icon?.classList.toggle(
                        'is-open',
                        isHidden
                    );

                    button.setAttribute(
                        'aria-expanded',
                        isHidden ? 'true' : 'false'
                    );
                });
            });

        /*
         * Compatibilidade temporária com o botão antigo.
         */
        const legacyButton =
            document.getElementById('toggle-filter');

        const legacyFilter =
            document.getElementById('filter-form');

        if (
            legacyButton
            && legacyFilter
            && legacyButton.dataset.filterInitialized !== 'true'
        ) {
            legacyButton.dataset.filterInitialized = 'true';

            legacyButton.addEventListener('click', function () {
                const currentlyHidden =
                    window.getComputedStyle(legacyFilter).display
                    === 'none';

                legacyFilter.style.display =
                    currentlyHidden ? 'block' : 'none';
            });
        }
    }

    /**
     * Pesquisa local em tabelas.
     *
     * Campo:
     *
     * data-fleet-table-search="#vehiclesTable"
     *
     * Tabela:
     *
     * id="vehiclesTable"
     */
    function initializeTableSearch() {
        document
            .querySelectorAll('[data-fleet-table-search]')
            .forEach(function (input) {
                if (input.dataset.searchInitialized === 'true') {
                    return;
                }

                input.dataset.searchInitialized = 'true';

                const table =
                    document.querySelector(
                        input.dataset.fleetTableSearch
                    );

                if (!table) {
                    return;
                }

                const rows =
                    table.querySelectorAll('tbody tr');

                input.addEventListener('input', function () {
                    const searchValue =
                        normalizeValue(input.value);

                    rows.forEach(function (row) {
                        const rowValue =
                            normalizeValue(row.textContent);

                        row.hidden =
                            searchValue !== ''
                            && !rowValue.includes(searchValue);
                    });
                });
            });
    }

    /**
     * Coloca placas em letras maiúsculas.
     */
    function initializeLicensePlates() {
        document
            .querySelectorAll(
                'input[name="license_plate"], [data-fleet-license-plate]'
            )
            .forEach(function (input) {
                if (input.dataset.plateInitialized === 'true') {
                    return;
                }

                input.dataset.plateInitialized = 'true';

                input.addEventListener('input', function () {
                    const cursorPosition =
                        input.selectionStart;

                    input.value = input.value
                        .toUpperCase()
                        .replace(/\s+/g, '');

                    if (cursorPosition !== null) {
                        input.setSelectionRange(
                            cursorPosition,
                            cursorPosition
                        );
                    }
                });

                input.value = input.value.toUpperCase();
            });
    }

    /**
     * Atualiza a visualização do seletor de cor.
     */
    function initializeColorPreview() {
        document
            .querySelectorAll(
                'input[type="color"][name="color"]'
            )
            .forEach(function (input) {
                if (input.dataset.colorInitialized === 'true') {
                    return;
                }

                input.dataset.colorInitialized = 'true';

                const targetSelector =
                    input.dataset.colorPreview;

                const preview = targetSelector
                    ? document.querySelector(targetSelector)
                    : input
                        .closest('.fleet-color-field')
                        ?.querySelector('.fleet-color-preview');

                function updatePreview() {
                    if (preview) {
                        preview.style.backgroundColor =
                            input.value;
                    }
                }

                input.addEventListener(
                    'input',
                    updatePreview
                );

                updatePreview();
            });
    }

    /**
     * Inicia o Flatpickr nos campos de data.
     */
    function initializeDatePickers() {
        if (typeof flatpickr === 'undefined') {
            return;
        }

        document
            .querySelectorAll('.datepicker')
            .forEach(function (input) {
                if (
                    input._flatpickr
                    || input.dataset.datepickerInitialized === 'true'
                ) {
                    return;
                }

                input.dataset.datepickerInitialized = 'true';

                flatpickr(input, {
                    dateFormat: 'd/m/Y',
                    allowInput: true,
                    disableMobile: false,
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            });
    }

    /**
     * Retorna todas as opções originais de um select.
     */
    function saveOriginalOptions(select) {
        if (!select || select._fleetOriginalOptions) {
            return;
        }

        select._fleetOriginalOptions =
            Array.from(select.options).map(function (option) {
                return option.cloneNode(true);
            });
    }

    /**
     * Filtra serviços compatíveis com o tipo de veículo.
     */
    function filterServices(form, vehicleType) {
        const serviceItems =
            form.querySelectorAll(
                '.service-checkbox[data-service-type]'
            );

        serviceItems.forEach(function (item) {
            const serviceType =
                normalizeValue(item.dataset.serviceType);

            const isCompatible =
                serviceType === vehicleType
                || serviceType === 'all';

            item.style.display =
                isCompatible ? '' : 'none';

            if (!isCompatible) {
                const checkbox =
                    item.querySelector(
                        'input[type="checkbox"]'
                    );

                if (checkbox) {
                    checkbox.checked = false;
                }
            }
        });
    }

    /**
     * Filtra oficinas compatíveis com o tipo de veículo.
     */
    function filterWorkshops(form, vehicleType) {
        const workshopSelect =
            form.querySelector('.workshop-select');

        if (!workshopSelect) {
            return;
        }

        saveOriginalOptions(workshopSelect);

        const previouslySelected =
            workshopSelect.value;

        workshopSelect.innerHTML = '';

        const placeholder =
            document.createElement('option');

        placeholder.value = '';
        placeholder.textContent = 'Selecione uma oficina';

        workshopSelect.appendChild(placeholder);

        workshopSelect
            ._fleetOriginalOptions
            .forEach(function (originalOption) {
                const workshopType =
                    normalizeValue(
                        originalOption.dataset.workshopType
                    );

                if (
                    !originalOption.value
                    || workshopType === vehicleType
                    || workshopType === 'all'
                ) {
                    const clonedOption =
                        originalOption.cloneNode(true);

                    workshopSelect.appendChild(clonedOption);
                }
            });

        const previousOption =
            Array.from(workshopSelect.options)
                .find(function (option) {
                    return option.value === previouslySelected;
                });

        if (previousOption) {
            workshopSelect.value = previouslySelected;
        }
    }

    /**
     * Atualiza a dica da quilometragem.
     */
    function updateMileageHint(form, vehicleId) {
        const mileageInput =
            form.querySelector('input[name="mileage"]');

        if (!mileageInput) {
            return;
        }

        const mileageLabel =
            form.querySelector(
                '[data-mileage-label], #mileage-label'
            );

        const maxMileages =
            getMaxMileages();

        const currentMileage =
            maxMileages[vehicleId];

        if (
            currentMileage !== undefined
            && currentMileage !== null
            && currentMileage !== ''
        ) {
            const formattedMileage =
                Number(currentMileage).toLocaleString(
                    'pt-BR'
                );

            mileageInput.placeholder =
                `Última registrada: ${formattedMileage} km`;

            if (mileageLabel) {
                mileageLabel.textContent =
                    `Quilometragem — última: ${formattedMileage} km`;
            }

            mileageInput.min =
                Number(currentMileage);

            return;
        }

        mileageInput.placeholder = '';

        if (mileageLabel) {
            mileageLabel.textContent = 'Quilometragem';
        }

        mileageInput.removeAttribute('min');
    }

    /**
     * Atualiza formulário de manutenção.
     */
    function updateMaintenanceForm(form) {
        const vehicleSelect =
            form.querySelector('.vehicle-select');

        if (!vehicleSelect) {
            return;
        }

        const selectedOption =
            vehicleSelect.options[
                vehicleSelect.selectedIndex
            ];

        const vehicleType =
            normalizeValue(
                selectedOption?.dataset.vehicleType
            );

        if (!vehicleType) {
            return;
        }

        filterServices(form, vehicleType);
        filterWorkshops(form, vehicleType);
        updateMileageHint(form, vehicleSelect.value);
    }

    /**
     * Inicializa os formulários de manutenção.
     */
    function initializeMaintenanceForms() {
        document
            .querySelectorAll('form')
            .forEach(function (form) {
                const vehicleSelect =
                    form.querySelector('.vehicle-select');

                if (
                    !vehicleSelect
                    || vehicleSelect.dataset.fleetMaintenanceInitialized
                        === 'true'
                ) {
                    return;
                }

                vehicleSelect.dataset
                    .fleetMaintenanceInitialized = 'true';

                vehicleSelect.addEventListener(
                    'change',
                    function () {
                        updateMaintenanceForm(form);
                    }
                );

                const modal =
                    form.closest('.modal');

                modal?.addEventListener(
                    'shown.bs.modal',
                    function () {
                        updateMaintenanceForm(form);
                    }
                );

                updateMaintenanceForm(form);
            });
    }

    /**
     * Reabre automaticamente modal com erro de validação.
     *
     * Na view:
     *
     * <div
     *     class="modal"
     *     data-open-on-error="{{ $errors->any() ? 'true' : 'false' }}">
     */
    function initializeErrorModals() {
        if (typeof bootstrap === 'undefined') {
            return;
        }

        const modal =
            document.querySelector(
                '.modal[data-open-on-error="true"]'
            );

        if (!modal) {
            return;
        }

        bootstrap.Modal
            .getOrCreateInstance(modal)
            .show();
    }

    /**
     * Seleciona automaticamente o primeiro campo ao abrir um modal.
     */
    function initializeModalAutofocus() {
        document
            .querySelectorAll('.modal')
            .forEach(function (modal) {
                if (
                    modal.dataset.autofocusInitialized
                    === 'true'
                ) {
                    return;
                }

                modal.dataset.autofocusInitialized = 'true';

                modal.addEventListener(
                    'shown.bs.modal',
                    function () {
                        const field =
                            modal.querySelector(
                                '[data-autofocus], input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])'
                            );

                        field?.focus();
                    }
                );
            });
    }

    /**
     * Ordenação simples de tabelas.
     *
     * No cabeçalho:
     *
     * <th data-fleet-sort="text">Modelo</th>
     * <th data-fleet-sort="number">Ano</th>
     */
    function initializeTableSorting() {
        document
            .querySelectorAll(
                'table[data-fleet-sortable]'
            )
            .forEach(function (table) {
                const headers =
                    table.querySelectorAll(
                        'thead th[data-fleet-sort]'
                    );

                headers.forEach(function (header, index) {
                    header.style.cursor = 'pointer';
                    header.setAttribute(
                        'title',
                        'Clique para ordenar'
                    );

                    header.addEventListener(
                        'click',
                        function () {
                            const tbody =
                                table.querySelector('tbody');

                            if (!tbody) {
                                return;
                            }

                            const rows =
                                Array.from(
                                    tbody.querySelectorAll(
                                        ':scope > tr'
                                    )
                                );

                            const sortType =
                                header.dataset.fleetSort;

                            const direction =
                                header.dataset.sortDirection
                                === 'asc'
                                    ? 'desc'
                                    : 'asc';

                            headers.forEach(function (item) {
                                delete item.dataset.sortDirection;
                            });

                            header.dataset.sortDirection =
                                direction;

                            rows.sort(function (rowA, rowB) {
                                const valueA =
                                    getCellValue(
                                        rowA.cells[index],
                                        sortType
                                    );

                                const valueB =
                                    getCellValue(
                                        rowB.cells[index],
                                        sortType
                                    );

                                let result;

                                if (sortType === 'number') {
                                    result =
                                        Number(valueA)
                                        - Number(valueB);
                                } else {
                                    result =
                                        String(valueA)
                                            .localeCompare(
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
                                    : -result;
                            });

                            rows.forEach(function (row) {
                                tbody.appendChild(row);
                            });
                        }
                    );
                });
            });
    }

    /**
     * Extrai o valor da célula.
     */
    function getCellValue(cell, type) {
        if (!cell) {
            return '';
        }

        const explicitValue =
            cell.dataset.sortValue;

        const value =
            explicitValue !== undefined
                ? explicitValue
                : cell.textContent.trim();

        if (type === 'number') {
            return String(value)
                .replace(/[^\d,.-]/g, '')
                .replace(/\./g, '')
                .replace(',', '.');
        }

        return normalizeValue(value);
    }

    /**
     * Executa todos os componentes do módulo.
     */
    function initializeFleetModule() {
        initializeTooltips();
        initializeFormSubmissions();
        initializeModalReset();
        initializeDeleteConfirmation();
        initializeFilterToggles();
        initializeTableSearch();
        initializeLicensePlates();
        initializeColorPreview();
        initializeDatePickers();
        initializeMaintenanceForms();
        initializeErrorModals();
        initializeModalAutofocus();
        initializeTableSorting();
    }

    document.addEventListener(
        'DOMContentLoaded',
        initializeFleetModule
    );

    /*
     * Disponibiliza atualização manual, útil para conteúdo carregado
     * futuramente por AJAX ou Livewire.
     */
    window.FleetModule = {
        initialize: initializeFleetModule,
        updateMaintenanceForm: updateMaintenanceForm,
        setButtonLoading: setButtonLoading
    };
})();