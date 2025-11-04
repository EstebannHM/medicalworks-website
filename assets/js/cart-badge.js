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
