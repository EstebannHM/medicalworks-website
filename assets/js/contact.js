// Inicializa el mapa cuando se carga la API de Google Maps
function initMap() {
    // Coordenadas provicionales
    const sanJose = { 
        lat: 9.9281, 
        lng: -84.0907 
    };
    
    // Crea el mapa
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: sanJose,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        // Estilos para el mapa
        styles: [
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{ color: '#a8d8ea' }]
            },
            {
                featureType: 'landscape',
                elementType: 'geometry',
                stylers: [{ color: '#f5f7fa' }]
            }
        ]
    });
    
    // Ping de la ubicación
    const marker = new google.maps.Marker({
        position: sanJose,
        map: map,
        title: 'Medical Works',
        animation: google.maps.Animation.DROP
    });
    
    // Crea la ventana de información
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 12px; font-family: Inter, sans-serif;">
                <h3 style="margin: 0 0 8px 0; color: #0f172a; font-size: 1.1rem; font-weight: 600;">Medical Works</h3>
                <p style="margin: 0 0 6px 0; color: #475569; font-size: 0.9rem;">San José, Costa Rica</p>
                <p style="margin: 0 0 6px 0; color: #475569; font-size: 0.9rem;">📞 +506 8418 6031</p>
                <a href="https://www.google.com/maps/dir/?api=1&destination=9.9281,-84.0907" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   style="color: #0a7bc2; text-decoration: none; font-weight: 500;">
                    📍 Cómo llegar →
                </a>
            </div>
        `
    });
    
    // Muestra la información al hacer clic en el marcador
    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });
}
