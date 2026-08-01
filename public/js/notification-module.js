/**
 * ===========================================================
 * Notification Module
 * Arquivo: public/js/notification-module.js
 * ===========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initNotificationEditModal();
    initNotificationConfirmations();
    initNotificationSubmitLock();
    initNotificationLogMessages();
    initNotificationRecipientForms();
    initNotificationModalFocus();
});

/**
 * ===========================================================
 * Modal de edição das notificações
 * ===========================================================
 */
function initNotificationEditModal() {
    const modal = document.getElementById(
        'editNotificationModal'
    );

    if (!modal) {
        return;
    }

    modal.addEventListener(
        'show.bs.modal',
        function (event) {
            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            const message =
                button.dataset.msg || '';

            const sendAt =
                button.dataset.sendAt || '';

            const updateUrl =
                button.dataset.updateUrl || '';

            const notificationId =
                button.dataset.id || '';

            const form = document.getElementById(
                'editNotificationForm'
            );

            const messageField = document.getElementById(
                'edit-msg'
            );

            const sendAtField = document.getElementById(
                'edit-send_at'
            );

            if (form) {
                if (updateUrl !== '') {
                    form.action = updateUrl;
                } else if (notificationId !== '') {
                    form.action =
                        '/admin/notification/' +
                        encodeURIComponent(notificationId);
                }
            }

            if (messageField) {
                messageField.value = message;
            }

            if (sendAtField) {
                sendAtField.value =
                    normalizeDateTimeLocal(sendAt);
            }
        }
    );

    modal.addEventListener(
        'hidden.bs.modal',
        function () {
            const form = document.getElementById(
                'editNotificationForm'
            );

            if (form) {
                resetSubmitLock(form);
            }
        }
    );
}

/**
 * ===========================================================
 * Normaliza a data para datetime-local
 *
 * Formato esperado:
 * YYYY-MM-DDTHH:mm
 * ===========================================================
 */
function normalizeDateTimeLocal(value) {
    if (!value) {
        return '';
    }

    const normalizedValue =
        String(value).trim();

    /*
     * Aceita:
     * 2026-07-31T15:30
     * 2026-07-31 15:30:00
     * 2026-07-31T15:30:00.000000Z
     */
    const directMatch = normalizedValue.match(
        /^(\d{4}-\d{2}-\d{2})[T\s](\d{2}:\d{2})/
    );

    if (directMatch) {
        return `${directMatch[1]}T${directMatch[2]}`;
    }

    const date = new Date(normalizedValue);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const year = date.getFullYear();

    const month = String(
        date.getMonth() + 1
    ).padStart(2, '0');

    const day = String(
        date.getDate()
    ).padStart(2, '0');

    const hours = String(
        date.getHours()
    ).padStart(2, '0');

    const minutes = String(
        date.getMinutes()
    ).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

/**
 * ===========================================================
 * Confirmações de ações
 *
 * Exemplo:
 * data-confirm="Tem certeza?"
 * ===========================================================
 */
function initNotificationConfirmations() {
    document
        .querySelectorAll('[data-confirm]')
        .forEach((element) => {
            element.addEventListener(
                'click',
                function (event) {
                    const message =
                        this.dataset.confirm ||
                        'Deseja realmente continuar?';

                    if (!window.confirm(message)) {
                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();
                    }
                }
            );
        });
}

/**
 * ===========================================================
 * Evita envio duplo de formulários
 *
 * Formulário:
 * data-submit-lock
 * ===========================================================
 */
function initNotificationSubmitLock() {
    document
        .querySelectorAll('form[data-submit-lock]')
        .forEach((form) => {
            form.addEventListener(
                'submit',
                function (event) {
                    /*
                     * Caso o formulário ainda esteja inválido,
                     * não bloqueia o botão.
                     */
                    if (!form.checkValidity()) {
                        return;
                    }

                    /*
                     * A validação específica de destinatários
                     * é feita antes deste bloqueio.
                     */
                    if (
                        form.hasAttribute(
                            'data-recipient-required'
                        ) &&
                        !hasSelectedRecipients(form)
                    ) {
                        return;
                    }

                    const submitButton =
                        event.submitter ||
                        form.querySelector(
                            'button[type="submit"], input[type="submit"]'
                        );

                    if (
                        !submitButton ||
                        submitButton.disabled
                    ) {
                        return;
                    }

                    lockSubmitButton(
                        submitButton,
                        'Processando...'
                    );
                }
            );
        });
}

/**
 * Bloqueia um botão de envio.
 */
function lockSubmitButton(
    button,
    loadingText = 'Processando...'
) {
    button.disabled = true;

    if (button.tagName === 'BUTTON') {
        button.dataset.originalHtml =
            button.innerHTML;

        button.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" ' +
            'role="status" aria-hidden="true"></span>' +
            escapeHtml(loadingText);

        return;
    }

    button.dataset.originalValue =
        button.value;

    button.value = loadingText;
}

/**
 * Restaura os botões de um formulário.
 */
function resetSubmitLock(form) {
    if (!form) {
        return;
    }

    form
        .querySelectorAll(
            'button[type="submit"], input[type="submit"]'
        )
        .forEach((button) => {
            button.disabled = false;

            if (
                button.tagName === 'BUTTON' &&
                button.dataset.originalHtml
            ) {
                button.innerHTML =
                    button.dataset.originalHtml;

                delete button.dataset.originalHtml;
            }

            if (
                button.tagName === 'INPUT' &&
                button.dataset.originalValue
            ) {
                button.value =
                    button.dataset.originalValue;

                delete button.dataset.originalValue;
            }
        });
}

/**
 * ===========================================================
 * Expande e recolhe mensagens dos logs
 * ===========================================================
 */
function initNotificationLogMessages() {
    document
        .querySelectorAll('[data-message-toggle]')
        .forEach((button) => {
            button.addEventListener(
                'click',
                function () {
                    const shortSelector =
                        this.dataset.shortTarget || '';

                    const fullSelector =
                        this.dataset.fullTarget || '';

                    const shortText = shortSelector
                        ? document.querySelector(
                            shortSelector
                        )
                        : null;

                    const fullText = fullSelector
                        ? document.querySelector(
                            fullSelector
                        )
                        : null;

                    if (!shortText || !fullText) {
                        return;
                    }

                    const fullTextIsHidden =
                        fullText.classList.contains(
                            'd-none'
                        );

                    shortText.classList.toggle(
                        'd-none',
                        fullTextIsHidden
                    );

                    fullText.classList.toggle(
                        'd-none',
                        !fullTextIsHidden
                    );

                    this.textContent =
                        fullTextIsHidden
                            ? 'Mostrar menos'
                            : 'Mostrar mais';

                    this.setAttribute(
                        'aria-expanded',
                        fullTextIsHidden
                            ? 'true'
                            : 'false'
                    );
                }
            );
        });
}

/**
 * ===========================================================
 * Destinatários da notificação
 *
 * - Selecionar todos
 * - Desmarcar todos
 * - Validar pelo menos um selecionado
 * ===========================================================
 */
function initNotificationRecipientForms() {
    document
        .querySelectorAll(
            'form[data-recipient-required]'
        )
        .forEach((form) => {
            const recipientList = form.querySelector(
                '[data-recipient-list]'
            );

            const feedback = form.querySelector(
                '[data-recipient-feedback]'
            );

            const toggleButton = form.querySelector(
                '[data-toggle-recipients]'
            );

            const checkboxes = Array.from(
                form.querySelectorAll(
                    'input[name="recipient_ids[]"]'
                )
            );

            if (!recipientList) {
                return;
            }

            if (checkboxes.length === 0) {
                recipientList.classList.add(
                    'is-invalid'
                );

                if (feedback) {
                    feedback.textContent =
                        'Nenhum destinatário está disponível.';

                    feedback.style.display = 'block';
                }

                return;
            }

            /**
             * Atualiza o estado visual da lista.
             */
            function updateRecipientState(
                showError = false
            ) {
                const hasSelected =
                    checkboxes.some(
                        (checkbox) => checkbox.checked
                    );

                recipientList.classList.toggle(
                    'is-invalid',
                    showError && !hasSelected
                );

                if (feedback) {
                    feedback.style.display =
                        showError && !hasSelected
                            ? 'block'
                            : 'none';
                }

                return hasSelected;
            }

            /**
             * Atualiza o texto do botão
             * selecionar/desmarcar todos.
             */
            function updateToggleButton() {
                if (!toggleButton) {
                    return;
                }

                const allSelected =
                    checkboxes.length > 0 &&
                    checkboxes.every(
                        (checkbox) =>
                            checkbox.checked
                    );

                toggleButton.dataset.allSelected =
                    allSelected
                        ? 'true'
                        : 'false';

                toggleButton.innerHTML =
                    allSelected
                        ? '<i class="bi bi-square me-1"></i> Desmarcar todos'
                        : '<i class="bi bi-check2-square me-1"></i> Selecionar todos';
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener(
                    'change',
                    function () {
                        updateRecipientState(false);
                        updateToggleButton();
                    }
                );
            });

            if (toggleButton) {
                toggleButton.addEventListener(
                    'click',
                    function () {
                        const shouldSelect =
                            this.dataset.allSelected !==
                            'true';

                        checkboxes.forEach(
                            (checkbox) => {
                                checkbox.checked =
                                    shouldSelect;
                            }
                        );

                        updateRecipientState(false);
                        updateToggleButton();
                    }
                );
            }

            /*
             * Esta validação deve ser registrada
             * antes do submit lock.
             */
            form.addEventListener(
                'submit',
                function (event) {
                    const isValid =
                        updateRecipientState(true);

                    if (isValid) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    event.stopImmediatePropagation();

                    recipientList.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    const firstCheckbox =
                        checkboxes[0];

                    if (firstCheckbox) {
                        firstCheckbox.focus({
                            preventScroll: true
                        });
                    }
                },
                true
            );

            updateRecipientState(false);
            updateToggleButton();

            const modal = form.closest('.modal');

            if (modal) {
                modal.addEventListener(
                    'hidden.bs.modal',
                    function () {
                        resetSubmitLock(form);
                        updateRecipientState(false);
                    }
                );
            }
        });
}

/**
 * Confere se há destinatário selecionado.
 */
function hasSelectedRecipients(form) {
    if (!form) {
        return false;
    }

    return Array.from(
        form.querySelectorAll(
            'input[name="recipient_ids[]"]'
        )
    ).some(
        (checkbox) => checkbox.checked
    );
}

/**
 * ===========================================================
 * Foco automático nos modais
 * ===========================================================
 */
function initNotificationModalFocus() {
    document
        .querySelectorAll(
            '.notification-modal'
        )
        .forEach((modal) => {
            modal.addEventListener(
                'shown.bs.modal',
                function () {
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
                }
            );
        });
}

/**
 * ===========================================================
 * Escape simples para conteúdo inserido com innerHTML
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