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

if (toggleStatusProvider && statusTextProvider) {
  toggleStatusProvider.addEventListener('change', () => {
    statusTextProvider.textContent = toggleStatusProvider.checked ? 'Activo' : 'Inactivo';
  });
}

if (btnOpenProvider) {
  btnOpenProvider.addEventListener('click', () => {
    openProviderModal();
  });
}

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

function openProviderModal() {
  if (modalProvider) {
    modalProvider.classList.add('active');
    modalProvider.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
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
      
      const maxSize = 5 * 1024 * 1024; // 5MB
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
    if (imageInputProvider) imageInputProvider.value = '';
    if (imagePreviewProvider) imagePreviewProvider.src = '';
    if (imagePreviewContainerProvider) imagePreviewContainerProvider.style.display = 'none';
  });
}


if (formProvider) {
  formProvider.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nameInput = document.getElementById('providerName');
    const descriptionInput = document.getElementById('providerDescription');
    
    if (!nameInput || !descriptionInput || !imageInputProvider) {
      showProviderError('Error: no se encontraron los campos del formulario');
      return;
    }
    
    const name = nameInput.value.trim();
    const description = descriptionInput.value.trim();
    const image = imageInputProvider.files[0];
    
    if (!name || !description) {
      showProviderError('Por favor complete todos los campos requeridos');
      return;
    }
    
    if (!image) {
      showProviderError('La imagen del proveedor es requerida');
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
        Guardando...
      `;
    }
    
    try {
      const formData = new FormData(formProvider);
      
      const response = await fetch('/api/create_provider.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        showProviderSuccess('Proveedor agregado exitosamente');
        
        setTimeout(async () => {
          if (typeof loadProviders === 'function') await loadProviders();
          if (typeof renderPage === 'function') renderPage('proveedores', typeof PAGE !== 'undefined' ? PAGE : 1, false);
          closeProviderModal();
        }, 1000);
      } else {
        showProviderError(result.message || 'Error al guardar el proveedor');
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

function showProviderSuccess(message) {
  if (formErrorProvider) {
    formErrorProvider.style.display = 'block';
    formErrorProvider.style.background = '#d1fae5';
    formErrorProvider.style.borderColor = '#6ee7b7';
    formErrorProvider.style.color = '#065f46';
    formErrorProvider.textContent = message;
  }
}