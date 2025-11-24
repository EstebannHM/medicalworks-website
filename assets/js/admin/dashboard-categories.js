/**
 * Dashboard Admin - Gestión de Categorías
 * Carga, filtrado y búsqueda de categorías
 */


async function loadCategoriesAdmin() {
  const res = await fetch("/api/categories_admin.php");
  const data = await res.json();
  if (data.success && Array.isArray(data.categories)) {
    CATEGORY_MAP = new Map(
      data.categories.map((c) => [Number(c.id_category), c.name])
    );
    A_CATEGORIES = data.categories;
    A_FILTERED_CATEGORIES = [...A_CATEGORIES];
    renderCategories(data.categories);
  }
}

/**
 * Aplica filtro de búsqueda para categorías
 */
function applyCategorySearchFilter() {
  const searchInput = document.getElementById("categorySearch");
  if (!searchInput) return;

  currentCategorySearchTerm = searchInput.value.trim();
  let filtered = [...A_CATEGORIES];

  // Filtrar por búsqueda si hay término
  if (currentCategorySearchTerm !== "") {
    const normalizedSearch = normalizeText(currentCategorySearchTerm);

    filtered = filtered.filter((category) => {
      const name = category.name || "";
      return normalizeText(name).includes(normalizedSearch);
    });
  }

  A_FILTERED_CATEGORIES = filtered;
  renderPage('categorias', 1, false);
}

/**
 * Configura listener para búsqueda de categorías
 */
function setupCategorySearchListener() {
  const searchInput = document.getElementById("categorySearch");
  if (!searchInput) return;

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      applyCategorySearchFilter();
    }
  });

  searchInput.setAttribute("enterkeyhint", "search");
}
