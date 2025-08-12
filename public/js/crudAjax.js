// public/js/CrudAjax.js

document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // DELETE genérico
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            if (!confirm('Tem certeza que deseja excluir este item?')) return;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    toastr.success(data.message || 'Item excluído com sucesso!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Erro ao excluir.');
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error('Erro ao processar requisição.');
            });
        });
    });

    // UPDATE genérico
    document.querySelectorAll('.btn-update').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const formId = this.getAttribute('data-form');
            const form = document.getElementById(formId);

            if (!form) {
                toastr.error('Formulário não encontrado.');
                return;
            }

            const formData = new FormData(form);
            formData.append('_method', 'PUT'); // Necessário para Laravel

            fetch(url, {
                method: 'POST', // Laravel aceita POST com _method=PUT
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    toastr.success(data.message || 'Atualizado com sucesso!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Erro ao atualizar.');
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error('Erro ao processar atualização.');
            });
        });
    });

    // CREATE genérico
    document.querySelectorAll('.btn-create').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const formId = this.getAttribute('data-form');
            const form = document.getElementById(formId);

            if (!form) {
                toastr.error('Formulário não encontrado.');
                return;
            }

            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    toastr.success(data.message || 'Criado com sucesso!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Erro ao criar.');
                }
            })
            .catch(error => {
                console.error(error);
                toastr.error('Erro ao processar criação.');
            });
        });
    });
});
