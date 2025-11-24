/**
 * Dashboard Admin - Tabla de Productos
 * - Consume /api/all_products_admin.php, /api/categories.php, /api/providers.php
 * - Renderiza tabla con paginación similar a catalog.js
 * - Incluye funcionalidad de editar productos
 */

let A_PRODUCTS = [];
let A_FILTERED = [];
let PAGE = 1;
const ROWS_PER_PAGE = 10;

let CATEGORY_MAP = new Map(); // id_category -> nombre
let PROVIDER_MAP = new Map(); // id_provider -> nombre
let A_CATEGORIES = [];
let A_PROVIDERS = [];
let currentSearchTerm = ""; // Variable para almacenar el término de búsqueda actual
let currentStatusFilter = "all";
let currentCategoryId = "all";
let currentProviderId = "all";

// Utilidad simple para evitar inyecciones en nombres
function esc(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

document.addEventListener("DOMContentLoaded", async () => {
  try {
    await Promise.all([loadCategoriesAdmin(), loadProviders()]);
    await loadProducts();
    setupDelegatedActions();
    setupSearchListener();
    setupStatusFilterListener();
    setupCategoryFilterListener();
    setupProviderFilterListener();
    setupMenuSectionListener(); // Agregado
    renderPage('productos', 1, false);
  } catch (e) {
    console.error(e);
   
  }
});

// Carga productos desde la API y actualiza el KPI
async function loadProducts() {
  const res = await fetch("/api/all_products_admin.php");
  const data = await res.json();
  if (!data.success) throw new Error("No se pudieron cargar productos");
  A_PRODUCTS = data.products;
  A_FILTERED = [...A_PRODUCTS];

  updateProductsKPIFromAPI(data.stats); // Actualizar KPI usando las estadísticas de la API
}

// Carga categorías y las renderiza en el dropdown
async function loadCategoriesAdmin() {
  const res = await fetch("/api/categories_admin.php");
  const data = await res.json();
  if (data.success && Array.isArray(data.categories)) {
    CATEGORY_MAP = new Map(
      data.categories.map((c) => [Number(c.id_category), c.name])
    );
    A_CATEGORIES = data.categories;
    renderCategories(data.categories);
  }
}
// Carga proveedores y los almacena en PROVIDER_MAP
async function loadProviders() {
  const res = await fetch("/api/providers_admin.php");
  const data = await res.json();
  if (data.success && Array.isArray(data.providers)) {
    PROVIDER_MAP = new Map(
      data.providers.map((p) => [Number(p.id_provider), p.name])
    );
    A_PROVIDERS = data.providers;
  renderProviders(data.providers);
    if (data.stats) {
      updateProvidersKPIFromAPI(data.stats);
    }
  }
}

// Aplicar filtro de búsqueda y estado
function applySearchAndStatusFilter() {
  const searchInput = document.getElementById("productSearch");
  if (!searchInput) return;

  currentSearchTerm = searchInput.value.trim();
  let filtered = [...A_PRODUCTS];

  // Filtrar por estado
  if (currentStatusFilter === "active") {
    filtered = filtered.filter((product) => Number(product.status) === 1);
  } else if (currentStatusFilter === "inactive") {
    filtered = filtered.filter((product) => Number(product.status) === 0);
  }

  // Filtrar por categoría
  if (currentCategoryId !== "all") {
    filtered = filtered.filter(
      (product) => Number(product.id_category) === Number(currentCategoryId)
    );
  }

  // Filtrar por proveedor
  if (currentProviderId !== "all") {
    filtered = filtered.filter(
      (product) => Number(product.id_provider) === Number(currentProviderId)
    );
  }

  // Filtrar por búsqueda si hay término
  if (currentSearchTerm !== "") {
    const normalizeText = (text) =>
      String(text)
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const normalizedSearch = normalizeText(currentSearchTerm);

    filtered = filtered.filter((product) => {
      const sku = product.sku || "";
      const name = product.name || "";
      const description = product.description || "";

      return (
        normalizeText(name).includes(normalizedSearch) ||
        normalizeText(description).includes(normalizedSearch) ||
        normalizeText(sku).includes(normalizedSearch) ||
        String(product.id_product).includes(currentSearchTerm)
      );
    });
  }

  A_FILTERED = filtered;
  renderPage('productos', 1, false); // Volver a la primera página y renderizar
}

// Tabla genérica para productos, categorías y proveedores
function tableHTML(rows, tipo) {
  let thead = '';
  if (tipo === 'productos') {
    thead = `<tr>
      <th style="width:32px"><input type="checkbox" aria-label="Seleccionar todos"></th>
      <th>Producto</th>
      <th style="width:110px">SKU</th>
      <th style="width:140px">Categoría</th>
      <th style="width:200px">Proveedor</th>
      <th style="width:120px">Estado</th>
      <th style="width:120px">Acciones</th>
    </tr>`;
  } else if (tipo === 'categorias') {
    thead = `<tr><th>Nombre de la categoría</th></tr>`;
  } else if (tipo === 'proveedores') {
    thead = `<tr><th>Nombre del proveedor</th><th>Estado</th><th>Acciones</th></tr>`;
  }
  return `
    <table class="products-table" aria-label="Listado de ${tipo}">
      <thead>${thead}</thead>
      <tbody>
        ${rows.map(row => renderRow(row, tipo)).join("")}
      </tbody>
    </table>
  `;
}

function renderRow(p, tipo) {
  if (tipo === 'productos') {
    const sku = p.sku || `MED-${String(p.id_product).padStart(3, "0")}`;
    const category = CATEGORY_MAP.get(Number(p.id_category)) || `#${p.id_category}`;
    const provider = PROVIDER_MAP.get(Number(p.id_provider)) || `#${p.id_provider}`;
    const status = Number(p.status) === 1 ? "Activo" : "Inactivo";
    return `
      <tr data-product-id="${p.id_product}">
        <td><input type="checkbox" aria-label="Seleccionar"></td>
        <td>
          <div class="prod-cell">
            <div class="prod-icon" aria-hidden="true">
              <svg width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
              </svg>
            </div>
            <div class="prod-meta">
              <div class="prod-name">${esc(p.name)}</div>
            </div>
          </div>
        </td>
        <td><span class="badge badge-id">${esc(sku)}</span></td>
        <td><span class="badge badge-cat">${esc(category)}</span></td>
        <td><span class="text-provider">${esc(provider)}</span></td>
        <td>
          <span class="status ${status === "Activo" ? "ok" : "off"}">
            <span class="dot"></span>${status}
          </span>
        </td>
        <td>
          <div class="row-actions">
              <button class="action-btn btn-toggle-status" data-action="toggle" title="${status === "Activo" ? "Inactivar" : "Activar"}" aria-label="${status === "Activo" ? "Inactivar" : "Activar"}">
                <svg width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                  <path d="M7.5 1v7h1V1z"/>
                  <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"/>
                </svg>
            </button>
            <button class="action-btn btn-edit" data-action="edit" title="Editar" aria-label="Editar">
              <svg width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    `;
  } else if (tipo === 'categorias') {
    return `
      <tr>
        <td>
          <div class="prod-cell">
            <div class="prod-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/>
                <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"/>
              </svg>
            </div>
            <div class="prod-meta">
              <div class="prod-name">${esc(p.name || p.nombre)}</div>
            </div>
          </div>
        </td>
      </tr>
    `;
  } else if (tipo === 'proveedores') {
    const status = Number(p.status) === 1 ? 'Activo' : 'Inactivo';
    return `
      <tr>
        <td>
          <div class="prod-cell">
            <div class="prod-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
              </svg>
            </div>
            <div class="prod-meta">
              <div class="prod-name">${esc(p.name)}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="status ${status === 'Activo' ? 'ok' : 'off'}">
            <span class="dot"></span>${status}
          </span>
        </td>
        <td>
          <div class="row-actions">
            <button class="action-btn btn-toggle-provider-status" data-action="toggle-provider" data-provider-id="${p.id_provider || ''}" title="${status === 'Activo' ? 'Inactivar' : 'Activar'}" aria-label="${status === 'Activo' ? 'Inactivar' : 'Activar'}">
              <svg width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                <path d="M7.5 1v7h1V1z"/>
                <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    `;
  }
  return '';
}


function renderPage(tipo, page = 1, shouldScroll = true) {
  PAGE = page;
  let datos, label;

  if (tipo === 'productos') {
    datos = A_FILTERED;
    label = 'productos';
  } else if (tipo === 'categorias') {
    datos = A_CATEGORIES;
    label = 'categorías';
  } else if (tipo === 'proveedores') {
    datos = A_PROVIDERS;
    label = 'proveedores';
  }

  const start = (PAGE - 1) * ROWS_PER_PAGE;
  const end = start + ROWS_PER_PAGE;
  const rows = datos.slice(start, end);

  const mount = document.getElementById("tableMount");
  if (!mount) return;
  mount.innerHTML = tableHTML(rows, tipo);

  const info = document.getElementById("tablePageInfo");
  if (info)
    info.textContent = `${start + 1}-${Math.min(end, datos.length)} de ${datos.length} ${label}`;

  renderPagination(tipo, datos);

  if (shouldScroll) {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

function renderPagination(tipo, datos) {
  const totalPages = Math.ceil(datos.length / ROWS_PER_PAGE) || 1;
  const cont = document.getElementById("tablePagination");
  if (!cont) return;

  let btns = [];
  if (PAGE > 1)
    btns.push(`<button class="page-btn" data-go="${PAGE - 1}">‹</button>`);
  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= PAGE - 1 && i <= PAGE + 1)) {
      const active = i === PAGE ? "active" : "";
      btns.push(
        `<button class="page-btn ${active}" data-go="${i}">${i}</button>`
      );
    } else if (i === PAGE - 2 || i === PAGE + 2) {
      btns.push(`<span class="page-dots">...</span>`);
    }
  }
  if (PAGE < totalPages)
    btns.push(`<button class="page-btn" data-go="${PAGE + 1}">›</button>`);

  cont.innerHTML = btns.join("");
  cont.onclick = (e) => {
    const b = e.target.closest("button.page-btn");
    if (!b) return;
    const go = Number(b.getAttribute("data-go"));
    if (!Number.isNaN(go)) renderPage(tipo, go);
  };
}



// Renderizar botones de categorías con data-category-id
function renderCategories(categories) {
  const container = document.getElementById("categoriesContainer");
  if (!container) return;
  container.innerHTML = categories
    .map(
      (c) => `
    <button class="dropdown-item" data-category-id="${
      c.id_category
    }" data-category="${esc(c.name)}">
      ${esc(c.name)}
    </button>
  `
    )
    .join("");
}

// Renderizar botones de proveedores con data-provider-id
function renderProviders(providers) {
  const container = document.getElementById("providersContainer");
  if (!container) return;
  container.innerHTML = providers
    .map(
      (p) => `
    <button class="dropdown-item" data-provider-id="${
      p.id_provider
    }" data-provider="${esc(p.name)}">
      ${esc(p.name)}
    </button>
  `
    )
    .join("");
}


// Listener genérico para dropdowns de filtro
function setupDropdownFilterListener({
  dropdownToggleId,
  dropdownMenuId,
  getValue,
  getText,
  setFilter,
  defaultText
}) {
  const dropdownToggle = document.getElementById(dropdownToggleId);
  const dropdownMenu = document.getElementById(dropdownMenuId);
  if (!dropdownToggle || !dropdownMenu) return;

  // Mostrar/ocultar el menú
  dropdownToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    dropdownMenu.classList.toggle("show");
  });

  // Selección de opción
  dropdownMenu.addEventListener("click", (e) => {
    const item = e.target.closest(".dropdown-item");
    if (!item) return;
    // Remover clase active de todos
    dropdownMenu
      .querySelectorAll(".dropdown-item")
      .forEach((btn) => btn.classList.remove("active"));
    item.classList.add("active");
    // Actualizar filtro y texto
    const value = getValue(item);
    setFilter(value);
    let text = defaultText;
    if (value !== "all") {
      text = getText(item) || text;
    }
    dropdownToggle.querySelector(".dropdown-text").textContent = text;
    dropdownMenu.classList.remove("show");
    applySearchAndStatusFilter();
  });

  // Cerrar menú al hacer click fuera
  document.addEventListener("click", (e) => {
    if (
      !dropdownMenu.contains(e.target) &&
      !dropdownToggle.contains(e.target)
    ) {
      dropdownMenu.classList.remove("show");
    }
  });
}

// Configurar listener para filtro de categoría
function setupCategoryFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "categoryDropdown",
    dropdownMenuId: "categoryDropdownMenu",
    getValue: (item) => item.getAttribute("data-category-id") || item.getAttribute("data-category"),
    getText: (item) => item.getAttribute("data-category"),
    setFilter: (val) => { currentCategoryId = val; },
    defaultText: "Todas las categorías"
  });
}
// Configurar listener para filtro de proveedor
function setupProviderFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "providerDropdown",
    dropdownMenuId: "providerDropdownMenu",
    getValue: (item) => item.getAttribute("data-provider-id") || item.getAttribute("data-provider"),
    getText: (item) => item.getAttribute("data-provider"),
    setFilter: (val) => { currentProviderId = val; },
    defaultText: "Todos los proveedores"
  });
}
// Configurar listener para filtro de estado
function setupStatusFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "statusDropdown",
    dropdownMenuId: "statusDropdownMenu",
    getValue: (item) => item.getAttribute("data-status"),
    getText: (item) => {
      const status = item.getAttribute("data-status");
      if (status === "active") return "Solo activos";
      if (status === "inactive") return "Solo inactivos";
      return "Todos los estados";
    },
    setFilter: (val) => { currentStatusFilter = val; },
    defaultText: "Todos los estados"
  });
}

// Configurar listener para la barra de búsqueda
function setupSearchListener() {
  const searchInput = document.getElementById("productSearch");
  if (!searchInput) return;

  // Búsqueda solo al presionar Enter
  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      applySearchAndStatusFilter();
    }
  });

  searchInput.setAttribute("enterkeyhint", "search");
}

// Configurar acciones delegadas para botones en la tabla
function setupDelegatedActions() {
  const mount = document.getElementById("tableMount");
  if (!mount) return;

  mount.addEventListener("click", async (e) => {
    const btn = e.target.closest("button.action-btn");
    if (!btn) return;
    const row = btn.closest("tr");
    if (!row) return;

    // Productos
    const productId = Number(row.getAttribute("data-product-id"));
    if (productId) {
      if (btn.classList.contains("btn-toggle-status")) {
        e.preventDefault();
        const product = A_PRODUCTS.find(
          (p) => Number(p.id_product) === productId
        );
        if (!product) return;
        const newStatus = Number(product.status) === 1 ? 0 : 1;
        btn.disabled = true;
        try {
          await updateProductStatus(productId, newStatus);
          await loadProducts();
          applySearchAndStatusFilter();
        } catch (error) {
          alert("Error al cambiar el estado del producto: " + error.message);
          btn.disabled = false;
        }
      } else if (btn.classList.contains("btn-edit")) {
        e.preventDefault();
        const product = A_PRODUCTS.find(
          (p) => Number(p.id_product) === productId
        );
        if (!product) {
          console.error("Producto no encontrado:", productId);
          return;
        }
        if (typeof window.openEditProductModal === "function") {
          window.openEditProductModal(product);
        } else {
          console.error("La función openEditProductModal no está disponible");
        }
      }
      return;
    }

    // Proveedores
    const providerId = Number(btn.getAttribute("data-provider-id"));
    if (providerId && btn.classList.contains("btn-toggle-provider-status")) {
      e.preventDefault();
      // Obtener el status actual
      const statusCell = row.querySelector(".status");
      const statusText = statusCell ? statusCell.textContent.trim() : "";
      const currentStatus = statusText === "Activo" ? 1 : 0;
      const newStatus = currentStatus === 1 ? 0 : 1;
      btn.disabled = true;
      try {
        await updateProviderStatus(providerId, newStatus);
        await loadProviders();
        renderPage('proveedores', PAGE, false);
      } catch (error) {
        alert("Error al cambiar el estado del proveedor: " + error.message);
        btn.disabled = false;
      }
    }
  });
}

// Función para actualizar el status de un producto
async function updateProductStatus(idProduct, newStatus) {
  try {
    const res = await fetch("/api/update_product_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_product: idProduct,
        status: newStatus,
      }),
    });

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.message || "Error al actualizar el status");
    }

    return data;
  } catch (error) {
    console.error("Error:", error);
    throw error;
  }
}

// Función para actualizar el status de un proveedor
async function updateProviderStatus(idProvider, newStatus) {
  try {
    const res = await fetch("/api/update_providers_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_provider: idProvider,
        status: newStatus,
      }),
    });

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.message || "Error al actualizar el status del proveedor");
    }

    return data;
  } catch (error) {
    console.error("Error:", error);
    throw error;
  }
}

// Actualiza el KPI de productos usando las estadísticas de la API
function updateProductsKPIFromAPI(stats) {
  const label = document.getElementById("kpi-productos-label");
  if (!label) return;

  const card = label.closest(".kpi-card");
  if (!card) return;

  const valueEl = card.querySelector(".kpi-value");
  if (!valueEl) return;

  valueEl.textContent = String(stats.active); // Mostrar solo productos activos usando las estadísticas de la API
}

// Actualiza el KPI de proveedores usando las estadísticas de la API
function updateProvidersKPIFromAPI(stats) {
  const label = document.getElementById("kpi-proveedores-label");
  if (!label) return;

  const card = label.closest(".kpi-card");
  if (!card) return;

  const valueEl = card.querySelector("#kpiProvidersValue");
  if (!valueEl) return;

  valueEl.textContent = String(stats.active); // Mostrar solo proveedores activos usando las estadísticas de la API
}

// Listener para el menú lateral y renderizado de la tabla correspondiente
function setupMenuSectionListener() {
  const menuItems = document.querySelectorAll('.menu-item[data-section]');
  menuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const section = item.getAttribute('data-section');
      // Quitar clase active de todos y poner solo al seleccionado
      menuItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      // Mostrar/ocultar filtros según la sección
      const toolbar = document.querySelector('.products-toolbar');
      if (section === 'productos') {
        if (toolbar) toolbar.style.display = '';
        renderPage('productos', 1, false);
      } else if (section === 'categorias') {
        if (toolbar) toolbar.style.display = 'none';
        renderPage('categorias', 1, false);
      } else if (section === 'proveedores') {
        if (toolbar) toolbar.style.display = 'none';
        renderPage('proveedores', 1, false);
      }
    });
  });
}

