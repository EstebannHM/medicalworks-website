<?php

/**
 * Componente: Product Card
 * Card reutilizable para mostrar productos con controles de cantidad
 * Boton para agregar al carrito
 */

function renderProductCard($product)
{
    // Sanitizar datos, por seguridad.
    $id = htmlspecialchars($product['id_product']);
    $name = htmlspecialchars($product['name']);
    $description = htmlspecialchars($product['description']);
    $image = htmlspecialchars($product['image_path']);
    $sku = isset($product['sku']) ? htmlspecialchars($product['sku']) : 'MED-' . str_pad($id, 3, '0', STR_PAD_LEFT);

    // Limitar descripción a 100 caracteres
    $shortDescription = strlen($description) > 100
        ? substr($description, 0, 100) . '...'
        : $description;
        
    $idInt = isset($product['id_product']) ? (int) $product['id_product'] : 0;
    $detailLink = 'product-detail.php?id=' . rawurlencode($idInt);
   
?>


    <div class="product-card" data-product-id="<?php echo $id; ?>">
        <span class="product-badge">Destacado</span>

        <div class="product-image">
           <a href="<?php echo htmlspecialchars($detailLink, ENT_QUOTES, 'UTF-8'); ?>" data-product-id="<?php echo $id; ?>">
                <img src="../assets/img/<?php echo $image; ?>"
                    alt="<?php echo $name; ?>"
                    loading="lazy">
            </a>
        </div>

        <div class="product-info">
            <div class="product-header">
                <span class="product-id">ID: <?php echo $sku; ?></span>
                <h3 class="product-name">
                    
                    <a href="<?php echo htmlspecialchars($detailLink, ENT_QUOTES, 'UTF-8'); ?>" data-product-id="<?php echo $id; ?>">
                        <?php echo $name; ?>
                    </a>

                </h3>
                <p class="product-description"><?php echo $shortDescription; ?></p>
            </div>

            <div class="product-actions">
                <div class="quantity-controls">
                    <label for="quantity-<?php echo $id; ?>">Cantidad:</label>
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn qty-decrease" data-product-id="<?php echo $id; ?>">-</button>
                        <input type="number"
                            id="quantity-<?php echo $id; ?>"
                            class="qty-input"
                            value="1"
                            min="1"
                            max="999"
                            data-product-id="<?php echo $id; ?>">
                        <button type="button" class="qty-btn qty-increase" data-product-id="<?php echo $id; ?>">+</button>
                    </div>
                </div>

                <button type="button" class="btn-add-cart" data-product-id="<?php echo $id; ?>">
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
?>

