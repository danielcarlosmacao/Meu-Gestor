
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DADOS DAS FIBRAS
            |--------------------------------------------------------------------------
            */

            const fibersAll = @json($allFibers);
            const currentBoxId = @json($box->id);
            const fiberColors = @json($fiberColors);

            /*
            |--------------------------------------------------------------------------
            | CRIAÇÃO DAS FIBRAS DO CABO
            |--------------------------------------------------------------------------
            */

            const cableSelect = document.getElementById('cable_select');
            const fibersContainer = document.getElementById('fibers_container');

            if (cableSelect && fibersContainer) {

                cableSelect.addEventListener('change', function() {

                    const selectedOption =
                        this.options[this.selectedIndex];

                    const fiberQuantity =
                        parseInt(selectedOption?.dataset?.fibers || 0);

                    const cableInfo =
                        selectedOption?.dataset?.info || 'CABO';

                    fibersContainer.innerHTML = '';

                    if (!fiberQuantity) {
                        return;
                    }

                    for (let index = 1; index <= fiberQuantity; index++) {

                        const fiberName =
                            cableInfo +
                            '-F-' +
                            String(index).padStart(2, '0');

                        /*
                        | Não duplicar a fibra na caixa atual.
                        */

                        const alreadyExists = fibersAll.find(function(fiber) {
                            return (
                                fiber.fiber_identification === fiberName &&
                                Number(fiber.fiber_box_id) === Number(currentBoxId) &&
                                fiber.deleted_at === null
                            );
                        });

                        if (alreadyExists) {
                            continue;
                        }

                        /*
                        | Procura o sinal da mesma fibra em outra caixa.
                        */

                        const mirrorFiber = fibersAll.find(function(fiber) {
                            return (
                                fiber.fiber_identification === fiberName &&
                                Number(fiber.fiber_box_id) !== Number(currentBoxId) &&
                                fiber.optical_power !== null
                            );
                        });

                        const opticalPower =
                            mirrorFiber ? mirrorFiber.optical_power : '';

                        const item = document.createElement('div');

                        item.className =
                            'row g-2 mb-2 align-items-center fiber-item';

                        item.innerHTML = `
                            <div class="col-12 col-md-5">
                                <input type="hidden"
                                    name="fibers[${index}][fiber_identification]"
                                    value="${escapeHtml(fiberName)}">

                                <input type="text"
                                    class="form-control shadow-sm"
                                    value="${escapeHtml(fiberName)}"
                                    disabled>
                            </div>

                            <div class="col-10 col-md-5">
                                <div class="input-group">
                                    <input type="number"
                                        step="0.01"
                                        name="fibers[${index}][optical_power]"
                                        class="form-control shadow-sm"
                                        value="${escapeHtml(opticalPower)}"
                                        placeholder="Sinal">

                                    <span class="input-group-text">
                                        dBm
                                    </span>
                                </div>
                            </div>

                            <div class="col-2 col-md-2 text-end">
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-fiber"
                                    title="Remover fibra"
                                    aria-label="Remover fibra">

                                    <i class="bi bi-dash-lg"></i>
                                </button>
                            </div>
                        `;

                        fibersContainer.appendChild(item);
                    }

                    if (!fibersContainer.children.length) {

                        fibersContainer.innerHTML = `
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Todas as fibras deste cabo já estão cadastradas nesta caixa.
                            </div>
                        `;
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | REMOVER FIBRA DO FORMULÁRIO
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const removeButton =
                    event.target.closest('.remove-fiber');

                if (!removeButton) {
                    return;
                }

                const row =
                    removeButton.closest('.fiber-item');

                if (row) {
                    row.remove();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | COLORIR SELECTS DE FIBRA
            |--------------------------------------------------------------------------
            */

            const fiber1Select = document.getElementById('fiber1');
            const fiber2Select = document.getElementById('fiber2');

            function getFiberNumber(name) {

                if (!name) {
                    return null;
                }

                const match = name.match(/(\d+)$/);

                return match ? parseInt(match[1]) : null;
            }

            function isDarkColor(color) {

                const darkColors = [
                    '#000000',
                    '#0000ff',
                    '#8b4513',
                    '#8a2be2',
                    '#808080'
                ];

                return darkColors.includes(
                    String(color).toLowerCase()
                );
            }

            function paintFiberSelect(selectElement) {

                if (
                    !selectElement ||
                    selectElement.selectedIndex < 0
                ) {
                    return;
                }

                const selectedOption =
                    selectElement.options[selectElement.selectedIndex];

                if (!selectedOption) {
                    return;
                }

                const fiberNumber =
                    getFiberNumber(selectedOption.text);

                const background =
                    fiberColors[fiberNumber] || '#ffffff';

                selectElement.style.backgroundColor = background;
                selectElement.style.color =
                    isDarkColor(background) ? '#ffffff' : '#000000';
            }

            function updateDisabledFiberOptions() {

                if (!fiber1Select || !fiber2Select) {
                    return;
                }

                const selectedFiber1 = fiber1Select.value;
                const selectedFiber2 = fiber2Select.value;

                Array.from(fiber2Select.options).forEach(function(option) {
                    option.disabled =
                        Boolean(selectedFiber1) &&
                        option.value === selectedFiber1;
                });

                Array.from(fiber1Select.options).forEach(function(option) {
                    option.disabled =
                        Boolean(selectedFiber2) &&
                        option.value === selectedFiber2;
                });
            }

            if (fiber1Select) {

                fiber1Select.addEventListener('change', function() {
                    updateDisabledFiberOptions();
                    paintFiberSelect(this);
                });

                paintFiberSelect(fiber1Select);
            }

            if (fiber2Select) {

                fiber2Select.addEventListener('change', function() {
                    updateDisabledFiberOptions();
                    paintFiberSelect(this);
                });

                paintFiberSelect(fiber2Select);
            }

            updateDisabledFiberOptions();

            /*
            |--------------------------------------------------------------------------
            | TOOLTIPS
            |--------------------------------------------------------------------------
            */

            if (typeof bootstrap !== 'undefined') {

                const tooltipElements =
                    document.querySelectorAll('[data-bs-toggle="tooltip"]');

                tooltipElements.forEach(function(element) {

                    new bootstrap.Tooltip(element, {
                        boundary: 'window'
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | SELETOR DE COR DO CABO
            |--------------------------------------------------------------------------
            */

            const colorPicker =
                document.getElementById('colorPicker');

            const colorHex =
                document.getElementById('colorHex');

            if (colorPicker && colorHex) {

                colorPicker.addEventListener('input', function() {
                    colorHex.value = colorPicker.value;
                });

                colorHex.addEventListener('input', function() {

                    const validColor =
                        /^#([0-9A-F]{3}){1,2}$/i.test(colorHex.value);

                    if (validColor) {
                        colorPicker.value = colorHex.value;
                    }
                });

                colorHex.value = colorPicker.value;
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL EDITAR SINAL
            |--------------------------------------------------------------------------
            */

            const editSignalModalElement =
                document.getElementById('editSignalModal');

            if (
                editSignalModalElement &&
                typeof bootstrap !== 'undefined'
            ) {

                const editSignalModal =
                    new bootstrap.Modal(editSignalModalElement);

                const fiberNameInput =
                    document.getElementById('fiber_name');

                const oldSignalInput =
                    document.getElementById('old_signal');

                const editSignalForm =
                    document.getElementById('formEditSignal');

                document
                    .querySelectorAll('.btn-edit-signal')
                    .forEach(function(button) {

                        button.addEventListener('click', function() {

                            const fiberId =
                                this.dataset.id;

                            const fiberName =
                                this.dataset.fiber || '';

                            const fiberSignal =
                                this.dataset.signal || '';

                            if (fiberNameInput) {
                                fiberNameInput.value = fiberName;
                            }

                            if (oldSignalInput) {
                                oldSignalInput.value = fiberSignal;
                            }

                            if (editSignalForm) {
                                editSignalForm.action =
                                    "{{ url('/ftth/fiber-box/updatesignal') }}/" +
                                    fiberId;
                            }

                            editSignalModal.show();
                        });
                    });
            }

        });

        /*
        |--------------------------------------------------------------------------
        | COPIAR COR DO CABO
        |--------------------------------------------------------------------------
        */

        function copyColor(color) {

            if (!navigator.clipboard) {

                if (typeof toastr !== 'undefined') {
                    toastr.warning('Seu navegador não permite copiar automaticamente.');
                }

                return;
            }

            navigator.clipboard
                .writeText(color)
                .then(function() {

                    if (typeof toastr !== 'undefined') {
                        toastr.success('Cor copiada: ' + color);
                    }

                })
                .catch(function() {

                    if (typeof toastr !== 'undefined') {
                        toastr.error('Não foi possível copiar a cor.');
                    }

                });
        }

        /*
        |--------------------------------------------------------------------------
        | ESCAPAR TEXTO INSERIDO NO HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        /*
        |--------------------------------------------------------------------------
        | COMPATIBILIDADE COM O ANTIGO MODAL DE EDIÇÃO
        |--------------------------------------------------------------------------
        |
        | Mantido para não perder funcionalidade caso outro botão ainda utilize
        | esta função em algum modal ou componente.
        |
        */

        function openEditFiberModal(id, name, power) {

            const modalElement =
                document.getElementById('modalEditFiber');

            const fiberNameInput =
                document.getElementById('editFiberName');

            const fiberPowerInput =
                document.getElementById('editFiberPower');

            const editForm =
                document.getElementById('formEditFiber');

            if (
                !modalElement ||
                !editForm ||
                typeof bootstrap === 'undefined'
            ) {
                return;
            }

            if (fiberNameInput) {
                fiberNameInput.value = name ?? '';
            }

            if (fiberPowerInput) {
                fiberPowerInput.value = power ?? '';
            }

            let updateUrl =
                "{{ route('fiber.update', ':id') }}";

            updateUrl =
                updateUrl.replace(':id', id);

            editForm.action = updateUrl;

            const modal =
                new bootstrap.Modal(modalElement);

            modal.show();
        }