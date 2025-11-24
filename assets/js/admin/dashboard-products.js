/**
 * Dashboard Admin - Gestión de Productos
 * Carga, filtrado, búsqueda y acciones sobre productos
 */

/**
 * Carga productos desde la API y actualiza el KPI
 */
async function loadProducts() {
  const res = await fetch("/api/all_products_admin.php");
  const data = await res.json();
  if (!data.success) throw new Error("No se pudieron cargar productos");
  A_PRODUCTS = data.products;
  A_FILTERED = [...A_PRODUCTS];

  updateProductsKPIFromAPI(data.stats);
}

/**
 * Renderiza botones de categorías en dropdown
 */
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

/**
 * Renderiza botones de proveedores en dropdown
 */
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

/**
 * Aplica filtros de búsqueda, estado, categoría y proveedor a productos
 */
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
  renderPage('productos', 1, false);
}

/**
 * Configura listener para búsqueda de productos
 */
function setupSearchListener() {
  const searchInput = document.getElementById("productSearch");
  if (!searchInput) return;

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      applySearchAndStatusFilter();
    }
  });

  searchInput.setAttribute("enterkeyhint", "search");
}

/**
 * Configura listener para filtro de categoría
 */
function setupCategoryFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "categoryDropdown",
    dropdownMenuId: "categoryDropdownMenu",
    getValue: (item) => item.getAttribute("data-category-id") || item.getAttribute("data-category"),
    getText: (item) => item.getAttribute("data-category"),
    setFilter: (val) => { 
      currentCategoryId = val; 
      applySearchAndStatusFilter();
    },
    defaultText: "Todas las categorías"
  });
}

/**
 * Configura listener para filtro de proveedor
 */
function setupProviderFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "providerDropdown",
    dropdownMenuId: "providerDropdownMenu",
    getValue: (item) => item.getAttribute("data-provider-id") || item.getAttribute("data-provider"),
    getText: (item) => item.getAttribute("data-provider"),
    setFilter: (val) => { 
      currentProviderId = val; 
      applySearchAndStatusFilter();
    },
    defaultText: "Todos los proveedores"
  });
}

/**
 * Configura listener para filtro de estado
 */
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
    setFilter: (val) => { 
      currentStatusFilter = val; 
      applySearchAndStatusFilter();
    },
    defaultText: "Todos los estados"
  });
}

/**
 * Configura acciones delegadas para botones en la tabla de productos
 */
function setupDelegatedActions() {
  const mount = document.getElementById("tableMount");
  if (!mount) return;

  mount.addEventListener("click", async (e) => {
    const btn = e.target.closest("button.action-btn");
    if (!btn) return;
    const row = btn.closest("tr");
    if (!row) return;

    // Solo manejar productos aquí
    const productId = Number(row.getAttribute("data-product-id"));
    if (!productId) return;

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
  });
}

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

function updateProductsKPIFromAPI(stats) {
  const label = document.getElementById("kpi-productos-label");
  if (!label) return;

  const card = label.closest(".kpi-card");
  if (!card) return;

  const valueEl = card.querySelector(".kpi-value");
  if (!valueEl) return;

  valueEl.textContent = String(stats.active);
}
