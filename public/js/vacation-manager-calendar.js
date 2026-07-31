/**
 * ===========================================================
 * Vacation Manager Calendar
 * Arquivo: public/js/vacation-manager-calendar.js
 * ===========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initVacationCalendarTooltips();
    initVacationCalendarYearFilter();
});

/**
 * ===========================================================
 * Inicializa os tooltips do calendário
 * ===========================================================
 */
function initVacationCalendarTooltips() {
    if (
        typeof bootstrap === 'undefined' ||
        !bootstrap.Tooltip
    ) {
        return;
    }

    document
        .querySelectorAll(
            '.vacation-calendar [data-bs-toggle="tooltip"]'
        )
        .forEach((element) => {
            bootstrap.Tooltip.getOrCreateInstance(
                element,
                {
                    container: 'body',
                    trigger: 'hover focus'
                }
            );
        });
}

/**
 * ===========================================================
 * Controle do filtro de ano
 * ===========================================================
 */
function initVacationCalendarYearFilter() {
    const yearInput = document.getElementById(
        'calendarYear'
    );

    if (!yearInput) {
        return;
    }

    yearInput.addEventListener('input', function () {
        validateCalendarYear(this);
    });

    yearInput.addEventListener('change', function () {
        validateCalendarYear(this);
    });

    yearInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') {
            return;
        }

        if (!validateCalendarYear(this)) {
            event.preventDefault();
        }
    });
}

/**
 * ===========================================================
 * Valida o ano informado
 * ===========================================================
 */
function validateCalendarYear(input) {
    const minimumYear = Number.parseInt(
        input.min || '2000',
        10
    );

    const maximumYear = Number.parseInt(
        input.max || '2100',
        10
    );

    const selectedYear = Number.parseInt(
        input.value,
        10
    );

    input.setCustomValidity('');

    if (input.value === '') {
        input.setCustomValidity(
            'Informe o ano do calendário.'
        );

        return false;
    }

    if (Number.isNaN(selectedYear)) {
        input.setCustomValidity(
            'Informe um ano válido.'
        );

        return false;
    }

    if (selectedYear < minimumYear) {
        input.setCustomValidity(
            `O ano mínimo permitido é ${minimumYear}.`
        );

        return false;
    }

    if (selectedYear > maximumYear) {
        input.setCustomValidity(
            `O ano máximo permitido é ${maximumYear}.`
        );

        return false;
    }

    return true;
}