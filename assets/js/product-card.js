// Incrementar cantidad
document.querySelectorAll('.qty-increase').forEach(btn => {
    btn.onclick = function() {
        const input = document.getElementById(`quantity-${this.dataset.productId}`);
        const val = parseInt(input.value) || 1;
        if (val < 999) input.value = val + 1;
    };
});

// Decrementar cantidad
document.querySelectorAll('.qty-decrease').forEach(btn => {
    btn.onclick = function() {
        const input = document.getElementById(`quantity-${this.dataset.productId}`);
        const val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    };
});

// Validar input
document.querySelectorAll('.qty-input').forEach(input => {
    input.oninput = function() {
        const val = parseInt(this.value);
        if (isNaN(val) || val < 1) this.value = 1;
        if (val > 999) this.value = 999;
    };
});

// Agregar al carrito (preparado para backend)
document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.onclick = function() {
        const id = this.dataset.productId;
        const qty = document.getElementById(`quantity-${id}`).value;
        console.log(`Producto ${id} - Cantidad: ${qty}`);
        
        // Feedback visual
        const original = this.innerHTML;
        this.innerHTML = '¡Agregado!';
        this.style.background = '#10B981';
        setTimeout(() => {
            this.innerHTML = original;
            this.style.background = '';
        }, 2000);
    };
});