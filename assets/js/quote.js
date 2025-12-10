// Variables
let currentStep = 1;
const formData = {
    fullName: '',
    email: '',
    phone: ''
};

document.addEventListener('DOMContentLoaded', function() {
    initializeQuoteForm();
    setupEventListeners();
});

function initializeQuoteForm() {
    showStep(1);
    loadCartProducts();
}

function setupEventListeners() {

    const form = document.getElementById('quote-form');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    } else {
        console.error('Formulario quote-form no encontrado');
    }

    // Validaciones
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (fullNameInput) {
        fullNameInput.addEventListener('blur', () => validateField('fullName'));
        fullNameInput.addEventListener('input', () => clearError('fullName'));
    }

    if (emailInput) {
        emailInput.addEventListener('blur', () => validateField('email'));
        emailInput.addEventListener('input', () => clearError('email'));
    }

    if (phoneInput) {
        phoneInput.addEventListener('blur', () => validateField('phone'));
        phoneInput.addEventListener('input', () => clearError('phone'));
        phoneInput.addEventListener('input', formatPhoneInput);
    }

    // Botón volver al paso 1 desde el paso 2
    const btnBackToStep1 = document.getElementById('btn-back-to-step1');
    if (btnBackToStep1) {
        btnBackToStep1.addEventListener('click', () => goToStep(1));
    }

    // Botón generar cotización PDF
    const btnGenerateQuote = document.getElementById('btn-generate-quote');
    if (btnGenerateQuote) {
        btnGenerateQuote.addEventListener('click', handleGenerateQuote);
    }

    // Checkbox de aceptación de política (por definirse)
    const acceptPolicy = document.getElementById('acceptPolicy');
    if (acceptPolicy) {
        acceptPolicy.addEventListener('change', () => clearError('acceptPolicy'));
    }

    // Preveine espacios al inicio de los campos
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function(e) {
            if (e.target.value.startsWith(' ')) {
                e.target.value = e.target.value.trimStart();
            }
        });
    });
}

function showStep(stepNumber) {
    
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.remove('active');
    });

    
    const currentStepElement = document.getElementById(`step-${stepNumber}`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
    }

    updateStepIndicators(stepNumber);
    
    currentStep = stepNumber;

    if (stepNumber === 2) {
        loadSummary();
    }
}

function goToStep(stepNumber) {
    showStep(stepNumber);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateStepIndicators(activeStep) {
    for (let i = 1; i <= 3; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        if (indicator) {
            indicator.classList.remove('active', 'completed');
            
            if (i === activeStep) {
                indicator.classList.add('active');
            } else if (i < activeStep) {
                indicator.classList.add('completed');
            }
        }
    }
}

// Validaciones

function validateField(fieldName) {
    const input = document.getElementById(fieldName);
    const errorElement = document.getElementById(`${fieldName}-error`);
    
    if (!input) {
        console.error(`Campo ${fieldName} no encontrado`);
        return false;
    }

    const value = input.value.trim();
    let errorMessage = '';
    let isValid = true;

    if (value === '') {
        errorMessage = 'Este campo es obligatorio';
        isValid = false;
    } else {

        switch (fieldName) {
            case 'fullName':
                if (value.length < 3) {
                    errorMessage = 'El nombre debe tener al menos 3 caracteres';
                    isValid = false;
                } else if (value.length > 100) {
                    errorMessage = 'El nombre no puede exceder 100 caracteres';
                    isValid = false;
                }
                break;

            case 'email':
                // Formato básico de correo electrónico
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    errorMessage = 'El correo debe tener formato válido (ej: usuario@dominio.com)';
                    isValid = false;
                } else if (value.length > 200) {
                    errorMessage = 'El correo no puede exceder 200 caracteres';
                    isValid = false;
                }
                break;

            case 'phone':
                // El telefono debe de tener 8 dígitos numéricos
                // Acepta formatos: 12345678, +50612345678
                const phoneRegex = /^(\+506)?[0-9]{8}$/;
                if (!phoneRegex.test(value)) {
                    errorMessage = 'El teléfono debe tener 8 dígitos numéricos';
                    isValid = false;
                }
                break;
        }
    }

    // Mostrar u ocultar error
    if (isValid) {
        input.classList.remove('error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    } else {
        input.classList.add('error');
        if (errorElement) {
            errorElement.textContent = errorMessage;
        }
    }

    return isValid;
}

function clearError(fieldName) {
    const input = document.getElementById(fieldName);
    const errorElement = document.getElementById(`${fieldName}-error`);
    
    if (input) {
        input.classList.remove('error');
    }
    
    if (errorElement) {
        errorElement.textContent = '';
    }
}

function validateAllFields() {
    const fields = ['fullName', 'email', 'phone'];
    let allValid = true;

    fields.forEach(field => {
        if (!validateField(field)) {
            allValid = false;
        }
    });

    return allValid;
}

// FORMULARIO - PASO 1

function handleFormSubmit(e) {
    e.preventDefault();

    // Valida todos los campos
    if (!validateAllFields()) {
        const firstError = document.querySelector('.form-control.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        return;
    }

    // Guarda datos del formulario
    formData.fullName = document.getElementById('fullName').value.trim();
    formData.email = document.getElementById('email').value.trim();
    formData.phone = document.getElementById('phone').value.trim();

    goToStep(2);
}

// FORMULARIO - PASO 2

function loadSummary() {
    // Cargar datos del usuario
    const summaryName = document.getElementById('summary-name');
    const summaryEmail = document.getElementById('summary-email');
    const summaryPhone = document.getElementById('summary-phone');
    
    if (summaryName) summaryName.textContent = formData.fullName;
    if (summaryEmail) summaryEmail.textContent = formData.email;
    if (summaryPhone) summaryPhone.textContent = formData.phone;

    // Los productos se cargarán desde la API del carrito
    loadCartProducts();
}

async function loadCartProducts() {
    
    const productsContainer = document.getElementById('products-summary');
    const productCount = document.getElementById('product-count');
    
    if (!productsContainer) return;

    try {
        // Mostrar loading
        productsContainer.innerHTML = `
            <p class="empty-message">Cargando productos...</p>
        `;

        const response = await fetch('../api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'get' }),
        });

        const data = await response.json();

        if (data.success) {
            if (!data.cart || data.cart.length === 0) {
                // Carrito vacío
                productsContainer.innerHTML = `
                    <div class="empty-message">
                        <p>No hay productos en el carrito</p>
                        <a href="catalog.php" style="color: #0EA5E9; text-decoration: none; margin-top: 12px; display: inline-block;">
                            Ir al catálogo
                        </a>
                    </div>
                `;
                if (productCount) {
                    productCount.textContent = '(0)';
                }
            } else {
                // Renderizar productos
                renderProducts(data.cart);
                
                // Actualizar contador
                if (productCount) {
                    productCount.textContent = `(${data.cart.length})`;
                }
            }
        } else {
            throw new Error(data.error || 'Error al cargar productos');
        }
    } catch (error) {
        console.error('Error al cargar productos del carrito:', error);
        productsContainer.innerHTML = `
            <p class="empty-message" style="color: #EF4444;">
                Error al cargar los productos. Por favor, intenta nuevamente.
            </p>
        `;
    }
}

function renderProducts(products) {
    const container = document.getElementById('products-summary');
    if (!container) return;

    container.innerHTML = '';

    products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'product-item';
        
        // Construir ruta de imagen
        const imagePath = product.image 
            ? `../assets/img/${product.image}` 
            : '../assets/img/placeholder.jpg';
        
        productElement.innerHTML = `
            <div class="product-image">
                <img src="${imagePath}" 
                     alt="${product.name}"
                     onerror="this.onerror=null; this.src='../assets/img/placeholder.jpg';">
            </div>
            <div class="product-details">
                <div class="product-name">${product.name}</div>
                <div class="product-id">SKU: ${product.sku}</div>
                <div class="product-quantity">Cantidad: ${product.quantity} unidad${product.quantity > 1 ? 'es' : ''}</div>
            </div>
        `;
        container.appendChild(productElement);
    });
}

// FORMULARIO - PASO 3

function handleGenerateQuote() {
    // Valida checkbox de aceptación de política
    const acceptPolicy = document.getElementById('acceptPolicy');
    const errorElement = document.getElementById('acceptPolicy-error');

    if (!acceptPolicy) {
        console.error('Checkbox acceptPolicy no encontrado');
        return;
    }

    if (!acceptPolicy.checked) {
        if (errorElement) {
            errorElement.textContent = 'Debes aceptar la política de tratamiento de datos';
        }
        acceptPolicy.focus();
        return;
    }

    if (errorElement) {
        errorElement.textContent = '';
    }

    // Aquí se procesaría la cotización
    processQuote();
}

async function processQuote() {
    const btn = document.getElementById('btn-generate-quote');
    if (!btn) return;
    
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
            <circle cx="12" cy="12" r="10"/>
        </svg>
        Generando cotización...
    `;

    try {
        const response = await fetch('../api/quote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                fullName: formData.fullName,
                email: formData.email,
                phone: formData.phone
            }),
        });

        const data = await response.json();

        if (data.success) {
            goToStep(3);
        } else {
            throw new Error(data.error || 'Error al generar la cotización');
        }
        
    } catch (error) {
        console.error('Error completo:', error);
        Toast.error(error.message || 'Error al generar la cotización');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

// Formateo automático del teléfono
function formatPhoneInput(e) {
    let value = e.target.value.replace(/\D/g, ''); // Solo números
    
    // Si empieza con 506, agregar el +
    if (value.startsWith('506') && value.length > 3) {
        value = '+506' + value.substring(3);
    }
    
    e.target.value = value;
}

// Preveniene envío del formulario con Enter en campos de texto
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
        e.preventDefault();
    }
});

// Modal de Política de Tratamiento de Datos
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('policyModal');
    const openBtn = document.getElementById('openPolicyModal');
    const closeBtn = document.querySelector('.policy-modal-close');
    const closeBtnFooter = document.getElementById('closePolicyModal');
    const overlay = document.querySelector('.policy-modal-overlay');

    function openModal(e) {
        e.preventDefault();
        if (modal) {
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal() {
        if (modal) {
            if (document.activeElement && modal.contains(document.activeElement)) {
                document.activeElement.blur();
            }
            
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (closeBtnFooter) {
        closeBtnFooter.addEventListener('click', closeModal);
    }

    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
