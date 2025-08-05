/**
 * ajax-crud.js
 * Funções genéricas para CRUD via AJAX com fetch e Laravel (CSRF, métodos REST)
 */

(() => {
  // Pega token CSRF do meta tag (no header do seu layout já deve ter)
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (!csrfToken) {
    console.error('Token CSRF não encontrado no meta tag.');
  }

  /**
   * Função para enviar formulário via AJAX
   * @param {object} options - Configurações:
   *  - formElement: elemento form (obrigatório)
   *  - url: endpoint da requisição (obrigatório)
   *  - method: método HTTP (default 'POST', Laravel usa POST+_method)
   *  - onSuccess: callback sucesso (recebe data)
   *  - onError: callback erro (recebe erro)
   */
  async function ajaxFormSubmit({ formElement, url, method = 'POST', onSuccess, onError }) {
    try {
      const formData = new FormData(formElement);

      // Para Laravel: método PUT, DELETE, PATCH são enviados via POST com campo _method
      if (method.toUpperCase() !== 'POST') {
        formData.set('_method', method.toUpperCase());
      }

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData,
      });

      const data = await response.json();

      if (!response.ok) throw data;

      if (onSuccess) onSuccess(data);
    } catch (error) {
      if (onError) onError(error);
      else console.error(error);
    }
  }

  /**
   * Função para deletar via AJAX (sem form)
   * @param {object} options - Configurações:
   *  - url: endpoint DELETE (obrigatório)
   *  - onSuccess: callback sucesso (recebe data)
   *  - onError: callback erro (recebe erro)
   */
  async function ajaxDelete({ url, onSuccess, onError }) {
    try {
      const response = await fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      });

      const data = await response.json();

      if (!response.ok) throw data;

      if (onSuccess) onSuccess(data);
    } catch (error) {
      if (onError) onError(error);
      else console.error(error);
    }
  }

  // Expõe globalmente para uso em outras partes do código
  window.ajaxFormSubmit = ajaxFormSubmit;
  window.ajaxDelete = ajaxDelete;

  // ===========================
  // Exemplos básicos de uso:
  // ===========================

  // Criar (exemplo com form #createForm)
  const createForm = document.querySelector('#createForm');
  if (createForm) {
    createForm.addEventListener('submit', e => {
      e.preventDefault();

      ajaxFormSubmit({
        formElement: createForm,
        url: createForm.action,
        method: 'POST',
        onSuccess: data => {
          showToast(data.message);
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
          if (modal) modal.hide();
          createForm.reset();
          location.reload();
        },
        onError: error => showToast('Erro ao salvar', true),
      });
    });
  }

  // Editar (exemplo com form #editForm)
  const editForm = document.querySelector('#editForm');
  if (editForm) {
    editForm.addEventListener('submit', e => {
      e.preventDefault();

      ajaxFormSubmit({
        formElement: editForm,
        url: editForm.action,
        method: 'PUT',
        onSuccess: data => {
          showToast(data.message);
          const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
          if (modal) modal.hide();
          location.reload();
        },
        onError: error => showToast('Erro ao atualizar', true),
      });
    });
  }

  // Deletar (botões com classe .delete-btn e data-url)
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
      if (!confirm('Confirma a exclusão?')) return;

      const url = button.dataset.url;

      ajaxDelete({
        url,
        onSuccess: data => {
          showToast(data.message);
          const row = button.closest('tr');
          if (row) row.remove();
        },
        onError: error => showToast('Erro ao deletar', true),
      });
    });
  });

})();
