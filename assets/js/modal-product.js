// Variables del modal
const modal = document.getElementById('modalAddProduct');
const form = document.getElementById('formAddProduct');
const btnOpen = document.getElementById('btnCreateProduct');
const btnClose = document.querySelector('.modal-close');
const btnCancel = document.getElementById('btnCancelProduct');
const btnSave = document.getElementById('btnSaveProduct');
const btnPreview = document.getElementById('btnPreview');
const imageInput = document.getElementById('productImage');
const imagePreview = document.getElementById('imagePreview');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const removePreviewBtn = document.querySelector('.remove-preview');
const formError = document.getElementById('formError');
const toggleStatus = document.getElementById('productStatus');
const statusText = document.querySelector('.status-text');

// Cambiar texto del estado
if (toggleStatus && statusText) {
  toggleStatus.addEventListener('change', () => {
    statusText.textContent = toggleStatus.checked ? 'Activo' : 'Inactivo';
  });
}

// Abre el modal
if (btnOpen) {
  btnOpen.addEventListener('click', () => {
    openModal();
  });
}

// Cierra el modal
if (btnClose) {
  btnClose.addEventListener('click', closeModal);
}

if (btnCancel) {
  btnCancel.addEventListener('click', closeModal);
}

// Cierra al hacer click fuera del modal
if (modal) {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });
}

// Cierra con tecla ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modal.classList.contains('active')) {
    closeModal();
  }
});

function openModal() {
  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
  
  // Carga categorías y proveedores
  loadCategoriesForModal();
  loadProvidersForModal();
}

function closeModal() {
  modal.classList.remove('active');
  modal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
  
  // Reseteo del form
  form.reset();
  imagePreviewContainer.style.display = 'none';
  hideError();
  
  // Reseteo botón de guardar (esto porque cuando uno crea el primer producto, el segundo producto a la hora de guardarlo, el botón quedaba pegado)
  if (btnSave) {
    btnSave.disabled = false;
    btnSave.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
      </svg>
      Guardar Producto
    `;
  }
}

// Carga categorías
async function loadCategoriesForModal() {
  try {
    const res = await fetch('/api/categories.php');
    const data = await res.json();
    
    if (data.success && Array.isArray(data.categories)) {
      const select = document.getElementById('productCategory');
      select.innerHTML = '<option value="">Seleccionar categoría</option>';
      
      data.categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id_category;
        option.textContent = cat.name;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error('Error al cargar categorías:', error);
  }
}

// Carga proveedores
async function loadProvidersForModal() {
  try {
    const res = await fetch('/api/providers.php');
    const data = await res.json();
    
    if (data.success && Array.isArray(data.providers)) {
      const select = document.getElementById('productProvider');
      select.innerHTML = '<option value="">Seleccionar proveedor</option>';
      
      data.providers.forEach(prov => {
        const option = document.createElement('option');
        option.value = prov.id_provider;
        option.textContent = prov.name;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error('Error al cargar proveedores:', error);
  }
}

// Preview de imagen
if (imageInput) {
  imageInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    
    if (file) {
      // Validacion del tipo de archivo
      const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!validTypes.includes(file.type)) {
        showError('Por favor seleccione una imagen JPG o PNG');
        imageInput.value = '';
        return;
      }
      
      // Validacion del tamaño de la imagen (5MB)
      const maxSize = 5 * 1024 * 1024;
      if (file.size > maxSize) {
        showError('La imagen no debe superar los 5MB');
        imageInput.value = '';
        return;
      }
      
      // Mostrar preview
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreviewContainer.style.display = 'block';
      };
      reader.readAsDataURL(file);
      
      hideError();
    }
  });
}

// Remueve el preview
if (removePreviewBtn) {
  removePreviewBtn.addEventListener('click', () => {
    imageInput.value = '';
    imagePreview.src = '';
    imagePreviewContainer.style.display = 'none';
  });
}

// Botón de previsualizar (provisional)
if (btnPreview) {
  btnPreview.addEventListener('click', () => {
    const formData = new FormData(form);
    const data = {
      nombre: formData.get('name'),
      sku: formData.get('sku'),
      categoria: document.getElementById('productCategory').selectedOptions[0]?.text || '',
      proveedor: document.getElementById('productProvider').selectedOptions[0]?.text || '',
      descripcion: formData.get('description'),
      status: formData.get('status') ? 'Activo' : 'Inactivo',
      imagen: imageInput.files[0] ? imageInput.files[0].name : 'Sin imagen'
    };
    
    const preview = `
      Producto: ${data.nombre}
      SKU: ${data.sku}
      Categoría: ${data.categoria}
      Proveedor: ${data.proveedor}
      Descripción: ${data.descripcion}
      Estado: ${data.status}
      Imagen: ${data.imagen}
    `;
    
    alert(preview.trim());
  });
}

// Envío del formulario
if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Validacion de los campos requeridos
    const name = document.getElementById('productTitle').value.trim();
    const sku = document.getElementById('productSku').value.trim();
    const category = document.getElementById('productCategory').value;
    const provider = document.getElementById('productProvider').value;
    const description = document.getElementById('productDescription').value.trim();
    const image = imageInput.files[0];
    
    if (!name || !sku || !category || !provider || !description || !image) {
      showError('Por favor complete todos los campos requeridos');
      return;
    }
    
    // Deshabilita botón
    btnSave.disabled = true;
    btnSave.innerHTML = `
      <svg class="spinner" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="30" stroke-dashoffset="0">
          <animateTransform attributeName="transform" type="rotate" from="0 8 8" to="360 8 8" dur="1s" repeatCount="indefinite"/>
        </circle>
      </svg>
      Guardando...
    `;
    
    try {
      // Crear FormData
      const formData = new FormData(form);
      
      // Enviar a la API
      const response = await fetch('/api/create_product.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {

        showSuccess('Producto agregado exitosamente');
        
        // Recarga productos
        setTimeout(async () => {
          await loadProducts();
          updateProductsKPI();
          renderPage(1, false);
          closeModal();
        }, 1000);
      } else {
        showError(result.message || 'Error al guardar el producto');
        btnSave.disabled = false;
        btnSave.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
          </svg>
          Guardar Producto
        `;
      }
    } catch (error) {
      console.error('Error:', error);
      showError('Error al conectar con el servidor');
      btnSave.disabled = false;
      btnSave.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
        </svg>
        Guardar Producto
      `;
    }
  });
}

function showError(message) {
  if (formError) {
    formError.textContent = message;
    formError.style.display = 'block';
  }
}

function hideError() {
  if (formError) {
    formError.style.display = 'none';
  }
}

function showSuccess(message) {
  if (formError) {
    formError.style.display = 'block';
    formError.style.background = '#d1fae5';
    formError.style.borderColor = '#6ee7b7';
    formError.style.color = '#065f46';
    formError.textContent = message;
  }
}