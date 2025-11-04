document.addEventListener('DOMContentLoaded', () => {
    const container = document.body;
    
    // Cargar contador del carrito al iniciar
    updateCartBadge();
    
    // Delegación para todos los botones de cantidad y carrito
    container.addEventListener('click', function(e) {
        const target = e.target.closest('button');
        if (!target) return;
        
        // Incrementar
        if (target.classList.contains('qty-increase')) {
            const id = target.dataset.productId;
            const input = document.getElementById(`quantity-${id}`);
            const val = parseInt(input.value) || 1;
            if (val < 999) input.value = val + 1;
        }
        
        // Decrementar
        if (target.classList.contains('qty-decrease')) {
            const id = target.dataset.productId;
            const input = document.getElementById(`quantity-${id}`);
            const val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
        }
        
        // Agregar al carrito
        if (target.classList.contains('btn-add-cart')) {
            const id = target.dataset.productId;
            const qty = parseInt(document.getElementById(`quantity-${id}`).value);
            addToCart(id, qty, target);
        }
    });
    
    // Validar inputs
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const val = parseInt(e.target.value);
            if (isNaN(val) || val < 1) e.target.value = 1;
            if (val > 999) e.target.value = 999;
        }
    });
});

/**
 * Agregar producto al carrito
 */
async function addToCart(productId, quantity, button) {
    try {
        const response = await fetch('/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'add',
                productId: parseInt(productId),
                quantity: parseInt(quantity)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Feedback visual en el botón
            const original = button.innerHTML;
            button.innerHTML = '¡Agregado!';
            button.style.background = '#10B981';
            button.disabled = true;
            
            // Actualizar badge del carrito
            updateCartBadge(data.totalItems);
            
            // Restaurar botón después de 2 segundos
            setTimeout(() => {
                button.innerHTML = original;
                button.style.background = '';
                button.disabled = false;
            }, 2000);
            
        } else {
            alert('Error: ' + data.error);
        }
        
    } catch (error) {
        console.error('Error al agregar al carrito:', error);
        alert('Error al agregar el producto al carrito');
    }
}

/**
 * Actualizar badge del carrito en el header
 */
async function updateCartBadge(count = null) {
    try {
        // Si no se pasa count, obtenerlo del servidor
        if (count === null) {
            const response = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'get' })
            });
            
            const data = await response.json();
            if (data.success) {
                count = data.totalItems;
            }
        }
        
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 'flex';
        }
        
    } catch (error) {
        console.error('Error al actualizar badge:', error);
    }
}