document.addEventListener("DOMContentLoaded", loadCart);

async function loadCart() {
  try {
    const response = await fetch("../api/cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ action: "get" }),
    });

    const data = await response.json();

    if (data.success) {
      renderCart(data.cart);
      updateSummary(data.cartCount, data.totalItems);

      updateCartBadge(data.totalItems);
    }
  } catch (error) {
    console.error("Error al cargar el carrito:", error);
  }
}

function renderCart(cart) {
  const container = document.getElementById("cartContent");

  if (!cart || cart.length === 0) {
    container.innerHTML = `
            <div class="empty-cart">
                <h2>Tu carrito está vacío</h2>
                <p>Agrega productos para comenzar tu cotización</p>
                <a href="/pages/catalog.php" class="btn-generate">
                    Ir a productos
                </a>
            </div>
        `;
    return;
  }

  let html = `
        <div class="cart-table-header">
            <div>Producto</div>
            <div>SKU</div>
            <div>Cantidad</div>
            <div>Acciones</div>
        </div>
    `;

  cart.forEach((item) => {
    const imagePath = item.image
      ? `../assets/img/${item.image}`
      : "../assets/img/placeholder.jpg";

    html += `
            <div class="cart-item" data-product-id="${item.id}">
                <div class="product-info">
                    <img src="${imagePath}" 
                         alt="${item.name}" 
                         class="product-image"
                         onerror="this.onerror=null; this.src='../assets/img/placeholder.jpg';">
                    <div class="product-details">
                        <h3>${item.name}</h3>
                    </div>
                </div>
                
                <div class="product-id">${item.sku}</div>
                
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="updateQuantity(${
                      item.id
                    }, ${item.quantity - 1})">−</button>
                    <span class="quantity-value">${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity(${
                      item.id
                    }, ${item.quantity + 1})">+</button>
                </div>
                
                <div>
                    <button class="delete-btn" onclick="removeItem(${
                      item.id
                    })" title="Eliminar">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 2L9 3H4v2h16V3h-5l-1-1h-4zM5 7v13c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V7H5zm3 2h2v9H8V9zm4 0h2v9h-2V9z"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
  });

  container.innerHTML = html;
}

function updateSummary(productCount, totalItems) {
  document.getElementById("cartCount").textContent = productCount;
  document.getElementById("totalProducts").textContent = productCount;
  document.getElementById("totalQuantity").textContent = totalItems;
}

async function updateQuantity(productId, newQuantity) {
  if (newQuantity < 1) {
    await removeItem(productId);
    return;
  }

  try {
    const response = await fetch("../api/cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "update",
        productId: productId,
        quantity: newQuantity,
      }),
    });

    const data = await response.json();

    if (data.success) {
      await loadCart();
    } else {
      Toast.error("Error al actualizar cantidad: " + data.error);
    }
  } catch (error) {
    console.error("Error:", error);
    Toast.error("Error al actualizar el producto");
  }
}

async function removeItem(productId) {
  try {
    const response = await fetch("../api/cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "remove",
        productId: productId,
      }),
    });

    const data = await response.json();

    if (data.success) {
      await loadCart();
    } else {
      Toast.error("Error al eliminar producto: " + data.error);
    }
  } catch (error) {
    console.error("Error:", error);
    Toast.error("Error al eliminar el producto");
  }
}
