// Variables del modal
const modal = document.getElementById("modalAddProduct");
const form = document.getElementById("formAddProduct");
const btnOpen = document.getElementById("btnCreateProduct");
const btnClose = document.querySelector(".modal-close");
const btnCancel = document.getElementById("btnCancelProduct");
const btnSave = document.getElementById("btnSaveProduct");
const imageInput = document.getElementById("productImage");
const imagePreview = document.getElementById("imagePreview");
const imagePreviewContainer = document.getElementById("imagePreviewContainer");
const removePreviewBtn = document.querySelector(".remove-preview");
const formError = document.getElementById("formError");
const toggleStatus = document.getElementById("productStatus");
const statusText = document.querySelector(".status-text");
const modalTitle = document.getElementById("modalTitle");
const modalSubtitle = document.getElementById("modalSubtitle");

// Variables para ficha técnica (RF-039)
const datasheetInput = document.getElementById("productPdf");
const datasheetPreviewContainer = document.getElementById(
  "pdfPreviewContainer"
);
const datasheetFileName = document.getElementById("pdfFileName");
const removeDatasheetBtn = document.getElementById("removePdf");

// Variable para rastrear el modo del modal (crear o editar)
let modalMode = "create"; // 'create' o 'edit'
let editingProductId = null;
let existingImagePath = null;
let existingDatasheetPath = null; // RF-039

// Cambiar texto del estado
if (toggleStatus && statusText) {
  toggleStatus.addEventListener("change", () => {
    statusText.textContent = toggleStatus.checked ? "Activo" : "Inactivo";
  });
}

// Abre el modal en modo crear
if (btnOpen) {
  btnOpen.addEventListener("click", () => {
    openModal("create");
  });
}

// Función para abrir el modal en modo editar (será llamada desde dashboard.js)
window.openEditProductModal = function (product) {
  modalMode = "edit";
  editingProductId = product.id_product;
  existingImagePath = product.image_path;
  existingDatasheetPath = product.pdf_path || null; // RF-039

  openModal("edit", product);
};

// Cierra el modal
if (btnClose) {
  btnClose.addEventListener("click", closeModal);
}

if (btnCancel) {
  btnCancel.addEventListener("click", closeModal);
}

// Cierra al hacer click fuera del modal
if (modal) {
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });
}

// Cierra con tecla ESC
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && modal.classList.contains("active")) {
    closeModal();
  }
});

function openModal(mode = "create", productData = null) {
  modalMode = mode;

  modal.classList.add("active");
  modal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";

  // Actualiza título y subtítulo del modal
  if (modalTitle && modalSubtitle) {
    if (mode === "edit") {
      modalTitle.textContent = "Editar Producto";
      modalSubtitle.textContent = "Modifica los datos del producto existente";

      // Determina si la imagen es requerida según si el producto ya tiene una
      const hasExistingImage = productData && productData.image_path;

      if (imageInput) {
        const imageLabel = document.querySelector('label[for="productImage"]');

        if (hasExistingImage) {
          // Si ya tiene imagen, es opcional
          imageInput.removeAttribute("required");
          if (imageLabel) {
            imageLabel.innerHTML =
              'Cambiar Imagen <span style="color: #64748b; font-weight: 400;">(opcional)</span>';
          }
        } else {
          // Si NO tiene imagen, es requerida
          imageInput.setAttribute("required", "required");
          if (imageLabel) {
            imageLabel.innerHTML =
              'Imagen del Producto <span class="required">*</span>';
          }
        }
      }
    } else {
      modalTitle.textContent = "Crear Nuevo Producto";
      modalSubtitle.textContent =
        "Completa los campos para agregar un producto";
      // Asegura que sea required en modo crear
      if (imageInput) {
        imageInput.setAttribute("required", "required");
        // Restaura el label con asterisco rojo
        const imageLabel = document.querySelector('label[for="productImage"]');
        if (imageLabel) {
          imageLabel.innerHTML =
            'Imagen del Producto <span class="required">*</span>';
        }
      }
    }
  }

  // Actualiza texto del botón
  if (btnSave) {
    if (mode === "edit") {
      btnSave.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
        </svg>
        Actualizar Producto
      `;
    } else {
      btnSave.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
        </svg>
        Guardar Producto
      `;
    }
  }

  // Carga categorías y proveedores
  const loadPromises = [
    loadCategoriesForModal(),
    loadProvidersForModal()
  ];

  // Si es modo editar, prellenar los campos DESPUÉS de cargar categorías y proveedores
  if (mode === "edit" && productData) {
    Promise.all(loadPromises).then(() => {
      fillFormWithProductData(productData);
    });
  }
}

function fillFormWithProductData(product) {
  // Rellena campos del formulario
  document.getElementById("productTitle").value = product.name || "";
  document.getElementById("productSku").value = product.sku || "";
  document.getElementById("productDescription").value =
    product.description || "";

  // Selecciona categoría
  const categorySelect = document.getElementById("productCategory");
  if (categorySelect && product.id_category) {
    categorySelect.value = product.id_category;
  }

  // Selecciona proveedor
  const providerSelect = document.getElementById("productProvider");
  if (providerSelect && product.id_provider) {
    providerSelect.value = product.id_provider;
  }

  // Establece estado
  const statusCheckbox = document.getElementById("productStatus");
  if (statusCheckbox) {
    statusCheckbox.checked = Number(product.status) === 1;
    if (statusText) {
      statusText.textContent = statusCheckbox.checked ? "Activo" : "Inactivo";
    }
  }

  // Mostrar imagen actual si existe
  if (product.image_path) {
    const imgSrc = `/assets/img/${product.image_path}`;
    imagePreview.src = imgSrc;
    imagePreviewContainer.style.display = "block";
    existingImagePath = product.image_path;
  }

  // RF-039: Mostrar ficha técnica actual si existe
  if (product.pdf_path && datasheetPreviewContainer && datasheetFileName) {
    const pdfName = product.pdf_path.split("/").pop();
    datasheetFileName.textContent = pdfName;
    datasheetPreviewContainer.style.display = "flex";
    existingDatasheetPath = product.pdf_path;
  }
}

function closeModal() {
  modal.classList.remove("active");
  modal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";

  // Reseteo del form
  form.reset();
  imagePreviewContainer.style.display = "none";

  // RF-039: Resetear ficha técnica
  if (datasheetPreviewContainer) {
    datasheetPreviewContainer.style.display = "none";
  }
  if (datasheetFileName) {
    datasheetFileName.textContent = "";
  }

  hideError();

  // Resetear variables de modo
  modalMode = "create";
  editingProductId = null;
  existingImagePath = null;
  existingDatasheetPath = null; // RF-039

  // Reseteo botón de guardar
  if (btnSave) {
    btnSave.disabled = false;
    btnSave.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
      </svg>
      Guardar Producto
    `;
  }

  // Resetear estado
  if (statusText) {
    statusText.textContent = "Activo";
  }
}

// Carga categorías
async function loadCategoriesForModal() {
  try {
    const response = await fetch("/api/categories.php");
    const result = await response.json();

    if (result.success) {
      const select = document.getElementById("productCategory");
      if (select) {
        select.innerHTML = '<option value="">Seleccione una categoría</option>';
        result.categories.forEach((cat) => {
          select.innerHTML += `<option value="${cat.id_category}">${cat.name}</option>`;
        });
      }
    }
  } catch (error) {
    console.error("Error cargando categorías:", error);
  }
}

// Carga proveedores
async function loadProvidersForModal() {
  try {
    const response = await fetch("/api/providers.php");
    const result = await response.json();

    if (result.success) {
      const select = document.getElementById("productProvider");
      if (select) {
        select.innerHTML = '<option value="">Seleccione un proveedor</option>';
        result.providers.forEach((prov) => {
          select.innerHTML += `<option value="${prov.id_provider}">${prov.name}</option>`;
        });
      }
    }
  } catch (error) {
    console.error("Error cargando proveedores:", error);
  }
}

// Preview de imagen
if (imageInput) {
  imageInput.addEventListener("change", (e) => {
    const file = e.target.files[0];

    if (file) {
      // Validaciones en frontend
      const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
      if (!allowedTypes.includes(file.type)) {
        showError("Solo se permiten imágenes JPG y PNG");
        imageInput.value = "";
        return;
      }

      const maxSize = 5 * 1024 * 1024; // 5MB
      if (file.size > maxSize) {
        showError("La imagen no debe superar los 5MB");
        imageInput.value = "";
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreviewContainer.style.display = "block";
      };
      reader.readAsDataURL(file);

      hideError();
    }
  });
}

if (removePreviewBtn) {
  removePreviewBtn.addEventListener("click", () => {
    // Si estamos en modo editar y se quiere eliminar la imagen existente, no permitr eliminarla
    if (modalMode === "edit" && existingImagePath && !imageInput.files[0]) {
      showError(
        "El producto debe tener una imagen. Si desea cambiarla, suba una nueva."
      );
      return;
    }

    imageInput.value = "";
    imagePreview.src = "";
    imagePreviewContainer.style.display = "none";

    // Si eliminamos una  imagen seleccionada pero hay una existente, mostrar la nueva
    if (modalMode === "edit" && existingImagePath) {
      const imgSrc = `/assets/img/${existingImagePath}`;
      imagePreview.src = imgSrc;
      imagePreviewContainer.style.display = "block";
    }
  });
}

// Preview de ficha técnica (PDF)
if (datasheetInput) {
  datasheetInput.addEventListener("change", (e) => {
    const file = e.target.files[0];

    if (file) {
      // Validación de tipo PDF
      if (file.type !== "application/pdf") {
        showError("La ficha técnica debe ser un archivo PDF");
        datasheetInput.value = "";
        return;
      }

      // Validación de tamaño (10MB)
      const maxSize = 10 * 1024 * 1024;
      if (file.size > maxSize) {
        showError("La ficha técnica no debe superar los 10MB");
        datasheetInput.value = "";
        return;
      }

      // Muestra el preview con nombre del archivo
      if (datasheetFileName && datasheetPreviewContainer) {
        datasheetFileName.textContent = file.name;
        datasheetPreviewContainer.style.display = "flex";
      }

      hideError();
    }
  });
}

// Remover ficha técnica
if (removeDatasheetBtn) {
  removeDatasheetBtn.addEventListener("click", () => {
    datasheetInput.value = "";

    if (datasheetPreviewContainer) {
      datasheetPreviewContainer.style.display = "none";
    }
    if (datasheetFileName) {
      datasheetFileName.textContent = "";
    }

    // Si estamos en modo editar y había una ficha existente, la muestra de nuevo
    if (modalMode === "edit" && existingDatasheetPath) {
      const pdfName = existingDatasheetPath.split("/").pop();
      datasheetFileName.textContent = pdfName;
      datasheetPreviewContainer.style.display = "flex";
    }
  });
}

// Envío del formulario
if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Validacion de los campos requeridos
    const name = document.getElementById("productTitle").value.trim();
    const sku = document.getElementById("productSku").value.trim();
    const category = document.getElementById("productCategory").value;
    const provider = document.getElementById("productProvider").value;
    const description = document
      .getElementById("productDescription")
      .value.trim();
    const image = imageInput.files[0];

    if (!name || !sku || !category || !provider || !description) {
      showError("Por favor complete todos los campos requeridos");
      return;
    }

    // Valida la imagen: es requerida en modo crear O en modo editar si no hay imagen existente
    if (modalMode === "create" && !image) {
      showError("La imagen del producto es requerida");
      return;
    }

    if (modalMode === "edit" && !image && !existingImagePath) {
      showError("Debe subir una imagen para el producto");
      return;
    }

    // Deshabilita botón
    btnSave.disabled = true;
    btnSave.innerHTML = `
      <svg class="spinner" width="16" height="16" viewBox="0 0 16 16" fill="none">
        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="30" stroke-dashoffset="0">
          <animateTransform attributeName="transform" type="rotate" from="0 8 8" to="360 8 8" dur="1s" repeatCount="indefinite"/>
        </circle>
      </svg>
      ${modalMode === "edit" ? "Actualizando..." : "Guardando..."}
    `;

    try {
      // Crear FormData
      const formData = new FormData(form);

      // Si estamos en modo editar, agregar el ID del producto
      if (modalMode === "edit" && editingProductId) {
        formData.append("id_product", editingProductId);
      }

      // Determinar la URL del endpoint
      const apiUrl =
        modalMode === "edit"
          ? "/api/update_product.php"
          : "/api/create_product.php";

      // Enviar a la API
      const response = await fetch(apiUrl, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        const successMessage =
          modalMode === "edit"
            ? "Producto actualizado exitosamente"
            : "Producto agregado exitosamente";

        showSuccess(successMessage);

        // Recarga productos
        setTimeout(async () => {
          if (typeof loadProducts === 'function') {
            await loadProducts();
          }
          if (typeof renderPage === 'function') {
            renderPage('productos', 1, false);
          }
          closeModal();
        }, 1000);
      } else {
        showError(result.message || "Error al guardar el producto");
        btnSave.disabled = false;

        const btnText =
          modalMode === "edit" ? "Actualizar Producto" : "Guardar Producto";

        const btnIcon =
          modalMode === "edit"
            ? `<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
            </svg>`
            : `<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
            </svg>`;

        btnSave.innerHTML = `${btnIcon} ${btnText}`;
      }
    } catch (error) {
      console.error("Error:", error);
      showError("Error al conectar con el servidor");
      btnSave.disabled = false;

      const btnText =
        modalMode === "edit" ? "Actualizar Producto" : "Guardar Producto";

      const btnIcon =
        modalMode === "edit"
          ? `<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
          </svg>`
          : `<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
          </svg>`;

      btnSave.innerHTML = `${btnIcon} ${btnText}`;
    }
  });
}

function showError(message) {
  if (formError) {
    formError.textContent = message;
    formError.style.display = "block";
    formError.style.background = "#fee2e2";
    formError.style.borderColor = "#fecaca";
    formError.style.color = "#7f1d1d";
  }
}

function hideError() {
  if (formError) {
    formError.style.display = "none";
  }
}

function showSuccess(message) {
  if (formError) {
    formError.style.display = "block";
    formError.style.background = "#d1fae5";
    formError.style.borderColor = "#6ee7b7";
    formError.style.color = "#065f46";
    formError.textContent = message;
  }
}
