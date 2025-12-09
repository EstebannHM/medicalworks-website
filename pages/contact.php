<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Medical Works">
    <meta name="keywords" content="suministros médicos, equipos de protección, Medical Works, salud, productos médicos, cotizaciones médicas, contacto medical works">
    <title>Medical Works - Contacto</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="../assets/css/components/whatsapp-fab.css">
    <link rel="stylesheet" href="../assets/css/pages/contact.css">
    <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body>
    <?php
    include_once('../includes/header.php');
    ?>

    <main>

        <!-- Hero Section -->
        <section class="hero-contact">
            <h1>Contáctanos</h1>
            <p>Estamos aquí para ayudarte. Comunícate con nosotros por cualquiera de nuestros canales de atención.</p>
        </section>

        <!-- Contact Information -->
        <div class="contact-container">
            <div class="contact-grid">
                <!-- Phone Card -->
                <div class="contact-card">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: white;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </div>
                    <h3>Teléfono</h3>
                    <p class="subtitle">Llámanos de lunes a sábado</p>
                    <a href="tel:+50622308023">+506 2230 8023</a>
                </div>

                <!-- Email Card -->
                <div class="contact-card">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: white;">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m2 7 10 7 10-7" />
                        </svg>
                    </div>
                    <h3>Correo Electrónico</h3>
                    <p class="subtitle">Respuesta en 24 horas</p>
                    <a href="mailto:info@medworkcr.com">info@medworkcr.com</a>
                </div>

                <!-- Location Card -->
                <div class="contact-card">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: white;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>
                    <h3>Dirección</h3>
                    <p class="subtitle">Ubicación central</p>
                    <p>San José, Aserrí.</p>
                </div>
            </div>

            <!-- Map and Hours Section -->
            <div class="map-hours-section">
                <!-- Map Container -->
                <div class="map-container">
                    <div class="map-header">
                        <h3>Nuestra ubicación</h3>
                        <p>San José, Aserrí</p>
                    </div>
                    <div id="map"></div>
                </div>
                
                <!-- Hours Card -->
                <div class="hours-card">
                    <div class="hours-header">
                        <div class="hours-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: white;">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <h3>Horario de atención</h3>
                    </div>
                    <ul class="hours-list">
                        <li class="hours-item">
                            <span class="day">Lunes - Viernes</span>
                            <span class="time">8:00 AM - 5:00 PM</span>
                        </li>
                        <li class="hours-item">
                            <span class="day">Sábado</span>
                            <span class="time">Cerrado</span>
                        </li>
                        <li class="hours-item">
                            <span class="day">Domingo</span>
                            <span class="time">Cerrado</span>
                        </li>
                    </ul>
                    <div class="note">
                        <strong>Nota:</strong> Para atención fuera del horario, contáctanos por WhatsApp
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h2>¿Tienes alguna consulta?</h2>
                <p>Nuestro equipo está listo para ayudarte. Comunícate con nosotros por teléfono, correo o WhatsApp y te responderemos a la brevedad.</p>
                <div class="cta-buttons">
                    <a href="tel:+50622308023" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        Llamar ahora
                    </a>
                    <a href="https://wa.me/50689471791?text=Hola,%20me%20gustaría%20obtener%20más%20información%20sobre%20sus%20productos"
                        class="btn btn-whatsapp"
                        target="_blank"
                        rel="noopener noreferrer">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>

    </main>

    <?php
    include_once('../includes/footer.php');
    include_once('../includes/whatsapp-button.php');
    ?>

    <!-- Js -->
    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/header.js"></script>

    <?php
        require_once __DIR__ . '/../config/env_loader.php';
        $apiKey = $_ENV['API_KEY_MAP'];
    ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($apiKey)?>&loading=async&callback=initMap" async defer></script>
    
</body>

</html>