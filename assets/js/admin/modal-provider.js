const modalProvider = document.getElementById('modalAddProvider');
const formProvider = document.getElementById('formAddProvider');
const btnOpenProvider = document.getElementById('btnCreateProvider');
const btnCloseProvider = document.getElementById('btnCloseProviderModal');
const btnCancelProvider = document.getElementById('btnCancelProvider');
const btnSaveProvider = document.getElementById('btnSaveProvider');
const imageInputProvider = document.getElementById('providerImage');
const imagePreviewProvider = document.getElementById('providerImagePreview');
const imagePreviewContainerProvider = document.getElementById('providerImagePreviewContainer');
const removePreviewBtnProvider = document.getElementById('btnRemoveProviderPreview');
const formErrorProvider = document.getElementById('formProviderError');
const toggleStatusProvider = document.getElementById('providerStatus');
const statusTextProvider = document.querySelector('#modalAddProvider .status-text');
const modalTitleProvider = document.getElementById('modalProviderTitle');
const modalSubtitleProvider = document.getElementById('modalProviderSubtitle');

let modalProviderMode = 'create';
let editingProviderId = null;
let existingProviderImagePath = null;

if (toggleStatusProvider && statusTextProvider) {
  toggleStatusProvider.addEventListener('change', () => {
    statusTextProvider.textContent = toggleStatusProvider.checked ? 'Activo' : 'Inactivo';
  });
}

if (btnOpenProvider) {
  btnOpenProvider.addEventListener('click', () => {
    openProviderModal('create');
  });
}

window.openEditProviderModal = function(provider) {
  modalProviderMode = 'edit';
  editingProviderId = provider.id_provider;
  existingProviderImagePath = provider.image_path;
  
  openProviderModal('edit', provider);
};

if (btnCloseProvider) {
  btnCloseProvider.addEventListener('click', closeProviderModal);
}

if (btnCancelProvider) {
  btnCancelProvider.addEventListener('click', closeProviderModal);
}

if (modalProvider) {
  modalProvider.addEventListener('click', (e) => {
    if (e.target === modalProvider) {
      closeProviderModal();
    }
  });
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modalProvider && modalProvider.classList.contains('active')) {
    closeProviderModal();
  }
});

function openProviderModal(mode = 'create', providerData = null) {
  modalProviderMode = mode;
  
  if (modalProvider) {
    modalProvider.classList.add('active');
    modalProvider.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  if (modalTitleProvider && modalSubtitleProvider) {
    if (mode === 'edit') {
      modalTitleProvider.textContent = 'Editar Proveedor';
      modalSubtitleProvider.textContent = 'Modifica los datos del proveedor existente';

      if (imageInputProvider) {
        const hasExistingImage = providerData && providerData.image_path;
        const imageLabel = document.querySelector('label[for="providerImage"]');
        
        if (hasExistingImage) {
          imageInputProvider.removeAttribute('required');
          if (imageLabel) {
            imageLabel.innerHTML = 'Cambiar Imagen/Logo <span style="color: #64748b; font-weight: 400;">(opcional)</span>';
          }
        } else {
          imageInputProvider.setAttribute('required', 'required');
          if (imageLabel) {
            imageLabel.innerHTML = 'Imagen/Logo del Proveedor <span class="required">*</span>';
          }
        }
      }
    } else {
      modalTitleProvider.textContent = 'Crear Nuevo Proveedor';
      modalSubtitleProvider.textContent = 'Completa los campos para agregar un proveedor';
      
      if (imageInputProvider) {
        imageInputProvider.setAttribute('required', 'required');
        const imageLabel = document.querySelector('label[for="providerImage"]');
        if (imageLabel) {
          imageLabel.innerHTML = 'Imagen/Logo del Proveedor <span class="required">*</span>';
        }
      }
    }
  }
  
  if (btnSaveProvider) {
    if (mode === 'edit') {
      btnSaveProvider.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
        </svg>
        Actualizar Proveedor
      `;
    } else {
      btnSaveProvider.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
        </svg>
        Guardar Proveedor
      `;
    }
  }
  
  if (mode === 'edit' && providerData) {
    fillFormWithProviderData(providerData);
  }
}

function fillFormWithProviderData(provider) {
  const nameInput = document.getElementById('providerName');
  const websiteInput = document.getElementById('providerWebsite');
  
  if (nameInput) nameInput.value = provider.name || '';
  if (websiteInput) websiteInput.value = provider.website_url || '';
  
  if (toggleStatusProvider) {
    toggleStatusProvider.checked = Number(provider.status) === 1;
    if (statusTextProvider) {
      statusTextProvider.textContent = toggleStatusProvider.checked ? 'Activo' : 'Inactivo';
    }
  }
  
  if (provider.image_path && imagePreviewProvider && imagePreviewContainerProvider) {
    const imgSrc = `/assets/img/${provider.image_path}`;
    imagePreviewProvider.src = imgSrc;
    imagePreviewContainerProvider.style.display = 'block';
    existingProviderImagePath = provider.image_path;
  }
}

function closeProviderModal() {
  if (!modalProvider) return;
  
  modalProvider.classList.remove('active');
  modalProvider.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
  
  if (formProvider) formProvider.reset();
  if (imagePreviewContainerProvider) imagePreviewContainerProvider.style.display = 'none';
  hideProviderError();
  
  modalProviderMode = 'create';
  editingProviderId = null;
  existingProviderImagePath = null;
  
  if (btnSaveProvider) {
    btnSaveProvider.disabled = false;
    btnSaveProvider.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
      </svg>
      Guardar Proveedor
    `;
  }
  
  if (statusTextProvider) {
    statusTextProvider.textContent = 'Activo';
  }
  
  if (imageInputProvider) {
    imageInputProvider.setAttribute('required', 'required');
    const imageLabel = document.querySelector('label[for="providerImage"]');
    if (imageLabel) {
      imageLabel.innerHTML = 'Imagen/Logo del Proveedor <span class="required">*</span>';
    }
  }
}

if (imageInputProvider) {
  imageInputProvider.addEventListener('change', (e) => {
    const file = e.target.files[0];
    
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/avif', 'image/webp'];
      if (!allowedTypes.includes(file.type)) {
        showProviderError('Solo se permiten imágenes JPG, PNG, AVIF y WebP');
        imageInputProvider.value = '';
        return;
      }
      
      const maxSize = 5 * 1024 * 1024;
      if (file.size > maxSize) {
        showProviderError('La imagen no debe superar los 5MB');
        imageInputProvider.value = '';
        return;
      }
      
      const reader = new FileReader();
      reader.onload = (e) => {
        if (imagePreviewProvider) imagePreviewProvider.src = e.target.result;
        if (imagePreviewContainerProvider) imagePreviewContainerProvider.style.display = 'block';
      };
      reader.readAsDataURL(file);
      
      hideProviderError();
    }
  });
}

if (removePreviewBtnProvider) {
  removePreviewBtnProvider.addEventListener('click', () => {
    if (modalProviderMode === 'edit' && existingProviderImagePath && !imageInputProvider.files[0]) {
      showProviderError('El proveedor debe tener una imagen. Si desea cambiarla, suba una nueva.');
      return;
    }
    
    if (imageInputProvider) imageInputProvider.value = '';
    if (imagePreviewProvider) imagePreviewProvider.src = '';
    if (imagePreviewContainerProvider) imagePreviewContainerProvider.style.display = 'none';
    
    if (modalProviderMode === 'edit' && existingProviderImagePath) {
      const imgSrc = `/assets/img/${existingProviderImagePath}`;
      imagePreviewProvider.src = imgSrc;
      imagePreviewContainerProvider.style.display = 'block';
    }
  });
}

if (formProvider) {
  formProvider.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nameInput = document.getElementById('providerName');
    
    if (!nameInput || !imageInputProvider) {
      showProviderError('Error: no se encontraron los campos del formulario');
      return;
    }
    
    const name = nameInput.value.trim();
    const image = imageInputProvider.files[0];
    
    if (!name) {
      showProviderError('Por favor complete todos los campos requeridos');
      return;
    }
    
    if (modalProviderMode === 'create' && !image) {
      showProviderError('La imagen del proveedor es requerida');
      return;
    }
    
    if (modalProviderMode === 'edit' && !image && !existingProviderImagePath) {
      showProviderError('El proveedor debe tener una imagen');
      return;
    }
    
    if (btnSaveProvider) {
      btnSaveProvider.disabled = true;
      btnSaveProvider.innerHTML = `
        <svg class="spinner" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="30" stroke-dashoffset="0">
            <animateTransform attributeName="transform" type="rotate" from="0 8 8" to="360 8 8" dur="1s" repeatCount="indefinite"/>
          </circle>
        </svg>
        ${modalProviderMode === 'edit' ? 'Actualizando...' : 'Guardando...'}
      `;
    }
    
    try {
      const formData = new FormData(formProvider);

      if (modalProviderMode === 'edit') {
        formData.append('id_provider', editingProviderId);
      }
      
      const apiUrl = modalProviderMode === 'edit' ? '/api/update_providers.php' : '/api/create_provider.php';
      
      const response = await fetch(apiUrl, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        const successMessage = modalProviderMode === 'edit' 
          ? 'Proveedor actualizado exitosamente' 
          : 'Proveedor agregado exitosamente';
        
        // Cerrar modal primero
        closeProviderModal();
        
        // Mostrar toast después de cerrar
        setTimeout(() => {
          if (typeof showToast === 'function') {
            showToast(successMessage, 'success');
          }
        }, 100);
        
        // Recargar proveedores
        if (typeof loadProviders === 'function') await loadProviders();
        if (typeof renderPage === 'function') renderPage('proveedores', typeof PAGE !== 'undefined' ? PAGE : 1, false);
      } else {
        showProviderError(result.message || 'Error al guardar el proveedor');
        if (btnSaveProvider) {
          btnSaveProvider.disabled = false;
          const buttonText = modalProviderMode === 'edit' ? 'Actualizar Proveedor' : 'Guardar Proveedor';
          const buttonIcon = modalProviderMode === 'edit' 
            ? '<path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>'
            : '<path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>';
          btnSaveProvider.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              ${buttonIcon}
            </svg>
            ${buttonText}
          `;
        }
      }
    } catch (error) {
      console.error('Error:', error);
      showProviderError('Error al conectar con el servidor');
      if (btnSaveProvider) {
        btnSaveProvider.disabled = false;
        btnSaveProvider.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
          </svg>
          Guardar Proveedor
        `;
      }
    }
  });
}

function showProviderError(message) {
  if (formErrorProvider) {
    formErrorProvider.textContent = message;
    formErrorProvider.style.display = 'block';
    formErrorProvider.style.background = '#fee2e2';
    formErrorProvider.style.borderColor = '#fecaca';
    formErrorProvider.style.color = '#7f1d1d';
  }
}

function hideProviderError() {
  if (formErrorProvider) {
    formErrorProvider.style.display = 'none';
  }
}
