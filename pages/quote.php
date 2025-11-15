<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, cotización medical works">
    <title>Medical Works - Cotización</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="../assets/css/components/whatsapp-fab.css">
    <link rel="stylesheet" href="../assets/css/pages/quote.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php
    include_once('../includes/header.php');
    ?>

    <main>
        <div class="quote-container">
            <div class="steps-indicator">
                <div class="step active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <span class="step-label">Tus datos</span>
                </div>
                <div class="step" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <span class="step-label">Resumen</span>
                </div>
                <div class="step" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <span class="step-label">Confirmación</span>
                </div>
            </div>

            <div class="steps-content">
                
                <!-- PASO 1: Formulario de datos -->
                <div class="step-content active" id="step-1">
                    <div class="step-card">
                        <h2>Completa tus datos</h2>
                        <p class="step-subtitle">Completa tus datos para enviar la cotización</p>

                        <form id="quote-form" novalidate>
                            
                            <div class="form-group">
                                <label for="fullName">Nombre completo <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="fullName" 
                                    name="fullName" 
                                    class="form-control"
                                    placeholder="Ingresa tu nombre completo"
                                    required
                                >
                                <span class="error-message" id="fullName-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="email">Correo electrónico <span class="required">*</span></label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-control"
                                    placeholder="correo@ejemplo.com"
                                    required
                                >
                                <span class="error-message" id="email-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono <span class="required">*</span></label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    class="form-control"
                                    placeholder="+50612345678"
                                    required
                                >
                                <span class="error-message" id="phone-error"></span>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="window.location.href='cart.php'">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                                    </svg>
                                    Volver al carrito
                                </button>
                                <button type="submit" class="btn-primary">
                                    Continuar
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- PASO 2: Resumen de cotización -->
                <div class="step-content" id="step-2">
                    <div class="step-card">
                        <h2>Resumen de cotización</h2>

                        <div class="summary-section">
                            <h3>Tus datos</h3>
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Nombre:</span>
                                    <span class="summary-value" id="summary-name">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Correo:</span>
                                    <span class="summary-value" id="summary-email">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Teléfono:</span>
                                    <span class="summary-value" id="summary-phone">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Productos carrito del cliente -->
                        <div class="summary-section">
                            <h3>Productos <span class="product-count" id="product-count">(0)</span></h3>
                            <div id="products-summary" class="products-list">
                                <p class="empty-message">Cargando productos...</p>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="acceptPolicy" required>
                                <span>Acepto la <a href="#" target="_blank">política de tratamiento de datos</a></span>
                            </label>
                            <span class="error-message" id="acceptPolicy-error"></span>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" id="btn-back-to-step1">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                                </svg>
                                Editar datos
                            </button>
                            <button type="button" class="btn-primary" id="btn-generate-quote">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                Generar cotización PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Confirmación -->
                <div class="step-content" id="step-3">
                    <div class="step-card confirmation-card">
                        <div class="success-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <h2>¡Tu cotización ha sido generada!</h2>
                        <p class="confirmation-text">
                            Un asesor de Medical Works te contactará pronto para confirmar tu solicitud.
                        </p>
                        <button type="button" class="btn-primary" onclick="window.location.href='catalog.php'">
                            Volver al catálogo
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/quote.js"></script>

</body>

</html>