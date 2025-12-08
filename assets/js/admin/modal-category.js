/**
 * Modal de Categorías - Medical Works
 * Maneja creación y edición de categorías
 */

const modalCategory = document.getElementById('modalAddCategory');
const formCategory = document.getElementById('formAddCategory');
const btnOpenCategory = document.getElementById('btnCreateCategory');
const btnCloseCategory = document.getElementById('btnCloseCategoryModal');
const btnCancelCategory = document.getElementById('btnCancelCategory');
const btnSaveCategory = document.getElementById('btnSaveCategory');
const formErrorCategory = document.getElementById('formCategoryError');
const modalTitleCategory = document.getElementById('modalCategoryTitle');
const modalSubtitleCategory = document.getElementById('modalCategorySubtitle');

let modalCategoryMode = 'create';
let editingCategoryId = null;

// Abrir modal para crear
if (btnOpenCategory) {
  btnOpenCategory.addEventListener('click', () => {
    openCategoryModal('create');
  });
}

// Función global para abrir modal de edición
window.openEditCategoryModal = function(category) {
  modalCategoryMode = 'edit';
  editingCategoryId = category.id_category;
  openCategoryModal('edit', category);
};

// Cerrar modal
if (btnCloseCategory) {
  btnCloseCategory.addEventListener('click', closeCategoryModal);
}

if (btnCancelCategory) {
  btnCancelCategory.addEventListener('click', closeCategoryModal);
}

// Cerrar al hacer clic fuera
if (modalCategory) {
  modalCategory.addEventListener('click', (e) => {
    if (e.target === modalCategory) {
      closeCategoryModal();
    }
  });
}

// Cerrar con ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modalCategory && modalCategory.classList.contains('active')) {
    closeCategoryModal();
  }
});

/**
 * Abrir modal
 */
function openCategoryModal(mode = 'create', categoryData = null) {
  modalCategoryMode = mode;
  
  if (modalCategory) {
    modalCategory.classList.add('active');
    modalCategory.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  if (modalTitleCategory && modalSubtitleCategory) {
    if (mode === 'edit') {
      modalTitleCategory.textContent = 'Editar Categoría';
      modalSubtitleCategory.textContent = 'Modifica el nombre de la categoría';
      
      // Llenar formulario con datos existentes
      if (categoryData) {
        document.getElementById('categoryName').value = categoryData.name || categoryData.nombre || '';
      }
    } else {
      modalTitleCategory.textContent = 'Crear Nueva Categoría';
      modalSubtitleCategory.textContent = 'Completa el campo para agregar una categoría';
    }
  }

  if (btnSaveCategory) {
    btnSaveCategory.innerHTML = `
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z" />
      </svg>
      ${mode === 'edit' ? 'Actualizar Categoría' : 'Guardar Categoría'}
    `;
  }

  hideError();
  setTimeout(() => {
    document.getElementById('categoryName')?.focus();
  }, 100);
}

/**
 * Cerrar modal
 */
function closeCategoryModal() {
  if (modalCategory) {
    modalCategory.classList.remove('active');
    modalCategory.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  formCategory.reset();
  hideError();
  modalCategoryMode = 'create';
  editingCategoryId = null;
}

/**
 * Mostrar error
 */
function showError(message) {
  if (formErrorCategory) {
    formErrorCategory.textContent = message;
    formErrorCategory.style.display = 'block';
  }
}

/**
 * Ocultar error
 */
function hideError() {
  if (formErrorCategory) {
    formErrorCategory.textContent = '';
    formErrorCategory.style.display = 'none';
  }
}

/**
 * Guardar categoría
 */
if (formCategory) {
  formCategory.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();

    if (btnSaveCategory.disabled) return;

    const name = document.getElementById('categoryName').value.trim();

    if (!name) {
      showError('El nombre de la categoría es requerido');
      return;
    }

    // Deshabilitar botón
    btnSaveCategory.disabled = true;
    const originalText = btnSaveCategory.innerHTML;
    btnSaveCategory.innerHTML = `
      <svg width="16" height="16" fill="currentColor" class="spinner" viewBox="0 0 16 16">
        <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM7 12.5V11H5.5A1.5 1.5 0 0 1 4 9.5V8H2.5A.5.5 0 0 1 2 7.5V6.5A.5.5 0 0 1 2.5 6H4V4.5A1.5 1.5 0 0 1 5.5 3H7V1.5A.5.5 0 0 1 7.5 1h1a.5.5 0 0 1 .5.5V3h1.5A1.5 1.5 0 0 1 12 4.5V6h1.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H12v1.5a1.5 1.5 0 0 1-1.5 1.5H9v1.5a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
      </svg>
      Guardando...
    `;

    try {
      const formData = new FormData();
      formData.append('name', name);

      let url = '/api/create_category.php';
      if (modalCategoryMode === 'edit') {
        url = '/api/update_category.php';
        formData.append('id_category', editingCategoryId);
      }

      const response = await fetch(url, {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Error al guardar la categoría');
      }

      const successMessage =
        modalCategoryMode === 'edit'
          ? 'Categoría actualizada exitosamente'
          : 'Categoría agregada exitosamente';

      // Cerrar modal primero
      closeCategoryModal();

      // Mostrar toast después de cerrar
      setTimeout(() => {
        if (typeof showToast === 'function') {
          showToast(successMessage, 'success');
        }
      }, 100);

      // Recargar categorías
      if (typeof loadCategoriesAdmin === 'function') {
        await loadCategoriesAdmin();
      }

      // Renderizar tabla de categorías
      if (typeof renderPage === 'function') {
        renderPage('categorias', 1, false);
      }

    } catch (error) {
      console.error('Error:', error);
      showError(error.message || 'Error al guardar la categoría');
    } finally {
      btnSaveCategory.disabled = false;
      btnSaveCategory.innerHTML = originalText;
    }
  });
}
