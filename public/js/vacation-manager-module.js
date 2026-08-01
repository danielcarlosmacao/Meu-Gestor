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
});

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
 *
 * Exemplo:
 * data-confirm="Deseja excluir este registro?"
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
 *
 * Exemplo:
 * data-file-preview="#nomeDoElemento"
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
 *
 * Formulário:
 * data-submit-lock
 * ===========================================================
 */
function initSubmitLock() {
    document
        .querySelectorAll('form[data-submit-lock]')
        .forEach((form) => {
            form.addEventListener('submit', function (event) {
                /*
                 * Caso o formulário esteja inválido, não bloqueia
                 * o botão porque o navegador não enviará os dados.
                 */
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
 *
 * - Atualiza a data final pela quantidade de dias
 * - Atualiza a quantidade de dias pela data final
 * - Funciona no cadastro e na edição
 * - Alerta início em quinta, sexta ou sábado
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

/**
 * Inicializa um formulário específico de férias.
 */
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

    /**
     * Atualiza a data final.
     *
     * Exemplo:
     * início: 01/08
     * dias: 30
     * término: 30/08
     */
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

        /*
         * Subtrai 1 porque o dia inicial
         * também faz parte do período.
         */
        endDate.setDate(
            endDate.getDate() + numberOfDays - 1
        );

        endDateInput.value =
            formatLocalDate(endDate);

        endDateInput.setCustomValidity('');
    }

    /**
     * Atualiza a quantidade de dias quando a
     * data inicial ou final é alterada manualmente.
     */
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

    /**
     * Verifica o dia da semana da data inicial.
     */
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

        /*
         * 4 = quinta-feira
         * 5 = sexta-feira
         * 6 = sábado
         */
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

    /**
     * Alteração na quantidade de dias.
     */
    daysInput.addEventListener(
        'input',
        calculateEndDate
    );

    daysInput.addEventListener(
        'change',
        calculateEndDate
    );

    /**
     * Alteração na data inicial.
     */
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

    /**
     * Alteração manual na data final.
     */
    endDateInput.addEventListener(
        'change',
        calculateVacationDays
    );

    /**
     * Ao abrir o modal, garante que a quantidade
     * de dias esteja preenchida corretamente.
     */
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

    /**
     * Também calcula imediatamente caso o formulário
     * de edição já tenha datas preenchidas.
     */
    if (
        startDateInput.value &&
        endDateInput.value
    ) {
        calculateVacationDays();
    }
}

/**
 * ===========================================================
 * Converte YYYY-MM-DD para uma data local
 *
 * Evita problema de fuso horário causado por:
 * new Date('2026-07-31')
 * ===========================================================
 */
function parseLocalDate(value) {
    if (!value) {
        return null;
    }

    const parts = value.split('-');

    if (parts.length !== 3) {
        return null;
    }

    const year = Number.parseInt(
        parts[0],
        10
    );

    const month = Number.parseInt(
        parts[1],
        10
    );

    const day = Number.parseInt(
        parts[2],
        10
    );

    if (
        Number.isNaN(year) ||
        Number.isNaN(month) ||
        Number.isNaN(day)
    ) {
        return null;
    }

    const date = new Date(
        year,
        month - 1,
        day
    );

    /*
     * Confirma se a data construída corresponde
     * exatamente aos valores informados.
     */
    if (
        date.getFullYear() !== year ||
        date.getMonth() !== month - 1 ||
        date.getDate() !== day
    ) {
        return null;
    }

    return date;
}

/**
 * ===========================================================
 * Converte uma data local para YYYY-MM-DD
 * ===========================================================
 */
function formatLocalDate(date) {
    if (
        !(date instanceof Date) ||
        Number.isNaN(date.getTime())
    ) {
        return '';
    }

    const year =
        date.getFullYear();

    const month = String(
        date.getMonth() + 1
    ).padStart(2, '0');

    const day = String(
        date.getDate()
    ).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

/**
 * ===========================================================
 * Define o valor de um campo pelo ID
 * ===========================================================
 */
function setFieldValue(id, value) {
    const field =
        document.getElementById(id);

    if (!field) {
        return;
    }

    field.value = value;
}

/**
 * ===========================================================
 * Escapa texto antes de inserir com innerHTML
 * ===========================================================
 */
function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}