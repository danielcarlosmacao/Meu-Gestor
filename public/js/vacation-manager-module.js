/**
 * ===========================================================
 * Vacation Manager Module
 * Arquivo: public/js/vacation-manager-module.js
 * ===========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initCourseEditModal();
    initDeleteConfirmations();
    initFileNamePreview();
    initModalAutoFocus();
    initSubmitLock();
    initVacationForms();
    initWorkScheduleToggle(); // <-- ADICIONADO AQUI
});

/**
 * ===========================================================
 * Controle de Escalas de Trabalho (Alternar Folga / Trabalho)
 * ===========================================================
 */
function initWorkScheduleToggle() {


    // Seletor de mês/ano
    const picker = document.getElementById('monthYearPicker');
    if (picker && !picker.dataset.listenerAttached) {
        picker.dataset.listenerAttached = 'true';
        picker.addEventListener('change', function () {
            const value = this.value;
            if (!value) return;
            const [yearFull, month] = value.split('-');
            const yearShort = yearFull.slice(-2);
            window.location.href = `/vacation_manager/work_schedules/month/${month}-${yearShort}`;
        });
    }

    // Delegação de evento global para as células da grade
    if (!document.body.dataset.scheduleListener) {
        document.body.dataset.scheduleListener = 'true';

        document.addEventListener('click', function (event) {
            // SE O USUÁRIO NÃO TIVER PERMISSÃO, NÃO FAZ NADA AO CLICAR
            if (window.canEditSchedule === false) {
                return;
            }

            const cell = event.target.closest('.schedule-day-cell[data-collaborator]');
            if (!cell) return;

            const collaboratorId = cell.dataset.collaborator;
            const date = cell.dataset.date;
            const badge = cell.querySelector('.schedule-badge');

            if (!badge) return;

            // Identifica o tipo atual baseado nas classes do badge
            let currentType = 'work';
            if (badge.classList.contains('badge-day_off')) currentType = 'day_off';
            else if (badge.classList.contains('badge-medical_leave')) currentType = 'medical_leave';
            else if (badge.classList.contains('badge-vacation')) currentType = 'vacation';
            else if (badge.classList.contains('badge-holiday')) currentType = 'holiday';

            // SE FOR LICENÇA, FÉRIAS OU FERIADO, NÃO FAZ NADA AO CLICAR
            if (['medical_leave', 'vacation', 'holiday'].includes(currentType)) {
                return;
            }

            if (cell.dataset.loading === 'true') return;
            cell.dataset.loading = 'true';

            // Alterna apenas entre 'work' e 'day_off'
            const targetType = currentType === 'day_off' ? 'work' : 'day_off';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("/vacation_manager/work_schedules/toggle", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    collaborator_id: collaboratorId,
                    date: date,
                    type: targetType
                })
            })
                .then(response => response.json())
                .then(data => {
                    cell.dataset.loading = 'false';
                    if (data.success) {
                        badge.className = `badge badge-${data.type} w-100 py-1 schedule-badge`;
                        badge.textContent = data.label;
                        badge.title = data.type;
                    }
                })
                .catch(error => {
                    cell.dataset.loading = 'false';
                    console.error('Erro ao atualizar escala:', error);
                });
        });
    }


    // Botão Atualizar jornada (Abre o Modal com segurança)
    const btnUpdate = document.getElementById('btnUpdateSchedule');
    if (btnUpdate && !btnUpdate.dataset.listenerAttached) {
        btnUpdate.dataset.listenerAttached = 'true';
        btnUpdate.addEventListener('click', function (event) {
            event.preventDefault(); // <-- EVITA QUE DISPARUE OUTRO FORMULÁRIO NA PÁGINA

            const modalElement = document.getElementById('updateScheduleModal');
            if (modalElement && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                modal.show();
            }
        });
    }
}
/**
 * ===========================================================
 * Modal de edição dos cursos
 * ===========================================================
 */
function initCourseEditModal() {
    const modal = document.getElementById('editCourseModal');

    if (!modal) {
        return;
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const token = button.dataset.token || '';
        const title = button.dataset.title || '';
        const collaborator = button.dataset.collaborator || '';
        const validity = button.dataset.validity || '';
        const updateUrl = button.dataset.updateUrl || '';

        setFieldValue('edit_title', title);
        setFieldValue('edit_collaborator', collaborator);
        setFieldValue('edit_validity', validity);

        const form = document.getElementById('editCourseForm');

        if (!form) {
            return;
        }

        if (updateUrl !== '') {
            form.action = updateUrl;
            return;
        }

        if (token !== '') {
            form.action =
                '/vacation_manager/collaborators/courses/' +
                encodeURIComponent(token);
        }
    });
}

/**
 * ===========================================================
 * Confirma exclusão
 * ===========================================================
 */
function initDeleteConfirmations() {
    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', function (event) {
            const message =
                this.dataset.confirm ||
                'Deseja realmente continuar?';

            if (!window.confirm(message)) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
            }
        });
    });
}

/**
 * ===========================================================
 * Exibe o nome do arquivo selecionado
 * ===========================================================
 */
function initFileNamePreview() {
    document
        .querySelectorAll('input[type="file"][data-file-preview]')
        .forEach((input) => {
            input.addEventListener('change', function () {
                const selector = this.dataset.filePreview;

                if (!selector) {
                    return;
                }

                const preview = document.querySelector(selector);

                if (!preview) {
                    return;
                }

                if (this.files && this.files.length > 0) {
                    preview.innerHTML =
                        '<i class="bi bi-file-earmark-pdf me-1"></i>' +
                        escapeHtml(this.files[0].name);
                } else {
                    preview.textContent =
                        'Nenhum arquivo selecionado';
                }
            });
        });
}

/**
 * ===========================================================
 * Coloca o foco no primeiro campo disponível do modal
 * ===========================================================
 */
function initModalAutoFocus() {
    document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('shown.bs.modal', () => {
            const field = modal.querySelector(
                [
                    'input:not([type="hidden"]):not([disabled])',
                    'select:not([disabled])',
                    'textarea:not([disabled])'
                ].join(', ')
            );

            if (field) {
                field.focus();
            }
        });
    });
}

/**
 * ===========================================================
 * Impede envio duplo do formulário
 * ===========================================================
 */
function initSubmitLock() {
    document
        .querySelectorAll('form[data-submit-lock]')
        .forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    return;
                }

                const submitter = event.submitter;

                const button =
                    submitter ||
                    form.querySelector(
                        'button[type="submit"], input[type="submit"]'
                    );

                if (!button || button.disabled) {
                    return;
                }

                button.disabled = true;

                if (button.tagName === 'BUTTON') {
                    button.dataset.originalHtml = button.innerHTML;

                    button.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" ' +
                        'role="status" aria-hidden="true"></span>' +
                        'Salvando...';
                } else {
                    button.dataset.originalValue = button.value;
                    button.value = 'Salvando...';
                }
            });
        });
}

/**
 * ===========================================================
 * Formulários de férias
 * ===========================================================
 */
function initVacationForms() {
    const toastElement = document.getElementById(
        'invalidVacationDateToast'
    );

    const toastMessage = document.getElementById(
        'invalidVacationDateToastMessage'
    );

    let vacationToast = null;

    if (
        toastElement &&
        typeof bootstrap !== 'undefined' &&
        bootstrap.Toast
    ) {
        vacationToast = bootstrap.Toast.getOrCreateInstance(
            toastElement,
            {
                delay: 4000
            }
        );
    }

    document
        .querySelectorAll('form.vacation-form')
        .forEach((form) => {
            initializeVacationForm(
                form,
                vacationToast,
                toastMessage
            );
        });
}

function initializeVacationForm(
    form,
    vacationToast,
    toastMessage
) {
    const daysInput = form.querySelector(
        'input[name="temp_vacations"]'
    );

    const startDateInput = form.querySelector(
        'input[name="start_date"]'
    );

    const endDateInput = form.querySelector(
        'input[name="end_date"]'
    );

    if (
        !daysInput ||
        !startDateInput ||
        !endDateInput
    ) {
        return;
    }

    let lastWarnedDate = null;

    function calculateEndDate() {
        const numberOfDays = Number.parseInt(
            daysInput.value,
            10
        );

        const startDate = parseLocalDate(
            startDateInput.value
        );

        if (
            !startDate ||
            Number.isNaN(numberOfDays) ||
            numberOfDays < 1
        ) {
            return;
        }

        const endDate = new Date(startDate);

        endDate.setDate(
            endDate.getDate() + numberOfDays - 1
        );

        endDateInput.value =
            formatLocalDate(endDate);

        endDateInput.setCustomValidity('');
    }

    function calculateVacationDays() {
        const startDate = parseLocalDate(
            startDateInput.value
        );

        const endDate = parseLocalDate(
            endDateInput.value
        );

        if (!startDate || !endDate) {
            return;
        }

        if (endDate < startDate) {
            endDateInput.setCustomValidity(
                'A data final não pode ser anterior à data inicial.'
            );

            endDateInput.reportValidity();

            return;
        }

        endDateInput.setCustomValidity('');

        const millisecondsPerDay =
            1000 * 60 * 60 * 24;

        const difference =
            endDate.getTime() -
            startDate.getTime();

        const numberOfDays =
            Math.round(
                difference / millisecondsPerDay
            ) + 1;

        daysInput.value = numberOfDays;
    }

    function validateStartDate() {
        const startDate = parseLocalDate(
            startDateInput.value
        );

        if (!startDate) {
            return;
        }

        const selectedValue =
            startDateInput.value;

        const weekday =
            startDate.getDay();

        const warningDays = [4, 5, 6];

        if (
            warningDays.includes(weekday) &&
            lastWarnedDate !== selectedValue
        ) {
            if (toastMessage && vacationToast) {
                toastMessage.textContent =
                    'Atenção: a data escolhida para o início das férias pode não estar de acordo com as normas trabalhistas.';

                vacationToast.show();
            }

            lastWarnedDate = selectedValue;
        }
    }

    daysInput.addEventListener(
        'input',
        calculateEndDate
    );

    daysInput.addEventListener(
        'change',
        calculateEndDate
    );

    startDateInput.addEventListener(
        'change',
        function () {
            validateStartDate();

            if (daysInput.value !== '') {
                calculateEndDate();
            } else {
                calculateVacationDays();
            }
        }
    );

    endDateInput.addEventListener(
        'change',
        calculateVacationDays
    );

    const modal = form.closest('.vacation-modal');

    if (modal) {
        modal.addEventListener(
            'shown.bs.modal',
            function () {
                if (
                    startDateInput.value &&
                    endDateInput.value
                ) {
                    calculateVacationDays();
                }
            }
        );
    }

    if (
        startDateInput.value &&
        endDateInput.value
    ) {
        calculateVacationDays();
    }
}

function parseLocalDate(value) {
    if (!value) {
        return null;
    }

    const parts = value.split('-');

    if (parts.length !== 3) {
        return null;
    }

    const year = Number.parseInt(parts[0], 10);
    const month = Number.parseInt(parts[1], 10);
    const day = Number.parseInt(parts[2], 10);

    if (Number.isNaN(year) || Number.isNaN(month) || Number.isNaN(day)) {
        return null;
    }

    const date = new Date(year, month - 1, day);

    if (
        date.getFullYear() !== year ||
        date.getMonth() !== month - 1 ||
        date.getDate() !== day
    ) {
        return null;
    }

    return date;
}

function formatLocalDate(date) {
    if (
        !(date instanceof Date) ||
        Number.isNaN(date.getTime())
    ) {
        return '';
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function setFieldValue(id, value) {
    const field = document.getElementById(id);
    if (!field) {
        return;
    }
    field.value = value;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}