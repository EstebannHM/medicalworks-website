<?php
/**
 * Componente de vista para mostrar el detalle de un producto
 * 
 * @param array $product Datos del producto
 */
function renderProductDetail(array $product): void {

    $id = htmlspecialchars($product['id_product']);
    $name = htmlspecialchars($product['name']);
    $description = htmlspecialchars($product['description']);
    $image = htmlspecialchars($product['image_path']);
    $sku = isset($product['sku']) ? htmlspecialchars($product['sku']) : 'MED-' . str_pad($id, 3, '0', STR_PAD_LEFT);
    
    // Obtiene ruta de la ficha técnica si existe
    $pdfPath = isset($product['pdf_path']) && !empty($product['pdf_path']) 
        ? htmlspecialchars($product['pdf_path']) 
        : null;

?>
    <div class="product-detail" data-product-id="<?= $id ?>">
        <div class="product-media">
            <div class="product-image">
                <img src="../assets/img/<?= $image ?>" alt="<?= $name ?>" loading="lazy">
            </div>
        </div>

        <div class="product-info">
            <div class="product-header">
                <span class="product-id">SKU: <?= $sku ?></span>
                <h3 class="product-name"><?= $name ?></h3>
                <p class="product-description"><?= $description ?></p>
            </div>

            <div class="product-actions">
                <div class="quantity-controls">
                    <label for="quantity-<?= $id ?>">Cantidad:</label>
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn qty-decrease" data-product-id="<?= $id ?>">-</button>
                        <input
                            id="quantity-<?= $id ?>"
                            class="qty-input"
                            type="number"
                            value="1"
                            min="1"
                            max="999"
                            data-product-id="<?= $id ?>">
                        <button type="button" class="qty-btn qty-increase" data-product-id="<?= $id ?>">+</button>
                    </div>
                </div>

                <button type="button" class="btn-add-cart" data-product-id="<?= $id ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                    </svg>
                    Agregar al carrito
                </button>
                
                <?php if ($pdfPath): ?>
                <!-- Botón de descarga de ficha técnica -->
                <a href="../assets/docs/<?= $pdfPath ?>" 
                   class="btn-add-cart btn-datasheet" 
                   style="text-decoration: none;"
                   target="_blank" 
                   rel="noopener noreferrer"
                   download
                   title="Descargar ficha técnica">
                    <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                        <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029z"/>
                    </svg>
                    Ficha Técnica
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}