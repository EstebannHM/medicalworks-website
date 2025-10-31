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

    // Limitar descripción a 100 caracteres
    $shortDescription = strlen($description) > 100
        ? substr($description, 0, 100) . '...'
        : $description;

    $fullDescription = htmlspecialchars($product['description']);

?>
    <div class="product-detail" data-product-id="<?= $id ?>">
        <div class="product-media">
            <div class="product-image">
                <img src="../assets/img/<?= $image ?>" alt="<?= $name ?>" loading="lazy">
            </div>

            <div class="product-accordion">
                <details class="accordion-item">
                    <summary>Detalles</summary>
                    <div class="accordion-content">
                        <p><?= $fullDescription ?></p>
                    </div>
                </details>

                <details class="accordion-item">
                    <summary>Ingredientes</summary>
                    <div class="accordion-content">
                        <ul>
                            <li>Ingrediente 1</li>
                            <li>Ingrediente 2</li>
                            <li>Ingrediente 3</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>

        <div class="product-info">
            <div class="product-header">
                <span class="product-id">SKU: <?= $sku ?></span>
                <h3 class="product-name"><?= $name ?></h3>
                <p class="product-description"><?= $shortDescription ?></p>
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
            </div>
        </div>
    </div>
<?php
}
