/**
 * Dashboard Admin - Gestión de Proveedores
 * Carga, filtrado, búsqueda y acciones sobre proveedores
 */


async function loadProviders() {
  const res = await fetch("/api/providers_admin.php");
  const data = await res.json();
  if (data.success && Array.isArray(data.providers)) {
    PROVIDER_MAP = new Map(
      data.providers.map((p) => [Number(p.id_provider), p.name])
    );
    A_PROVIDERS = data.providers;
    A_FILTERED_PROVIDERS = [...A_PROVIDERS];
    renderProviders(data.providers);
    if (data.stats) {
      updateProvidersKPIFromAPI(data.stats);
    }
  }
}

/**
 * Aplica filtro de búsqueda y estado para proveedores
 */
function applyProviderSearchAndStatusFilter() {
  const searchInput = document.getElementById("providerSearch");
  if (!searchInput) return;

  currentProviderSearchTerm = searchInput.value.trim();
  let filtered = [...A_PROVIDERS];

  // Filtrar por estado
  if (currentProviderStatusFilter === "active") {
    filtered = filtered.filter((provider) => Number(provider.status) === 1);
  } else if (currentProviderStatusFilter === "inactive") {
    filtered = filtered.filter((provider) => Number(provider.status) === 0);
  }

  // Filtrar por búsqueda si hay término
  if (currentProviderSearchTerm !== "") {
    const normalizedSearch = normalizeText(currentProviderSearchTerm);

    filtered = filtered.filter((provider) => {
      const name = provider.name || "";
      return normalizeText(name).includes(normalizedSearch);
    });
  }

  A_FILTERED_PROVIDERS = filtered;
  renderPage('proveedores', 1, false);
}

/**
 * Configura listener para búsqueda de proveedores
 */
function setupProviderSearchListener() {
  const searchInput = document.getElementById("providerSearch");
  if (!searchInput) return;

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      applyProviderSearchAndStatusFilter();
    }
  });

  searchInput.setAttribute("enterkeyhint", "search");
}

/**
 * Configura listener para filtro de estado de proveedores
 */
function setupProviderStatusFilterListener() {
  setupDropdownFilterListener({
    dropdownToggleId: "providerStatusDropdown",
    dropdownMenuId: "providerStatusDropdownMenu",
    getValue: (item) => item.getAttribute("data-status"),
    getText: (item) => {
      const status = item.getAttribute("data-status");
      if (status === "active") return "Solo activos";
      if (status === "inactive") return "Solo inactivos";
      return "Todos los estados";
    },
    setFilter: (val) => { currentProviderStatusFilter = val; },
    defaultText: "Todos los estados"
  });
  
  // Aplicar filtros al cambiar el estado
  const dropdownMenu = document.getElementById("providerStatusDropdownMenu");
  if (dropdownMenu) {
    dropdownMenu.addEventListener("click", () => {
      setTimeout(() => applyProviderSearchAndStatusFilter(), 100);
    });
  }
}

/**
 * Configura acciones para proveedores (toggle status)
 */
function setupProviderActions() {
  const mount = document.getElementById("tableMount");
  if (!mount) return;

  mount.addEventListener("click", async (e) => {
    const btn = e.target.closest("button.btn-toggle-provider-status");
    if (!btn) return;
    
    const row = btn.closest("tr");
    if (!row) return;

    const providerId = Number(btn.getAttribute("data-provider-id"));
    if (!providerId) return;

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
  });
}

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


function updateProvidersKPIFromAPI(stats) {
  const label = document.getElementById("kpi-proveedores-label");
  if (!label) return;

  const card = label.closest(".kpi-card");
  if (!card) return;

  const valueEl = card.querySelector("#kpiProvidersValue");
  if (!valueEl) return;

  valueEl.textContent = String(stats.active);
}
