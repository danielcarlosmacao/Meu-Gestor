document.addEventListener('DOMContentLoaded', function() {
    window.confirmAction = function(form, message = 'Tem certeza?') {
        event.preventDefault();
        Swal.fire({
            title: 'Confirmação',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true   // <-- essa linha faz o foco iniciar no Cancelar
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    };
});
