/**
 * Catálogo - Medical Works
 */

let allProducts = [];
let filteredProducts = []; // ARRAY PARA PRODUCTOS FILTRADOS
let currentPage = 1;
const PRODUCTS_PER_PAGE = 12;
let currentCategoryId = "all"; // CATEGORÍA ACTUAL
let currentProviderId = "all"; // PROVEEDOR ACTUAL

document.addEventListener("DOMContentLoaded", () => {
  loadProducts();
  loadCategories(); // carga categorías desde la API
  loadProviders(); // carga proveedores desde la API
  setupSearchListener(); // CARGAR EL LISTENER DE BÚSQUEDA
});

// Utilidad simple para evitar inyecciones en nombres
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

async function loadProducts() {
  try {
    const res = await fetch("/api/products.php");
    const data = await res.json();

    if (data.success) {
      allProducts = data.products;
      filteredProducts = [...allProducts]; // INICIALIZAR FILTRADOS
      renderPage(1);
    }
  } catch (error) {
    console.error("Error:", error);
    document.getElementById("loading").innerHTML =
      "<p>Error al cargar productos</p>";
  }
}

async function loadCategories() {
  try {
    const res = await fetch("/api/categories.php");
    const data = await res.json();

    if (data.success && Array.isArray(data.categories)) {
      renderCategories(data.categories);
      setupFilterListeners(0, "category");
    } else {
      console.warn("No se recibieron categorías");
    }
  } catch (err) {
    console.error("Error al cargar categorías:", err);
  }
}

async function loadProviders() {
  try {
    const res = await fetch("/api/providers.php");
    const data = await res.json();

    if (data.success && Array.isArray(data.providers)) {
      renderProviders(data.providers);
      setupFilterListeners(1, "provider");
    } else {
      console.warn("No se recibieron proveedores");
    }
  } catch (err) {
    console.error("Error al cargar proveedores:", err);
  }
}

function setupSearchListener() {
  const input = document.querySelector(".search-input");
  if (!input) return;

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      applyFilters();
    }
  });

  input.setAttribute("enterkeyhint", "search");
}

function setupFilterListeners(sectionIndex, filterType) {
  if (filterType === "category") {
    const section = document.querySelectorAll(".filter-section")[sectionIndex];
    if (!section) return;

    section.addEventListener("click", (e) => {
      const btn = e.target.closest(".filter-btn");
      if (!btn) return;

      section
        .querySelectorAll(".filter-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const value = btn.getAttribute("data-category");
      currentCategoryId =
        value === "all"
          ? "all"
          : parseInt(btn.getAttribute("data-category-id"), 10);
      applyFilters();
    });
  } else if (filterType === "provider") {
    const dropdown = document.getElementById("providerDropdown");
    const menu = document.getElementById("providerDropdownMenu");
    if (!dropdown || !menu) return;

    dropdown.onclick = (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("active");
      menu.classList.toggle("show");
    };

    menu.onclick = (e) => {
      const item = e.target.closest(".dropdown-item"); //Buscar item clickeado
      if (!item) return;

      //Actualiza la seleccion
      menu
        .querySelectorAll(".dropdown-item")
        .forEach((i) => i.classList.remove("active"));
      item.classList.add("active");
      //Actualiza el texto dentro del dropdown
      dropdown.querySelector(".dropdown-text").textContent =
        item.textContent.trim();
      //Cierra dropdown
      dropdown.classList.remove("active");
      menu.classList.remove("show");

      //Aplica filtro
      const value = item.getAttribute("data-provider");
      currentProviderId =
        value === "all"
          ? "all"
          : parseInt(item.getAttribute("data-provider-id"), 10);
      applyFilters();
    };
    //Cerrar dropdwon al hacer click fuera
    document.onclick = () => {
      dropdown.classList.remove("active");
      menu.classList.remove("show");
    };
  }
}

function applyFilters() {
  const searchTerm =
    document.querySelector(".search-input")?.value.trim() || "";

  // Partimos de todos
  let base = [...allProducts];

  // Filtrar por categoría si no es 'all'
  if (currentCategoryId !== "all") {
    base = base.filter(
      (p) => Number(p.id_category) === Number(currentCategoryId)
    );
  }

  // Filtrar por proveedor si no es 'all'
  if (currentProviderId !== "all") {
    base = base.filter(
      (p) => Number(p.id_provider) === Number(currentProviderId)
    );
  }

  // Filtrar por búsqueda si hay término
  if (searchTerm !== "") {
    const normalizeText = (text) =>
      String(text)
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const normalizedSearch = normalizeText(searchTerm);

    base = base.filter((product) => {
      const sku = product.sku;
      const name = product.name || "";
      const description = product.description || "";

      return (
        normalizeText(name).includes(normalizedSearch) ||
        normalizeText(description).includes(normalizedSearch) ||
        normalizeText(sku).includes(normalizedSearch) ||
        String(product.id_product).includes(searchTerm)
      );
    });
  }

  filteredProducts = base;
  renderPage(1, false);
}

function renderPage(page, shouldScroll = true) {
  currentPage = page;

  document.getElementById("loading").style.display = "none";
  document.getElementById("productsContainer").style.display = "block";

  // Calcular qué productos mostrar
  const start = (page - 1) * PRODUCTS_PER_PAGE;
  const end = start + PRODUCTS_PER_PAGE;
  const pageProducts = filteredProducts.slice(start, end);

  // Renderizar productos
  const productsGrid = document.getElementById("productsGrid");

  if (pageProducts.length === 0) {
    productsGrid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                <h3>No se encontraron productos</h3>
                <p>Intenta con otros términos de búsqueda</p>
            </div>
        `;
  } else {
    productsGrid.innerHTML = pageProducts
      .map((p) => createProductCard(p))
      .join("");
  }

  // Actualizar contador
  const total = filteredProducts.length;
  document.getElementById("pageInfo").textContent = `${start + 1}-${Math.min(
    end,
    total
  )} de ${total} productos`;
  document.getElementById(
    "totalInfo"
  ).textContent = `Total: ${total} productos`;

  // Renderizar botones de paginación
  renderPagination();

  if (shouldScroll) {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

function renderPagination() {
  const totalPages = Math.ceil(filteredProducts.length / PRODUCTS_PER_PAGE);
  let buttons = [];

  // Botón anterior
  if (currentPage > 1) {
    buttons.push(
      `<button class="page-btn" onclick="renderPage(${
        currentPage - 1
      })">‹</button>`
    );
  }

  // Botones numéricos con puntos
  for (let i = 1; i <= totalPages; i++) {
    // Mostrar: primera, última, actual y vecinas
    if (
      i === 1 ||
      i === totalPages ||
      (i >= currentPage - 1 && i <= currentPage + 1)
    ) {
      const active = i === currentPage ? "active" : "";
      buttons.push(
        `<button class="page-btn ${active}" onclick="renderPage(${i})">${i}</button>`
      );
    }
    // Agregar puntos solo una vez
    else if (i === currentPage - 2 || i === currentPage + 2) {
      buttons.push(`<span class="page-dots">...</span>`);
    }
  }

  // Botón siguiente
  if (currentPage < totalPages) {
    buttons.push(
      `<button class="page-btn" onclick="renderPage(${
        currentPage + 1
      })">›</button>`
    );
  }

  document.getElementById("pagination").innerHTML = buttons.join("");
}

function createProductCard(p) {
  const sku = p.sku;
  const desc =
    p.description.length > 100
      ? p.description.substring(0, 100) + "..."
      : p.description;
  const detailLink = `product-detail.php?id=${encodeURIComponent(
    p.id_product
  )}`;

  return `
        <div class="product-card" data-product-id="${p.id_product}">
            <div class="product-image">
                <a href="${detailLink}" data-product-id=${p.id_product}>
                    <img src="/assets/img/${p.image_path}" 
                        alt="${p.name}" 
                        loading="lazy">
                </a>
            </div>
            <div class="product-info">
                <div class="product-header">
                    <span class="product-id">SKU: ${sku}</span>
                    <h3 class="product-name">
                        <a href="${detailLink}" data-product-id=${p.id_product}>
                            ${p.name}
                        </a>
                    </h3>
                    <p class="product-description">${desc}</p>
                </div>
                <div class="product-actions">
                    <div class="quantity-controls">
                        <label for="quantity-${p.id_product}">Cantidad:</label>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn qty-decrease" data-product-id="${p.id_product}">-</button>
                            <input type="number" id="quantity-${p.id_product}" class="qty-input" value="1" min="1" max="999" data-product-id="${p.id_product}">
                            <button type="button" class="qty-btn qty-increase" data-product-id="${p.id_product}">+</button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-cart" data-product-id="${p.id_product}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                        </svg>
                        Agregar al carrito
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Renderizar botones de categorías
function renderCategories(categories) {
  const container = document.getElementById("categoriesContainer");
  if (!container) return;

  container.innerHTML = categories
    .map(
      (c) => `
    <button class="filter-btn" 
            data-category-id="${c.id_category}" 
            data-category="${c.name}">
      ${escapeHtml(c.name)}
    </button>
  `
    )
    .join("");
}

// Renderizar botones de proveedores
function renderProviders(providers) {
  const container = document.getElementById("providersContainer");
  if (!container) return;

  container.innerHTML = providers
    .map(
      (p) => `
    <button class="dropdown-item" 
            data-provider-id="${p.id_provider}" 
            data-provider="${p.name}">
      ${escapeHtml(p.name)}
    </button>
  `
    )
    .join("");
}
