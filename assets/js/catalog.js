/**
 * Catálogo - Medical Works
 */

let allProducts = [];
let currentPage = 1;
const PRODUCTS_PER_PAGE = 12;

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});

async function loadProducts() {
    try {
        const res = await fetch('/api/products.php');
        const data = await res.json();
        
        if (data.success) {
            allProducts = data.products;
            renderPage(1);
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('loading').innerHTML = '<p>Error al cargar productos</p>';
    }
}

function renderPage(page) {
    currentPage = page;
    
    document.getElementById('loading').style.display = 'none';
    document.getElementById('productsContainer').style.display = 'block';
    
    // Calcular qué productos mostrar
    const start = (page - 1) * PRODUCTS_PER_PAGE;
    const end = start + PRODUCTS_PER_PAGE;
    const pageProducts = allProducts.slice(start, end);
    
    // Renderizar productos
    document.getElementById('productsGrid').innerHTML = 
        pageProducts.map(p => createProductCard(p)).join('');

    // Actualizar contador
    const total = allProducts.length;
    document.getElementById('pageInfo').textContent = 
        `${start + 1}-${Math.min(end, total)} de ${total} productos`;
    document.getElementById('totalInfo').textContent = 
        `Total: ${total} productos`;
    
    // Renderizar botones de paginación
    renderPagination();
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function renderPagination() {
    const totalPages = Math.ceil(allProducts.length / PRODUCTS_PER_PAGE);
    let buttons = [];
    
    // Botón anterior
    if (currentPage > 1) {
        buttons.push(`<button class="page-btn" onclick="renderPage(${currentPage - 1})">‹</button>`);
    }
    
    // Botones numéricos con puntos
    for (let i = 1; i <= totalPages; i++) {
        // Mostrar: primera, última, actual y vecinas
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const active = i === currentPage ? 'active' : '';
            buttons.push(`<button class="page-btn ${active}" onclick="renderPage(${i})">${i}</button>`);
        } 
        // Agregar puntos solo una vez
        else if (i === currentPage - 2 || i === currentPage + 2) {
            buttons.push(`<span class="page-dots">...</span>`);
        }
    }
    
    // Botón siguiente
    if (currentPage < totalPages) {
        buttons.push(`<button class="page-btn" onclick="renderPage(${currentPage + 1})">›</button>`);
    }
    
    document.getElementById('pagination').innerHTML = buttons.join('');
}

function createProductCard(p) {
    const sku = p.sku || `MED-${String(p.id_product).padStart(3, '0')}`;
    const desc = p.description.length > 100 ? p.description.substring(0, 100) + '...' : p.description;
    
    return `
        <div class="product-card" data-product-id="${p.id_product}">
            <div class="product-image">
                <img src="/assets/img/${p.image_path}" alt="${p.name}" loading="lazy">
            </div>
            <div class="product-info">
                <div class="product-header">
                    <span class="product-id">ID: ${sku}</span>
                    <h3 class="product-name">${p.name}</h3>
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