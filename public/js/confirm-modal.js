window.openConfirmModal = function (url, title, subtext = '', method = 'POST') {
    const form = document.getElementById('confirmForm');

    form.action = url;

    // limpa métodos antigos
    let methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();

    // adiciona method spoofing se necessário
    if (method !== 'POST') {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_method';
        input.value = method;
        form.appendChild(input);
    }

    document.getElementById('confirmMessage').innerText = title;
    document.getElementById('confirmSubMessage').innerText = subtext;

    new bootstrap.Modal(document.getElementById('confirmModal')).show();
};