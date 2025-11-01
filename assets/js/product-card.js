document.addEventListener('DOMContentLoaded', () => {
    const container = document.body; //Todo elemento dentro del body
    
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
            const qty = document.getElementById(`quantity-${id}`).value;
            console.log(`Producto ${id} - Cantidad: ${qty}`);
            
            const original = target.innerHTML;
            target.innerHTML = '¡Agregado!';
            target.style.background = '#10B981';
            setTimeout(() => {
                target.innerHTML = original;
                target.style.background = '';
            }, 2000);
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
