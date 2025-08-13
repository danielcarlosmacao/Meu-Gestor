 function toggleMenu(btn) {
        const menu = document.querySelector(".exo-menu");
        menu.classList.toggle("show");
        btn.classList.toggle("active");
        btn.textContent = menu.classList.contains("show") ? "✖" : "☰";
    }

    document.addEventListener("DOMContentLoaded", function () {
        const dropDownItems = document.querySelectorAll(".exo-menu .drop-down > a");

        dropDownItems.forEach((item) => {
            item.addEventListener("click", function (e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;

                    document.querySelectorAll(".exo-menu .drop-down-ul").forEach(ul => {
                        if (ul !== submenu) {
                            ul.style.display = "none";
                        }
                    });

                    submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
                }
            });
        });
    });


/*
$(function () {
    $('.toggle-menu').click(function(){
       $('.exo-menu').toggleClass('display');
       
    });
    
});*/
   
// Função de delete.

  function deletar(id) {
    if (!confirm('Tem certeza que deseja deletar ' + refDestroy + ' ?')) return;

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = routeDestroy.replace(':id', id);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Erro ao deletar ' + refDestroy);
        return res.json();
    })
    .then(data => {
        toastr.success(data.message); // Mostra toast
        setTimeout(() => window.location.reload(), 1500); // Dá tempo de ver o toast antes de recarregar
    })
    .catch(err => {
        console.error('Erro:', err);
        toastr.error('Erro ao deletar ' + refDestroy);
    });
}

function mostrarPopupConfirmacao() {
    var confirmacao = confirm("Tem certeza que quer reparar todas as torres?");
    if (confirmacao == true) {
        // Redireciona o utilizador para o URL do link
        window.location.href = "/tower/repairsummary"; // Substitua "seu_link_aqui" pelo URL do link
    } else {
        // Não faz nada, ou pode optar por bloquear a ação
        return false;
    }
}
  
// Função reutilizável para ativar datepicker com padrão BR
function setupBrazilianDatepicker(selector) {
    if (!window.flatpickr) {
        console.error('Flatpickr não está carregado.');
        return;
    }

    flatpickr(selector, {
        locale: "pt",
        dateFormat: "Y-m-d",
        allowInput: true,
        altInput: true,
        altFormat: "d/m/Y",
        wrap: false,
        //maxDate: "today",
    });
}

// Inicializa os datepickers ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    setupBrazilianDatepicker('.datepicker');
});

