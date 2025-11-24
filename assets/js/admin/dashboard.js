/**
 * Dashboard Admin - Archivo Principal
 * Inicialización y variables globales
 */

// Productos
let A_PRODUCTS = [];
let A_FILTERED = [];
let currentSearchTerm = "";
let currentStatusFilter = "all";
let currentCategoryId = "all";
let currentProviderId = "all";

// Categorías
let CATEGORY_MAP = new Map();
let A_CATEGORIES = [];
let A_FILTERED_CATEGORIES = [];
let currentCategorySearchTerm = "";

// Proveedores
let PROVIDER_MAP = new Map();
let A_PROVIDERS = [];
let A_FILTERED_PROVIDERS = [];
let currentProviderSearchTerm = "";
let currentProviderStatusFilter = "all";

// Paginación
let PAGE = 1;
const ROWS_PER_PAGE = 10;


document.addEventListener("DOMContentLoaded", async () => {
  try {
    // Cargar datos de las APIs
    await Promise.all([loadCategoriesAdmin(), loadProviders()]);
    await loadProducts();
    
    // Configurar listeners de productos
    setupDelegatedActions();
    setupSearchListener();
    setupStatusFilterListener();
    setupCategoryFilterListener();
    setupProviderFilterListener();
    
    // Configurar listeners de categorías
    setupCategorySearchListener();
    
    // Configurar listeners de proveedores
    setupProviderSearchListener();
    setupProviderStatusFilterListener();
    setupProviderActions();
    
    // Configurar menú de navegación
    setupMenuSectionListener();
    
    // Renderizar primera página de productos
    renderPage('productos', 1, false);
  } catch (e) {
    console.error("Error al inicializar dashboard:", e);
  }
});
