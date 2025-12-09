/**
 * Sistema de notificaciones Toast - Medical Works
 * Sistema centralizado y reutilizable para mostrar notificaciones
 * @version 1.0.0
 */

const ToastSystem = (() => {
    // Configuración
    const CONFIG = {
        duration: 4000,
        maxToasts: 3,
        position: 'top-right'
    };

    // Queue de toasts
    const toastQueue = [];

    /**
     * Sanitiza HTML para prevenir XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Obtiene o crea el contenedor de toasts
     */
    function getContainer() {
        let container = document.getElementById('toastContainer');
        
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        
        return container;
    }

    /**
     * Obtiene el ícono SVG según el tipo
     */
    function getIcon(type) {
        const icons = {
            success: '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>',
            error: '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>',
            warning: '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>',
            info: '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>'
        };
        return icons[type] || icons.info;
    }

    /**
     * Obtiene el título según el tipo
     */
    function getTitle(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };
        return titles[type] || titles.info;
    }

    /**
     * Limpia toasts antiguos si excede el máximo
     */
    function cleanupOldToasts() {
        const container = getContainer();
        const toasts = container.querySelectorAll('.toast');
        
        if (toasts.length >= CONFIG.maxToasts) {
            const oldestToast = toasts[0];
            removeToast(oldestToast);
        }
    }

    /**
     * Remueve un toast con animación
     */
    function removeToast(toastElement) {
        toastElement.classList.remove('show');
        toastElement.classList.add('hide');
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 300);
    }

    /**
     * Muestra una notificación toast
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo: success, error, warning, info
     * @param {number} duration - Duración en ms (opcional)
     */
    function show(message, type = 'success', duration = CONFIG.duration) {
        if (!message) {
            console.warn('[Toast] No message provided');
            return;
        }

        cleanupOldToasts();

        const container = getContainer();
        const toast = document.createElement('div');
        
        toast.className = `toast ${type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');
        toast.setAttribute('aria-atomic', 'true');

        const sanitizedMessage = escapeHtml(message);
        const icon = getIcon(type);
        const title = getTitle(type);

        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${sanitizedMessage}</div>
            </div>
        `;

        container.appendChild(toast);
        toastQueue.push(toast);

        // Mostrar con animación
        setTimeout(() => toast.classList.add('show'), 10);

        // Ocultar y remover
        setTimeout(() => {
            removeToast(toast);
            const index = toastQueue.indexOf(toast);
            if (index > -1) {
                toastQueue.splice(index, 1);
            }
        }, duration);
    }

    // API pública
    return {
        show,
        success: (message, duration) => show(message, 'success', duration),
        error: (message, duration) => show(message, 'error', duration),
        warning: (message, duration) => show(message, 'warning', duration),
        info: (message, duration) => show(message, 'info', duration),
        config: CONFIG
    };
})();

// Hacer disponible globalmente
window.Toast = ToastSystem;
