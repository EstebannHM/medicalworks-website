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
    updateProvidersKPI();
    setupDelegatedActions();
    setupSearchListener();
    setupStatusFilterListener();
    setupCategoryFilterListener();
    setupProviderFilterListener();
    renderPage(1, false);
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
    renderProviders(data.providers);
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
  renderPage(1, false); // Volver a la primera página y renderizar
}

function tableHTML(rows) {
  return `
    <table class="products-table" aria-label="Listado de productos">
      <thead>
        <tr>
          <th style="width:32px"><input type="checkbox" aria-label="Seleccionar todos"></th>
          <th>Producto</th>
          <th style="width:110px">SKU</th>
          <th style="width:140px">Categoría</th>
          <th style="width:200px">Proveedor</th>
          <th style="width:120px">Estado</th>
          <th style="width:120px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        ${rows.map(renderRow).join("")}
      </tbody>
    </table>
  `;
}

function renderRow(p) {
  const sku = p.sku || `MED-${String(p.id_product).padStart(3, "0")}`;
  const category =
    CATEGORY_MAP.get(Number(p.id_category)) || `#${p.id_category}`;
  const provider =
    PROVIDER_MAP.get(Number(p.id_provider)) || `#${p.id_provider}`;
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
            <button class="action-btn btn-toggle-status" data-action="toggle" title="${
              status === "Activo" ? "Inactivar" : "Activar"
            }" aria-label="${status === "Activo" ? "Inactivar" : "Activar"}">
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
}

function renderPage(page, shouldScroll = true) {
  PAGE = page;

  const start = (PAGE - 1) * ROWS_PER_PAGE;
  const end = start + ROWS_PER_PAGE;
  const rows = A_FILTERED.slice(start, end);

  const mount = document.getElementById("tableMount");
  if (!mount) return;

  mount.innerHTML = tableHTML(rows);

  const total = A_FILTERED.length;
  const info = document.getElementById("tablePageInfo");
  if (info)
    info.textContent = `${start + 1}-${Math.min(
      end,
      total
    )} de ${total} productos`;

  renderPagination();

  if (shouldScroll) {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

function renderPagination() {
  const totalPages = Math.ceil(A_FILTERED.length / ROWS_PER_PAGE) || 1;
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
    if (!Number.isNaN(go)) renderPage(go);
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

// Función para configurar el listener del filtro de categoría
function setupCategoryFilterListener() {
  const dropdownToggle = document.getElementById("categoryDropdown");
  const dropdownMenu = document.getElementById("categoryDropdownMenu");
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
    const catId =
      item.getAttribute("data-category-id") ||
      item.getAttribute("data-category");
    currentCategoryId = catId;
    let text = "Todas las categorías";
    if (catId !== "all") {
      text = item.getAttribute("data-category") || text;
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

// Función para configurar el listener del filtro de proveedor
function setupProviderFilterListener() {
  const dropdownToggle = document.getElementById("providerDropdown");
  const dropdownMenu = document.getElementById("providerDropdownMenu");
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
    const provId =
      item.getAttribute("data-provider-id") ||
      item.getAttribute("data-provider");
    currentProviderId = provId;
    let text = "Todos los proveedores";
    if (provId !== "all") {
      text = item.getAttribute("data-provider") || text;
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

// Función para configurar el listener del filtro de estado
function setupStatusFilterListener() {
  const dropdownToggle = document.getElementById("statusDropdown");
  const dropdownMenu = document.getElementById("statusDropdownMenu");
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
    const status = item.getAttribute("data-status");
    currentStatusFilter = status;
    let text = "Todos los estados";
    if (status === "active") text = "Solo activos";
    else if (status === "inactive") text = "Solo inactivos";
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

// Delega eventos (agregar y editar) sobre el contenedor de la tabla
function setupDelegatedActions() {
  const mount = document.getElementById("tableMount");
  if (!mount) return;

  mount.addEventListener("click", async (e) => {
    const btn = e.target.closest("button.action-btn");
    if (!btn) return;
    const row = btn.closest("tr");
    if (!row) return;
    const productId = Number(row.getAttribute("data-product-id"));
    if (!productId) return;

    // Toggle status
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
        // Recarga productos desde el servidor para obtener datos actualizados
        await loadProducts();
        applySearchAndStatusFilter();
      } catch (error) {
        alert("Error al cambiar el estado del producto: " + error.message);
        btn.disabled = false;
      }
    } else if (btn.classList.contains("btn-edit")) {
      e.preventDefault();
      // Buscar el producto completo en el array
      const product = A_PRODUCTS.find(
        (p) => Number(p.id_product) === productId
      );
      if (!product) {
        console.error("Producto no encontrado:", productId);
        return;
      }

      // Abre el modal en modo editar
      if (typeof window.openEditProductModal === "function") {
        window.openEditProductModal(product);
      } else {
        console.error("La función openEditProductModal no está disponible");
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

// Actualiza el KPI de proveedores usando el tamaño del PROVIDER_MAP
function updateProvidersKPI() {
  const label = document.getElementById("kpi-proveedores-label");
  if (!label) return;
  const card = label.closest(".kpi-card");
  if (!card) return;
  const valueEl = card.querySelector("#kpiProvidersValue");
  if (!valueEl) return;
  valueEl.textContent = String(PROVIDER_MAP.size);
}
